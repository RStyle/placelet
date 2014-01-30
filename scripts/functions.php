<?php
//Vergangene Zeit berechnen
function days_since($unix_time) {
	$x_days_ago = ceil((strtotime("00:00") - $unix_time) / 86400);
	switch($x_days_ago) {
		case 0:
			$x_days_ago = $GLOBALS['lang']->misc->comments->heute->$GLOBALS['lng'];
			break;
		case 1:
			$x_days_ago = $GLOBALS['lang']->misc->comments->gestern->$GLOBALS['lng'];
			break;
		default:
			$x_days_ago = $GLOBALS['lang']->misc->comments->tagenvor->$GLOBALS['lng'].' '.$x_days_ago.' '.$GLOBALS['lang']->misc->comments->tagenend->$GLOBALS['lng'];
	}
	return $x_days_ago;
}
//Verarbeitet die Profildaten
function profile_stats($userdetails) {
	if (isset($userdetails['brid'])) {
		if (is_array($userdetails['brid'])) {
			foreach ($userdetails['brid'] as $val => $key) {
				$armbaender['brid'][$val] = $key;
			}
			foreach ($userdetails['date'] as $val => $key) {
				$armbaender['date'][$val] = $key;
			}
			$armbaender['picture_count'] = $userdetails['picture_count'];
		} else {
			$armbaender['brid'][0] = $userdetails['brid'];
			$armbaender['date'][0] = $userdetails['date'];
		}
		return $armbaender;
	}
}
//Prüft, ob das Captcha richtig eingegeben wurde.
function captcha_valid($captcha, $captcha_entered) {
	$privatekey = "6LfIVekSAAAAAD0cAiYIaUHY2iKSMkyWevTAhTkb";
	$resp = recaptcha_check_answer ($privatekey,
									$_SERVER["REMOTE_ADDR"],
									$captcha,
									$captcha_entered);
	
	if (!$resp->is_valid) {
		return false;
	} else {
		return true;
	}
}
//Sendet E-Mails von $sender mit dem Betreff $subject und Inhalt $content an $recipient
function send_email($sender, $subject, $content, $mailer = '', $recipient = 'info@placelet.de') {
	$mail_sender  = clean_input($sender);
	$mail_sender  = filter_var($mail_sender, FILTER_SANITIZE_EMAIL);
	$mail_subject = clean_input($subject);
	//$mail_content = clean_input($content); - Keine Sonderzeichen :'(
	$mail_content = $content;
	
	if(check_email_address($mail_sender)) {
		if($mailer == 'contact') {
			$betreff = 'Placelet - Danke für Ihre Anfrage';
			switch($subject) {
				case 'support':
					$mail_subject = 'Unsere Website';
					$mail_recipient = 'support@placelet.de';
					break;
				case 'misc':
					$mail_subject = 'Anderes';
					$mail_recipient = 'info@placelet.de';
					break;
				case 'info':
					$mail_subject = 'Unser Produkt';
					$mail_recipient = 'info@placelet.de';
					break;
				default:
					$mail_subject = 'Kein Betreff ausgewählt';
					$mail_recipient = 'support@placelet.de';
			}
		}
		$header = 'From:' . $sender . "\n";
		$header .= "MIME-Version: 1.0" . "\n";
		$header .= "Content-type: text/plain; charset=utf-8" . "\n";
		$header .= "Content-transfer-encoding: 8bit";
		mail($mail_recipient, $mail_subject, $mail_content, $header);
		//Bestätigung an den Sender
			$mail_header = "From: Placelet <info@placelet.de>\n";
			$mail_header .= "MIME-Version: 1.0" . "\n";
			$mail_header .= "Content-type: text/html; charset=utf-8" . "\n";
			$mail_header .= "Content-transfer-encoding: 8bit";
			
			$datei = 'text/kontakt_bestaetigung.txt';
			$fp = fopen($datei, 'r');
			$inhalt = fread($fp, filesize($datei));
			fclose($fp);
			mail($mail_sender, $betreff, $inhalt, $mail_header);
		return true;
	}else {
		return false;				
	}
}
//Benutzereingaben von ungewünschten Zeichen säubern
function clean_input($input) {
	if(!empty($input)) {
		//Umlaute und Sonderzeichen in HTML-Schreibweise umwandeln
		$input = htmlentities($input);
		//Überflüssige Leerzeichen entfernen
		$input = trim($input);
		return $input;
	}
}
//Erstellt ein Thumbnail vom Bild
//$target ist der Pfad vom Ausgangsbild; $thumb der unter dem das Thumbnail gespeichert wird; $w die maximale Breite des Thumbnails; $h die maximale Höhe des Thumbnails; $ext die Endung des Bildes
function create_thumbnail($target, $thumb, $w, $h, $ext) {
	list($w_orig, $h_orig) = getimagesize($target);
	$scale_ratio = $w_orig/$h_orig;
	if(($w / $h) > $scale_ratio) {
		$w = $h * $scale_ratio;
	} else {
		$h = $w / $scale_ratio;
	}
	$ext = strtolower($ext);
	switch($ext) {
		case 'jpeg';
		case 'jpg';
			$img = imagecreatefromjpeg($target);
			break;
		case 'gif';
			$img = imagecreatefromgif($target);
			break;
		case 'png';
			$img = imagecreatefrompng($target);
			break;
		default:
			echo 'Fehler';
			return false;
	}
	$tci = imagecreatetruecolor($w, $h);
	imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
	imagejpeg($tci, $thumb, 80);	
}

