		<article id="kontakt" class="mainarticles bottom_border_green">
			<div class="green_line mainarticleheaders line_header"><h1><?php echo $lang->kontakt[$lng.'-title']; ?></h1></div>
			<p><?php echo $lang->kontakt->text->$lng; ?></p>
			<form id="kontakt-form" method="post" action="/kontakt">
				<table>
					<tr>
						<td><label for="sender"><?php echo $lang->kontakt->youremail->$lng; ?>:</label><br></td>
						<td><input type="email" name="sender" id="sender" size="25" maxlength="100" placeholder="<?php echo $lang->form->email->$lng; ?>" required></td>
					</tr>
					<tr>
						<td><?php echo $lang->kontakt->betreff->$lng; ?>:</td>
						<td>
							<input type="radio" name="subject" value="support"> <?php echo $lang->kontakt->support->$lng; ?><br>
							<input type="radio" name="subject" value="info"> <?php echo $lang->kontakt->info->$lng; ?><br>
							<input type="radio" name="subject" value="misc"> <?php echo $lang->kontakt->misc->$lng; ?><br>
						</td>
					</tr>
					<tr>
						<td><label for="content"><?php echo $lang->kontakt->nachricht->$lng; ?>:</label></td>
						<td><textarea name="content" id="content" cols="60" rows="10" required></textarea></td>
					</tr>
				</table>
						<input type="hidden" name="mailer" value="contact">
				<input type="submit" name="submit" value="<?php echo $lang->form->send->$lng; ?>">
			</form>
		</article>