<?php function magie_admin ()
{
	global $db, $_SESSION;

	$answer = $db->query("SELECT COUNT(*) AS number FROM incan_list");
	$line = $answer->fetch();
	$answer->closeCursor();

	if ($_SESSION["connected"]) {
		
	if ($_SESSION["rank"] >= 4) { ?>

	<h2>Liste des sorts et d'incantations</h2>
	
	<?php
			$answer = $db->query("SELECT * FROM incan_list ORDER BY level DESC , type ASC, name ASC");
	?>
		
		<table>

			<th>Formule</th>
			<th>Description</th>
			<th>Energie nécessaire</th>
			<th>Commande</th>
			<th>Classe</th>
			<th>Type</th>
		
				
		<?php

	while ($line = $answer->fetch())
	{
							switch ($line['level']) { case 8: $level = "X"; break;	case 7:  $level = "S"; break; case 6:  $level = "A"; break; case 5:  $level = "B"; break; case 4:  $level = "C"; break; case 3:  $level = "D"; break; 
								case 2:  $level = "E"; break; case 1:  $level = "F"; break;}
							switch ($line['type']) {
								case 13: $type	= "Terre" ; break; case 12: $type = "Psy" ; break; case 11: $type = "Ombre" ; break; case 10:  $type = "Nature" ; break; case 9:  $type = "Metal" ; break;
								case 8: $type = "Lumiere" ; break; case 7: $type = "Glace" ; break; case 6: $type = "Feu" ; break; case 5: $type = "Energie" ; break;
								case 4: $type = "Eau" ; break; case 3: $type = "Chaos" ; break; case 2: $type = "Arcane" ; break; case 1: $type = "Air" ; break; case 0: $type = "Unknow" ; break; }

		
		?>
		
			<tr>
				<td><?= $line['name']?></td>
				<td><?= $line['desc']?></td>
				<td><img src="includes/img/magie/xp.png" alt="XP" class="magie_type" /> <?= $line['cost']?></td>
				<td><?= $line['command']?></td>
				<td><img class="magie" src="includes/img/magie/Magie_<?php echo $level ?>.png" alt="Niveau <?php echo $level ?>" title="Niveau <?php echo $level ?>" /></td>
				<td><img class="magie_type" src="includes/img/magie/Magie_<?php echo $type ?>.png" width="49" alt="Type <?php echo $type ?>" title="<?php echo $type ?>"/></td>
			</tr>
	<?php
	}
	?> 
			
		</table>
		
		<h2>Pages de prières aux entités</h2>
		
			<h4>Thorgeir</h4>
			<p><img src="http://www.rpnix.com/pics/Image_pgthor.gif" alt="Prière à Thorgeir" /><br />
			Traduction : Thorgeir tatium quantum curator orbis, audient vocem tuam et veni fidelem nobis <br />
			(fr) Thorgeir, représentant du respect, protecteur du Monde, entends l'appel de tes fidèles et viens à nous</p>
			
			<h4>Zitsi</h4>
			<p><img src="http://www.rpnix.com/pics/Image_pdzit.gif" alt="Prière à Zitsi" /><br />
			Traduction : Zitsi superbus tribu duce nos honoris tui nobiscum fidélibus tuis! <br />
			(fr) Zitsi, fier guide de notre tribue, fais nous l'honneur de ta présence à nous, tes plus fidèles serviteurs !</p>
			
	<? } ?>	<?php if ($_SESSION["rank"] < 5) { ?>
		<p>Vous n'avez pas le grade suffisant pour voir cette page.</p>
	<? } 
}
		else
		{?> <p>Vous devez être connecté pour voir cette page</p> <? }
	
	?>
	
<?php
}
?>