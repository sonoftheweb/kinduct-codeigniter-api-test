<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once ('Api_model.php');

class Province extends Api_model
{
	/**
	 * Database table affected via transformer
	 *
	 * @var $db_table
	 */
	public $db_table = 'province';

	/**
	 * Get province by name
	 *
	 * @param string $name
	 * @return array $province
	 */
	public function getProvinceByName($name)
	{
		$province = $this->db->select('*')
			->from($this->db_table)
			->where('name', $name)
			->get()
			->row_array();

		return $province;
	}
}
