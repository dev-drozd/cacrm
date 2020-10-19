<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="shortcut icon" href="{theme}/img/gear.ico" type="image/x-icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{lang=Authorization}</title>
	<link rel="stylesheet" href="{theme}/css/font-awesome.min.css">
	<link rel="stylesheet" href="{theme}/css/ui.css">
	<link rel="stylesheet" href="{theme}/css/style.css?v=2">
	<link rel="stylesheet" href="{theme}/css/mobile.css">
	<style>
	.google {
		margin-left: 5px;
		background: #de5543;
		color: #fff;
		padding: 10px;
		font-size: 17px;
		height: 42px;
		line-height: 20.5px;
		border-radius: 3px;
		outline: none;
		text-align: center;
		border: 1px solid #c74f3f;
	}
	.google:hover {
		border: 1px solid #de5543;
		background: #fff;
		color: #de5543;
	}
	</style>
</head>
	<body class="vAlign">
		<div class="login">
			<div class="logo">
				<img src="{theme}/img/cacrm-logo-2.svg">
				<!--<span class="fa fa-lock"></span>-->
			</div>
			<div class="form">
				<div class="fGroup">
					<span class="fa fa-at"></span>
					<input type="text" placeholder="Email" id="email">
				</div>
				<div class="fGroup">
					<span class="fa fa-asterisk"></span>
					<input type="password" placeholder="{lang=Password}" id="password" onkeypress="if(event.keyCode == 13) login();">
				</div>
				<div style="display: flex">
					<button class="btn btnLogin" style="flex:1" onclick="login()">{lang=SignIn}</button>
					<a href="{google-lnk}" class="google fa fa-google-plus" title="Sign in with Google"></a>
				</div>
			</div>
		</div>
		<div class="preloader">
			<span class="fa fa-cog fa-spin fa-5x fa-fw"></span>
			<span class="fa fa-cog fa-spin fa-3x fa-fw"></span>
			<div>Please, wait...</div>
		</div>
		
		<script src="{theme}/js/jquery-3.1.0.js"></script>
		<script src="{theme}/js/ui.js"></script>
		<script src="{theme}/js/main.js"></script>
		<script>
		function is_valid_email(a){
			var p = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
			return a.match(/@/) ? p.test(a) : true;
		}

		function login(){
			var email = $('#email').val().trim(), password = $('#password').val();
			if(is_valid_email(email) && password.length > 0 && email.length > 0){
				$('.login').hide(0);
				$('.preloader').show(0);
				$.post('/?login', {
					email: email,
					password: password
				}, function(r){
					if (r == 'OK') location.href = '/';
					else if($.isNumeric(r))
						location.href = location.href+'/users/edit/'+r;
					else if (r == 'IP') {
						alr.show({
							class: 'alrDanger',
							content: 'You can not login from this IP',
							delay: 2
						});
						$('.login').show(0);
						$('.preloader').hide(0);
					} else {
						alr.show({
							class: 'alrDanger',
							content: '{lang=wPass}',
							delay: 2
						});
						$('.login').show(0);
						$('.preloader').hide(0);
					}
				});
			} else if (email.length == 0) {
				alr.show({
					class: 'alrDanger',
					content: '{lang=eEmail}',
					delay: 2
				});
			} else if (!is_valid_email(email)) {
				alr.show({
					class: 'alrDanger',
					content: '{lang=vEmail}',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: '{lang=ePass}',
					delay: 2
				});
			}
		}
		</script>
		<!-- <script src="{theme}/js/jquery-3.1.0.js"></script> -->
	</body>
</html>