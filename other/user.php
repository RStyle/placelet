<?php

class User
{
	protected $db;
	public $login;
	public $logged = false; //eingeloggt?;
	public function __construct($login, $db){
		$this->db = $db;
		$this->login = $login;
		if ($login !== false && isset($_SESSION['dynamic_password'])){ //prüfen ob eingeloggt
			
			try {
				$stmt = $this->db->prepare('SELECT * FROM dynamic_password WHERE user = :user');
				$stmt->execute(array('user' =>'blabla'));
				/*while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
				
					print_r($row);
				}*/
				
				$row = $stmt->fetch(PDO::FETCH_OBJ);
				if (PassHash::check_password($row['password'], $_SESSION['dynamic_password'])){
				//if ($row['password'] == $_SESSION['dynamic_password']){
					$this->logged = true;
				}
			} catch(PDOException $e) {
				die('ERROR: ' . $e->getMessage());
			}
		}
	}
	
	public function login ($pw){
		$stmt = $this->db->prepare('SELECT * FROM users WHERE user = :user');
		$stmt->execute(array('user' =>$this->login));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		print_r($row);
		echo '---'.$pw;
		if (PassHash::check_password($row['password'], $pw)) { return true; } else { return false; }
	}
	
	public static function register($reg, $db){ //$reg ist ein array
		//ist ein (getrimter) Wert leer?
		if(tisset($reg['reg_name']) && tisset($reg['reg_first_name']) && tisset($reg['reg_login']) && tisset($reg['reg_email']) && !empty($reg['reg_password'])  && !empty($reg['reg_password2'])){
			if($reg['reg_password'] != $reg['reg_password2']){
				return 'Passwords are not the same.';
			}
			if(strlen($reg['reg_login']) < 4) return 'Login to short. Min. 4';
			if(strlen($reg['reg_password']) < 6) return 'Password to short. Min. 6';
			if(check_email_address($reg['reg_email']) === false) return 'Your email address is not valid. Please check that.';
			//$stmt = $db->prepare('... FROM dynamic_password WHERE user = :user');
			//$stmt->execute(array('user' =>'blabla'));
			$sql = "INSERT INTO users (user,email,password,status) VALUES (:user,:email,:password,:status)";
			$q = $db->prepare($sql);
			$q->execute(array(
				':user'=>$reg['reg_login'],
                ':email'=>$reg['reg_email'],
				':password'=>PassHash::hash($reg['reg_password']),
				':status'=>0)
			);
		
			return true;
			}
		return false;
	}
	
	
	



}

?>