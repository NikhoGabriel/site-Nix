(function () {
	var xhrDel = new XMLHttpRequest();

	//Suppression d'un message
	function deleteMsg (msg) {
		if (confirm('Voulez-vous vraiment supprimer ce message ? Il ne sera pas supprimé pour les utilisateurs l\'ayant déjà vu.')) {
			xhrDel.open('POST', 'index.php?p=chatboxsystemmj');
			xhrDel.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhrDel.send('action=delete&msg=' + msg);
		}
	}

	//Suppression du message qui indique que javascript n'est pas activé

	var chatboxStart = document.getElementById('chatbox');
	var chatbox = chatboxStart.cloneNode(false)
	chatboxStart.parentNode.replaceChild(chatbox, chatboxStart);

	var lastMsg = 0;
	var limit = 60;
	var salonChanged = false;
	var reloadTimeOut;

	var sendForm = {
		msg:document.getElementById('msg'),
		to:document.getElementById('to'),
		salon:document.getElementById('salon'),

		send:document.getElementById('send'),
		joinSalon:document.getElementById('joinSalon'),

		rmMsg:document.getElementById('rmMsg'),
		rmTo:document.getElementById('rmTo'),
		rmSalon:document.getElementById('rmSalon')
	};


	//Rechargement de la page
	function reload () {
		//Préparation requête
		var xhr = new XMLHttpRequest();
		xhr.open('POST', 'index.php?p=chatboxsystemmj');
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		//Définition salon
		var salon = localStorage.getItem('salon');
		salon = (salon) ? '&salon='+salon : '';

		//Si changement de salon
		if (salonChanged) {
			lastMsg = 0;

			chatboxTmp = chatbox.cloneNode(false);
			chatbox.parentNode.replaceChild(chatboxTmp, chatbox);
			chatbox = chatboxTmp;

			salonChanged = false;
		}

		//Envoir de la requête
		xhr.send('action=reload&limit=' + limit + '&last=' + lastMsg + salon);

		xhr.addEventListener('readystatechange', function () {
			//Requête terminée
			if (xhr.readyState === 4) {
				//Requête terminée avec succès (200)
				if (xhr.status === 200) {
					//Récupération du résultat
					var result = JSON.parse(xhr.responseText);
					var length = result.msg.length

					if (length) {
						//Parcours des résultats
						for (var i = 0; i < length; i++) {
							var line = document.createElement('p');
							line.id = result.msg[i].id;

							//Affichage du bouton de suppression et ajout de l'événement supprimer si l'on a les droits
							if (result.msg[i].canDelete) {
								var btDel = line.appendChild(document.createElement('span'));
								btDel.className = 'del_button';
								btDel.appendChild(document.createTextNode('[x]'));
							
								btDel.addEventListener('click', function (index) {
									return function () {
										deleteMsg(index);
									};
								}(result.msg[i].id), false);

								line.appendChild(document.createTextNode(' : '));
							}

							//Heure
							line.appendChild(document.createTextNode(result.msg[i].hourSend + ' : '));

							//Auteur (et chucho) + Avatar (mini skin)

							if (result.msg[i].pm == 1) {
								line.appendChild(document.createTextNode('Vous chuchotez à '));
								line.className = 'whisp';
							}

							var name = line.appendChild(document.createElement('em'));
							name.className = 'nameCb';
							if (result.msg[i].skin.exist) {
								var avatar = document.createElement('img');
								avatar.setAttribute('src', result.msg[i].skin.path);
								avatar.className = 'avatar';
								name.appendChild(avatar);
							}
							name.appendChild(document.createTextNode(result.msg[i].author.name));

							//Auto-complétion de la personne à qui on chuchotte en cliquant sur le nom
							name.addEventListener('click', function (e) {
								sendForm.to.value = e.target.textContent;	
							}, false);

							if (result.msg[i].pm == 0) {
								name.style.color = result.msg[i].author.color || null;
							}

							if (result.msg[i].pm == 2) {
								line.appendChild(document.createTextNode(' vous chuchote'));
								line.className = 'whisp';
							}

							//Message
							line.appendChild(document.createTextNode(' : '));
							var msg = line.appendChild(document.createElement('span'));
							msg.innerHTML = result.msg[i].msg;

							if (result.msg[i].msgStrong) {
								msg.className = 'msg_chat_strong';
							}
							
							//Ajout à la chatbox
							chatbox.appendChild(line);

							if (chatbox.childNodes.length > limit) {
								chatbox.removeChild(chatbox.firstChild);
							}

							if (i == (length - 1)) {
								lastMsg = result.msg[i].id;
							}
						}
					}
				}

				//La page n'existe pas (404) donc serveur indisponible
				else if (xhr.status === 404) {
					chatbox.textContent = 'Serveur indisponible.';
				}
			}
		}, false);

		reloadTimeout = setTimeout(reload, 15*1000);
	}

	
	//Envoi d'un message
	function send () {
		if (sendForm.msg.value != '') {
			var xhrSend = new XMLHttpRequest();
			xhrSend.open('POST', 'index.php?p=chatboxsystemmj');
			xhrSend.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

			var to = (sendForm.to.value) ? '&to=' + encodeURIComponent(sendForm.to.value) : '';
			var salon = (sendForm.salon.value) ? '&salon=' + encodeURIComponent(sendForm.salon.value) : '';

			xhrSend.send('action=send&msg=' + encodeURIComponent(sendForm.msg.value) + to + salon);

			xhrSend.addEventListener('readystatechange', function () {
				if (xhrSend.readyState === 4) {
					if (xhrSend.status === 200) {
						if (localStorage.getItem('salon') != sendForm.salon.value) {
							localStorage.setItem('salon', sendForm.salon.value);
							salonChanged = true;
						}
						sendForm.msg.value = '';
						reloadTimeout.clearTimeout;
						reload();
					}
				}
			}, false);
		}
	}

	//Ajout des événements sur les contrôles
		
		//Envoi du message quand le bouton 'Envoyer' est cliqué
	sendForm.send.addEventListener('click', send, false);
		//Envoi du message quand la touche Entrée est pressée
	window.addEventListener('keypress', function (e) {
		if (e.keyCode == 13 || e.key == 'Enter') {
			send();
		}
	}, false);

	sendForm.joinSalon.addEventListener('click', function () {
		if (localStorage.getItem('salon') != sendForm.salon.value) {
			localStorage.setItem('salon', sendForm.salon.value);
			salonChanged = true;
		}
		reloadTimeout.clearTimeout;
		reload();
	}, false);

	sendForm.rmMsg.addEventListener('click', function () {
		sendForm.msg.value = '';
	}, false);
	sendForm.rmTo.addEventListener('click', function () {
		sendForm.to.value = '';
	}, false);
	sendForm.rmSalon.addEventListener('click', function () {
		sendForm.salon.value = '';
		if (localStorage.getItem('salon') != sendForm.salon.value) {
			localStorage.setItem('salon', sendForm.salon.value);
			salonChanged = true;
		}
		reloadTimeout.clearTimeout;
		reload();
	}, false);

	//Récupération du dernier salon
	sendForm.salon.value = localStorage.getItem('salon');

	//Activation du rechargement automatique
	reload();
})();
