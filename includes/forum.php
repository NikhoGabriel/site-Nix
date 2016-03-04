<?php function forum ()
{
	global $db, $_SESSION, $_GET, $_POST;

	//Ajout d'un message
	define('rank_add_post', 2);
	//Ajout d'un message avec balises
	define('rank_add_post_enriched', 2);
	//Suppression d'un message
	define('rank_del_post', 4);
	//Modification d'un message
	define('rank_modif_post', 5);

	//Affichage de tout les forums
	define('rank_disp_all_forum', 7);
	//Ajout d'un forum
	define('rank_add_forum', 2);
	//Ajout d'un forum important
	define('rank_add_forum_important', 5);
	//Suppression d'un forum
	define('rank_del_forum', 5);

	define('msgByPage', 10);

	//Affichage ou modification d'un forum

	if (isset($_GET['forum']))
	{
		//Vérification forum existant et droit d'accès

		$forum = intval($_GET['forum']);

		$answer = $db->prepare('SELECT c.rank AS rank, c.name AS c_name, f.name AS name, f.id AS id,f.important, f.rp,f.locked
					FROM forum_category AS c
					INNER JOIN forum_forum AS f
					ON c.id = f.category
					WHERE f.id= ?');
		$answer->execute(array($forum));
		$line = $answer->fetch();
		$answer->closeCursor();
		
		

		if ($line && ($line['rank'] <= $_SESSION['rank']))
		{
			$rank = $line['rank'];
			$categoryName = $line['c_name'];
			$forumName = $line['name'];

			//Ajout d'une réponse

			if ($_SESSION['rank'] >= rank_add_post && isset($_POST['action']) && $_POST['action'] == 'add'
			    && isset($_POST['post']) && strlen($_POST['post']) > 0)
			{
				$post = htmlentities($_POST['post']);

				$insert = $db->prepare("INSERT INTO forum_post VALUES('', ?, NOW(), ?, ?)");
				$insert->execute(array($post, $_SESSION['id'], $forum));
				$lastId = $db->lastInsertId();
	
				$update = $db->prepare('UPDATE forum_forum SET last_post = ? WHERE id = ?');
				$update->execute(array($lastId, $forum));

				$update = $db->prepare('UPDATE forum_unread SET unread = 1 WHERE forum_id = ?'); 
				$update->execute(array($forum));

				$answer = $db->prepare('SELECT COUNT(*) AS number FROM forum_post WHERE forum_id = ?');
				$answer->execute(array($forum));
				$line = $answer->fetch();
				$page = intval($line['number'] / msgByPage) + 1;
			}

			//Suppression d'une réponse

			if (isset($_POST['action']) && $_POST['action'] == 'del_post' && isset($_POST['post']) && intval($_POST['post']) != 0)
			{
				$answer = $db->prepare('SELECT p.id id, m.rank rank
							FROM forum_post p
							INNER JOIN members m ON m.id = p.user_id
							WHERE p.id = ?');
				$answer->execute(array(intval($_POST['post'])));
				$line = $answer->fetch();

				if (($_SESSION['rank'] >= rank_del_post && $_SESSION['rank'] >= $rank && $_SESSION['rank'] >= $line['rank']) || $_SESSION['id'] == $line['id'])
				{
					$delete = $db->prepare('UPDATE forum_post SET forum_id = 0 WHERE id = ?');
					$delete->execute(array(intval($_POST['post'])));
	
					$answer = $db->prepare('SELECT * FROM forum_post WHERE forum_id = ? ORDER BY post_date DESC');
					$answer->execute(array($forum));
					$line = $answer->fetch();
					$id = $line['id'];

					$update = $db->prepare('UPDATE forum_forum SET last_post = ? WHERE id = ?');
					$update->execute(array($id, $forum));
	
					?>Le message a bien été supprimé.<?php
				}
			}

			//Modification d'une réponse

			if (isset($_POST['action']) && $_POST['action'] == 'modif'
				&& isset($_POST['post_modif']) && intval($_POST['post_modif']) != 0
			    && isset($_POST['post']) && strlen($_POST['post']) > 0)
			{
				$post = intval($_POST['post_modif']);

				$answer = $db->prepare('SELECT p.id AS id, m.id AS m_id, m.rank AS rank
							FROM forum_post AS p
							INNER JOIN members AS m ON p.user_id = m.id
							WHERE p.id = ?');
				$answer->execute(array($post));
				$line = $answer->fetch();
				$answer->closeCursor();

				if ($line && (
					($_SESSION['rank'] >= rank_modif_post && $_SESSION['rank'] >= $line['rank'])
					|| $_SESSION['id'] == $line['m_id']
					)
				)
				{
					$post = htmlentities($_POST['post']);

					$update = $db->prepare('UPDATE forum_post SET post = ? WHERE id = ?');
					$update->execute(array($post, $line['id']));
				}
			}

			//Affichage du forum

			?><h3><?= $categoryName?> &gt; <img src="pics/forumrp_<?= $line['rp'] ?>.png" width="30px" alt=" " /> <? if ($line['rp'] ==1) { echo "[RP]";}
			if ($line['important'] == 1) { echo "[Important]";}?> <?= $forumName?></h3>
			<? if ($_SESSION['rank'] >= 5) { ?>
			<p><form method="POST" action="index.php?p=forum&amp;forum=<?= $forum?>" >
					<? if ($line['important'] == 0) { ?>
				<input type="submit" value="[I]" name="set_imp" style="color:gold;" /> <? } ?>
					<? if ($line['important'] == 1) { ?>
				<input type="submit" value="[I]" name="deset_imp" style="color:grey;" /><? } ?>
					<? if ($line['rp'] == 0) { ?>
				<input type="submit" value="[RP]" name="set_RP" style="color:green;" /><? } ?>
					<? if ($line['rp'] == 1) { ?>
				<input type="submit" value="[HRP]" name="set_HRP" style="color:gray;" /><? } ?>
				<? if ($line['lock'] == 1) { ?>
				<input type="submit" value="[V]" name="unlock" style="color:blue;" /><? } ?>
				<? if ($line['lock'] == 1) { ?>
				<input type="submit" value="[V]" name="lock" style="color:red;" /><? } ?>
				<?php
			
			if (isset($_POST['set_imp'])) { $db->exec("UPDATE forum_forum SET important = 1 WHERE id = $forum") ; echo "Sujet désormais en tête de liste !";}
			if (isset($_POST['deset_imp'])) { $db->exec("UPDATE forum_forum SET important = 0 WHERE id = $forum") ; echo "Sujet désormais d'importance classique !";}
			if (isset($_POST['set_RP'])) { $db->exec("UPDATE forum_forum SET rp = 1 WHERE id = $forum") ; echo "Sujet désormais RolePlay !";}
			if (isset($_POST['set_HRP'])) { $db->exec("UPDATE forum_forum SET rp = 0 WHERE id = $forum") ; echo "Sujet désormais Hors RolePlay !";}
			if (isset($_POST['lock'])) { $db->exec("UPDATE forum_forum SET locked = 1 WHERE id = $forum") ; echo "Sujet vérouillé !";}
			if (isset($_POST['unlock'])) { $db->exec("UPDATE forum_forum SET locked = 0 WHERE id = $forum") ; echo "Sujet dévérouillé !";}
			} 	?>
			</form></p>
			<table id="forum"><?php

			include('includes/formateText.php');

			$answer = $db->prepare('SELECT id FROM forum_unread WHERE forum_id = ? AND user_id = ?');
			$answer->execute(array($forum, $_SESSION['id']));

			if ($answer->fetch())
			{
				$update = $db->prepare('UPDATE forum_unread SET unread = 0 WHERE forum_id = ? AND user_id = ?');
				$update->execute(array($forum, $_SESSION['id']));
			}
			else if ($_SESSION['rank'] > 0)
			{
				$insert = $db->prepare("INSERT INTO forum_unread VALUES ('', ?, ?, 0)");
				$insert->execute(array($forum, $_SESSION['id']));
				
			}
			$answer->closeCursor();

			$page = (isset($_GET['page']) && intval($_GET['page']) > 0) ? intval($_GET['page']) : 1;
			$answer = $db->prepare('SELECT COUNT(*) AS number FROM forum_post WHERE forum_id = ?');
			$answer->execute(array($forum));
			$line = $answer->fetch();
			$answer->closeCursor();
			$number = $line['number'];
			$page = ((($page - 1) * msgByPage) < $number) ? $page : 1;

			$answer = $db->prepare('SELECT p.post AS post,p.post_date AS date, p.id AS p_id,
						m.name AS name, m.rank AS rank, m.id AS m_id, m.title AS title, m.rank AS m_rank,m.pionier pionier,m.technician technician
						FROM members AS m
						INNER JOIN forum_post AS p ON m.id = p.user_id
						WHERE p.forum_id = ?
						ORDER BY p.post_date
						LIMIT ' . msgByPage . ' OFFSET ' . (($page-1) * msgByPage));

			$answer->execute(array(intval($_GET['forum'])));
			$void = true;

			while ($line = $answer->fetch()) 
			{  $filename = 'pics/avatar/miniskin_' .$line['m_id']. '.png';if (file_exists($filename)) {$skin = $line['m_id'];} else {$skin = 'no';}
				$void = false;
				$post = preg_replace('#\n#', '<br />', $line['post']);
				$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}:.{2}):.{2}$#', 'Le $3/$2/$1 à $4', $line['date']);
				
				switch ($line ['m_rank']) { 
				case 0: $fofo= "0" ; break; case 1: $fofo= "1" ; break; case 2: $fofo= "2" ; break; case 3: $fofo= "3" ; break; case 4: $fofo= "4" ; break;
				case 5: $fofo= "5" ; break; case 6: $fofo= "6" ; break; case 7: $fofo= "7" ; break; case 8: $fofo= "8" ; break; case 9: $fofo= "9" ; break; }
				if ($line['pionier'] == 1) { $pionier = "-P";} else { $pionier = '';} if ($line['technician'] == 1) { $tech = "-T";} else { $tech = '';}
				
				if ($line['rank'] >= rank_add_post_enriched)
				{
					$post = formateText($post);
				}
				?>
				<tr width="100%" class="forumrank<? echo $fofo?>">
					<td><p><?= $post?>
					<?php if ($_SESSION['rank'] >= 5) { ?>
						<form method="POST" action="index.php?p=forum&amp;forum=<?= $forum?>">
							<input type="button" value="[X]" class="del_button"/>
							<input type="hidden" name="action" value="del_post" />
							<input type="hidden" name="post" value="<?= $line['p_id']?>" />
						</form>
						<?php } 


						if (($_SESSION['rank'] >= rank_modif_post && $_SESSION['rank'] >= $line['rank']) || $_SESSION['id'] == $line['m_id'])
						{?>
						<form method="POST" action="index.php?p=forum&amp;forum=<?= $forum?>">
							<input type="submit" value="[Modifier]" class="button" />
							<input type="hidden" name="action" value="modif_form" />
							<input type="hidden" name="post" value="<?= $line['p_id']?>" />
						</form>

					<?php } ?></p></td>
					<td class="post_aside">
						<p ><img src="pics/avatar/miniskin_<? echo $skin?>.png" width="20px" /> <a href="index.php?p=viewmember&perso=<?= $line['m_id']?>" class="name<?= $line['rank']?><? echo $tech ?><? echo $pionier?>" >
						
						<? //Affichage du titre "Pionier" //
						if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?>
				
						<?= $line['name']?></a></p>
						<p><?= $date?></p>
					</td>
				</tr>
				<?php
			}

			if ($void)
			{
				?><tr><td>Aucun message dans ce forum</td></tr><?php
			}
			else
			{
			?>
				<script>
					var delButtons = document.getElementsByClassName('del_button');

					for (var i = 0; i < delButtons.length; i++)
					{
						delButtons[i].addEventListener('click', function (e) {
							if (confirm('Êtes vous sûr de vouloir supprimer ce message ?'))
							{
								e.target.parentNode.submit();
							}
						}, false);
					}
				</script>
			<?php
			}

			?></table>
			<p id="nav_forum"><?php if ($page > 1){ ?>
				<a href="index.php?p=forum&amp;forum=<?= $forum?>&amp;page=<?= $page - 1?>">Page précédente</a>
			<?php } ?> Page <?= $page?> <?php if (($page * msgByPage) < $number){ ?>
				<a href="index.php?p=forum&amp;forum=<?= $forum?>&amp;page=<?= $page + 1?>">Page suivante</a>
			</p>
			   <?php }

			//Formulaire de réponse
			
			if ($_SESSION['rank'] >= rank_add_post)
			{
				if (isset($_POST['action']) && $_POST['action'] == 'modif_form' && isset($_POST['post']) && intval($_POST['post']) != 0)
				{
					$post = intval($_POST['post']);

					$answer = $db->prepare('SELECT p.id AS id, p.post AS post, m.id AS m_id, m.rank AS rank
								FROM forum_post AS p
								INNER JOIN members AS m ON p.user_id = m.id
								WHERE p.id = ?');

					$answer->execute(array($post));
					$line = $answer->fetch();
					$answer->closeCursor();

					if ($line &&
					(($_SESSION['rank'] >= rank_modif_post && $_SESSION['rank'] >= $line['rank']) || $_SESSION['id'] == $line['m_id']))
					{
						$modif = true;
						$text = $line['post'];
						$id = $line['id'];
					}
					else
					{
						$modif = false;
					}
				}
			?>
				<form method="POST" action="index.php?p=forum&amp;forum=<?= $_GET['forum']?>" id="add_post">
					<label for="post_input"><?php echo ($modif) ? 'Modifier :' : 'Répondre :';?></label><br />
					<textarea name="post" id="post_input"><?php echo ($modif) ? $text : '';?></textarea><br />
					<input type="submit" value="<?php echo ($modif) ? 'Modifier' : 'Répondre';?>" />
					<input type="reset" value ="Annuler" />
					<input type="hidden" name="action" value="<?php echo ($modif) ? 'modif' : 'add';?>" />
					<?php if ($modif) { ?><input type="hidden" name="post_modif" value="<?= $id?>" /><?php } ?>
				</form>
			<?php
			}
		}
		else if ($line)
		{
			?><p>Vous n'avez pas accès à cette page.</p><?php
		}
		else
		{
			?><p>Cette page n'existe pas.</p><?php
		}
	}

	//Affichage des forums ou d'une catégorie

	else
	{
		if (isset($_GET['category']))
		{
			$category = intval($_GET['category']);

			if ($_SESSION['rank'] >= 6)
			{
				$answer2 = $answer = $db->prepare('SELECT * FROM forum_category WHERE id = ?');
				$answer2->execute(array($category));
				$params = array ($category);
			}
			else
			{
				$answer2 = $answer = $db->prepare('SELECT * FROM forum_category WHERE rank <= ? AND id = ?');
				$answer2->execute(array($_SESSION['rank'], $category));
				$params = array ($_SESSION['rank'], $category);
			}


			$dispForum = ($answer2->fetch()) ? true : false;
			$categoryPage = true;

			//Ajout d'un forum

			if ($dispForum && isset($_POST['subject']) && strlen($_POST['subject']) > 0 && isset($_POST['post']) && strlen($_POST['post']) > 0
			    && isset($_POST['action']) && $_POST['action'] == 'add' && $_SESSION['rank'] >= rank_add_forum)
			{
				$important = (isset($_POST['important']) && $_SESSION['rank'] >= rank_add_forum_important) ? true : false;
				
				$insert = $db->prepare("INSERT INTO forum_forum VALUES ('', ?, ?, 0, ?, 0, 0)");
				$insert->execute(array(htmlentities($_POST['subject']), $category, $important));
				$idForum = $db->lastInsertId();
				$insert->closeCursor();

				$insert = $db->prepare("INSERT INTO forum_post VALUES ('', ?, NOW(), ?, ?)");
				$insert->execute(array(htmlentities($_POST['post']), $_SESSION['id'], $idForum));
				$idPost = $db->lastInsertId();
					
				$update = $db->prepare('UPDATE forum_forum SET last_post=? WHERE id=?');
				$update->execute(array($idPost, $idForum));

				$answer = $db->query("SELECT id FROM members WHERE activate = 'true'");

				while ($line = $answer->fetch())
				{
					$insert = $db->prepare("INSERT INTO forum_unread VALUES ('', ?, ?, 1)");
					$insert->execute(array($idForum, $line['id']));
				}

				$update = $db->prepare('UPDATE forum_unread SET unread = 0 WHERE user_id = ?');
				$update->execute(array($_SESSION['id']));

				?>
				<p>Votre forum a bien été créé. Vous pouvez y accéder <a href="index.php?p=forum&forum=<?=$idForum?>">ici</a>.</p>
				<?php

				$dispForum = false;
			}
			else if (!$dispForum)
			{
				?>
				<p>Cette catgéorie n'existe pas ou vous n'y avez pas accès.</p>
				<?php
			}
		}
		else
		{
			if ($_SESSION['rank'] >= rank_disp_all_forum)
			{
				$answer = $db->prepare('SELECT * FROM forum_category ORDER BY rank, name');
				$params = array ();
			}
			else
			{
				$answer = $db->prepare('SELECT * FROM forum_category WHERE rank <= ? ORDER BY rank, name');
				$params = array ($_SESSION['rank']);
			}
			$dispForum = true;
			$categoryPage = false;
		}

		//Suppression d'un forum

		if (isset($_POST['action']) && $_POST['action'] == 'del_forum' && isset($_POST['forum']) && $_SESSION['rank'] >= rank_del_forum)
		{
			$answer = $db->prepare('SELECT id FROM forum_forum WHERE id = ?');
			$answer->execute(array(intval($_POST['forum'])));

			if ($line = $answer->fetch())
			{
				$id = $line['id'];
				$delete = $db->prepare('UPDATE forum_forum SET category  = 0 WHERE id = ?');
				$delete->execute(array($id));
				$delete->closeCursor();

				?>
				<p>Le forum a bien été supprimé.</p>
				<?php

				$dispForum = false;
			}
		}

		//Affichage des forums

		if ($dispForum)
		{
		$answer->execute($params);
		
		?><h3>Forum</h3><?php

		if (!$categoryPage)
		{
			?>
				<ul id="categories">
			<?php
		}

		while ($line = $answer->fetch())
		{
			//Affichage de l'en-tête de la catégorie

			if ($categoryPage)
			{
				?><h4><?= $line['name']?></h4><?php
				$answer2 = $db->prepare('SELECT f.id AS id, f.name AS f_name, f.important important, p.post_date AS date, m.id AS m_id, m.name AS m_name, m.rank AS rank,m.title AS title,m.pionier AS pionier, u.unread AS unread
						 FROM forum_post AS p
						 RIGHT JOIN forum_forum AS f ON f.last_post = p.id
						 LEFT JOIN members AS m ON m.id = p.user_id
						 LEFT JOIN forum_unread AS u ON ? = u.user_id AND f.id = u.forum_id
						 WHERE f.category = ? 
						 ORDER BY f.important DESC, p.post_date DESC');
					
					 if ($line['pionier'] == 1) { $rank = "Pionier";} else {  $rank = $line['rank'] ;}
			}
			else
			{
				?>
				<li class="forum_category"><p><a href="index.php?p=forum&amp;category=<?= $line['id']?>"><?echo $rank?> <?= $line['name']?></a></p>
				
				<img style="margin-left: 20px;" src="pics/forumcat_<?= $line['id']?>.png" alt=" " class="guild" />
				
				<?php
				$answer2 = $db->prepare('SELECT f.id AS id, f.name AS f_name, f.important important,f.rp rp, p.post_date AS date, m.id AS m_id, m.name AS m_name, m.rank AS rank, m.title AS title, m.pionier pionier,m.technician technician, u.unread AS unread
						 FROM forum_post AS p
						 RIGHT JOIN forum_forum AS f ON f.last_post = p.id
						 LEFT JOIN members AS m ON m.id = p.user_id
						 LEFT JOIN forum_unread AS u ON ? = u.user_id AND f.id = u.forum_id
						 WHERE f.category = ? 
						 ORDER BY f.important DESC, p.post_date DESC
						 LIMIT 10');
			}

			?><table width="100%" class="forum" cellspacing="0" cellpadding="0">
				<tr class="head_table">
					<th>Sujet</th>
					<th class="last_post">Dernière participation</th>
				</tr>
			<?php

			//Affichage des forums de la catégorie

			$answer2->execute(array($_SESSION['id'], $line['id']));
			$void = true;

			while ($line2 = $answer2->fetch())
			{	$filename = 'pics/avatar/miniskin_' .$line2['m_id']. '.png';if (file_exists($filename)) {$skin = $line2['m_id'];} else {$skin = 'no';}
				$void = false;
				if ($line2['pionier'] == 1) { $pionier = "-P";} else { $pionier = '';} if ($line2['technician'] == 1) { $tech = "-T";} else { $tech = '';}
				
				?><tr>
					<td
					<?php echo (isset($line2['unread']) && $line2['unread']) ? 'class="unread" ' : 'class="read" '; ?>
					><img src="pics/forumrp_<?= $line2['rp'] ?>.png" width="30px" alt=" " /> <a href="index.php?p=forum&amp;forum=<?= $line2['id']?>"><?php if ($line2['rp'] == 1) { echo "[RP] ";}
					echo ($line2['important']) ? '[Important] ':'';?><?= $line2['f_name']?></a><?php

				if ($_SESSION['rank'] >= rank_del_forum)
				{
					?><form method="POST" action="index.php?p=forum" class="del_form">
						<input type="button" value="[X]" class="del_button"/>
						<input type="hidden" name="action" value="del_forum" />
						<input type="hidden" name="forum" value="<?= $line2['id']?>" />
					</form><?php
				}

				$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}:.{2}):.{2}$#', 'Le $3/$2/$1 à $4', $line2['date']);

				if ($line2['rank'])
				{
				?></td>
				<td
				<?php echo (isset($line2['unread']) && $line2['unread']) ? 'class="unread" ' : ' '; ?>
				><img src="pics/avatar/miniskin_<? echo $skin?>.png" width="20px" /> <a href="index.php?p=viewmember&perso=<?= $line2['m_id']?>" class="name<?= $line2['rank']?><?echo $tech ?><? echo $pionier?>">
				
				<? //Affichage du titre "Pionier" //
				if ($line2['pionier'] == 1) { echo "Pionier";} else { echo $line2['title'] ;}?>
				
				<?= $line2['m_name']?></a> <br /> <?= $date?></td>
				</tr><?php
				}
			}

			if ($void)
			{
				?><tr><td>Catégorie vide.</td></tr><?php
			}

			
			?></table></li><?php

			if ($categoryPage && $_SESSION['rank'] >= rank_add_forum)
			{
				//Nouveau forum
				
				?>
					<form method="POST" action="index.php?p=forum&category=<?= $category?>" id="add_forum">
						<h4>Créer un forum :</h4>
						<label for="subject">Sujet :</label><input type="text" name="subject" id="subject"><br /><?php if ($_SESSION['rank'] >= rank_add_forum_important) { ?>
						<label for="important">Important :</label><input type="checkbox" name="important" id="important"><br />
						<?php } ?>
						<label for="post">Premier message</label><br />
						<textarea name="post" id="post"></textarea><br />
						<input type="submit" value="Envoyer" />
						<input type="reset" value="Annuler" />
						<input type="hidden" name="action" value="add" />
					</form>
				<?php
				

			}
			
			
			$answer2->closeCursor();

		}

		?></ul><?php

		?>
		<script>
			var delButtons = document.getElementsByClassName('del_button');

			for (var i = 0; i < delButtons.length; i++)
			{
				delButtons[i].addEventListener('click', function (e) {
					if (confirm('Êtes vous sûr de vouloir supprimer ce forum ?'))
					{
						e.target.parentNode.submit();
					}
				}, false);
			}
		</script>
		<?php
		}
	}
}
?>
