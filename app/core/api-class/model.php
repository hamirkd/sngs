<?php

require_once(dirname(__FILE__). "/Rest.inc.php");
require_once (dirname(__FILE__). "/db.php");

class model extends REST {

    public $mysqli = NULL;
    public $proprietaire;

    public function __construct() {
        parent::__construct();
        $DB = new db();
        $DB->dbConnect($DB->getOptions());
        $this->mysqli = $DB->getMysqlObject();
        $this->mysqli->set_charset("utf8");
        $this->proprietaire = $DB->getProprietaire();
    }

    public function esc($str) {

        return $this->mysqli->real_escape_string($this->supac($str));
    }

    public function json($data) {
        if (is_array($data)) {
            return json_encode($data);
        }
    }

    public function processApp() {
        $func = trim(str_replace("/", "", $_REQUEST['x']));
        if ((int) method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404);
    }

    public function supac($str, $encoding = 'utf-8') {
        /* $str = htmlentities($str, ENT_NOQUOTES, $encoding);

          $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
          $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
          $str = preg_replace('#&[^;]+;#', '', $str);
          return $str; */
        /* return strtr($str,
          '���������������������������������������������������',
          'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'); */
        return $str;
    }

    /**
     * ==================================================================================
     * ==================================================================================
     * =================================== MODEL ========================================
     * ==================================================================================
     * ==================================================================================
     */

