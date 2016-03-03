<?php function account ()
{
	global $db, $_SESSION, $_GET, $_POST;

	if ($_SESSION['connected'])
	{
		$action = (isset($_GET['action'])) ? $_GET['action'] : false;
		
		switch($action)
		{
			case 'chgpwd':
			{
				include('includes/account/chgPwd.php');
				chgPwd();
				break;
			}		

			case 'chgemail':
			{
				include('includes/account/chgEmail.php');
				chgEmail();
				break;
			}	
		
			default :
			{
		
			?>
				<h3>Compte</h3>

				<nav id="account">
					<p><a href="index.php?p=account&amp;action=chgpwd">Changer le mot de passe</a></p>
					<p><a href="index.php?p=account&amp;action=chgemail">Changer l'adresse email</a></p>
				</nav>
			<?php
			}
		}
	}
}
?>
