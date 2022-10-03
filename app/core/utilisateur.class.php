<?php

require_once ("api-class/model.php");

class utilisateurController extends model {

   public $data = "";
    public function __construct() {
        parent::__construct(); 
    }
 
    public function getUsers() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
         
        if($_SESSION['userMag']==0 && $_SESSION['userProfil']<2)
         $query = "SELECT login_user,code_user
              FROM t_user WHERE login_user not in('super','brou','root') AND veille=0 order by login_user";
        else
          $query = "SELECT login_user,code_user
              FROM t_user WHERE mag_user=".intval($_SESSION['userMag'])." AND login_user not in('super','brou','root') AND veille=0 order by login_user";
        
        
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "msg" => "");
            $this->response($this->json($response), 200);  
        }
        else {
             $response = array("status" => 0,
                "datas" =>"",
                "msg" => "");
            $this->response($this->json($response), 200);  
        }
        $this->response('', 204); 
    }
  

}

 

 session_name('SessSngS');
session_start(); 
if(isset($_SESSION['userId'])){
$app = new utilisateurController;
$app->processApp();
}
?>