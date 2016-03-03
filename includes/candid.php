<?php function candid ()
{
	global $db, $_SESSION, $_POST, $_GET;

	define("rank_validation", 4);
	define("rank_send_candid", 1);
	
	?>
		<h3>Candidature</h3>
	<?php
	
	$answer = $db->prepare("SELECT accepted FROM members WHERE id = ?");
	$answer->execute(array($_SESSION["id"]));
	$line = $answer->fetch();
	$answer->closeCursor();
	
	if ($_SESSION["rank"] >= rank_send_candid)
	{
		if ($line && !$line["accepted"])
		{
			$answer = $db->prepare("SELECT date_send FROM candid WHERE sender_id = ? AND verify = 0");
			$answer->execute(array($_SESSION["id"]));
			$line = $answer->fetch();
			$answer->closeCursor();
			
			if ($line)
			{
				$answer = $db->prepare("SELECT COUNT(id) number FROM candid WHERE verify = 0 AND date_send < ?");
				$answer->execute(array($line["date_send"]));
				$line = $answer->fetch();
				
				?>
					<p>Votre candidature n'a pas encore été examinée.</p>
				<?php
				
				if ($line["number"] > 0)
				{
					$plural = $line["number"] > 1;
					?>
						<p><?=$line["number"]?> candidature<?php echo ($plural)?'s':'';?> doi<?php echo ($plural)?'ven':'';?>t être<?php 
						echo ($plural)?'s':'';?> vérifiée<?php echo ($plural)?'s':'';?> avant la votre.</p>
					<?php
				}
				else
				{
					?>
						<p>Aucune candidature ne doit être vérifiée avant la votre.</p>
					<?php
				}
			}
			else
			{
				if (isset($_POST["action"]) && $_POST["action"] == "send" && isset($_POST["mc"]) && strlen($_POST["mc"]) <= 16 && isset($_POST["candid"]))
				{
					$pseudoMc = htmlspecialchars($_POST["mc"]);
					$candid = htmlspecialchars($_POST["candid"]);
					
					$insert = $db->prepare("INSERT INTO candid (sender_id, pseudo_mc, candid, date_send) VALUES (?, ?, ?, NOW())");
					$insert->execute(array($_SESSION["id"], $pseudoMc, $candid));
					
					$candidature = $db->prepare("UPDATE members SET Minecraft_Account = ? WHERE id = ?");
					$candidature->execute(array($pseudoMc, $_SESSION['id']));
					
					?>
						<p>Votre candidature a été envoyée, elle sera examinée sous peu.</p>
					<?php
				}
				else
				{
				?>
					<p>Pour pouvoir nous rejoindre, vous devez tout d'abord poster une candidature qui doit nous donner envie de vous accepter.<!--
					--> Dans cette candidature, racontez l'histoire de votre personnage en une dizaines de lignes et rapidement pourquoi vous voulez rejoindre Nix.</p>

					<p>Bon courage !</p>
					
					<form method="POST" action="index.php?p=candid">
						<label for="mc">Pseudo Minecraft :</label><input type="text" name="mc" id="mc" placeholder="Ex: xXX_killer36_XXx" maxlength="16" /><br />
						<label for="candid_form">Votre candidature</label>
						<textarea id="candid_form" name="candid" placeholder="Ex: Il était une fois, un jeune paysan qui..."></textarea><br />
						<input type="hidden" name="action" value="send" />
						<input type="submit" value="Soumettre" />
					</form>
				<?php
				}
			}
		}
		else if ($_SESSION["rank"] >= rank_validation)
		{
			if (isset($_POST["action"]) && isset($_POST["candid"]) && intval($_POST["candid"]) != 0)
			{
				if ($_POST["action"] == "accept")
				{
					$answer = $db->prepare("SELECT c.sender_id sender_id, c.id id, c.pseudo_mc mc, c.verify verify, m.name sender
								FROM candid c
								INNER JOIN members m ON c.sender_id = m.id
								WHERE c.id = ?");
					$answer->execute(array(intval($_POST["candid"])));
					$line = $answer->fetch();
					
					if ($line && !$line["verify"])
					{
						$update = $db->prepare("UPDATE candid SET verify = 1, accepted = 1, valider_id = ?, date_verify = NOW() WHERE id = ?");
						$update->execute(array($_SESSION["id"], $line["id"]));
						
						$update = $db->prepare("UPDATE members SET accepted = 1 WHERE id = ?");
						$update->execute(array($line["sender_id"]));
						
						$answer = $db->prepare("SELECT name, title FROM members WHERE id = ?");
						$answer->execute(array($_SESSION["id"]));
						$lineMsg = $answer->fetch();
						
						$subject = "Candidature acceptée.";
						$message = "Votre candidature a été acceptée par <em class=\"name\">".$lineMsg["title"]." ".$lineMsg["name"]."</em>.
Vous pouvez maintenant accéder au serveur, l'adresse ip se trouve sur la page <a href=\"index.php?p=serv\">Serveur</a>

À bientôt en jeu
Shirka";
						
						$insert = $db->prepare("INSERT INTO private_message (subject, message, date_send, from_id, to_id) VALUES (?, ?, NOW(), ?, ?)"); $db->exec("UPDATE members SET rank = rank + 1 WHERE id = ".$line['sender_id']." ");
						$insert->execute(array($subject, $message, Shirka, $line["sender_id"]));

						shirka_say('Candidature validée pour '.$line['sender']. '.');
						
						include('includes/interface/JSONapi.php');

						$ip = 'soul.omgcraft.fr';
						$port = 20059;
						$user = "nix";
						$pwd = "dragonball";
						$salt = 'salt';
						$api = new JSONAPI($ip, $port, $user, $pwd, $salt);
						
						$api->call("players.name.whitelist", array($line["mc"]));
						
						?>
							<p>La candidature a été acceptée.</p>
						<?php
					}
				}
				else if ($_POST["action"] == "refuse")
				{
					$answer = $db->prepare("SELECT sender_id sender_id, id id, pseudo_mc mc, verify verify
											FROM candid
											WHERE id = ?");
					$answer->execute(array(intval($_POST["candid"])));
					$line = $answer->fetch();
					
					if ($line && !$line["verify"])
					{
						$update = $db->prepare("UPDATE candid SET verify = 1, accepted = 0, valider_id = ?, date_verify = NOW() WHERE id = ?");
						$update->execute(array($_SESSION["id"], $line["id"]));
						
						$answer = $db->prepare("SELECT name, title FROM members WHERE id = ?");
						$answer->execute(array($_SESSION["id"]));
						$lineMsg = $answer->fetch();
						
						$subject = "Candidature refusée.";
						$message = "Votre candidature a été refusée par <em class=\"name\">".$lineMsg["title"]." ".$lineMsg["name"]."</em>.
Essayez d'en refaire une en respectant mieux les consignes données.

Bon courage
Shirka";
						
						$insert = $db->prepare("INSERT INTO private_message (subject, message, date_send, from_id, to_id) VALUES (?, ?, NOW(), ?, ?)");
						$insert->execute(array($subject, $message, Shirka, $line["sender_id"]));
						
						?>
							<p>La candidature a été refusée.</p>
						<?php
					}
					
					
				}
			}
			else if (isset($_GET["candid"]) && intval($_GET["candid"]) != 0)
			{
				$answer = $db->prepare("SELECT m.name sender, c.id id, c.date_send date, c.candid candid, c.pseudo_mc mc, c.verify verify
									  FROM candid c
									  INNER JOIN members m ON m.id = c.sender_id
									  WHERE c.id = ?
									  ORDER BY date_send DESC");
				$answer->execute(array(intval($_GET["candid"])));
				$line = $answer->fetch();
				
				if($line && !$line["verify"])
				{
					$candid = preg_replace( "#\n#","<br />", $line["candid"]);
					?>
					<section class="candid">
						<p>Nom : <?=$line["sender"]?></p>
						<p>Pseudo Minecraft : <?=$line["mc"]?></p>
						<p>Date : <?=$line["date"]?></p>
					</section>
					<section class="candid">
						<h4>Candidature:</h4>
						<p><?=$candid?></p>
					</section>
					
					<form id="candid_validation" method="POST" action="index.php?p=candid">
						<input type="button" value="Accepter" id="accept" />
						<input type="button" value="Refuser" id="refuse" />
						<input type="hidden" name="action" value="" id="action" />
						<input type="hidden" name="candid" value="<?=$line["id"]?>" />
					</form>
					
					<script>
						var accept = document.getElementById("accept");
						var refuse = document.getElementById("refuse");
						var form = document.getElementById("candid_validation");
						var action = document.getElementById("action");
						
						accept.addEventListener("click", function () {
							action.value = "accept";
							form.submit();
						}, false);
						
						refuse.addEventListener("click", function () {
							action.value = "refuse";
							form.submit();
						}, false);
					</script>
					<?php
				}
				else if ($line)
				{
					?>
						<p>Cette candidature a déjà été vérifiée.</p>
					<?php
				}
				else
				{
					?>
						<p>Cette candidature n'existe pas ou plus.</p>
					<?php
				}
			}
			else
			{
				$answer = $db->query("SELECT m.name sender, c.id id, c.date_send date, c.candid candid
									  FROM candid c
									  INNER JOIN members m ON m.id = c.sender_id
									  WHERE c.verify = 0");
				?>
				<table id="candid_list">
					<tbody>
						<tr>
							<th>Nom :</th>
							<th>Date :</th>
							<th>Début de la candidature :</th>
						</tr>
				<?php
				
				while ($line = $answer->fetch())
				{
					$startCandid = substr($line["candid"], 0, 100);
					?>
						<tr>
							<td class="name"><?= $line["sender"]?></td>
							<td><?= $line["date"]?></td>
							<td><a href="index.php?p=candid&amp;candid=<?=$line["id"]?>"><?= $startCandid?></a></td>
						</tr>
					<?php
				}
				
				?>
					</tbody>
				</table>
				<?php
			}
		}
		else
		{
			?>
				<p>Vous avez déjà fait votre candidature, vous pouvez trouver l'adresse IP du serveur sur la page <a href="index.php?p=server">Serveur</a></p>
			<?php
		}
	}
	else
	{	
	?>
		<p>Vous devez être connecté et avoir validé votre adresse email pour accéder à cette page.</p>
	<?php
	}
}
?>
