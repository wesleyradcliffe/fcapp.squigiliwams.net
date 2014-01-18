<?php

require_once 'bootstrap.php';

//this all needs to eventually be database driven, but before i know what to store i'll mock it pulling live from the db

//index - show basic FC info? 

//character page - pull an individual by id 

//group pages? *custom* set and store groups? (i.e. coil groups)

# New API
$API = new LodestoneAPI();

# Parse Free Company
$FreeCompany = $API->getFC(
[
  "name"    => "Divine Shadow", 
  "server"  => "Behemoth"
],
[
  "members" => true,
]);

include ('includes/header.inc.php');
include ('includes/home.inc.php');
include ('includes/footer.inc.php');
