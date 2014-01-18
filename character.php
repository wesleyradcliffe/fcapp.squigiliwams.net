<?php

require_once 'bootstrap.php';

//character page - pull an individual by id 


# New API
$API = new LodestoneAPI();

// $Character = $API->get(
// [
// 	"name"		=> "Aurelia Squigiliwams",
// 	"server"	=> "Behemoth"
// ]);

$API->parseProfile(2253189);
$Character = $API->getCharacterByID(2253189);



echo '<pre>'; print_r($Character->getClassJob('arcanist')); echo '</pre>';
// include ('includes/header.inc.php');
// include ('includes/home.inc.php');
// include ('includes/footer.inc.php');
