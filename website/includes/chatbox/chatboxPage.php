<?php function chatboxPage ()
{
	global $db, $_SESSION;

	//Accès à la chatbox
	define('rank_speak', 1);

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
						<input type="text" id="msg" maxlength="255" /><span id="rmMsg" class="rmBt"/>[X]</span>
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

		<script src="includes/chatbox/chatboxReload.js"></script>
	<?php
	}
	else 
	{	?>
		<h3>Chatbox</h3>

		
		<?php
		$answer = $db->query('SELECT COUNT(*) AS number FROM chatbox WHERE to_id = 0 AND salon = ""');
		$line = $answer->fetch();
		$answer->closeCursor();

		$numMax = $line['number'];
		$numMin = ($numMax >= 30) ? ($numMax - 30) : 0;
		$answer = $db->query("SELECT c.post_date date, c.message msg, m.name name, m.id m_id
				      FROM chatbox c
				      INNER JOIN members m ON m.id = c.user_id
				      WHERE to_id = 0 AND salon = ''
				      ORDER BY post_date
				      LIMIT $numMin, $numMax");

		while ($line = $answer->fetch())
		{
			$date = preg_replace('#^.{11}(.{2}):(.{2}):.{2}$#', '$1:$2', $line['date']);
			
			$color = color($line['m_id']);

			?>
				<p><?= $date?> : <span class="name"><?= $line['name']?></span> : <?= $line['msg']?></p>
			<?php
		}
		?>
		
		<p>Vous devez être connecté et avoir validé votre adresse email pour intéragir avec cette page.</p>
		<?php
	}
}
?>
