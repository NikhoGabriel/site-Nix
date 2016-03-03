<?php function incantations_admin ()
{
	global $db, $_SESSION, $_POST;

?>
	<? if ($_SESSION['connected']) {
	if ($_SESSION['rank'] >= 4) { 
	?>
	<h2> Vision Administrative</h2>
		<p>Ici vous pourrez valider les sorts des joueurs en appuyant simplement sur le bouton "valider" correspondant.<br />
		<br />
		<a href="index.php?p=incantations">Revenir à la page de sort personnelle</a><br />
		<br />
		<form method="POST" action="index.php?p=incantations_admin">
		Recherche par Membre : <input type="text" name="m" /> <input type="submit" value="Rechercher" />
		</form>
		</p>
		<table cellspacing="0" cellpadding="0" style=" border-collapse: collapse; margin-left: 25%; width: 640px;">
			<tbody>
				<tr>
					<tr>
						<td>
							<img src="includes/img/magiepapertop.png" alt=" " />
						</td>
					</tr>
					<td background="includes/img/magiepapercenter.png">
						<?
							$answer6 = $db->query('SELECT COUNT(*) incan_get');
							$answer6 = $db->query('SELECT ig.id AS g_id, ig.user_id AS user, ig.incan_id, ig.valid, il.id AS i_id, il.name as i_name, il.desc, il.level, il.type, m.id AS m_id, m.name AS nom, m.title, m.rank
							FROM incan_get ig
							RIGHT JOIN incan_list il ON il.id = ig.incan_id
							LEFT JOIN members m ON m.id = ig.user_id
							WHERE ig.id > 0
							ORDER BY  valid ASC, m.name ASC, il.level DESC, il.type DESC, i_name ASC');
							
							if (!empty($_POST['m'])) {
							
							$answer6 = $db->query('SELECT ig.id AS g_id, ig.user_id AS user, ig.incan_id, ig.valid, il.id AS i_id, il.name as i_name, il.desc, il.level, il.type, m.id AS m_id, m.name AS nom, m.title, m.rank
							FROM incan_get ig
							RIGHT JOIN incan_list il ON il.id = ig.incan_id
							LEFT JOIN members m ON m.id = ig.user_id
							WHERE ig.id >0 AND m.name = \''.$_POST['m'].'\'
							ORDER BY  valid ASC, m.name ASC, il.level DESC, il.type DESC, i_name ASC');
								
							}
							$line6 = $answer6->fetch();
						?>
						<table width="100%">
							<tbody>
							<?
							while ($line6 = $answer6->fetch()) {
							switch ($line6['level']) { case 8: $level2 = "X"; break;	case 7:  $level2 = "S"; break; case 6:  $level2 = "A"; break; case 5:  $level2 = "B"; break; case 4:  $level2 = "C"; break; case 3:  $level2 = "D"; break; 
								case 2:  $level2 = "E"; break; case 1:  $level2 = "F"; break;}
							switch ($line6['type']) {
								case 13: $type2	= "Terre" ; break; case 12: $type2 = "Psy" ; break; case 11: $type2 = "Ombre" ; break; case 10:  $type2 = "Nature" ; break; case 9:  $type2 = "Metal" ; break;
								case 8: $type2 = "Lumiere" ; break; case 7: $type2 = "Glace" ; break; case 6: $type2 = "Feu" ; break; case 5: $type2 = "Energie" ; break;
								case 4: $type2 = "Eau" ; break; case 3: $type2 = "Chaos" ; break; case 2: $type2 = "Arcane" ; break; case 1: $type2 = "Air" ; break; case 0: $type2 = "Unknow" ; break; }
							?>
								<tr>
									<td class="magie_tab">
										<p style="text-align: center" class="name<?= $line6['rank']?>">
											<?= $line6['title']?> <?= $line6['nom']?>
										</p>
									</td>
								</tr>
								<tr>
									<td class="magie_tab" style="text-align: center">
										<img class="magie" src="includes/img/magie/Magie_<? echo $level2?>.png" alt="" /> <img class="magie_type" width="49" src="includes/img/magie/Magie_<? echo $type2?>.png" alt=" " />
									</td>
								</tr>
								<tr>
									<td class="magie_tab">
										<p style="text-align: center" class="name1">
											<?= $line6["i_name"]?>
										</p>
									</td>
								</tr>
								<tr>
									<td class="magie_tab">
										<?
											if ($line6['valid'] == 0) { $valid = "<span style='color:red'>Non maitrisé.</span>";} else  { $valid = "<span style='color:green'>Maitrisé.</span>";}
										?>
										<p style="text-align: center">
										Etat : <? echo $valid ?>
										<? if ($line6['valid'] == 0) { ?>
											<form style="padding-left: 50%;" method="POST" action="index.php?p=incantations_admin">
												<input type="submit" name="ok<?= $line6['g_id']?>" style="color: green" value="[Valider]" id="upgrade_button" />
											</form>
											<? if(isset($_POST['ok'.$line6['g_id'].''])) { echo '<br /><span class="error">Sort validé !</span>';
											$db->exec("UPDATE incan_get SET valid = 1 WHERE id = \"".$line6['g_id']."\" ");	
											} } ?>
										</p>
									</td>
								</tr><? } ?>
							</tbody>
							</table> 
					<tr>
						</td>
					</tr>
					<tr>
						<td>
							<img src="includes/img/magiepapebottom.png" alt=" " />
						</td>
					</tr>
				</tr>
			</tbody>
		</table>
	<? } 
		else { ?>
		<p>Vous n'avez pas le grade suffisante pour consulter cette page</p>
		<? }} else { ?>
	<p>Vous devez être connecté pour accéder à cette page</p>
<?
} }
?>