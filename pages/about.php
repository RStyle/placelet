			<article id="about-us" class="mainarticles bottom_border_blue" style="width: 100%">
				<div class="blue_line mainarticleheaders line_header"><h1><?php echo $lang->about[$lng.'-title']; ?></h1></div>
				<a href="pictures/staff.jpg" data-lightbox="wir"><img src="pictures/thumb-staff.jpg" alt="Mitarbeiter von Placelet" id="wir" style="width: 100%; margin-bottom: 20px;"></a><br><br>
				
				<div style="float:left; font-size:3em; margin-top:12.5px">
					Unterstützt von:
				</div>
				<div style="float: right; width: 800px">
					<img src="img/oddLogo.png" alt="odd - print und medien" style="height: 80px; width: 200px; margin-left: 2.5%; margin-right: 2.5%;">
					<img src="img/laserkreativLogo.png" alt="Laserkreativ - Lasergravur | Laserschneiden | Digitaldruch" style="height: 80px; width: 200px; margin-left: 2.5%; margin-right: 2.5%;">
					<img src="img/aureliaLogo.png" alt="odd - print und medien" style="height: 80px; width: 200px; margin-left: 2.5%; margin-right: 2.5%;">
				</div>
				<br>
				<div style="float: right; width: 800px; text-align: right; font-size:0.75em">
					<a href="http://odd.de/">http://odd.de/</a>
					<a href="http://laserkreativ.de/" style="target: tab"><span style="margin-left:140px">http://laserkreativ.de/</span></a>
					<a href="http://www.goldschmiede-aurelia.de/" style="target: tab"><span style="margin-left:80px;margin-right:90px">http://www.goldschmiede-aurelia.de/</span></a>
				</div>
				
				<!--<div id="partner_box">
                    <h1><?php echo $lang->about->partner->$lng; ?></h1>
                    <p><?php echo $lang->about->graffelob->$lng; ?></p>
                    <a id="graffe_link" href="http://mst-graffe.de/" target="_blank">296
                        <img src="http://mst-graffe.de/index_htm_files/670.jpg" alt="mstgraffe_logo">
                        <p class="partner_name">Maschinen- und Stahlbau-Technik Markus Graffe GMBH</p>
                    </a>
                </div>-->
				<h1 style="clear: both; padding-top: 1em;">Global Bracelet. Travel&amp;Connect</h1>
				<p>
					<?php echo $lang->about->about_us->$lng; ?>
				</p>
				
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
			</article>
			<!--<aside class="side_container">
				<h1>JUNIOR</h1>
				<p>JUNIOR ist ein Projekt der Institut der deutschen Wirtschaft Köln JUNIOR gGmbH. JUNIOR wird 
				auf Bundesebene durch das Bundesministerium für Wirtschaft und Technologie, die KfW Mittelstandsbank, Gesamtmetall, dem Handelsblatt, Danfoss, Deloitte, der AXA Versicherung und Fed 
				Ex gefördert.</p>
			</aside>-->