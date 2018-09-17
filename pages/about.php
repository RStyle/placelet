			<article id="about-us" class="mainarticles bottom_border_blue" style="width: 100%">
				<div class="blue_line mainarticleheaders line_header"><h1><?php echo $lang->about[$lng.'-title']; ?></h1></div>
								
				<div id="wir">
    				<a href="/pictures/mitarbeiterklein.JPG" data-lightbox="wir"><img src="/cache.php?f=/pictures/thumb-mitarbeiterklein.JPG" alt="Mitarbeiter von Placelet"></a>
    				<div id="text_box"><?php echo $lang->about->imgtitle->$lng; ?></div>
				</div>
                <br>
				
				<!--<div id="partner_box">
                    <h1><?php echo $lang->about->partner->$lng; ?></h1>
                    <p><?php echo $lang->about->graffelob->$lng; ?></p>
                    <a id="graffe_link" href="https://mst-graffe.de/" target="_blank">296
                        <img src="https://mst-graffe.de/index_htm_files/670.jpg" alt="mstgraffe_logo">
                        <p class="partner_name">Maschinen- und Stahlbau-Technik Markus Graffe GMBH</p>
                    </a>
                </div>-->
                <div id="about_text">
    				<h1 style="clear: both; margin-top: 0;">Global Bracelet. Travel&amp;Connect.</h1>
    				<p style="margin-bottom: 0;">
    					<?php echo $lang->about->about_us->$lng; ?>
    				</p>
				</div>
				
				<!--<ul id='timeline'>
<?php
$timeline['title'] = array(1 => 'Erstes Treffen', 'Ideenfindungsworkshop', 'Gründung',           'Veröffentlichung der Website', 'Fortbildung');
$timeline['date']  = array(1 => '4. Juli 2013',   '24. August 2013',       '13. September 2013', '22. September',                 'Oktober 2013');
$timeline['description'] = array(1 => 'test', 'test', 'test', 'test', 'test', 'test');
for($i = 1; $i <= count($timeline['title']); $i++) {
?>
					<li class='work'>
						<input class='radio' id='work<?php echo $i; ?>' name='works' type='radio'>
						<div class="relative">
							<label for='work<?php echo $i; ?>'><?php echo $timeline['title'][$i]?></label>
							<span class='date'><?php echo $timeline['date'][$i]?></span>
							<span class='circle'></span>
						</div>
						<div class='content'>
							<p>
								<?php echo $timeline['description'][$i]?>
							</p>
						</div>
					</li>
<?php
}
?>
				</ul>-->

				
				<div class="tree">
					<ul>
						<li>
							<span><?php echo $lang->about->vorstandsvorsitz->$lng; ?>: Sarah Baiker & Janik Rennollet</span>
							<ul>
								<li>
									<span><?php echo $lang->about->verwaltung->$lng; ?>:<br>Patrick Piroth</span>
									<ul class="tree_second">
										<li>
											<span>Eloisa Marzell</span>
										</li>
									</ul>
								</li>
								<li>
									<span><?php echo $lang->about->finanzen->$lng; ?>:<br>Sebastian Gänz</span>
									<ul class="tree_second">
										<li>
											<span>Moritz Junkermann</span>
										</li>
									</ul>
								</li>
								<li>
									<span><?php echo $lang->about->marketing->$lng; ?>:<br>Celine Müller-Späth</span>
									<ul class="tree_second">
										<li>
											<span>Alicia Braun</span>
											<ul class="tree_3">
												<li>
													<span>Kai Zurmöhle</span>
													<ul class="tree_3">
														<li>
															<span>Justus Renger</span>
															<ul class="tree_3">
																<li>
																	<span>Lea Reinke</span>
																	<ul class="tree_3">
																		<li>
																			<span>Collin Meffert</span>
																		</li>
																	</ul>
																</li>
															</ul>
														</li>
													</ul>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<span><?php echo $lang->about->produktion->$lng; ?>:<br>Maximilian Klapdar</span>
									<ul class="tree_second">
										<li>
											<span>Edda Strohm</span>
											<ul class="tree_second">
												<li>
													<span>Johanna Dippel</span>
													<ul class="tree_second">
														<li>
															<span>Anna-Lena Bretscher</span>
															<ul class="tree_second">
																<li>
																	<span>Silvia Orth</span>
																</li>
															</ul>
														</li>
													</ul>
												</li>
											</ul>
										</li>
									</ul>
								</li>
								<li>
									<span><?php echo $lang->about->website->$lng; ?>:<br>Roman Savrasov</span>
									<ul class="tree_second">
										<li>
											<span>Daniel Schäfer</span>
											<ul class="tree_second">
												<li>
													<span>Julian Zimmerlin</span>
												</li>
											</ul>
										</li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</div>
				<div id="partner">
					<h1><?php echo $lang->about->supported->$lng; ?></h1>

    				<div style="float: right; width: 1000px">					
    					<img src="/cache.php?f=/img/aureliaLogo.png" alt="Aurelia - Goldschmiede" style="float: right; height: 80px; width: 159px; margin-right: 60px;">
    					<img src="/cache.php?f=/img/laserkreativLogo.png" alt="Laserkreativ - Lasergravur | Laserschneiden | Digitaldruch" style="float: right; height: 80px; width: 160; margin-right: 40px;">
    					<img src="/cache.php?f=/img/oddLogo.png" alt="odd - print und medien" style="float: right; height: 80px; width: 176; margin-right: 40px;">
    					<img src="/cache.php?f=/img/uranoLogo.jpg" alt="Urano - IT-Systemhaus" style="float: right; height: 80px; width: 160; margin-right: 40px;">    				
    				</div>

    				<br>
				    <div style="float: right; width: 900px; text-align: right; font-size:10pt">
				        <a target="_blank" style="float: right; margin-right: 75px;" href="https://www.goldschmiede-aurelia.de/">Goldschmiede Aurelia</a>	    
    					<a target="_blank" style="float: right; margin-right: 100px;" href="https://laserkreativ.de/">Laser Kreativ</a>
    					<a target="_blank" style="float: right; margin-right: 100px;" href="https://odd.de/">odd Print & Medien</a>
    					<a target="_blank" style="float: right; margin-right: 140px;" href="https://urano.de/">Urano IT-Systeme</a>
    				</div>
				</div>
		
			</article>
			<!--<aside class="side_container">
				<h1>JUNIOR</h1>
				<p>JUNIOR ist ein Projekt der Institut der deutschen Wirtschaft Köln JUNIOR gGmbH. JUNIOR wird 
				auf Bundesebene durch das Bundesministerium für Wirtschaft und Technologie, die KfW Mittelstandsbank, Gesamtmetall, dem Handelsblatt, Danfoss, Deloitte, der AXA Versicherung und Fed 
				Ex gefördert.</p>
			</aside>-->