/*************************************************************************
START
GLOBAL FUNCTIONS and VARIABLES
************************************************************************/

function qs(selector) {
	return document.querySelector(selector);
}
function qsAll(selectorAll) {
	return document.querySelectorAll(selectorAll);
}


/* добавить-убрать класс для любого id */
function changeCL(clTOadd, selectorToChangeClass) {
	var body = qs(selectorToChangeClass);
	var regex = new RegExp('\\b' + clTOadd + '\\b');
	if (body.className.search(regex) !== -1) {
		body.className = body.className.replace(new RegExp('(?:^|\\s)' + clTOadd + '(?:\\s|$)'), ' ');
	} else {
		body.className += ' ' + clTOadd;
	}
}
/* добавить-убрать класс для любого id END*/

/* добавить класс для любого id */
function addCL(clTOadd, idToChangeClass) {
	var body = qs(idToChangeClass);
	var regex = new RegExp('\\b' + clTOadd + '\\b');
	if (body.className.search(regex) == -1) {
		body.className += ' ' + clTOadd;
	}
}
/* добавить класс для любого id  END*/

/* убрать класс для любого id */
function removeCL(clTOadd, idToChangeClass) {
	var body = qs(idToChangeClass);
	var regex = new RegExp('\\b' + clTOadd + '\\b');
	if (body.className.search(regex) !== -1) {
		body.className = body.className.replace(new RegExp('(?:^|\\s)' + clTOadd + '(?:\\s|$)'), ' ');
	}
}
/* убрать класс для любого id  END*/




/***************************** ajax modal window with or without callback*/
function ajaxGET(url, ajax_response_container_id, ajax_response_insert_to, callbackFunc) {

	var xhttp;
	xhttp=new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {

			var ajax_response = xhttp.responseText;
			qs(ajax_response_insert_to).innerHTML = '<div id="'+ajax_response_container_id+'">' + ajax_response + '</div>';

			if (callbackFunc) {
				callbackFunc(this);
			}

		}
	};
	xhttp.open("GET", url, true);
	xhttp.send();

}
function ajaxPOST(url, ajax_response_container_id, ajax_response_insert_to, postdataFunc, callbackFunc) {

	var xhttp = new XMLHttpRequest();
	xhttp.open('POST', '/', true);
	xhttp.onreadystatechange = function () {
		if (xhttp.readyState == 4 && xhttp.status == 200) {

			var ajax_response = xhttp.responseText;
			qs(ajax_response_insert_to).innerHTML = '<div id="'+ajax_response_container_id+'">' + ajax_response + '</div>';

			if (callbackFunc) {
				callbackFunc(this);
			}

		}
	};

	xhttp.send(postdataFunc);


}

/***************************** ajax modal window with or without callback*/


/*************************************************************************
END
GLOBAL FUNCTIONS and VARIABLES
************************************************************************/


/*************************************************************************
start
Code
************************************************************************/
if(qs('#get-prize')){
	qs('#get-prize').addEventListener('click', function(e) {

		ajaxPOST(
			'/', //url
			'ajax-request-response', //ajax_response_container_id
			'#ajax-result', //ajax_response_insert_to
			(function(){ //postdataFunc
				var formdata = new FormData();
				// var token = qs('#get-prize').getAttribute('data-tkn');
				formdata.append('get-prize', '1');
				formdata.append('refer', document.referrer);
				// formdata.append('token', token);
				return formdata;
			})(),
			function(){ //callbackFunc
				qs('#get-prize').setAttribute('disabled', 'true');

				var action_buttons = qsAll('#prize-action > button');
				for(var i = 0; i < action_buttons.length;i++) {

					action_buttons[i].addEventListener('click', function(e) {

						var req_name = this.getAttribute('id');
						ajaxPOST(
							'/',
							'prize-actions',
							'footer',
							(function(){ //postdataFunc
								var formdata = new FormData();
								formdata.append('prize-action', '1');
								formdata.append(req_name, '1');
								formdata.append('refer', document.referrer);
								return formdata;
							})(),
							function(e){
								qs('#get-prize').removeAttribute('disabled');
								for(var i = 0; i < action_buttons.length;i++) {
									action_buttons[i].setAttribute('disabled', 'true');
								}
								if(req_name === 'money2bank'){
									alert('Типа форма отправки в банк');
								}
								if(req_name === 'gift2user'){

									ajaxPOST(
										'/',
										'prize-actions',
										'side_a',
										(function(){ //postdataFunc
											var formdata = new FormData();
											formdata.append('prize-action', '1');
											formdata.append('gift2user2db', '1');
											formdata.append('refer', document.referrer);
											return formdata;
										})()
										)

								}
							}
							)
					})

				}

			}
			);

	})
}

if(qs('#old_temp_gifts')){
	ajaxPOST(
		'/',
		'old_temp_gifts_child',
		'#side_a',
		(function(){ //postdataFunc
			var formdata = new FormData();
			formdata.append('prize-action', '1');
			formdata.append('abort-prize', '1');
			formdata.append('refer', document.referrer);
			return formdata;
		})(),
		function(e){ //callbackFunc
			qs('#old_temp_gifts').removeAttribute('id');
		}
		)
}


/*************************************************************************
END
Code
************************************************************************/

