<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_city_table extends CI_Migration
{
	public function up(){
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' =>TRUE
			),

			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 90,
			),

			'province_id' => array(
				'type' => 'INT',
				'constraint' => 2,
				'unsigned' => TRUE,
			),

			'country' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
			),
		));

		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('city');
	}

	public function down(){
		$this->dbforge->drop_table('city');
	}
}
