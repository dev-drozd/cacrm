<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
	<title>{title} | Your Company</title>
	<meta name="developer" content="Alexandr Drozd <dev.drozd@gmail.com>">
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/img/favicon/android-icon-192x192.png">
	<link rel="manifest" href="/img/favicon/manifest.json">
	<meta name="theme-color" content="#ffffff">
	[description]<meta name="description" content="{description}">[/description]
	[keywords]<meta name="keywords" content="{keywords}">[/keywords]{canonical}<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="google-site-verification" content="Kz3wfCQY_gAmwrH9b2WKUhbpzRuAkXBW-0nyQBNJuYU" />
	[gpst]
	[not-gpst]
	<link rel="stylesheet" href="{theme}/css/font-awesome.min.css" />
	<link rel="stylesheet" href="{theme}/css/ui.css?v=2" />
	<link rel="stylesheet" href="{theme}/css/style.css?v=4" />
	<link rel="stylesheet" href="{theme}/css/chat.css?v=3" />
	<link rel="stylesheet" href="{theme}/css/mobile.css" />
	<script src="{theme}/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAtalQktYG8vsla1PXKMpdUOyvmYK7a7YY"></script>
	<script src="{theme}/js/ui.js"></script>
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
		};
		[not-login]
			var user = undefined;
		[/login]
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(p) {
					find_closest_marker(p.coords.latitude, p.coords.longitude);
				},
				function(e){}
			);
		}
		var lang_code = '{lang}',
			map = '',
			marker = '',
			locations = [{obj-loc}],
			bounds = new google.maps.LatLngBounds(),
			geocoder = new google.maps.Geocoder(),
			near = null;
	</script>
	<script src="{theme}/js/chat.js?v=5"></script>
	<script src="{theme}/js/main.js?v=11"></script>
	<script type="application/ld+json">
	{
	  "@context" : "http://schema.org",
	  "@type" : "LocalBusiness",
	  "name" : "Your Company",
	  "image" : "https://yoursite.com/templates/site/img/logo.svg",
	  "telephone" : "1-866-488-2806",
	  "email" : "info@yoursite.com",
	  "openingHoursSpecification" : {
		"@type" : "OpeningHoursSpecification",
		"dayOfWeek" : {
		  "@type" : "DayOfWeek",
		  "name" : "Mo, Tu, We, Th, Fr 09:00-21:00"
		}
	  },
	  "address" : {
		"@type" : "PostalAddress",
		"streetAddress" : "818 Central Ave Albany, NY 12206",
		"addressLocality" : "New York",
		"addressRegion" : "NY",
		"addressCountry" : "USA",
		"postalCode" : "12206"
	  },
	  "priceRange" : "$0+",
	  "url" : "https://yoursite.com/",
	  "aggregateRating" : {
		"@type" : "AggregateRating",
		"ratingValue" : "5",
		"bestRating" : "5",
		"ratingCount" : "1027"
	  },
	  "review" : {
		"@type" : "Review",
		"author" : {
		  "@type" : "Person",
		  "name" : "Brandon Haegele"
		},
		"reviewRating" : {
		  "@type" : "Rating",
		  "ratingValue" : "5",
		  "bestRating" : "5"
		}
	  }
	}
	</script>
	[/gpst]	
</head>
<body>
	<div id="preloader">
		<img src="{theme}/img/miniLogo.svg" alt="Your Company">
	</div>
    <div class="ctnr">
        <header class="flex">
            <div class="nav-toggle">
                <span class="fa fa-bars" onclick="$('.hdr-nav > ul').slideToggle();"></span>
            </div>
            <div class="logo">
                <a href="/" onclick="Page.get(this.href); return false"><img src="{theme}/img/logo-2.svg"></a>
            </div>
            <div class="hdr-ctnr flex">
                <div class="flex">
                    <div class="hdr-nav">
						{menu2}
                    </div>
                    <div class="hdr-phone">
                        <a href="tel:1-866-488-2806">1-866-488-2806</a>
                    </div>
                    <div class="htd-login">
                        [login]
						Hello, <a href="/account"onclick="Page.get(this.href); return false;"><span>{uname}</span></a> | <a href="/?loggout"><span>Sign out</span></a>
						[not-login]
						<a href="javascript:account.login();"><span>Sign in</span></a>
						[/login]
                    </div>
                </div>
                <div class="flex">
                    <div class="hdr-search flex">
                        <input name="search" value="{query}" onkeypress="if(event.keyCode == 13 && this.value.length > 0) Page.get('/search?query='+this.value);">
                        <span class="fa fa-search" onclick="Page.get('/search?query='+$(this).prev().val());"></span>
                    </div>
                    <div class="hdr-call-me">
                        <button class="btn btn-quote" onclick="quote.mdl();" onmousedown="gtag_report_conversion()"><h2>Get quote</h2></button>
                    </div>
                    <div class="htd-cart">
