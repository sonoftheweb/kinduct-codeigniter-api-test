<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once ('Api_model.php');

class Country extends Api_model
{
	/**
	 * Database table affected via transformer
	 *
	 * @var $db_table
	 */
	public $db_table = 'country';

	/**
	 * Get country by name
	 *
	 * @param string $name
	 * @return array $country
	 */
	public function getCountryByName($name)
	{
		$country = $this->db->select('*')
			->from($this->db_table)
			->where('name', $name)
			->get()
			->row_array();

		return $country;
	}
}
