<?php function register ()
{
	global $db, $_POST, $_GET, $_SESSION;

	$dispForm = true;

	$sameEmails = true;
	$samePwds = true;
	$nameNotUse = true;
	$emailNotUse = true;

	$nameRight = true;
	$pwdRight = true;
	$emailRight = true;
	
	if ($_SESSION['connected'])
	{
		$dispForm = false;
		?>Vous êtes connecté <?= $_SESSION['rankText']?> <?= $_SESSION['name']?>. Pourquoi donc cherchez vous à vous inscrire ?<?php
	}
	else if (isset($_POST['name']) && isset($_POST['email1']) && isset($_POST['email2']) && isset($_POST['pwd1']) && isset($_POST['pwd2']))
	{
		$sameEmails = ($_POST['email1'] == $_POST['email2']) ? true : false;
		$samePwds = ($_POST['pwd1'] == $_POST['pwd2']) ? true : false;

		$answer = $db->prepare('SELECT id FROM members WHERE name = ?');
		$answer->execute(array(htmlspecialchars($_POST['name'])));
		$nameNotUse = ($answer->fetch()) ? false : true;
		$answer->closeCursor();
		
		$answer = $db->prepare('SELECT id FROM members WHERE email = ?');
		$answer->execute(array(htmlspecialchars($_POST['email1'])));
		$emailNotUse = ($answer->fetch()) ? false : true;
		$answer->closeCursor();

		$nameRight = (preg_match('#^[A-ZÀÄÂÉÈËÊÙÛÜÏÎÖÔ][a-z \-àäâéèëêùûüïîöô]{1,29}$#', $_POST['name'])) ? true : false;
		$pwdRight = (preg_match('#^.{6,1000}$#', $_POST['pwd1'])) ? true : false;
		$emailRight = (preg_match('#^[a-z0-9_.-]+@[a-z0-9_.-]{2,}\.[a-z]{2,4}$#', $_POST['email1'])) ? true : false;

		if ($sameEmails && $samePwds && $nameNotUse && $emailNotUse && $nameRight && $pwdRight && $emailRight)
		{

			$activateKey = md5(uniqid(rand(), true));
			$activateUrl = 'http://rpnix.com/index.php?p=a&k=' . $activateKey;
			
			$insert = $db->prepare("INSERT INTO members (name, password, registration_date, email, title, activate, last_action) 
						VALUES (?, ?, CURDATE(), ?, 'Vagabond', ?, NOW())");
			$insert->execute(array(htmlspecialchars($_POST['name']), password_hash($_POST['pwd1'], PASSWORD_DEFAULT), htmlspecialchars($_POST['email1']), 
			  $activateKey));

			$subject = 'Bienvenue sur Nix ' . $_POST['name'] . '.';

			$answer = $db->query("SELECT disp_text FROM texts WHERE name='activation_email'");
			$line = $answer->fetch();
			$message = $line['disp_text'];

			$message = preg_replace('#%name#', $_POST['name'], wordwrap($message, 70));
			$message = preg_replace('#%url#', $activateUrl, $message);

			mail($_POST['email1'], $subject, $message, 'From: Nix');

			?><p>Inscription réussie. Un e-mail vient de vous être envoyé pour valider votre adresse.</p><?php
		}
	}

	if ($dispForm)
	{
	?>
		<h3>Inscription</h3>
		
		<form method="POST" action="index.php?p=register">
			<table>
				<tr>
					<td><label for="name">Nom de votre personnage :</label></td>
					<td><input type="text" name="name" id="name" maxlength="30" <?php if(!$nameNotUse){echo 'style="boder: 1px solid red;"';}?> /></td>
					<td class="error"><?php 
					if(!$nameNotUse){echo'Nom déjà utilisé. ';}
					if(!$nameRight){
					echo'Votre nom doit commencer par une majuscule, ne comporter que des lettres, espaces ou tirets, et faire moins de 30 caractères.';}
					?></td>
				</tr>
				<tr>
					<td><label for="email1">Votre adresse e-mail :</label></td>
					<td><input type="email" name="email1" id="email1" maxlength="50" <?php if(!$emailNotUse){echo 'style="boder: 1px solid red;"';}?> /></td>
					<td class="error"><?php 
					if(!$emailNotUse){echo'Adresse e-mail déjà utilisée. ';}					
					if(!$emailRight){echo'Adresse e-mail non valide. ';}
					?></td>
				</tr>
				<tr>
					<td><label for="email2">Confirmez votre adresse e-mail :</label></td>
					<td><input type="email" name="email2" id="email2" maxlength="50" <?php if(!$sameEmails){echo 'style="boder: 1px solid red;"';}?> /></td>
					<td class="error"><?php 
					if(!$sameEmails){echo'Les deux adresses e-mails ne sont pas identiques. ';}
					?></td>
				</tr>
				<tr>
					<td><label for="pwd1">Votre mot de passe :</label></td>
					<td><input type="password" name="pwd1" id="pwd1" maxlength="1000" /></td>
					<td class="error"><?php 
					if(!$pwdRight){echo'Le mot de passe doit faire au moins 6 caractères. ';}
					?></td>
				</tr>
				<tr>
					<td><label for="pwd2">Confirmez votre mot de passe :</label></td>
					<td><input type="password" name="pwd2" id="pwd2" maxlength="1000" <?php if(!$samePwds){echo 'style="boder: 1px solid red;"';}?> /></td>
					<td class="error"><?php 
					if(!$samePwds){echo'Les deux mots de passes ne sont pas identiques. ';}
					?></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input type="submit" value="Inscription" />
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
