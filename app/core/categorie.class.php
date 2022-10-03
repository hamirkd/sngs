<?php

require_once ("api-class/model.php");

class categorieController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_categorie_article WHERE id_cat =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant de la categorie");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant de la categorie !");
        $this->response($this->json($response), 200);
    }

    
    public function getCategories() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT c.id_cat,c.nom_cat,c.code_cat  FROM t_categorie_article c order by CASE WHEN c.activite=" . $_SESSION['userMagAct'] . " THEN 0 ELSE 1 END ASC,c.nom_cat";
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
    
    
    public function loadMore() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $offset = doubleval($this->_request['offset']);
        $query = "SELECT c.*  FROM t_categorie_article c order by CASE WHEN c.activite=" . $_SESSION['userMagAct'] . " THEN 0 ELSE 1 END ASC,c.nom_cat limit 50 offset $offset";
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
    
    

    public function insertCategorie() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $categorie = $_POST;
        $this->isExistCat($categorie['nom_cat']);
        $column_names = array('nom_cat');
        $keys = array_keys($categorie);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $categorie[$desired_key];
            }
            $columns = $columns . $desired_key . ',';
            $values = $this->esc($values) . "'" . $$desired_key . "',";
        }

        $response = array();
        $query = "INSERT INTO  t_categorie_article (" . trim($columns, ',') . ",activite) VALUES(" . trim($values, ',') . "," . $_SESSION['userMagAct'] . ")";

        if (!empty($categorie)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_categorie_article SET code_cat='CA" . $lastInsertID . "' WHERE id_cat=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $categorie,
                    "msg" => "Categorie d'article creee avec success!");

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

    public function updateCategorie() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $categorie = $_POST;
        $id = (int) $categorie['id'];
        $this->isExistCatUpdt($categorie['categorie']['nom_cat'], $id);

        $column_names = array('nom_cat');
        $keys = array_keys($categorie['categorie']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $categorie['categorie'][$desired_key];
            }
            $columns = $columns . $desired_key . "='" . $this->esc($$desired_key) . "',";
        }

        $query = "UPDATE t_categorie_article SET " . trim($columns, ',') . " WHERE id_cat=$id";
        $response = array();
        if (!empty($categorie)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $categorie,
                    "msg" => "Categorie d'article [CA" . $id . "] modifiee avec success!");
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

    public function deleteCategorie() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $this->isExistSomeOpeation($id);
            $query = "DELETE FROM t_categorie_article WHERE id_cat = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Categorie supprimee avec success!");
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

    private function isExistCat($var) {
        $var = $this->esc($var);
        $query = "SELECT id_cat FROM t_categorie_article WHERE nom_cat ='$var'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cette categorie d'article existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistCatUpdt($var, $id) {

         $var = $this->esc($var);
        $query = "SELECT id_cat FROM t_categorie_article WHERE nom_cat ='$var' AND id_cat !=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Cette Categorie existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }
    
     private function isExistSomeOpeation($id) {

        $query = "SELECT id_art FROM t_article WHERE cat_art =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Des Article ont deja ete enregistres dan cette categorie ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        } 
    }

}

session_name('SessSngS');
session_start();

if (isset($_SESSION['userId'])) {
    $app = new categorieController;
    $app->processApp();
}
?>