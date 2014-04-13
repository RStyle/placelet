<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);
session_start(); //Session starten
include_once('../connection.php');
include_once('../functions.php');
require_once('../user.php');
$lang = simplexml_load_file('../../text/translations.xml');
$lng = 'de';
if(isset($_POST['eng'])) $lng = $_POST['eng'];
if(isset($_SESSION['user'])){
	$user = new User($_SESSION['user'], $db);
	$checklogin = $user->logged;
}else{
	$user = new User(false, $db);
	$checklogin = false;
}
$statistics = new Statistics($db, $user);

if($user->login) {
	if(isset($_POST['send_msg']) && isset($_POST['recipient']) && isset($_POST['message']) && isset($_POST['senderid'])) {
		$messages = $user->messages_read($_POST['senderid']);
		$user->send_message($_POST['recipient'], $_POST['message']);
	}elseif(isset($_POST['receive_msgs'])) {
		$messages = $user->receive_messages(false, false);
		if(isset($_POST['recipient'])) $recipient = array('id' => $_POST['recipient'], 'name' => Statistics::id2username($_POST['recipient']));
		if($_POST['msg_id'] != 'null') $msg_id = $_POST['msg_id'];
			else $msg_id = 0;
		foreach($messages as $recipientID => $chat) {
			if($recipientID == $recipient['id']) {
				foreach($chat as $key => $msg) {
					if($key !== 'recipient' && $msg['id'] >= $msg_id) {
						if($msg['sender']['id'] == $user->userid) $seen = $msg['seen'];
							else $seen = 0;
						if($msg['id'] > $msg_id) {
							$highest_msg_id = $msg['id'];
?>
						<div class="post">
							<img src="/pictures/profiles/pic?user=<?php echo $msg['sender']['id']; ?>" width="40" style="border: 1px #999 solid; float: left; margin-right: 10px;">
							<div class="post_rightside"><p style="color: #999; margin: 0;">
								<strong style="color: #b7d300"><?php if($msg['sender']['id'] == $user->userid) echo $lang->nachrichten->ich->$lng; else echo $msg['sender']['name']; ?></strong>, <?php echo days_since($msg['sent']).' '.date('H:i d.m.Y', $msg['sent'])?></p>
							<p style="margin: 2px;"><?php echo $msg['message']; ?></p></div>
						</div>
	<?php
						}
					}
				}
			}
		}
		if(!isset($highest_msg_id)) $highest_msg_id = $_POST['msg_id'];
		if(!isset($seen)) $seen = 0;
		if(!isset($seen) && !isset($highest_msg_id)) $messages->read();
?>
                    <p style="color: #999; margin-bottom: 20px;" id="seen_marker" data-msg_id="<?php echo $highest_msg_id; ?>"><?php if($seen != 0) echo '*'.$lang->nachrichten->seen->$lng.' '.date('H:i', $seen); ?></p>
<?php
	}elseif(isset($_POST['messages_read']) && isset($_POST['senderid'])) {
		$messages = $user->messages_read($_POST['senderid']);
	}
}
?>