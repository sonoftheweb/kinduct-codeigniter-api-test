<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model
{
	// I could use raw SQL for absolute speed but ehh

	/**
	 * Database table affected via transformer
	 *
	 * @var $db_table
	 */
	public $db_table = null;

	/**
	 * Get resource items based on passed resource
	 *
	 * @param int $page
	 * @param int $perPage
	 * @return array $result
	 */
	public function getItems($page = 0, $perPage = 10)
	{
		$sql = "select * from " . $this->db_table . " LIMIT ?, ?"; // Using raw here as we might be doing some heavy calls here if the table is huge
		$sqlCount = "select count(*) as count from $this->db_table";

		$items = $this->db->query($sql, array($page, $perPage))->result_array();
		$count = $this->db->query($sqlCount)->row_array();

		$result = array(
			'items' => $items,
			'totalItems' => $count['count']
		);

		return $result;
	}

	/**
	 * Get resource item based on passed resource
	 *
	 * @param int $id
	 * @return array $item
	 */
	public function getItem($id)
	{
		$item = $this->db
			->where('id', $id)
			->get($this->db_table)->row_array();

		return $item;
	}

	/**
	 * Get data and create an item in the DB
	 *
	 * @param array $data
	 * @return array
	 */
	public function createItem($data)
	{
		$this->db->insert($this->db_table, $data);
		$last_id = $this->db->insert_id();
		return $this->db->where('id', $last_id)->get($this->db_table)->row_array();
	}

	/**
	 * Get data and update an item in the DB
	 *
	 * @param int $id
	 * @param array $data
	 * @return array|null
	 */
	public function updateItem($id, $data)
	{
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$this->db->set($key, $value);
			}

			$this->db->where('id', $id);
			$this->db->update($this->db_table);

			return $this->db->where('id', $id)->get($this->db_table)->row_array();
		}

		return null;
	}

	/**
	 * Delete item in db
	 *
	 * @param int $id
	 */
	public function deleteItem($id)
	{
		$this->db->delete($this->db_table, array('id' => $id));
	}
}
