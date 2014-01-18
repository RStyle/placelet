<?php
class User
{
	protected $db;
	public $login;
	public $logged = false; //eingeloggt?
	public $admin = false; //admin?
	public function __construct($login, $db){
		$this->db = $db;
		$this->login = $login;
		if ($login !== false && isset($_SESSION['dynamic_password']) && isset($_SESSION['user'])){ //prüfen ob eingeloggt
			try {
				$stmt = $this->db->prepare('SELECT * FROM dynamic_password WHERE user = :user');
				$stmt->execute(array('user' =>$_SESSION['user']));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if (PassHash::check_password(substr($_SESSION['dynamic_password'], 0, 60), substr($row['password'], 0, 15)) 
					&& PassHash::check_password(substr($_SESSION['dynamic_password'], 60, 60), substr($row['password'], 15, 15)) 
					&& PassHash::check_password(substr($_SESSION['dynamic_password'], 120, 60), substr($row['password'], 30, 15)) 
					&& PassHash::check_password(substr($_SESSION['dynamic_password'], 180, 60), substr($row['password'], 45, 15))
					//Überprüfung des 4-fachen Hashs des Hashes - müsste unschlagbare Sicherheit bieten ;)
				){
					$this->logged = true;
					//Status abfragen
					$stmt = $this->db->prepare('SELECT status FROM users WHERE user = :user');
					$stmt->execute(array('user' =>$_SESSION['user']));
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					if($row['status'] == 2) {
						$this->admin = true;
					}
				} else {
					echo substr($_SESSION['dynamic_password'], 60, 60). '++++' .  substr($row['password'], 15, 15);
					$this->login = false;	//Hiermit werden falsch eingeloggte Benutzer nicht mehr mit $this->login Sicherheitslücken umgehen können
					$this->logout();	//Um zukünftige fehlschlagende Versuche des automatischen Logins zu vermeiden
				}
			} catch(PDOException $e) {
				die('ERROR: ' . $e->getMessage());
			}
		}
	}
	
	public function login ($pw){
		$stmt = $this->db->prepare('SELECT * FROM users WHERE user = :user');
		$stmt->execute(array('user' => $this->login));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row['status'] == 0) return 2;
		if (PassHash::check_password($row['password'], $pw)) {
			$this->login = $row['user'];
			$dynamic_password = PassHash::hash($row['password']);
			$_SESSION['dynamic_password'] = PassHash::hash(substr($dynamic_password, 0, 15)).PassHash::hash(substr($dynamic_password, 15, 15)).PassHash::hash(substr($dynamic_password, 30, 15)).PassHash::hash(substr($dynamic_password, 45, 15));
			//4-facher Hash des Hashes - da der Hash ab einer bestimmten Anzahl von Buchstaben das Passwort abschneidet.
			$_SESSION['user'] = $this->login;
		
			$sql = "SELECT * FROM dynamic_password WHERE user = :user LIMIT 1"; 
            $q = $this->db->prepare($sql); 
            $q->execute(array(':user' => $this->login));
            $anz = $q->rowCount(); 
            if ($anz > 0)
				{ $sql= "UPDATE dynamic_password SET password=:password WHERE user = :user LIMIT 1"; } 
			else
				{ $sql = "INSERT INTO dynamic_password (user,password) VALUES (:user,:password)"; }
		
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':user' => $this->login,
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
			$stmt = $this->db->prepare('SELECT status FROM users WHERE user = :user');
			$stmt->execute(array('user' =>$_SESSION['user']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row['status'] == 2) $this->admin = true;
			if($row['last_login'] == 0) return 3;
			return true;
		}else { 
			return false; 
		}
	}
	
	public static function logout (){
		unset($_SESSION['user']);
		unset($_SESSION['dynamic_password']);
	}
	
