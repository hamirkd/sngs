<?php

require_once ("api-class/model.php");

class sController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function gs() { 

        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        if (isset($_SESSION['userId'])) {
            $response = array("status" => 0,
                "datas" => 1,
                "message" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => 0,
                "message" => "");
            $this->response($this->json($response), 200);
        }
    }

}

session_name('SessSngS');
session_start();
$app = new sController;
$app->processApp();
?>