<?php

require_once ("../core/api-class/db.php");
require_once ("../core/api-class/helpers.php");
$db = new DB();
$db->dbConnect($db->getOptions());
$Mysqli = $db->getMysqlObject();
?>