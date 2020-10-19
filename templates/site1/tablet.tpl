<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        body {
            text-align: center;
            font-family: sans-serif;
            color: #888;
            line-height: 24px;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        .flex {
            display:flex;
            display: -webkit-flex; /* Safari */
            -webkit-align-items: center;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            height: 100%;
        }
        .tablet {
            width: 60%;
        }
        img {
            width: 300px;
            max-width: 100%;
        }
        h1 {
            color: #27b7c7;
        }
		.vote_list {
            display:flex;
            display: -webkit-flex; /* Safari */
            -webkit-align-items: center;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        .vote_list > a {
            display: block;
            flex: 1;
            -webkit-flex: 1;
            text-align: center;
            text-decoration: none;
            color: #000;
        }
        .vote_list > a > img {
            width: 65px;
        }
        .vote_list > a > span {
            display: block;
        }
		.vote_table {
            border:1px solid #e3e3e3;
            border-radius:5px 5px 5px 5px;
            background-color:#f3f3f3;
            padding: 10px 20px;
            text-align: left;
        }
        .agent {
            float: right;
            width: auto;
            margin-left: 10px;
            width: 200px;
        }
		h2 {
			font-size: 18px;
		}
        p:after {
            content: '';
            display: block;
            clear: both;
        }
		.thank-p {
			font-size: 18px;
		}

        @media (max-width: 1024px) {
            .tablet {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="flex">
		<div class="tablet" id="welcome">
			<img src="{theme}/img/logo.svg">
			<h1>Welcome to Your Company!</h1>
			<h2>We Know All About Computers</h2>
			<p>We have <strong>decades</strong> of <strong>experience</strong> repairing  <strong>computers</strong>, helping people live a <strong>technology</strong>, stress-free life.<br>
			Our shop first opened in 2001, in the heart of New York, Albany.<br>
			Our focus is dedicated to providing you with the most  <strong>affordable computer repairs</strong>.<br>
			The computer repairs consists of onsite and offsite <strong>computer support</strong>.</p>
		</div>
		<div class="tablet" id="thankyou" style="display: none;">
            <img src="{theme}/img/logo.svg">
            <h1>Thank You for Your Business </h1>
            <p class="thank-p">Your Company. </p>
        </div>
		<div class="tablet" id="vote" style="display: none;">
            <div class="vote_table">
                <img src="{theme}/img/logo.svg" class="agent">
                <h1>Dear <span id="name"></span>!</h1>
                <p>
                    Thank you for your recent visit to Your Company.
                </p>
                <h2>
                    How likely is it that you would recommend this company to a friend or colleague?
                </h2>
                <div class="vote_list">
                    <a href="javascript:fb(1);">
                        <img src="/feedback/images/rate_1.png">
                        <span>No, never</span>
                    </a>
                    <a href="javascript:fb(2);">
                        <img src="/feedback/images/rate_2.png">
                        <span>I'm not sure</span>
                    </a>
                    <a href="javascript:fb(3);">
                        <img src="/feedback/images/rate_3.png">
                        <span>Maybe</span>
                    </a>
                    <a href="javascript:fb(4);">
                        <img src="/feedback/images/rate_4.png">
                        <span>I will</span>
                    </a>
                    <a href="javascript:fb(5);">
                        <img src="/feedback/images/rate_5.png">
                        <span>Definitely</span>
                    </a>
                </div>
                <p>
                    Sincerely yours<br> 
                    Your Company inc.
                </p>
            </div>
        </div>
    </div>
	<script>
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
		ajax(location.href, 'POST', function(a){
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
			if(d.type == 'tablet_feedback'){
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
			}
			listen();
		});
	})();
	</script>
</body>
</html>