<?php

require_once ("api-class/model.php");

class structureController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getInfosStruct() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT * FROM t_structure WHERE 1=1 LIMIT 1";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $result = $r->fetch_assoc();
        $response = array("status" => 0,
            "datas" => $result,
            "message" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }
    
    public function updateStructInfo() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $response = array();

        $struct = $_POST;
        $id = (int) $struct['id'];
        if (!empty($struct)) {

            $column_names = array('nom_struct','tel_struct','reg_imp_struct', 'abbr_nom_struct', 'mob_struct', 'div_fisc_struct','sigle_struct','bp_struct','situa_geo_struct','slogan_struct','fax_struct','directeur_struct','adr_struct');
            $keys = array_keys($struct['struct']);
            $columns = '';
            $values = '';
            foreach ($column_names as $desired_key) {
                if (!in_array($desired_key, $keys)) {
                    $$desired_key = '';
                } else {
                    $$desired_key = $this->esc($struct['struct'][$desired_key]);
                }
                $columns = $columns . $desired_key . "='" . $$desired_key . "',";
            }

                $query = "UPDATE t_structure SET " . trim($columns, ',') . " WHERE id_struct=$id";



            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Structure Modifiee avec success!");
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

}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new structureController;
    $app->processApp();
}
?>