<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="shortcut icon" href="{theme}/img/gear.ico" type="image/x-icon" />
		<meta name="developer" content="Alexandr Drozd <dev.drozd@gmail.com>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv='Content-Language' content='en_EN'>
		<title>{title}</title>
		<!-- <script async>if(window.width <= 1410) document.body.className = "nav-hide";</script> -->
		<link rel="stylesheet" href="{theme}/css/font-awesome.min.css?86000">
		<link rel="stylesheet" href="{theme}/css/ui.css?86000&v=2">
		<link rel="stylesheet" href="{theme}/css/style.css?v=2567">
		
		<!-- NEW CSS -->
		<link rel="stylesheet" href="{theme}/new-css/index.css?v=2680">
		<link rel="stylesheet" href="{theme}/new-css/new.css">
		<!--/ NEW CSS -->
		
		<link rel="stylesheet" href="{theme}/css/feditor.css?86000&v=2">
		<link rel="stylesheet" href="{theme}/css/mobile.css?86000&v=4">
		<link rel="stylesheet" href="{theme}/css/json-list.css">
		[training]<style>body {background: #9e9e9e36}</style>[/training]
		<script>
		var _user = {
				lang: '{lang}',
				id: '{uid}',
				groups: '{group-ids}',
				name: '{uname}',
				lastname: '{ulastname}',
				ava: '{uava}'
			}, 
			pf = JSON.parse('{price-formula}'),
			currency_val = JSON.parse('{currencies}'),
			quick_sell = {quick-sell},
			online_users = null,
			camtimer = undefined;
			var aaSnowConfig = {snowflakes: '200'};
		</script>
		<script src="{theme}/js/jquery.min.js?86000&v=2"></script>
		<script src="{theme}/js/notifications.js?v=286005"></script>
		<script src="{theme}/js/ui.js"></script>
		<script src="{theme}/js/ui-min.js"></script>
		<!-- <script src="{theme}/js/main[dev].dev[/dev].js?v=9991"></script> -->
		
		<!-- NEW JAVASCRIPT -->
			<script src="{theme}/new-js/index.js"></script>
			<script src="{theme}/new-js/dashboard.js"></script>
		<!--/ NEW JAVASCRIPT -->
		
		<script src="{theme}/js/main.js?v=380067"></script>
		<script src="{theme}/js/index.js"></script>
		<script src='{theme}/js/feditor.js?86000'></script>
		<script src='{theme}/js/chart.min.js?86000'></script>
		<script src='{theme}/js/im.js?86000'></script>
		<script src='{theme}/js/chat.min.js?86001'></script>
<!-- 		<script>
		$(document).ready(function(){
			if ($(window).width() >= 992)
				menuSize();
		});
		   (function(w,d,s,g,js,fs){
			 g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
			 js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
			 js.src='https://apis.google.com/js/platform.js';
			 fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
		   }(window,document,'script'));
		</script> -->
	</head>
	<body>
	[print]
<!-- Navigation -->
		<header>
			<span class="fa fa-bars" onclick="$('body').toggleClass('nav-hide')"></span>
			<div class="main-search" id="main-search">
				<input type="text" placeholder="&#xf002; Start typing or press F2 to say" onkeydown="Rec.on(event);" class="fa" onkeyup="mainSearch(this.value);" oninput="checkBarcode(this.value);" autofocus>
				<div class="searchResult hdn"></div>
			</div>
			<ul class="rMenu hMenu">
				<li class="addTask">
					<a href="javascript:tab.add();" class="hnt hntBottom" data-title="Add tab"><span class="fa fa-star"></span></a>
				</li>
				<li class="addTask">
					<a href="javascript:tasks.add();" class="hnt hntBottom" data-title="Add task"><span class="fa fa-exclamation"></span></a>
				</li>
				<!-- <li id="points_top" class="fa" onclick="Page.get('/users/point_details/{uid}');">{points}</li> -->
				<li class="notifications nfc">
					<span class="fa fa-bell nfClick nfc" onclick="Notify.get();"></span>
					<span[notify] class="nfCount nfc pulse"[/notify] id="notify">{notify-count}</span>
					<div class="nfArea nfc" onclick="$(this).hide();"></div>
				</li>
				[show]
				[timer-enable]
				<li class="timer">
					<span class="timerClock" value="{timer}" data-id="{timer-id}" onclick="timer.week();">0:00:00</span>
					<a href="javascript:timer.confirm('[timer]pause[not-timer]start[/timer]');" class="hnt hntBottom" data-title="[timer]Punch out[not-timer]Punch in[/timer]"><span class="fa fa-[timer]pause[not-timer]play[/timer]"></span></a>
					<a href="javascript:timer.confirm('stop');"  class="hnt hntBottom" data-title="Punch out"><span class="fa fa-stop"></span></a>
				</li>
				[/timer-enable]
				[/show]
				<li>
				<div class="header-profile">
					[ava]<img src="/uploads/images/users/{uid}/thumb_{uava}" align="left">[not-ava]<span class="fa fa-user-secret"></span>[/ava]
					<b>{uname} {ulastname}</b><br>
					<span id="auth_store">[auth-store]Authorized in {store-name}[not-auth-store]not authorized in store[/auth-store]</span><br>
					<a href="/users/point_details/{uid}" onclick="Page.get(this.href); return false">
						<span class="fa fa-money"></span> <span id="points_top">{points}</span>
					</a>
					<ul>
						<li><a href="/users/view/{uid}" onclick="Page.get(this.href); return false;">View profile</a></li>
						<li><a href="/users/edit/{uid}" onclick="Page.get(this.href); return false;">Edit profile</a></li>
						[logout-user]<li><a href="/users/user_auth">Logout user</a></li>[/logout-user]
						<li><a href="https://yoursite.com" target="_blank">View website</a></li>
						<li><a href="/?loggout=admin">Loggout</a></li>
					</ul>
				</div>
<!-- 			<div class="profile flex" onclick="lPanel(event)">
			    [ava]<img src="/uploads/images/users/{uid}/thumb_{uava}" align="left">[not-ava]<span class="fa fa-user-secret"></span>[/ava] 
				<div>
					{uname} {ulastname}
					<br>
					<span id="auth_store">[auth-store]Authorized in {store-name}[not-auth-store]not authorized in store[/auth-store]</span>
				</div>
			    <div id="pnl-ellipsis">
					<span class="fa fa-ellipsis-v" style="position: absolute;padding: 15px;right: 0;"></span>
					<ul>
						<li><a href="/users/view/{uid}" onclick="Page.get(this.href); $(this).parents('ul').hide(); return false;">View profile</a></li>
						<li><a href="/users/edit/{uid}" onclick="Page.get(this.href); $(this).parents('ul').hide(); return false;">Edit profile</a></li>
						[logout-user]<li><a href="/users/user_auth">Logout user</a></li>[/logout-user]
						<li><a href="/?loggout=admin">Loggout</a></li>
					</ul>
			    </div>
			</div> -->
			
				<!-- <a href="//yoursite.com/" target="_blank"><span class="fa fa-external-link" title="Go to website"></span><span>{lang=to_site}</span></a> -->
				
				</li>
				<!-- <li class="logout"><a href="/?loggout=admin" onclick="$(this).remove();"><span class="fa fa-sign-out" title="Logout"></span><span></span></a></li> -->
<!-- 				<li><a href="//yoursite.com/" target="_blank"><span class="fa fa-external-link" title="Go to website"></span><span>{lang=to_site}</span></a></li>
				<li class="logout"><a href="/?loggout=admin" onclick="$(this).remove();"><span class="fa fa-sign-out" title="Logout"></span><span></span></a></li> -->
			</ul>
			<nav class="fav-tabs">{tabs}</nav>
		</header>
		<nav class="left-nav">
			<a href="/" class="flex logo-panel" onclick="Page.get(this.href); return false;">
			    <img src="{theme}/img/cacrm-logo-2.svg">
			</a>
			<div class="fn-search">
				<input type="search" placeholder="&#xf002; Navigation" list="search-functions" oninput="fnGet(this)" class="fa">
				{include="fnlist.tpl"}
			</div>
		[dev]
<!-- 		<div class="code">
			<p>Memory: <span id="cpu">{cpu}</span>mb</p>
			<p>Now online: <span>25000</span> staff</p>
			<p>Total memory: <span id="cpu-total">{cpu-total}</span>mb</p>
			<p>Time script: <span id="time_script">{time-script}</span>.sec</p>
			<p>Compiled templates: <span id="compile_tpl">{compile-tpl}</span></p>
			<p>Cache queries: <span id="query_cache">{query-cache}</span></p>
			<p>DB queries: <span id="query_db">{query-db}</span></p>
		</div>
		<style>
		.left-nav > ul {
			height: calc(100% - 410px);
		}
		</style> -->
		[/dev]
<!-- 			<div class="left-nav-tabs">
				<a href="#" class="active" onclick="return leftPanel.tab(this, 'nav');">
					<span class="fa fa-bars"></span> Menu
				</a>
				<a href="#" onclick="return leftPanel.tab(this, 'chat');">
					<span class="fa fa-comment"></span> Chat
				</a>
			</div> -->
			<ul class="left-nav-list">
				<li><a href="/" onclick="Page.get(this.href); return false;"><span class="fa fa-dashboard"></span><span class="dText">Dashbord</span></a></li>
				<li id="new_bug"><a href="/bugs" onclick="Page.get(this.href); return false;"><span class="fa fa-bug"></span><span>Bugs</span> {new-bugs}</a></li>
				<li><a href="https://mail.yoursite.com" target="_blank"><span class="fa fa-envelope"></span><span>Comppany email</span></a></li>
				[users]<li>
					<a href="#" onclick="$(this).next().slideToggle(); return false;">
						<span class="fa fa-user"></span>{lang=Users} <i class="fa fa-chevron-down" style="float: right;"></i>
					</a>
					<ul>
						<li><a href="/users" onclick="Page.get(this.href);return false;">All users</a></li>
						<li><a href="/users/company" onclick="Page.get(this.href);return false;">Companies</a></li>
					{groups}
					</ul>
				</li>[/users]
				[im]<li id="new_msg"><a href="/im" onclick="Page.get(this.href); return false;"><span class="fa fa-comments"></span><span>{lang=IM}</span> {new-msg}</a></li>[/im]
				<li id="new_quote"><a href="/quote" onclick="Page.get(this.href); return false;"><span class="fa fa-money"></span><span>Quote requests</span> {new-quotes}</a></li>
				[store]<li><a href="/objects" onclick="Page.get(this.href); return false;"><span class="fa fa-shopping-cart"></span><span>{lang=Objects}</span></a></li>[/store]
				[service]
				<li>
					<a href="/onsite" onclick="Page.get(this.href); return false;">
						<span class="fa fa-truck"></span><span>Onsite services</span>
					</a>
				</li>
				<li><a href="/inventory" onclick="Page.get(this.href); return false;"><span class="fa fa-laptop"></span><span>{lang=Stock&Service}</span></a></li>[/service]
		<!-- 		[purchase]<li><a href="/purchases" onclick="Page.get(this.href); return false;"><span class="fa fa-desktop"></span><span>{lang=Purchase}</span></a></li>[/purchase] -->
				[purchase]<li><a href="/buy" onclick="Page.get(this.href); return false;"><span class="fa fa-desktop"></span><span>{lang=Purchase}s<span> <!-- <sup style="color:#d04646">(beta)</sup> --></span></a></li>[/purchase]
				[commerce]<li><a href="/store" onclick="Page.get(this.href); return false;"><span class="fa fa-cart-plus"></span><span>{lang=ECommerce}</span>[e-order]<span class="nfCount nfc">{e-order}</span>[/e-order]</a></li>[/commerce]
				[invoces]<li><a href="/invoices" onclick="Page.get(this.href); return false;"><span class="fa fa-credit-card"></span><span>{lang=Invoices}</span></a></li>[/invoces]
				<li><a href="/activity/issues" onclick="Page.get(this.href); return false;"><span class="fa fa-question-circle"></span><span>Jobs</span></a></li>
				[cash]<li><a href="/cash" onclick="Page.get(this.href); return false;"><span class="fa fa-calculator"></span><span>{lang=Cash}</span></a></li>[/cash]
				[organizer]<li><a href="/organizer" onclick="Page.get(this.href); return false;"><span class="fa fa-calendar"></span><span>{lang=Organizer}</span></a></li>[/organizer]
				[feedback]<li><a href="/feedbacks" onclick="Page.get(this.href); return false;"><span class="fa fa-paper-plane"></span><span>Feedbacks</span></a></li>[/feedback]
				[analytics]<li><a href="/analytics/tradein" onclick="Page.get(this.href); return false;"><span class="fa fa-line-chart"></span><span>Manage</span></a></li>[/analytics]
				<li><a href="/faq" onclick="Page.get(this.href); return false;"><span class="fa fa-question-circle"></span><span>FAQ</span></a></li>
				[camera]<li><a href="/camera" onclick="Page.get(this.href); return false;"><span class="fa fa-camera"></span><span>Camera</span></a></li>[/camera]
				<li><a href="/tablet/customers" onclick="Page.get(this.href); return false;"><span class="fa fa-users"></span><span>Customer acceptions</span></a></li>
				[settings]<li><a href="/settings" onclick="Page.get(this.href); return false;"><span class="fa fa-gear"></span><span>{lang=Settings}</span></a></li>[/settings]
			</ul>
			<div class="left-chat-list">
				<input type="text" id="mini-chat-search" placeholder="Enter name" onkeyup="Chat.search(this.value)">
				<span class="fa fa-plus" id="btn-add-group" onclick="Im.createGroup()"></span>
				<div class="mini-chat-person" data-id="0" onclick="Chat.open(this);">
					<span class="fa fa-users miniRound"></span>
					 Group chat
				</div>
				<ul>{ims}</ul>
				<div class="left-nav-tabs-botton">
					<a href="#" class="active" onclick="return leftPanel.tab(this, 'staffs');">
						<span class="fa fa-users"></span>
						<br>
						Staffs
					</a>
					<a href="#" onclick="return leftPanel.tab(this, 'support');">
						<span class="fa fa-life-ring"></span>
						<br>
						Support
					</a>
					<a href="#" onclick="return leftPanel.tab(this, 'emails');">
						<span class="fa fa-envelope"></span>
						<br>
						Emails
					</a>
				</div>
			</div>
		</nav>
		<div class="nav-bg" onclick="$('body').removeClass('nav-hide')"></div>
		[/print]
		
		<main class="ctnr" id="page">
			{content}
		</main>
		
		<div class="right-fixed">
			<a href="/bugs" onclick="bugs.add(); return false;" class="bugBtn"><span class="fa fa-bug"></span></a>
			<a href="javascript:downapp();" class="androidBtn hnt hntLeft" data-title="Download apk"><span class="fa fa-android"></span></a>
		</div>
		
<!-- 		<div class="chat hide" id="chat">
			<div class="chatTitle">
				<span class="fa fa-comment"></span> Live chat
				<span class="fa fa-times"></span>
			</div>
			<span id="cnt"></span>
		</div> -->
		
		<!-- <a href="https://dadmin.yoursite.com" class="nsystem">Back to the old system (stable)</a> -->
		
		[print]
		<div id="dragAlert">text</div>
		<div class="mobBtn">
			<ul>
				<!-- <li><a href="/uploads/apps/yoursite.apk?v=4" download><span class="fa fa-comment"></span></a></li> -->
				<li><a href="https://yoursite.com" target="_blank"><span class="fa fa-external-link"></span></a></li>
				<li><a href="/?loggout=admin"><span class="fa fa-sign-out"></span></a></li>
			</ul>
			<button onclick="$(this).parent().toggleClass('open');"><span class="fa fa-plus"></span></button>
		</div>
		[/print]
		
		<script>
		setInterval(function(){
			if(Number($('#new_quote .newMsg').text()) > 0){
				quoteBox();
			}
		}, 300000)
		var lPanel = function(a){
		console.log(a.target);
			if($(a.target).hasClass('fa-ellipsis-v'))
				$(a.target).next().toggle(0);
			else if(!$(a.target).parents('#pnl-ellipsis').length)
				Page.get('/users/view/{uid}');
			else
				return false;
		};
		var p_timer = [];
		$(document).ready(function(){
			//lprsz();
			var etarget = {};
			Notify.pending('{user-token}', {long-polling});
			timer.winStart();
			$('body').on('click', function(event) {
				if (!$(event.target).hasClass('cl') && !$(event.target).hasClass('nc') && !$(event.target).parents('.tabs').length) {
					$('.calendar').hide().parents('.iGroup').removeClass('act');
					//if ($('input[name="sText"]').length) $('input[name="sText"]').focus();
				}
				if (!$(event.target).parents('.dd').length) {
					$('.uMore > ul').hide(0, function() {
						if ($(event.target).hasClass('fa-ellipsis-h') || $(event.target).hasClass('togMore') || $(event.target).hasClass('fa-ellipsis-v') || $(event.target).hasClass('showFll')) {
							$(event.target).parents('.uMore').find('ul').first().show();
							return false;
						}
					});
				}
			});
			
			$( window ).on('resize', function(e) {
				clearTimeout(timeMenu);
				$('.navMore').slideUp('fast');
				timeMenu = setTimeout('menuSize()', 200);
			});
			$(window).scroll(Page.check_top);
			
			//if (purchases_dep = {purchases}) {
			//	$.each(purchases_dep, function(i, v) {
					//p_timer[v.id] = setInterval(function() {
						//show_notif(v);
					//}, 300000);
			//	})
			//}
			Page.init();
			[from-login]downapp();[/from-login]
		});
		
		var width = $(window).width(),
			timeMenu = undefined;
		function menuSize() {
			if ($(window).width() > 991) {
				if ($('.lMenu').width() > ($('.nvg').width() - $('.rMenu').width() - 100)) {
					$('.navMoreBtn').show();
					$('.lMenu li').last().prependTo('.navMore');	
					menuSize();
				} else {
					if ($(window).width() <= width) {
						width = $(window).width();
						return false;
					} else {
						width = $(window).width();
						$('.navMore li').first().appendTo('.lMenu');
						if (!$('.lMenu li').length) $('.navMoreBtn').hide();
						menuSize();
					}
				}
			} else {
				if ($('.navMore li').length) {
					$('.lMenu').append($('.navMore').html());
					$('.navMore').empty();
				}
				$('.navMoreBtn').hide();
			}
		}
		function menuMore() {
			$('.navMore').css('left', $('.navMoreBtn').offset().left).slideToggle('fast');
		}
		
		function show_notif(v) {
			var alrt = $('<div/>', {
				class: 'notif not_deposit',
				css: {
					opacity: 0,
					right: '-300px'
				},
				html: $('<div/>', {
					class: 'tNotif',
					html: _user.name + ' have not get deposite for purchase #' + v.id + '. Be aware, that purchase wouldn\'t be comfirmed '
				}).click(function(){
					Page.get('/purchases/edit/' + v.id);
					$(this.parentNode).remove();
				})
			}).append($('<button/>', {
				class: 'btn',
				html: 'OK'
			}).click(function(){
				Page.get('/purchases/edit/' + v.id);
				$(this.parentNode).remove();
			})).append($('<span>', {
				class: 'fa fa-times'
			}).click(function(){
				$(this.parentNode).remove();
			})).appendTo('body').animate({
				right: 20,
				opacity: 1
			}, 300);
			setTimeout(function(){
				alrt.animate({
					right: '-300',
					opacity: 0
				}, 300, function(){
					this.remove();
				});
			}, 300000);
		}
		//$("body").swipe({
		//  swipeLeft:function(event, direction, distance, duration, fingerCount) {
		//	if($(window).width() <= 1410)
		//		$('body').removeClass('nav-hide');
		//		return false;
		//  },swipeRight:function(event, direction, distance, duration, fingerCount) {
		//	if($(window).width() <= 1410)
		//		$('body').addClass('nav-hide');
		//		return false;
		//  },
		//  excludedElements: "label, button, input, select, textarea, h1, p, a"
		//});
		</script>
		<div class="top_page" onclick="Page.up();">
			<li class="fa fa-arrow-up"></li>
		</div>
		<audio id="notify_sound" hidden></audio>
	</body>
</html>