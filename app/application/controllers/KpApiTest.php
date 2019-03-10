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
		//$this->load->view('kontrolpaneli/main',$data);
		echo "KpApi Works!";
	}

	public function loginCheck(){

		$gelen = $this->input->post();
		if(!$gelen) return false;

		$formData = $gelen['formData'];

		$this->db->select('cu.*');
		$this->db->from('cms_users cu');
		$this->db->where('cu.userName',$formData['userName']);
		$this->db->where('cu.password',$formData['password']);
		$dbUser = $this->db->get()->row_array();

		if(!$dbUser){
			$data['code'] = 1;
			$data['sonuc'] = 'Kullanıcı adı veya şifre bulunamadı';
		}
		if($dbUser){
			$data['code'] = 2;
			$data['sonuc'] = 'Giriş Başarılı';

			$expiryDate = date('Y-m-d H:i:s',strtotime('+30 min'));
			$userToken = md5($dbUser['id'].'_'.$expiryDate);

			$data['userData'] = array(
				'userName' => $dbUser['userName'],
				'userToken' => $userToken,
				'expiryDate' => $expiryDate,
			);

			$gonderTable['user_id'] = $dbUser['id'];
			$gonderTable['expiry_date'] = $expiryDate;
			$gonderTable['token'] = $userToken;
			$this->db->insert('cms_user_token',$gonderTable);
		}

		echo json_encode($data);
	}

	public function setSess(){
		//$this->session->set_userdata('adminUser',array('id'=>2, 'ad'=>'ugur','soyad'=>'yilmaz'));
	}
	public function cUserLogout(){
		//header("Access-Control-Allow-Origin: http://localhost:8080");
		$gelen = $this->input->post();
		if(!$gelen) return false;
		$TOKEN = $gelen['token'];

		$this->db->where('token',$TOKEN);
		$this->db->delete('cms_user_token');
	}

	public function checkSess(){

		$gelen = $this->input->post();
		if(!$gelen) return false;
		//print_r($gelen);
		$TOKEN = $gelen['token'];

		$suanZaman = date('Y-m-d H:i:s');

		$this->db->select('ut.*, cu.userName');
		$this->db->from('cms_user_token ut');
		$this->db->join('cms_users cu','cu.id = ut.user_id','left');
		$this->db->where('ut.token',$TOKEN);
		$this->db->where('ut.expiry_date >',$suanZaman);
		$q = $this->db->get();
		$userToken = $q->row_array();
		if($userToken){
			//print_r($userToken);
			$userDataGonder['expiry_date'] = date('Y-m-d H:i:s',strtotime('+30 min'));
			$this->db->where('token',$userToken['token']);
			$this->db->update('cms_user_token',$userDataGonder);
			// expiry date yenile.
			$data['sonuc'] = 'user_var';

			$data['userData'] = array(
				'userName' => $userToken['userName'],
				'userToken' => $userToken['token'],
				'expiryDate' => $userDataGonder['expiry_date'],
			);



		}else{
			// token süresi doldu yeniden login iste.
			//echo "token süresi doldu. Login Redirect et ! ";
			$this->db->select('ut.*, cu.userName');
			$this->db->from('cms_user_token ut');
			$this->db->join('cms_users cu','cu.id = ut.user_id','left');
			$this->db->where('ut.expiry_date <',$suanZaman);
			$q = $this->db->get();
			$userToken = $q->result_array();
			if($userToken){
				foreach ($userToken as $key => $value) {
					$this->db->where('id', $value['id']);
					$this->db->delete('cms_user_token');
				}
			}



			$data['sonuc'] = 'no_user_exit';
		}
		echo json_encode($data);
		//$userName = 'ugur';
		//$adminUser = $this->session->userdata('adminUser');

		/*$data = array();
		$data['sonuc'] = '';

		if($adminUser){
			//echo "user var";
			$data['sonuc'] = 'user_var';
			$data['userData'] = $adminUser;
		}else{
			//echo "user yok";
			$data['sonuc'] = 'no_user_exit';
		}
		echo json_encode($data);*/
	}


	public function ciSifrele(){
		$this->load->library('encryption');
		$config['key'] = 'rmf554689iT';
		$config['cipher'] = 'aes-256';
		$config['mode'] = 'cbc';
		$config['driver'] = 'openssl';
		$this->encryption->initialize($config);
		$data = array();
		$data['name'] = 'Ugur Yilmaz';
		$data['id'] = 25;
		$data['email'] = 'ugur@gizliada.com';
		$data['issuedDate'] = strtotime("now");
		$plain_text = json_encode($data);
		$ciphertext = $this->encryption->encrypt($plain_text);
		echo $ciphertext;

	}

	public function sifreCoz(){
		$this->load->library('encryption');
		$config['key'] = 'rmf554689iT';
		$config['cipher'] = 'aes-256';
		$config['mode'] = 'cbc';
		$config['driver'] = 'openssl';
		$this->encryption->initialize($config);

		$ciphertext = '7ba47b2dc054220bbfdb18540b317b8b18b6ac87197fc80c575848e7e24b6696d375b55f26c415ec9e7ad500cfede49b5ded00a48449d15b19f70b7d7553ddce5DQBt32/ZmZs7+GfWxzLTGK1nWEBriQi2TjNxdzfypLp4/RqFfJQZtAk25fEmd0E1wHD9U+Ucgdq9c4Vwpehoo13u3SI8s5MaNYmBKAlC5qj32/t9tJknq0MCX3feFtvz1E05kVkMCnoT4TqteKEaA==';
		//echo $ciphertext;
		$decrypted = $this->encryption->decrypt(trim($ciphertext));
		//print_r($decrypted);
		$header_req = $this->input->get_request_header('Accept-Encoding', TRUE);
		print_r($header_req);


	}


	/*
	public function sifreleEski(){
		$secretKey = md5(base64_encode('rmf554689iT'));
		$iv = 1234567812345678;
		$method = 'AES-256-CBC';

		$data = array();
		$data['name'] = 'Ugur Yilmaz';
		$data['id'] = 25;
		$data['email'] = 'ugur@gizliada.com';
		$data['bilgileri'] = '123123123123123';
		$data['issuedDate'] = strtotime("now");
		//print_r($data);
		$dataStr = base64_encode(json_encode($data));
		$token = openssl_encrypt($dataStr, $method, $secretKey, 0, $iv);
		echo $token;
	}
	public function sifreKontrolEski(){
		$secretKey = md5(base64_encode('rmf554689iT'));
		$iv = 1234567812345678;
		$method = 'AES-256-CBC';
		$token = 'z8GqG74k1IP1QwWZ0gJGr4qXz
		VuQkYAOgnw6o3yoS/trtxAjCELcjh7lW5aNjFv
		4F3+bkpRyrpkLXF+9/LM+DaEzNKodLHKVCCii/3VEVebdqUx7aehF35TD9F3ZBYUIL16kl4YSfVdwd
		r5aCHKA9gPyo3N6qlpVD1TxkSbJCv0HAQkg+5j1dBl59V92vP2K2fbhVm2ZIfYrfeoXKrvahg==';
		$bilgilerB64 = openssl_decrypt($token, $method, $secretKey, 0, $iv);
		print_r(base64_decode($bilgilerB64));
	}*/


	public function dbRead(){
		$sonucToplam = $this->db->select('COUNT(p.id) toplam')
			->from('tpage p')->get()->row_array();

		//print_r($sonucToplam);

		$this->db->select('p.*  '); //c.name kategorisi
		$this->db->from('tpage p');
		//$this->db->join('tpage_cat c','c.id = p.parent','left');
		//$this->db->where('p.id > 450000');
		$this->db->limit(60, 453000);
		//$this->db->group_by('p.id');
		$sonuc = $this->db->get()->result_array();
		print_r($sonuc);

	}
	public function dbInsert(){
		return false;
		for ($i=314999; $i < 1000000; $i++) {
			$gonder['title'] = 'Örnek Haber Başlığı '.$i;
			$gonder['text'] = 'Örnek Haber İçeriği '.$i;

			$this->db->insert('tpage', $gonder);
		}
	}

}
