<!DOCTYPE html>
<html ng-app="sngs" class="metro ng-cloak" ng-controller="appCtrl">

<head>
    <?php
        define('APPLICATION_REV', '3.0');
        ?>
    <?php
        $filt = filemtime('app/fltrs/filters-m.js');
        $mdl = filemtime('app/mdls/main-m.js');
        $conf = filemtime('app/srvs/config-m.js');
        $dao = filemtime('app/srvs/dao-m.js');
        $util = filemtime('app/srvs/utils-m.js');
        $prmutil = filemtime('app/srvs/prmutils-m.js');
        $services = filemtime('app/srvs/services.js');
        $anview = filemtime('app/drctvs/anView-m.js');
        $wait = filemtime('app/drctvs/waiting-m.js');
        $app = filemtime('app/ctrls/appController-m.js');
        $log = filemtime('app/ctrls/loginController-m.js');
        $hom = filemtime('app/ctrls/homeController-m.js');
        $par = filemtime('app/ctrls/paramController-m.js');
        $stk = filemtime('app/ctrls/stockController-m.js');
        $vnt = filemtime('app/ctrls/venteController-m.js');
        $reg = filemtime('app/ctrls/reglementController-m.js');
        $eta = filemtime('app/ctrls/etatController-m.js');
        $dec = filemtime('app/ctrls/decaissController-m.js');
        $ann = filemtime('app/ctrls/annulController-m.js');
        $fact = filemtime('app/ctrls/factController-m.js');
        ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="public/images/favicon.ico" />
    <link rel="icon" type="image/ico" href="public/images/favicon.ico" />
    <title ng-bind="pageTitle || 'SONGO-STOCK' ">SONGO-STOCK</title>
    <!-- <link href="public/less/grid.less" rel="stylesheet"> -->
    <link href="public/bootstrap-3.4.1-dist/css/bootstrap.css" rel="stylesheet">
    <link href="public/css/mbts.min.css" rel="stylesheet">
    <script src="public/js/y/y.m.js"></script>
    <script src="node_modules/socket.io/node_modules/socket.io-client/socket.io.js"></script>
    <style>
        .shortcut1{
            top: -100px;
        }
        .dropdown-menu{
            top:auto;
            right: 0;
            left: auto;
            float: right!important;
        }
        .example{
            margin-right: -15px!important;
            margin-left: -15px!important;
        }
    </style>
</head>

<body class="background-screen">
<!-- <ng-include src="'app/vws/navbar-m.html'"></ng-include> -->
<!-- <marquee DIRECTION="left" BGCOLOR="red">
    <div style="text-align: center;"> CETTE VERSION EST OBSOLETE, VEUILLEZ VOUS FAMILIARISER AVEC LA NOUVELLE VERSION
