<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once ('Api_model.php');

class Players extends Api_model
{
	/**
	 * Database table affected via transformer
	 *
	 * @var $db_table
	 */
	public $db_table = 'players';

	/**
	 * Get players from db, replacing the extended class's method
	 *
	 * @param int $page
	 * @param int $perPage
	 * @return array $city
	 */
	public function getItems($page = 0, $perPage = 10)
	{
		$items = $this->db->select(array('players.id', 'players.name', 'players.age',  'players.created_at',  'players.updated_at', 'country.name as country', 'province.name as province', 'city.name as city'))
			->from('players')
			->join('city', 'city.id = players.city_id', 'left')
			->join('province', 'province.country_id = city.country_id', 'left')
			->join('country', 'country.id = city.country_id', 'left')
			->limit($perPage)
			->offset($page)
			->get();

		$sqlCount = "select count(*) as count from " . $this->db_table . ";";

		$items = $items->result_array();
		$count = $this->db->query($sqlCount)->row_array();

		$result = array(
			'items' => $items,
			'totalItems' => $count['count'],
		);

		return $result;
	}

	/**
	 * Get single player by id
	 *
	 * @param int $id
	 * @return array $item
	 */
	public function getItem($id)
	{
		$item = $this->db
			->select(array('players.id', 'players.name', 'players.age',  'players.created_at',  'players.updated_at', 'country.name as country', 'province.name as province', 'city.name as city'))
			->from('players')
			->where('players.id', $id)
			->join('city', 'city.id = players.city_id', 'left')
			->join('province', 'province.country_id = city.country_id', 'left')
			->join('country', 'country.id = city.country_id', 'left')
			->get()
			->row_array();

		return $item;
	}

	/**
	 * Add players in bulk
	 *
	 * @param $data
	 * @return void
	 */
	public function batchInsertPlayers($data)
	{
		$this->db->insert_batch('players', $data);
	}
}
