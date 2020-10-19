<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{title}</title>
		<link href="{theme}/css/index.css" rel="stylesheet">
		<link href="{theme}/css/grid.css" rel="stylesheet">
		<link href="{theme}/css/media.css" rel="stylesheet">
		<script src="{theme}/js/jquery-3.2.1.min.js"></script>
		<script>
		var _user = {
			lang: '{lang}',
			id: '{uid}',
			groups: '{group-ids}',
			name: '{uname}',
			lastname: '{ulastname}',
			ava: '{uava}'
		}, pf = JSON.parse('{price-formula}'), currency_val = JSON.parse('{currencies}'), quick_sell = {quick-sell}, online_users = null, camtimer = undefined;
		</script>
		<script src="{theme}/js/index.js"></script>
	</head>
   <body>
      <div class="wrapper">
         <nav>
            <div class="profile">
			   <img src="/uploads/images/users/{uid}/thumb_{uava}" align="left">
                <p>
					<font color="#333">{uname} {ulastname}</font>
					<br>
					<font color="#aaa">${points}</font>
				</p>
               <a></a>
            </div>
			<div class="search">
				<input type="search" placeholder="Navigation" list="fn-search">
				<datalist id="fn-search">
					{include="fnlist.tpl"}
				</datalist>
			</div>
            <ul>
               <li><span>Navigation</span></li>
               <li><a class="active" data-icon="&#xf00a;">Dashboard</a></li>
               <li><a data-icon="&#xf007;">Users</a></li>
               <li><a data-icon="&#xf086;">Dialogues</a></li>
               <li><a data-icon="&#xf07a;">Stores</a></li>
               <li><a data-icon="&#xf26c;">Stock & Services</a></li>
               <li><a data-icon="&#xf0d1;">Purchases</a></li>
               <li><a data-icon="&#xf217;">E-Commerce</a></li>
               <li><a data-icon="&#xf09d;">Invoices</a></li>
               <li><a data-icon="&#xf0fa;">Jobs</a></li>
               <li><a data-icon="&#xf1ec;">Cash</a></li>
               <li><a data-icon="&#xf073;">Organizer</a></li>
               <li><a data-icon="&#xf1d8;">Feedback</a></li>
               <li><a data-icon="&#xf201;">Manage</a></li>
               <li><a data-icon="&#xf03d;">Camera</a></li>
               <li><a data-icon="&#xf0a1;">Quote requests</a></li>
               <li><a data-icon="&#xf0c0;">Customer acceptions</a></li>
               <li><span>Other</span></li>
               <li><a data-icon="&#xf085;">Settings</a></li>
			   <li><a data-icon="&#xf1cd;">FAQ</a></li>
            </ul>
         </nav>
         <main>
            <header></header>
			<div class="content">
				{content}
			</div>
         </main>
      </div>
   </body>
</html>