<a href="http://sngs.ddns.net/sngs2" style="color:black">http://sngs2.ddns.net/sngs2</a>
</div>
</marquee> -->
<ng-include src="'app/vws/navbar-m.html'" ng-show="app.navbar.show && app.userPfl!=''"></ng-include>
    <div ng-show="app.navbar.show && app.userPfl!=''" style="margin-top: 20px;width:145px;position:absolute;">
        <div style="position:relative;">
            <ng-include src="'app/vws/shorcuts-m.html'"></ng-include>
        </div>
    </div>
    <waiting model="app.waiting" ng-show="app.waiting.show"></waiting>
    <div class="container" style="padding:-5px">
        <div ng-show="app.title.show">
            <nav class="breadcrumbs" style="margin-top: 20px">
                <ul>
                    <li><a> {{app.title.text}}</a></li>
                    <li class="active"><a> {{app.title.subtitle}}</a></li>
                </ul>
            </nav>

        </div>
        <ng-view class="view-slide-in"></ng-view>
    </div>
    <div class="footer">
        <div ng-show="app.navbar.show && app.userPfl!='' && app.userPfl.mg==0 && app.userPfl.pfl!=7"
            style="position:absolute;text-align:center; width:100%;" align="center">
            <a href="#/etarglc" ng-show="crnv>0" class="shortcut success shortcut1">
                <i class="icon-user-2"></i>
                Regle..ts
                <small class="bg-red fg-white">0{{crnv}}</small>
            </a>
            <a href="#/etadep" ng-show="depnv>0" class="bg-amber shortcut shortcut1">
                <i class="icon-medal-2"></i>
                Depenses
                <small class="bg-red fg-white">0{{depnv}}</small>
            </a>
            <a href="#/stketadeff" ng-show="defnv>0" class="bg-red shortcut shortcut1">
                <i class="icon-finder"></i>
                Destockage
                <small class="bg-blue fg-white">0{{defnv}}</small>
            </a>
            <a href="#/stkbs" ng-show="srtnv>0" class="shortcut teal shortcut1">
                <i class="icon-box-remove"></i>
                Sorties Stock
                <small class="bg-darkOrange fg-white">0{{srtnv}}</small>
            </a>
        </div>
        <div ng-show="app.navbar.show && app.PRMS.bonatt && app.userPfl!='' && app.userPfl.mg!=0 && app.userPfl.pfl!=7"
            style="position:absolute;text-align:center; width:100%; height:0!important;" align="center">
            <a href="#/stkbaae" ng-show="srtnba>0" class="shortcut teal shortcut1">
                <i class="icon-box-remove"></i>
                Bons en Attentes
                <small class="bg-darkOrange fg-white">0{{srtnba}}</small>
            </a>
            <a href="#/stkbs" ng-show="srtnbarj>0" class="shortcut teal shortcut1">
                <i class="icon-arrow-down-3"></i>
                Bons rejetés
                <small class="bg-darkOrange fg-white">0{{srtnbarj}}</small>
            </a>
        </div>
        
        {{(app.navbar.show && app.PRMS.bon_att && app.userPfl!='' && app.userPfl.mg!=0)}}
        <span style="float:left;margin-left:20px;">GBCYS, copyright &copy;2013-
            <?php echo date("Y"); ?> ! {{app.appns}} {{app.appv}} ::: Licence accord&eacute;e &agrave; <span
                class="bg-green fg-white">{{app.benef}}</span>
        </span>
        <span style="float:right;margin-right:20px;">Contacts : {{app.cont}} :::::: E-mail : {{app.mail}}</span>
        <!-- <span style="float:right;
              z-index: -9999999;
              margin-right:5px;
              position:absolute;
              bottom:45px;left:15px;">
            &nbsp;<img src="public/images/logo-dbcys.jpg" alt="logo">
        </span> -->
    </div>

    <script type="text/javascript" src="bower_components/angular/angular.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-resource/angular-resource.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-cache/angular-cache.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-local-storage/angular-local-storage.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-charts/angular-charts.min.js"></script>
    <script type="text/javascript" src="bower_components/d3/d3.min.js"></script>
    <script type="text/javascript" src="app/fltrs/filters-m.js?<?php echo $filt; ?>"></script>

    <script type="text/javascript" src="app/mdls/main-m.js?<?php echo $mdl; ?>"></script>
    <script type="text/javascript" src="app/srvs/config-m.js?<?php echo $conf; ?>"></script>
    <script type="text/javascript" src="app/srvs/dao-m.js?<?php echo $dao; ?>"></script>
    <script type="text/javascript" src="app/srvs/utils-m.js?<?php echo $util; ?>"></script>
    <script type="text/javascript" src="app/srvs/prmutils-m.js?<?php echo $prmutil; ?>"></script>
    <script type="text/javascript" src="app/srvs/services-m.js?<?php echo $services; ?>"></script>
    <script type="text/javascript" src="app/drctvs/anView-m.js?<?php echo $anview; ?>"></script>
    <script type="text/javascript" src="app/drctvs/waiting-m.js?<?php echo $wait; ?>"></script>
    <script type="text/javascript" src="app/ctrls/appController-m.js?<?php echo $app; ?>"></script>
    <script type="text/javascript" src="app/ctrls/loginController-m.js?<?php echo $log; ?>"></script>
    <script type="text/javascript" src="app/ctrls/homeController-m.js?<?php echo $hom; ?>"></script>
    <script type="text/javascript" src="app/ctrls/paramController-m.js?<?php echo $par; ?>"></script>
    <script type="text/javascript" src="app/ctrls/stockController-m.js?<?php echo $stk; ?>"></script>
    <script type="text/javascript" src="app/ctrls/venteController-m.js?<?php echo $vnt; ?>"></script>
    <script type="text/javascript" src="app/ctrls/reglementController-m.js?<?php echo $reg; ?>"></script>
    <script type="text/javascript" src="app/ctrls/etatController-m.js?<?php echo $eta; ?>"></script>
    <script type="text/javascript" src="app/ctrls/decaissController-m.js?<?php echo $dec; ?>"></script>
    <script type="text/javascript" src="app/ctrls/annulController-m.js?<?php echo $ann; ?>"></script>
    <script type="text/javascript" src="app/ctrls/factController-m.js?<?php echo $fact; ?>"></script>
</body>

</html>
<script src="public/js/app.min.js"></script>