<?php
if(isset($_POST['squery'])) {
	if(strlen($squery) <= 18) {
		$squery_href = urlencode($squery);
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
				<ul>
<?php
		if($squery_result['user'] == 1 && $squery_result['bracelet_name'] != 2 && $squery_result['bracelet_id'] == 0 && $squery_result['users'] === false && $squery_result['bracelets_name'] === false)
			echo '<li>'.$lang->search->noresults->$lng.'</li>';
		switch($squery_result['user']) {
			case 0:
?>
					<li><?php echo $lang->search->benutzergefunden->$lng; ?> <a href="/profil?user=<?php echo $squery_href;?>"><strong><?php echo $squery;?></strong></a></li>
<?php
				break;
		}
		if($squery_result['users'] !== false){
			echo '<li>'.$lang->search->ähnlichebenutzer->$lng.'<br><ul>';
			foreach($squery_result['users'] as $user_temp){ //User_temporär, um mögliche Namenskonflikte zu umgehen
				echo '<li><a href="/profil?user='.urlencode($user_temp['user']).'"><strong>'.$user_temp['user'].'</strong></a></li>';
			}
			echo '</ul></li>';
		}
		
		
		switch($squery_result['bracelet_name']) {
			case 2:

					echo str_replace(array(':squery_href', ':squery', ':owner', ':owner_href'), array($squery_href, $squery, $braceOwner['owner'], urlencode($user_temp['user'])), $lang->search->armbandgefunden->$lng);

				break;
		}
		if($squery_result['bracelets_name'] !== false){
			echo '<li>'.$lang->search->ähnlichenamen->$lng.'<br><ul>';
			foreach($squery_result['bracelets_name'] as $user_temp){
				echo str_replace(array(':squery_href', ':squery', ':owner', ':owner_href'), array(urlencode($user_temp['name']), $user_temp['name'], @$user_temp['user'], urlencode(@$user_temp['user'])), $lang->search->armbandgefunden->$lng);
			}
			echo '</ul></li>';
		}
		
		
		switch($squery_result['bracelet_id']) {
			case 1:
				echo str_replace(array(':squery_href', ':squery'), array($squery_href, $squery), $lang->search->nochnichtregistriert->$lng);
			
				break;
			case 2:
?>
					<li>
						<?php echo str_replace(array(':squery_href', ':squery', ':brid2name_href', ':owner_href', ':owner'), array($squery_href, $squery, urlencode($statistics->brid2name($squery)), urlencode($braceOwner['owner']), $braceOwner['owner']), $lang->search->schonregistriert->$lng); ?>
					</li>
<?php
				break;
		}
	}else {
?>
			<article id="kontakt" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
				<ul>
					<li><?php echo $lang->search->ü18->$lng; ?></li>
<?php
	}
?>
				</ul>
			</article>
<?php
}
?>