<?php function chatboxSystem ()
{
	global $db, $_SESSION, $_POST;

	//Accès à la chatbox
	define('rank_speak', 1);
	//Suppression des messages
	define('rank_del', 4);
	//Message en gras
	define('rank_msg_strong', 4);
	//Chemin d'accès des avatars (mini skins)
	define('path_avatar', "pics/avatar/miniskin_");
	define('avatar_file_end', '.png');

	define('limit_default',120);

	if ($_SESSION['rank'] >= rank_speak)
	{
		if (isset($_POST['action']))
		{
			//Ajout d'un message
			if ($_POST['action'] == 'send' && isset($_POST['msg']) && strlen($_POST['msg']) > 0)
			{
				$msg = htmlspecialchars(substr($_POST['msg'], 0, 255));
				$salon = (isset($_POST['salon'])) ? htmlspecialchars($_POST['salon']) : '';

				if (isset($_POST['to']))
				{
					$answer = $db->prepare('SELECT id FROM members WHERE name = ?');
					$answer->execute(array(htmlspecialchars($_POST['to'])));

					if ($line = $answer->fetch())
					{
						$to = $line['id'];
						$salon = '';
					}
					else
					{
						$to = 0;
					}
				}
				else
				{
					$to = 0;
				}

				$insert = $db->prepare("INSERT INTO chatbox VALUES ('', NOW(), ?, ?, ?, ?)");
				$insert->execute(array($_SESSION['id'], $to, $salon, $msg));
			}

			//Supression d'un message
			else if ($_POST['action'] == 'delete' && isset($_POST['msg']))
			{
				$msg = intval($_POST['msg']);

				$answer= $db->prepare('SELECT user_id FROM chatbox WHERE id = ?');
				$answer->execute(array($msg));

				if ($line = $answer->fetch())
				{
					$rank = rank($line['user_id']);

					if (($rank <= $_SESSION['rank'] && $_SESSION['rank'] >= rank_del) || $line['user_id'] == $_SESSION['id'])
					{
						$delete = $db->prepare('DELETE FROM chatbox WHERE id = ?');
						$delete->execute(array($msg));
					}
				}
			}

			//Rechargement des messages
			else if ($_POST['action'] == 'reload')
			{
				$result = array();

				//Messages à envoyer → result.resultMsg
				$resultMsg = array();

				$limit = (isset($_POST['limit'])) ? intval($_POST['limit']) : limit_default;
				$salon = (isset($_POST['salon'])) ? $_POST['salon'] : '';
			
				$answer = $db->prepare('SELECT COUNT(*) AS number FROM chatbox WHERE ((to_id = 0 AND salon = ?) OR to_id = ? OR (user_id = ? AND to_id != 0))');
				$answer->execute(array($salon, $_SESSION['id'], $_SESSION['id']));
				$line = $answer->fetch();
				$answer->closeCursor();

				$numMax = $line['number'];
				$numMin = ($numMax >= $limit) ? ($numMax - $limit) : 0;
		
				$answer = $db->prepare("SELECT c.id AS id, c.post_date AS date, c.message AS message, c.to_id AS to_id, m.id AS m_id, m.name AS name
						      FROM members AS m
						      INNER JOIN chatbox AS c
						      ON c.user_id = m.id
						      WHERE ((c.to_id = 0 AND c.salon = ?) OR c.to_id = ? OR (c.user_id = ? AND c.to_id != 0))
						      ORDER BY c.post_date 
						      LIMIT ".$numMin.",".$numMax);
				$answer->execute(array($salon, $_SESSION['id'], $_SESSION['id']));
			
				while ($line = $answer->fetch())
				{
					$lineMsg = array();
				
					//hourSend
					$lineMsg['hourSend'] = preg_replace('#^.{11}(.{2}):(.{2}):.{2}$#', '$1:$2', $line['date']);
				
					//author.name && author.color && pm
					if ($line['to_id'] != 0 && $line['to_id'] != $_SESSION['id'])
					{
						$answer2 = $db->prepare('SELECT name FROM members WHERE id = ?');
						$answer2->execute(array($line['to_id']));
						$line2 = $answer2->fetch();

						$lineMsg['author'] = array (
							'name'=>$line2['name'],
							'color'=>''
						);
						$lineMsg['pm'] = 1;
					}
					else if ($line['to_id'] != 0)
					{	
						$lineMsg['author'] = array (
							'name'=>$line['name'],
							'color'=>''
						);
						$lineMsg['pm'] = 2;
					}
					else
					{
						$lineMsg['author'] = array (
							'name'=>$line['name'],
							'color'=>color($line['m_id'])
						);
						$lineMsg['pm'] = 0;
					}

					//canDelete
					if (($_SESSION['rank'] >= rank_del && $_SESSION['rank'] >= rank($line['m_id'])) || $line['m_id'] == $_SESSION['id'])
					{
						$lineMsg['canDelete'] = true;
					}
					else
					{
						$lineMsg['canDelete'] = false;
					}

					//id
					$lineMsg['id'] = $line['id'];

					//msg
					$lineMsg['msg'] = $line['message'];

					//msgStrong
					$lineMsg['msgStrong'] = (rank($line['m_id']) >= rank_msg_strong) ? true : false;
					
					//Skin
					if (file_exists(path_avatar.$line["m_id"].avatar_file_end))
					{
						$lineMsg['skin'] = array( "exist" => true,
									"path" => path_avatar.$line['m_id'].avatar_file_end );
					}
					else
					{
						$lineMsg['skin'] = array( "exist" => false );
					}

					//Ajout du message aux résultats
					$resultMsg[] = $lineMsg;
				}

				$result['msg'] = $resultMsg;

				//Envoi du résultat
				echo json_encode($result);
			}
		}
	

	/*$result = array (
		array (
			'canDelete'=>1,
			'id'=>3,
			'hourSend'=>'1345',
			'author'=> array (
				'name'=>'Alwine',
				'color'=>'blue'
			),
			'msgStrong'=>1,
			'msg'=>'test 1',
			'pm'=>0
		),
		array (
			'canDelete'=>0,
			'id'=>5,
			'hourSend'=>'7986',
			'author'=> array (
				'name'=>'Quelqu\'un',
				'color'=>0
			),
			'msgStrong'=>0,
			'msg'=>$_POST['action'],
			'pm'=>1
		)
	);*/

	}
}
?>
