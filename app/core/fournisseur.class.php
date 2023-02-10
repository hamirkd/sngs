<?php

require_once ("api-class/model.php");

class fournisseurController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getFournisseur() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_fournisseur WHERE id_frns =$id LIMIT 1";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($r->num_rows > 0) {
                $result = $r->fetch_assoc();

                $response = array("status" => 0,
                    "datas" => $result,
                    "message" => "");
                $this->response($this->json($response), 200);
            }
            $response = array("status" => 1,
                "datas" => "",
                "message" => "Mauvais identifiant du fournisseur");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Veuillez fournie un identifiant du  fournisseur !");
        $this->response($this->json($response), 200);
    }

    public function getFournisseurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $cond = " 1=1 ";

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $cond = " (f.user_frns in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ") OR f.id_frns=1)";


        $query = "";
        if ($_SESSION['userMag'] > 0)
            $query = "SELECT f.id_frns,f.code_frns,
            f.nom_frns,(f.dette_frns-f.dette_en_cours_frns) as dette_frns,
            f.sexe_frns,  f.type_frns,f.adr_frns,
            f.bp_frns,f.pays_frns,f.ville_frns,
            f.tel_frns,f.mob_frns,f.mail_frns,
            f.siteweb_frns FROM t_fournisseur f WHERE f.actif=1 
            AND  $cond
    order by f.nom_frns";
        else
            $query = "SELECT f.id_frns,f.code_frns,
            f.nom_frns,(f.dette_frns-f.dette_en_cours_frns) as dette_frns,
            f.sexe_frns,  f.type_frns,f.adr_frns,
            f.bp_frns,f.pays_frns,f.ville_frns,
            f.tel_frns,f.mob_frns,f.mail_frns,
            f.siteweb_frns FROM t_fournisseur f WHERE f.actif=1
    order by f.nom_frns";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function getaFournisseurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $cond = " 1=1 ";

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $cond = " f.user_frns in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";


        if ($_SESSION['userMag'] > 0)
            $query = "SELECT f.id_frns,f.code_frns,f.actif,
            f.nom_frns,(f.dette_frns-f.dette_en_cours_frns) as dette_frns,
            f.sexe_frns,  f.type_frns,f.adr_frns,
            f.bp_frns,f.pays_frns,f.ville_frns,
            f.tel_frns,f.mob_frns,f.mail_frns,
            f.siteweb_frns FROM t_fournisseur f WHERE  $cond
order by f.nom_frns";
        else
            $query = "SELECT f.id_frns,f.code_frns,f.actif,
            f.nom_frns,(f.dette_frns-f.dette_en_cours_frns) as dette_frns,
            f.sexe_frns,  f.type_frns,f.adr_frns,
            f.bp_frns,f.pays_frns,f.ville_frns,
            f.tel_frns,f.mob_frns,f.mail_frns,
            f.siteweb_frns FROM t_fournisseur f            
order by f.nom_frns";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
            $response = array("status" => 0,
                "datas" => $result,
                "message" => "");
            $this->response($this->json($response), 200);
        } else {
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        }
        $this->response('', 204);
    }

    public function insertFournisseur() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $fournisseur = $_POST;
        $column_names = array('nom_frns', 'sexe_frns', 'dette_frns', 'type_frns', 'adr_frns', 'bp_frns', 'pays_frns', 'ville_frns', 'tel_frns', 'mob_frns', 'fax_frns', 'mail_frns', 'siteweb_frns');
        $keys = array_keys($fournisseur);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "dette_frns")
                    $$desired_key = intval($fournisseur[$desired_key]);
                else
                    $$desired_key = $this->esc($fournisseur[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            if ($desired_key == "dette_frns")
                $values = $values . "" . $$desired_key . ",";
            else
                $values = $values . "'" . $$desired_key . "',";
        }



        $response = array();
        $query = "INSERT INTO  t_fournisseur (" . trim($columns, ',') . ",user_frns) VALUES(" . trim($values, ',') . "," . $_SESSION['userId'] . ")";

        if (!empty($fournisseur)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_fournisseur SET code_frns='FRNS" . $lastInsertID . "' WHERE id_frns=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $fournisseur,
                    "message" => "fournisseur cree avec success!");

                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());

                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204);
    }

    public function updateFournisseur() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $fournisseur = $_POST;
        $id = (int) $fournisseur['id'];
        $column_names = array('nom_frns', 'sexe_frns', 'dette_frns', 'type_frns', 'adr_frns', 'bp_frns', 'pays_frns', 'ville_frns', 'tel_frns', 'mob_frns', 'fax_frns', 'mail_frns', 'siteweb_frns');
        $keys = array_keys($fournisseur['fournisseur']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "dette_frns")
                    $$desired_key = intval($fournisseur['fournisseur'][$desired_key]);
                else
                    $$desired_key = $this->esc($fournisseur['fournisseur'][$desired_key]);
            }
            if ($desired_key == "dette_frns")
                $columns = $columns . $desired_key . "=" . $$desired_key . ",";
            else
                $columns = $columns . $desired_key . "='" . $$desired_key . "',";
        }

        $query = "UPDATE t_fournisseur SET " . trim($columns, ',') . " WHERE id_frns=$id";
        $response = array();

        if (!empty($fournisseur)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $fournisseur,
                    "message" => "Fournisseur fournisseur [CLT" . $id . "] modifie avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());
                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204);
    }

    public function deleteFournisseur() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $this->isExistSomeOpeation($id);
            $query = "DELETE FROM t_fournisseur WHERE id_frns = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Fournisseur supprime avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage());
                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204);
    }

    public function setStat() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];



        $query = "UPDATE  t_fournisseur set actif= $status WHERE id_frns=$id";
        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => "",
                "message" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    private function isExistSomeOpeation($id) {

        $query = "SELECT id_appro FROM t_approvisionnement WHERE frns_appro =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce fournisseur a deja participe a des operations de d'approvisionnements ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new fournisseurController;
    $app->processApp();
}
?>