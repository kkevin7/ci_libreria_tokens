<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*Impotacion de la liberia de REST_Controller*/
require APPPATH . 'libraries/Rest_Controller.php';
require APPPATH . 'libraries/Format.php';
/* Espeficiacion de header en esta parte de verifica los que esta
dispuesto aceptar nuestra aplicación */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Genero extends REST_Controller
{

  public function __construct()
  {
    parent::__construct("rest");
    // Cargaremos el modelo de genero y el de usuarios
    $this->load->model(array('Usuarios_model', 'Genero_model'));
    // llamaremos a la liberiaria de jwt que nos ayudara con utilización de tokens
    $this->load->helper(['jwt', 'authorization']);
  }

  public function index_options()
  {
    return $this->response(NULL, REST_Controller::HTTP_OK);
  }

  public function index_get()
  {
    if (!is_null($this->Genero_model->findAll())) {
      $this->response($this->Genero_model->findAll(), 200);
    } else {
      $this->response(array('error' => 'No existen registros'), 404);
    }
  }

  public function login_post()
  {
    // Obtenemos los datos de la peticion POST
    $usuario = array(
      'username' => $this->post('username'),
      'password' => $this->post('password')
    );

    // Validamos que el usuario o la contraseña no se encuentre vacío
    if (isset($usuario['username']) && isset($usuario['password'])) {
      //validamos que el usuario se valido
      if ($this->Usuarios_model->login($usuario['username'], $usuario['password']) == true) {

        // Creamos un token que con la inforación que el usuario envie
        $token = AUTHORIZATION::generateToken(['username' => $usuario['username']]);
        // Preparamos la respuesta
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'token' => $token];
        //Enviamos la respuesta
        $this->response($response, $status);
      } else {
        // En caso que el usario no se encuentre en la base datos enviamops un mensaje
        $this->response(['msg' => 'Invalid username or password!'], parent::HTTP_NOT_FOUND);
      }
    } else {
      // Mensaje de validacion del usuario
      $this->response(array(
        'error' =>  'EL usuario o la contraseña estan vacías',
        'username' => $usuario['username'],
        'password' => $usuario['password']
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  private function verify_request()
  {
      // Obtenemos los header de la aplicación
      $headers = $this->input->request_headers();
      // Extraemos el token
      $token = $headers['Authorization'];
      // Usamos un try-catch para que nos se muera peticion 
      // JWT library throws exception if the token is not valid
      try {
          // Validamos que el token exista 
          // Si toda de manera correcta generar un respuesta con estatus 200 OK
          // Si no delvera un respuesta con estatus 401 UNAUTHORIZED que no esta autorizado
          $data = AUTHORIZATION::validateToken($token);
          if ($data === false) {
              $response = ['msg' => 'Unauthorized Access!', 'status' => parent::HTTP_UNAUTHORIZED];
              return $response;
          } else {
             $response = ['msg' => 'Successful Authorization!', 'status' => parent::HTTP_OK];
              return $response;
          }
      } catch (Exception $e) {
          // Si el token o si exite algun error genera un mensaje
          // Enviado que la petición no fue autorizada
          $response = ['msg' => 'Unauthorized Access! ', 'status' => parent::HTTP_UNAUTHORIZED];
          return $response;
      }
  }

}
