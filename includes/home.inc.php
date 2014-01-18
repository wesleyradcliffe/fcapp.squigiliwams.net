<?php

$fc_name = $FreeCompany->getName();
$fc_link_to_lodestone = $FreeCompany->getLodestone();
$fc_gc_alignment = $FreeCompany->getCompany();
$fc_server = $FreeCompany->getServer();
$fc_short_tag = $FreeCompany->getTag();
$fc_slogan = $FreeCompany->getSlogan();
$quick_roster = $FreeCompany->getMembers();
//print_r($quick_roster);

?>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active"><a href="#">Home</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
        <h3 class="text-muted">FFXIV FCAPP</h3>
      </div>

      <div class="jumbotron">
        <h1><?=$fc_name?> <?="'".$fc_short_tag."'"?></h1>
        <p class="lead"><?=$fc_slogan?></p>
        
      </div>

      <div class="row marketing">
        <div class="col-lg-12">
          <table id="quick_roster" class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Last Class / Level</th>
                <th>FC Rank</th>
              </tr>
            </thead>
            <tbody>
              <?
              if(is_array($quick_roster)){
                foreach($quick_roster as $id=>$member){
                  //print_r($member);
                  ?>
                  <tr>
                    <td><?=$id+1?></td>
                    <td><?=$member['name']?></td>
                    <td><?=$member['class']['level']?><img src="<?=$member['class']['image']?>"/></td>
                    <td><img src="<?=$member['rank']['image']?>"/><?=$member['rank']['title']?></td>
                  </tr>
                  <?
                }
              }
              ?>
            </tbody>
          </table>
          
        </div>

        
      </div>

      <div class="footer">
        <p><a href="https://www.youtube.com/watch?v=BCUvx2BExZI">hey omegan!</a></p>
      </div>

    </div> <!-- /container -->
