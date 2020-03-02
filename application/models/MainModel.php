<?php
class MainModel extends CI_Model{
    function __construct(){
        $this->load->database();
    }
    public function login($user,$pass){
        $data = $this->db->get_where('usuarios',array('nombre'=> $user, 'contraseña'=>$pass),1);
        if($data->result_array()){

            return $data->row();
        }else{
            return false;
        }

    }
    public function listCategorias($id_user){
        $data = $this->db->get_where('categorias',array('id_usuario'=> $id_user));
        if($data->result_array()){

            return $data->result_array();
        }else{
            return false;
        }

    }
    public function checkUser($user){
        $data = $this->db->get_where('usuarios',array('nombre'=> $user),1);
        if($data->result_array()){

            return true;
        }else{
            return false;
        }

    }
    public function createUser($user,$pass){
        $arrayInsert = array(
            'nombre' => $user,
            'contraseña'=>$pass
        );
        $this->db->insert('usuarios', $arrayInsert);
        if($this->db->affected_rows()>0){
           
            

            return true;
      
        }else{
            return false;

        }
        


    }
    public function updateProducto($id_producto,$id_categoria,$nombre,$precio,$stock){
        $data = array(
            'id_categoria' => $id_categoria,
            'nombre' => $nombre,
            'precio' => $precio,
            'stock' => $stock
    );
    
    $this->db->where('id_producto', $id_producto);
    $this->db->update('productos', $data);
    if($this->db->affected_rows()>0){
        return true;
    }else{
        return false;
    }
    }
    public function listarProductos($id_user){
        //$data = $this->db->get_where('productos',array('id_usuario'=> $id_user));
        $sql = "select categorias.nombre_categoria,productos.* 
        from productos
        join categorias on productos.id_categoria = categorias.id_categorias
        where productos.id_usuario ='$id_user'";
        $data = $this->db->query($sql);
        if($data->result_array()){

            return $data->result_array();
        }else{
            return false;
        }
        

    }
    public function deleteProducto($id_producto){
        $this->db->where('id_producto', $id_producto);
        $this->db->delete('productos');
        if($this->db->affected_rows()>0){
            return true;
        }else{
            return false;
        }

    }



    public function addCategoria($user,$categoria){
        $arrayInsert = array(
            'id_usuario' => $user,
            'nombre_categoria'=>$categoria
        );
        $this->db->insert('categorias', $arrayInsert);
        if($this->db->affected_rows()>0){
           
            $response= true;
        }else{
            $response= false;

        }
        echo json_encode($response);
    }

    public function addProducto($user,$nombre,$precio,$stock,$categoria){
        $arrayInsert = array(
            'nombre' => $nombre,
            'id_categoria' => $categoria,
            'precio' => $precio,
            'stock'=>$stock,
            'id_usuario'=>$user


        );
        /*
        $arrayInsertCat = array(
            'id_usuario' => $user,
            'nombre_categoria' => 
        )
        */
        $this->db->insert('productos', $arrayInsert);
        if($this->db->affected_rows()>0){
           
            $response= true;
        }else{
            $response= false;

        }
        echo json_encode($response);


    }
    
}