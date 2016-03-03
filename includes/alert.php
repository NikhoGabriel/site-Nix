<?php function alert ()
{
	global $_SESSION, $_GET;

	$page = (isset($_GET['p'])) ? $_GET['p'] : false;

	if ($_SESSION['connected'])
	{
		if ($_SESSION['alertEmail'] && $page != 'a')
		{
			?>
			<section class="alert">Vous n'avez pas encore activé votre adresse e-mail. Pour le faire, cliquez sur le lien dans votre e-mail d'inscription.</section>
			<?php
		}

		if ($_SESSION['alertNewMsgs'] && $page != 'pm')
		{
			$end = ($_SESSION['alertNewMsgs'] > 1)?'s':'';
			$endx = ($_SESSION['alertNewMsgs'] > 1)?'x':'';
			?>
			<section class="alert">
				<h4>Nouveau<?= $endx?> message<?=$end?></h4>
				<p>Vous avez <?= $_SESSION['alertNewMsgs']?> nouveau<?= $endx?> message<?= $end?>.
				Pour le<?= $end?> consulter, <a href="index.php?p=pm">cliquez ici.</a></p>
			</section>
			<?php
		}
	}
}
?>
