<?php

require_once ("api-class/model.php");
require_once ("api-class/helpers.php");

class servicesController extends model {

    public $data = "";

    public function __construct() {
        parent::__construct();
    }
 
    
    public function serviceGetCreditEncoursOfClient($id_clt) {
       
        $query = "SELECT  
              SUM(fv.crdt_fact-fv.som_verse_crdt-remise_vnt_fact) as mnt_credit_en_cours,
            FROM t_facture_vente fv 
             INNER JOIN t_client cl ON fv.clnt_fact=cl.id_clt
            WHERE fv.clnt_fact=$id_clt fv.bl_fact_crdt=1 AND fv.bl_crdt_regle=0 AND fv.crdt_fact>0 AND (fv.crdt_fact-fv.som_verse_crdt-fv.remise_vnt_fact)>0 LIMIT 1";

         $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

         $row = $r->fetch_assoc();
         
         return $row[mnt_credit_en_cours];
    }    
} 
 
?>