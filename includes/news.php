<?php function news($action)
{
	global $db, $_POST;

	define('rank_add_news', 5);
	define('rank_manage_news', 5);

	if ($action == 'disp')
	{
		$answer = $db->query('SELECT message FROM news ORDER BY date_post DESC LIMIT 1');
		$line = $answer->fetch();

		?>
		<p><?= $line['message']?></p>
		<?php
	}
	else if ($action == 'page')
	{
		if (isset($_POST['news']) && $_SESSION['rank'] >= rank_add_news)
		{
			$insert = $db->prepare('INSERT INTO news (date_post, author, message) VALUES (NOW(), ?, ?)');
			$insert->execute(array($_SESSION['id'], htmlspecialchars($_POST['news'])));
		}
		else if (isset($_POST['shirka']) && $_SESSION['rank'] >= rank_add_news)
		{
			shirka_say($_POST['shirka']);
		}
		?>
		<h3>Actualité de Nix</h3>

		<section id="news_list">
			<ul>
		<?php

		$answer = $db->query('SELECT n.message msg, n.date_post date, m.name name, m.id m_id, m.rank rank,m.pionier pionier,m.technician technician
					FROM news n
					INNER JOIN members m ON m.id = n.author
					ORDER BY n.date_post DESC
					LIMIT 3');
		while ($line = $answer->fetch())
		{
			$date = preg_replace('#^(.{4})-(.{2})-(.{2}) (.{2}):(.{2}):.{2}$#', 'Le $3/$2 à $4h$5', $line['date']);
			?>
				<li><?=$date?> par <a href="index.php?p=viewmember&amp;perso=<?=$line['m_id']?>" class="name<?= $line["rank"]?><? echo $tech?><? echo $pionnier?>"><?=$line['name']?></a> : <?=$line['msg']?></li>
			<?php
		}

		?>
			</ul>
		</section>
		
		<?php if ($_SESSION['rank'] >= rank_add_news) {?>
		<section class="add_news">
			<h4>Nouvelle :</h4>

			<form method="POST" action="index.php?p=news">
				<input type="text" name="news" maxlength="255" />
				<input type="submit" value="Envoyer" />
			</form>

			<h4>Shirka :</h4>

			<form method="POST" action="index.php?p=news">
				<input type="text" name="shirka" maxlength="255" />
				<input type="submit" value="Envoyer" />
			</form>
		</section><?php
		}
	}
	
	// Forums récents //
	
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
		
		if ($dispForum)
		{
		$answer->execute($params);
		
		?><h3>Activité récente</h3><?php
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
						 WHERE f.category = ? AND unread = 0
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
			
			?></table></li>
	<?php
}
?>
