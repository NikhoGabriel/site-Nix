<?php function chgEmail ()
{
	global $db, $_SESSION, $_POST;

	$dispForm = true;

	$sameEmails = true;
	$emailValid = true;

	$answer = $db->prepare('SELECT email FROM members WHERE id = ?');
	$answer->execute(array($_SESSION['id']));
	$line = $answer->fetch();
	$oldEmail = $line['email'];

	if (isset($_POST['email1']) && isset($_POST['email2']))
	{
		$email1 = $_POST['email1'];
		$email2 = $_POST['email2'];

		$sameEmails = ($email1 == $email2) ? true : false;
		$emailValid = (preg_match('#[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-z]{2,6}#', $email1)) ? true : false;

		if ($sameEmails && $emailValid && $email1 != $oldEmail)
		{
			$update = $db->prepare('UPDATE members SET email = ? WHERE id = ?');
			$update->execute(array($email1, $_SESSION['id']));

			$dispForm = false;

			$key = md5(uniqid(rand(), true));

			$update = $db->prepare('UPDATE members SET activate = ? WHERE id = ?');
			$update->execute(array($key, $_SESSION['id']));

			$activateUrl = 'http://rpnix.com/index.php?p=a&k=' . $key;
			$subject = 'Validation de votre nouvelle adresse e-mail.';
			
			$answer = $db->query("SELECT disp_text FROM texts WHERE name='email_validation'");
			$line = $answer->fetch();
			$message = $line['disp_text'];
			$message = preg_replace('#%name#', $_SESSION['name'], wordwrap($message, 70));
			$message = preg_replace('#%url#', $activateUrl, $message);
			
			mail($_POST['email1'], $subject, $message, 'From: Nix');
			
			$_SESSION['alertEmail'] = true;
			
			?>Votre adresse email a bien été changée. Activez la depuis l'e-mail d'activation.<?php
		}
	}

	if ($dispForm)
	{
		?>
			<form method="POST" action="index.php?p=account&amp;action=chgemail">
				<p>Votre adresse e-mail actuelle est <?= $oldEmail?> .</p>
				<table>
					<tr>
						<td><label for="email1">Nouvelle adresse e-mail :</label></td>
						<td><input type="email" name="email1" id="email1"<?php echo (!$emailValid) ? ' class="falseInput"' : '';?> /></td>
						<td><?php echo (!$emailValid) ? 'Cette adresse e-mail n\'est pas valide.' : '';?></td>
					</tr>
					<tr>
						<td><label for="email2"> Confirmez l'adresse e-mail :</label></td>
						<td><input type="email" name="email2" id="email1" autocomplete="off"<?php 
						 echo (!$sameEmails) ? ' class="falseInput"' : '';?> /></td>
						<td><?php echo (!$sameEmails) ? 'Les deux adresses e-mail ne sont pas identiques.' : '';?></td>
					</tr>
					<tr>
						<td colspan="2" id="formAction">
							<input type="submit" value="Changer" />
							<input type="reset" value="Annuler" />
						</td>
						<td></td>
					</tr>
				</table>
			</form>
		<?php
	}
}
?>
