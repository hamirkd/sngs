<?php
 
require_once ("api-class/model.php");

class uniteController extends model {

    public $data = "";
    public function __construct() {
        parent::__construct(); 
    }

    

    public function getUnite() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_unite_article WHERE id_unite =$id LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();
                 
                $response = array("status" => 0,
                    "datas" => $result,
                    "msg" => "");
                $this->response($this->json($response), 200);
            }
            $response = array("status" => 1,
                "datas" => "",
                "msg" => "Mauvais identifiant de l'unite");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant de l'unite !");
        $this->response($this->json($response), 200);
    }

    public function getUnites() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT u.*  FROM t_unite_article u order by u.nom_unite DESC";
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

    public function insertUnite() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $unite = $_POST;
        $column_names = array('nom_unite');
        $keys = array_keys($unite);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) { 
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $unite[$desired_key];
            }
            $columns = $columns . $desired_key . ',';
            $values = $this->esc($values) . "'" . $$desired_key . "',";
        }

        $response = array();
        $query = "INSERT INTO  t_unite_article (" . trim($columns, ',') . ") VALUES(" . trim($values, ',') . ")";

        if (!empty($unite)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_unite_article SET code_unite='UA" . $lastInsertID . "' WHERE id_unite=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $unite,
                    "msg" => "unite article creee avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204); 
    }

    public function updateUnite() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $unite = $_POST;
        $id = (int) $unite['id'];
        $column_names = array('nom_unite');
        $keys = array_keys($unite['unite']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) { 
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $unite['unite'][$desired_key];
            }
            $columns = $columns . $desired_key . "='" . $this->esc($$desired_key) . "',";
        }

        $query = "UPDATE t_unite_article SET " . trim($columns, ',') . " WHERE id_unite=$id";
        $response = array();
        if (!empty($unite)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $unite,
                    "msg" => "Unite article [UA" . $id . "] modifiee avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());
                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204); 
    }

    public function deleteUnite() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $query = "DELETE FROM t_unite_article WHERE id_unite = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Unite supprimee avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "msg" => $exc->getMessage());
                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204); 
    }
  

} 

session_name('SessSngS');
session_start(); 
if(isset($_SESSION['userId'])){
$app = new uniteController;
$app->processApp();
}
?>