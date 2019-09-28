<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*Impotacion de la liberia de REST_Controller*/
require APPPATH. 'libraries/Rest_Controller.php';
require APPPATH. 'libraries/Format.php';
/* Espeficiacion de header en esta parte de verifica los que esta
dispuesto aceptar nuestra aplicación */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Genero extends REST_Controller
{
    
  public function __construct(){
    parent::__construct("rest");
    // Cargaremos el modelo de genero y el de usuarios
    $this->load->model(array('Usuarios_model', 'Genero_model'));
    // llamaremos a la liberiaria de jwt que nos ayudara con utilización de tokens
    $this->load->helper(['jwt', 'authorization']); 
}

public function index_options(){
    return $this->response(NULL,REST_Controller::HTTP_OK);
}

public function index_get()
    {
        if (!is_null($this->Genero_model->findAll())) {
            $this->response($this->Genero_model->findAll(), 200);
        } else {
            $this->response(array('error' => 'No existen registros'), 404);
        }
    }
  

}
