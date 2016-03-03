<?php function chgPwd ()
{
	global $db, $_SESSION, $_POST;

	$oldPwdCorrect = true;
	$samePwds = true;
	$enoughLength = true;
	$dispForm = true;

	if (isset($_POST['oldPwd']) && isset($_POST['newPwd1']) && isset($_POST['newPwd2']))
	{
		$oldPwd = $_POST['oldPwd'];
		$newPwd1 = $_POST['newPwd1'];
		$newPwd2 = $_POST['newPwd2'];

		$samePwds = ($newPwd1 == $newPwd2) ? true : false;

		$enoughLength = (strlen($newPwd1) >= 6) ? true : false;

		$answer = $db->prepare('SELECT password FROM members WHERE id = ?');
		$answer->execute(array($_SESSION['id']));
		$line = $answer->fetch();
		$oldPwdCorrect = (password_verify($oldPwd, $line['password'])) ? true : false;

		if ($samePwds && $enoughLength && $oldPwdCorrect)
		{
			$dispForm = false;
			$newPwd = password_hash($newPwd1, PASSWORD_DEFAULT);

			$update = $db->prepare('UPDATE members SET password = ? WHERE id = ?');
			$update->execute(array($newPwd, $_SESSION['id']));

			?><p>Votre mot de passe a bien été changé</p><?php
		}
	}

	if ($dispForm)
	{
		?>
			<form method="POST" action="index.php?p=account&amp;action=chgpwd">
				<table>
					<tr>
						<td><label for="oldPwd">Ancien mot de passe :</label></td>
						<td><input type="password" name="oldPwd" id="oldPswd"<?php 
						 echo (!$oldPwdCorrect) ? ' class="falseInput"' : '';?> /></td>
						<td><?php echo (!$oldPwdCorrect) ? 'L\'ancien mot de passe n\'est pas correct.' : '';?> </td>
					</tr>

					<tr>
						<td><label for="newPwd1">Nouveau mot de passe :</label></td>
						<td><input type="password" name="newPwd1" id="newPwd1" maxlength="255"<?php 
						 echo (!$enoughLength) ? ' class="falseInput"' : '';?>  /></td>
						<td><?php echo (!$enoughLength) ? 'Votre mot de passe doit faire au moins 6 caractères.' : '';?> </td>
					</tr>

					<tr>
						<td><label for="newPwd2">Confirmez votre nouveau mot de passe :</label></td>
						<td><input type="password" name="newPwd2" id="newPwd2" maxlength="255"<?php 
						 echo (!$samePwds) ? ' class="falseInput"' : '';?>  /></td>
						<td><?php echo (!$samePwds) ? 'Ce mot de passe n\'est pas identique au précédent.' : '';?> </td>
					</tr>

					<tr>
						<td colspan="2" id="formAction"><input type="submit" value="Changer" /><input type="reset" value="Annuler" /></td>
						<td></td>
					</tr>
				</table>
			</form>
		<?php
	}
}
?>
