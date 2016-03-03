<?php function loggedIn ()
{
	global $db;

	?>
	
	<?php if ($_SESSION["rank"] >= 5) { ?>
	
	
	<div class="navtitle">Activité Membres</div>
		<ul class="nav">
			Joueurs connectés :
			
	<?php

	$answer1 = $db->query('SELECT *
				FROM members
				WHERE ADDDATE(last_action, INTERVAL 15 MINUTE) > NOW()
				ORDER BY rank desc, name asc');
	$answer2 = $db->query('SELECT *
				FROM members
				WHERE ADDDATE(last_action, INTERVAL 1 DAY) > NOW() AND ADDDATE(last_action, INTERVAL 30 MINUTE) < NOW()
				ORDER BY rank desc, name asc');

	$void = true;

	while ($line = $answer1->fetch())
	{

		switch($line ["rank"] )
				{
					
					case 9:
					$img = "rankcrea.png" ;
					break;
					
					case 8:
					$img = "ranktitan.png" ;
					break;
					
					case 7:
					$img = "rank7.png" ;
					break;
					
					case 6:
					$img = "rank6.png" ;
					break;
					
					case 5:
					$img = "rank5.png" ;
					break;
					
					case 4:
					$img = "rank4.png" ;
					break;
					
					case 3:
					$img = "rank3.png" ;
					break;
					
					case 2:
					$img = "rank2.png" ;
					break;
					
					case 1:
					$img = "rank1.png" ;
					break;
					
					case 0:
					$img = "rank0.png" ;
					break;
				}
				if ($line["technician"] == 1) { $img = "ranktech.png" ;}
				if ($line["ban"] == 1) { $img = "rankban.png" ;}
				if ($line["removed"] == 1) { $img = "rankdel.png" ;}
				
		switch($line ['invisible'])
		{
			case 1:
			$vanish = "[V]";
			break;
			
			case 0:
			$vanish = ' ';
			break;
		}

		
		?>
			<li class="navbg2" style="list-style-type: none; padding: 10;"><img class="magie_type" width="27" src="pics/<?php echo $img ?>" /><a href="index.php?p=viewmember&perso=<?= $line['id']?>" title="<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?>"> <?= $line['name']?></a> <? echo $vanish ?></li>
		<?php
	}
	?>
		<br />
		Passés récemment :
		

	<?php
	$void = true;

	while ($line = $answer2->fetch())
	{

		switch($line ["rank"] )
				{
					case 9:
					$img = "rankcrea.png" ;
					break;
					
					case 8:
					$img = "ranktitan.png" ;
					break;
					
					case 7:
					$img = "rank7.png" ;
					break;
					
					case 6:
					$img = "rank6.png" ;
					break;
					
					case 5:
					$img = "rank5.png" ;
					break;
					
					case 4:
					$img = "rank4.png" ;
					break;
					
					case 3:
					$img = "rank3.png" ;
					break;
					
					case 2:
					$img = "rank2.png" ;
					break;
					
					case 1:
					$img = "rank1.png" ;
					break;
					
					case 0:
					$img = "rank0.png" ;
					break;
				}
				
				if ($line["technician"] == 1) { $img = "ranktech.png" ;}
				if ($line["ban"] == 1) { $img = "rankban.png" ;}
				if ($line["removed"] == 1) { $img = "rankdel.png" ;}
				
				switch($line['invisible'])
		{
			case 1:
			$vanish = "[V]";
			break;
			
			case 0:
			$vanish = ' ';
			break;
		}


		?>
			<li class="navbg2" style="list-style-type: none; padding: 10;"><img class="magie_type" width="27" src="pics/<?php echo $img ?>"/><a href="index.php?p=viewmember&perso=<?= $line['id']?>" title="<? if ($line['pionier'] == 1) { echo "Pionier";} else { echo $line['title'] ;}?> <?= $line['name']?>"> <?= $line['name']?></a> <? echo $vanish ?> </li>
		<?php
	}

	?>
		</ul>
	
	<?php } ?>
	
	<?php if ($_SESSION["rank"] <= 4) { ?>
	
	<div class="navtitle">Activité Membres</div>
	Joueurs connectés :
		<ul class="nav">
	<?php

	$answer1 = $db->query('SELECT *
				FROM members
				WHERE ADDDATE(last_action, INTERVAL 15 MINUTE) > NOW() AND invisible = 0
				ORDER BY rank desc, name asc');
	$answer2 = $db->query('SELECT *
				FROM members
				WHERE ADDDATE(last_action, INTERVAL 1 DAY) > NOW() AND ADDDATE(last_action, INTERVAL 30 MINUTE) < NOW() AND invisible = 0
				ORDER BY rank desc, name asc');

	$void = true;
	
	
		while ($line = $answer1->fetch())
		{
			switch($line ["rank"] )
					{
						case 9:
						$img = "rankcrea.png" ;
						break;
					
						case 8:
						$img = "ranktitan.png" ;
						break;
						
						case 7:
						$img = "rank7.png" ;
						break;
						
						case 6:
						$img = "rank6.png" ;
						break;
						
						case 5:
						$img = "rank5.png" ;
						break;
						
						case 4:
						$img = "rank4.png" ;
						break;
						
						case 3:
						$img = "rank3.png" ;
						break;
						
						case 2:
						$img = "rank2.png" ;
						break;
						
						case 1:
						$img = "rank1.png" ;
						break;
						
						case 0:
						$img = "rank0.png" ;
						break;
					}
				if ($line["technician"] == 1) { $img = "ranktech.png" ;}
				if ($line["ban"] == 1) { $img = "rankban.png" ;}
				if ($line["removed"] == 1) { $img = "rankdel.png" ;}
		
		?>
			<li class="navbg2" style="list-style-type: none; padding: 10;"><img class="magie_type" width="27" src="pics/<?php echo $img ?>" /><a href="index.php?p=viewmember&perso=<?= $line['id']?>" title="<?= $line['title']?> <?= $line['name']?>" style="margin-right:6px;color: <?= $line2['color']?>;"> <?= $line['name']?></a></li>
		<?php
	}
	?>
		<br />
		Passés récemment :
		
	<?php
	$void = true;

	while ($line = $answer2->fetch())
	{

		switch($line ["rank"] )
				{
					case 9:
					$img = "rankcrea.png" ;
					break;
					
					case 8:
					$img = "ranktitan.png" ;
					break;
					
					case 7:
					$img = "rank7.png" ;
					break;
					
					case 6:
					$img = "rank6.png" ;
					break;
					
					case 5:
					$img = "rank5.png" ;
					break;
					
					case 4:
					$img = "rank4.png" ;
					break;
					
					case 3:
					$img = "rank3.png" ;
					break;
					
					case 2:
					$img = "rank2.png" ;
					break;
					
					case 1:
					$img = "rank1.png" ;
					break;
					
					case 0:
					$img = "rank0.png" ;
					break;
				}
				if ($line["technician"] == 1) { $img = "ranktech.png" ;}
				if ($line["ban"] == 1) { $img = "rankban.png" ;}
				if ($line["removed"] == 1) { $img = "rankdel.png" ;}

		?>
			<li class="navbg2" style="list-style-type: none; padding: 10;"><img class="magie_type" width="27" src="pics/<?php echo $img ?>"/><a href="index.php?p=viewmember&perso=<?= $line['id']?>" title="<?= $line['title']?>  <?= $line['name']?>" style="margin-right:6px;color: <?= $line2['color']?>;"> <?= $line['name']?></a></li>
		<?php
	}

	?>
		</ul>
	
	<?php } ?>
	
	<?php
}
?>
