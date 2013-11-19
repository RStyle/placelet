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
				{ $sql= " UPDATE dynamic_password SET password=:password WHERE user = :user LIMIT 1"; } 
			else
				{ $sql = "INSERT INTO dynamic_password (user,password) VALUES (:user,:password)"; }
		
			$q = $this->db->prepare($sql);
			$q->execute(array(
				':user'=>$this->login,
				':password'=>$dynamic_password)
			);
			$this->logged = true;
			//Status abfragen
			$stmt = $this->db->prepare('SELECT status FROM users WHERE user = :user');
			$stmt->execute(array('user' =>$_SESSION['user']));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row['status'] == 2) {
				$this->admin = true;
			}
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
				return 'Die Passwörter sind nicht dieselben.';
			}
			if(Statistics::userexists($reg['reg_login'])){
				return 'Dieser Benutzer existiert schon';
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
			$sql = "INSERT INTO users (user,email,password,status) VALUES (:user,:email,:password,:status)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => clean_input($reg['reg_login']),
				':email' => clean_input($reg['reg_email']),
				':password' => PassHash::hash($reg['reg_password']),
				':status' => 0)
			);
			$sql = "INSERT INTO user_status (user,code) VALUES (:user,:code)";
			$q = $db->prepare($sql);
			$code = substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20);
			$q->execute(array(
				':user' => clean_input($reg['reg_login']),
				':code' => $code) // Ein 60 buchstabenlanger Zufallscode
			);
			$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='. $reg['reg_login'].'&regstatuschange='. $code;
			$mail_header = "From: Placelet <support@placelet.de>\n";
			$mail_header .= "MIME-Version: 1.0" . "\n";
			$mail_header .= "Content-type: text/plain; charset=utf-8" . "\n";
			$mail_header .= "Content-transfer-encoding: 8bit";
			mail($reg['reg_email'], 'Bestätigungsemail', $content, $mail_header);
			$sql = "INSERT INTO addresses (user) VALUES (:user)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => clean_input($reg['reg_login']))
			);
			return true;
		} else{
			return 'Die beiden Passwörter sind nicht gleich.';
		}
	}
	public function regstatuschange ($code, $username){
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
				return 3;
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
	public function change_details($firstname, $lastname, $email, $old_pwd, $new_pwd, $username) {
		$return = '';
		//Vorname ändern
		if($firstname != NULL) {
			$firstname = clean_input($firstname);
			$sql = "UPDATE addresses SET first_name = :firstname WHERE user = :user";
			$q = $this->db->prepare($sql);
			$q->execute(array(
						':firstname' => $firstname,
						':user' => $username
						));
			$return .= "Vorname erfolgreich geändert.\\n";
		}
		//Nachname ändern
		if($lastname != NULL) {
			$lastname = clean_input($lastname);
			$sql = "UPDATE addresses SET last_name = :lastname WHERE user = :user";
			$q = $this->db->prepare($sql);
			$q->execute(array(
						':lastname' => $lastname,
						':user' => $username
						));
			$return .= "Nachname erfolgreich geändert.\\n";
		}
		//Passwort ändern
		$return .= $this->change_password($old_pwd, $new_pwd, $username)."\\n";
		//E-Mail ändern
		if($email != NULL) {
			$email = clean_input($email);
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
	public function reset_password($email, $username) {
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
			':pass_code' => '',
			':user' => $username
			));

		return 'Passwort erfolgreich geändert.';
	}
	public function revalidate($username, $email){
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
			':user' => clean_input($username),
			':code' => $code) // Ein 60 buchstabenlanger Zufallscode
		);
		$sql = "UPDATE users SET email = :email WHERE user = :user";
		$q = $this->db->prepare($sql);
		$q->execute(array(
			':user' => clean_input($username),
			':email' => $email
		));
		//Neue Email senden.
		$content = "Bitte klicke auf diesen Link, um deinen Account zu bestätigen:\n" . 'http://placelet.de/?regstatuschange_user='.$username.'&regstatuschange='.$code;
		$mail_header = "From: Placelet <support@placelet.de>\n";
		$mail_header .= "MIME-Version: 1.0" . "\n";
		$mail_header .= "Content-type: text/plain; charset=utf-8" . "\n";
		$mail_header .= "Content-transfer-encoding: 8bit";
		mail($email, 'Bestätigungsemail', $content, $mail_header);
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
		$stmt = $this->db->prepare("SELECT user, email, status FROM users WHERE user = :user LIMIT 1");
		$stmt->execute(array('user' => $user));
		$result[0] = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = $this->db->prepare("SELECT last_name, first_name, adress, adress_2, city, post_code, phone_number, country FROM addresses WHERE user = :user");
		$stmt->execute(array('user' => $user));
		$result[1] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt = $this->db->prepare("SELECT brid, date FROM bracelets WHERE user = :user ORDER BY  `bracelets`.`date` ASC ");
		$stmt->execute(array('user' => $user));
		$result[2] = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$user_details = $result;
		$user_details['users'] = $user_details[0];
		$user_details['addresses'] = $user_details[1][0]; //<--- Nur eine Adresse wird ausgegeben, aber Benutzer können eventuell mehrere haben!
		$brids = array();
		foreach ($user_details[2] as $key => $val) {
			$brids = array_merge_recursive($val, $brids);
			$stmt = $this->db->prepare("SELECT picid FROM pictures WHERE brid = :brid ORDER BY  `pictures`.`picid` DESC LIMIT 1");
			$stmt->execute(array('brid' => $val['brid']));
			$result[3][$val['brid']] = $stmt->fetch(PDO::FETCH_ASSOC);
		}
		$user_details['bracelets'] = $brids;
		$userdetails = array_merge($user_details['users'], $user_details['addresses'], $user_details['bracelets']);
		if(isset($result[3]))
			$userdetails['picture_count'] = $result[3];
		else
			$userdetails['picture_count'] = 0;
		return $userdetails;
	}
	//Zeigt die allgemeine Statistik an
	public function systemStats($user_anz, $brid_anz) {
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
				$stats['user_most_bracelets']['user'][$i] = $q[$i]['user'];
				$stats['user_most_bracelets']['number'][$i] = $q[$i]['number'];
		}
		
		//Uploads der Top-Benutzer
		for ($i = 0; $i < $user_anz; $i++) {
			$sql = "SELECT COUNT(*) AS number,user FROM pictures WHERE user = '".$stats['user_most_bracelets']['user'][$i]."' GROUP BY user ORDER BY number DESC";
			$stmt = $this->db->query($sql);
			$q = $stmt->fetchAll();
			if(isset($q[0]['number'])) {
				$stats['user_most_bracelets']['uploads'][$i] = $q[0]['number'];
			} else {
				$stats['user_most_bracelets']['uploads'][$i] = 0;
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
				ORDER BY  `date` DESC";
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
	public function name2brid($name) {
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
		$stats['owner'] = $q[0]['user'];
		$stats['date'] = $q[0]['date'];
		$sql = "SELECT picid FROM pictures WHERE brid = :brid ORDER BY  `pictures`.`picid` DESC LIMIT 1";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetch(PDO::FETCH_ASSOC);
		if($q != NULL) {
			$stats['owners'] = $q['picid'];
		}
		$stats['name'] = $this->brid2name($brid);
		return $stats;
	}
	//Bilderdetails
	public function picture_details ($brid) {
		$sql = "SELECT user, description, picid, city, country, date, title, fileext FROM pictures WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			//$details[$val['picid']] = $val;
			$details[$val['picid']]['user'] = $val['user'];
			$details[$val['picid']]['description'] = nl2br($val['description'], 0);
			$details[$val['picid']]['picid'] = $val['picid'];
			$details[$val['picid']]['city'] = $val['city'];
			$details[$val['picid']]['country'] = $val['country'];
			$details[$val['picid']]['date'] = $val['date'];
			$details[$val['picid']]['title'] = $val['title'];
			$details[$val['picid']]['fileext'] = $val['fileext'];
		}
		$sql = "SELECT commid, picid, user, comment, date FROM comments WHERE brid = :brid";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array('brid' => $brid));
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']] [$val['commid']] = array();
			$details[$val['picid']] [$val['commid']] ['commid'] = $val['commid'];
			$details[$val['picid']] [$val['commid']] ['picid'] = $val['picid'];
			$details[$val['picid']] [$val['commid']] ['user'] = $val['user'];
			$details[$val['picid']] [$val['commid']] ['comment'] = nl2br($val['comment'], 0);
			$details[$val['picid']] [$val['commid']] ['date'] = $val['date'];
		}
		$details = array_reverse($details);
		return $details;
		
	}
	//Kommentar schreiben
	public function write_comment ($brid, $username, $comment, $picid) {
		$submissions_valid = true;
		if (isset($this->user->login)) {
			if ($this->user->login != $username) {
				$userexists = Statistics::userexists($username);
			} else $userexists = false;
		} else {
			$userexists = Statistics::userexists($username);
			
		}
		if ($userexists) {
			return 'Diesen Benutzer gibt es schon';
			$submissions_valid = false;
		}
		if(strlen($username) < 4) {
			$submissions_valid = false;
			return 'Benutzername zu kurz, mindestens 4 Zeichen';
		}
		if(strlen($comment) < 2) {
			$submissions_valid = false;
			return 'Kommentar zu kurz, mindestens 2 Zeichen';
		}
		if ($submissions_valid) {
			$comment = clean_input($comment);
			$username = clean_input($username);
			$brid = clean_input($brid);
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
				return 'Kommentar erfolgreich gesendet.';
			} catch (PDOException $e) {
					die('ERROR: ' . $e->getMessage());
					return false;
			}
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
	public function registerpic ($brid, $description, $city, $country, $state, $latitude, $longitude, $title, $picture_file, $max_file_size) {
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
		//Prüft, wenn es das erste Bild ist, ob der Poster der Besitzer ist
		$bracelet_stats = $this->bracelet_stats($brid);
		if($bracelet_stats['owner'] != $this->user->login && !isset($bracelet_stats['owners'])) return 6;
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
				///////////////////////////
			
				$sql = "INSERT INTO pictures (picid, brid, user, description, date, city, country, title, fileext, latitude, longitude, state) VALUES (:picid, :brid, :user, :description, :date, :city, :country, :title, :fileext, :latitude, :longitude, :state)";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':user' => $this->user->login,
					':description' => $description,
					':date' => time(),
					'city' => $city,
					'country' => $country,
					'title' => $title,
					'fileext' => $fileext,
					':latitude' => $latitude,
					':longitude' => $longitude,
					':state' => $state
				));
				///E-Mail an die Personen senden, die das Armband abboniert haben
				$sql = "SELECT email FROM subscriptions WHERE brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(':brid' => $brid));
				$q->setFetchMode(PDO::FETCH_ASSOC);
				while( $row = $q->fetch(PDO::FETCH_ASSOC)){
				print_r($row);
				echo '<h1>'.$row['email'].'ascasc</h1>';
				$content = "Zu dem Armband <a href='http://placelet.de/armband?name=".urlencode($this->brid2name($brid))."'>".$this->brid2name($brid)."</a> wurde ein neues Bild gepostet.<br>
							Um keine Benachrichtigungen für dieses Armband mehr zu erhalten klicke <a href='http://placelet.de/armband?name=".urlencode($this->brid2name($brid))."&sub=false&sub_email=".urlencode(PassHash::hash($row['email']))."'>hier</a>";
				$mail_header = "From: Placelet <support@placelet.de>\n";
				$mail_header .= "MIME-Version: 1.0" . "\n";
				$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
				$mail_header .= "Content-transfer-encoding: 8bit";
				mail($row['email'], 'Neues Bild für Armband '.$this->brid2name($brid), $content, $mail_header);
				}
				return 7;//Bild erfolgreich gepostet.
			} elseif ($file_uploaded == false) {
				return $picture_file['error'];//Mit dem Bild stimmt etwas nicht. Bitte melde deinen Fall dem Support.
			}
		}
	}
	public function manage_subscription($input, $brid, $email) {
		if($input == 'true') {//true beudeutet Hinzufügen
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
			foreach($result as $key => $val) {
				if(PassHash::check_password(urldecode($email), $val['email'])) {
					$sql = "DELETE FROM subscriptions WHERE brid = :brid AND email = :email";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':email' => $val['email'],
						':brid' => $brid
					));
					return false;
				}else {
					return 3;
				}
			}
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
					return 2;//gemeldet
				}
			}else {
				return 2;//gemeldet
			}
		}
	}
}
?>