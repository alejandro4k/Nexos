<?php
class MainModel extends CI_Model{
    function __construct(){
        $this->load->database();
    }
    public function login($user,$contraseña){
        /*
        $pass= $data->clave;
        if(password_verify($contraseña,$pass)){
            return true;

        }else{
            return false;
        }
        */
        $data = $this->db->get_where('Usuarios',array('usuario'=> $user),1);
        if($data->result_array()){
            $data2= $data->row();
            $pass= $data2->contraseña;
            if(password_verify($contraseña,$pass)){
               
                return $data->row();
    
            }else{
                return false;
            }
        }else{
            return false;
        }

    }
    public function registerUser($usuario,$contraseña){
        $data = $this->db->get_where('Usuarios',array('usuario'=> $usuario),1);
       
        if(!$data->result_array()){
            $arrayInsert = array(
                'usuario'=>$usuario,
                'contraseña'=>$contraseña,
                
            );
            $this->db->insert('Usuarios', $arrayInsert);
            $data2 = $this->db->get_where('Usuarios',array('usuario'=> $usuario),1);
            $data2 = $data2->row();
           
            $response['status'] = true;
            $response['id_usuario']= $data2->id_usuario; 
            
        }else{
            
            $response['status']=false;
            $response['msg']= "Usuario no disponible.";
        }
        return $response;
        die;

    }
    public function searchClient($identificacion)
    {
        $data = $this->db->get_where('Clientes',array('identificacion'=> $identificacion),1);
        if($data->result_array()){
            
            return $data->row();
        }else{
            
            return false;
        }

    }
    public function searchCuentas(){
       
        $this->db->select('*');
        $this->db->from('Cuentas');
        $this->db->join('Clientes', 'Clientes.id_cliente = Cuentas.id_cliente');
        $this->db->order_by('Cuentas.created', 'DESC');
        $query = $this->db->get();
        if($query->result_array()){
            return $query->result_array();
        }else{

            return false;
        }

    }
    public function validateCuenta($clave,$id_cuenta){
        $data = $this->db->get_where('Cuentas',array('id_cuenta'=> $id_cuenta),1);
        if($data->result_array()){

            $data= $data->row();
            $pass= $data->clave;
            if(password_verify($clave,$pass)){
                return true;

            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function consignarValor($id_cuenta,$valor,$usuario){
        $data = $this->db->get_where('Cuentas',array('id_cuenta'=> $id_cuenta),1);
        if($data->result_array()){
            
            $data= $data->row();
            if($data->saldo ==0 and $data->activo ==='f' and $valor>=100000){
    
                $descripcion = "Consignación de cuenta No $data->numero_cuenta efectuada desde el cajero $usuario ";
                $arrayInsert = array(
                    'id_cuenta'=>$id_cuenta,
                    'monto'=>$valor,
                    'id_usuario'=> $usuario,
                    'created'=>date("Y-m-d H:i:s"),
                    'tipo'=>'Consignacion',
                    'descripcion'=> $descripcion
                );
                $this->db->insert('Transacciones', $arrayInsert);
                $dataUpdate = array(
                    'saldo' => $valor,
                    'activo'=> true
            );
            
                $this->db->where('id_cuenta', $id_cuenta);
                $this->db->update('Cuentas', $dataUpdate);
    
                if($this->db->affected_rows()>0){
     
                    $response['status']= true;
                    
                }else{
                    $response['status']= false;
                    
                }
            }else{
                if($data->activo === 't'){
                    $descripcion = "Consignación de cuenta No $data->numero_cuenta efectuada desde el cajero $usuario ";
                    $arrayInsert = array(
                        'id_cuenta'=>$id_cuenta,
                        'monto'=>$valor,
                        'id_usuario'=> $usuario,
                        'created'=>date("Y-m-d H:i:s"),
                        'tipo'=>'Consignacion',
                        'descripcion'=> $descripcion
                    );
                    $this->db->insert('Transacciones', $arrayInsert);
                    $dataUpdate = array(
                        'saldo' => $valor,
                        'activo'=> true
                );
                
                    $this->db->where('id_cuenta', $id_cuenta);
                    $this->db->update('Cuentas', $dataUpdate);
        
                    if($this->db->affected_rows()>0){
         
                        $response['status']= true;
                        
                    }else{
                        $response['status']= false;
                        
                    }

                }else{

                    $response['status']= false;
                    $response['msj']= "Se requiere consignar un monto mayor a 100000 para activar tu cuenta por primera vez.";
                }


            }


        }else{
            $response['status']= false;
            $response['msj']= "Error al consignar, intentalo de nuevo por favor.";
            
        }
        return $response;



    }
    public function retirarValor($id_cuenta,$valor,$usuario){
        $data = $this->db->get_where('Cuentas',array('id_cuenta'=> $id_cuenta),1);
        if($data->result_array()){
            
            $data= $data->row();
            if($data->saldo >= $valor){
                $saldo = ($data->saldo - $valor);
                $dataUpdate = array(
                    'saldo' => $saldo,
                    
            );
            
                $this->db->where('id_cuenta', $id_cuenta);
                $this->db->update('Cuentas', $dataUpdate);

                $descripcion = "Retiro de cuenta No $data->numero_cuenta por el valor de $valor efectuada desde el cajero $usuario ";
                $arrayInsert = array(
                    'id_cuenta'=>$id_cuenta,
                    'monto'=>$valor,
                    'id_usuario'=> $usuario,
                    'created'=>date("Y-m-d H:i:s"),
                    'tipo'=>'Retiro',
                    'descripcion'=> $descripcion
                );
                $this->db->insert('Transacciones', $arrayInsert);


    
                if($this->db->affected_rows()>0){
     
                    $response['status']= true;
                    $response['msj']= "Retiro exitoso, tu nuevo saldo es: $saldo";
                    
                }else{
                    $response['status']= false;
                    $response['msj']= "Error al retirar, intentalo de nuevo por favor.";
                    
                }

            }else{
                $response['status']= false;
                $response['msj']= "Dinero insuficiente en la cuenta";

            }


        }else{
            $response['status']= false;
            $response['msj']= "Error al retirar, intentalo de nuevo por favor.";
            
        }
        return $response;

    }

    public function updateInfoCliente($nombre,$apellidos,$direccion,$telefono,$Email,$tipoDocumento,$identificacion,$id_cliente){
       
        $dataUpdate = array(
            'Tipo_documento' => $tipoDocumento,
            'apellidos'=>$apellidos,
            'direccion'=>$direccion,
            'email'=>$Email,
            'nombres'=>$nombre,
            'telefono'=>$telefono,
            'identificacion'=>$identificacion
    );
    
        $this->db->where('id_cliente', $id_cliente);
        $this->db->update('Clientes', $dataUpdate);

        if($this->db->affected_rows()>0){

            $response['status']= true;
            
        }else{
            $response['status']= false;
            
        }
        return $response;
    }
    public function searchCuenta($numCuenta){
        $data = $this->db->get_where('Cuentas',array('numero_cuenta'=> $numCuenta),1);
        if($data->result_array()){

            return $data->row();
        }else{
            return false;
        }

    }
    public function inactivarCuenta($id_cuenta){
           
        $dataUpdate = array(
            'activo' => false
    );
    
        $this->db->where('id_cuenta', $id_cuenta);
        $this->db->update('Cuentas', $dataUpdate);

        if($this->db->affected_rows()>0){

            $response['status']= true;
            
        }else{
            $response['status']= false;
            
        }
        return $response;

    }
    public function activarCuenta($id_cuenta){
           
        $dataUpdate = array(
            'activo' => true
    );
    
        $this->db->where('id_cuenta', $id_cuenta);
        $this->db->update('Cuentas', $dataUpdate);

        if($this->db->affected_rows()>0){

            $response['status']= true;
            
        }else{
            $response['status']= false;
            
        }
        return $response;

    }

    public function saveClient($nombre,$apellidos,$tipoDocumento,$direccion,$telefono,$email,$identificacion,$numeroCuenta,$clave){
        $data = $this->db->get_where('Clientes',array('identificacion'=> $identificacion),1);
        
        if(!$data->result_array()){

            $arrayInsert = array(
                'Tipo_documento' => $tipoDocumento,
                'apellidos'=>$apellidos,
                'direccion'=>$direccion,
                'email'=>$email,
                'nombres'=>$nombre,
                'telefono'=>$telefono,
                'identificacion'=>$identificacion
            );
            $this->db->insert('Clientes', $arrayInsert);
            $data2 = $this->db->get_where('Clientes',array('identificacion'=> $identificacion),1);
           
    
                $id_cliente= $data2->row();  
                $id_cliente= $id_cliente->id_cliente;
                
                /*
                $sql = "INSERT INTO cuentas (numero_cuenta,id_cliente,created,clave,activo,saldo) VALUES('$numeroCuenta','$id_cliente',now(),'$clave',false,0)";
                $this->db->query($sql);
                */
                $arrayInsertCliente = array(
                    'numero_cuenta' => $numeroCuenta,
                    'clave'=> $clave,
                    'saldo'=>0,
                    'activo'=>false,
                    'id_cliente'=>$id_cliente,
                    'created'=>date("Y/m/d")
                );
                $this->db->insert('Cuentas', $arrayInsertCliente);
                
                if($this->db->affected_rows()>0){
                    
                    
                    
                    $response['status']= true;
                    
                }else{
                    $response['status']= false;
                    $response['msj']= "error al crear el cliente";
                    
                }
       
    }else{
        
        $response['status']= false;
        $response['msj']= "El cliente ya existe";

    }
    return $response;


    }
    public function createCuenta($id_cliente,$numeroCuenta,$clave){
        $arrayInsert = array(
            'numero_cuenta' => $numeroCuenta,
            'clave'=> $clave,
            'saldo'=>0,
            'activo'=>false,
            'id_cliente'=>$id_cliente,
            'created'=> date("Y/m/d") 
        );
        $this->db->insert('Cuentas', $arrayInsert);
        if($this->db->affected_rows()>0){
           
            

            return true;
      
        }else{
            return false;

        }

    }
    public function historialTransacciones(){
        $this->db->select('*');
        $this->db->from('Transacciones');
        $this->db->join('Cuentas', 'Cuentas.id_cuenta = Transacciones.id_cuenta');
        $this->db->order_by('Transacciones.created', 'DESC');
        $query = $this->db->get();
        return $query->result_array();

    }
    
    
}