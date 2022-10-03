<?php

require_once ("api-class/model.php");

class banqueController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getBanque() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_banque WHERE id_bank =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant de la banque");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant de la banque !");
        $this->response($this->json($response), 200);
    }

    public function getBanques() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT u.*  FROM t_banque u order by u.nom_bank DESC";
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
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function insertBanque() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $banque = $_POST;
        $this->isExistBnk($banque['nom_bank']);

        $column_names = array('nom_bank');
        $keys = array_keys($banque);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $banque[$desired_key];
            }
            $columns = $columns . $desired_key . ',';
            $values = $this->esc($values) . "'" . $$desired_key . "',";
        }

        $response = array();
        $query = "INSERT INTO  t_banque (" . trim($columns, ',') . ") VALUES(" . trim($values, ',') . ")";

        if (!empty($banque)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_banque SET code_bank='BNK" . $lastInsertID . "' WHERE id_bank=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $banque,
                    "msg" => "banque  creee avec success!");

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

    public function updateBanque() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $banque = $_POST;
        $id = (int) $banque['id'];
        $this->isExistBnkUpdt($banque['banque']['nom_bank'], $id);

        $column_names = array('nom_bank');
        $keys = array_keys($banque['banque']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $banque['banque'][$desired_key];
            }
            $columns = $columns . $desired_key . "='" . $this->esc($$desired_key) . "',";
        }

        $query = "UPDATE t_banque SET " . trim($columns, ',') . " WHERE id_bank=$id";
        $response = array();
        if (!empty($banque)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $banque,
                    "msg" => "Banque  [BNK" . $id . "] modifiee avec success!");
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

    public function deleteBanque() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
             $this->isExistSomeOpeation($id);
            $query = "DELETE FROM t_banque WHERE id_bank = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Banque supprimee avec success!");
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

    private function isExistBnk($bnk) {
        $bnk = $this->esc($bnk);
        $query = "SELECT id_bank FROM t_banque WHERE nom_bank ='$bnk'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cette banque existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistBnkUpdt($bnk, $id) {
        $bnk = $this->esc($bnk);
        $query = "SELECT id_bank FROM t_banque WHERE nom_bank ='$bnk' AND id_bank !=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cette Banque existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }
    
     private function isExistSomeOpeation($id) {

        $query = "SELECT id_vrsmnt FROM t_versement WHERE bank_vrsmnt =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cet Article a deja participe a des operations de versements ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        } 
    }

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new banqueController;
    $app->processApp();
}
?>