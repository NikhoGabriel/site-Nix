<?php function incantations ()
{
	global $db, $_SESSION, $_POST;

	if ($_SESSION['connected']) {
		
			$answer = $db->query('SELECT COUNT(*) AS incans FROM incan_get WHERE user_id = '.$_SESSION['id'].'');
			$line = $answer->fetch();
			$answer->closeCursor();
			
			$answer = $db->query('SELECT il.id AS il_id, il.cost, il.name, il.desc, il.level, il.type, ig.user_id, ig.id, ig.incan_id, ig.valid, m.id
			FROM incan_list il
			RIGHT JOIN incan_get ig ON ig.incan_id = il.id
			LEFT JOIN members m ON m.id = ig.user_id
			WHERE ig.user_id ='.$_SESSION['id'].'
			ORDER BY il.level DESC');
	
	?>
		<h3>Sorts et Incantations de mon Personnage</h3>
		<p>
			Ici sont regroupé les différents sorts que votre personnage aura appris durant son aventure sur Nix.<br />
		<? if ($_SESSION['rank'] >= 4) { ?><br />
		<a href="index.php?p=incantations_admin">Aller à la vision administrative de la page</a> <? } ?>
		</p>

		<p>
			<form action="index.php?p=incantations" method="POST">
				Ajout d'un nouveau sort : <input type="text" name="new"/>
				<input type="submit" value="Envoyer"/>
			</form>
			<?if(!empty($_POST['new'])) { $answer2 = $db->query('SELECT COUNT(*) AS exist FROM incan_list WHERE name = \''.$_POST['new'].'\' '); $line2 = $answer2->fetch();
				if ($line2["exist"] == 1) { $answer3 = $db->query('SELECT * FROM incan_list WHERE name = \''.$_POST['new'].'\' '); $line3 = $answer3->fetch();
			 $incantation = $line3['id']; $answer4 = $db->query('SELECT COUNT(*) AS verify FROM incan_get WHERE  incan_id = \''.$incantation.'\' AND user_id = \'' .$_SESSION['id']. '\''); $line4 = $answer4->fetch();
					if ($line4['verify'] == 0) {
			$db->exec('INSERT INTO incan_get (user_id, incan_id) VALUES (\'' .$_SESSION['id']. '\' , \'' .$incantation. '\')');
				echo "Félicitations ! Vous avez pris connaissance d'un nouveau sort ! ";} 
				else { echo "<span class='error'>Vous connaissez déjà ce sort.</span>";} }
			else { echo "<span class='error'>Désolé mais cette incantation n'existe pas.</span>";}	}
			?>
		</p>
	<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; margin-left: 25%; width: 640px;">
		<tbody>
			<tr>
				<td>
					<img src="includes/img/magiepapertop.png" alt=" " />
				</td>
			</tr>
			<tr>
				<td>
					<table background="includes/img/magiepapercenter.png">
						<tbody>
	<? if ($line["incans"] > 0) { 
	while ($line = $answer->fetch()) { 
		switch ($line['level']) { case 8: $level = "X"; break;	case 7:  $level = "S"; break; case 6:  $level = "A"; break; case 5:  $level = "B"; break; case 4:  $level = "C"; break; case 3:  $level = "D"; break; 
			case 2:  $level = "E"; break; case 1:  $level = "F"; break;}
		switch ($line['type']) {
			case 13: $type = "Terre" ; break; case 12: $type = "Psy" ; break; case 11: $type = "Ombre" ; break; case 10:  $type = "Nature" ; break; case 9:  $type = "Metal" ; break;
			case 8: $type = "Lumiere" ; break; case 7: $type = "Glace" ; break; case 6: $type = "Feu" ; break; case 5: $type = "Energie" ; break;
			case 4: $type = "Eau" ; break; case 3: $type = "Chaos" ; break; case 2: $type = "Arcane" ; break; case 1: $type = "Air" ; break; case 0: $type = "Unknow" ; break; }
	?>
							<tr>
								<td class="magie_tab"  style="text-align: center">
									<? if ($line['valid'] == 1) { ?>
									<img class="magie" src="includes/img/magie/Magie_<? echo $level?>.png" alt="" /> <img class="magie_type" width="49" src="includes/img/magie/Magie_<? echo $type?>.png" alt=" " />
									<? } else { ?>
									<img class="magie_type" width="49" src="includes/img/magie/Magie_Unknow.png" alt=" " />
									<? } ?>
								</td>
							</tr>
							<tr>
								<td class="magie_tab">
									<p style="text-align: center" class="name1">
										<?= $line["name"]?>
									</p>
								</td>
							</tr>
								<? if ($line['valid'] == 1) { ?>
							<tr>
								<td class="magie_tab">
									<p style="text-align: center">
										<?= $line["desc"] ?>
									</p>
								</td>
							</tr>
							<tr>
								<td class="magie_tab">
									<p style="text-align: center">
										<?= $line['cost']?> Points Magiques
									</p>
								</td>
							</tr>
								<? } else {?>
							<tr>
								<td class="magie_tab">
									<p style="text-align: center">
										Vous ne connaissez pas encore le rôle de ce sort.
									</p>
								</td>
							</tr>
								<?} } } else { ?>
							<tr>
								<td class="magie_tab">
									<p style="text-align: center" class="name1">
										Votre personnage ne connait aucun sort ni incantation pour le moment.
									</p>
								</td>
							</tr>
								<? } ?>
						</tbody>
					</table>
				</td>
				<tr>
						<td>
							<img src="includes/img/magiepapebottom.png" alt="" />
						</td>
					</tr>
			</tr>
		</tbody>
	</table>
	<?	} else { ?>
	<p>Vous devez être connecté pour accéder à cette page</p>
<?
} }
?>