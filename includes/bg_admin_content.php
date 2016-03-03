<?php function bg_admin_content ($content_uuid) { ?>
	<?php
		global $db, $_SESSION, $_GET;
		
		$p_category = (isset($_GET['p2'])) ? $_GET['p2'] : false;
		$p_content = (isset($_GET['p3'])) ? $_GET['p3'] : false;
		$p_action = (isset($_GET['p4'])) ? $_GET['p4'] : false;
	?>
	<?php if ($_SESSION['connected']) { ?>
		<?php if ($_SESSION['rank'] >= 4) { ?>
				<?php
					$answer_content = $db->query('SELECT COUNT(*) AS categories FROM t_bg_category WHERE uuid = '.$content_uuid.' ORDER BY name') or die(mysql_error());
					$line_content = $answer_content->fetch() or die(mysql_error());
					$answer_content->closeCursor();
				?>
				<?php if ($line_content["categories"] > 0) { ?>
					<?php
						$answer2_content = $db->query('SELECT * FROM t_bg_category WHERE uuid = '.$content_uuid.' ORDER BY name') or die(mysql_error());
						//$line2_content = $answer2_content->fetch() or die(mysql_error());
					?>
					<?php while ( $line2_content = $answer2_content->fetch()) { ?>
						<?php bg_admin_content_get_content($line2_content['uuid']); ?>
					<?php } ?>
				<?php } else { ?>
					<span>Shit Happened lvl admin content</span>
				<?php } ?>
		<?php } else { ?>
			<span>Shit Happened lvl rank</span>
		<?php } ?>
	<?php } else { ?>
		<span>Vous devez être connecté pour accéder à cette page</span>
	<?php } ?>
<?php } ?>

<?php function bg_admin_content_cur_content ($name) { ?>
	<?php
		global $db, $_SESSION, $_GET;
		
		$p_category = (isset($_GET['p2'])) ? $_GET['p2'] : false;
		$p_content = (isset($_GET['p3'])) ? $_GET['p3'] : false;
		$p_action = (isset($_GET['p4'])) ? $_GET['p4'] : false;
	?>
	<?php if ($_SESSION['connected']) { ?>
		<?php if ($_SESSION['rank'] >= 4) { ?>
			<span>- => <?php echo $name ?></span>
		<?php } else { ?>
			<span>Shit Happened lvl rank</span>
		<?php } ?>
	<?php } else { ?>
		<span>Vous devez être connecté pour accéder à cette page</span>
	<?php } ?>
<?php } ?>

<?php function bg_admin_content_link_content ($name) { ?>
	<?php
		global $db, $_SESSION, $_GET;
		
		$p_category = (isset($_GET['p2'])) ? $_GET['p2'] : false;
		$p_content = (isset($_GET['p3'])) ? $_GET['p3'] : false;
		$p_action = (isset($_GET['p4'])) ? $_GET['p4'] : false;
	?>
	<?php if ($_SESSION['connected']) { ?>
		<?php if ($_SESSION['rank'] >= 4) { ?>
			<a href="index.php?p=bg_admin&p2=<?php echo $p_category; ?>&p3=<?php echo $name; ?>">- <?php echo $name ?></a>
		<?php } else { ?>
			<span>Shit Happened lvl rank</span>
		<?php } ?>
	<?php } else { ?>
		<span>Vous devez être connecté pour accéder à cette page</span>
	<?php } ?>
<?php } ?>

<?php function bg_admin_content_get_content ($owner_uuid) { ?>
	<?php
		global $db, $_SESSION, $_GET;
		
		$p_category = (isset($_GET['p2'])) ? $_GET['p2'] : false;
		$p_content = (isset($_GET['p3'])) ? $_GET['p3'] : false;
		$p_action = (isset($_GET['p4'])) ? $_GET['p4'] : false;
	?>
	<?php if ($_SESSION['connected']) { ?>
		<?php if ($_SESSION['rank'] >= 4) { ?>
				<?php
					$answer_content = $db->query('SELECT COUNT(*) AS owners FROM t_bg_category_content WHERE fk_uuid_content = '.$owner_uuid.'') or die(mysql_error());
					$line_content = $answer_content->fetch() or die(mysql_error());
					$answer_content->closeCursor();
				?>
				<?php if ($line_content["owners"] > 0) { ?>
					<?php
						$answer2_content = $db->query('SELECT * FROM t_bg_category_content WHERE fk_uuid_category = '.$owner_uuid.'') or die(mysql_error());
						//$line2_content = $answer2_content->fetch() or die(mysql_error());
					?>
					<ul>
						<?php while ( $line2_content = $answer2_content->fetch()) { ?>
							<?php
								$answer3_content = $db->query('SELECT COUNT(*) AS contents FROM t_bg_content WHERE uuid = '.$line2_content['fk_uuid_content'].'') or die(mysql_error());
								$line3_content = $answer3_content->fetch() or die(mysql_error());
								$answer3_content->closeCursor();
							?>
							<?php if ($line3_content["contents"] > 0) { ?>
								<?php
									$answer4_content = $db->query('SELECT * FROM t_bg_content WHERE uuid = '.$line2_content['fk_uuid_content'].'') or die(mysql_error());
									//$line4_content = $answer4_content->fetch() or die(mysql_error());
								?>
								<?php while ( $line4_content = $answer4_content->fetch()){ ?>
									<li style="padding-left: 15px;">
										<?php if ($p_content == $line4_content['name']) { ?>
											<?php bg_admin_content_cur_content($line4_content['name']); ?>
										<?php } else { ?>
											<?php bg_admin_content_link_content($line4_content['name']); ?>
										<?php } ?>
									</li>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</ul>
				<?php } ?>
		<?php } else { ?>
			<span>Shit Happened lvl rank</span>
		<?php } ?>
	<?php } else { ?>
		<span>Vous devez être connecté pour accéder à cette page</span>
	<?php } ?>
<?php } ?>