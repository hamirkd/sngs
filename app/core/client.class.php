<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");


class clientController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getClient() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT *  FROM t_client WHERE id_clt =$id LIMIT 1";
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
                "msg" => "Mauvais identifiant du client");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant du  client !");
        $this->response($this->json($response), 200);
    }

    public function getClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $cond = " 1=1 ";

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $cond = " c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";

        $query = "SELECT c.id_clt,c.exo_tva_clt,c.code_clt,c.nom_clt,c.max_crdt_clt,c.credit_en_cours_clt  FROM t_client c WHERE actif=1 
            AND $cond order by c.nom_clt";


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

    public function getOrClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $cond = " 1=1 ";

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $cond = "c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";

        $query = "SELECT c.id_clt,c.exo_tva_clt,c.code_clt,c.nom_clt,c.max_crdt_clt,c.credit_en_cours_clt  FROM t_client c WHERE actif=1 
            AND $cond order by c.nom_clt";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $resul = array("id_clt" => 0, "exo_tva_clt" => 0, "code_clt" => "ORD", "nom_clt" => "1_Ordinaire", "max_crdt_en_cours" => 0);
            $result = array();
            $result[] = $resul;
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

    public function getaClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['mode_clnt_uniq'] == 0 && $_SESSION['userMag'] > 0)
            $query = "SELECT c.*  FROM t_client c 
             WHERE c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
                 order by c.nom_clt";
        else
            $query = "SELECT c.*  FROM t_client c";

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

    public function getClientaCreances() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $c_us = " AND 1=1 ";
        if ($_SESSION['mode_clnt_uniq'] == 0 && $_SESSION['userMag'] > 0)
            $c_us = " AND c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")";


        $condMag = "";
        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT c.id_clt,c.code_clt,c.nom_clt
                           FROM 
                           t_facture_vente f
                           INNER JOIN t_client c ON f.clnt_fact=c.id_clt 
                           WHERE f.bl_fact_crdt=1 AND f.bl_fact_grt=0 AND f.sup_fact=0
                          $c_us
                           AND f.bl_crdt_regle=0 AND f.crdt_fact>0 AND (f.crdt_fact-f.som_verse_crdt)>0 $condMag
                           GROUP BY c.id_clt ORDER BY c.nom_clt ASC";


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

    public function getLmClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $query = "SELECT c.*  FROM t_client c 
             WHERE c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
                 order by c.nom_clt limit 20";
        else
            $query = "SELECT c.*  FROM t_client c limit 20";
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

    public function queryClients() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $qry = $this->esc($this->_request['q']);

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $query = "SELECT c.*  FROM t_client c 
             WHERE (c.nom_clt LIKE '%$qry%' OR c.tel_clt LIKE '%$qry%') AND  c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
                 order by c.nom_clt";
        else
            $query = "SELECT c.*  FROM t_client c WHERE (c.nom_clt LIKE '%$qry%' OR c.tel_clt LIKE '%$qry%')";

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

        if ($_SESSION['mode_clnt_uniq'] == 0)
            $query = "SELECT c.*  FROM t_client c 
             WHERE c.user_clt in (SELECT id_user from t_user where mag_user=" . $_SESSION['userMag'] . ")
                 order by c.nom_clt limit 20 offset $offset";
        else
            $query = "SELECT c.*  FROM t_client c limit 5 offset $offset";
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

    public function insertClient() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $client = $_POST;
        $column_names = array('nom_clt', 'sexe_clt', 'type_clt', 'adr_clt', 'bp_clt', 'regime_clt', 'situation_clt', 'division_clt', 'forme_juri_clt', 'ifu_clt', 'rccm_clt', 'pays_clt', 'ville_clt', 'tel_clt', 'mob_clt', 'fax_clt', 'mail_clt', 'siteweb_clt');
        $keys = array_keys($client);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "max_crdt_clt")
                    $$desired_key = intval($client[$desired_key]);
                else
                    $$desired_key = $this->esc($client[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            if ($desired_key == "max_crdt_clt")
                $values = $values . "" . $$desired_key . ",";

            if (($desired_key == "tel_clt") || ($desired_key == "mob_clt") || ($desired_key == "fax_clt"))
                $values = $values . "'" . cleanspaces($$desired_key) . "',";
            else
                $values = $values . "'" . $$desired_key . "',";
        }

        if (isset($client['exo_tva_clt']))
            $exo = intval($client['exo_tva_clt']);
        else
            $exo = 0;

        if (isset($client['max_crdt_clt']) && $client['max_crdt_clt'] > 0)
            $max_credit = doubleval($client['max_crdt_clt']);
        else
            $max_credit = 20000000;



        $response = array();
        $query = "INSERT INTO  t_client (" . trim($columns, ',') . ",exo_tva_clt,max_crdt_clt,user_clt) VALUES(" . trim($values, ',') . ",$exo,$max_credit," . $_SESSION['userId'] . ")";

        if (!empty($client)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $lastInsertID = $this->mysqli->insert_id;

                $rek_update = "UPDATE t_client SET tel_clt=replace(tel_clt,' ',''), code_clt='CLT" . $lastInsertID . "' WHERE id_clt=" . intval($lastInsertID);
                $r = $this->mysqli->query($rek_update) or die($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $client,
                    "msg" => "client cree avec success!");

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

    public function updateClient() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $client = $_POST;
        $id = (int) $client['id'];
        $column_names = array('nom_clt', 'exo_tva_clt', 'sexe_clt', 'max_crdt_clt', 'type_clt', 'adr_clt', 'bp_clt', 'regime_clt', 'situation_clt', 'division_clt', 'forme_juri_clt', 'ifu_clt', 'rccm_clt', 'pays_clt', 'ville_clt', 'tel_clt', 'mob_clt', 'fax_clt', 'mail_clt', 'siteweb_clt');
        $keys = array_keys($client['client']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                if ($desired_key == "max_crdt_clt" || $desired_key == "exo_tva_clt")
                    $$desired_key = intval($client['client'][$desired_key]);
                else
                    $$desired_key = $this->esc($client['client'][$desired_key]);
            }
            if ($desired_key == "max_crdt_clt" || $desired_key == "exo_tva_clt")
                $columns = $columns . $desired_key . "=" . $$desired_key . ",";

            if (($desired_key == "tel_clt") || ($desired_key == "mob_clt") || ($desired_key == "fax_clt"))
                $columns = $columns . $desired_key . "='" . cleanspaces($$desired_key) . "',";
            else
                $columns = $columns . $desired_key . "='" . $$desired_key . "',";
        }

        $query = "UPDATE t_client SET " . trim($columns, ',') . " WHERE id_clt=$id";
        $response = array();

        if (!empty($client)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $client,
                    "msg" => "Client client [CLT" . $id . "] modifie avec success!");
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

    public function deleteClient() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $this->isExistSomeOpeation($id);
            $query = "DELETE FROM t_client WHERE id_clt = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Client supprime avec success!");
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

    public function setStat() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];



        $query = "UPDATE  t_client set actif= $status WHERE id_clt=$id";
        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => "",
                "msg" => "");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    private function isExistSomeOpeation($id) {

        $query = "SELECT id_fact FROM t_facture_vente WHERE clnt_fact =$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "msg" => "Ce client a deja participe a des operations de facturations ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    public function getCreditEncoursOfClient() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT IFNULL(SUM(fv.crdt_fact-fv.som_verse_crdt-remise_vnt_fact),0) as mntcec
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.clnt_fact=$id AND fv.bl_fact_crdt=1 AND f.sup_fact=0 AND fv.bl_crdt_regle=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact)>0 LIMIT 1";
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
                "msg" => "Mauvais identifiant du client");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "msg" => "Veuillez fournie un identifiant du  client !");
        $this->response($this->json($response), 200);
    }

    public function replaceCustomer() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $data = $_POST;

        $anc = intval($data['anc']);
        $newe = intval($data['newe']);

        try {
            $this->mysqli->autocommit(FALSE);

            $query = "update t_vente SET clnt_vnt=$newe  WHERE clnt_vnt=$anc ";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $query = "update t_creance_client SET clnt_crce=$newe  WHERE clnt_crce=$anc ";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            $query = "update t_facture_vente SET clnt_fact=$newe  WHERE clnt_fact=$anc ";
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

            if ($anc != $newe) {
                $query = "delete from t_client WHERE id_clt=$anc";
                $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
            }

            $this->mysqli->commit();
            $this->mysqli->autocommit(TRUE);

            $response = array("status" => 0,
                "datas" => "",
                "msg" => "Fusion de client effectuee avec success!");

            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $this->mysqli->rollback();
            $this->mysqli->autocommit(TRUE);
            $response = array("status" => 1,
                "datas" => "",
                "msg" => $exc->getMessage());

            $this->response($this->json($response), 200);
        }
    }

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new clientController;
    $app->processApp();
}
?>