<?php

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Login extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
    }

    public function index_get()
    {
        $this->response([
            "test" => "oke"
        ], REST_Controller::HTTP_OK);
    }
}
