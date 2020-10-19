<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="{theme}/img/gear.ico" type="image/x-icon" />
        <title>{title}</title>
		<meta name="description" content="{description}">
		<meta name="keywords" content="{keywords}">
		{add-tags}
<!-- 		<link rel="apple-touch-icon" sizes="57x57" href="/img/favicon//apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/img/favicon//apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/img/favicon//apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/img/favicon//apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/img/favicon//apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/img/favicon//apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/img/favicon//apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/img/favicon//apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/img/favicon//apple-icon-180x180.png"> -->
<!-- 		<link rel="icon" type="image/png" sizes="192x192"  href="/img/favicon//android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon//favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/img/favicon//favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon//favicon-16x16.png"> -->
		<link rel="manifest" href="/img/favicon//manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/img/favicon//ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
		<meta property="og:title" content="{title}">
		<link rel="icon" type="image/png" sizes="192x192"  href="{or:image}">
		<meta property="og:image" content="{or:image}">
		<meta property="og:description" content="{description}">
		<!-- <meta name="twitter:image" content="https://yoursite.com/img/logo.png"> -->
		<link rel="stylesheet" type="text/css" href="{theme}/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="{theme}/css/ui.css">
		<link rel="stylesheet" type="text/css" href="{theme}/css/style.css">
		<link rel="stylesheet" type="text/css" href="{theme}/css/mobile.css">
		<script src="{theme}/js/jquery-3.2.1.js"></script>
		<!--<script src="{theme}/js/lang/{lang}.js"></script>-->
		<!--<script src="{theme}/js/ui.js"></script>-->
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-91299623-1', 'auto');
		  ga('send', 'pageview');
		
			[login]
			var user = {
				id: {uid}
			}
			[/login]
			
			var lang_code = '{lang}';
		</script>
		<script src="{theme}/js/ui.min.js"></script>
		<script src="{theme}/js/chat.js"></script>
		<script src="{theme}/js/main.js?v=2"></script>
		<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAtalQktYG8vsla1PXKMpdUOyvmYK7a7YY"	></script>
	</head>
	<body>
		<header class="hdr">
			<div class="ctnr">
				<div class="logo">
					<a href="/" onclick="Page.get(this.href); return false;">
						<img src="{theme}/img/logo2.svg" alt="{title}">
					</a>
				</div>
				<div class="uMenu">
					<ul>
						[login]
						<li><a href="/account"onclick="Page.get(this.href); return false;"><span>Account</span></a></li>
						<li><a href="/?loggout"><span>Sign out</span></a></li>
						[not-login]
						<li><a href="javascript:account.login();"><span>Sign in</span></a></li>
						[/login]
					</ul>
				</div>
				<div class="hCart">
					<div class="fa-shopping-cart" onclick="Page.get('/cart');">
						<span class="fa fa-shopping-cart"></span>
						[nocart][not-nocart]<i>{cart-count}</i>[/nocart]
					</div>
					<div class="miniCart[nocart] nCart[/nocart]">						
						<div class="nItems">No items</div>			
						<ul class="cartItems">
							{cart-items}
						</ul>
						<div class="cartSubtotal">Subtotal: <span class="cartPrice">${cart-subtotal}</span></div>
					</div>
				</div>
				<div class="hContacts">
					<div class="hcText">Call us: </div>
					1-866-488-2806
 				</div>
				<div class="hSearch">
					<input type="text" placeholder="Search">
					<span class="btnSearch fa fa-search"></span>
				</div>
			</div>
		</header>

		<nav class="nvg">
			<div class="ctnr">
				<a href="/account">
					<img src="{theme}/img/miniLogo.svg" alt="{title}">
				</a>
				<div class="nvgBurger" onclick="$(this).next().slideToggle();"><span class="fa fa-bars"></span></div>
				{menu}
				<div class="hCart">
					<div class="fa-shopping-cart" onclick="Page.get('/cart');">
						<span class="fa fa-shopping-cart"></span>
						[nocart][not-nocart]<i>{cart-count}</i>[/nocart]
					</div>
					<div class="miniCart[nocart] nCart[/nocart]">						
						<div class="nItems">No items</div>			
						<ul class="cartItems">
							{cart-items}
						</ul>
						<div class="cartSubtotal">Subtotal: <span class="cartPrice">${cart-subtotal}</span></div>
					</div>
				</div>
				<!--<div class="hSearch">
					<input type="text" placeholder="Search">
					<span class="btnSearch fa fa-search"></span>
				</div>-->
			</div>
		</nav>
		
		<div id="page">
			{content}
		</div>
		
		<footer class="ftr dClear">
			<div class="ctnr">
				<div class="snArea">
					<ul>
						<li><a href="#"><span class="fa fa-facebook"></span></a></li>
						<li><a href="#"><span class="fa fa-twitter"></span></a></li>
						<li><a href="#"><span class="fa fa-instagram"></span></a></li>
					</ul>
				</div>
				<div class="pp">
					<img src="{theme}/img/paypal.png" />
					<p><a href="/terms" style="color: #515151;" onclick="Page.get(this.href); return false;">TERMS OF SERVICE</a></p>
				</div>	
			</div>	
		</footer>

		<div class="copyright">
			{this-year} &copy; Your Company. All Rights Reserved
		</div>
		
		[owner]<a href="/?new_design=1" class="new-design">New design</a>[/owner]
		<script>
		function polling(){
			$.getJSON('/sub/{guest-id}.sock?time='+$.now()+'&confirm_purchase=0&chat_support=0&all=0&chat_support=0', function(j){
				switch(j.type){
					case 'support_msg':
						clearTimeout(Timeout);
						var a = new Audio();
						if(j.content){
							a.src = '/notify.mp3';
							$('#chat #cnt').html(j.content);
							$('#chat .chatTitle').mousedown();
						}
						a.play();
					break;
					
					case 'chat_message':
						clearTimeout(Timeout);
						var a = new Audio();
						$('#chat .chatTitle').mousedown();
						$('#chat .dialog').append(j.message).scrollTop(999999999);
						a.src = 'http://www.formilla.com/remoteAssets/media/Notification_mp3.mp3';
						a.play();
					break;
				}
				polling();
			});
		}
		polling();
		$(function() {
		[login]
			$('body').bind("mousemove", function(e){
				if(e.pageY <= 5 && !getCookie('showWindow'))
					closeWindow();
			});
		[/login]
		
			$('li.dd > a').on('touchstart', function() {
				if ($(window).width() <= 991) {
					$(this).next().slideToggle();
					mob = 1;
				}
			});
		})
		</script>
	</body>
</html>