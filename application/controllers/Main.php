<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller{
    
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        };
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('MainModel');
        
    }
    public function index(){
        echo "entra index";
    }
    public function test(){
        echo "entra";
    }
    public function Validate(){
        $username = $this->input->post('user');
        $pass = $this->input->post('pass');
		$res = $this->MainModel->login($username,$pass);
		if($res){
            $data_res['status']= True;
            $data_res['id_usuario']= $res->id_usuario;
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
		echo json_encode($data_res);
		die;
    }
    public function addProducto(){
        $user = $this->input->post('user');
        $producto = $this->input->post('producto');
        $stock = $this->input->post('stock');
        $precio = $this->input->post('precio');
        $categoria = $this->input->post('categoria');
        
        $this->MainModel->addProducto($user,$producto,$precio,$stock,$categoria);

    }
    public function deleteProducto(){
        $id_producto = $this->input->post('id_producto');
        echo json_encode($this->MainModel->deleteProducto($id_producto));

    }
    public function createUser(){
        $username = $this->input->post('username');
        $pass = $this->input->post('pass');
        if(!$this->MainModel->checkUser($username)){
            //agregar el usuario
            
            if($this->MainModel->createUser($username,$pass)){
                $res = $this->MainModel->login($username,$pass);
                
                if($res){

                    $response['status'] = true;
                    $response['id_usuario']=$res->id_usuario;
                }

            }else{
                $response['status'] = false;
                $response['msg'] = 'error al crear el usuario';
            }
        }else{
            //msg de respuesta
            $response['status'] = false;
            $response['msg'] = "el usuario ya existe";
            
        }
        echo json_encode($response);
    }
    public function updateProducto(){
        $id_producto = $this->input->post('id_producto');
        $id_categoria =$this->input->post('id_categoria');
        $nombre =$this->input->post('nombre');
        $precio =$this->input->post('precio');
        $stock =$this->input->post('stock');
        echo json_encode($this->MainModel->updateProducto($id_producto,$id_categoria,$nombre,$precio,$stock));


        

    }

    public function listarProductos(){
        $id_user = $this->input->post('id_usuario');
        echo json_encode($this->MainModel->listarProductos($id_user));

    }
    public function addCategoria(){
        $id_user = $this->input->post('id_user');
        $categoria = $this->input->post('categoria');
        $this->MainModel->addCategoria($id_user,$categoria);
    }
    public function listarCategorias(){
        $id_user = $this->input->post('id_user');
        echo json_encode($this->MainModel->listCategorias($id_user));
        die;
    }
}


?>