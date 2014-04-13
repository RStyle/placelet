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

//$return = array('notsent' => 'notsent');
if($user->login) {
	if(isset($_POST['send_msg']) && isset($_POST['recipient']) && isset($_POST['message'])) {
		$user->send_message($_POST['recipient'], $_POST['message']);
	}elseif(isset($_POST['recieve_msgs'])) {
		$messages = $user->recieve_messages();
		if(isset($_POST['recipient'])) $recipient = array('id' => $_POST['recipient'], 'name' => Statistics::id2username($_POST['recipient']));
		if($_POST['msg_id'] != 'null') $msg_id = $_POST['msg_id'];
			else $msg_id = 0;
		foreach($messages as $recipientID => $chat) {
			if($recipientID == $recipient['id']) {
				foreach($chat as $key => $msg) {
					if($key !== 'recipient' && $msg['id'] > $msg_id) {
						if($recipientID != $user->userid) $seen = $msg['seen'];
	?>
						<div class="post">
							<img src="/pictures/profiles/pic?user=<?php echo $user->userid; ?>" width="40" style="border: 1px #999 solid; float: left; margin-right: 10px;">
							<div style="float: left;"><p style="color: #999; margin: 0;">
								<strong style="color: #b7d300"><?php if($msg['sender']['id'] == $user->userid) echo $lang->nachrichten->ich->$lng; else echo $msg['sender']['name']; ?></strong>, <?php echo days_since($msg['sent']).' '.date('H:i d.m.Y', $msg['sent'])?></p>
							<p style="margin: 2px;"><?php echo $msg['message']; ?></p></div>
						</div>
	<?php
					}
				}
			}
		}
	}else echo 'du hast nichts ausgewählt';
}else echo 'WTF is wrong with you?';
//echo json_encode($return);
profile_pic(5);
?>