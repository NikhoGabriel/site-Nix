<?php function pm ()
{
	global $db, $_SESSION, $_GET, $_POST;

	//Accès à la page
	define('rank_access_page', 1);

	if ($_SESSION['rank'] >= rank_access_page)
	{
		?><h3>Messages privés</h3><?php

		$dispAllMsgs = true;
		$dispNewMessage = false;

		//Envoi d'un message (ou réponse à un message)

		if (isset($_POST['action']) && $_POST['action'] == 'send')
		{
			if (isset($_POST['subject']) && $_POST['subject'] != '' && isset($_POST['to']) && $_POST['to'] != '' &&
			    isset($_POST['msg']) && $_POST['msg'] != '')
			{
				$toName = htmlspecialchars($_POST['to']);
				$subject = htmlspecialchars($_POST['subject']);
				$msg = $_POST['msg'];

				$answer = $db->prepare('SELECT id FROM members WHERE name = ?');
				$answer->execute(array($toName));

				if ($line = $answer->fetch())
				{
					$dispAllMsgs = false;

					$insert = $db->prepare("INSERT INTO private_message VALUES ('', ?, ?, NOW(), ?, ?, 1)");
					$insert->execute(array($subject, $msg, $_SESSION['id'], $line['id']));

					?>
					<p>Votre message à bien été envoyé.</p>
					<?php
				}
				else
				{
					if (isset($_POST['reply']))
					{
						header('Location: index.php?p=pm&action=reply&msg='.$_POST['reply']);
					}
					else
					{
						$dispNewMessage = true;
						$falseName = true;
					}
				}
			}
			else
			{
				if (isset($_POST['reply']))
				{
					header('Location: index.php?p=pm&action=reply&msg='.$_POST['reply']);
				}
				else
				{
					$dispNewmessage = true;
				}
			}
		}

		//Suppression d'un message

		if (isset($_POST['action']) && $_POST['action'] == 'delMsg' && isset($_POST['msg']))
		{
			$msgToDel = intval($_POST['msg']);

			$answer = $db->prepare('SELECT to_id FROM private_message WHERE id = ?');
			$answer->execute(array($msgToDel));

			if ($line = $answer->fetch())
			{
				if($line['to_id'] == $_SESSION['id'])
				{
					$delete = $db->prepare('DELETE FROM private_message WHERE id = ?');
					$delete->execute(array($msgToDel));

					?>
					<p>Le message a bien été supprimé.</p>
					<?php
				}
			}
		}

		//Affichage d'un message

		if (isset($_GET['pm']))
		{
			$pm = intval($_GET['pm']);

			$answer = $db->prepare('SELECT p.id AS id, p.to_id AS to_id, p.from_id AS from_id,
						       p.subject AS subject, p.message AS message, p.date_send AS date, p.unread AS unread,
						       m.name AS author, m.title AS auth_title, m.rank AS auth_rank, m.id AS auth_id, m.pionier pionier
						FROM private_message AS p
						LEFT JOIN members AS m
						ON m.id = p.from_id
						WHERE p.id = ?');
			$answer->execute(array($pm));
			$line = $answer->fetch();

			if ($line && ($line['to_id'] == $_SESSION['id'] || $line['from_id'] == $_SESSION['id']))
			{
				$dispAllMsgs = false;

				$myMsg = ($line['to_id'] != $_SESSION['id']) ? true : false;
				
				if ($line['unread'] == 1 && !$myMsg)
				{
					$update = $db->prepare('UPDATE private_message SET unread = 0 WHERE id = ?');
					$update->execute(array($pm));
				}

				$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}):(.{2}):.{2}$#', 'Le $3/$2/$1 à $4h$5', $line['date']);
				$message = preg_replace('#\n#', '<br />', $line['message']);
				if ($line['pionier'] == 1) { $pionier = "-P";} else { $pionier = '';}
				?>
				<section id="pm">
				<nav id="navPm">
					<ul>
						<li><a href="index.php?p=pm">Retour</a></li>
						<?php if (!$myMsg) { ?>
						<li><a href="#" id="delMsg">Suprimer</a></li>
						<li><a href="index.php?p=pm&amp;action=reply&amp;msg=<?= $pm?>">Répondre</a></li>
						<?php } ?>
						<li><a href="index.php?p=pm&amp;action=transfer&amp;msg=<?= $line['id']?>">Transférer</a></li>
					</ul>
				</nav>

				<div id="pm_head">
				<table>
					<tr>
						<th>Sujet :</th>
						<td id="subject_pm"><?= $line['subject']?></td>
					</tr>

					<tr>
						<th>Auteur :</th>
						<td>
						<a href="index.php?p=viewmember&amp;perso=<?= $line['auth_id']?>" class="rank<?= $line['auth_rank']?>"><? if ($line['pionier'] == 1) { echo "Pionier";}
						else { echo $line['auth_title'] ;}?> <?= $line['author']?></a>
						</td>
					</tr>

					<tr>
						<th>Date :</th>
						<td><?= $date?></td>
					</tr>
				</table>
				</div>

				<p id="pm_content"><?= $message?></p>

				<form method="POST" action="index.php?p=pm" id="delMsgForm">
					<input type="hidden" name="action" value="delMsg" />
					<input type="hidden" name="msg" value="<?= $line['id']?>" />
				</form>
				</section>

				<script>
					var delMsg = document.getElementById('delMsg');
					var delMsgForm = document.getElementById('delMsgForm');

					delMsg.addEventListener('click', function (e) {
						if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?'))
						{
							delMsgForm.submit();
							e.preventDefault;
						}
					}, false);
				</script>
				<?php

			}
		}

		//Envoyer un message

		if ((isset($_GET['action']) && ($_GET['action'] == 'newmessage' || $_GET['action'] == 'transfer')) || $dispNewMessage)
		{
			$dispAllMsgs = false;

			if ($_GET['action'] == 'transfer' && isset($_GET['msg']))
			{
				$msgId = intval($_GET['msg']);
				$answer = $db->prepare('SELECT p.message msg, p.subject subject, p.date_send date, p.to_id to_id, p.from_id from_id, m.name author
							FROM private_message p
							INNER JOIN members m ON p.from_id = m.id
							WHERE p.id = ?');
				$answer->execute(array($msgId));
				$line = $answer->fetch();
				if ($line && ($line['to_id'] == $_SESSION['id'] || $line['from_id'] == $_SESSION['id']))
				{
					$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}):(.{2}):.{2}$#', 'le $3/$2/$1 à $4h$5', $line['date']);
					$msgHead = '[Par '.$line['author'].' '.$date.'.]';
					$msg = $line['msg'];
					$subject = $line['subject'];
				}
			}

			?>
			<div id="pm">
			<p><a href="index.php?p=pm">Retour</a></p>

			<form method="POST" action="index.php?p=pm">
				<div id="pm_head">
				<table>
					<tr>
						<th><label for="subject">Sujet :</label></th>
						<td colspan="2"><input type="text" name="subject" id="subject" maxlength="255" <?php 
						echo(isset($subject))?'value="'.$subject.'" ':'';?>/></td>
					</tr>
					<tr>
						<th><label for="to">Pour :</label></th>
						<td>
							<input type="text" name="to" id="to" maxlength="255" <?php echo(isset($falseName))?'class="falseInput" ':'';?>/><?php

							?>
						</td>
						<td><?php echo(isset($falseName)) ? 'Cet utilisateur n\'existe pas.' : '';?></td>
					</tr>
				</table>
				</div>

				<label for="msg">Message :</label><br />
				<textarea name="msg" id="msg"><?php if (isset($msgHead)) { echo $msgHead; ?>

				
<?php } ?><?php echo(isset($msg))?$msg:'';?>
				</textarea><br />

				<input type="hidden" name="action" value="send">
				<input type="submit" value="Envoyer">
				<input type="reset" value="Annuler">
			</form>
			</div>
			<?php
		}
		
		//Répondre à un message

		if (isset($_GET['action']) && $_GET['action'] == 'reply' && isset($_GET['msg']))
		{
			$oldMsg = intval($_GET['msg']);

			$answer = $db->prepare('SELECT p.subject AS subject, p.message AS message, p.to_id AS to_id,
						       m.name AS author, m.title AS auth_title, m.rank AS auth_rank, m.id AS auth_id,m.pionier pionier
						FROM private_message AS p
						INNER JOIN members AS m ON m.id = p.from_id
						WHERE p.id = ?');
			$answer->execute(array($oldMsg));

			$line = $answer->fetch();
			if ($line && $line['to_id'] == $_SESSION['id'])
			{
				$dispAllMsgs = false;
				$newSubject = (preg_match('#^Re :#', $line['subject'])) ? $line['subject'] : 'Re : '.$line['subject'];
				$message = preg_replace('#\n#', '<br />', $line['message']);

				?>
				<section id="pm">
				<p><a href="index.php?p=pm">Retour</a></p>

				<form method="POST" action="index.php?p=pm">
					<div id="pm_head">
					<table>
						<tr>
							<th>Sujet :</th>
							<td id="subject_pm"><?= $newSubject?></td>
						</tr>
						<tr>
							<th>Pour :</th>
							<td class="rank<?=$line['auth_rank']?>"><? if ($line['pionier'] == 1) { echo "Pionier";}
						else { echo $line['auth_title'] ;}?> <?= $line['author']?></td>
						</tr>
					</table>
					</div>

					<label for="msg">Message :</label><br />
					<textarea name="msg" id="msg"></textarea><br />

					<input type="hidden" name="subject" value="<?= $newSubject?>">
					<input type="hidden" name="to" value="<?= $line['author']?>">
					<input type="hidden" name="action" value="send">
					<input type="hidden" name="reply" value="<?= $oldMsg?>">
					<input type="submit" value="Envoyer">
					<input type="reset" value="Annuler">
				</form>

				<p id="pm_content"><?= $message?></p>
				</section>
				<?php
			}
		}



		//Affichage des message reçus

		if ($dispAllMsgs)
		{
			if (isset($_GET['action']) && $_GET['action'] == 'showmsgsend')
			{
				$answer = $db->prepare('SELECT p.id AS id, p.subject AS subject, p.date_send AS date, p.unread AS unread,
							       m.name AS author, m.title AS auth_title, m.rank AS auth_rank, m.id AS auth_id, m.pionier pionier
							FROM private_message AS p
							LEFT JOIN members AS m
							ON m.id = p.to_id
							WHERE p.from_id = ?
							ORDER BY date_send DESC');
				$msgSend = true;

			}
			else
			{
				$answer = $db->prepare('SELECT p.id AS id, p.subject AS subject, p.date_send AS date, p.unread AS unread,
							       m.name AS author, m.title AS auth_title, m.rank AS auth_rank, m.id AS auth_id, m.pionier pionier
							FROM private_message AS p
							LEFT JOIN members AS m
							ON m.id = p.from_id
							WHERE p.to_id = ?
							ORDER BY date_send DESC');
				$msgSend = false;
			}
			$answer->execute(array($_SESSION['id']));

			$void = true;
			
			?>
			<section id="pm_list_section">
			<nav id="navPm">
				<ul>
					<li><a href="index.php?p=pm&amp;action=newmessage">Nouveau message</a></li>
					<?php if ($msgSend)
					{ ?> <li><a href="index.php?p=pm">Messages reçus</a></li><?php }
					else
					{ ?><li><a href="index.php?p=pm&amp;action=showmsgsend">Messages envoyés</a></li><?php }
					?>
				</ul>
			</nav>

			<table id="pm_list">
				<tr>
					<th>Sujet</th>
					<th><?php echo ($msgSend) ? 'Destinataire' : 'Auteur'; ?></th>
					<th>Date</th>
				</tr>
			<?php
			while ($line = $answer->fetch())
			{
				$void = false;

				$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}):(.{2}):.{2}$#', 'Le $3/$2/$1 à $4h$5', $line['date']);

				?>
				<tr>
					<td><a href="index.php?p=pm&amp;pm=<?= $line['id']?>"><?php
					echo($line['unread'])?'<span class="unread">[!]</span> ':'';?><?= $line['subject']?></a></td>
					<td><a href="index.php?p=viewmember&amp;perso=<?= $line['auth_id']?>" class="rank<?= $line['auth_rank']?>"><? if ($line['pionier'] == 1) { echo "Pionier";}
						else { echo $line['auth_title'] ;}?> <?= $line['author']?></a></td>
					<td><?= $date?></td>
				</tr>
				<?php
			}
			if ($void)
			{
				?>
				<tr>
					<td colspan="3">Aucuns messages</td>
				</tr>
				<?php
			}

			?>
			</table>
			</section>
			<?php
		}
	}
	else if ($_SESSION['connected'])
	{
		?><p>Vous devez avoir validé votre adresse email pour accéder à cette page. (Allez ce n'est pas dur, et vous pouvez la changer sur la page "Compte". ;) )</p><?php
	}
	else
	{
		?><p>Vous devez être connecté pour accéder à cette page. ;)<?php
	}
}
?>