<!--                         <div class="support-nav">
                            <a href="/support" onclick="Page.get(this.href); return false;">Support</a>
                            <ul>
                                <li><a href="/support/remote-technical-support" onclick="Page.get(this.href); return false;">Remote Support</a></li>
								<li><a href="/custom-gaming-pcs" onclick="Page.get(this.href); return false;">Custom Gaming PCs</a></li>
								<li><a href="/support/virus-and-spyware-removal" onclick="Page.get(this.href); return false;">Virus, Malware & Spyware Removal Albany, Brooklyn</a></li>
                            </ul>
                        </div> -->
                        <div class="mini-cart">
                            <a href="/cart" onclick="Page.get(this.href); return false;"><span class="fa fa-shopping-cart" style="font-size: 15pt;color: #ff5000;"></span> Cart </a>
                            <ul>
                                {cart-items}
								<li class="cartSubtotal"><span>Subtotal:</span> <span class="cartPrice">${cart-subtotal}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="line">
            <span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
    
    </div>
    
	<span id="page">{content}</span>		

    <footer>
        <div class="line"></div>
        <div class="ctnr flex">
            <div class="sn">
                <ul>
                    <li><a href="https://www.facebook.com/computersanswers/"><span class="fa fa-facebook"></span></a></li>
                    <li><a href="https://twitter.com/computerans"><span class="fa fa-twitter"></span></a></li>
                    <li><a href="#"><span class="fa fa-instagram"></span></a></li>
                </ul>
            </div>
            <div>
                <img src="{theme}/img/paypal.png">
            </div>
        </div>
        <div class="line last-line">
            <div class="ctnr flex" style="text-shadow: 1px 1px 1px #000;">
                <div >{year} © Your Company. All Rights Reserved</div>
                <div><a href="#">TERMS OF SERVICE</a> | <a href="https://gdr.one" target="_blank">Devpartner</a></div>
            </div>
        </div>
    </footer>
	
	<!-- Chat -->
    <div class="back-chat" style="display: none;"></div>
    <div class="chat" id="chat" style="display: none;">
        <div class="chat-title">
            <span class="chat-name">Welcome!</span>
            <span class="chat-close"></span>
        </div>
		<span id="cnt"></span>
    </div>
    <!-- Chat -->


    <!-- Chat button -->
    <div class="chat-btn" id="chat-btn" style="display: block;" onclick="Chat.open(this);">
        <img src="{theme}/img/miniLogo.svg" alt="Your Company"> Live chat
        <span><img src="{theme}/img/chat.png"></span>
    </div>
    <!-- Chat button -->

	[gpst]
		<style>
		{include="css/font-awesome.min.css"}
		{include="css/ui.css"}
		{include="css/style.css"}
		{include="css/chat.css"}
		{include="css/mobile.css"}
		</style>
		<script src="{theme}/js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="{theme}/js/ui.js"></script>
		<script>
		$(function(){
			$('#slider').rbtSlider({
				height: '315px', 
				arrows: true
			});
		});
		</script>
	[not-gpst]
	<script>
	
		function gtag_report_conversion_chat(url) {
			return false;
		  var callback = function () {
			if (typeof(url) != 'undefined') {
			  window.location = url;
			}
		  };
		  gtag('event', 'conversion', {
			  'send_to': 'AW-1065220139/wJXhCMaE3pkBEKvw9_sD',
			  'event_callback': callback
		  });
		  return false;
		}
		
		function gtag_report_conversion(url) {
			return false;
		  var callback = function () {
			if (typeof(url) != 'undefined') {
			  window.location = url;
			}
		  };
		  gtag('event', 'conversion', {
			  'send_to': 'AW-1065220139/g0mqCNmF3pkBEKvw9_sD',
			  'event_callback': callback
		  });
		  return false;
		}
		
		function polling(){
			$.getJSON('/sub/{guest-id}.sock?time='+$.now()+'&confirm_purchase=0&chat_support=0&all=0&chat_support=0', function(j){
				switch(j.type){
					case 'support_msg':
						clearTimeout(Timeout);
						var a = new Audio();
						if(j.content){
							a.src = '/notify.mp3';
							$('#chat #cnt').html(j.content);
							$('.chat-btn').click();
						}
						a.play();
					break;
					
					case 'chat_message':
						clearTimeout(Timeout);
						var a = new Audio();
						$('.chat-btn').click();
						$('#chat .chat-dialog').append(j.message).scrollTop(999999999);
						a.src = 'http://www.formilla.com/remoteAssets/media/Notification_mp3.mp3';
						a.play();
					break;
				}
				polling();
			});
		}
		
		polling();
		
		$(function(){
		[login]
			$('body').bind("mousemove", function(e){
				if(e.pageY <= 5 && !getCookie('showWindow'))
					closeWindow();
			});
		[/login]
		});
	</script>
	[/gpst]
</body>
</html>