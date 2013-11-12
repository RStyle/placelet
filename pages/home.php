<?php
if(isset($_GET['regstatuschange']) && isset($_GET['regstatuschange_user'])){
	$regstatus_change = $user->regstatuschange($_GET['regstatuschange'], $_GET['regstatuschange_user']);
	if($regstatus_change) {
		$js .= 'alert("Deine E-Mail wurde erfolgreich bestätigt.");';
	}elseif(!$regstatus_change) {
		$js .= 'alert("Die Bestätigung deiner Email ist gescheitert.");';
	}
}
$systemStats = $statistics->systemStats(0, 1);
//hier werden die Armbänder bestimmt, die angezeigt werden
$bracelets_displayed = $systemStats['recent_brids'];
foreach($bracelets_displayed as $key => $val) {
	$stats[$key] = array_merge($statistics->bracelet_stats($val), $statistics->picture_details($val));
}
?>
<!--HINWEIS-->
            <div class="hint" id="hint1">
				<h1>Hinweis:</h1>
				<p>
					Sie befinden sich hier auf der Webseite des JUNIOR-Unternehmens "Placelet". Bitte beachten Sie, dass sich diese Webseite noch im Aufbau befindet und daher einige unvollst&auml;ndige Inhalte wie zum Beispiel Platzhalter-Texte enthalten k&ouml;nnte. Wir arbeiten an einer z&uuml;gigen Vervollst&auml;ndigung dieser Webseite und bitten um Ihr Verst&auml;ndnis.
				</p> 
				<span class="pseudo_link toggle_comments" onclick="document.getElementById('hint1').style.display='none';">Hinweis ausblenden</span>  
			</div>
<!--CONNECT-LEISTE-->
            <div id="connect_leiste">
                <div class="connect_box" id="uploads_box">
                    <h1>neuester upload</h1>
					<div id="newest_pic">
						<div class="pseudo_link float_right" onClick="return false" onMouseDown="javascript:change_pic('+', '1');">&gt;</div>
						<a href="pictures/bracelets/pic<?php echo '-'.$bracelets_displayed[1].'-'.$stats[1][0]['picid'].'.'.$stats[1][0]['fileext']; ?>" data-lightbox="pictures" title="<?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?>" class="thumb_link">
							<img src="img/triangle.png" alt="" class="thumb_triangle">
							<img src="pictures/bracelets/thumb<?php echo '-'.$bracelets_displayed[1].'-'.$stats[1][0]['picid'].'.jpg'; ?>" alt="<?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?>" class="thumbnail">
						</a>
						<div>
							<table class="pic-info">
								<tr>
									<th>Armband</th>
									<td><strong><?php echo '<a href="armband?name='.urlencode($statistics->brid2name($bracelets_displayed[1])).'">'.$statistics->brid2name($bracelets_displayed[1]).'</a>'; ?></strong></td>
								</tr>
								<tr>
									<th>Datum</th>
									<td><?php echo date('d.m.Y H:i', $stats[1][0]['date']); ?> Uhr</td>
								</tr>
								<tr>
									<th>Ort</th>
									<td><?php echo $stats[1][0]['city'].', '.$stats[1][0]['country']; ?></td>
								</tr>
	<?php
				if($stats[1][0]['user'] != NULL) {
	?>
								<tr>
									<th>Uploader</th>
									<td><?php echo $stats[1][0]['user']; ?></td>
								</tr>
	<?php
				 }
	?>
							</table> 
							<p class="pic-desc">
								<span class="desc-header"><?php echo $stats[1][0]['title']; ?></span><br>
								<?php echo $stats[1][0]['description']; ?>
							</p>
						</div>
						<img src="img/loading.gif" id="loading" alt="loading..." style="display: block; margin: 0 auto; display: none; right: 25.5%;">
					</div>
                </div>
                <div class="connect_box" id="submit_box">
                    <h1>+1 bild</h1>
                    <p>Gib deine <span>Armband-ID</span> an:
                    <form action="login" method="get">
                        <input name="postpic" type="text" maxlength="6" size="6" placeholder="ID...">
						<input type="submit" value="Zum Upload">
                    </form>
                    </p>
                    <hr>
                    
                    <h1>neues armband</h1>
                    <p>Gib deine <span>Armband-ID</span> an:
                    <form action="login" method="get">
                        <input name="registerbr" type="text" maxlength="6" size="6" placeholder="ID...">
						<input type="submit" value="Armband registrieren">
                    </form> 
                </div>
                <div class="connect_box" id="facebook_box">
                    <h1>facebook</h1>
                    <div class="fb-like-box" data-href="http://www.facebook.com/Placelet" data-width="200" data-height="190" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="true"></div>
                </div>
            </div>

