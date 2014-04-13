		<article class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h2><?php echo $lang->nachrichten[$lng.'-title']; ?></h2></div>
<?php
if($user->login) {
?>
			<div id="sidebar">
			    <ul id="chat_list">
			        <hr>
<?php
	if($new_message) {
?>
                    <a href="/nachrichten?msg=<?php echo $recipient['name']; ?>" style="text-decoration: none;"><li class="selected">
                         <strong><?php echo $recipient['name']; ?></strong><br>
                         <p style="color: #999; margin: 0;">&nbsp;</p>                         
                    </li></a>
                    <hr>
<?php
	}
	foreach(array_reverse($messages) as $key => $chat) {
		$latestMSG = end($chat);
		reset($chat);
		if(!isset($recipient)) $recipient = array('name' => $chat['recipient']['name'], 'id' => $chat['recipient']['id']);
?>
                    <a href="/nachrichten?msg=<?php echo $chat['recipient']['name']; ?>" style="text-decoration: none;"><li<?php if($chat['recipient']['id'] == $recipient['id']) echo ' class="selected"'; ?>>
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
                <div id="message_box" style="height: 350px; overflow-y: scroll;">
<?php
	$recipient_known = false;
	foreach($messages as $recipientID => $chat) {
		if($recipientID == $recipient['id']) {
			foreach($chat as $key => $msg) {
				if($key !== 'recipient') {
					$recipient_known = true;
					if($msg['sender']['id'] == $user->userid) $seen = $msg['seen'];
						else $seen = 0;
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
		$user->messages_read();
?>
                    hallo<p style="color: #999; margin-bottom: 20px;" id="seen_marker" data-msg_id="<?php echo $highest_msg_id; ?>"><?php if($seen != 0) echo '*Gesehen '.date('H:i', $seen); ?></p>
<?php
	}
?>
                </div>
<?php
	if($new_message || $recipient_known) {
?>
					<textarea id="chat_text" placeholder="Antwort verfassen..."></textarea>
<?php
	}elseif(!isset($recipient['name']) && $messages == NULL) echo '<p>Du musst einen Benutzer auswählen, dem du schreiben möchtest.</p>';
	elseif($user->userid == $recipient['id']) echo '<p>Du kannst dir nicht selbst schreiben.</p>';
	else echo '<p>Diesen Benutzer gibt es nicht.</p>';
?>
            </div>
<?php
}else echo '<p>Du kannst Nachrichten nur verschicken oder empfangen, wenn du eingeloggt bist. Logge dich ein, oder erstelle dir einen <a href="/login">Account</a>.</p>';
?>
		</article>