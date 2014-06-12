<?php
class User
{
	protected $db;
	public $login;
	public $logged = false; //eingeloggt?
	public $admin = false; //admin?
	public $userid;
	public $android;
	
	public function __construct($login, $db, $dynPW = '', $android = false) {
		$this->db = $db;
		$this->userid = Statistics::username2id($login);
		$this->android = $android;
		$this->login = $login;
		if($android) {
			if(isset($dynPW) && isset($login)){
				$stmt = $this->db->prepare('SELECT * FROM dynamic_password WHERE userid = :userid');
				$stmt->execute(array('userid' => $this->userid));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if(PassHash::check_password(substr($dynPW, 0, 60), substr($row['password'], 0, 15)) 
					&& PassHash::check_password(substr($dynPW, 60, 60), substr($row['password'], 15, 15)) 
					&& PassHash::check_password(substr($dynPW, 120, 60), substr($row['password'], 30, 15)) 
					&& PassHash::check_password(substr($dynPW, 180, 60), substr($row['password'], 45, 15))
					//Überprüfung des 4-fachen Hashs des Hashes - müsste unschlagbare Sicherheit bieten ;)
				){
					$this->logged = true;
					//Status abfragen
					$stmt = $this->db->prepare('SELECT status FROM users WHERE user = :user');
					$stmt->execute(array('user' => $login));
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if($row['status'] == 2) {
						$this->admin = true;
					}
				}else {
					$this->login = "not_logged";	//Hiermit werden falsch eingeloggte Benutzer nicht mehr mit $this->login Sicherheitslücken umgehen können
				}
			}			
		}else {
			if($login !== false && isset($_SESSION['dynamic_password']) && isset($_SESSION['userid'])){ //prüfen ob eingeloggt
				try {
					$stmt = $this->db->prepare('SELECT * FROM dynamic_password WHERE userid = :userid');
					$stmt->execute(array('userid' =>$_SESSION['userid']));
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if(PassHash::check_password(substr($_SESSION['dynamic_password'], 0, 60), substr($row['password'], 0, 15)) 
						&& PassHash::check_password(substr($_SESSION['dynamic_password'], 60, 60), substr($row['password'], 15, 15)) 
						&& PassHash::check_password(substr($_SESSION['dynamic_password'], 120, 60), substr($row['password'], 30, 15)) 
						&& PassHash::check_password(substr($_SESSION['dynamic_password'], 180, 60), substr($row['password'], 45, 15))
						//Überprüfung des 4-fachen Hashs des Hashes - müsste unschlagbare Sicherheit bieten ;)
					){
						$this->logged = true;
						//Status abfragen
						$stmt = $this->db->prepare('SELECT status FROM users WHERE user = :user');
						$stmt->execute(array('user' => $_SESSION['user']));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						if($row['status'] == 2) {
							$this->admin = true;
						}
					}else {
						//echo substr($_SESSION['dynamic_password'], 60, 60). '++++' .  substr($row['password'], 15, 15); WAARUUM????
						$this->login = false;	//Hiermit werden falsch eingeloggte Benutzer nicht mehr mit $this->login Sicherheitslücken umgehen können
						$this->logout();	//Um zukünftige fehlschlagende Versuche des automatischen Logins zu vermeiden
					}
				} catch(PDOException $e) {
					die('ERROR: '.$e->getMessage());
				}
			}
		}
	}
	
