<?php
if(isset($_POST['submit'])) {
		$send_email = send_email($_POST['sender'], $_POST['subject'], $_POST['content'], $_POST['mailer']);
}
if(isset($_GET['captcha'])) {
	if($_GET['captcha'] == 'false') {
			$send_email = 'Das Captcha wurde falsch eingegeben.';	
	}
}
if (isset($send_email)) {
	echo '<script type="text/javascript">
			//$(document).ready(function(){
				alert("'.$send_email.'");
			//});
		  </script>';
}
?>
		<article id="kontakt" class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h1>Kontakt</h1></div>
			<p>Dieses Formular können Sie benutzen, wenn sie Fragen zu unseren Produkten oder Anliegen bezüglich unserer Webseite haben.<br>
			Wir bemühen uns, Ihnen schnellstmöglich eine Antwort per E-Mail zukommen zu lassen.</p>
			<form style="padding-left: 20px; border-left: 4px #BEBEBE solid;" method="post" action="<?php echo $friendly_self;?>">
				<label for="sender">Ihre E-Mail Adresse:</label>
				<input type="text" name="sender" id="sender" size="25" placeholder="E-Mail Adresse">
				<p>Bitte geben sie den Betreff Ihrer Nachricht an:</p>
				<p>
					<input type="radio" name="subject" value="support"> Unsere Webseite<br>
					<input type="radio" name="subject" value="info"> Unser Produkt<br>
					<input type="radio" name="subject" value="misc"> Anderes<br>
				</p>
				<label for="content">Ihre Nachricht:</label><br>
				<textarea name="content" id="content" cols="120" rows="10"></textarea><br><br>           
				<input type="hidden" name="mailer" value="contact">
				<input type="submit" name="submit" value="Abschicken">
			</form>
		</article>