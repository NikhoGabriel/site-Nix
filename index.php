<?php

ini_set('display_errors', 1);

session_start();
echo 'test';
//Nombre de pages vues

$views = fopen('../views.txt', 'r+');
$viewsNbr = intval(fgets($views));
$viewsNbr++;
fseek($views, 0);
fputs($views, $viewsNbr);
fclose($views);

	//Recupération de la base de donnée


include_once('../db.php');
$db = init_db();

	//Définition si nécessaire des variables de sessions
	//Actualisation de la dernière action

if (!isset($_SESSION['connected']))
{
	$_SESSION['connected'] = false;
}

function color ($id, $cssStyle = false)
{
	//Couleur
	
	global $db;
	$answer = $db->prepare('SELECT rank FROM members WHERE id = ?');
	$answer->execute(array(intval($id)));
	if ($line = $answer->fetch())
	{
		switch ($line['rank'])
		{ 
			case 0: $color= "#404040" ; break;
			case 1: $color= "#000000" ; break;
			case 2: $color= "#00e5e6" ; break;
			case 3: $color= "#007acc" ; break;
			case 4: $color= "#339900" ; break;
			case 5: $color= "#ff4d4d" ; break;
			case 6: $color= "#ff0000" ; break;
			case 7: $color= "#FFD700" ; break;
			case 8: $color= "#FF8C00" ; break;
			case 9: $color= "#9900FF" ; break;
			default: $color = "inherit" ; break;
		}
	}
	else
	{
		$color = "inherit";
	}

	if ($cssStyle)
	{
		$color = ($color) ? "style=\"color:$color;\"":'';
	}

	return $color;
}

function rank ($id)
{
	global $db;
	$answer = $db->prepare('SELECT rank FROM members WHERE id = ?');
	$answer->execute(array(intval($id)));
	
	if ($line = $answer->fetch())
	{
		$rank = $line['rank'];
	}
	else
	{
		$rank = 0;
	}
	return $rank;
}

function shirka_say ($msg)
{
	global $db;

	$insert = $db->prepare('INSERT INTO chatbox (post_date, user_id, message) VALUES (NOW(), 92, ?)');
	$insert->execute(array(htmlspecialchars($msg)));
}

if ($_SESSION['connected'])
{
	$update = $db->prepare('UPDATE members SET last_action = NOW() WHERE id = ?');
	$update->execute(array($_SESSION['id']));

	$answer = $db->prepare('SELECT rank, title FROM members WHERE id = ?');
	$answer->execute(array($_SESSION['id']));
	$line = $answer->fetch();
	$answer->closeCursor();

	$rank = $line['rank'];
	$_SESSION['title'] = $line['title'];

	$answer = $db->prepare('SELECT COUNT(*) AS number FROM private_message WHERE to_id = ? AND unread = 1');
	$answer->execute(array($_SESSION['id']));
	$line = $answer->fetch();
	$answer->closeCursor();
	$_SESSION['alertNewMsgs'] = $line['number'];

}
else
{
	$_SESSION['rank'] = 0;
}

	//Définition des rangs
	//Insertion de la fonction affichant la page
if (isset($_GET['p']) && $_GET['p'] == 'chatboxsystem')
{
	include('includes/chatbox/chatboxSystem.php');
	chatboxSystem();
}
else if (isset($_GET['p']) && $_GET['p'] == 'chatboxsystemmj')
{
	include('includes/chatbox_mj/chatboxSystem.php');
	chatboxSystem();
}
else
{
	include('includes/showPage.php');
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
		<meta name="robots" content="all, index, follow" />
		<meta name="language" content="fr-FR" />
		<meta name="keywords" content="nix, Nix, serveur, Minecraft,  rp, roleplay, jeu de rôle, magie, mystère, neige, nord, nordique, aventure, savoir" />
		<meta name="description" content="Nix est un serveur français de roleplay sur Minecraft, se déroulant dans une région enneigée et remplit de mystères occultes." />
		<title>Nix &bull; Roleplay sur Minecraft</title>
		
		<link rel="icon" type="image/x-icon" href="img/favicon.ico" />
		<link href='http://fonts.googleapis.com/css?family=Indie+Flower' rel='stylesheet' type='text/css '>
		<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
		<link href="files/Minecraftia/stylesheet.css" rel="stylesheet" type="text/css">
		<link href="includes/style.css" rel="stylesheet" type="text/css">
	</head>

	<body>
		<?php showPage(); ?>

		<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-63605557-1', 'auto');
		ga('send', 'pageview');
		</script>
	</body>
</html><?php } ?>
