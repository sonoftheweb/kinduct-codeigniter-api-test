<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once ('BaseTransformer.php');

class Playerstransformer extends BaseTransformer
{
	/**
	 * Transform items
	 * A list of all the items attributes to allow out of the transformer
	 *
	 * @var array
	 */
	protected $transformItems = array(
		'id',
		'name',
		'age',
		'city',
		'province',
		'country',
		'created_at'
	);

	/**
	 * Create item based on the resource given
	 *
	 * @param array $resource
	 * @return null|string
	 */
	public function createItem($resource)
	{
		$this->ci->load->model($resource['model'], 'model');
		$postData = $this->ci->input->post();

		if (isset($postData['file']) === true) {

			$uploaded = $this->do_upload();

			if ($uploaded['status']  === true) {
				$response = 'Resource file uploaded and created successfully';
			} else {
				$response = 'Resource file upload failed. ' . $uploaded['error'];
			}
		} else {
			$this->ci->model->createItem($postData);
			$response = 'Resource created successfully';
		}

		return $response;
	}

	/**
	 * do_upload here because I am not sure what codeigniter is doing as far as mime type is concerned. Just had to craft mine.
	 *
	 * @return array
	 */
	private function do_upload()
	{
		$currentDir = getcwd();
		$uploadDirectory = "/uploads/";

		$errors = array(); // Store all foreseen and unforseen errors here

		$fileExtensions = array('json'); // Get all the file extensions

		$fileName = $_FILES['players_file']['name'];
		$fileSize = $_FILES['players_file']['size'];
		$fileTmpName  = $_FILES['players_file']['tmp_name'];
		$fileType = $_FILES['players_file']['type'];

		$fileNameArray = explode('.', $fileName);

		$fileExtension = end($fileNameArray);

		$fileExtension = strtolower($fileExtension);

		$uploadPath = $currentDir . $uploadDirectory . basename($fileName);

		if (! in_array($fileExtension, $fileExtensions)) {
			$errors[] = "This file extension is not allowed. Please upload a JPEG or PNG file";
		}

		if ($fileSize > 2000000) {
			$errors[] = "This file is more than 2MB. Sorry, it has to be less than or equal to 2MB";
		}

		if (empty($errors)) {
			$didUpload = move_uploaded_file($fileTmpName, $uploadPath);

			if ($didUpload) {
				$this->processFile($uploadPath);

				$response = array(
					'status' => true,
					'upload_data' => basename($fileName)
				);
			} else {
				$response = array(
					'status' => false,
					'error' => 'Failed to move the file to the uploads folder.'
				);
			}
		} else {
			$response = array(
				'status' => false,
				'error' => $errors
			);
		}

		return $response;
	}

	/**
	 * Process the uploaded JSON file
	 *
	 * @param string $file
	 */
	private function processFile($file) {
		$playersData = file_get_contents($file);
		$playersData  = json_decode($playersData, true);

		$this->ci->load->model('country');
		$this->ci->load->model('province');
		$this->ci->load->model('city');
		$this->ci->load->model('players');

		$batch = array();

		foreach ($playersData['Players'] as $player) {
			// Check for location availability in DB
			$country = $this->ci->country->getCountryByName($player['Location']['Country']);
			if (!$country) {
				$country = $this->ci->country->createItem(array('name' => $player['Location']['Country']));
			}

			$province = $this->ci->province->getProvinceByName($player['Location']['Province']);
			if (!$province) {
				$province = $this->ci->province->createItem(array('name' => $player['Location']['Province'], 'country_id' => $country['id']));
			}

			$city = $this->ci->city->getCityByName($player['Location']['City']);
			if (!$city) {
				$city = $this->ci->city->createItem(array('name' => $player['Location']['City'], 'country_id' => $country['id'], 'province_id' => $province['id']));
			}

			// We do not want to run one SQL per loop so... Build the batch.
			$batch[] = array(
				'name' => $player['Name'],
				'age' => $player['Age'],
				'city_id' => $city['id'],
				'province_id' => $province['id'],
				'country_id' => $country['id']
			);
		}

		// Insert the batch into players
		$this->ci->players->batchInsertPlayers($batch);
	}
}