<!--ERSTER ARTIKEL-->
			<article id="reisearmband" class="mainarticles bottom_border_blue">
				<div class="blue_line mainarticleheaders line_header"><h1>Das Reisearmband</h1></div>
				<img id="reisearmband_img" alt="kein Bild von uns verfügbar" src="http://1.bp.blogspot.com/-zjwIgMkzOjY/UY0Go8XBceI/AAAAAAAAFH0/rC73lAYTAUg/s1600/armband+ganz+leicht+selber+machen.jpg">
				<p>
                    Möchtest du auch ein Reisearmband besitzen, es weiterverschenken und dich somit vielen anderen netten Usern unserer Community anzuschließen?<br>
                    Kauf' ein Armband und werde ein Teil davon! Unser Armband ist etwas ganz persönliches, womit man vielen Menschen auf der Welt eine Freude bereiten kann.<br>
                    Mithilfe eines Namens, den du selbst für dein Armband festlegst, kann jeder, dem du den Namen mitteilst, dein Armband mithilfe unserer Suchleiste finden und seinen Weg nachverfolgen.<br><br>
                    <b>Hast du Lust, bei unserem sozialen Experiment mitzumachen?</b> In unserem Shop geht es weiter.
                     
				</p>
			</article>                                                                                                        
<!--ZWEITER ARTIKEL-->
			<article id="kollektion" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Wie funktioniert's?</h1></div>
				<img id="kollektion_img" alt="kein Bild von uns verfügbar" src="http://img.geo.de/div/image/61566/01-armbaender.jpg">
				<p>
					<span class="highlighted kollektion_numbers">1</span> Kaufe Dir ein Armband in unserem Shop. Du erhältst eine einmalige Armband-ID, die du auf der Rückseite des Metallplättchens am Armband findest. Damit kannst du dein Armband in der grünen Box auf unserer Startseite registrieren.
				</p>
				<span class="arrow highlighted">&#11015;</span>
				<p>
					<span class="highlighted kollektion_numbers">2</span> Trage das Armband solange du möchtest. Wenn du Lust hast, lade ein Foto mit Dir und Deinem Armband (zum Beispiel vor einer Sehenswürdigkeit) in der grünen Box auf unserer Webseite hoch. Achte darauf, dass man auf dem Bild die ID des Armbandes nicht sehen kann, da sie wie ein Passwort funktioniert.
				</p>
				<span class="arrow highlighted">&#11015;</span>
				<p>
					<span class="highlighted kollektion_numbers">3</span> Gib dein Armband an einen Freund, Bekannten oder auch zufällig getroffenen Menschen deiner Wahl weiter, wobei du ihm das Grundprinzip des Reisearmbandes erklärst.
				</p>
				<span class="arrow highlighted">&#11015;</span>
				<p>
					<span class="highlighted kollektion_numbers">4</span>  Der nächste Armbandträger kann dann ebenfalls mit der ID ein Bild hochladen und das Armband weitergeben. Hierzu ist keine Registrierung auf unserer Webseite erforderlich.
				</p>
				<span class="arrow highlighted">&#11015;</span>
				<p>
					<span class="highlighted kollektion_numbers">5</span>  Verfolge den Weg deines Armbandes um die Welt und kommentiere nach Lust und Laune die Bilder zu deinem oder auch anderen Armbändern.
				</p>
			</article>
<!--SIDEBAR-->
			<aside class="side_container">
				<h1>JUNIOR</h1>
				<p>Das Unternehmen Placelet entstand durch das Projekt JUNIOR der Institut der deutschen Wirtschaft Köln JUNIOR gGmbH. JUNIOR wird 
				auf Bundesebene durch das Bundesministerium für Wirtschaft und Technologie, die KfW Mittelstandsbank, Gesamtmetall, dem Handelsblatt, Danfoss, Deloitte, der AXA Versicherung und Fed 
				Ex gefördert.</p>
			</aside>