    /**
     * 
     * @param type $idArticle article a recherche la quantite approvio
     * @param type $date
     * @return int
     */
    public function getApproOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_appro_art = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_appro_art),0) as qteappro
                from t_approvisionnement_article a
                           WHERE a.art_appro_art=$idArticle
                           AND date(a.date_appro_art)='$date' $condmag ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qteappro'];
    }

    /**
     * 
     * @param type $idArticle article a rechercher les operations en quantites
     * @param type $date_debut date debut plage de periode
     * @param type $date_fin date e fin palge ed periode
     * @param type $magasin boutique concernees
     * @return type
     */
    public function getApproOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_appro_art = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_appro_art),0) as qteappro
                from t_approvisionnement_article a
                           WHERE a.art_appro_art=$idArticle
                           AND date(a.date_appro_art) between '$date_debut' AND '$date_fin' $condmag ";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qteappro'];
    }

    /**
     * 
     * @param type $idArticle article a recherche la quantite transferee
     * @param type $date
     * @return int
     */
    public function getTransGetfOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_dst_transf = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_transf),0) as qtetransfget
                from t_transfert a
                           WHERE a.art_transf=$idArticle
                           AND date(a.date_transf)='$date' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtetransfget'];
    }

    /**
     * 
     * @param type $idArticle
     * @param type $date_debut
     * @param type $date_fin
     * @param type $magasin
     * @return type
     */
    public function getTransGetfOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_dst_transf = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_transf),0) as qtetransfget
                from t_transfert a
                           WHERE a.art_transf=$idArticle
                           AND date(a.date_transf) between '$date_debut' AND '$date_fin' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtetransfget'];
    }

    /**
     * 
     * @param type $idArticle article a recherche la quantite transferee
     * @param type $date
     * @return int
     */
    public function getTransSetfOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_src_transf = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_transf),0) as qtetransfset
                from t_transfert a
                           WHERE a.art_transf=$idArticle
                           AND date(a.date_transf)='$date' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtetransfset'];
    }

    /**
     * Rcuper
     * @param type $idArticle
     * @param type $date_debut
     * @param type $date_fin
     * @param type $magasin
     * @return type
     */
    public function getTransSetfOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_src_transf = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_transf),0) as qtetransfset
                from t_transfert a
                           WHERE a.art_transf=$idArticle
                           AND date(a.date_transf) between '$date_debut' AND '$date_fin' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtetransfset'];
    }

    /**
     * 
     * @param type $idArticle article a recherche la quantite vendu
     * @param type $date
     * @return int
     */
    public function getVenteOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND f.mag_fact = $magasin";
        $query = "SELECT IFNULL(sum(v.qte_vnt),0) as qtevente
                from t_vente v
                INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                           WHERE v.article_vnt=$idArticle
                           AND date(v.date_vnt)='$date' AND f.sup_fact=0 $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtevente'];
    }

    /**
     * Rcuper
     * @param type $idArticle
     * @param type $date_debut
     * @param type $date_fin
     * @param type $magasin
     * @return type
     */
    public function getVenteOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND f.mag_fact = $magasin";
        $query = "SELECT IFNULL(sum(v.qte_vnt),0) as qtevente
                from t_vente v
                INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                           WHERE v.article_vnt=$idArticle
                           AND date(v.date_vnt) between '$date_debut' AND '$date_fin' AND f.sup_fact=0 $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtevente'];
    }

    /**
     * 
     * @param type $idArticle article a recherche la quantite sortie
     * @param type $date
     * @return int
     */
    public function getSortieOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND s.mag_sort_src = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_sort_art),0) as qtesortie
                from t_sortie_article a
                INNER JOIN t_sortie s ON a.sort_sort_art=s.id_sort
                           WHERE a.art_sort_art=$idArticle
                           AND date(s.date_sort)='$date' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtesortie'];
    }

    /**
     * 
     * @param type $idArticle
     * @param type $date_debut
     * @param type $date_fin
     * @param type $magasin
     * @return type
     */
    public function getSortieOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND s.mag_sort_src = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_sort_art),0) as qtesortie
                from t_sortie_article a
                INNER JOIN t_sortie s ON a.sort_sort_art=s.id_sort
                           WHERE a.art_sort_art=$idArticle
                           AND date(s.date_sort) between '$date_debut' AND '$date_fin' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtesortie'];
    }

    /**
     * 
     * @param type $idArticle article a recherche la quantite destocker
     * @param type $date
     * @return int
     */
    public function getDeffOfArticleFrom($idArticle, $date, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_def = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_def),0) as qtedef
                from t_deffectueux a
                           WHERE a.art_def=$idArticle
                           AND date(a.date_def)='$date' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtedef'];
    }

    /**
     * Rcuper
     * @param type $idArticle
     * @param type $date_debut
     * @param type $date_fin
     * @param type $magasin
     * @return type
     */
    public function getDeffOfArticleFromTo($idArticle, $date_debut, $date_fin, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND a.mag_def = $magasin";
        $query = "SELECT IFNULL(sum(a.qte_def),0) as qtedef
                from t_deffectueux a
                           WHERE a.art_def=$idArticle
                           AND date(a.date_def) between '$date_debut' AND '$date_fin' $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtedef'];
    }

    public function getQuantiteOfAritlceOfMagasin($idArticle, $magasin = 0) {

        $condmag = "";
        if ($magasin != 0)
            $condmag .=" AND s.mag_stk = $magasin";
        $query = "SELECT IFNULL(SUM(s.qte_stk),0) as qtestk
                from t_stock s
                           WHERE s.art_stk=$idArticle  $condmag ";
        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);
        $res = $r->fetch_assoc();
        return $res['qtestk'];
    }

    public function getDetailsOfFacture($idFact) {

        $condMag = "";

        if ($_SESSION['userMag'] > 0)
            $condMag = "AND f.mag_fact=" . $_SESSION['userMag'] . "";

        $query = "SELECT a.nom_art,f.code_fact,
                         m.nom_mag,m.code_mag,m.id_mag,
                         v.id_vnt,v.qte_vnt,v.pu_theo_vnt,v.mnt_theo_vnt,v.date_vnt,v.marge_vnt,
                         COALESCE(c.nom_clt,'-') as clt,
                         time(f.date_fact) as heure_fact,
                         f.login_caissier_fact,
                         f.sup_fact,
                         f.date_sup_fact,
                         f.code_caissier_fact,
                         f.bl_crdt_regle,f.crdt_fact,f.som_verse_crdt,f.remise_vnt_fact,f.bl_bic,f.bl_tva,
                         f.bl_fact_crdt,f.bl_fact_grt,f.bl_encaiss_grt
                         FROM t_vente v 
                         INNER JOIN t_article a ON v.article_vnt=a.id_art
                         INNER JOIN t_facture_vente f ON v.facture_vnt=f.id_fact
                         INNER JOIN t_magasin m on f.mag_fact=m.id_mag
                         LEFT JOIN t_client c ON f.clnt_fact=c.id_clt 
                         WHERE  f.id_fact=$idFact $condMag ORDER BY v.id_vnt DESC";

        $r = $this->mysqli->query($query) or die($this->mysqli->error . __LINE__);

        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $row['TYPE'] = "VNT";
                $result[] = $row;
            }

            return $result;
        }
    }

}

?>