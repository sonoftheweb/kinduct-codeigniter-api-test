<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller {

	/* Status code to be displayed when request is responded to.*/
	protected $statusCode = 200;

	/**
	 * Return status code
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * Set a status code
	 *
	 * @param int $statusCode
	 * @return object $this
	 */
	public function setStatusCode($statusCode)
	{
		$this->statusCode = $statusCode;
		//we return $this because we sometimes respond stuff like this:
		//$this->setStatusCode(202)->respond('message');
		return $this;
	}

	/**
	 * Respond to all request depending on the results
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function respond($data)
	{
		return $this->output
			->set_content_type('application/json')
			->set_status_header($this->getStatusCode())
			->set_output(json_encode($data));
	}

	/**
	 * Respond with errors, only called for errors
	 *
	 * @param Throwable $throwable
	 * @return mixed
	 */
	public function respondWithError(\Throwable $throwable)
	{
		$this->setStatusCode($throwable->getCode());

		if (
			$this->statusCode > 700 ||
			$this->statusCode == 0 ||
			$this->statusCode == 42 ||
			$this->statusCode == 22 ||
			$this->statusCode == -1
		) {
			// Likely an SQL error (for instance: foreign key dependency, not respecting default value...), let's tell them this is not authorized
			$this->setStatusCode(401);
		}

		$response = array(
			'status' => 'failed',
			'message' => 'Something went wrong. Please try again or contact the developer.',
			'error' => array(
				'status_code' => $this->getStatusCode()
			),
		);

		if (getenv('CI_ENV')) {
			$response = array_merge($response, array(
				'message' => $throwable->getMessage(),
				'file' => $throwable->getFile(),
				'line' => $throwable->getLine(),
				'trace' => $throwable->getTraceAsString()
			));
		}

		return $this->respond($response);
	}

	/**
	 * Response for success
	 * @param int $statusCode
	 * @param array $response
	 * @return mixed
	 */
	public function respondWithSuccessMessage($statusCode, $response)
	{
		switch (gettype($response)) {
			case "array":
				return $this->setStatusCode($statusCode)->respond(array_merge(array(
					'status' => 'success'
				), $response));
			default:
				return $this->setStatusCode($statusCode)->respond(array(
					'status' => 'success',
					'message' => $response,
				));
		}
	}

	/**
	 * Resource map of all resources accessible via the api.
	 *
	 * @return array $mapping
	 */
	public function getResourceMap()
	{
		$mapping = array(
			'players' => array(
				'model' => 'players',
				'library' => 'playerstransformer', // Built our very own transformer system to filter data that is being sent out
				'isSmart' => true,
			),
		);

		foreach ($mapping as $key => &$data) {
			// Define resource used for mapping
			$data['resource'] = $key;
		}

		return $mapping;
	}

	/**
	 * Get the resource for a specific request
	 *
	 * @param array $resource
	 * @return array $resource
	 */
	public function getResource($resource)
	{
		$resourceMap = $this->getResourceMap();
		if (!isset($resourceMap[$resource])) {
			$response = array(
				'status' => 'failed',
				'message' => 'Resource not found.',
				'error' => array(
					'status_code' => $this->getStatusCode()
				),
			);

			return $this->respond($response);
		}

		$resource = $resourceMap[$resource];

		return $resource;
	}

	/**
	 * Route targeted method to get a collection (array) of non specific data.
	 *
	 * @param array $resource
	 * @return mixed
	 */
	public function getCollection($resource)
	{
		try {
			$resource = $this->getResource($resource);

			$this->load->library($resource['library'], null, 'lib');

			// handle all request to this resource in the transformers
			if (isset($resource['isSmart']) && $resource['isSmart'] == true) {
				return $this->respond($this->lib->smartTransformCollection($resource));
			}

			// Get collection @TODO: come back to do pagination count and stuff here if not smart
			// Load model here so it is not loaded twice if something up there requires the model
			$this->load->model($resource['model']);

			$items = $this->$resource['model']->getItems();

			return $this->respond(array(
				'data' => $this->lib->transformCollection($items)
			));
		} catch (\Throwable $e) {
			return $this->respondWithError($e);
		}
	}

	/**
	 * Route targeted method to get an item.
	 * Receives ID as the resource id to retrieve.
	 *
	 * @param array $resource
	 * @param int $id
	 * @return mixed
	 */
	public function getItem($resource, $id)
	{
		try {
			// If an ID is not provided and and for any reason it reaches here, (╯°□°）╯︵ ┻━┻
			if (!isset($id)) {
				throw new \Exception('Please provide a valid id.', 404);
			}

			$resource = $this->getResource($resource);

			$this->load->library($resource['library'], null, 'lib');

			// handle all request to this resource in the transformers
			if (isset($resource['isSmart']) && $resource['isSmart'] == true) {
				$data = $this->lib->smartTransform($resource, $id);
				return $this->respond($data);
			}

			//non-smart resource
			$item = $this->$resource['model']->getItem($id);

			return $this->respond(array(
				'data' => $this->lib->transform($item),
			));
		} catch (\Throwable $e) {
			return $this->respondWithError($e);
		}
	}

	/**
	 * Route targeted method to delete an item.
	 * @param array $resource
	 * @param int $id
	 * @return mixed
	 */
	public function deleteItem($resource, $id)
	{
		try {

			$resource = $this->getResource($resource);

			$this->load->library($resource['library'], null, 'lib');

			$message = $this->lib->deleteItem($resource, $id);

			return $this->respondWithSuccessMessage(201, $message);
		} catch (\Throwable $e) {
			return $this->respondWithError($e);
		}
	}

	/**
	 * Route targeted method to create an item and store in the db.
	 *
	 * @param array $resource
	 * @return mixed
	 */
	public function createItem($resource)
	{
		try {
			$resource = $this->getResource($resource);

			$this->load->library($resource['library'], null, 'lib');

			$message = $this->lib->createItem($resource);

			return $this->respondWithSuccessMessage(201, $message);
		} catch (\Throwable $e) {
			return $this->respondWithError($e);
		}
	}

	/**
	 * Route targeted method to update a database item.
	 *
	 * @param array $resource
	 * @param int $id
	 * @return mixed
	 */
	public function updateItem($resource, $id)
	{
		try {
			$resource = $this->getResource($resource);

			$this->load->library($resource['library'], null, 'lib');

			$message = $this->lib->updateItem($resource, $id);

			return $this->respondWithSuccessMessage(202, $message);
		} catch (\Throwable $e) {
			return $this->respondWithError($e);
		}
	}
}
