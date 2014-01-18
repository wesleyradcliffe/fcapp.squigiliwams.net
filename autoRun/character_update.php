<?php
set_time_limit(0);
//echo getcwd();die();
set_include_path("/home/squigiliwams/fcapp.squigiliwams.net/");
require_once 'bootstrap.php';
echo '<pre>'; echo 'START'; echo '</pre>';
//find the next person who is either A a stub or B older than XXX hours old?
$parse_candidate_query = array('stub_only'=>1);
$parse_candidate = Characters::findBy($parse_candidate_query,'id',1);
$parse_candidate = $parse_candidate->rows;
if(count($parse_candidate)==0){
	//no one here as stub so grab by time last updated > 24 hours
	exit;
}
$parse_candidate = $parse_candidate[0];
// echo '<pre>'; print_r($parse_candidate); echo '</pre>';
// die();

echo "<p>-----Updating " . $parse_candidate->name . " -----</p>";

$character = new Characters();

//check to see if this character already exists in the DB
$name = $parse_candidate->name;
$name_search = array('name'=>$name);
$exists = Characters::findBy($name_search);
if(is_object($exists) && $exists->name != ''){
	echo "<p>Character Found! - Updating</p>";
	$update = true;
	$character = $exists;
}else{
	echo "<p>Inserting New Member</p>";
	$update = false;
	
}
unset($exists);

//die();
$API = new LodestoneAPI();
$API->parseProfile($parse_candidate->lodestone_id);
$Member_parse = $API->getCharacterByID($parse_candidate->lodestone_id);



//basic info
$character->name = $Member_parse->getName();
$character->lodestone_id = $Member_parse->getID();
$character->lodestone_url = $Member_parse->getLodestone();
$character->portrait_url = $Member_parse->getAvatar(96);
$character->bodyshot_url = $Member_parse->getPortrait();
$character->race = $Member_parse->getRace();
$character->clan = $Member_parse->getClan();
$character->nameday = $Member_parse->getNameday();
$character->guardian = $Member_parse->getGuardian();
$character->gc_name = $Member_parse->getCompanyName();
$character->gc_rank = $Member_parse->getCompanyRank();
$character->city = $Member_parse->getCity();
$character->stats_hp = $Member_parse->getStat('core','hp');
$character->stats_mp = $Member_parse->getStat('core','mp');
$character->legacy = $Member_parse->getLegacy();

//class levels
$class_level_gladiator = $Member_parse->getClassJob('gladiator');
$character->class_level_gladiator = $class_level_gladiator['level'];
$class_level_pugilist = $Member_parse->getClassJob('pugilist');
$character->class_level_pugilist = $class_level_pugilist['level'];
$class_level_marauder = $Member_parse->getClassJob('marauder');
$character->class_level_marauder = $class_level_marauder['level'];
$class_level_lancer = $Member_parse->getClassJob('lancer');
$character->class_level_lancer = $class_level_lancer['level'];
$class_level_archer = $Member_parse->getClassJob('archer');
$character->class_level_archer = $class_level_archer['level'];
$class_level_conjurer = $Member_parse->getClassJob('conjurer');
$character->class_level_conjurer = $class_level_conjurer['level'];
$class_level_thaumaturge = $Member_parse->getClassJob('thaumaturge');
$character->class_level_thaumaturge = $class_level_thaumaturge['level'];
$class_level_arcanist = $Member_parse->getClassJob('arcanist');
$character->class_level_arcanist = $class_level_arcanist['level'];
$class_level_carpenter = $Member_parse->getClassJob('carpenter');
$character->class_level_carpenter = $class_level_carpenter['level'];
$class_level_blacksmith = $Member_parse->getClassJob('blacksmith');
$character->class_level_blacksmith = $class_level_blacksmith['level'];
$cclass_level_armorer = $Member_parse->getClassJob('armorer');
$character->class_level_armorer = $cclass_level_armorer['level'];
$class_level_goldsmith = $Member_parse->getClassJob('goldsmith');
$character->class_level_goldsmith = $class_level_goldsmith['level'];
$class_level_leatherworker = $Member_parse->getClassJob('leatherworker');
$character->class_level_leatherworker = $class_level_leatherworker['level'];
$class_level_weaver = $Member_parse->getClassJob('weaver');
$character->class_level_weaver = $class_level_weaver['level'];
$class_level_alchemist = $Member_parse->getClassJob('alchemist');
$character->class_level_alchemist = $class_level_alchemist['level'];
$class_level_culinarian = $Member_parse->getClassJob('culinarian');
$character->class_level_culinarian = $class_level_culinarian['level'];
$class_level_miner = $Member_parse->getClassJob('miner');
$character->class_level_miner = $class_level_miner['level'];
$class_level_botanist = $Member_parse->getClassJob('botanist');
$character->class_level_botanist = $class_level_botanist['level'];
$class_level_fisher = $Member_parse->getClassJob('fisher');
$character->class_level_fisher = $class_level_fisher['level'];

//no longer a stub!
$character->stub_only = 0;

//gear ids -- need to sort this out later


if($update){
	$character->update();
}else{
	$character->save();
}

unset($character);