	public static function register($reg, $db){ //$reg ist ein array
		//ist ein (getrimter) Wert leer?
		if(tisset($reg['reg_login']) && tisset($reg['reg_email']) && !empty($reg['reg_password'])  && !empty($reg['reg_password2'])){
			if($reg['reg_password'] != $reg['reg_password2']){
				return 'Die Passwörter passen nicht zusammen.';
			}
			if(Statistics::userexists($reg['reg_login'])){
				return 'Dieser Benutzer existiert schon.';
			}
			//Überprüfen, ob die E-Mail Adresse schon registriert wurde.
			$stmt = $db->prepare('SELECT email FROM users WHERE email = :email');
			$stmt->execute(array('email' => $reg['reg_email']));
			$anz = $stmt->rowCount();
			if($anz != 0) return 'Auf diese E-Mail Adresse wurde schon ein anderer Benutzer registriert.';
			if(strlen($reg['reg_login']) < 4) return 'Benutzername zu kurz. Min. 4';
			if(strlen($reg['reg_login']) > 15) return 'Benutzername zu lang. Max. 15';
			if(strlen($reg['reg_password']) < 6) return 'Passwort zu kurz. Min. 6';
			if(strlen($reg['reg_password']) > 30) return 'Passwort zu lang. Max. 30';
			if(check_email_address($reg['reg_email']) === false) return 'Das ist keine gültige E-Mail Adresse';
			$sql = "INSERT INTO users (user, email, password, status, date) VALUES (:user, :email, :password, :status, :date)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => trim($reg['reg_login']),
				':email' => trim($reg['reg_email']),
				':password' => PassHash::hash($reg['reg_password']),
				':status' => 0,
				':date' => time()
			));
			$sql = "INSERT INTO user_status (user,code) VALUES (:user,:code)";
			$q = $db->prepare($sql);
			$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
			$q->execute(array(
				':user' => trim($reg['reg_login']),
				':code' => $code) // Ein 60 buchstabenlanger Zufallscode
			);
			$sql = "INSERT INTO notifications (user, pic_own, comm_own, comm_pic) VALUES (:user, :pic_own, :comm_own, :comm_pic)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => trim($reg['reg_login']),
				':pic_own' => 3,
				':comm_own' => 1,
				':comm_pic' => 1
			));
			$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='.urlencode($reg['reg_login']).'&regstatuschange='.urlencode($code);
			$mail_header = "From: Placelet <support@placelet.de>\n";
			$mail_header .= "MIME-Version: 1.0" . "\n";
			$mail_header .= "Content-type: text/plain; charset=utf-8" . "\n";
			$mail_header .= "Content-transfer-encoding: 8bit";
			mail($reg['reg_email'], 'Bestätigungsemail', $content, $mail_header);
			return true;
		}else {
			return 'Die beiden Passwörter sind nicht gleich.';
		}
	}
	public function regstatuschange ($code, $username){
		$username = urldecode($username);
		$sql = "SELECT * FROM users WHERE user = :user LIMIT 1"; 
        $q = $this->db->prepare($sql); 
        $q->execute(array(':user' => $username));
        $anz = $q->rowCount();
		$row = $q->fetch(PDO::FETCH_ASSOC);		
        if ($anz > 0 && $row['status'] == 0){
			$stmt = $this->db->prepare('SELECT * FROM user_status WHERE user = :user LIMIT 1');
			$stmt->execute(array('user' => $username));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row['code'] == $code){
				$sql= "UPDATE users SET status = :status WHERE user = :user LIMIT 1";
				$q = $this->db->prepare($sql); 
				$q->execute(array(':status' => '1', ':user' => $username));
				//Code löschen
				$sql= "UPDATE user_status SET code = :code WHERE user = :user LIMIT 1";
				$q = $this->db->prepare($sql); 
				$q->execute(array(':code' => NULL, ':user' => $username));
				return true;
			}else {
				return false;
			}
		}
	}
	//Armband registrieren
	public function registerbr ($brid) {
		try {
			$stmt = $this->db->prepare('SELECT user FROM bracelets WHERE brid = :brid LIMIT 1');
			$stmt->execute(array('brid' => $brid));
			$anz = $stmt->rowCount(); 
			$bracelet = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($anz == 0) {
				return 0;
			} elseif ($bracelet['user'] == NULL ) {	
				$stmt = $this->db->prepare('SELECT COUNT(*) FROM bracelets WHERE user = :user');
				$stmt->execute(array('user' => $this->login));
				$q2 = $stmt->fetch(PDO::FETCH_ASSOC);
				$number = $q2['COUNT(*)'] + 1;
				
				$sql = "UPDATE bracelets SET user = :user, date = :date, name = :name WHERE brid=:brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':user' => $this->login,
					':brid' => $brid,
					':date' => time(),
					':name' => $this->login.'#'.$number)
				);
				return 1;
			}elseif ($bracelet['user'] == $this->login) {
				return 2;
			}else {
				return array(3, $bracelet['user']);
			}
		} catch (PDOException $e) {
				die('ERROR: ' . $e->getMessage());
				return false;
		}
	}
	//Passwort ändern
	public function change_password($old_pwd, $new_pwd, $username) {
		if($old_pwd != NULL && $new_pwd != NULL) {
			$stmt = $this->db->prepare('SELECT password FROM users WHERE user = :user');
			$stmt->execute(array('user' => $this->login));
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if (PassHash::check_password($result['password'], $old_pwd)) {
				$sql = "UPDATE users SET password = :password WHERE user = :user";
				$q = $this->db->prepare($sql);
				$q->execute(array(
							':password' => PassHash::hash($new_pwd),
							':user' => $username
							));
				return 'Passwort erfolgreich geändert.';
			}else {
				return 'Falsches Passwort';
			}
		}
	}
	//Accountdetails ändern
	public function change_details($email, $old_pwd, $new_pwd, $username) {
		$return = '';
		//Passwort ändern
		$return .= $this->change_password($old_pwd, $new_pwd, $username)."\\n";
		//E-Mail ändern
		if($email != NULL) {
			$email = trim($email);
			if(check_email_address($email)) {
				$sql = "UPDATE users SET email = :email WHERE user = :user";
				$q = $this->db->prepare($sql);
				$q->execute(array(
							':email' => $email,
							':user' => $username
							));
				$return .= 'E-Mail erfolgreich geändert.';
			}else {
				$return .= 'Das ist keine gültige E-Mail.';
			}
		}
		return $return;
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
		if($email != NULL && $submissions_valid) {
		  $code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
		  $sql = "UPDATE user_status SET pass_code = :pass_code WHERE user = :user";
		  $q = $this->db->prepare($sql);
		  $q->execute(array(
				':pass_code' => $code,
				':user' => $username
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
		  return 'Erfolgreich gesendet';
		}else {
			return 'Es ist ein Fehler aufgetreten.//nDiese E-Mail ist nicht bei uns registriert.';
		}
	}
	public function check_recover_code($code) {
		$stmt = $this->db->prepare("SELECT user FROM user_status WHERE pass_code = :code");
		$stmt->execute(array('code' => $code));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result != NULL) {
			return $result['user'];
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
		//Code wieder löschen
		$sql = "UPDATE user_status SET pass_code = :pass_code WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':pass_code' => NULL,
			':user' => $username
			));

		return 'Passwort erfolgreich geändert.';
	}
	public function revalidate($username, $email){
		$username = trim($username);
		$email = trim($email);
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
		$sql = "UPDATE user_status SET code = :code WHERE user = :user";
		$q = $this->db->prepare($sql);
		$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
		$q->execute(array(
			':user' => $username,
			':code' => $code) // Ein 60 buchstabenlanger Zufallscode
		);
		$sql = "UPDATE users SET email = :email WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':user' => $username,
			':email' => $email
		));
		//Neue Email senden.
		$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='.urlencode($username).'&regstatuschange='.urlencode($code);
		$mail_header = "From: Placelet <support@placelet.de>\n";
		$mail_header .= "MIME-Version: 1.0" . "\n";
		$mail_header .= "Content-type: text/plain; charset=utf-8" . "\n";
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
		$comm_pic_online, $comm_pic_email
	) {
		$pic_own = 0;
		$comm_own = 0;
		$comm_pic = 0;
		if($pic_own_online == 'on') $pic_own++;
		if($pic_own_email == 'on') $pic_own+=2;
		if($comm_own_online == 'on') $comm_own++;
		if($comm_own_email == 'on') $comm_own+=2;
		if($comm_pic_online == 'on') $comm_pic++;
		if($comm_pic_email == 'on') $comm_pic+=2;
		$sql = "UPDATE notifications SET pic_own = :pic_own, comm_own = :comm_own, comm_pic = :comm_pic WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':user' => $this->login,
			':pic_own' => $pic_own,
			':comm_own' => $comm_own,
			':comm_pic' => $comm_pic
		));
		return true;
	}
}
?>
<?php
class Statistics {
	protected $db;
	public function __construct($db, $user){
		$this->db = $db;
		$this->user = $user;
	}
	//Userdetails abfragen
	public function userdetails($user) {
		$result = array();
		//Allgemeine Daten
		$stmt = $this->db->prepare("SELECT user, email, status, date AS registered FROM users WHERE user = :user LIMIT 1");
		$stmt->execute(array('user' => $user));
		$result[0] = $stmt->fetch(PDO::FETCH_ASSOC);
		//Gekaufte Armbänder
		$stmt = $this->db->prepare("SELECT brid, date FROM bracelets WHERE user = :user ORDER BY  `bracelets`.`date` ASC ");
		$stmt->execute(array('user' => $user));
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
		$stmt = $this->db->prepare("SELECT brid, picid, fileext FROM pictures WHERE user = :user");
		$stmt->execute(array('user' => $user));
		$result[4] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($result[4] as $key => $val) {
			$stmt = $this->db->prepare("SELECT picid FROM pictures WHERE brid = :brid ORDER BY picid DESC");
			$stmt->execute(array('brid' => $val['brid']));
			$q = $stmt->fetch(PDO::FETCH_ASSOC);
			$result[4][$key]['picCount'] = $q['picid'];
		}
		//Benachrichtigungen
		$stmt = $this->db->prepare("SELECT pic_own, comm_own, comm_pic FROM notifications WHERE user = :user LIMIT 1");//none: 0, online: 1, email: 2, online&email: 3    ----- Just like chmod: online = 1, email = 2
		$stmt->execute(array('user' => $user));
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
		$userdetails['user'] = htmlentities($userdetails['user']);
		return $userdetails;
	}
	//Zeigt die allgemeine Statistik an
	public function systemStats($user_anz, $brid_anz) {
		//Arnzahl 'beposteter' Armbänder		
		$sql = "SELECT brid FROM pictures GROUP BY brid";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total_posted'] = count($q);
		
		//Arnzahl registrierter Armbänder
		$sql = "SELECT brid FROM bracelets WHERE user != ''";
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
		
		//Stadt auf die die meisten Armbänder registriert wurden(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,city FROM pictures GROUP BY city ORDER BY number DESC";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['most_popular_city']['city'] = $q[0]['city'];
		$stats['most_popular_city']['number'] = $q[0]['number'];
		
		//Benutzer, die die meisten Armbänder auf sich registriert haben(mit Anzahl)
		//Die Anzahl der Benutzer, die Ausgegeben werden, $banz festgelegt
		$sql = "SELECT COUNT(*) AS number,user FROM bracelets WHERE user IS NOT NULL GROUP BY user ORDER BY number DESC";//GROUP BY user ORDER BY number DESC
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		for ($i = 0; $i < $user_anz; $i++) {
			if(isset($q[$i]['user'])) {
				$stats['user_most_bracelets']['user'][$i] = htmlentities($q[$i]['user']);
				$stats['user_most_bracelets']['number'][$i] = $q[$i]['number'];
			}
		}
		
		//Uploads der Top-Benutzer
		for ($i = 0; $i < $user_anz; $i++) {
			if(isset($stats['user_most_bracelets']['user'][$i])) {
				$sql = "SELECT COUNT(*) AS number,user FROM pictures WHERE user = '".$stats['user_most_bracelets']['user'][$i]."' GROUP BY user ORDER BY number DESC";
				$stmt = $this->db->query($sql);
				$q = $stmt->fetchAll();
				if(isset($q[0]['number'])) {
					$stats['user_most_bracelets']['uploads'][$i] = $q[0]['number'];
				} else {
					$stats['user_most_bracelets']['uploads'][$i] = 0;
				}
			}
		}
		
		//Armband, das Bilder in den meisten Städten hat(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,brid FROM pictures GROUP BY brid ORDER BY number DESC";
		$stmt = $this->db->query($sql);
		$q = $stmt->fetchAll();
		$stats['bracelet_most_cities']['brid'] = $q[0]['brid'];
		$stats['bracelet_most_cities']['name'] = $this->brid2name($q[0]['brid']);
		$stats['bracelet_most_cities']['number'] = $q[0]['number'];
		
		//Ermittelt die IDs der neuesten $brid_anz Bilder
		$sql = "SELECT brid
				FROM pictures
				ORDER BY id DESC";
		$stmt = $this->db->query($sql);
		$q1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$q2 = array();
		$q = array();
		foreach ($q1 as $i){
			if(!isset($q2[$i['brid']])){
				$q2[$i['brid']] = true;
				$q[] = array( 'brid' => $i['brid']);
			}
		}
		for($i = 0; $i < $brid_anz; $i++) {
			$stats['recent_brids'][$i+1] = $q[$i]['brid'];
		}
		return $stats;
	}
	//Name von Armband ermitteln
	public function brid2name($brid) {
		$stmt = $this->db->prepare('SELECT name, user, date FROM bracelets WHERE brid = :brid');
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
	public function bracelet_stats($brid) {
		$sql = "SELECT user, date FROM bracelets WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			$stats['name'] = false;
		}else {
			$stats['name'] = $this->brid2name($brid);
			$stats['owner'] = $q[0]['user'];
			$stats['date'] = $q[0]['date'];
			$sql = "SELECT picid FROM pictures WHERE brid = :brid ORDER BY  `pictures`.`picid` DESC LIMIT 1";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array('brid' => $brid));
			$q = $stmt->fetch(PDO::FETCH_ASSOC);
			if($q != NULL) {
				$stats['owners'] = $q['picid'];
			}
		}
		return $stats;
	}
	//Bilderdetails
	public function picture_details ($brid) {
		$sql = "SELECT user, description, picid, city, country, date, title, fileext, longitude, latitude, state FROM pictures WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']]['user'] = htmlentities($val['user']);
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
		$sql = "SELECT commid, picid, user, comment, date FROM comments WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']] [$val['commid']] = array();
			$details[$val['picid']] [$val['commid']] ['commid'] = $val['commid'];
			$details[$val['picid']] [$val['commid']] ['picid'] = $val['picid'];
			$details[$val['picid']] [$val['commid']] ['user'] = htmlentities($val['user']);
			$details[$val['picid']] [$val['commid']] ['comment'] = nl2br($val['comment'], 0);
			$details[$val['picid']] [$val['commid']] ['date'] = $val['date'];
		}
		$details = array_reverse($details);
		return $details;
		
	}
	//Kommentar schreiben
	public function write_comment ($brid, $username, $comment, $picid) {
		$username = trim($username);
		$brid = $brid;
		$comment = clean_input($comment);
		if($this->user->login != $username) $username = '[Gast] '.$username;
		if($username == '[Gast] ') $username = '[Gast] Anonymous';
		if(strlen($username) < 4) {
			return 'Benutzername zu kurz, mindestens 4 Zeichen';
		}
		if(strlen($comment) < 2) {
			return 'Kommentar zu kurz, mindestens 2 Zeichen';
		}
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
			
			$sql = "INSERT INTO comments (brid, commid, picid, user, comment, date) VALUES (:brid, :commid, :picid, :user, :comment, :date)";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':brid' => $brid,
				':commid' => $commid,
				':picid' => $picid,
				':user' => $username,
				':comment' => $comment,
				':date' => time())
			);
			$this->notify_subscribers($brid, $username, true);
			return 'Kommentar erfolgreich gesendet.';
		} catch (PDOException $e) {
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
	//Prüft, ob ein Armband schon registriert wurde
	public function bracelet_status($brid) {
		$stmt = $this->db->prepare('SELECT user FROM bracelets WHERE brid = :brid');
		$stmt->execute(array('brid' => $brid));
		$anz = $stmt->rowCount();
		$bracelet = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($anz == 0) {
			return '0';
		} elseif ($bracelet['user'] == NULL ) {
			return 1;
		} else {
			return 2;
		}
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
				return 'Wir unterstützen nur Bilder bis 8MB Größe';
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
						if($exif_date['DateTimeOriginal'] == NULL || preg_match('[^A-Za-z]', $exif_date['DateTimeOriginal'])) {
							$date = time();
						}else {
							$date = strtotime($exif_date['DateTimeOriginal']);
						}
					}else {
						$date = time();
					}
				}
				///////////////////////////
			
				$sql = "INSERT INTO pictures (picid, brid, user, description, date, city, country, title, fileext, latitude, longitude, state) VALUES (:picid, :brid, :user, :description, :date, :city, :country, :title, :fileext, :latitude, :longitude, :state)";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':user' => $this->user->login,
					':description' => $description,
					':date' => $date,
					'city' => $city,
					'country' => $country,
					'title' => $title,
					'fileext' => $fileext,
					':latitude' => $latitude,
					':longitude' => $longitude,
					':state' => $state
				));
				///E-Mail an die Personen senden, die das Armband abboniert haben
				$this->notify_subscribers($brid, $this->user->login);
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
				$sql = "SELECT user FROM bracelets WHERE user = :user AND brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':user' => $this->user->login,
					':brid' => $brid,
				));
				$anz = $q->rowCount();
				if($anz == 1) {
					return $this->delete_comment($input, $commid, $picid, $brid);
				}else {
					$sql = "UPDATE comments SET spam = true WHERE brid = :brid AND picid = :picid AND commid = :commid";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid,
						':commid' => $commid
					));
					return 2;//gemeldet
				}
			}else {
				$sql = "UPDATE comments SET spam = true WHERE brid = :brid AND picid = :picid AND commid = :commid";
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
			$sql = "SELECT fileext FROM pictures WHERE brid = :brid AND picid = :picid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
			$result = $q->fetch(PDO::FETCH_ASSOC);
			$file_path = $brid.'-'.$picid.'.';
			unlink($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/pic-'.$file_path.$result['fileext']);
			unlink($_SERVER['DOCUMENT_ROOT'].'/pictures/bracelets/thumb-'.$file_path.'jpg');
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
				$sql = "SELECT user FROM bracelets WHERE user = :user AND brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':user' => $this->user->login,
					':brid' => $brid,
				));
				$anz = $q->rowCount();
				if($anz == 1) {
					return $this->delete_pic($input, $picid, $brid);
				}else {
					$sql = "UPDATE pictures SET spam = true WHERE brid = :brid AND picid = :picid";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':picid' => $picid,
						':brid' => $brid
					));
					return 2;//gemeldet
				}
			}else {
				$sql = "UPDATE pictures SET spam = true WHERE brid = :brid AND picid = :picid";
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
		$sql = "SELECT commid, picid, user, comment, date, brid FROM comments WHERE spam = true";
		$q = $this->db->prepare($sql);
		$q->execute();
		$result['spam_comments'] = $q->fetchAll(PDO::FETCH_ASSOC);
		//Spam-Bilder
		$sql = "SELECT picid, brid, user, description, date, city, country, title, fileext FROM pictures WHERE spam = true";
		$q = $this->db->prepare($sql);
		$q->execute();
		$result['spam_pics'] = $q->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
	public function no_spam($brid, $picid, $commid) {
		if($commid == 0) {//Bild
			$sql = "UPDATE pictures SET spam = false WHERE brid = :brid AND picid = :picid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid
			));
		}else {//Kommentar
			$sql = "UPDATE comments SET spam = false WHERE brid = :brid AND picid = :picid AND commid = :commid";
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':picid' => $picid,
				':brid' => $brid,
				':commid' => $commid
			));			
		}
	}
	public function private_userdetails() {
		$stmt = $this->db->prepare("SELECT pic_own_online, pic_own_email, comm_own_online, comm_own_email, comm_own_online, comm_pic_email FROM users WHERE user = :user LIMIT 1");
		$stmt->execute(array('user' => $user));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	}
	private function notify_subscribers($brid, $uploader, $comm = false) {
		$own_bracelet = false;
		$bracelet_stats = $this->bracelet_stats($brid);
		$owner = $bracelet_stats['owner'];
		if($this->user->login) if($this->user->login == $owner) $own_bracelet = true;
		if(!$own_bracelet) {
			$braceName = $this->brid2name($brid);
			if(!$comm) {
				//Direkte Abonnenten informieren
				$sql = "SELECT email FROM subscriptions WHERE brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(':brid' => $brid));
				$q->setFetchMode(PDO::FETCH_ASSOC);
				while($row = $q->fetch(PDO::FETCH_ASSOC)){
					$content = "Zu dem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neues Bild gepostet.<br>
								Um keine Benachrichtigungen für dieses Armband mehr zu erhalten klicke <a href='http://placelet.de/armband?name=".urlencode($braceName)."&sub=false&sub_code=".urlencode(PassHash::hash($row['email']))."'>hier</a>";
					$mail_header = "From: Placelet <support@placelet.de>\n";
					$mail_header .= "MIME-Version: 1.0" . "\n";
					$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
					$mail_header .= "Content-transfer-encoding: 8bit";
					mail($row['email'], 'Neues Bild für Armband '.$braceName, $content, $mail_header);
				}
			}
			//Benachrichtigungen, wie im Profil festgelegt
			$sql = "SELECT user, pic_own, comm_own, comm_pic FROM notifications WHERE user = :uploader";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array(':uploader' => $uploader));
			$q = $stmt->fetchAll();
			foreach($q as $userNR => $userProps) {
				$userdetails = $this->userdetails($userProps['user']);
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
					echo 'HI; DU HAST NOCH EIN KOMMENTAR GESENDET';
							if($comm) {
								$content = "Zu deinem Armband <a href='http://placelet.de/armband?name=".urlencode($braceName)."'>".$braceName."</a> wurde ein neuer Kommentar gepostet.<br>
											Um deine Benachrichtigungseinstellungen zu ändern, besuche bitte dein <a href='http://placelet.de/profil'>Profil</a>.";
								$mail_header = "From: Placelet <support@placelet.de>\n";
								$mail_header .= "MIME-Version: 1.0" . "\n";
								$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
								$mail_header .= "Content-transfer-encoding: 8bit";
								mail($user_email, 'Neues Kommentar für Armband '.$braceName, $content, $mail_header);
							}
						}
					}
				}
			}
		}
	}
}
?>