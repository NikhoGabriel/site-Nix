<?php function chat_ig ()
{
	global $_SESSION, $_POST, $_GET;
	
	define("rank_send_msg", 4);
	
	if ($_SESSION['rank'] >= 3)
	{
		include('includes/interface/JSONapi.php');

		$ip = 'soul.omgcraft.fr';
		$port = 20059;
		$user = "nix";
		$pwd = "dragonball";
		$salt = 'salt';
		$api = new JSONAPI($ip, $port, $user, $pwd, $salt);
		
		if ($_SESSION["rank"] >= rank_send_msg && isset($_POST["action"]) && $_POST["action"] == "send" && isset($_POST['msg']) && strlen($_POST["msg"]) <= 255)
		{
			$api->call('chat.broadcast', array($_POST['msg']));
		}
		
		$chat_ig = $api->call('streams.chat.latest', array(100));
		
		if ($chat_ig[0]["is_success"])
		{
			$len = count($chat_ig[0]["success"]);
			
			?>
				<h3>Chat in game</h3>
			
				<section id="chatboxig">
			<?php
			for ($i = 0; $i < $len; $i++)
			{
				$player = htmlspecialchars($chat_ig[0]["success"][$i]["player"]);
				$message = htmlspecialchars($chat_ig[0]["success"][$i]["message"]);

				$msgStaff = preg_match("#$\$#", $message);
				
				?>
					<p><em class="name"><?=$player?></em> : <?=$message?></p>
				<?php
			}
			?>
				</section>
			<?php
		}
		
		if ($_SESSION["rank"] >= rank_send_msg)
		{
		?>
			<form method="POST" action="index.php?p=chat_ig">
				<label for="msg">Message :</label><input type="text" id="msg" name="msg" maxlength="255" />
				<input type="hidden" name="action" value="send" />
				<input type="submit" value="Envoyer" />
			</form>
		<?php
		}
		
		?>
			<p><a href="index.php?p=chat_ig&staff_msg=true">Voir seulement les messages staff.</a></p>
		<?php
	}
}
?>