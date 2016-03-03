<?php function guilds ()
{
	
	global $db, $_SESSION, $_GET;
		$answer = $db->query("SELECT * FROM members");
		$line = $answer->fetch();
		$answer->closeCursor();
	?>
	
	<h2>Groupes et Guildes de la région</h2>
	
	<?
	$akhora = $db->query("SELECT * FROM members WHERE akhora = 1 ORDER BY akhora_rank DESC, name ASC");
	$ombres = $db->query("SELECT * FROM members WHERE ombres = 1 ORDER BY ombres_rank DESC, name ASC");
	$revo = $db->query("SELECT * FROM members WHERE revo = 1 ORDER BY revo_rank DESC, name ASC");
	$aca = $db->query("SELECT * FROM members WHERE aca = 1 ORDER BY aca_rank DESC, name ASC");
	
	?>
	
	<h3>Le Donjon des Ombres</h3>
	<img src="pics/ombres.png" alt="Donjon des Ombres" class="guild" />
	<p>Donjon regroupant les Mages noirs et Mages corrompus.</p>
	
	<?
	
	if ($_SESSION['ombres_rank'] >= 4 OR  $_SESSION['rank'] >= 5) { 
	?>
		<form method="POST" action="index.php?p=guilds" class="grade_form"><p>Ajout de Membre :
	<input type="text" name="add_ombres" style="width: 130px;" placeholder="Nouveau Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['add_ombres']))	{	$db->exec('UPDATE members SET ombres = 1 WHERE name = \'' .$_POST['add_ombres']. '\''); 
	echo '<span class="error">Membre ajouté !</span>';}?>
	Gradation d'un Membre : 
	<input type="text" name="name_ombres" style="width: 120px;" placeholder="Nom du Membre" /> <input type="number" name="rank_ombres" min="0" max="5" step="1" style="width: 50px;" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['rank_ombres'] AND $_POST['name_ombres']))	{	$db->exec('UPDATE members SET ombres_rank = \'' .$_POST['rank_ombres']. '\' WHERE name = \'' .$_POST['name_ombres']. '\''); 
	echo '<span class="error">Rang changé !</span>';}?>
	Renvoie de Membre :
	<input type="text" name="remove_ombres" style="width: 120px;" placeholder="Nom du Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['remove_ombres']))	{	$db->exec('UPDATE members SET ombres = 0 WHERE name = \'' .$_POST['remove_ombres']. '\'');  $db->exec('UPDATE members SET ombres_rank = 0 WHERE name = \'' .$_POST['remove_ombres']. '\''); 
	echo '<span class="error">Membre retiré !</span>';}?>
	</p></form> <? } ?>
	<?php while ($line = $ombres->fetch()) { if ($line["rank"] == 0) { $rank= "0" ;} elseif ($line["rank"] == 1) { $rank= "1" ;} elseif ($line["rank"] == 2) { $rank= "2" ;} elseif ($line["rank"] == 3) { $rank= "3" ;} elseif ($line["rank"] == 4) { $rank= "4" ;}
	elseif ($line["rank"] == 5) { $rank= "5" ;} elseif ($line["rank"] == 6) { $rank= "6" ;} elseif ($line["rank"] == 7) { $rank= "7" ;} elseif ($line["rank"] == 8) { $rank= "titan" ;} elseif ($line["rank"] == 9) { $rank= "crea" ;}
	if ($line["removed"] == 1) { $rank = "del";} if ($line["ban"] == 1) { $rank="ban";} 
	if ($line["technician"] >= 1 ) { $rank = "tech";} ?>
	<table class="guilds">
		<tbody>
			<th>[G<?= $line['ombres_rank']?>]</th>
			<td><img class="magie_type" width="27" src="pics/rank<?echo $rank ?>.png" alt="Grade HRP" /> 
			<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?></td>
		</tbody>
	</table>
	<? } ?>
	
	<h3>Le Culte d'Akhora</h3>
	<img src="pics/akhora.png" alt="Culte d'Akhora" class="guild" />
	<p>Culte dévoué à une mystérieuse déesse.</p>
	<?if ($_SESSION['akhora_rank'] >= 4 OR  $_SESSION['rank'] >= 5) { 
	?>
		<form method="POST" action="index.php?p=guilds" class="grade_form"><p>Ajout de Membre : 
	<input type="text" name="add_akhora" style="width: 130px;" placeholder="Nouveau Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['add_akhora']))	{	$db->exec('UPDATE members SET ombres = 1 WHERE name = \'' .$_POST['add_akhora']. '\''); 
	echo '<span class="error">Membre ajouté !</span>';}?>
	Gradation d'un Membre :
	<input type="text" name="name_akhora" style="width: 120px;" placeholder="Nom du Membre" /> <input type="number" name="rank_akhora" min="0" max="5" step="1" style="width: 50px;" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['rank_akhora'] AND $_POST['name_akhora']))	{	$db->exec('UPDATE members SET akhora_rank = \'' .$_POST['rank_akhora']. '\' WHERE name = \'' .$_POST['name_akhora']. '\''); 
	echo '<span class="error">Rang changé !</span>';}?>
	Renvoie de Membre :
	<input type="text" name="remove_akhora" style="width: 120px;" placeholder="Nom du Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['remove_akhora']))	{	$db->exec('UPDATE members SET akhora = 0 WHERE name = \'' .$_POST['remove_akhora']. '\'');  $db->exec('UPDATE members SET akhora_rank = 0 WHERE name = \'' .$_POST['remove_akhora']. '\''); 
	echo '<span class="error">Membre retiré !</span>';}?>
	</p></form> <? } ?>
	<?php while ($line = $akhora->fetch()) { 
	if ($line["rank"] == 0) { $rank= "0" ;} elseif ($line["rank"] == 1) { $rank= "1" ;} elseif ($line["rank"] == 2) { $rank= "2" ;} elseif ($line["rank"] == 3) { $rank= "3" ;} elseif ($line["rank"] == 4) { $rank= "4" ;}
	elseif ($line["rank"] == 5) { $rank= "5" ;} elseif ($line["rank"] == 6) { $rank= "6" ;} elseif ($line["rank"] == 7) { $rank= "7" ;} elseif ($line["rank"] == 8) { $rank= "titan" ;} elseif ($line["rank"] == 9) { $rank= "crea" ;}
	if ($line["removed"] == 1) { $rank = "del";} if ($line["ban"] == 1) { $rank="ban";} 
	if ($line["technician"] >= 1 ) { $rank = "tech";}
	?>
	<table class="guilds">
		<tbody>
			<th>[G<?= $line['akhora_rank']?>]</th>
			<td><img class="magie_type" width="27" src="pics/rank<?echo $rank ?>.png" alt="Grade HRP" /> 
			<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?></td>
		</tbody>
	</table>
	<? } ?>
	
	<h3>Les Révolutionnaires</h3>
	<img src="pics/revolu.png" alt="Revolutionnaires" class="guild" />
	<p>Regroupement scientifique avec pour objectif : Améliorer le quotidien de tous.</p>
	<?if ($_SESSION['revo_rank'] >= 4 OR  $_SESSION['rank'] >= 5) {
	?>
		<form method="POST" action="index.php?p=guilds" class="grade_form"><p>Ajout de Membre :
	<input type="text" name="add_revo" style="width: 130px;" placeholder="Nouveau Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['add_revo']))	{	$db->exec('UPDATE members SET revo = 1 WHERE name = \'' .$_POST['add_revo']. '\''); 
	echo '<span class="error">Membre ajouté !</span>';}?>
	Gradation d'un Membre : 
	<input type="text" name="name_revo" style="width: 120px;" placeholder="Nom du Membre" /> <input type="number" name="rank_revo" min="0" max="5" step="1" style="width: 50px;" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['rank_revo'] AND $_POST['name_revo']))	{	$db->exec('UPDATE members SET revo_rank = \'' .$_POST['rank_revo']. '\' WHERE name = \'' .$_POST['name_revo']. '\''); 
	echo '<span class="error">Rang changé !</span>';}?>
	Renvoie de Membre :
	<input type="text" name="remove_revo" style="width: 120px;" placeholder="Nom du Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['remove_revo']))	{	$db->exec('UPDATE members SET revo = 0 WHERE name = \'' .$_POST['remove_revo']. '\'');  $db->exec('UPDATE members SET revo_rank = 0 WHERE name = \'' .$_POST['remove_revo']. '\''); 
	echo '<span class="error">Membre retiré !</span>';}?>
	</p></form> <? } ?>
	<?php while ($line = $revo->fetch()) { 
	if ($line["rank"] == 0) { $rank= "0" ;} elseif ($line["rank"] == 1) { $rank= "1" ;} elseif ($line["rank"] == 2) { $rank= "2" ;} elseif ($line["rank"] == 3) { $rank= "3" ;} elseif ($line["rank"] == 4) { $rank= "4" ;}
	elseif ($line["rank"] == 5) { $rank= "5" ;} elseif ($line["rank"] == 6) { $rank= "6" ;} elseif ($line["rank"] == 7) { $rank= "7" ;} elseif ($line["rank"] == 8) { $rank= "titan" ;} elseif ($line["rank"] == 9) { $rank= "crea" ;}
	if ($line["removed"] == 1) { $rank = "del";} if ($line["ban"] == 1) { $rank="ban";} 
	if ($line["technician"] >= 1 ) { $rank = "tech";}
	?>
	<table class="guilds">
		<tbody>
			<th>[G<?= $line['revo_rank']?>]</th>
			<td><img class="magie_type" width="27" src="pics/rank<?echo $rank ?>.png" alt="Grade HRP" /> 
			<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?></td>
		</tbody>
	</table>
	<? } ?>
	
	<h3>Le Personnel de l'Académie</h3>
	<img src="pics/aca.png" alt="Equipe Académique" class="guild" />
	<p>Personnel chargé d'apprendre la magie aux intérressés.</p>
	<?if ($_SESSION['aca_rank'] >= 4 OR  $_SESSION['rank'] >= 5) { 
	?>
		<form method="POST" action="index.php?p=guilds" class="grade_form"><p>Ajout de Membre :
	<input type="text" name="add_aca" style="width: 130px;" placeholder="Nouveau Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['add_aca']))	{	$db->exec('UPDATE members SET aca = 1 WHERE name = \'' .$_POST['add_aca']. '\''); 
	echo '<span class="error">Membre ajouté !</span>';}?>
	Gradation d'un Membre :
	<input type="text" style="width: 120px;" name="name_aca" placeholder="Nom du Membre" /> <input type="number" name="rank_aca" min="0" max="5" step="1" style="width: 50px;" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['rank_aca'] AND $_POST['name_aca']))	{	$db->exec('UPDATE members SET aca_rank = \'' .$_POST['rank_aca']. '\' WHERE name = \'' .$_POST['name_aca']. '\''); 
	echo '<span class="error">Rang changé !</span>';}?>
	Renvoie de Membre :
	<input type="text" name="remove_aca" style="width: 120px;" placeholder="Nom du Membre" /><input type="submit" value="Valider" />
	<?php if(!empty($_POST['remove_aca']))	{	$db->exec('UPDATE members SET aca = 0 WHERE name = \'' .$_POST['remove_aca']. '\''); $db->exec('UPDATE members SET aca_rank = 0 WHERE name = \'' .$_POST['remove_aca']. '\''); 
	echo '<span class="error">Membre retiré !</span>';}?>
	</p></form> <? } ?>
	<?php while ($line = $aca->fetch()) { 
	if ($line["rank"] == 0) { $rank= "0" ;} elseif ($line["rank"] == 1) { $rank= "1" ;} elseif ($line["rank"] == 2) { $rank= "2" ;} elseif ($line["rank"] == 3) { $rank= "3" ;} elseif ($line["rank"] == 4) { $rank= "4" ;}
	elseif ($line["rank"] == 5) { $rank= "5" ;} elseif ($line["rank"] == 6) { $rank= "6" ;} elseif ($line["rank"] == 7) { $rank= "7" ;} elseif ($line["rank"] == 8) { $rank= "titan" ;} elseif ($line["rank"] == 9) { $rank= "crea" ;}
	if ($line["removed"] == 1) { $rank = "del";} if ($line["ban"] == 1) { $rank="ban";} 
	if ($line["technician"] >= 1 ) { $rank = "tech";}
	?>
	<table class="guilds">
		<tbody>
			<th>[G<?= $line['aca_rank']?>]</th>
			<td><img class="magie_type" width="27" src="pics/rank<?echo $rank ?>.png" alt="Grade HRP" /> 
			<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?></td>
		</tbody>
	</table>
	<? } ?>
<?php
} ?>