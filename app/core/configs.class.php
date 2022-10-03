<?php

require_once ("api-class/model.php");

class configsController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getInfosOptions() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }


        $query = "SELECT * FROM t_configs WHERE 1=1 LIMIT 1";


        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        $result = $r->fetch_assoc();
        foreach ($result as $key => $value) {
            if ($key == "id_conf" || $key == "delai_pay_fact" || $key == "delai_bons" || $key == "user_sms" || $key == "pass_sms") {
                
            } else {
                if ($key != "val_tva" || $key != "val_bic")
                    $result[$key] = (boolean) $value;
                if ($key == "val_tva" || $key == "val_bic")
                    $result[$key] = $value * 100;
            }
        }
        $response = array("status" => 0,
            "datas" => $result,
            "msg" => "");
        $this->response($this->json($response), 200);

        $this->response('', 204);
    }

    public function updateOptions() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $response = array();

        $opt = $_POST;
        $id = (int) $opt['id'];
        if (!empty($opt)) {
            $opt['opt']['val_tva'] = $opt['opt']['val_tva'] / 100;
            $opt['opt']['val_bic'] = $opt['opt']['val_bic'] / 100;
            $column_names = array('tva_fact', 'bic_fact', 'prix_vari', 'prix_gros',
                'categorie_art', 'aff_pu', 'restrict_annul', 'dyn_load', 'pfl_strict',
                'grt', 'load_ipagld', 'mdltrsf', 'delai_pay_fact', 'val_tva', 'val_bic',
                'def_date', 'prix_achat', 'mdl_bon_att', 'mdl_cmd', 'mode_fact_uniq',
                'mode_clnt_uniq', 'delai_bons', 'pass_sms', 'user_sms');
            $keys = array_keys($opt['opt']);
            $columns = '';
            $values = '';
            foreach ($column_names as $desired_key) {
                if (!in_array($desired_key, $keys)) {
                    $$desired_key = '';
                } else {
                    if ($desired_key == "user_sms" || $desired_key == "pass_sms")
                        $$desired_key = "'".$opt['opt'][$desired_key]."'";
                    else
                        $$desired_key = doubleval($opt['opt'][$desired_key]);
                }
                $columns = $columns . $desired_key . "=" . $$desired_key . ",";
            }

            $query = "UPDATE t_configs SET " . trim($columns, ',') . " WHERE id_conf=$id";


            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "msg" => "Options de configurations Modifiees avec success!");
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
if (isset($_SESSION['userId'])) {
    $app = new configsController;
    $app->processApp();
}
?>