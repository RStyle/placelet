<?php
//Funktionen - kann gut aufgeteilt werden - demnächst füge ich noch zukünfitg in Gebrauch werdende Funktionen hinzu

//Start der Überprüfung ob die hochgeladene Datei überhaupt ein Bild ist und nicht zu groß ist
function check_image(){

}


//Start - Hash Klasse um Passwörter zu verschlüsseln und zu überprüfen
//Zum verschlüsseln eines Passworts "$pass_hash = PassHash::hash($_POST['password']);" verwenden
//Zum überprüfen eines Passworts "if (PassHash::check_password($user['pass_hash'], $_POST['password']) { eingeloggt } else { falsches Passwort }"
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




//Start - der Funktion is_mobile, welche ermittelt, ob der Besucher ein Smartphone und eventuell Tablet (muss noch getestet werden) verwendet
function is_mobile($useragentmobile){
$is_t=0;
        $userAgent = strtolower($useragentmobile);  

if(preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4)))
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
        "android"  
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
