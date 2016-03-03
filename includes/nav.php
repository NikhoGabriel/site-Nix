<?php function nav ()
{
	define('rank_cbm', 5);
	define('rank_admin',5);
	define('6', 3);

	global $db, $_SESSION, $_GET;

	$page = (isset($_GET['p'])) ? $_GET['p'] : false;
	
	$answer = $db->query("SELECT COUNT(*) AS count FROM candid WHERE verify = 0");
	$line = $answer->fetch();
	$answer->closeCursor();
	
	
?>
	<div class="navtitle">Acceuil</div>
		<ul class="nav">
			<a class="link" href="index.php" >
				<li class="navbg" <?php echo (!$page) ? 'class="cur_page"' : '';?>><img src="includes/img/home.gif" alt="" />Accueil</li>
			</a>
			<a class="link" href="index.php?p=rules" >
				<li class="navbg" <?php echo ($page == 'rules') ? 'class="cur_page"' : '';?>><img src="includes/img/rules.gif" alt="" />Les Règles</li>
			</a>
			<a class="link" href="index.php?p=server" >
				<li class="navbg" <?php echo ($page == 'server') ? 'class="cur_page"' : '';?>><img src="includes/img/server.gif" alt="" />Le Serveur</li>
			</a>
			<a class="link" href="index.php?p=candid" >
				<li class="navbg" <?php echo ($page == 'candid') ? 'class="cur_page"' : '';?>>
					<img src="includes/img/candid.gif" alt="" />Votre Candidature 
						<?if ($_SESSION["rank"] >= 5) { if ($line['count'] >= 1) {?>
							<span style="color: red">[<?= $line['count']?>]</span>
						<? } } ?>
				</li>
			</a>
		</ul>
		
		
	<div class="navtitle">Informations</div>
		<ul class="nav">
			<a class="link" href="index.php?p=news" >
				<li class="navbg" <?php echo ($page == 'news') ? 'class="cur_page"' : '';?>><img src="includes/img/news.gif" alt="" />L'Actualité</li>
			</a>
			<a class="link" href="index.php?p=members" >
				<li class="navbg" <?php echo ($page == 'members') ? 'class="cur_page"' : '';?>><img src="includes/img/members.gif" alt="" />Les Membres</li>
			</a>
			<a class="link" href="index.php?p=forum" >
				<li class="navbg" <?php echo ($page == 'forum') ? 'class="cur_page"' : '';?>><img src="includes/img/forum.gif" alt="" />Les Forums</li>
			</a>
			<a class="link" href="index.php?p=chatbox" >
				<li class="navbg" <?php echo ($page == 'chatbox') ? 'class="cur_page"' : '';?>><img src="includes/img/chat.gif" alt="" />Dialogue en direct</li>
			</a>
		</ul>
		
	<div class="navtitle">Contenu</div>
		<ul class="nav">
			<a class="link" href="index.php?p=races" >
				<li class="navbg" <?php echo ($page == 'races') ? 'class="cur_page"' : '';?>><img src="includes/img/race.gif" alt="" />Les Races</li>
			</a>
			<a class="link" href="index.php?p=guilds" >
				<li class="navbg" <?php echo ($page == 'groups') ? 'class="cur_page"' : '';?>><img src="includes/img/group.gif" alt="" />Les Groupes</li>
			</a>
			<a class="link" href="index.php?p=staffteam" >
				<li class="navbg" <?php echo ($page == 'staffteam') ? 'class="cur_page"' : '';?>><img src="includes/img/staffteam.gif" alt="" />L'Equipe Admin'</li>
			</a>
		</ul>
		
<?php if ($_SESSION["rank"] >= 4) { ?>
	<div class="navtitle">Contenu</div>
		<ul class="nav">
			<a class="link" href="index.php?p=whitelist" >
				<li class="navbg" <?php echo ($page == 'whitelist') ? 'class="cur_page"' : '';?>><img src="includes/img/wl.gif" alt="" />La Whitelist</li>
			</a>
			<a class="link" href="index.php?p=staffcontent" >
				<li class="navbg" <?php echo ($page == 'background') ? 'class="cur_page"' : '';?>><img src="includes/img/bg.gif" alt="" />BackGround</li>
			</a>
			<a class="link" href="index.php?p=magie_admin" >
				<li class="navbg" <?php echo ($page == 'magie_admin') ? 'class="cur_page"' : '';?>><img src="includes/img/blabla.gif" alt="" />Magie Admin'</li>
			</a>
		</ul>
<?php } ?>
	
<?php if ($_SESSION["rank"] >= rank_admin) { ?>
	<div class="navtitle">Administration</div>
		<ul class="nav">
			<a class="link" href="index.php?p=chat_ig" >
				<li class="navbg" <?php echo ($page == 'chat_ig') ? 'class="cur_page"' : '';?>><img src="includes/img/chat_ig.gif" alt="" />Chat In Game</li>
			</a>
			<a class="link" href="index.php?p=chatboxmj" >
				<li class="navbg" <?php echo ($page == 'chatboxmj') ? 'class="cur_page"' : '';?>><img src="includes/img/chat.gif" alt="" />ChatBox MJ</li>
			</a>
			<a class="link" href="index.php?p=serv_admin" >
				<li class="navbg" <?php echo ($page == 'serv_admin') ? 'class="cur_page"' : '';?>><img src="includes/img/server_admin.gif" alt="" />Administration Serveur'</li>
			</a>
			<a class="link" href="http://62.210.232.129:1414" target="_blank" >
				<li class="navbg" <?php echo ($page == 'dynmap') ? 'class="cur_page"' : '';?>><img src="includes/img/map.gif" alt="" />La DynMap'</li>
			</a>
			<a class="link" href="index.php?p=chrono" >
				<li class="navbg" <?php echo ($page == 'chrono') ? 'class="cur_page"' : '';?>><img src="includes/img/chrono.gif" alt="" />Chronologie'</li>
			</a>
			<a class="link" href="index.php?p=rulesmj" >
				<li class="navbg" <?php echo ($page == 'rulesmj') ? 'class="cur_page"' : '';?>><img src="includes/img/rules.gif" alt="" />Réglement MJ</li>
			</a>
			<a class="link" href="index.php?p=pnj_list" >
				<li class="navbg" <?php echo ($page == 'pnj_list') ? 'class="cur_page"' : '';?>><img src="includes/img/page.gif" alt="" />Liste des PNJs'</li>
			</a>
		</ul>
<?php } ?>
	
<?php if ($_SESSION['connected']) { ?>
	<div class="navtitle">Magie</div>
		<ul class="nav">
			<a class="link" href="index.php?p=incantations" >
				<li class="navbg" <?php echo ($page == 'incantations') ? 'class="cur_page"' : '';?>><img src="includes/img/book.gif" alt="" />Mes sorts</li>
			</a>
		</ul>
<?php } ?>
	
<?php if (!$_SESSION['connected']) { ?>
	<div class="navtitle">Enregistrement</div>
		<ul class="nav">
			<a class="link" href="index.php?p=register" >
				<li class="navbg" <?php echo ($page == 'register') ? 'class="cur_page"' : '';?>><img src="includes/img/register.gif" alt="" />Inscription</li>
			</a>
			<a class="link" href="index.php?p=login" >
				<li class="navbg" <?php echo ($page == 'login') ? 'class="cur_page"' : '';?>><img src="includes/img/connection.gif" alt="" />Connexion</li>
			</a>
		</ul>
<?php }?>
<?php if ($_SESSION['connected']) { if ($_SESSION['pionier'] == 1) { $pionier = "-P";} else { $pionier = '';} if ($_SESSION['technician'] == 1) { $tech = "-T";} else { $tech = '';} ?>
		<div class="navtitle">Compte</div>
		<ul class="nav">
			<li class="navbg"><span class="name<?= $_SESSION['rank']?><?echo $tech?><?echo $pionier?>"><? if ($_SESSION['pionier'] == 1) { echo "Pionier";} else { echo $_SESSION['title'] ;}?> <?= $_SESSION['name' ]?></span></li>
			<a class="link" href="index.php?p=perso" >
				<li class="navbg" <?php echo ($page == 'perso') ? 'class="cur_page"' : '';?>><img src="includes/img/page.gif" alt="" />Personnage</li>
			</a>
			<a class="link" href="index.php?p=pm" >
				<li class="navbg" <?php echo ($page == 'pm') ? 'class="cur_page"' : '';?>><img src="includes/img/pm.gif" alt="" />Messages Privés
					<? if ($_SESSION['alertNewMsgs']) { ?>
						<span style="color:red">[<?= $_SESSION['alertNewMsgs']?>] </span>
					<? } ?>
				</li>
			</a>
			<a class="link" href="index.php?p=account" >
				<li class="navbg" <?php echo ($page == 'account') ? 'class="cur_page"' : '';?>><img src="includes/img/option.gif" alt="" />Mon Compte</li>
			</a>
			<a class="link" href="index.php?p=login&amp;action=disconnection" >
				<li class="navbg" ><img src="includes/img/porte.gif" alt="" />Déconnexion</li>
			</a>
		</ul>
<?php } ?>
	
<?php if ($_SESSION["rank"] >= 3) { 
			
	$answer = $db->query('SELECT COUNT(*) AS ngrada FROM hist_grada');
	$line = $answer->fetch();
	$answer->closeCursor();

	$numMax = $line['ngrada'];
	$numMin = ($numMax >= 10) ? ($numMax - 10) : 0;
				
	$grada = $db->query('SELECT * FROM hist_grada ORDER BY id DESC LIMIT 10');
?>
	<div class="navtitle">Progression</div>
		<ul class="nav" style="padding: 10px;">
<?php	  
	while ($line = $grada->fetch())
	{	switch ($line['method']) { case 0: $gradam= "-" ; $gradat= "dégradé"; break; case 1: $gradam= "+"; $gradat= "promu"; break;} 
				$datasMemb = $db->query('SELECT * FROM members WHERE members.id = \'' .$line['upped_id']. '\'');
				$datasupped = $datasMemb->fetch();
				$datasMemb = $db->query('SELECT * FROM members WHERE members.id = \'' .$line['upper_id']. '\'');
				$datasupper = $datasMemb->fetch();
	?>
			<li style="list-style-type: none;">[<? echo $gradam ?>] <img src="pics/rank<?php echo $datasupped['rank']; ?>" width="25" class="magie_type" title="<?= $datasupper['name']?> a <? echo $gradat ?> <?= $datasupped['name']?> !" /> <?= $datasupped['name']?></li>
<?php } ?>
		</ul>
<?php } ?>

	<div class="navtitle">Plateformes</div>
		<ul class="nav">
			<li class="navbg2" style="font-size: 12px;list-style-type: none;"><img src="includes/img/ts.gif" alt="" />rpnix.ts3serv.com:10261</li>
			<li class="navbg2" style="clear: both;;list-style-type: none;"><img src="includes/img/twitter.gif" alt="" /><a href="https://twitter.com/RPNixfr" target=_blank >@RPNixfr</a></li>
		</ul>
		
	<div class="navtitle">Découvrez :</div>
		<ul class="nav">
			<a href="http://www.herobrine.fr" target=_blank >
				<li class="navbg2" style="list-style-type: none;"><img src="includes/img/hb.gif" alt="" />Herobrine.fr</li>
			</a>
		</ul>
		
	<div class="navtitle">Nos Pioniers</div>
		<p class="navbg2" style="text-align: center;">Membres honorable qui ont apporté un soutien considérable à Nix !<br />
		<br />
		&bull; Alwine<br />
		&bull; Glenn<br />
		&bull; Lune<br />
		&bull; Shaolern<br />
		&bull; Zelenan</p>
	
<?php 
}
?>
