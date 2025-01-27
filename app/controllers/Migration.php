<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends CI_Controller {

	/**
	 * Run migrations
	 *
	 * @return void
	 */
	public function index()
	{
		$this->load->library('migration');

		if ($this->migration->current() === FALSE)
		{
			show_error($this->migration->error_string());
		}

		echo 'Done';
	}
}
