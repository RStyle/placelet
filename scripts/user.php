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
	//Armband registrieren
	public static function registerbr ($brid, $user, $db) {		
		if (!isset($braces[$brid])) {
				try {
					$sql = "INSERT INTO bracelets (user, brid, date) VALUES (:user, :brid, :date)";
					$q = $db->prepare($sql);
					$q->execute(array(
						':user' => $user,
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
	}
	//Userdetails abfragen
	public static function userdetails($username, $db) {
		$sql = "SELECT user, email, status FROM users WHERE user = '".$username."'";
		$stmt = $db->query($sql);
		$result[0] = $stmt->fetchAll();
		$sql = "SELECT last_name, first_name, adress, adress_2, city, post_code, phone_number, country, status FROM addresses WHERE user = '".$username."'";
		$stmt = $db->query($sql);
		$result[1] = $stmt->fetchAll();
		$sql = "SELECT brid, date FROM bracelets WHERE user = '".$username."' ORDER BY  `bracelets`.`date` ASC ";
		$stmt = $db->query($sql);
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
	
}

?>