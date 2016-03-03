<?php function chrono ()
{
	global $db, $_SESSION;

	$answer = $db->query("SELECT COUNT(*) AS number FROM chrono");
	$line = $answer->fetch();
	$answer->closeCursor();
	
	$answer = $db->query("SELECT * FROM chrono WHERE cycle_g >= 0 ORDER BY cycle_g DESC, revo_g DESC, an DESC, id DESC");
	?>
	
	<?if ($_SESSION["rank"] >= 5) {	?>
	
	<h3>Frise Chronologique de l'Univers</h3>
	
	<p>
		Préambule et repères :<br /><br />
			1 Cycle galactique représente le temps qu'elle met à faire un tour sur elle - même,  soit 200 Révolutions Galactiques.<br /><br />
			1 Revolution Galactique représente le temps que met un système stellaire à tourner autour de son trou noir central, soit 1 500 ans Terriens.<br /><br />
			1 an Terrien représente la révolution de la Terre autour de son étoile, soit 365 cycles.<br /><br />
			1 Cycle représente les différentes phases de jour et nuit. Ces derniers durant 20 minutes à eux deux, durant 24 heures.
	</p>
	
	<? if ($_SESSION["name"] == "Nikho" OR $_SESSION["name"] == "Eftarthadeth") {?>
<p>
	<form  method="POST" action="index.php?p=chrono">
		Cycle Galactique : <input type="text" name="cycle_g"/> 
		Révolution Galactique :<input type="text" name="revo_g"/> 
		An : <input type="text" name="an"/><br/>
		Event : <textarea type="text" name="event"> </textarea>
		<input type="submit" value="Envoyer" />
	</form>
</p>
	
<?php	
		if(!empty($_POST['cycle_g']) AND !empty($_POST['revo_g']) AND !empty($_POST['an']) AND !empty($_POST['event'])){$db->exec('INSERT INTO chrono (cycle_g, revo_g, an, event) VALUES (\''.$_POST['cycle_g'].'\', \''.$_POST['revo_g'].'\', \''.$_POST['an'].'\', \''.$_POST['event'].'\')');} ;}


		while ($line = $answer->fetch()) { 
		switch ($line ["cycle_g"]) {case 0: $cycle_g = "Zéro" ; break; case 1: $cycle_g = "Premier" ; break; case 2: $cycle_g = "Second" ; break; case 3: $cycle_g = "Troisième" ; break; case 4: $cycle_g = "Quatrième" ; break; }
		if ($line ["revo_g"] == 0) { $revo_g = "Révolution Zéro"; } elseif ($line ["revo_g"] == 1) { $revo_g = "1ère Révolution"; } else { $revo_g = "".$line['revo_g']."e Révolution"; }
?>
<table style="border-collapse: collapse; width: 100%;">
	<tr>
	<td class="chrono<?= $line['cycle_g']?>">
			<li>Cycle Galactique <? echo $cycle_g ?></li>
			<li><? echo $revo_g ?></li>
			<li>An <?= $line["an"]?></li>
			<li><?= $line["event"]?></li>
	</td>
	</tr>
</table>
	<?php } }
	elseif ($_SESSION["rank"] <= 4) { ?>
	<p>Vous n'avez pas le grade suffisant pour consulter cette page.</p>
	<? } else {?>
	<p>Vous devez être connecté pour consulter cette page.</p>
<?
} }
?>