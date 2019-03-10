<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('url');
	}

	public function index(){
		
		$this->db->select('t.*');
		$this->db->from('tables t');
		$liste = $this->db->get()->result_array();

		print_r($liste);

	}
	public function call1(){
		$userName = $this->session->userdata('userName');
		$userLastName = $this->session->userdata('userLastName');

		echo $userName.' '.$userLastName.' ';
		echo "call1";
	}
}
