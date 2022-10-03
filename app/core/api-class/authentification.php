<?php
function authentication(){
    
    if(isset($_SERVER['HTTP_USERDATA'])){
        // echo "BONJOUR";
        $result=json_decode($_SERVER['HTTP_USERDATA']);

        $_SESSION['userLogin'] = $result->login_user;
        $_SESSION['userId'] = $result->id_user;
        $_SESSION['userCode'] = $result->code_user;
        $_SESSION['userProfil'] = $result->profil_user;
        $_SESSION['userMag'] = $result->mag_user;
        $_SESSION['userMagAct'] = $result->act_mag;
        $_SESSION['codeMag'] = $result->code_mag;
        $_SESSION['nomMag'] = $result->nom_mag;
        $_SESSION['reglCredit'] = $result->regl_credit;
        $_SESSION['venteCredit'] = $result->vente_credit;
        $_SESSION['factureVenteAnnulee'] = $result->facture_vente_annulee;

    }
}
?>