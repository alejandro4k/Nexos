<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
use chriskacerguis\RestServer\RestController;
require(APPPATH.'/libraries/RestController.php');
require(APPPATH.'/libraries/Format.php');
*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}
/*
*/


class Api extends CI_Controller{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('Auth');

    }
    //aqui se dirijiran las llamadas desde react para comunicarse a la base de datos
    public function index(){
        echo "entra";
    }
    public function test(){
        echo $this->db->query("SELECT VERSION()")->row('version');
        /*
        $array = array("Hola","mundo","codeigniter");
        $this->response($array);
        */
    }
    
    public function Validate(){
        echo "entra";
        die;
        $username = $this->input->post('user');
        $pass = $this->input->post('pass');
		$res = $this->Auth->login($username,$pass);
		if($res){
			$data_res['status']= True;
			//$data_res['url']= base_url('Dashboard');
			$data = array(
				"id"=> $res->id_usuario,
				"nombre_usuario" =>$res->nombre,
				"is_loged"=> TRUE
			);
			$this->session->set_userdata($data);
		}else{
			$data_res['status']= False;
			$data_res['msg']='Usuario o contraseña incorrectos';
		}
		/*
		var_dump($res->id_usuario);
		die;
		*/
		$this->response($data_res);
		die;
    }



}