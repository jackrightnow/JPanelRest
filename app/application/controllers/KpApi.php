<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KpApi extends CI_Controller {

	function __construct(){
		parent::__construct();
		header("Access-Control-Allow-Origin: *");
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('url');
	}
	public function index(){
		echo "KpApi Works!";
	}

	public function formLogin(){
		$gelen = $this->input->post();
		if(!$gelen) return false;

		$fd = $gelen['formData'];
		//print_r($fd);
		//return false;
		$this->db->select('cu.*');
		$this->db->from('cms_users cu');
		$this->db->where('cu.userName',$fd['userName']);
		$this->db->where('cu.password',$fd['password']);
		$dbUser = $this->db->get()->row_array();

		if(!$dbUser){
			$data['code'] = 1;
			$data['sonuc'] = 'Kullanıcı adı veya şifre bulunamadı';
		}
		if($dbUser){
			$data['code'] = 2;
			$data['sonuc'] = 'Giriş Başarılı';
			$data['userData'] = $dbUser;
			$data['userData']['issuedDate'] = strtotime("now");
			unset($data['userData']['password']);

			$this->load->library('encryption');
			$config['key'] = 'rmf554689iT';
			$config['cipher'] = 'aes-256';
			$config['mode'] = 'cbc';
			$config['driver'] = 'openssl';
			$this->encryption->initialize($config);

			$userTokenId = $this->encryption->encrypt(json_encode($data['userData']));
			$data['userTokenId'] = $userTokenId;

			//$expiryDate = date('Y-m-d H:i:s',strtotime('+30 min'));
			//$userToken = md5($dbUser['id'].'_'.$expiryDate);

			/*$data['userData'] = array(
				'userName' => $dbUser['userName'],
				'userToken' => $userToken,
				'expiryDate' => $expiryDate,
			);
			$gonderTable['user_id'] = $dbUser['id'];
			$gonderTable['expiry_date'] = $expiryDate;
			$gonderTable['token'] = $userToken;
			$this->db->insert('cms_user_token',$gonderTable);*/
		}

		echo json_encode($data);
	}




}
