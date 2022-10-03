<?php

require_once ("api-class/model.php");

class magasinController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct(); 
    }

    

    public function getMagasin() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_magasin WHERE id_mag =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant du magasin");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant du  magasin !");
        $this->response($this->json($response), 200);
    }

    public function changerMagasin() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        
        if (!empty($this->_request['nom_mag'])) {
            $_SESSION['userMag'] = $this->_request['id_mag'];
            $_SESSION['nomMag'] = $this->_request['nom_mag'];
            $response = array("status" => 0,
                "datas" => array("userMag"=>$this->_request['id_mag'],"nomMag"=>$this->_request['nom_mag']),
                "msg" => "Vous avez changé de magasin");
            $this->response($this->json($response), 200);
        }
        $response = array("status" => -1,
                "datas" => "",
                "msg" => "Impossible de changer de magasin");
            $this->response($this->json($response), 200);
    }
    

    public function getMagasins() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] == 0)
            $query = "SELECT m.*  FROM t_magasin m order by m.nom_mag";
        else
            $query = "SELECT m.*  FROM t_magasin m WHERE id_mag=" . intval($_SESSION['userMag']) . " order by m.nom_mag";

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
    
    public function getExceptMagasins() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] == 0)
            $query = "SELECT m.*  FROM t_magasin m order by m.nom_mag";
        else
            $query = "SELECT m.*  FROM t_magasin m WHERE id_mag!=" . intval($_SESSION['userMag']) . " order by m.nom_mag";

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

    public function getAllMagasins() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT m.*  FROM t_magasin m order by m.nom_mag";

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

    public function getLimitedMagasins() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['userMag'] > 0)
            $query = "SELECT m.*  FROM t_magasin m where m.id_mag=".intval($_SESSION['userMag'])." order by m.nom_mag";
        else
            $query = "SELECT m.*  FROM t_magasin m order by m.nom_mag";

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

    public function insertMagasin() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $magasin = $_POST;
        $column_names = array('nom_mag','resp_mag','titre_resp_mag', 'type_mag', 'pays_mag', 'ville_mag', 'tel_mag', 'mob_mag', 'fax_mag', 'mail_mag');
        $keys = array_keys($magasin);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) { 
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $this->esc($magasin[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            $values = $values . "'" . $$desired_key . "',";
        }



        $response = array();
        $query = "INSERT INTO  t_magasin (" . trim($columns, ',') . ",date_crea_mag) VALUES(" . trim($values, ',') . ",now())";

        if (!empty($magasin)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_magasin SET code_mag='MAG" . $lastInsertID . "' WHERE id_mag=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $magasin,
                    "msg" => "magasin magasin cree avec success!");

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

    public function updateMagasin() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $magasin = $_POST;
        $id = (int) $magasin['id'];
        $column_names = array('nom_mag','resp_mag','titre_resp_mag', 'type_mag', 'pays_mag', 'ville_mag', 'tel_mag', 'mob_mag', 'fax_mag', 'mail_mag');
        $keys = array_keys($magasin['magasin']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) { 
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $this->esc($magasin['magasin'][$desired_key]);
            }
            $columns = $columns . $desired_key . "='" . $$desired_key . "',";
        }

        $query = "UPDATE t_magasin SET " . trim($columns, ',') . " WHERE id_mag=$id";
        $response = array();

        if (!empty($magasin)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $magasin,
                    "msg" => "Magasin magasin [MAG" . $id . "] modifie avec success!");
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

    public function deleteMagasin() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $query = "DELETE FROM t_magasin WHERE id_mag = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Magasin supprime avec success!");
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
$app = new magasinController;
$app->processApp();
}

?>