	public function login($pw) {
		$stmt = $this->db->prepare('SELECT * FROM users WHERE user = :user');
		$stmt->execute(array(':user' => $this->login));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row['status'] == 0) return 2;
		if (PassHash::check_password($row['password'], $pw)) {
			$this->login = $row['user'];
			$this->userid = $row['userid'];
			$dynamic_password = PassHash::hash($row['password']);
			$_SESSION['dynamic_password'] = PassHash::hash(substr($dynamic_password, 0, 15)).PassHash::hash(substr($dynamic_password, 15, 15)).PassHash::hash(substr($dynamic_password, 30, 15)).PassHash::hash(substr($dynamic_password, 45, 15));
			//4-facher Hash des Hashes - da der Hash ab einer bestimmten Anzahl von Buchstaben das Passwort abschneidet.
			$_SESSION['user'] = $this->login;
			$_SESSION['userid'] = $this->userid;
		
			$sql = "SELECT * FROM dynamic_password WHERE userid = :userid LIMIT 1"; 
            $q = $this->db->prepare($sql); 
            $q->execute(array(':userid' => $this->userid));
            $anz = $q->rowCount(); 
            if ($anz > 0)
				{ $sql= "UPDATE dynamic_password SET password=:password WHERE userid = :userid LIMIT 1"; } 
			else
				{ $sql = "INSERT INTO dynamic_password (userid,password) VALUES (:userid,:password)"; }
		
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':userid' => $this->userid,
				':password' => $dynamic_password)
			);
			$this->logged = true;
			//Einlogg-Zeit eintragen
			$sql= "UPDATE users SET last_login = :date WHERE user = :user LIMIT 1";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':user' => $this->login,
				':date' => time()
			));
			//Status abfragen
			if($row['status'] == 2) $this->admin = true;
			if($row['last_login'] == 0) return 3;
			return true;
		}else { 
			return false; 
		}
	}
	
	public static function logout(){
		unset($_SESSION['user']);
		unset($_SESSION['userid']);
		unset($_SESSION['dynamic_password']);
	}
	
	public static function register($reg, $db) { //$reg ist ein array
		//ist ein (getrimter) Wert leer?
		if(tisset($reg['reg_login']) && tisset($reg['reg_email']) && !empty($reg['reg_password'])  && !empty($reg['reg_password2'])){
			if($reg['reg_password'] != $reg['reg_password2']){
				return 2;//'Die Passwörter passen nicht zusammen.';
			}
			if(Statistics::userexists($reg['reg_login'])){
				return 3;//'Dieser Benutzer existiert schon.';
			}
			//Überprüfen, ob die E-Mail Adresse schon registriert wurde.
			$stmt = $db->prepare('SELECT userid, email FROM users');
			$stmt->execute(array('email' => $reg['reg_email']));
			$useremails = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$lastuser = end($useremails);
			$userid = $lastuser['userid'] + 1;
			$duplicate_email = false;
			foreach($useremails as $val) {
				if($val['email'] == $reg['reg_email']) $duplicate_email = true;
			}
			if($duplicate_email != 0) return 4;//'Auf diese E-Mail Adresse wurde schon ein anderer Benutzer registriert.';
			if(strlen($reg['reg_login']) < 4) return 5;//'Benutzername zu kurz. Min. 4';
			if(strlen($reg['reg_login']) > 15) return 6;//'Benutzername zu lang. Max. 15';
			if(strlen($reg['reg_password']) < 6) return 7;//'Passwort zu kurz. Min. 6';
			if(strlen($reg['reg_password']) > 30) return 8;//'Passwort zu lang. Max. 30';
			if(check_email_address($reg['reg_email']) === false) return 9;//'Das ist keine gültige E-Mail Adresse';
			$sql = "INSERT INTO users (user, email, password, status, date, lng) VALUES (:user, :email, :password, :status, :date, :lng)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => trim($reg['reg_login']),
				':email' => trim($reg['reg_email']),
				':password' => PassHash::hash($reg['reg_password']),
				':status' => 0,
				':date' => time(),
				':lng' => $reg['lng']
			));
			$sql = "INSERT INTO user_status (userid, code) VALUES (:userid, :code)";
			$q = $db->prepare($sql);
			$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
			$q->execute(array(
				':userid' => $userid,
				':code' => $code // Ein 60 buchstabenlanger Zufallscode
			));
			$sql = "INSERT INTO notifications (userid, pic_own, comm_own, comm_pic, pic_subs) VALUES (:userid, :pic_own, :comm_own, :comm_pic, :pic_subs)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':userid' => $userid,
				':pic_own' => 3,
				':comm_own' => 1,
				':comm_pic' => 1,
				':pic_subs' => 3
			));
			//$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='.urlencode($reg['reg_login']).'&regstatuschange='.urlencode($code);
			$content = str_replace(array('username', 'code'), array(urlencode($reg['reg_login']), urlencode($code)), file_get_contents('./text/email/basic.html'));
			$mail_header = "From: Placelet <support@placelet.de>\n";
			$mail_header .= "MIME-Version: 1.0" . "\n";
			$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
			$mail_header .= "Content-transfer-encoding: 8bit";
			mail($reg['reg_email'], 'Bestätigungsemail', $content, $mail_header);
			//echo $reg['reg_login'].'--'.$userid;
			return 1;
		}else {
			return 0;//'Bitte gib etwas ein.';
		}
	}
	public function regstatuschange($code, $username) {
		$code = urldecode($code);
		$username = urldecode($username);
		$userid = Statistics::username2id($username);
		$sql = "SELECT * FROM users WHERE user = :user LIMIT 1"; 
        $q = $this->db->prepare($sql); 
        $q->execute(array(':user' => $username));
        $anz = $q->rowCount();
		$row = $q->fetch(PDO::FETCH_ASSOC);
        if ($anz > 0 && $row['status'] == 0){
			$stmt = $this->db->prepare('SELECT * FROM user_status WHERE userid = :userid LIMIT 1');
			$stmt->execute(array('userid' => $userid));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row['code'] == $code){
				$sql= "UPDATE users SET status = :status WHERE user = :user LIMIT 1";
				$q = $this->db->prepare($sql); 
				$q->execute(array(':status' => '1', ':user' => $username));
				//Code löschen
				$sql= "UPDATE user_status SET code = :code WHERE userid = :userid LIMIT 1";
				$q = $this->db->prepare($sql); 
				$q->execute(array(':code' => NULL, ':userid' => $userid));
				return true;
			}else {
				return false;
			}
		}
	}
	//Armband registrieren
	public function registerbr($brid) {
		try {
			$stmt = $this->db->prepare('SELECT userid FROM bracelets WHERE brid = :brid LIMIT 1');
			$stmt->execute(array('brid' => $brid));
			$anz = $stmt->rowCount(); 
			$bracelet = $stmt->fetch(PDO::FETCH_ASSOC);

			if($anz == 0) {
				return 0;
			}elseif($bracelet['userid'] == 0) {
				$stmt = $this->db->prepare('SELECT COUNT(*) FROM bracelets WHERE userid = :userid');
				$stmt->execute(array('userid' => $this->userid));
				$q2 = $stmt->fetch(PDO::FETCH_ASSOC);
				$number = $q2['COUNT(*)'] + 1;
				
				$sql = "UPDATE bracelets SET userid = :userid, date = :date, name = :name WHERE brid=:brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':userid' => $this->userid,
					':brid' => $brid,
					':date' => time(),
					':name' => $this->login.'#'.$number)
				);
				return 1;
			}elseif(Statistics::id2username($bracelet['userid']) == $this->login && Statistics::id2username($bracelet['userid']) !== false) {
				return 2;
			}else {
				return array(3, Statistics::id2username($bracelet['userid']));
			}
		} catch (PDOException $e) {
				die('ERROR: ' . $e->getMessage());
				return false;
		}
	}
	//Passwort ändern
	public function change_password($old_pwd, $new_pwd) {
		if($old_pwd != '' && $new_pwd != '') {
			$stmt = $this->db->prepare('SELECT password FROM users WHERE user = :user');
			$stmt->execute(array('user' => $this->login));
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if (PassHash::check_password($result['password'], $old_pwd)) {
				$sql = "UPDATE users SET password = :password WHERE user = :user";
				$q = $this->db->prepare($sql);
				$q->execute(array(
							':password' => PassHash::hash($new_pwd),
							':user' => $this->login
							));
				return true;
			}else {
				return false;
			}
		}else return false;
	}
	//Accountdetails ändern
	public function change_details($email, $old_pwd, $new_pwd, $new_username) {
		//Benutzername ändern
		if($new_username != NULL) {
			$stmt = $this->db->prepare('SELECT user FROM users WHERE user = :user');
			$stmt->execute(array('user' => $new_username));
			if($stmt->rowCount() == 0) {
				$stmt = $this->db->prepare('SELECT user FROM users WHERE user = :user');
				$stmt->execute(array('user' => $new_username));
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if($result == NULL) {//Wenn es noch keinen Benutzer mit selbem Namen gibt
					rename('pictures/profiles/'.$this->login.'.jpg', 'pictures/profiles/'.$new_username.'.jpg');
					$sql = "UPDATE users SET user = :newuser WHERE user = :olduser";
					$q = $this->db->prepare($sql);
					$q->execute(array(':olduser' => $this->login, ':newuser' => $new_username));
					$change_username = true;
				}else $change_username = false;
			}else $change_username = false;
		}else $change_username = true;
		//Passwort ändern
		if($old_pwd != NULL && $new_pwd != NULL) $change_password = $this->change_password($old_pwd, $new_pwd);
			else $change_password = true;
		//E-Mail ändern
		if($email != NULL) {
			$email = trim($email);
			if(check_email_address($email)) {
				$sql = "UPDATE users SET email = :email WHERE user = :user";
				$q = $this->db->prepare($sql);
				$q->execute(array(
							':email' => $email,
							':user' => $this->login
							));
				$change_email = true;
			}else {
				$change_email = false;
			}
		}else $change_email = true;
		if(        $change_password === true  && $change_email === true  && $change_username === true)   return true;
			elseif($change_password === false && $change_email === true  && $change_username === true)   return 2;
			elseif($change_password === true  && $change_email === false && $change_username === true)   return 3;
			elseif($change_password === true  && $change_email === true  && $change_username === false)  return 4;
			elseif($change_password === false && $change_email === false && $change_username === true)   return 5;
			elseif($change_password === false && $change_email === true  && $change_username === false)  return 6;
			elseif($change_password === true  && $change_email === false && $change_username === false)  return 7;
			else return false;
	}
	public function reset_password($email) {
		$submissions_valid = false;
		if($email != NULL) {
		  $stmt = $this->db->prepare('SELECT user FROM users WHERE email = :email');
		  $stmt->execute(array('email' => $email));
		  $result = $stmt->fetch(PDO::FETCH_ASSOC);
		  if($result != NULL) {
			$submissions_valid = true;
			$username = $result['user'];
		  }
		}
		$userid = Statistics::username2id($username);
		if($email != NULL && $submissions_valid) {
		  $code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
		  $sql = "UPDATE user_status SET pass_code = :pass_code WHERE userid = :userid";
		  $q = $this->db->prepare($sql);
		  $q->execute(array(
				':pass_code' => $code,
				':userid' => $userid
				));
		  $sql = "UPDATE users SET password = :pwd WHERE user = :user";
		  $q = $this->db->prepare($sql);
		  $q->execute(array(
				':pwd' => PassHash::hash('1resetPassword1'),
				':user' => $username
				));
		  $mail_header = "From: Placelet <support@placelet.de>\n";
		  $mail_header .= "MIME-Version: 1.0" . "\n";
		  $mail_header .= "Content-type: text/plain; charset=utf-8" . "\n";
		  $mail_header .= "Content-transfer-encoding: 8bit";
		  $content = "Bitte klicken sie auf diesen Link, um das Passwort von Ihrem Placelet.de Account zurückzusetzen.\n http://placelet.de/account?passwordCode=".$code;
		  mail($email, 'Placelet - Passwort zurücksetzen', $content, $mail_header);
		  return true;
		}else {
			return false;
		}
	}
	public function check_recover_code($code) {
		$stmt = $this->db->prepare("SELECT userid FROM user_status WHERE pass_code = :code");
		$stmt->execute(array('code' => $code));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result != NULL) {
			return Statistics::id2username($result['userid']);
		}else {
			return false;
		}
	}
	public function new_password($username, $new_pwd) {
		$sql = "UPDATE users SET password = :password WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
					':password' => PassHash::hash($new_pwd),
					':user' => $username
					));
		$userid = Statistics::username2id($username);
		//Code wieder löschen
		$sql = "UPDATE user_status SET pass_code = :pass_code WHERE userid = :userid";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':pass_code' => NULL,
			':userid' => $userid
			));

		return true;
	}
	public function revalidate($username, $email){
		$username = trim($username);
		$email = trim($email);
		$userid = Statistics::username2id($username);
		//Überprüfen, ob der Benutzer existiert.
		if(!Statistics::userexists($username)) return 'Diesen Benutzer gibt es nicht.';
		//Überprüfen, ob die E-Mail Adresse schon registriert wurde.
		$stmt = $this->db->prepare('SELECT email FROM users WHERE email = :email AND user != :user');
		$stmt->execute(array(
			'email' => $email,
			':user' => $username
		));
		$anz = $stmt->rowCount();
		if($anz != 0) return 'Auf diese E-Mail Adresse wurde schon ein anderer Benutzer registriert.';
		//Überprüfen, ob der Benutzer schon bestätigt wurde.
		$sql = "SELECT status FROM users WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(':user' => $username));
		$result = $q->fetch(PDO::FETCH_ASSOC);
		if($result['status'] != '0') return 'Dieser Benutzer wurde schon bestätigt.';
		//Updaten
		$sql = "UPDATE user_status SET code = :code WHERE userid = :userid";
		$q = $this->db->prepare($sql);
		$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
		$q->execute(array(
			':userid' => $userid,
			':code' => $code) // Ein 60 buchstabenlanger Zufallscode
		);
		$sql = "UPDATE users SET email = :email WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':user' => $username,
			':email' => $email
		));
		//Neue Email senden.
		$content = str_replace(array('username', 'code'), array(urlencode($username), urlencode($code)), file_get_contents('./text/email/basic.html'));
		$mail_header = "From: Placelet <support@placelet.de>\n";
		$mail_header .= "MIME-Version: 1.0" . "\n";
		$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
		$mail_header .= "Content-transfer-encoding: 8bit";
		mail($email, 'Bestätigungsemail', $content, $mail_header);
		return true;
	}
	public function edit_br_name($brid, $new_name) {
		$stmt = $this->db->prepare("SELECT name FROM bracelets WHERE name = :name");
		$stmt->execute(array('name' => $new_name));
		$anz = $stmt->rowCount();
		if($anz == 0) {
			$sql = "UPDATE bracelets SET name = :new_name WHERE brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':brid' => $brid,
				':new_name' => $new_name
			));
			return 1;
		}else {
			return 2;
		}
	}
	public function update_notifications(
		$pic_own_online, $pic_own_email,
		$comm_own_online, $comm_own_email,
		$comm_pic_online, $comm_pic_email,
		$pic_subs_online, $pic_subs_email
	) {
		$pic_own = 0;
		$comm_own = 0;
		$comm_pic = 0;
		$pic_subs = 0;
		if($pic_own_online == 'on') $pic_own++;
		if($pic_own_email == 'on') $pic_own+=2;
		if($comm_own_online == 'on') $comm_own++;
		if($comm_own_email == 'on') $comm_own+=2;
		if($comm_pic_online == 'on') $comm_pic++;
		if($comm_pic_email == 'on') $comm_pic+=2;
		if($pic_subs_online == 'on') $pic_subs++;
		if($pic_subs_email == 'on') $pic_subs+=2;
		$sql = "UPDATE notifications SET pic_own = :pic_own, comm_own = :comm_own, comm_pic = :comm_pic, pic_subs = :pic_subs WHERE userid = :userid";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':userid' => $this->userid,
			':pic_own' => $pic_own,
			':comm_own' => $comm_own,
			':comm_pic' => $comm_pic,
			':pic_subs' => $pic_subs
		));
		return true;
	}
	public function receive_notifications() {
		$sql = "SELECT brid FROM bracelets WHERE userid = :userid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':userid' => $this->userid));
		$bracelets = $stmt->fetchAll();
		
		$sql = "SELECT notific_checked FROM users WHERE user = :user LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':user' => $this->login));
		$stats = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$sql = "SELECT pic_own, comm_own, comm_pic, pic_subs FROM notifications WHERE userid = :userid LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(':userid' => $this->userid));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$sql = "SELECT email FROM users WHERE user = :user LIMIT 1";
		$email_q = $this->db->prepare($sql);
		$email_q->execute(array(':user' => $this->login));
		$email = $email_q->fetch(PDO::FETCH_ASSOC);
		$email = $email['email'];
		$sql = "SELECT brid FROM subscriptions WHERE email = :email";
		$subs_q = $this->db->prepare($sql);
		$subs_q->execute(array(':email' => $email));
		$subscriptions = $subs_q->fetchAll(PDO::FETCH_ASSOC);
		
		if($q != NULL) {
			foreach($q as $key => $val) {
				if($val == 1 || $val == 3) {
					if($key == 'pic_own') {
						foreach($bracelets as $bracelet) {
							$sql = "SELECT userid, brid, description, picid, city, country, date, title, fileext, longitude, latitude, state FROM pictures WHERE brid = :brid AND date > :notific_checked AND userid != :userid";
							$stmt = $this->db->prepare($sql);
							$stmt->execute(array(
								':brid' => $bracelet['brid'],
								':notific_checked' => $stats['notific_checked'],
								':userid' => $this->userid
							));
							$pic_owns = $stmt->fetchAll(PDO::FETCH_ASSOC);
						}
					}elseif($key == 'comm_own') {
						foreach($bracelets as $bracelet) {
							$sql = "SELECT commid, picid, userid, comment, date FROM comments WHERE brid = :brid AND date > :notific_checked AND userid != :userid";
							$stmt = $this->db->prepare($sql);
							$stmt->execute(array(
								':brid' => $bracelet['brid'],
								':notific_checked' => $stats['notific_checked'],
								':userid' => $this->userid
							));
							$comm_owns = $stmt->fetchAll(PDO::FETCH_ASSOC);
						}
					}elseif($key == 'comm_pic') {
						$sql = "SELECT brid, picid FROM pictures WHERE userid = :userid";
						$stmt = $this->db->prepare($sql);
						$stmt->execute(array(':userid' => $this->userid));
						$own_pics = $stmt->fetchAll(PDO::FETCH_ASSOC);
						
						foreach($own_pics as $id => $pic_details) {
							$sql = "SELECT commid, picid, userid, comment, date FROM comments WHERE brid = :brid AND picid = :picid AND date > :notific_checked AND userid != :userid";
							$stmt = $this->db->prepare($sql);
							$stmt->execute(array(
								':brid' => $pic_details['brid'],
								':picid' => $pic_details['picid'],
								':notific_checked' => $stats['notific_checked'],
								':userid' => $this->userid
							));
							$comm_pics = $stmt->fetchAll(PDO::FETCH_ASSOC);
						}
					}elseif($key == 'pic_subs') {
						foreach($subscriptions as $brid) {
							$sql = "SELECT userid, brid, description, picid, city, country, date, title, fileext, longitude, latitude, state FROM pictures WHERE brid = :brid AND date > :notific_checked AND userid != :userid";
							$stmt = $this->db->prepare($sql);
							$stmt->execute(array(
								':brid' => $brid['brid'],
								':notific_checked' => $stats['notific_checked'],
								':userid' => $this->userid
							));
							$pic_subs = $stmt->fetchAll(PDO::FETCH_ASSOC);
						}
					}
				}
			}
		}
		@$return['pic_owns'] = $pic_owns;
		@$return['comm_owns'] = $comm_owns;
		@$return['comm_pics'] = $comm_pics;
		@$return['pic_subs'] = $pic_subs;
		return $return;
	}
	public function notifications_read() {
		$sql = "UPDATE users SET notific_checked = :date WHERE userid = :userid";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':date' => time(),
			':userid' => $this->userid
		));
	}
	public function update_profilepic($picture_file, $max_file_size) {
		if(isset($picture_file)) {
			$filename_props = explode(".", $picture_file['name']);
			$fileext = strtolower(end($filename_props));
			if($fileext != 'jpeg' && $fileext != 'jpg' && $fileext != 'gif' && $fileext != 'png') {
				unset($fileext);
				$submissions_valid = false;
				return 2;//Dieses Format wird nicht unterstützt. Wir unterstützen nur: .jpeg, .jpg, .gif und .png. Wende dich bitte an unseren Support, dass wir dein Format hinzufügen können.
			}else {
				if($picture_file['size'] < $max_file_size) {
					$file_uploaded = move_uploaded_file($picture_file['tmp_name'], 'pictures/profiles/'.$this->userid.'.'.$fileext);
				}else {
					return 6;//'Wir unterstützen nur Bilder bis 8MB Größe';
				}
				if($file_uploaded == true) {
					//Bild speichern
					$img_path = 'pictures/profiles/'.$this->userid.'.'.$fileext;
					$thumb_path = 'pictures/profiles/'.$this->userid.'.jpg';
					create_thumbnail($img_path, $thumb_path, 80, 80, $fileext);
					return 7;//Bild erfolgreich gepostet.
				} elseif ($file_uploaded == false) {
					return $picture_file['error'];//Mit dem Bild stimmt etwas nicht. Bitte melde deinen Fall dem Support.
				}
			}
		}else {
			return 3;//Kein Bild ausgewählt, versuch es noch ein Mal.
		}
	}
	//nachricht senden
	public function send_message($recipient, $content) {
		$sql = "INSERT INTO messages (sender, recipient, sent, message) VALUES (:sender, :recipient, :sent, :message)";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			":sender" => $this->userid,
			":recipient" => $recipient,
			":sent" => time(),
			":message" => htmlentities($content)
		));
		$sql = "SELECT androidToken FROM users WHERE userid = :userid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":userid" => $recipient));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result['androidToken'] != NULL)
		sendNotificationToAndroid($this->login.' hat dir eine Nachricht geschickt.', $result['androidToken']);
	}
	//Nachrichten empfangen
	public function receive_messages($only_unseen, $only_recieved) {
		$sql = "SELECT id, sender, recipient, sent, seen, message FROM messages WHERE recipient = :userid";
		if(!$only_recieved) $sql .= " OR sender = :userid";
		if($only_unseen) $sql .= ' AND seen = 0';
		$sql .= " ORDER BY id ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":userid" => $this->userid));
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$messages = array();
		for($i = 0; $i < count($result); $i++) {
			$result[$i]['recipient'] = array('name' => Statistics::id2username($result[$i]['recipient']), 'id' => $result[$i]['recipient']);
			$result[$i]['sender'] = array('name' => Statistics::id2username($result[$i]['sender']), 'id' => $result[$i]['sender']);
			
			if($result[$i]['sender']['id'] == $this->userid) {
				$messages[$result[$i]['recipient']['id']]['recipient'] = $result[$i]['recipient'];
				$messages[$result[$i]['recipient']['id']][$i] = $result[$i];
				$messages[$result[$i]['recipient']['id']][$i]['message'] = nl2br($result[$i]['message'], 0);
			}else {
				$messages[$result[$i]['sender']['id']]['recipient'] = $result[$i]['sender'];
				$messages[$result[$i]['sender']['id']][$i] = $result[$i];
				$messages[$result[$i]['sender']['id']][$i]['message'] = nl2br($result[$i]['message'], 0);
			}
		}
		return $messages;
	}
	//Nachricht als "gelesen" markieren
	public function messages_read($userid) {
		$sql = "UPDATE messages SET seen = :date WHERE recipient = :userid AND seen = 0 AND sender = :senderid";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':date' => time(),
			':userid' => $this->userid,
			':senderid' => $userid
		));
	}
}
?>
<?php
class Statistics {
	protected $db;
	public function __construct($db, $user){
		$this->db = $db;
		$this->user = $user;
		$this->usernames = $this->getUsernames();
	}
	//Userdetails abfragen
	public function userdetails($user) {
		$userid = self::username2id($user);
		$result = array();
		//Allgemeine Daten
		$stmt = $this->db->prepare("SELECT userid, user, email, status, date AS registered FROM users WHERE user = :user LIMIT 1");
		$stmt->execute(array('user' => $user));
		$result[0] = $stmt->fetch(PDO::FETCH_ASSOC);
		//Gekaufte Armbänder
		$stmt = $this->db->prepare("SELECT brid, date FROM bracelets WHERE userid = :userid ORDER BY  `bracelets`.`date` ASC ");
		$stmt->execute(array('userid' => $userid));
		$result[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//Abonnierte Armbänder
		$stmt = $this->db->prepare("SELECT brid FROM subscriptions WHERE email = :email");
		$stmt->execute(array('email' => $result[0]['email']));
		$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$result[2] = array();
		foreach($q as $key => $val) {
			$stmt = $this->db->prepare("SELECT picid, fileext FROM pictures WHERE brid = :brid ORDER BY picid DESC");
			$stmt->execute(array('brid' => $val['brid']));
			$result[2][$val['brid']] = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		//Gepostete Bilder
		$stmt = $this->db->prepare("SELECT brid, picid, fileext FROM pictures WHERE userid = :userid");
		$stmt->execute(array('userid' => $userid));
		$result[4] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result[4] as $key => $val) {
			$stmt = $this->db->prepare("SELECT picid FROM pictures WHERE brid = :brid ORDER BY picid DESC");
			$stmt->execute(array('brid' => $val['brid']));
			$q = $stmt->fetch(PDO::FETCH_ASSOC);
			$result[4][$key]['picCount'] = $q['picid'];
		}
		//Benachrichtigungen
		$stmt = $this->db->prepare("SELECT pic_own, comm_own, comm_pic, pic_subs FROM notifications WHERE userid = :userid LIMIT 1");//none: 0, online: 1, email: 2, online&email: 3    ----- Just like chmod: online = 1, email = 2
		$stmt->execute(array('userid' => $userid));
		$result[5] = $stmt->fetch(PDO::FETCH_ASSOC);
		
		//Array verschönern
		$user_details = $result;
		$user_details['users'] = $user_details[0];
		$user_details['pics']['pics'] = $user_details[4];
		
		if($result[5] != NULL) {
			foreach($result[5] as $key => $val) {
				switch($val) {
					case 1:
						$user_details['notifications']['notifications'][$key.'_online'] = true;
						$user_details['notifications']['notifications'][$key.'_email'] = false;
						break;
					case 2:
						$user_details['notifications']['notifications'][$key.'_online'] = false;
						$user_details['notifications']['notifications'][$key.'_email'] = true;
						break;
					case 3:
						$user_details['notifications']['notifications'][$key.'_online'] = true;
						$user_details['notifications']['notifications'][$key.'_email'] = true;
						break;
					default:
						$user_details['notifications']['notifications'][$key.'_online'] = false;
						$user_details['notifications']['notifications'][$key.'_email'] = false;
				}
			}
		}else $user_details['notifications']['notifications'] = 'not_set';
		
		$brids = array();
		foreach ($user_details[1] as $key => $val) {
			$brids = array_merge_recursive($val, $brids);
			$stmt = $this->db->prepare("SELECT picid FROM pictures WHERE brid = :brid ORDER BY  `pictures`.`picid` DESC LIMIT 1");
			$stmt->execute(array('brid' => $val['brid']));
			$result[3][$val['brid']] = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		$user_details['bracelets'] = $brids;
		$userdetails = array_merge($user_details['users'], $user_details['bracelets'], $user_details['pics'], $user_details['notifications']);
		$userdetails['subscriptions'] = $user_details[2];
		if(isset($result[3]))
			$userdetails['picture_count'] = $result[3];
		else
			$userdetails['picture_count'] = 0;
		$userdetails['userid'] = $userdetails['userid'];
		return $userdetails;
	}
	//Zeigt die allgemeine Statistik an
	public function systemStats($user_anz, $brid_anz, $recent_brid_pics = false) {
		//Arnzahl 'beposteter' Armbänder		
		$sql = "SELECT brid FROM pictures GROUP BY brid";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total_posted'] = count($q);
		
		//Arnzahl registrierter Armbänder
		$sql = "SELECT brid FROM bracelets WHERE userid != ''";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total_registered'] = count($q);
		
		//Armbänder insgesamt
		$sql = "SELECT brid FROM bracelets";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total'] = count($q);
		
		//Anzahl der verschiedenen Städte
		$sql = "SELECT COUNT(DISTINCT city)  FROM pictures";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['city_count'] = $q[0][0];
		
		//Anzahl der verschiedenen Städte
		$sql = "SELECT COUNT(DISTINCT country)  FROM pictures";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['country_count'] = $q[0][0];
		
		//Stadt auf die die meisten Armbänder registriert wurden(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,city FROM pictures GROUP BY city ORDER BY number DESC";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['most_popular_city']['city'] = $q[0]['city'];
		$stats['most_popular_city']['number'] = $q[0]['number'];
		
		//Benutzer, die die meisten Armbänder auf sich registriert haben(mit Anzahl)
		//Die Anzahl der Benutzer, die Ausgegeben werden, $banz festgelegt
		$sql = "SELECT COUNT(*) AS number, userid FROM bracelets WHERE userid > 0 GROUP BY userid ORDER BY number DESC";//GROUP BY user ORDER BY number DESC
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		for($i = 0; $i < $user_anz; $i++) {
			if(isset($q[$i]['userid'])) {
				$stats['user_most_bracelets']['user'][$i] = self::id2username($q[$i]['userid']);
				$stats['user_most_bracelets']['userid'][$i] = $q[$i]['userid'];
				$stats['user_most_bracelets']['number'][$i] = $q[$i]['number'];
			}
		}
		
		//Uploads der Top-Benutzer
		for($i = 0; $i < $user_anz; $i++) {
			if(isset($stats['user_most_bracelets']['user'][$i])) {
				$sql = "SELECT COUNT(*) AS number, userid FROM pictures WHERE userid = '".$stats['user_most_bracelets']['userid'][$i]."' GROUP BY userid ORDER BY number DESC";
				$stmt = $this->db->query($sql);
				$q = $stmt->fetchAll();
				if(isset($q[0]['number'])) {
					$stats['user_most_bracelets']['uploads'][$i] = $q[0]['number'];
				} else {
					$stats['user_most_bracelets']['uploads'][$i] = 0;
				}
			}
		}
		//asort($stats['user_most_bracelets']['uploads']);
		
		//Armband, das Bilder in den meisten Städten hat(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,brid FROM pictures GROUP BY brid ORDER BY number DESC";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['bracelet_most_cities']['brid'] = $q[0]['brid'];
		$stats['bracelet_most_cities']['name'] = $this->brid2name($q[0]['brid']);
		$stats['bracelet_most_cities']['number'] = $q[0]['number'];
		
		//Ermittelt die IDs der neuesten $brid_anz Bilder
		
		/*$sql = "SELECT id FROM pictures ORDER BY id DESC";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stats['recent_brids'] = $q;*/
		$sql = "SELECT brid, picid FROM pictures ORDER BY id DESC";
		$stmt = $this->db->query($sql);
		$q1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if(!$recent_brid_pics) {
			$q = $q1;
		}else {
			$q2 = array();
			$q = array();
			foreach ($q1 as $i){
				if(!isset($q2[$i['brid']])){
					$q2[$i['brid']] = true;
					$q[] = array('brid' => $i['brid'], 'picid' => $i['picid']);
				}
			}
		}
		for($i = 0; $i < $brid_anz; $i++) {
			$stats['recent_brids'][$i+1] = $q[$i]['brid'];
			$stats['recent_picids'][$i+1] = $q[$i]['picid'];
		}
		/*foreach($q as $key => $val) {
			$stats['recent_brids'][$key] = $val['brid'];
			$stats['recent_picids'][$key] = $val['picid'];
		}*/
		return $stats;
	}
	//Name von Armband ermitteln
	public function brid2name($brid) {
		$stmt = $this->db->prepare('SELECT name FROM bracelets WHERE brid = :brid');
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return $q['name'];
	}
	public function name2brid($name, $oldname = false) {
		$stmt = $this->db->prepare('SELECT brid FROM bracelets WHERE name = :name');
		$stmt->execute(array('name' => $name));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return $q['brid'];
	}
	//Statistik vom Armband abfragen
	public function bracelet_stats($brid, $pic_details = false) {
		$sql = "SELECT userid, date FROM bracelets WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			$stats = false;
		}else {
			if($pic_details) $stats = $this->picture_details($brid);
			$stats['name'] = $this->brid2name($brid);
			$stats['owner'] = self::id2username($q[0]['userid']);
			$stats['date'] = $q[0]['date'];
			$sql = "SELECT picid, city, country FROM pictures WHERE brid = :brid ORDER BY  `pictures`.`picid` DESC LIMIT 1";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array('brid' => $brid));
			$q = $stmt->fetch(PDO::FETCH_ASSOC);
			if($q != NULL) {
				$stats['pic_anz'] = $q['picid'];
				$stats['lastcity'] = $q['city'];
				$stats['lastcountry'] = $q['country'];
			}
		}
		return $stats;
	}
	//Bilderdetails
	public function picture_details($brid, $desc = false) {
		$details = array();
		$sql = "SELECT userid, description, picid, city, country, date, title, fileext, longitude, latitude, state FROM pictures WHERE brid = :brid ORDER BY picid";
		if($desc) $sql .= " DESC";
			else $sql .= " ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']]['user'] = self::id2username($val['userid']);
			$details[$val['picid']]['userid'] = $val['userid'];
			$details[$val['picid']]['description'] = nl2br($val['description'], 0);
			$details[$val['picid']]['picid'] = $val['picid'];
			$details[$val['picid']]['city'] = $val['city'];
			$details[$val['picid']]['country'] = $val['country'];
			$details[$val['picid']]['date'] = $val['date'];
			$details[$val['picid']]['title'] = $val['title'];
			$details[$val['picid']]['fileext'] = $val['fileext'];
			$details[$val['picid']]['latitude'] = $val['latitude'];
			$details[$val['picid']]['longitude'] = $val['longitude'];
			$details[$val['picid']]['state'] = $val['state'];
		}
		$sql = "SELECT commid, picid, userid, comment, date FROM comments WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']] [$val['commid']] = array();
			$details[$val['picid']] [$val['commid']] ['commid'] = $val['commid'];
			$details[$val['picid']] [$val['commid']] ['picid'] = $val['picid'];
			$details[$val['picid']] [$val['commid']] ['user'] = self::id2username($val['userid']);
			$details[$val['picid']] [$val['commid']] ['userid'] = $val['userid'];
			$details[$val['picid']] [$val['commid']] ['comment'] = nl2br($val['comment'], 0);
			$details[$val['picid']] [$val['commid']] ['date'] = $val['date'];
		}
		return $details;
		
	}
	//Kommentar schreiben
	public function write_comment($brid, $comment, $picid) {
		$comment = smileys(clean_input($comment));
		if(!$this->user->login) $userid = 0;
			else $userid = $this->user->userid;
		try {
			$sql = "SELECT commid FROM comments WHERE brid = :brid AND picid = :picid";
			$q = $this->db->prepare($sql);
			$q->execute(array(':brid' => $brid, ':picid' => $picid));
			$row = $q->fetchAll(PDO::FETCH_ASSOC);	
			$row = array_reverse($row);
			if(isset($row[0]['commid'])) {
				$commid = $row[0]['commid'] + 1;
			} else {
				$commid = 1;
			}
			
			if(strpos($comment, 'http') !== false || strpos($comment, 'www') !== false) $spam = 1;
			else $spam = 0;
			
			$sql = "INSERT INTO comments (brid, commid, picid, userid, comment, date, spam) VALUES (:brid, :commid, :picid, :userid, :comment, :date, :spam)";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':brid' => $brid,
				':commid' => $commid,
				':picid' => $picid,
				':userid' => $userid,
				':comment' => $comment,
				':date' => time(),
				':spam' => $spam
			));
			$this->notify_subscribers($brid, true);
			return true;
		}catch (PDOException $e) {
				die('ERROR: ' . $e->getMessage());

				return false;
		}
	}
	//Überprüft, ob ein bestimmter Benutzer $user in der Datenbank eingetragen ist
	public static function userexists($user) {
		$sql = "SELECT * FROM users WHERE user = :user LIMIT 1"; 
        $q = $GLOBALS['db']->prepare($sql); 
        $q->execute(array(':user' => $user));
        $anz = $q->rowCount();
        if ($anz > 0){
			return true;
		} else {
			return false;
		}
	}
	//Überprüft, ob es Nutzer mit ähnlichem Namen gibt
	public static function usersexists($user) {
		$user = trim($user);
		if(strlen($user)>3){
			$sql = "SELECT user FROM users WHERE user LIKE :userlike AND user != :user"; 
			$q = $GLOBALS['db']->prepare($sql); 
			$q->bindValue(':userlike', "%{$user}%", PDO::PARAM_STR);
			$q->bindValue(':user', $user, PDO::PARAM_STR);
			//DANKE an: http://www.mm-newmedia.de/2009/08/pdo-und-die-vergleichsfunktion-like/ ;)
			$q->execute();
			$rows = $q->fetchAll(PDO::FETCH_ASSOC);	
			$anz = $q->rowCount();
			if ($anz > 0){
				return $rows;
			}
		}
		return false;
	}
	//Prüft, ob ein Armband schon registriert wurde
	public function bracelet_status($brid) {
		$stmt = $this->db->prepare('SELECT userid FROM bracelets WHERE brid = :brid');
		$stmt->execute(array('brid' => $brid));
		$anz = $stmt->rowCount();
		$bracelet = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($anz == 0) {
			return '0';
		} elseif ($bracelet['userid'] == 0 ) {
			return 1;
		} else {
			return 2;
		}
	}
	//Prüft, ob ähnliche Armbänder schon registriert wurden
	public function bracelets_status($brid) {
		$brid = trim($brid);
		if(strlen($brid)>3){
			$stmt = $this->db->prepare('SELECT userid, name FROM bracelets WHERE name LIKE :bridlike AND name != :brid');
			$stmt->bindValue(':bridlike', "%{$brid}%", PDO::PARAM_STR);
			$stmt->bindValue(':brid', $brid, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			$anz = $stmt->rowCount();
			$bracelet = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($anz > 0) {
				return $rows;
			}
		}
		return false;
	}
	//Postet ein Bild
	public function registerpic($brid, $description, $city, $country, $state, $latitude, $longitude, $title, $date, $picture_file, $max_file_size) {
		$submissions_valid = true;
		if(strlen($country) < 2) {
			$submissions_valid = false;
			return 0;//Das Land ist zu kurz, mindestens 2 Buchstaben, bitte.
		}
		if(strlen($description) < 2) {
			$submissions_valid = false;
			return 1;//Beschreibung zu kurz, mindestens 2 Zeichen, bitte.
		}
		if (isset($picture_file)) {
			$filename_props = explode(".", $picture_file['name']);
			$fileext = strtolower(end($filename_props));
			if($fileext != 'jpeg' && $fileext != 'jpg' && $fileext != 'gif' && $fileext != 'png') {
				unset($fileext);
				$submissions_valid = false;
				return 2;//Dieses Format wird nicht unterstützt. Wir unterstützen nur: .jpeg, .jpg, .gif und .png. Wende dich bitte an unseren Support, dass wir dein Format hinzufügen können.
			}
		} else {
			return 3;//Kein Bild ausgewählt, versuch es noch ein Mal.
		}
		//Prüft, ob das Armband schon registriert wurde
		$bracelet_status = $this->bracelet_status($brid);
		if($bracelet_status == 1) return 4;
		elseif($bracelet_status == 0) return 5;
		//Lädt das Bild hoch und trägt es in die Datenbank ein
		if ($submissions_valid) {
			$description = clean_input($description);
			$city = clean_input($city);
			$country = clean_input($country);
			$title = clean_input($title);
			$sql = "SELECT picid FROM pictures WHERE brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(':brid' => $brid));
			$row = $q->fetchAll(PDO::FETCH_ASSOC);	
			$row = array_reverse($row);
			if(isset($row[0]['picid'])) {
				$picid = $row[0]['picid'] + 1;
			} else {
				$picid = 1;
			}
			if ($picture_file['size'] < $max_file_size) {
				$file_uploaded = move_uploaded_file($picture_file['tmp_name'], 'pictures/bracelets/pic-'.$brid.'-'.$picid.'.'.$fileext);
			} else {
				return 6;//'Wir unterstützen nur Bilder bis 8MB Größe';
			}
			if($file_uploaded == true) {
				///////////////////////////
				//Hier werdendie hochgeladenen Dateien modifiziert.
				//Datei-Pfad:
			    $img_path = 'pictures/bracelets/pic-'.$brid.'-'.$picid.'.'.$fileext;
				/*//Bild-Instanz erstellen
				switch($fileext) {
					case 'jpeg':
					case 'jpg':
						$img = imagecreatefromjpeg($img_path);
						break;
					case 'gif':
						$img = imagecreatefromgif($img_path);
						break;
					case 'png':
						$img = imagecreatefrompng($img_path);
						break;
				}
				//Interlacing aktivieren
				imageinterlace($img, true);
				//Geändertes Bild speichern(altes ersetzen)
				switch($fileext) {
					case 'jpeg':
					case 'jpg':
						imagejpeg($img, $img_path);
						break;
					case 'gif':
						imagegif($img, $img_path);
						break;
					case 'png':
						imagepng($img, $img_path);
						break;
				}
				imagedestroy($img);*/
				//Thumbnail von dem hochgeladenen Bild erstellen
				//Die Funktion wird in scripts/functions.php definiert
				//create_thumbnail($target, $thumb, $w, $h, $ext)
				$thumb_path = 'pictures/bracelets/thumb-'.$brid.'-'.$picid.'.jpg';
				create_thumbnail($img_path, $thumb_path, 400, 500, $fileext);
				if($date == 'default') {
					//EXIF-Header auslesen und Aufnamedatum bestimmen
					if($fileext == 'jpg' || $fileext == 'jpeg') {
						$exif_date = exif_read_data($img_path, 'EXIF', 0);
						if(@$exif_date['DateTimeOriginal'] == NULL || @preg_match('[^A-Za-z]', $exif_date['DateTimeOriginal'])) {
							$date = time();
						}else {
							$date = strtotime($exif_date['DateTimeOriginal']);
						}
					}else {
						$date = time();
					}
				}
				///////////////////////////
			
				$sql = "INSERT INTO pictures (picid, brid, userid, description, date, city, country, title, fileext, latitude, longitude, state, upload) VALUES (:picid, :brid, :userid, :description, :date, :city, :country, :title, :fileext, :latitude, :longitude, :state, :upload)";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':userid' => $this->user->userid,
					':description' => $description,
					':date' => $date,
					'city' => $city,
					'country' => $country,
					'title' => $title,
					'fileext' => $fileext,
					':latitude' => $latitude,
					':longitude' => $longitude,
					':state' => $state,
					':upload' => time()
				));
				$stmt = $this->db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid=:brid');
				$stmt->execute(array('picid' => $picid, 'brid' => $brid));
				$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
				//rename muss sein
				rename('pictures/bracelets/pic-'.$brid.'-'.$picid.'.'.$fileext, 'pictures/bracelets/pic-'.$rowid['id'].'.'.$fileext);
				rename('pictures/bracelets/thumb-'.$brid.'-'.$picid.'.jpg', 'pictures/bracelets/thumb-'.$rowid['id'].'.jpg');
				if($fileext == 'png')
					tinypng('pictures/bracelets/pic-'.$rowid['id'].'.'.$fileext);
				///E-Mail an die Personen senden, die das Armband abboniert haben
				$this->notify_subscribers($brid);
				return 7;//Bild erfolgreich gepostet.
			} elseif ($file_uploaded == false) {
				return $picture_file['error'];//Mit dem Bild stimmt etwas nicht. Bitte melde deinen Fall dem Support.
			}
		}
	}
	public function manage_subscription($input, $brid, $email) {
		if($input == 'email' || $input == 'username') {//Hinzufügen
			if($input == 'username') {
				$sql = "SELECT email FROM users WHERE user = :user";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':user' => $email
				));
				$result = $q->fetch(PDO::FETCH_ASSOC);
				$email = $result['email'];
			}
			$sql = "SELECT email FROM subscriptions WHERE brid = :brid AND email = :email";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':brid' => $brid,
				':email' => $email
			));
			$anz = $q->rowCount();
			if($anz == 0){
				$sql = "INSERT INTO subscriptions (brid, email) VALUES (:brid, :email)";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':brid' => $brid,
					':email' => $email
				));
				return true;
			}else {
				return 2;
			}
		}elseif($input == 'false') {//False bedeutet Löschen
			$sql = "SELECT email FROM subscriptions WHERE brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':brid' => $brid
			));
			$result = $q->fetchAll(PDO::FETCH_ASSOC);
			$unsubscribed = false;
			foreach($result as $key => $val) {
				if(PassHash::check_password($email, $val['email'])) {
					$unsubscribed = true;
					$sql = "DELETE FROM subscriptions WHERE brid = :brid AND email = :email";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':email' => $val['email'],
						':brid' => $brid
					));
				}
			}
			if($unsubscribed) return false;
				else return 3;
		}
	}
	private function delete_comment($input, $commid, $picid, $brid) {
			$sql = "DELETE FROM comments WHERE commid = :commid AND picid = :picid AND brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid,
				':commid' => $commid
			));
			$anz = $q->rowCount();
			if($input == 'middle') {
				$sql = "SELECT commid FROM comments WHERE brid = :brid AND picid = :picid AND commid > :commid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':commid' => $commid
				));
				$result = $q->fetchAll(PDO::FETCH_ASSOC);
				foreach($result as $key => $val) {
					$sql = "UPDATE comments SET commid = :newcommid WHERE brid = :brid AND picid = :picid AND commid = :commid";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid,
						':commid' => $val['commid'],
						':newcommid' => $val['commid'] - 1
					));
				}
			}
			if($anz == 1) {
				return true;//erfolgreich gelöscht
			}else {
				return false;
			}
	}
	public function manage_comment($admin, $input, $commid, $picid, $brid) {
		if($admin) {
			return $this->delete_comment($input, $commid, $picid, $brid);
		}else {
			if($this->user->login) {
				$sql = "SELECT userid FROM bracelets WHERE userid = :userid AND brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':userid' => $this->user->userid,
					':brid' => $brid,
				));
				$anz = $q->rowCount();
				if($anz == 1) {
					return $this->delete_comment($input, $commid, $picid, $brid);
				}else {
					$sql = "UPDATE comments SET spam = spam + 1 WHERE brid = :brid AND picid = :picid AND commid = :commid";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid,
						':commid' => $commid
					));
					return 2;//gemeldet
				}
			}else {
				$sql = "UPDATE comments SET spam = spam + 1 WHERE brid = :brid AND picid = :picid AND commid = :commid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':commid' => $commid
				));
				return 2;//gemeldet
			}
		}
	}
	private function delete_pic($input, $picid, $brid) {
			//Datei löschen
			$sql = "SELECT id, fileext FROM pictures WHERE brid = :brid AND picid = :picid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
			$result = $q->fetch(PDO::FETCH_ASSOC);
			unlink($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/pic-'.$result['id'].'.'.$result['fileext']);
			unlink($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/thumb-'.$result['id'].'.jpg');
			//Datenbankeintrag löschen
			$sql = "DELETE FROM pictures WHERE picid = :picid AND brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
			$anz = $q->rowCount();
			//Alle Kommentare zu diesem Bild löschen

			$sql = "DELETE FROM comments WHERE picid = :picid AND brid = :brid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
			//Wenn das Bild nicht das zuletzt gepostete ist
			if($input == 'middle') {
				$sql = "SELECT picid, fileext FROM pictures WHERE brid = :brid AND picid > :picid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid
				));
				$result = $q->fetchAll(PDO::FETCH_ASSOC);
				foreach($result as $key => $val) {
					$sql = "UPDATE pictures SET picid = :newpicid WHERE brid = :brid AND picid = :picid ";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid,
						':picid' => $val['picid'],
						':newpicid' => $val['picid'] - 1
					));
					$sql = "UPDATE comments SET picid = :newpicid WHERE brid = :brid AND picid = :picid ";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid,
						':picid' => $val['picid'],
						':newpicid' => $val['picid'] - 1
					));
					rename($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/pic-'.$brid.'-'.$val['picid'].'.'.$val['fileext'],
						   $_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/pic-'.$brid.'-'.($val['picid'] - 1).'.'.$val['fileext']);
					rename($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/thumb-'.$brid.'-'.$val['picid'].'.jpg',
						   $_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/thumb-'.$brid.'-'.($val['picid'] - 1).'.jpg');
				}
			}
			if($anz == 1) {
				return true;//erfolgreich gelöscht
			}else {
				return false;
			}
	}
	public function manage_pic($admin, $input, $picid, $brid) {
		if($admin) {
			return $this->delete_pic($input, $picid, $brid);
		}else {
			if($this->user->login) {
				$sql = "SELECT userid FROM bracelets WHERE userid = :userid AND brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':userid' => $this->user->userid,
					':brid' => $brid,
				));
				$anz = $q->rowCount();
				if($anz == 1) {
					return $this->delete_pic($input, $picid, $brid);
				}else {
					$sql = "UPDATE pictures SET spam = spam + 1 WHERE brid = :brid AND picid = :picid";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid
					));
					return 2;//gemeldet
				}
			}else {
				$sql = "UPDATE pictures SET spam = spam + 1 WHERE brid = :brid AND picid = :picid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid
				));
				return 2;//gemeldet
			}
		}
	}
	public function admin_stats() {
		//Spam-Kommentare
		$sql = "SELECT commid, picid, userid, comment, date, brid, spam FROM comments WHERE spam > 0";
		$q = $this->db->prepare($sql);
		$q->execute();
		$result['spam_comments'] = $q->fetchAll(PDO::FETCH_ASSOC);
		//Spam-Bilder
		$sql = "SELECT picid, brid, userid, description, date, city, country, title, fileext, spam FROM pictures WHERE spam > 0";
		$q = $this->db->prepare($sql);
		$q->execute();
		$result['spam_pics'] = $q->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	public function no_spam($brid, $picid, $commid) {
		if($commid == 0) {//Bild
			$sql = "UPDATE pictures SET spam = 0 WHERE brid = :brid AND picid = :picid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
		}else {//Kommentar
			$sql = "UPDATE comments SET spam = 0 WHERE brid = :brid AND picid = :picid AND commid = :commid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid,
				':commid' => $commid
			));			
		}
	}
	private function notify_subscribers($brid, $comm = false) {
		$own_bracelet = false;
		$bracelet_stats = $this->bracelet_stats($brid);
		$owner = $bracelet_stats['owner'];
		if($this->user->login) if($this->user->login == $owner) $own_bracelet = true;
		if(!$own_bracelet) {
			$braceName = $this->brid2name($brid);
			//Benachrichtigungen, wie im Profil festgelegt
				//Beim Inhaber
			//echo 'Ownerid:'.self::username2id($owner).'!';
			$sql = "SELECT pic_own, comm_own, comm_pic FROM notifications WHERE userid = :ownerid";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array(':ownerid' => self::username2id($owner)));
			$userProps = $stmt->fetch(PDO::FETCH_ASSOC);
			//print_r($userProps);
			$users_pic_subs_informed = array();
			$userdetails = $this->userdetails($owner);
			//echo 'retrieved from DB:'.$userProps['userid'].'----'.print_r($userProps).'!';
			//echo "--".self::id2username($userProps['userid']).'--'.var_dump($userProps['userid']).'--';
			$user_email = $userdetails['email'];
			foreach($userProps as $key => $val) {
				if($key == 'pic_own') {
					if($val == 2 || $val == 3) {
						if(!$comm) {
							$content = "Zu deinem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neues Bild gepostet.<br>
										Um deine Benachrichtigungseinstellungen zu ändern, besuche bitte dein <a href='http://placelet.de/profil'>Profil</a>.";
							$mail_header = "From: Placelet <support@placelet.de>\n";
							$mail_header .= "MIME-Version: 1.0" . "\n";
							$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
							$mail_header .= "Content-transfer-encoding: 8bit";
							mail($user_email, 'Neues Bild für Armband '.$braceName, $content, $mail_header);
						}
					}
				}elseif($key == 'comm_own') {
					if($val == 2 || $val == 3) {
						if($comm) {
							$content = "Zu deinem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neuer Kommentar gepostet.<br>
										Um deine Benachrichtigungseinstellungen zu ändern, besuche bitte dein <a href='http://placelet.de/profil'>Profil</a>.";
							$mail_header = "From: Placelet <support@placelet.de>\n";
							$mail_header .= "MIME-Version: 1.0" . "\n";
							$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
							$mail_header .= "Content-transfer-encoding: 8bit";
							mail($user_email, 'Neuer Kommentar für Armband '.$braceName, $content, $mail_header);
						}
					}
				}elseif($key == 'pic_subs') {
					if($val == 2 || $val == 3) {
						if(!$comm) {
							$content = "Zu dem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neues Bild gepostet.<br>
										Um keine Benachrichtigungen für dieses Armband mehr zu erhalten klicke <a href='http://placelet.de/armband?name=".urlencode($braceName)."&sub=false&sub_code=".urlencode(PassHash::hash($row['email']))."'>hier</a>";
							$mail_header = "From: Placelet <support@placelet.de>\n";
							$mail_header .= "MIME-Version: 1.0" . "\n";
							$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
							$mail_header .= "Content-transfer-encoding: 8bit";
							mail($row['email'], 'Neues Bild für Armband '.$braceName, $content, $mail_header);
							$useremails_pic_subs_informed[] = $user_email;
						}
					}
				}
			}
				//Und beim Bildbesitzer
			if($comm) {
				$sql = "SELECT userid FROM pictures WHERE brid = :brid AND picid = :picid";
				$stmt = $this->db->prepare($sql);
				$stmt->execute(array(':brid' => $brid, ':picid' => $comm));
				$picposter = $stmt->fetch();
				$sql = "SELECT userid, pic_own, comm_own, comm_pic FROM notifications WHERE userid = :picposterid";
				$stmt = $this->db->prepare($sql);
				$stmt->execute(array(':picposterid' => $picposter['userid']));
				$userProps = $stmt->fetch(PDO::FETCH_ASSOC);
				if($userProps['userid'] != 0) {
					$userdetails = $this->userdetails(self::id2username($userProps['userid']));
					$user_email = $userdetails['email'];
					foreach($userProps as $key => $val) {
						if($key == 'comm_pic') {
							if($val == 2 || $val == 3) {
								$content = "Zu deinem Bild <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neuer Kommentar gepostet.<br>
											Um deine Benachrichtigungseinstellungen zu ändern, besuche bitte dein <a href='http://placelet.de/profil'>Profil</a>.";
								$mail_header = "From: Placelet <support@placelet.de>\n";
								$mail_header .= "MIME-Version: 1.0" . "\n";
								$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
								$mail_header .= "Content-transfer-encoding: 8bit";
								mail($user_email, 'Neuer Kommentar für Armband '.$braceName, $content, $mail_header);
							}
						}
					}
				}
			}
			//Direkte Abonnenten informieren
			if(!$comm) {
				$sql = "SELECT email FROM subscriptions WHERE brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(':brid' => $brid));
				$q->setFetchMode(PDO::FETCH_ASSOC);
				while($row = $q->fetch(PDO::FETCH_ASSOC)){
					if(!in_array($row['email'], $users_pic_subs_informed)) {
						$content = "Zu dem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neues Bild gepostet.<br>
									Um keine Benachrichtigungen für dieses Armband mehr zu erhalten klicke <a href='http://placelet.de/armband?name=".urlencode($braceName)."&sub=false&sub_code=".urlencode(PassHash::hash($row['email']))."'>hier</a>";
						$mail_header = "From: Placelet <support@placelet.de>\n";
						$mail_header .= "MIME-Version: 1.0" . "\n";
						$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
						$mail_header .= "Content-transfer-encoding: 8bit";
						//mail($row['email'], 'Neues Bild für Armband '.$braceName, $content, $mail_header);
					}
				}
			}
		}
	}
	public function getUsernames() {
		$sql = "SELECT user, userid FROM users";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$q = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($q as $user) {
			$usernames[$user['userid']] = $user['user'];
		}
		return $usernames;
	}
	public static function username2id($username) {
		/*if($username == false) return 0;
		$sql = "SELECT id FROM users WHERE user = :username";
		$stmt = $GLOBALS['db']->prepare($sql);
		$stmt->execute(array(":username" => $username));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return $q['id'];*/
		return $GLOBALS['usernamelist']['id'][strtolower($username)];
	}
	public static function id2username($id) {
		/*if($id == 0) return NULL;
		$sql = "SELECT user FROM users WHERE id = :id";
		$stmt = $GLOBALS['db']->prepare($sql);
		$stmt->execute(array(":id" => $id));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return $q['user'];*/
		return $GLOBALS['usernamelist']['user'][$id];
	}
	public function id2pic($id) {
		$sql = "SELECT brid, picid FROM pictures WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":id" => $id));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return array('picid' => $q['picid'], 'brid' => $q['brid']);
	}
	public function pic2id($brid, $picid) {
		$sql = "SELECT id FROM pictures WHERE brid = :brid AND picid = :picid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":brid" => $brid, ':picid' => $picid));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		return $q['id'];
	}
	public function kjlasdf($picid, $brid) {
		$stmt = $db->prepare('SELECT id FROM pictures WHERE picid = :picid AND brid = :brid');
		//$stmt->execute(array(':picid' => $picid, ':brid' => $brid);
		$rowid = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = $db->prepare('SELECT brid FROM bracelets WHERE userid = :ownerid ORDER BY date ASC');
		$stmt->execute(array(':ownerid' => $statistics->username2id($stats[$i]['owner'])));
		$userfetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
?>