//Überprüft, ob der Wert leer, bzw. auch getrimmt leer ist
function tisset($a){
	$t = trim($a);
	if(!empty($t)){
		return true;
	}
	return false;
}

//Email überprüfen - unterstützt keine arabischen emailadressen :'(
function check_email_address($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	return false;
}

//Longitude und Latitude Daten erhalten
function getlnlt($name = false){
	global $db;
	$return = array();
	if($name === false){
		$query = 'SELECT brid, city, longitude, latitude  FROM pictures';
		$stmt = $db->query($query);
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$return[] = $row;
		}
	}
	else
	{
		$query = 'SELECT brid FROM bracelets WHERE name = :name';
		$stmt = $db->prepare($query);
		$stmt->execute(array('name' => $name));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
		$query = 'SELECT brid, city, longitude, latitude FROM pictures WHERE brid = :brid';
		$stmt = $db->prepare($query);
		$stmt->execute(array('brid' => $row['brid']));
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$return[] = $row;
		}
	}
	return $return;
}

//Start - Hash Klasse um Passwörter zu verschlüsseln und zu überprüfen
//Zum verschlüsseln eines Passworts "$pass_hash = PassHash::hash($_POST['password']);" verwenden
//Zum überprüfen eines Passworts "if (PassHash::check_password($user['pass_hash'], $_POST['password'])) { eingeloggt } else { falsches Passwort }"
//	$user['pass_hash'] bezeichnet das gespeicherte verschlüsseltes Passwort, welches in der Datenbank liegt
class PassHash {  
	//http://net.tutsplus.com/tutorials/php/understanding-hash-functions-and-keeping-passwords-safe/
  
    // blowfish  
    private static $algo = '$2a';
	
    // cost parameter  
    private static $cost = '$10';  
    // mainly for internal use  
    public static function unique_salt() {  
        return substr(sha1(mt_rand()),0,22);  
    }  
    // this will be used to generate a hash  
    public static function hash($password) {  
  
        return crypt($password,  
                    self::$algo .  
                    self::$cost .  
                    '$' . self::unique_salt());  
    }  
    // this will be used to compare a password against a hash  
    public static function check_password($hash, $password) {  
  
        $full_salt = substr($hash, 0, 29);  
  
        $new_hash = crypt($password, $full_salt);  
  
        return ($hash == $new_hash);  
    }  
}  


function getBrowserLanguage($arrAllowedLanguages, $strDefaultLanguage, $strLangVariable = null, $boolStrictMode = true) {//code by:http://burian.appfield.net/entwicklung/php-mysql/php-browsersprache-fur-mehrsprachige-anwendungen-ermitteln.htm
    if (!is_array($arrAllowedLanguages)) {
        if (strpos($arrAllowedLanguages,';')) {
            $array = explode(';',$arrAllowedLanguages);
            $arrAllowedLanguages = $array;
        }
    }
    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        return $arrAllowedLanguages[0];
    }
    if ($strLangVariable === null) $strLangVariable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if (empty($strLangVariable)) return $strDefaultLanguage;
    $arrAcceptedLanguages = preg_split('/,\s*/', $strLangVariable);
    $strCurrentLanguage = $strDefaultLanguage;
    $intCurrentQ = 0;
    foreach ($arrAcceptedLanguages as $arrAcceptedLanguage) {
        $boolResult = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)'.
                        '(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $arrAcceptedLanguage, $arrMatches);
        if (!$boolResult) continue;
        $arrLangCode = explode ('-', $arrMatches[1]);
        if (isset($arrMatches[2]))
            $intLangQuality = (float)$arrMatches[2];
        else
            $intLangQuality = 1.0;
        while (count ($arrLangCode)) {
            if (!is_array($arrAllowedLanguages)) $arrAllowedLanguages = array($arrAllowedLanguages);
            if (in_array (strtolower (join ('-', $arrLangCode)), $arrAllowedLanguages)) {
                if ($intLangQuality > $intCurrentQ) {
                    $strCurrentLanguage = strtolower (join ('-', $arrLangCode));
                    $intCurrentQ = $intLangQuality;
                    break;
                }
            }
            if ($boolStrictMode) break;
            array_pop ($arrLangCode);
        }
    }
    return $strCurrentLanguage;
}



