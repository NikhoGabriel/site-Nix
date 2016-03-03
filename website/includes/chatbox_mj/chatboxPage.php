<?php function chatboxPage ()
{
	global $db, $_SESSION;

	//Accès à la chatbox
	define('rank_speak', 5);

	if ($_SESSION['rank'] >= rank_speak)
	{	
	?>
		<h3>Chatbox</h3>
		<section id="chatbox">
			Activez Javascript pour accéder à la chatbox, ou arrêtez d'utiliser Internet Explorer, essayez plutôt <a href="https://www.mozilla.org/fr/firefox/desktop/">Firefox</a>, <a href="https://www.google.fr/chrome/browser/desktop/">Google Chrome</a> ou <a href="http://www.opera.com/fr">Opera</a>.
		</section>

		<form>
			<table>
				<tr>
					<td class="chat_input">
						<label for="msg">Message :</label>
					</td>
					<td class="chat_input">
						<input type="text" id="msg" maxlength="255" /><span id="rmMsg" class="rmBt"/>[x]</span>
					</td>
					<td class="chat_input">
						<input type="button" id="send" value="Envoyer" />
					</td>
				</tr>
				<tr>
					<td class="chat_input">
						<label for="to">Chuchoter à :</label>
					</td>
					<td class="chat_input">
						<input type="text" id="to" maxlength="255" /><span id="rmTo" class="rmBt"/>[x]</span>
					</td>
				</tr>
				<tr>
					<td class="chat_input">
						<label for="salon">Salon :</label>
					</td>
					<td class="chat_input">
						<input type="text" id="salon" maxlength="255" /><span id="rmSalon" class="rmBt">[x]</span>
					</td>
					<td class="chat_input">
						<input type="button" id="joinSalon" value="Rejoindre" />
					</td>
				</tr>
			</table>
		</form>

		<script src="includes/chatbox_mj/chatboxReload.js"></script>
	<?php
	}
	else
	{	?>
		<h3>Dialogue en direct du Staff</h3>

		
		<?php
		$answer = $db->query('SELECT COUNT(*) AS number FROM chatbox WHERE to_id = 0 AND salon = ""');
		$line = $answer->fetch();
		$answer->closeCursor();

		$numMax = $line['number'];
		$numMin = ($numMax >= 30) ? ($numMax - 30) : 0;
		$answer = $db->query("SELECT c.post_date date, c.message msg, m.name name, m.id m_id
				      FROM chatbox_mj c
				      INNER JOIN members m ON m.id = c.user_id
				      WHERE to_id = 0 AND salon = ''
				      ORDER BY post_date
				      LIMIT $numMin, $numMax");

		while ($line = $answer->fetch())
		{
						//Tag vers Joueur
			if (preg_match("#@".$_SESSION['name']."#i", $line['msg']))
			{$tag = "<span class='tag'>"; $tagend = "</span>";}
			else { $tag = ''; $tagend = '';}
			
			//Images codée
			if (preg_match("#^(lol7){1}$#", $line['msg'])) { $img = "<br /><img src='pics/cb/"; $imgend = ".png' alt='".$line['msg']."' width='200' />";}
			elseif (preg_match("#^(fp7){1}$#", $line['msg'])) { $img = "<br /><img src='pics/cb/"; $imgend = ".png' alt='".$line['msg']."' width='300' />";}
			elseif (preg_match("#^(tf7){1}$#", $line['msg'])) { $img = "<br /><img src='pics/cb/"; $imgend = ".png' alt='".$line['msg']."' width='400' />";}
			elseif (preg_match("#^(wat7){1}$#", $line['msg'])) { $img = "<br /><img src='pics/cb/"; $imgend = ".png' alt='".$line['msg']."' width='400' />";} else { $img = ''; $imgend = '';}

			
			if ($line['pionier'] == 1) { $pionier = "-P";} else { $pionier = '';} if ($line['technician'] == 1) { $tech = "-T";} else { $tech = '';}
			$filename = 'pics/avatar/miniskin_' .$line['m_id']. '.png';if (file_exists($filename)) {$skin = $line['m_id'];} else {$skin = 'no';}
			
			//Date
			$date = preg_replace('#^.{11}(.{2}):(.{2}):.{2}$#', '$1:$2', $line['date']);

			if ($_SESSION['name'] == "Eftarthadeth" OR $_SESSION['name'] == "Nikho")
				
			{ ?>
			<form method="POST" action="index.php?p=chatbox">
				<p class="chat_msg <?=$whisp?>">
				<input type="button" value="[x]" class="del_button_cb" /> <?= $date?> : <img src="pics/avatar/miniskin_<? echo $skin?>.png" width="17px" /> <span class="name<?= $line['rank']?><? echo $tech?><? echo $pionier?>"><?= $line['name']?></span> : 
				<span <?php echo ($line['rank'] >= 4) ? 'class="msg_chat_strong"' : '';?>>
				<? echo $tag?><? echo $img?><?= $line['msg']?><?echo $imgend?><? echo $tagend?></span></p>
				
				<input type="hidden" name="action" value="del_msg">
				<input type="hidden" name="salon" value="<?=$salon?>">
				<input type="hidden" name="del_msg" value="<?= $line['id']?>" />
			</form><?php
			}
			
			else
			{
				?>
				<p><?= $date?> : <img src="pics/avatar/miniskin_<? echo $skin?>.png" width="17px" /> <span class="name<?= $line['rank']?><? echo $tech?><? echo $pionier?>"><?= $line['name']?></span> :  
				<span <?php echo ($line['rank'] >= 4) ? 'class="msg_chat_strong"' : '';?>>
				<? echo $tag?><? echo $img?><?= $line['msg']?><?echo $imgend?><? echo $tagend?></span></p>
				<?php
			}
			?>
			<?php
		}
		?>
		
		<p>Vous devez être connecté et avoir validé votre adresse email pour intéragir avec cette page.</p>
		<?php
	}
}
?>
