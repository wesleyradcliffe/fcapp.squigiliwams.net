<?php
set_time_limit(0);
require_once '../bootstrap.php';

echo "<p>Redownloading Divine Shadow</p>";

# New API
$API = new LodestoneAPI();

# Parse Free Company
$FreeCompany = $API->getFC(
[
	"name" 		=> "Divine Shadow", 
	"server" 	=> "Behemoth"
],
[
	"members"	=> true,
]);

$settings['fc_name'] = strip_tags($FreeCompany->getName());
$settings['fc_link_to_lodestone'] = strip_tags($FreeCompany->getLodestone());
$settings['fc_gc_alignment'] = strip_tags($FreeCompany->getCompany());
$settings['fc_server'] = strip_tags($FreeCompany->getServer());
$settings['fc_short_tag'] = strip_tags($FreeCompany->getTag());
$settings['fc_slogan'] = strip_tags($FreeCompany->getSlogan());
$roster = $FreeCompany->getMembers();
//echo '<pre>'; print_r($roster); echo '</pre>';die();
//clear old settings?
echo '<p>Clearing OLD Settings</p>';
$old_settings = Settings::findAll();
foreach($old_settings->rows as $old_setting){
	$setting = Settings::findByID($old_setting->name);
	$setting->delete();
	//$old_setting->delete();
	//echo '<pre>'; print_r($old_setting); echo '</pre>';
}

foreach($settings as $key=>$setting){
	$new_settings = new Settings();
	$new_settings->name = $key;
	$new_settings->value = $setting;
	$new_settings->save();
	echo '<p>Applied Setting: ' . $key . ' = ' . $setting . '</p>';
}


//echo '<pre>'; print_r($roster); echo '</pre>';

echo "<p>-----ROSTER DOWNLOAD START! only inserting stubs into the DB. will need full pulls later!-----</p>";

if(is_array($roster)){
	foreach($roster as $member){


		echo "<p>Importing " . $member['name'] . "</p>";
		//sorry squenix
		
		$character = new Characters();

		//check to see if this character already exists in the DB
		$name = $member['name'];
		$name_search = array('name'=>$name);
		$exists = Characters::findBy($name_search);
		if(is_object($exists) && $exists->name != ''){
			echo "<p>Character Found! - Skipping</p>";
			print_r($exists);
			continue; // skip for now?
			$update = true;
			$character = $exists;
		}else{
			echo "<p>Inserting New Member</p>";
			$update = false;
			
		}
		unset($exists);

		$character->name = $member['name'];
		$character->lodestone_id = $member['id'];
		$character->last_update = date('Y-m-d H:i:s');
		$character->stub_only = 1;

		//gear ids -- need to sort this out later
		if($update){
			$character->update();
		}else{
			$character->save();
		}
		
		unset($character);


	}
}
