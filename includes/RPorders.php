<?php function RPorders ()
{
	global $db, $_SESSION;
	
	$answer = $db->query("SELECT * FROM members");
	$line = $answer->fetch();
	$answer->closeCursor();
?>

	
<?php
if ($_SESSION["connected"]) {
		
	if ($_SESSION["name"] == "Frost") { ?>
		plop
		
	<? } ?>
	
	<?php if ($_SESSION["ombres"] == 0) { ?>
		<p>Vous n'avez pas eu encore l'autorisation de consulter les ordres de ce groupe.</p>
	<? } 
}
		else
		{?> <p>Vous devez être connecté pour voir cette page</p> <? }
	
	?>
	
<?php
}
?>