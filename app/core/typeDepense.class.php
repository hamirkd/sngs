<?php
 
require_once ("api-class/model.php");

class typeDepenseController extends model {

    public $data = "";
    public function __construct() {
        parent::__construct(); 
    }

    

    public function getTypeDepense() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_type_depense WHERE id_type_dep =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant du type de depense");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant du type de depense !");
        $this->response($this->json($response), 200);
    }

    public function getTypeDepenses() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT td.*  FROM t_type_depense td order by td.lib_type_dep";
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

    public function insertTypeDepense() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $typedepense = $_POST;
        $column_names = array('lib_type_dep');
        $keys = array_keys($typedepense);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {  
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $typedepense[$desired_key];
            }
            $columns = $columns . $desired_key . ',';
            $values = $values . "'" . $this->esc($$desired_key) . "',";
        }

        $response = array();
        $query = "INSERT INTO  t_type_depense (" . trim($columns, ',') . ") VALUES(" . trim($values, ',') . ")";

        if (!empty($typedepense)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_type_depense SET code_type_dep='TD" . $lastInsertID . "' WHERE id_type_dep=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $typedepense,
                    "msg" => "type depense cree avec success!");

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

    public function updateTypeDepense() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $typedepense = $_POST;
        $id = (int) $typedepense['id'];
        $column_names = array('lib_type_dep');
        $keys = array_keys($typedepense['t']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {  
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $typedepense['t'][$desired_key];
            }
            $columns = $columns . $desired_key . "='" . $this->esc($$desired_key) . "',";
        }

        $query = "UPDATE t_type_depense SET " . trim($columns, ',') . " WHERE id_type_dep=$id";
        $response = array();
        if (!empty($typedepense)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $typedepense,
                    "msg" => "Type depense [TD" . $id . "] modifie avec success!");
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

    public function deleteTypeDepense() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $query = "DELETE FROM t_type_depense WHERE id_type_dep = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Type depense supprime avec success!");
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
$app = new typeDepenseController;
$app->processApp();
}

?>