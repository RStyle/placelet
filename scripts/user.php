<?php

class User
{
	protected $db;
	public $login;
	public $logged = false; //eingeloggt?;
	public function __construct($login, $db){
		$this->db = $db;
		$this->login = $login;
		if ($login !== false && isset($_SESSION['dynamic_password']) && isset($_SESSION['user'])){ //prüfen ob eingeloggt
			
			try {
				$stmt = $this->db->prepare('SELECT * FROM dynamic_password WHERE user = :user');
				$stmt->execute(array('user' =>$_SESSION['user']));
				/*while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				
					print_r($row);
				}*/
				
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($row['password'] == $_SESSION['dynamic_password']){
					$this->logged = true;
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
		if (PassHash::check_password($row['password'], $pw)) {
			$dynamic_password = PassHash::hash($row['password']);
			$_SESSION['dynamic_password'] = $dynamic_password;
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
		
			return true; 
		} else { 
			return false; 
		}
	}
	
	public static function logout (){
		unset($_SESSION['user']);
		unset($_SESSION['dynamic_password']);
	}
	
	
	public static function register($reg, $db){ //$reg ist ein array
		//ist ein (getrimter) Wert leer?
		if(tisset($reg['reg_name']) && tisset($reg['reg_first_name']) && tisset($reg['reg_login']) && tisset($reg['reg_email']) && !empty($reg['reg_password'])  && !empty($reg['reg_password2'])){
			if($reg['reg_password'] != $reg['reg_password2']){
				return 'Passwords are not the same.';
			}
			if(strlen($reg['reg_login']) < 4) return 'Login to short. Min. 4';
			if(strlen($reg['reg_login']) > 15) return 'Login to long. Max. 15';
			if(strlen($reg['reg_password']) < 6) return 'Password to short. Min. 6';
			if(strlen($reg['reg_password']) > 30) return 'Password to short. Max. 30';
			if(check_email_address($reg['reg_email']) === false) return 'Your email address is not valid. Please check that.';
			//$stmt = $db->prepare('... FROM dynamic_password WHERE user = :user');
			//$stmt->execute(array('user' =>'blabla'));
			$sql = "INSERT INTO users (user,email,password,status) VALUES (:user,:email,:password,:status)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => $reg['reg_login'],
                ':email' => $reg['reg_email'],
				':password' => PassHash::hash($reg['reg_password']),
				':status' => 0)
			);
			
			$sql = "INSERT INTO user_status (user,code) VALUES (:user,:code)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user'=>$reg['reg_login'],
				':code'=>substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20).substr(md5 (uniqid (rand())), 0, 20)) // Ein 60 buchstabenlanger Zufallscode
			);
			
			$sql = "INSERT INTO addresses (user, last_name, first_name) VALUES (:user,:last_name,:first_name)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user' => $reg['reg_login'],
				':last_name' => $reg['reg_name'],
				':first_name' => $reg['reg_first_name'])
			);
		
			return true;
			}
		return false;
	}
	
	public function regstatuschange ($code){
		$sql = "SELECT * FROM users WHERE user = :user LIMIT 1"; 
        $q = $this->db->prepare($sql); 
        $q->execute(array(':user' => $this->login));
        $anz = $q->rowCount();
		$row = $q->fetch(PDO::FETCH_ASSOC);		
        if ($anz > 0 && $row['status'] == 0){
			$stmt = $this->db->prepare('SELECT * FROM user_status WHERE user = :user LIMIT 1');
			$stmt->execute(array('user' => $this->login));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if($row['code'] == $code){
		
				$sql= "UPDATE users SET status = :status WHERE user = :user LIMIT 1";
				$q = $this->db->prepare($sql); 
				$q->execute(array(':status' => '1', ':user' => $this->login));
		 
			}
		}
		
		
	}
