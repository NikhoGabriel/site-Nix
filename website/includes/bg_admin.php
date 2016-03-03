<?php function bg_admin () { ?>
	<?php global $db, $_SESSION, $_GET;
	
		$p_category = (isset($_GET['p2'])) ? $_GET['p2'] : false;
		$p_content = (isset($_GET['p3'])) ? $_GET['p3'] : false;
		$p_action = (isset($_GET['p4'])) ? $_GET['p4'] : false;
		
		$answer = $db->query('SELECT COUNT(*) AS categories FROM t_bg_category WHERE hasParent = 0 ORDER BY name') or die(mysql_error());
		$line = $answer->fetch() or die(mysql_error());
		$answer->closeCursor();
		
		$answer2 = $db->query('SELECT * FROM t_bg_category WHERE hasParent = 0 ORDER BY name') or die(mysql_error());
		//$line2 = $answer2->fetch() or die(mysql_error());
	?>
	<?php if ($_SESSION['connected']) { ?>
		<?php if ($_SESSION['rank'] >= 4) { ?>
			<h2>BackGround Général</h2>
			<p>Introduction : Cette page relate les différents points à connaitre pour encadrer sur Nix, cette liste évoluera selon les points ajoutés.</p>
			<hr style="border-color:black;">
			<?php if ($line["categories"] > 0) { ?>
				<?php
					$answer_index = $db->query('SELECT COUNT(*) AS categories FROM t_bg_category WHERE hasParent = 0 ORDER BY name') or die(mysql_error());
					$line_index = $answer_index->fetch() or die(mysql_error());
					$answer_index->closeCursor();
				?>
				<?php if ($line_index["categories"] > 0) { ?>
					<?php
						$answer2_index = $db->query('SELECT * FROM t_bg_category WHERE hasParent = 0 ORDER BY name') or die(mysql_error());
						//$line2_index = $answer2_index->fetch() or die(mysql_error());
					?>
					<?php while ( $line2_index = $answer2_index->fetch()) { ?>
						<?php
							include('includes/bg_admin_index.php');
							bg_admin_index($line2_index['uuid']);
						?>
					<?php } ?>
				<?php } else { ?>
					<span>Shit Happened lvl index</span>
				<?php } ?>
				<?php
					$answer_child = $db->query('SELECT COUNT(*) AS categories FROM t_bg_category WHERE name = "'.$p_category.'" ORDER BY name') or die(mysql_error());
					$line_child = $answer_child->fetch() or die(mysql_error());
					$answer_child->closeCursor();
				?>
				<?php if ($line_child["categories"] > 0) { ?>
					<hr style="border-color:black;">
					<?php
						$answer2_child = $db->query('SELECT * FROM t_bg_category WHERE name = "'.$p_category.'" ORDER BY name') or die(mysql_error());
						//$line2_child = $answer2_child->fetch() or die(mysql_error());
					?>
					<?php while ( $line2_child = $answer2_child->fetch()) { ?>
						<?php
							include('includes/bg_admin_child.php');
							bg_admin_child($line2_child['uuid']);
						?>
					<?php } ?>
				<?php } ?>
				<?php
					$answer_content = $db->query('SELECT COUNT(*) AS categories FROM t_bg_category WHERE name = "'.$p_category.'" ORDER BY name') or die(mysql_error());
					$line_content = $answer_content->fetch() or die(mysql_error());
					$answer_content->closeCursor();
				?>
				<?php if ($line_content["categories"] > 0) { ?>
					<hr style="border-color:black;">
					<?php
						$answer2_content = $db->query('SELECT * FROM t_bg_category WHERE name = "'.$p_category.'" ORDER BY name') or die(mysql_error());
						//$line2_content = $answer2_content->fetch() or die(mysql_error());
					?>
					<?php while ( $line2_content = $answer2_content->fetch()) { ?>
						<?php
							include('includes/bg_admin_content.php');
							bg_admin_content($line2_content['uuid']);
						?>
					<?php } ?>
				<?php } ?>
			<?php } else { ?>
				<p>Shit Happened lvl admin</p>
			<?php } ?>
		<?php } else { ?>
			<p>Shit Happened lvl rank</p>
		<?php } ?>
	<?php } else { ?>
		<p>Vous devez être connecté pour accéder à cette page</p>
	<?php } ?>
<?php } ?>