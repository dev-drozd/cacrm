<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcom Your Company</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
		* {
		  -webkit-box-sizing: border-box;
		  -moz-box-sizing: border-box;
		  box-sizing: border-box;
		}
        body {
            text-align: center;
            font-family: sans-serif;
            color: #888;
            line-height: 24px;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
		
        img {
            width: 300px;
            max-width: 100%;
        }
		
        h1 {
            color: #27b7c7;
        }
		
		h2 {
			font-size: 18px;
		}
		
        p:after {
            content: '';
            display: block;
            clear: both;
        }
		
        @media (max-width: 1024px) {
            .tablet {
                width: 90%;
            }
        }
    </style>
</head>
<body><h1>Store id: {store-id}</h1>{content}</body>
<script>

	window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
	  alert(errorMsg);

	  return false;
	};
	var j = false, _ = document, g = function(a){
		return _.getElementById(a);
	}, q = {}, ajax = function(a, b, c, d){
		var x = new XMLHttpRequest();
		if(typeof b == 'function'){
			c = b;
			b = 'GET';
		}
		x.onload = function(){
			if (this.status == 200){
				c(this.responseText);
			}
		};
		x.open(b, a, true);
		x.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		x.send(d);
	}, store_id = {store-id}, fb = function(r){
		g('welcome').style.display = 'none';
		g('vote').style.display = 'none';
		g('thankyou').style.display = 'block';
		ajax('/tablet/send_feedback', 'POST', function(a){
			setTimeout(function(){
				if(g('vote').style.display == 'none'){
					g('welcome').style.display = 'block';
					g('thankyou').style.display = 'none';
				}
			}, 1000);
		}, 'customer_id='+q.customer_id+'&issue_id='+q.issue_id+'&staff_id='+q.staff_id+'&rate='+r);
	};
	(function listen(){
		ajax('/sub/{token}.sock?confirm_purchase=0&chat_support=0&all=0&chat_support=0&t='+(new Date().getTime()), function(r){
			var d = JSON.parse(r);
			switch(d.type){
			
				case 'tablet_feedback':
					g('welcome').style.display = 'none';
					g('thankyou').style.display = 'none';
					g('vote').style.display = 'block';
					g('name').innerText = d.name;
					q.customer_id = d.customer_id;
					q.issue_id = d.issue_id;
					q.staff_id = d.staff_id;
					var s = new Audio();
					s.src = '/tablet2.mp3';
					s.play();
				break;
				
				case 'acception':
					ajax(location.href.replace('tablet','tablet/acception'), 'POST', function(a){
						document.body.innerHTML = a;
						setTimeout(function(){
							document.getElementById('stt').onchange = function(e){
								ajax('/geo/cities/'+e.target.value, 'GET', function(r){
									var c = document.getElementById('city'), r = JSON.parse(r), opts = '';
									for(var n in r){
										opts += '<option value="'+n+'">'+r[n]+'</option>';
									}
									c.innerHTML = opts;
									c.onchange = function(e){
										document.getElementById('zipcode').value = e.target.value;
									};
								});
							};

							document.getElementById('form').onsubmit = function(e){
								if(e.target.password.length && e.target.password2.length && e.target.password.value !== e.target.password2.value){
									e.preventDefault();
									alert('Passwords do not match');
									return false;
								}
								if(!document.getElementById('step2').style.display){
									e.preventDefault();
									document.getElementById('type').removeAttribute('disabled');
									document.getElementById('type').setAttribute('required', 'required');
									document.getElementById('brand').removeAttribute('disabled');
									document.getElementById('brand').setAttribute('required', 'required');
									document.getElementById('model').removeAttribute('disabled');
									document.getElementById('model').setAttribute('required', 'required');
									document.getElementById('step1').style.display = 'none';
									document.getElementById('step2').style.display = 'block';
									return false;
								}
							};

							document.getElementById('zipcode').oninput = function(e){
								ajax('/geo/zipcode/'+e.target.value, 'GET', function(r){
									var r = JSON.parse(r);
									if(r.state){
										document.getElementById('stt').value = r.state;
										ajax('/geo/cities/'+r.state+'/'+e.target.value, 'GET', function(r){
											var c = document.getElementById('city'), r = JSON.parse(r), opts = '';
											for(var n in r){
												opts += '<option value="'+n+'"'+(n == e.target.value ? ' selected' : '')+'>'+r[n]+'</option>';
											}
											c.innerHTML = opts;
										});
									}
								});
							};

							document.getElementById('brand').onchange = function(e){
								ajax('/tablet/all_models?type='+e.target.value, 'GET', function(r){
									var c = document.getElementById('model_list'), r = JSON.parse(r), opts = '';
									for(var n in r){
										opts += '<option value="'+r[n]+'">'+n+'</option>';
									}
									c.innerHTML = opts;
								});
							};

							document.getElementById('image').onchange = function(e){
								var capture = document.getElementById('capture');
								document.getElementById('webcam').style.display = 'none';
								capture.style.display = 'block';
								capture.innerHTML = '<img src="'+URL.createObjectURL(e.target.files[0])+'">';
							};
						}, 100);
					}, 'token={token}');
				break;
			}
			listen();
		});
	})();
	</script>
</body>
</html>