<?php function activate ()
{
	global $_GET, $_SESSION, $db;

	if ($_SESSION['connected'])
	{
		if ($_SESSION['alertEmail'])
		{
			if (isset($_GET['k']))
			{
				$answer = $db->prepare('SELECT activate, rank FROM members WHERE name=?');
				$answer->execute(array($_SESSION['name']));
				$line = $answer->fetch();

				if ($line['activate'] == 'true')
				{
					?><p>Votre adresse e-mail est déjà validée <?= $_SESSION['name'] ?>.<?php
					$_SESSION['alertEmail'] = false;
				}
				else
				{
					if ($_GET['k'] == $line['activate'])
					{
						if ($line['rank'] == 0)
						{
							$update = $db->prepare("UPDATE members SET activate='true', rank=1 WHERE name=?");
							$update->execute(array($_SESSION['name']));

							$answer = $db->query('SELECT id FROM forum_forum');

							while ($line = $answer->fetch())
							{
								$insert = $db->prepare("INSERT INTO forum_unread VALUES ('', ?, ?, 1)");
								$insert->execute(array($line['id'], $_SESSION['id']));
							}
						}
						else
						{
							$update = $db->prepare("UPDATE members SET activate='true' WHERE name=?");
							$update->execute(array($_SESSION['name']));
						}
						$_SESSION['alertEmail'] = false;
						$_SESSION['rank'] = 1;

						?>Votre adresse e-mail a été activée avec succès.<?php
					}
					else
					{
						?>La clé d'activation n'est pas correcte.<?php
					}
				}
			}
			else
			{
				?>La clé d'activation n'est pas présente dans l'url.<?php
			}
		}
		else
		{
			?><p>Votre adresse e-mail est déjà validée <?= $_SESSION['name'] ?>.<?php
		}
	}
	else
	{
		?><p>Vous devez être connecté pour effectuer cette action.</p><?php
	}
}
?>
