			<article id="shop" class="mainarticles bottom_border_green">
				<div class="green_line mainarticleheaders line_header"><h1>Shop</h1></div>
				
				
				<?php $js.='var options = { $AutoPlay: true };
        var jssor_slider1 = new $JssorSlider$("slider1_container", options);'; ?>
				
				
				
				<div style="float: left; width: 50%;">
					<div id="slider1_container" style="position: relative; width: 400px; height: 600px;">
						<div u="slides" class="product_pic" style="cursor: move; overflow: hidden; width: 400px; height: 600px;">
							<div><img u="image" src="/pictures/shop/thumb-1.jpg" /></div>
							<!--<div><img u="image" src="/pictures/shop/thumb-2.jpg" /></div>-->
							<div><img u="image" src="/pictures/shop/thumb-3.jpg" /></div>
							<!--<div><img u="image" src="/pictures/shop/thumb-4.jpg" /></div>-->
							<!--<div><img u="image" src="/pictures/shop/thumb-5.jpg" /></div>-->
							<div><img u="image" src="/pictures/shop/thumb-6.jpg" /></div>
						</div>
					</div><br>
					<!--<a href="/pictures/product_pic2.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" src="/pictures/product_pic2_medium.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>-->
					<a href="/pictures/shop/2.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" style="width: 225px; height: 150px" src="/pictures/shop/thumb-2.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>
					
					<a href="/pictures/product_pic3.jpg" data-lightbox="pictures" title="<?php echo $lang->shop->unser_armband->$lng; ?>">
						<img class="product_pic" src="/pictures/thumb-product_pic3.jpg" alt="<?php echo $lang->shop->unser_armband->$lng; ?>">
					</a>
				</div>
					
				<div style="float: left; width: 48%; margin-left: 2%;">
					<h2><?php echo $lang->shop->reisearmband->$lng; ?></h2>
					<h3><?php echo $lang->shop->artikelinfo->$lng; ?></h3>
					<p class="shop_text"><?php echo $lang->shop->description->$lng; ?></p>			
					<h4><?php echo $lang->shop->size[$lng.'-title']; ?></h4>
					   <select size="1" name="os0" form="paypal">
                        	<option value="<?php echo $lang->shop->small->$lng; ?>"><?php echo $lang->shop->small->$lng; ?></option>
                        	<!--<option value="medium"><?php echo $lang->shop->medium->$lng; ?></option> -->
                        	<option value="<?php echo $lang->shop->big->$lng; ?>"><?php echo $lang->shop->big->$lng; ?></option>
                        </select><br>
					<?php echo $lang->shop->size->$lng; ?>
					<?php /*
					<hr>
					<h4><?php echo $lang->shop->color[$lng.'-title']; ?></h4>
					<?php echo $lang->shop->color->$lng; ?><br>
                    <?php echo $lang->shop->claspcolor->$lng; ?> 
					<!--<select size="1" name="os0" form="paypal">
						<option value="<?php echo $lang->shop->white->$lng; ?>"><?php echo $lang->shop->white->$lng; ?></option>
						<option value="<?php echo $lang->shop->green->$lng; ?>"><?php echo $lang->shop->green->$lng; ?></option>
						<option value="<?php echo $lang->shop->blue->$lng; ?>"><?php echo $lang->shop->blue->$lng; ?></option>
						<option value="<?php echo $lang->shop->red->$lng; ?>"><?php echo $lang->shop->red->$lng; ?></option>
						<option value="<?php echo $lang->shop->yellow->$lng; ?>"><?php echo $lang->shop->yellow->$lng; ?></option>
					</select><br>-->      */
					?>
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
							<input type="hidden" name="on0" value="Größe">
							<input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
							<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
						</form>

					<?php }else{ ?>
					<form action="https://www.paypal.com/cgi-bin/webscr" id="paypal" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="B4XEFT2AWQN2S">
						<input type="hidden" name="on0" value="size">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
					<?php } //$lang->shop->notavailable->$lng; ?>
				</div>
			</article>