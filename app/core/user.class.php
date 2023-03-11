<?php

require_once ("api-class/model.php");

class userController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }

    public function getUser() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT u.*,COALESCE(m.nom_mag,'TOUS') as nom_mag  
                FROM t_user u LEFT JOIN t_magasin m ON u.mag_user=m.id_mag WHERE u.id_user =$id LIMIT 1";
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
                "message" => "Mauvais identifiant du user");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Veuillez fournir un identifiant du  user !");
        $this->response($this->json($response), 200);
    }

    public function getUsers() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT u.id_user,
                         u.nom_user,
                         u.prenom_user,
                         u.login_user,
                         u.code_user,
                         u.profil_user,
                         u.veille,
                         u.actif,
                         COALESCE(m.nom_mag,'TOUS') as mag_user                         
            FROM t_user u LEFT JOIN t_magasin m ON m.id_mag=u.mag_user
            WHERE u.login_user not in('super','brou','root') order by u.code_user";
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

    public function insertUser() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $user = $_POST;
        $this->isExistcode($user['code_user']);
        $this->isExistlogin($user['login_user']);

        $column_names = array('nom_user', 'login_user', 'prenom_user', 'code_user', 'mail_user');
        $keys = array_keys($user);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $this->esc($user[$desired_key]);
            }
            $columns = $columns . $desired_key . ',';
            $values = $values . "'" . $$desired_key . "',";
        }


        $mag_user = ($user['mag_user']) ? intval($user['mag_user']) : 0;


        $response = array();
        $query = "INSERT INTO  t_user (" . trim($columns, ',') . ",mag_user,date_crea_user,profil_user,pass_user) VALUES(" . trim($values, ',') . "," . $mag_user . ",now()," . intval($user['profil_user']) . ",md5('2014stock'))";

        if (!empty($user)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $user,
                    "message" => "utilisateur cree avec success!");

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

    public function updateUser() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $user = $_POST;
        $id = (int) $user['id'];
        
        $this->isExistcodeUpdt($user['user']['code_user'], $id);
        $this->isExistloginUpdt($user['user']['login_user'], $id);

        $column_names = array('nom_user', 'login_user', 'prenom_user', 'code_user', 'mail_user','regl_credit','vente_credit','facture_vente_annulee','droit_facture_vente_annulee_today','droit_controle_prix_vente','droit_reglement_facture_credit');
        $keys = array_keys($user['user']);
        $columns = '';
        $values = '';
        foreach ($column_names as $desired_key) {
            if (!in_array($desired_key, $keys)) {
                $$desired_key = '';
            } else {
                $$desired_key = $this->esc($user['user'][$desired_key]);
            }
            $columns = $columns . $desired_key . "='" . $$desired_key . "',";
        }

        $mag_user = $user['user']['mag_user'] ? intval($user['user']['mag_user']) : 0;

        $query = "UPDATE t_user SET " . trim($columns, ',') . ",mag_user=" . $mag_user . ",profil_user=" . intval($user['user']['profil_user']) . " WHERE id_user=$id";

        $response = array();

        if (!empty($user)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => $user,
                    "message" => "Utilisateur [" . $id . "] modifie avec success!");
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

    public function _() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $response = array();

        $user = $_POST;
        $id = (int) $user['id'];
        if (!empty($user)) {

            if (!empty($user['user']['apwd_user']) && md5($user['user']['apwd_user']) != $user['user']['pass_user']) {
                $response = array("status" => 0,
                    "datas" => "-1",
                    "message" => "Mot de passe actuel incorrect");
                $this->response($this->json($response), 200);
            }

            $column_names = array('nom_user', 'mob_user', 'prenom_user', 'mail_user');
            $keys = array_keys($user['user']);
            $columns = '';
            $values = '';
            foreach ($column_names as $desired_key) {
                if (!in_array($desired_key, $keys)) {
                    $$desired_key = '';
                } else {
                    $$desired_key = $this->esc($user['user'][$desired_key]);
                }
                $columns = $columns . $desired_key . "='" . $$desired_key . "',";
            }

            if (!empty($user['user']['apwd_user']))
                $query = "UPDATE t_user SET " . trim($columns, ',') . ",pass_user=md5('" . $user['user']['npwd_user'] . "') WHERE id_user=$id";
            else
                $query = "UPDATE t_user SET " . trim($columns, ',') . " WHERE id_user=$id";



            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Profil Modifie avec success!");
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
    
    
    

    public function deleteUser() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $query = "DELETE FROM t_user WHERE id_user = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Utilisateur supprime avec success!");
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

    private function isExistcode($code) {

        $query = "SELECT code_user FROM t_user WHERE code_user ='$code'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce code utilisateur existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistcodeUpdt($code, $id) {

        $query = "SELECT code_user FROM t_user WHERE code_user ='$code' AND id_user!=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce code utilisateur existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistlogin($login) {

        $login = $this->esc($login);
        $query = "SELECT login_user FROM t_user WHERE login_user ='$login'";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce identifiant/login utilisateur existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    private function isExistloginUpdt($login, $id) {

        $login = $this->esc($login);
        $query = "SELECT login_user FROM t_user WHERE login_user ='$login' AND id_user!=$id";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $response = array("status" => 0,
                "datas" => "-1",
                "message" => "Ce identifiant/login utilisateur existe deja ..Impossible de continuer l'operation");
            $this->response($this->json($response), 200);
        }
    }

    public function setStat() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];



        $query = "UPDATE  t_user set actif= $status WHERE id_user=$id";
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

    public function setVeil() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id'];
        $status = (int) $client['s'];



        $query = "UPDATE  t_user set veille= $status WHERE id_user=$id";
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
    
     public function setPs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $client = $_GET;
        $id = (int) $client['id']; 

        $query = "UPDATE  t_user set pass_user= md5('2014stock') WHERE id_user=$id";
        $response = array();
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
            $response = array("status" => 0,
                "datas" => "",
                "message" => "Mot de passe reinitialise avec success");
            $this->response($this->json($response), 200);
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
    }

    /**
     * Recuperer la liste des profils
     */
    public function getProfils() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        $query = "SELECT `id_profil`, `lib_profil`, `code_profil` FROM `t_profil` WHERE 1";
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
    
    public function getProfil() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }

        if (!empty($this->_request['id'])) {
            $id = intval($this->_request['id']);
            $query = "SELECT `id_profil`, `lib_profil`, `code_profil` FROM `t_profil` WHERE id_profil =$id LIMIT 1";
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
                "message" => "Mauvais identifiant de profil");
            $this->response($this->json($response), 200);
        }

        $response = array("status" => 1,
            "datas" => "",
            "message" => "Veuillez fournir un identifiant du  user !");
        $this->response($this->json($response), 200);
    }

    /**
     * Ajouter un profil
     */
    public function insertProfil() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $profil = $_POST;


        $response = array();
        $query = "INSERT INTO  t_profil (code_profil,lib_profil) VALUES(UPPER('".$profil['code_profil']."'),UPPER('".$profil['lib_profil']."'))";

        if (!empty($profil)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $user,
                    "message" => "profil ajouté avec success!");
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
    
    public function updateProfil() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $response = array();

        $user = $_POST;
        $id = (int) $user['id'];
        if (!empty($user)) {

            if (!empty($user['user']['apwd_user']) && md5($user['user']['apwd_user']) != $user['user']['pass_user']) {
                $response = array("status" => 0,
                    "datas" => "-1",
                    "message" => "Mot de passe actuel incorrect");
                $this->response($this->json($response), 200);
            }

            $column_names = array('nom_user', 'mob_user', 'prenom_user', 'mail_user');
            $keys = array_keys($user['user']);
            $columns = '';
            $values = '';
            foreach ($column_names as $desired_key) {
                if (!in_array($desired_key, $keys)) {
                    $$desired_key = '';
                } else {
                    $$desired_key = $this->esc($user['user'][$desired_key]);
                }
                $columns = $columns . $desired_key . "='" . $$desired_key . "',";
            }

            if (!empty($user['user']['apwd_user']))
                $query = "UPDATE t_user SET " . trim($columns, ',') . ",pass_user=md5('" . $user['user']['npwd_user'] . "') WHERE id_user=$id";
            else
                $query = "UPDATE t_user SET " . trim($columns, ',') . " WHERE id_user=$id";
                $file = fopen("fichier.txt", "a");
                fwrite($file,json_encode($user['user']['apwd_user']));
                fclose($file);


            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Profil Modifie avec success!");
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
    
    
    public function updateProfil2() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $profil = $_POST['profil'];
        $id = $_POST['id'];


        $response = array();
        $query = "UPDATE  t_profil SET code_profil=UPPER('".$profil['code_profil']."'),lib_profil=UPPER('".$profil['lib_profil']."') WHERE id_profil=".intval($profil['id_profil']);

        if (!empty($profil)) {
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);

                $response = array("status" => 0,
                    "datas" => $user,
                    "message" => "profil modifié avec success!");
                $this->response($this->json($response), 200);
            } catch (Exception $exc) {
                $response = array("status" => 1,
                    "datas" => "",
                    "message" => $exc->getMessage().json_encode($r));

                $this->response($this->json($response), 200);
            }
        }
        else
            $this->response('', 204);
    }

    
    public function deleteProfil() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id = (int) $this->_request['id'];
        if ($id > 0) {
            $query = "DELETE FROM t_profil WHERE id_profil = $id";
            $response = array();
            try {
                if (!$r = $this->mysqli->query($query))
                    throw new Exception($this->mysqli->error . __LINE__);
                $response = array("status" => 0,
                    "datas" => "",
                    "message" => "Profil supprimé avec success!");
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
    // lll
    public function getDroit() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $droit = $_POST['droit'];

        $response = array();
        $query = "SELECT `id_droit`, `code_droit`, `lib_droit`,
        if((SELECT id from t_profil_droit WHERE t_profil_droit.profil_id=".$droit['profil_id']."
         AND t_profil_droit.mag_id=".$droit['mag_id']." 
         AND t_profil_droit.droit_id=t_droit.id_droit ),1,0) as etat,
         (SELECT code_mag FROM t_magasin WHERE id_mag = ".$droit['mag_id'].") as magasin,
         (SELECT code_profil FROM t_profil WHERE id_profil = ".$droit['profil_id'].") as profil
         FROM `t_droit` WHERE 1";
         
        try {
            if (!$r = $this->mysqli->query($query))
                throw new Exception($this->mysqli->error . __LINE__);
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
        } catch (Exception $exc) {
            $response = array("status" => 1,
                "datas" => "",
                "message" => $exc->getMessage());
            $this->response($this->json($response), 200);
        }
        
        $this->response('', 204);
    }
    

    public function saveDroits() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $droit = $_POST['droit'];
        $query = "DELETE FROM t_profil_droit WHERE
          profil_id=".$droit['profil_id']."
         AND mag_id=".$droit['mag_id'];
         
         $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

         $profil_droits = $_POST['profil_droits'];

         foreach ($profil_droits as $pd) {
             if(!$pd['etat'])continue;
            $query = "INSERT INTO `t_profil_droit`(`profil_id`, `mag_id`, `droit_id`) 
            VALUES (".$droit['profil_id'].",".$droit['mag_id'].",".$pd['id_droit'].")";
            // $file = fopen("fichier.txt", "a");
            // fwrite($file,$query);
            // fclose($file);
            $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
         }
                    
        $response = array("status" => 0,
            "datas" => $result,
            "message" => "");
        $this->response($this->json($response), 200);

    }
}

session_name('SessSngS');
session_start();
if (isset($_SESSION['userId'])) {
    $app = new userController;
    $app->processApp();
}
?>