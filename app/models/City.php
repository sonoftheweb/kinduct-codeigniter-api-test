<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once ('Api_model.php');

class City extends Api_model
{
	/**
	 * Database table affected via transformer
	 *
	 * @var $db_table
	 */
	public $db_table = 'city';

	/**
	 * Get city by name
	 *
	 * @param string $name
	 * @return array $city
	 */
	public function getCityByName($name)
	{
		$city = $this->db->select('*')
			->from($this->db_table)
			->where('name', $name)
			->get()
			->row_array();

		return $city;
	}
}