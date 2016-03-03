<?php function perso ()
{
	global $db, $_POST, $_GET, $_SESSION;

	include('includes/interface/JSONapi.php');



		$ip = 'soul.omgcraft.fr';

		$port = 20059;

		$user = "nix";

		$pwd = "dragonball";

		$salt = 'salt';

		$api = new JSONAPI($ip, $port, $user, $pwd, $salt);


	if (isset($_GET['perso']))
	{
		$perso = intval($_GET['perso']);
		$answer = $db->prepare('SELECT m.id AS id, m.rank AS rank, m.name AS name, m.title AS title,title, m.background AS bg, m.valid_bg AS valid_bg
					FROM members AS m
					WHERE m.id = ?');
		$answer->execute(array($perso));

		if ($line = $answer->fetch())
		{
			$dispPerso = true;
			if($_GET['mod'] == 'chr')
			{
				if($_SESSION['rank'] >= 6)
				{
					$db->exec('update members set race = \'' .$_GET['r']. '\' where id = \'' .$perso. '\'');
					$api->call('server.run_command', array('/setrace (mc) (race)'));

				}
			}
			if (isset($_POST['action']))
			{
				$action = $_POST['action'];
			
				//Dégrader

				if ($action == 'downgrade' && $line['rank'] > 1 && ($line['rank'] < $_SESSION['rank'] || $_SESSION['MJ']))
				{
					if ($line['title'] == $line['r_title'])
					{
						$answer2 = $db->prepare('SELECT title FROM groups_ranks WHERE rank = ?');
						$answer2->execute(array(($line['rank'] - 1)));
						$line2 = $answer2->fetch();
						$newTitle = $line2['title'];
					}
					else
					{
						$newTitle = $line['title'];
					}

					$update = $db->prepare('UPDATE members SET rank = ?, title = ? WHERE id = ?');
					$update->execute(array(($line['rank'] - 1), $newTitle, $line['id']));

					$dispPerso = false;

					?><p>Le personnage a bien été dégradé.</p><?php
				}
			
				//Promure

				if ($action == 'upgrade' && $line['rank'] > 0 && $line['rank'] < maxRank &&
				(($line['rank'] + 1) < $_SESSION['rank'] || $_SESSION['MJ']))
				{
					if ($line['title'] == $line['r_title'])
					{
						$answer2 = $db->prepare('SELECT title FROM groups_ranks WHERE rank = ?');
						$answer2->execute(array(($line['rank'] + 1)));
						$line2 = $answer2->fetch();
						$newTitle = $line2['title'];
					}
					else
					{
						$newTitle = $line['title'];
					}

					$update = $db->prepare('UPDATE members SET rank = ?, title = ? WHERE id = ?');
					$update->execute(array(($line['rank'] + 1), $newTitle, $line['id']));

					$dispPerso = false;

					?><p>Le personnage a bien été promu.</p><?php
				}

				//Valider le Background

				if ($action == 'validate' && $_SESSION['rank'] >= 6)
				{
				echo 'BackGround Validé';
					$update = $db->prepare('UPDATE members SET valid_bg = 1 WHERE id = ?');
					$update->execute(array($perso));
				}

				//Retirer la validation du Background.

				if ($action == 'unvalidate' && $_SESSION['rank'] >= 6)
				{
					$update = $db->prepare('UPDATE members SET valid_bg = 0 WHERE id = ?');
					$update->execute(array($perso));
				}
			}

			if ($dispPerso)
			{
				if (true /*Mettre les conditions d'affichage*/)
				{
					?><h3 class="rank<?= $line['rank']?>"><?= $line['title']?> <?= $line['name']?></h3><?php

					//Formulaire de dégradation

					$answer2 = $db->prepare('SELECT * FROM groups_ranks WHERE rank = ?');
					$answer2->execute(array($line['rank'] - 1));
					$line2 = $answer2->fetch();
					$answer2->closeCursor();

					if ($line['rank'] > 1 && ($line['rank'] < $_SESSION['rank'] || $_SESSION['MJ']))
					{
						?><form method="POST" action="index.php?p=perso&amp;perso=<?= $perso?>" class="grade_form">
							<p>Dégrader ce personnage au rang de <em class="rank<?= $line2['id']?>"><?= $line2['title']?></em> : 
							<input type="button" value="[-]" class="button" id="downgrade_button" /></p>
							<input type="hidden" name="action" value="downgrade" />
						</form>
					
						<script>
							var button = document.getElementById('downgrade_button');

							button.addEventListener('click', function (e) {
								if (confirm('Êtes vous sûr de vouloir changer le grade de ce personnage ?'))
								{
									e.target.parentNode.parentNode.submit();
								}
							}, false);
							
						</script><?php
					}

					//Formulaire de promulgation

					$answer2 = $db->prepare('SELECT * FROM groups_ranks WHERE rank = ?');
					$answer2->execute(array($line['rank'] + 1));
					$line2 = $answer2->fetch();

					if ($line['rank'] < maxRank && $line['rank'] > 0 && (($line['rank'] + 1) < $_SESSION['rank'] || $_SESSION['MJ']))
					{
						?><form method="POST" action="index.php?p=perso&amp;perso=<?= $perso?>" class="grade_form">
							<p>Élever ce personnage au rang de <em class="rank<?= $line2['id']?>"><?= $line2['title']?></em> : 
							<input type="button" value="[+]" class="button" id="upgrade_button" /></p>
							<input type="hidden" name="action" value="upgrade" />
						</form>
					
						<script>
							var button = document.getElementById('upgrade_button');

							button.addEventListener('click', function (e) {
								if (confirm('Êtes vous sûr de vouloir changer le grade de ce personnage ?'))
								{
									e.target.parentNode.parentNode.submit();
									}
							}, false);							
						</script><?php
					}

					//Background
					
					$bg = preg_replace('#\n#', '<br />', $line['bg']);
					$bg = ($bg != 'none') ? $bg : '[Ce personnage n\'a pas commencé l\'écriture de son background.]';
	
					?>
					<h4>Background</h4>
					<p id="background"><?= $bg?></p>
					<?php
					if ($_SESSION['rank'] >= 6)
					{
						?>
						<p id="validBt"><a href="#"><?php echo($line['valid_bg'])?'Inv':'V';?>alider ce Background</a></p>
						<form method="POST" action="index.php?p=perso&amp;perso=<?=$perso?>" id="validForm">
							<input type="hidden" name="action" value="<?php echo($line['valid_bg'])?'un':''?>validate" />
						</form>
						<script>
							var validBt = document.getElementById('validBt');
							var validForm = document.getElementById('validForm');

							validBt.addEventListener('click', function() {
								validForm.submit();
							}, false);
						</script>
						<?php
					}
				}
				else
				{
					?>
					<p>Vous n'avez pas accès à la fiche de ce personnage.</p>
					<?php
				}
			}
		}
		else
		{
			?><p>Ce personnage n'existe pas.</p><?php
		}
	}
	else
	{
		if (isset($_POST['action']) && $_POST['action'] == "changebg" && isset($_POST['bg']) && strlen($_POST['bg']) > 0)
		{
			$update = $db->prepare('UPDATE members SET background = ?, valid_bg = 0 WHERE id = ?');
			$update->execute(array(htmlentities($_POST['bg']), $_SESSION['id']));
		}

		$answer = $db->prepare('SELECT background AS bg FROM members WHERE id = ?');
		$answer->execute(array($_SESSION['id']));
		$line = $answer->fetch();

		$bg = preg_replace('#\n#', '<br />', $line['bg']);
		$bg = ($bg != 'none') ? $bg : '[Ce personnage n\'a pas commencé l\'écriture de son background.]';

		?>
		<h3 class="rank style="color:<?= color($_SESSION['id'])?>" <?= $_SESSION['rank']?>"><?= $_SESSION['title']?> <?= $_SESSION['name']?></h3>

		<h4>Background</h4>
		<?php
		
		if (isset($_GET['action']) && $_GET['action'] == 'pagechangebg')
		{
			?>
			<form method="POST" action="index.php?p=perso">
				<textarea name="bg" id="background"><?= $line['bg']?></textarea>
				<input type="submit" value="Modifier" />
				<input type="hidden" name="action" value="changebg" />
			</form>
			<?php
		}
		else
		{
		?>
			<p id="background"><?= $bg?></p>
			<p><a href="index.php?p=perso&amp;action=pagechangebg">Modifier votre background</a></p>
			
			<?
			$valid = $db->query('SELECT * FROM members WHERE id = \'' .$line['valider_id']. '\' ');
			$id = $valid->fetch();
			$valid->closeCursor();
			?>
				<? if ($_SESSION['valid_bg'] >= 1) { ?>
			<p style="color: red">Votre background a été validé par <?= $id['rank']?> <?= $id['name']?>, si vous le modifiez il perdra sa validation.</p>
				<? } ?>
			
			<p>Le changement de race n'est pas encore au point, vous pouvez tout de même vous adresser à un membre du Staff pour effectuer ce changement !</p>
			
		<?php
		}
	}
}
?>
