<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_players_table extends CI_Migration{

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

			'age' => array(
				'type' => 'INT',
				'constraint' => 2,
				'unsigned' => TRUE,
			),

			'city_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
			),

			'province_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
			),

			'country_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'null' => TRUE,
			),

			'created_at' => array(
				'type' => 'DATETIME',
			),

			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),

		));

		$this->dbforge->add_key('id',TRUE);
		$this->dbforge->create_table('players');
	}

	public function down(){
		$this->dbforge->drop_table('players');
	}
}
