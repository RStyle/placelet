			<article id="shop" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Shop</h1></div>
				<div style="float: left; width: 50%;">
					<a href="pictures/product_pic2.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" src="pictures/product_pic2_medium.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>
					<a href="pictures/product_pic1.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" src="pictures/thumb-product_pic1.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>
					
					<a href="pictures/product_pic3.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" src="pictures/thumb-product_pic3.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>
				</div>
					
				<div style="float: left; width: 48%; margin-left: 2%;">
					<h2><?php echo $lang->shop->reisearmband->$lng; ?></h2>
					<h3><?php echo $lang->shop->artikelinfo->$lng; ?></h3>
					<p class="shop_text"><?php echo $lang->shop->description->$lng; ?></p>			
					<h4><?php echo $lang->shop->size[$lng.'-title']; ?></h4>
					   <select size="1" name="size">
                        	<option value="small"><?php echo $lang->shop->small->$lng; ?></option>
                        	<!--<option value="medium"><?php echo $lang->shop->medium->$lng; ?></option> -->
                        	<option value="big"><?php echo $lang->shop->big->$lng; ?></option>
                        </select><br>
					<?php echo $lang->shop->size->$lng; ?>
					<hr>
					<h4><?php echo $lang->shop->color[$lng.'-title']; ?></h4>
					<?php echo $lang->shop->color->$lng; ?><br>
                    <?php echo $lang->shop->claspcolor->$lng; ?> 
					<select size="1" name="os0" form="paypal">
						<option value="<?php echo $lang->shop->white->$lng; ?>"><?php echo $lang->shop->white->$lng; ?></option>
						<option value="<?php echo $lang->shop->green->$lng; ?>"><?php echo $lang->shop->green->$lng; ?></option>
						<option value="<?php echo $lang->shop->blue->$lng; ?>"><?php echo $lang->shop->blue->$lng; ?></option>
						<option value="<?php echo $lang->shop->red->$lng; ?>"><?php echo $lang->shop->red->$lng; ?></option>
						<option value="<?php echo $lang->shop->yellow->$lng; ?>"><?php echo $lang->shop->yellow->$lng; ?></option>
					</select><br>             					
					<hr>
					<h4><?php echo $lang->shop->material[$lng.'-title']; ?></h4>
					<?php echo $lang->shop->material->$lng; ?>
					<hr>
					<h4><?php echo $lang->shop->gender[$lng.'-title']; ?></h4>
					<?php echo $lang->shop->gender->$lng; ?><br><br>
					<?php if ($lng == 'de'){ ?>
					<form action="https://www.paypal.com/cgi-bin/webscr" id="paypal" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="AWD5N67HHG896">
						<input type="hidden" name="on0" value="Verschlussfarbe" form="paypal">
						<!--<table>
						<tr><td><input type="hidden" name="on0" value="Verschlussfarbe">Verschlussfarbe</td></tr><tr><td><select name="os0">
							<option value="Weiß">Weiß </option>
							<option value="Grün">Grün </option>
							<option value="Blau">Blau </option>
							<option value="Rot">Rot </option>
							<option value="Gelb">Gelb </option>
						</select> </td></tr>
						</table>-->
						<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
						<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>

					<?php }else{ ?>
					<form action="https://www.paypal.com/cgi-bin/webscr" id="paypal" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="NQNXQZKL5GNYN">
						<input type="hidden" name="on0" value="clasp color">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
					<?php } //$lang->shop->notavailable->$lng; ?>
				</div>
			</article>