/*	//Armband neu registrieren
	public function registerbr ($brid) {		
		if (!isset($braces[$brid])) {
				try {
					$sql = "INSERT INTO bracelets (user, brid, date) VALUES (:user, :brid, :date)";
					$q = $this->db->prepare($sql);
					$q->execute(array(
						':user' => $this->login,
						':brid' => $brid,
						':date' => date("Y-m-d"))
					);
				} catch(PDOException $e) {
					if ($e->getCode() == 23000) {
						return false;
					} else {
						die('ERROR: ' . $e->getMessage());
						return false;
					}
				}
				return true;
		}
	}*/
	//Armband registrieren
	public function registerbr ($brid) {
		try {
			$sql = "SELECT user FROM bracelets WHERE brid = ".$brid;
			$stmt = $this->db->query($sql);
			$bracelet = $stmt->fetchAll();
			if ($bracelet == NULL) {
				return '0';
			} elseif ($bracelet[0]['user'] == NULL ) {
				$sql = "UPDATE bracelets SET user=:user, date=:date WHERE brid=:brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':user' => $this->login,
					':brid' => $brid,
					':date' => time())//time() wäre einfacher umzuwandeln
				);
				return 1;
			}elseif ($bracelet[0]['user'] == $this->login) {
				return 2;
			}else {
				return 3;
			}
		} catch (PDOException $e) {
				die('ERROR: ' . $e->getMessage());
				return false;
		}
	}
	
	//Userdetails abfragen
	public function userdetails() {
		$sql = "SELECT user, email, status FROM users WHERE user = '".$this->login."'";
		$stmt = $this->db->query($sql);
		$result[0] = $stmt->fetchAll();
		$sql = "SELECT last_name, first_name, adress, adress_2, city, post_code, phone_number, country FROM addresses WHERE user = '".$this->login."'";
		$stmt = $this->db->query($sql);
		$result[1] = $stmt->fetchAll();
		$sql = "SELECT brid, date FROM bracelets WHERE user = '".$this->login."' ORDER BY  `bracelets`.`date` ASC ";
		$stmt = $this->db->query($sql);
		$result[2] = $stmt->fetchAll();

		$user_details = $result;
		$user_details['users'] = $user_details[0][0];
		$user_details['addresses'] = $user_details[1][0];
		$brids = array();
		foreach ($user_details[2] as $key => $val) {
			$brids = array_merge_recursive($val, $brids);
		}
		$user_details['bracelets'] = $brids;
		$userdetails = array_merge($user_details['users'], $user_details['addresses'], $user_details['bracelets']);
		return $userdetails;
	}
	
	//Statistik vom Armband abfragen
	public static function bracelet_stats($brid, $db) {
		$sql = "SELECT user, date FROM bracelets WHERE brid = '".$brid."'";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['owner'] = $q[0]['user'];
		$stats['date'] = $q[0]['date'];
		
		$sql = "SELECT user FROM pictures WHERE brid = '".$brid."'";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		if($q != NULL) {
			$stats['owners'] = count($q[0]);
		}
		return $stats;
	}
	public static function picture_details ($brid, $db) {
		$sql = "SELECT user, description, picid, city, country, date, title, fileext FROM pictures WHERE brid = '".$brid."'";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			//$details[$val['picid']] = $val;
			$details[$val['picid']]['user'] = $val['user'];
			$details[$val['picid']]['description'] = $val['description'];
			$details[$val['picid']]['picid'] = $val['picid'];
			$details[$val['picid']]['city'] = $val['city'];
			$details[$val['picid']]['country'] = $val['country'];
			$details[$val['picid']]['date'] = $val['date'];
			$details[$val['picid']]['title'] = $val['title'];
			$details[$val['picid']]['fileext'] = $val['fileext'];
		}
		$sql = "SELECT commid, picid, user, comment, date FROM comments WHERE brid = '".$brid."'";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		foreach ($q as $key => $val) {
			$details[$val['picid']] [$val['commid']] = array();
			$details[$val['picid']] [$val['commid']] ['commid'] = $val['commid'];
			$details[$val['picid']] [$val['commid']] ['picid'] = $val['picid'];
			$details[$val['picid']] [$val['commid']] ['user'] = $val['user'];
			$details[$val['picid']] [$val['commid']] ['comment'] = $val['comment'];
			$details[$val['picid']] [$val['commid']] ['date'] = $val['date'];
		}
		$details = array_reverse($details);
		return $details;
		
	}
