<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller{
    
    public function __construct()
    {
        
        parent::__construct();
       
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        
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
				"nombre_usuario" =>$res->usuario,
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
    public function Register(){
        $usuario = $this->input->post('username');
        $contraseña = password_hash($this->input->post('pass'),PASSWORD_DEFAULT);
        echo json_encode($this->MainModel->registerUser($usuario,$contraseña));
    }
    public function CreateClient(){
        $nombre = $this->input->post('Nombres');
        $apellidos = $this->input->post('Apellidos');
        $direccion = $this->input->post('Direccion');
        $Telefono = $this->input->post('Telefono');
        $Email = $this->input->post('Email');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $identificacion = $this->input->post('identificacion');

        $numeroCuenta = rand(1111111111,9999999999); 
        
        $clave = password_hash($this->input->post('clave'),PASSWORD_DEFAULT);
        $response =$this->MainModel->saveClient($nombre,$apellidos,$tipoDocumento,$direccion,$Telefono,$Email,$identificacion,$numeroCuenta,$clave);
            if($response){
                
                $this->load->library('email');
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'smtp.gmail.com';
                $config['smtp_port'] = '465';
                $config['_smtp_auth']=TRUE;
                $config['smtp_user'] = 'jhoanalejandro.anaya@gmail.com';
                $config['smtp_pass'] = 'jhoanalejandro1';
                $config['smtp_timeout'] = '60';
                $config['charset'] = 'utf-8';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = "html";
                $this->email->initialize($config);
                $this->email->from('jhoanalejandro.anaya@gmail.com','Alejandro Anaya');
                $this->email->to($Email);
                $this->email->subject("Bienvenido");
                $this->email->message("<p>Gracias por utilizar nuestros servicios tu numero de cuenta es:</p> <strong> $numeroCuenta </strong> y tu contraseña son tus <strong>ultimos 4 digitos de tu identificacion</strong>");
                
                if($this->email->send()){
                    echo "envia correo";
                }else{
                    echo $this->email->print_debugger();
                }
                
            }
        

        echo json_encode($response);


    }
    public function SearchClient(){
        $identificacion =$this->input->post('identificacion');
        echo json_encode($this->MainModel->searchClient($identificacion));
        die;
    }
    public function SearchCuentas(){
        
        $identificacion =$this->input->post('identificacion');
        echo json_encode($this->MainModel->searchCuentas($identificacion));
        die;

    }
    public function CreateCuenta(){
        $numeroCuenta = rand(1111111111,9999999999); 
        
        $clave = password_hash($this->input->post('clave'),PASSWORD_DEFAULT);
        $id_cliente =$this->input->post('id_cliente');
        echo json_encode($this->MainModel->createCuenta($id_cliente,$numeroCuenta,$clave));
        /*
        if(password_verify('1234',$clave)){
            echo "contraseñas iguales";

        }else{
            echo "diferentes";
        }
        */

        die;

    }
    public function ValidateCuenta(){
        $clave = $this->input->post('clave');
        $id_cuenta = $this->input->post('id_cuenta');
        echo json_encode($this->MainModel->validateCuenta($clave,$id_cuenta));
    }
    public function Consignar(){
        $id_cuenta = $this->input->post('id_cuenta');
        $valor = $this->input->post('valor');
        $id_usuario = $this->input->post('id_usuario');
        echo json_encode($this->MainModel->consignarValor($id_cuenta,$valor,$id_usuario));

    }
    public function Retirar(){
        $id_cuenta = $this->input->post('id_cuenta');
        $valor = $this->input->post('valor');
        $id_usuario = $this->input->post('id_usuario');
        echo json_encode($this->MainModel->retirarValor($id_cuenta,$valor,$id_usuario));

    }
    public function SearchTransacciones(){
        echo json_encode($this->MainModel->historialTransacciones());
    }
    public function UpdateClient(){
        $nombre = $this->input->post('Nombres');
        $apellidos = $this->input->post('Apellidos');
        $direccion = $this->input->post('Direccion');
        $Telefono = $this->input->post('Telefono');
        $Email = $this->input->post('Email');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $identificacion = $this->input->post('identificacion');
        $id_cliente = $this->input->post('id_cliente');
        echo json_encode($this->MainModel->updateInfoCliente($nombre,$apellidos,$direccion,$Telefono,$Email,$tipoDocumento,$identificacion,$id_cliente));
    }
    public function SearchCuenta(){
        $numCuenta = $this->input->post('numCuenta');
        echo json_encode($this->MainModel->searchCuenta($numCuenta));
    }
    public function InactivarCuenta(){
        $id_cuenta = $this->input->post('id_cuenta');
        echo json_encode($this->MainModel->inactivarCuenta($id_cuenta));
    }
    public function activarCuenta(){
        $id_cuenta = $this->input->post('id_cuenta');
        echo json_encode($this->MainModel->activarCuenta($id_cuenta));
    }
}


?>