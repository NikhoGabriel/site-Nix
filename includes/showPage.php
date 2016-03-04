<?php function showPage ()
{
	
	global $db, $_POST, $_GET, $_SESSION;

	$answer = $db->query("SELECT * FROM members");
	$line = $answer->fetch();
	$answer->closeCursor();
	
	$page = (isset($_GET['p'])) ? $_GET['p'] : '';

	
	?>
	<div id="site">
			
			<table>
				<tbody>
					<tr>
						<table>
							<tbody>
								<tr>
									<td width="80px">
										<a href="index.php"><img src="pics/logo1.gif" alt="" /></a>
									</td>
									<td style="text-align: right;" width="1200">
										<p>
											<?php include('includes/news.php'); news('disp');?>
										</p>
									</td>
									<td width="80px">
										<img src="http://herobrine.fr/blapproved.php?bid=3" title="Approved by BL">
									</td>
									<td width="50px">
										<a href="https://minecraft.net/" target=_blank><img src="http://herobrine.fr/pics/mc1.png" alt="" /></a>
									</td>
								</tr>
							</tbody>
						</table>
					</tr>
						
					<tr>
					
						<table id="main_c" cellspacing="20" cellpadding="0">
							<tbody>
								<tr>
									<td width="270" valign="top">
										<?php
											include('includes/nav.php'); 		nav();
											include('includes/loggedIn.php'); 	loggedIn();
										?>
									</td>
									<td width="*" valign="top">
										<div class="bigalert">
											<h1>Contenu Bêta</h1>
											<p>Certaines fonctionnalités du site sont encore en cours d'élaboration, RPNix n'en est qu'à la version bêta mais reste tout de même libre d'entrée !<br />
											Vous pouvez toujours vous tenir au courant des mises à jour à venir ou des fonctions futures auprès des membres du Staff concernés !</p>
										</div>
										
											<?php 
												include('includes/alert.php'); 		alert();
											?>
										
										
										<!--<? 
										if ($_SESSION['E_magique'] <= 30 AND $_SESSION['E_magique'] > 0){ ?>
										<div class="alert">
										<h3>Fatigue grandissante du personnage</h3>
										<p>Du fait de la perte de presque la totalité de ses Points de Magie (PM), votrepersonnage souffre d'une fatigue assez prenoncée, il devient moins endurant, irritable, et il est compliqué pour lui de rester concentré sur des tâches complexes.
										</p><p>La restauration des flux magique de votre personnage sera naturelle. Aussi bien faites attention la prochaine fois que vous abusez des sorts.</p>
										</div>
										<? } if ($_SESSION['E_magique'] <= 0) {?>
										<div class="alert">
										<h3>Perte des ressources magiques</h3>
										<p>Après la perte de la totalité de vos Points de Magie (PM) votre persnne se voit très régulièrement épuisé, si de nouveaux sorts sont tentés, le coût de ces derniers impactera
										les Points Vitaux (PV) de votre personnage.<br />
										Il n'est jamais bon de voir cette jauge atteindre zéro.</p>
										<p>La restauration des flux magique de votre personnage sera naturelle. Aussi bien faites attention la prochaine fois que vous abusez des sorts.</p>
										</div>
										<? } if ($_SESSION['E_vitale'] <= 50 AND $_SESSION['E_vitale'] > 0) {?>
										<div class="alert">
										<h3>Points Vitaux faibles</h3>
										<p>Après la perte de plus de 75% de ses Points Vitaux (PV), votre personnage perd peu à peu pied dans la réalité, il somnole de manière chronique, et est victime de troubles de
										la concentration aigües.</p>
										<p>La restauration des flux vitaux est relativement longue, la prudence sera de mise à l'avenir aux yeux du personnage.</p>
										</div>
										<? } if ($_SESSION['E_vitale'] <= 0) { ?> <div class="alert">
										<h3>Perte total des repères</h3>
										<p>Suite à la perte totale des Points Magiques puis Vitaux (PM) (PV) votre personnage perd totalement la raison, il sera incapable d'aligner trois phrases cohérentes et impossible d'effectuer le moindre sort.<br />
										Il peut arriver que le récent perdu ne sache plus faire la différence entre allié et ennemi.</p>
										<p>La restauration des flux vitaux est relativement longue, la prudence sera de mise à l'avenir aux yeux du personnage.</p>
										</div>
										<?  }?>-->
										
										<div id="main">
											
												<?php

											switch ($page)
											{
												case '' : 					{	include('includes/home.php'); 					home(); 				break; }
												case 'login': 				{	include('includes/login.php'); 					login(); 				break; }
												case 'glennforum':			{	include('includes/glennforum.php');				glennforum(); 			break; }
												case 'glenngroups':			{	include('includes/glenngroups.php');			glenngroups(); 			break; }
												case 'register':			{	include('includes/register.php'); 				register(); 			break; }
												case 'a': 					{ 	include('includes/activate.php'); 				activate(); 			break; }
												case 'members': 			{ 	include('includes/members.php'); 				members(); 				break; }
												case 'chatbox': 			{ 	include('includes/chatbox/chatboxPage.php'); 	chatboxPage(); 			break; }
												case 'chatboxmj': 			{ 	if ($_SESSION['rank'] >= rank_cbm) { 	include('includes/chatbox_mj/chatboxPage.php');
												chatboxPage(); 				} 	else { 	?><p>Vous n'aves pas accès à cette page</p><?php 	} 			break; }
												case 'forum': 				{ 	include('includes/forum.php'); 					forum(); 				break; }
												case 'perso': 				{ 	include('includes/perso.php'); 					perso(); 				break; }
												case 'rules': 				{ 	include('includes/rules.php'); 					rules(); 				break; }
												case 'account': 			{ 	include('includes/account.php'); 				account(); 				break; }
												case 'guilds': 				{ 	include('includes/guilds.php'); 				guilds(); 				break; }
												case 'incantations_admin': 	{ 	include('includes/incantations_admin.php'); 	incantations_admin(); 	break; }
												case 'pnj_list': 			{ 	include('includes/pnj_list.php'); 				pnj_list(); 			break; }
												case 'pm': 					{ 	include('includes/pm.php'); 					pm(); 					break; }
												case 'news': 				{ 													news('page'); 			break; }
												case 'races': 				{ 	include('includes/races.php'); 					races(); 				break; }
												case 'chat_ig': 			{ 	include('includes/interface/chat_ig.php'); 		chat_ig(); 				break; }
												case 'whitelist': 			{ 	include('includes/interface/whitelist.php'); 	whitelist_page(); 		break; }
												case 'serv_admin': 			{ 	include('includes/interface/serv_admin.php'); 	serv_admin(); 			break; }
												case 'groups': 				{ 	include('includes/groups.php'); 				groups(); 				break; }
												case 'candid': 				{ 	include('includes/candid.php'); 				candid(); 				break; }
												case 'server': 				{ 	include('includes/server.php'); 				server(); 				break; }
												case 'magie_admin': 		{ 	include('includes/magie_admin.php'); 			magie_admin(); 			break; }
												case 'viewmember': 			{ 	include('includes/viewmember.php'); 			viewmember(); 			break; }
												case 'testpage': 			{ 	include('includes/testpage.php'); 				testpage(); 			break; }
												case 'testpage_3': 			{ 	include('includes/testpage_3.php'); 			testpage_3(); 			break; }
												case 'testpage_2': 			{ 	include('includes/testpage_2.php');				testpage_2(); 			break; }
												case 'staffteam': 			{ 	include('includes/staffteam.php'); 				staffteam(); 			break; }
												case 'staffcontent': 		{ 	include('includes/staffcontent.php'); 			staffcontent(); 		break; }
												case 'rulesmj': 			{ 	include('includes/rulesmj.php'); 				rulesmj(); 				break; }
												//Erreur 404 
												case '404': 				{ 					?><p>Page inexistante.</p><?php 						break; }
												default : 					{ 					?><p>Page inexistante.</p><?php 						break; }
												case 'chrono': 				{ 	include('includes/chrono.php'); 				chrono(); 				break; }
												case 'incantations': 		{ 	include('includes/incantations.php'); 			incantations(); 		break; }
												case 'bg_admin': 			{ 	include('includes/bg_admin.php'); 				bg_admin(); 			break; }
												case 'bg_package': 			{ 	include('includes/bg_package.php'); 			bg_package(); 			break; }
												case 'bg_class': 			{ 	include('includes/bg_class.php'); 				bg_class();				break; }
												case 'bg_category': 		{ 	include('includes/bg_category.php'); 			bg_category(); 			break; }
												case 'bg_sub': 				{ 	include('includes/bg_sub.php'); 				bg_sub(); 				break; }
												case 'bg_content': 			{	include('includes/bg_content.php'); 			bg_content(); 			break; }
											} 	?>
											
										</div>
									</td>
								</tr>
							
							</tbody>
						</table>
					</tr>
					<tr>
						<div id="footer">
							<p>Nix est un site communautaire à destination des joueurs de Minecraft. Le contenu de ce site internet est une fiction - 2015</p>
						</div>
					</tr>
				</tbody>
			</table>
		</div>
		
	<script>
		var button = document.getElementById('button_menu');
		var header = document.getElementById('header');
		var pageAside = document.getElementById('page_aside');
		var content = document.getElementById('cell_main');
		var dispMenu = false;

		button.addEventListener('click', function () {
			if (dispMenu)
			{
				pageAside.style.display = 'none';
				header.style.display = 'none';
				content.style.display = 'block';
				dispMenu = false;
			}
			else
			{
				pageAside.style.display = 'block';
				header.style.display = 'block';
				content.style.display = 'none';
				dispMenu = true;
			}
		}, false);
	</script>
	
		<!-- Site créé par Alwine pour Nix, maintenu par Nikho, serveur de roleplay sur Minecraft.-->

	<?php
}
?>
