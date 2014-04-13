		<article class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h2><?php echo $lang->nachrichten[$lng.'-title']; ?></h2></div>
<?php
if($user->login) {
?>
			<div id="sidebar">
			    <ul id="chat_list">
			        <hr>
<?php
	foreach($messages as $key => $chat) {
		$latestMSG = end($chat);
		reset($chat);
		if(!isset($recipient)) $recipient = array('name' => $chat['recipient']['name'], 'id' => $chat['recipient']['id']);
?>
                    <a href="/nachrichten?msg=<?php echo $chat['recipient']['name']; ?>" style="text-decoration: none;"><li class="selected">
                         <strong><?php echo $chat['recipient']['name']; ?></strong><br>
                         <p style="color: #999; margin: 0;"><?php echo days_since($latestMSG['sent']).' '.date('H:i d.m.Y', $latestMSG['sent']); ?></p>                         
                    </li></a>
                    <hr>
<?php
	}
?>
                </ul> 
            </div>
            <div id="chat_room" data-recipient="<?php echo $recipient['id']; ?>">
                <div id="message_box">
<?php
	$recipient_known = false;
	foreach($messages as $recipientID => $chat) {
		if($recipientID == $recipient['id']) {
			foreach($chat as $key => $msg) {
				if($key !== 'recipient') {
					$recipient_known = true;
					if($recipientID != $user->userid) $seen = $msg['seen'];
					$highest_msg_id = $msg['id'];
?>
                    <div class="post">
                        <img src="<?php echo profile_pic($msg['sender']['id']); ?>" width="40" style="border: 1px #999 solid; float: left; margin-right: 10px;">
                        <div style="float: left;"><p style="color: #999; margin: 0;">
							<strong style="color: #b7d300"><?php if($msg['sender']['id'] == $user->userid) echo $lang->nachrichten->ich->$lng; else echo $msg['sender']['name']; ?></strong>, <?php echo days_since($msg['sent']).' '.date('H:i d.m.Y', $msg['sent'])?></p>
                        <p style="margin: 2px;"><?php echo $msg['message']; ?></p></div>
                    </div>
<?php
				}
			}
		}
	}
	if($recipient_known) {
?>
                    <p style="color: #999; margin-bottom: 20px;" id="seen_marker" data-msg_id="<?php echo $highest_msg_id; ?>"><?php if($seen != 0) echo '*Gesehen '.date('H:i', $seen); ?></p>
<?php
	}elseif(Statistics::userexists(@$recipient['name'])) {
		echo $recipient['name'];
		$recipient_known = true;
	}
	if($recipient_known) {
?>
					<textarea id="chat_text" placeholder="Antwort verfassen..."></textarea>
<?php
	}elseif(!isset($recipient['name']) && $messages == NULL) {
?>
					<p>Du musst einen Benutzer auswählen, dem du schreiben möchtest.</p>
<?php
	}else {
?>
					<p>Diesen Benutzer gibt es nicht.</p>
<?php
	}
?>
                </div>
            </div>
<?php
}else {
?>
			<p>Du kannst Nachrichten nur verschicken oder empfangen, wenn du eingeloggt bist. Logge dich ein, oder erstelle dir einen <a href="/login">Account</a>.</p>
<?php
}
?>
		</article>