//Kommentar schreiben
	public static function write_comment ($brid, $username, $comment, $picid, $user, $db) {
		$submissions_valid = true;
		if (isset($user->login)) {
			if ($user->login != $username) {
				$userexists = User::userexists($username, $db);
			} else $userexists = false;
		} else {
			$userexists = User::userexists($username, $db);
			
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
			try {
				$sql = "SELECT commid FROM comments WHERE brid = :brid AND picid = :picid";
				$q = $db->prepare($sql);
				$q->execute(array(':brid' => $brid, ':picid' => $picid));
				$row = $q->fetchAll(PDO::FETCH_ASSOC);	
				$row = array_reverse($row);
				$commid = $row[0]['commid'] + 1;
				
				$sql = "INSERT INTO comments (brid, commid, picid, user, comment, date) VALUES (:brid, :commid, :picid, :user, :comment, :date)";
				$q = $db->prepare($sql);
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
	//Zeigt die allgemeine Statistik an
	public static function systemStats($banz, $db) {
		//Arnzahl registrierter Armbänder
		$sql = "SELECT brid FROM bracelets WHERE user != ''";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total_registered'] = count($q);
		//Armbänder insgesamt
		$sql = "SELECT brid FROM bracelets";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['total'] = count($q);
		//Anzahl der verschiedenen Städte
		$sql = "SELECT COUNT(DISTINCT city)  FROM pictures";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['city_count'] = $q[0][0];
		//Stadt auf die die meisten Armbänder registriert wurden(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,city FROM pictures GROUP BY city ORDER BY number DESC";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['most_popular_city']['city'] = $q[0]['city'];
		$stats['most_popular_city']['number'] = $q[0]['number'];
		//Benutzer, die die meisten Armbänder auf sich registriert haben(mit Anzahl)
		//Die Anzahl der Benutzer, die Ausgegeben werden, $banz festgelegt
		$sql = "SELECT COUNT(*) AS number,user FROM bracelets GROUP BY user ORDER BY number DESC";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		for ($i = 1; $i <= $banz; $i++) {
			$stats['user_most_bracelets']['user'][$i] = $q[$i]['user'];
			$stats['user_most_bracelets']['number'][$i] = $q[$i]['number'];
		}
		//Uploads der Top-Benutzer
		for ($i = 1; $i <= $banz; $i++) {
			$sql = "SELECT COUNT(*) AS number,user FROM pictures WHERE user = '".$stats['user_most_bracelets']['user'][$i]."' GROUP BY user ORDER BY number DESC";
			$stmt = $db->query($sql);
			$q = $stmt->fetchAll();
			$stats['user_most_bracelets']['uploads'][$i] = $q[0]['number'];
		}
		//Armband, das Bilder in den meisten Städten hat(mit Anzahl)
		$sql = "SELECT COUNT(*) AS number,brid FROM pictures GROUP BY brid ORDER BY number DESC";
		$stmt = $db->query($sql);
		$q = $stmt->fetchAll();
		$stats['bracelet_most_cities']['brid'] = $q[0]['brid'];
		$stats['bracelet_most_cities']['number'] = $q[0]['number'];
		return $stats;
	}
	//Überprüft, ob ein bestimmter Benutzer $user in der Datenbank eingetragen ist
	public static function userexists ($user, $db) {
		$sql = "SELECT * FROM users WHERE user = :user LIMIT 1"; 
        $q = $db->prepare($sql); 
        $q->execute(array(':user' => $user));
        $anz = $q->rowCount();
        if ($anz > 0){
			return true;
		} else {
			return false;
		}
	}
	//Postet ein Bild
	public function registerpic ($brid, $description, $city, $country, $title, $picture_file, $max_file_size) {
		$submissions_valid = true;
		if(strlen($country) < 2) {
			$submissions_valid = false;
			return 'Das Land ist zu kurz, mindestens 2 Buchstaben, bitte.';
		}
		if(strlen($description) < 2) {
			$submissions_valid = false;
			return 'Beschreibung zu kurz, mindestens 2 Zeichen, bitte.';
		}
		if (isset($picture_file)) {
			switch($picture_file['type']) {
				case 'image/jpeg':
					$fileext = 'jpg';
					break;
				case 'image/bmp':
					$fileext = 'bmp';
					break;
				case 'image/gif':
					$fileext = 'gif';
					break;
				case 'image/png':
					$fileext = 'png';
					break;
				default:
					$submissions_valid = false;
					return "Dieses Format wird nicht unterstützt. Wende dich bitte an unseren Support, dass wir dein Format hinzufügen können.";
			}
		}
		if ($submissions_valid) {
			try {
				$sql = "SELECT picid FROM pictures WHERE brid = :brid";
				$q = $this->db->prepare($sql);
				$q->execute(array(':brid' => $brid));
				$row = $q->fetchAll(PDO::FETCH_ASSOC);	
				$row = array_reverse($row);
				$picid = $row[0]['picid'] + 1;
				
				if ($picture_file['size'] < $max_file_size) {
					move_uploaded_file($picture_file['tmp_name'], 'pictures/bracelets/pic-'.$brid.'-'.$picid.'.'.$fileext);
				}
				
				$sql = "INSERT INTO pictures (picid, brid, user, description, date, city, country, title, fileext) VALUES (:picid, :brid, :user, :description, :date, :city, :country, :title, :fileext)";
				$q = $this->db->prepare($sql);
				$q->execute(array(
					':picid' => $picid,
					':brid' => $brid,
					':user' => $this->login,
					':description' => $description,
					':date' => time(),
					'city' => $city,
					'country' => $country,
					'title' => $title,
					'fileext' => $fileext
				));
				return 'Bild erfolgreich gepostet.';
			} catch (PDOException $e) {
					die('ERROR: ' . $e->getMessage());
					return false;
			}
		}
	}
}

?>