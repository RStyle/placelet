		<article id="kontakt" class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h1><?php echo $pagename[$page]; ?></h1></div>
			<p>
				Dieses Formular können Sie benutzen, wenn sie Fragen zu unseren Produkten oder Anliegen bezüglich unserer Webseite haben.<br>
				Wir bemühen uns, Ihnen schnellstmöglich eine Antwort per E-Mail zukommen zu lassen.
			</p>
			<form style="padding-left: 20px; border-left: 4px #BEBEBE solid;" method="post" action="kontakt">
				<table>
					<tr>
						<td><label for="sender">Ihre E-Mail Adresse:</label><br></td>
						<td><input type="email" name="sender" id="sender" size="25" placeholder="E-Mail Adresse" required></td>
					</tr>
					<tr>
						<td>Betreff:</td>
						<td>
							<input type="radio" name="subject" value="support"> Unsere Webseite<br>
							<input type="radio" name="subject" value="info"> Unser Produkt<br>
							<input type="radio" name="subject" value="misc"> Anderes<br>
						<td>
					</tr>
					<tr>
						<td><label for="content">Ihre Nachricht:</label></td>
						<td><textarea name="content" id="content" cols="60" rows="10" required></textarea></td>
					</tr>
				</table>
						<input type="hidden" name="mailer" value="contact">
				<input type="submit" name="submit" value="Abschicken">
			</form>
		</article>