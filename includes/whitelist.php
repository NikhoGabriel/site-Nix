<?php function whitelist_page ()
{
	global $_SESSION, $_GET;
	
	if ($_SESSION['rank'] >= 3)
	{
		/* Rajouter JSONapi ici */
		$api = null;
		
		?>
			<h3>Whitelist</h3>
		<?php
		
		if (isset($_POST["action"]))
		{
			if ($_POST["action"] == "add" && isset($_POST["pseudo"]) && strlen($_POST["pseudo"]) <= 16)
			{
				$api->call("players.name.whitelist", array($_POST["pseudo"]));
				?>
					<p>Le joueur <?= $_POST["pseudo"]?> a été ajouté à la whitelist.</p>
				<?php
			}
			
			if ($_POST["action"] == "rm" && isset($_POST["pseudo"]) && strlen($_POST["pseudo"]) <= 16)
			{
				$api->call("players.name.unwhitelist", array($_POST["pseudo"]));
				?>
					<p>Le joueur <?= $_POST["pseudo"]?> a été enlever de la whitelist.</p>
				<?php
			}
		}
		
		$whitelisted = $api->call('players.whitelisted.names');
		
		if ($whitelisted[0]['is_success'])
		{
			?>
			<form method="POST" action="index.php?p=whitelist">
				<input type="submit" value="Ajouter à la whitelist :" />
				<input type="text" id="pseudo" name="pseudo" maxlength="16" />
				<input type="hidden" name="action" value="add" />
			</form>
			
			<form method="POST" action="index.php?p=whitelist">
				<input type="submit" value="Retirer de la whitelist :" />
				<input type="text" id="pseudo" name="pseudo" maxlength="16" />
				<input type="hidden" name="action" value="rm" />
			</form>
			
			<table>
				<tbody>
			<?php
				for ($i = 0; $i< count($whitelisted[0]['success']); $i++)
				{
					?>
						<tr>
							<td><?php echo $whitelisted[0]['success'][$i];?></td>
						</tr>
					<?php
				}
			?>
				</tbody>
			</table>
			<?php
		}
	}
}
?>