//Start - der Funktion is_mobile, welche ermittelt, ob der Besucher ein mobiles Endgerät verwendet
function is_mobile($useragentmobile){
$is_t=0;
        $userAgent = strtolower($useragentmobile);  

if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
$is_t++;


    $mobileClients = array(  
        "midp",  
        "240x320",  
        "blackberry",  
        "netfront",  
        "nokia",  
        "panasonic",  
        "portalmmm",  
        "sharp",  
        "sie-",  
        "sonyericsson",  
        "symbian",  
        "windows ce",  
        "benq",  
        "mda",  
        "mot-",  
        "opera mini",  
        "philips",  
        "pocket pc",  
        "sagem",  
        "samsung",  
        "sda",  
        "sgh-",  
        "vodafone",  
        "xda",  
        "iphone",  
        "android",
//mehr Clients von http://erikastokes.com/php/how-to-test-if-a-browser-is-mobile.php
		"acer",
		"acoon",
		"acs-",
		"abacho",
		"ahong",
		"airness",
		"alcatel",
		"amoi",	
		"anywhereyougo.com",
		"applewebkit/525",
		"applewebkit/532",
		"asus",
		"audio",
		"au-mic",
		"avantogo",
		"becker",
		"bilbo",
		"bird",
		"blazer",
		"bleu",
		"cdm-",
		"compal",
		"coolpad",
		"danger",
		"dbtel",
		"dopod",
		"elaine",
		"eric",
		"etouch",
		"fly " ,
		"fly_",
		"fly-",
		"go.web",
		"goodaccess",
		"gradiente",
		"grundig",
		"haier",
		"hedy",
		"hitachi",
		"htc",
		"huawei",
		"hutchison",
		"inno",
		"ipad",
		"ipaq",
		"ipod",
		"jbrowser",
		"kddi",
		"kgt",
		"kwc",
		"lenovo",
		"lg ",
		"lg2",
		"lg3",
		"lg4",
		"lg5",
		"lg7",
		"lg8",
		"lg9",
		"lg-",
		"lge-",
		"lge9",
		"longcos",
		"maemo",
		"mercator",
		"meridian",
		"micromax",
		"mini",
		"mitsu",
		"mmm",
		"mmp",
		"mobi",
		"moto",
		"nec-",
		"newgen",
		"nexian",
		"nf-browser",
		"nintendo",
		"nitro",
		"nook",
		"novarra",
		"obigo",
		"palm",
		"pantech",
		"phone",
		"pg-",
		"playstation",
		"pocket",
		"pt-",
		"qc-",
		"qtek",
		"rover",
		"sama",
		"samu",
		"sanyo",
		"sch-",
		"scooter",
		"sec-",
		"sendo",
		"siemens",
		"softbank",
		"sony",
		"spice",
		"sprint",
		"spv",
		"tablet",
		"talkabout",
		"tcl-",
		"teleca",
		"telit",
		"tianyu",
		"tim-",
		"toshiba",
		"tsm",
		"up.browser",
		"utec",
		"utstar",
		"verykool",
		"virgin",
		"vk-",
		"voda",
		"voxtel",
		"vx",
		"wap",
		"wellco",
		"wig browser",
		"wii",
		"wireless",
		"xde",
		"zte"  
    );  
  
 
    /** 
     * Check if client is a mobile client 
     * 
     * @param string $userAgent 
     * @return boolean 
     */  
        foreach($mobileClients as $mobileClient) {  
            if (strstr($userAgent, $mobileClient)) {  
                $is_t++;  
            }  
        }  
        if($is_t == 0)
		return false;
		else
		return true;
 
}
?>
