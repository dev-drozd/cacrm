<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Company- {title}</title>
<meta name="description" content="{description}">
<meta name="keywords" content="{keywords}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link type="text/css" rel="stylesheet" href="{theme}/css/selfserv.css?v=9">
<script src="{theme}/js/jquery-3.2.1.min.js"></script>
<script src="{theme}/js/selfserv.js?v=3"></script>
</head>
<body>
<a href="/">
	<header>
		<img src="https://yoursite.com/templates/site/img/logo.svg">
		<div class="line">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
	</header>
</a>
<main style="padding: 7em;">
	<h1>Self services</h1>
	<form class="edit-account" method="post" action="/account/send_self_services" style="margin: auto;max-width: 500px;">
		<h3>Customer information</h3>
		<div class="input-group">
			<label>* Name</label>
			<input type="text" name="name" required>
		</div>
		<div class="input-group">
			<label>* Last name</label>
			<input type="text" name="lastname" required>
		</div>
		<div class="input-group">
			<label>* Phone</label>
			<input type="tel" name="phone" placeholder="+X (XXX) XXX-XXXX" required>
		</div>
		<div class="input-group">
			<label>* Email</label>
			<input type="email" name="email" required>
		</div>
		<div class="input-group">
			<label>* Zipcode</label>
			<input type="text" name="zipcode" required>
		</div>
		<div class="input-group">
			<label>* Addres</label>
			<textarea name="address" placeholder="Your address.." required></textarea>
		</div>
		<div class="input-group">
			<label>Photo</label>
			<p align="center" id="photo" onclick="this.nextElementSibling.click();">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="90" fill="rgb(87, 193, 232)" align="center">
				<path d="M 9.4414062 3 C 8.5804062 3 7.8179219 3.5501875 7.5449219 4.3671875 L 7 6 L 4 6 C 2.897 6 2 6.897 2 8 L 2 19 C 2 20.103 2.897 21 4 21 L 20 21 C 21.103 21 22 20.103 22 19 L 22 8 C 22 6.897 21.103 6 20 6 L 17 6 L 16.455078 4.3671875 C 16.182078 3.5501875 15.419594 3 14.558594 3 L 9.4414062 3 z M 12 9 C 14.206 9 16 10.794 16 13 C 16 15.206 14.206 17 12 17 C 9.794 17 8 15.206 8 13 C 8 10.794 9.794 9 12 9 z M 12 11 A 2 2 0 0 0 10 13 A 2 2 0 0 0 12 15 A 2 2 0 0 0 14 13 A 2 2 0 0 0 12 11 z"></path>
			</svg>
			<img width="90">
			</p>
			<input type="file" name="photo" accept="image/*" capture="camera" hidden onchange="getPicture(this.files)">
		</div>
		<h3>Device information</h3>
		<div class="input-group">
			<label>* Type</label>
			<div name="type_id" json='/account/allTypes' res="list" search="ajax10" class="sfWrap" required></div>
		</div>
		<div class="input-group">
			<label>* Brand</label>
			<div name="brand" json='/account/allCategories2' res="list" search="ajax0" class="sfWrap" onchange="getModels" input required></div>
		</div>
		<div class="input-group">
			<label>* Model</label>
			<div name="model" json='' res="list" search="ajax0" class="sfWrap" input required></div>
		</div>
		<div class="input-group">
			<label>Device password</label>
			<input type="text" name="device_password">
		</div>
		<div class="input-group">
			<label>* Issue</label>
			<textarea name="issue" placeholder="Tell us about the problem.." required></textarea>
		</div>
		<h3>Other information</h3>
		<div class="input-group">
			<label>* Store</label>
			<div name="store_id" json='/account/allObjects' res="list" search="ajax10" class="sfWrap" required></div>
		</div>
		<div class="submit-group" style="text-align: left;">
			<input type="checkbox" name="terms" required>
			<label>* I agree to the <a href="/terms-and-conditions" target="_blank">terms and conditions</a></label>
		</div>
		<div class="submit-group">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Submit</button>
		</div>
	</form>
</main>
</body>
<script>
function this_submit(a){
	a.preventDefault();
	let image = $('#signature > canvas')[0].toDataURL("image/png");
	console.log(image);
	return false;
}
function getPicture(a){
	$('#photo > svg').remove();
	$('#photo > img').attr('src', URL.createObjectURL(a[0]));
}
function getModels(a){
	$('[name=model]')[0].load('/account/allModels2?type='+a);
}
$('form').inptel();
$('div[json]').json_list();
$('form').ajaxSubmit({
	callback: function(r){
		if (r == 'OK') {
			$('h1').text('Thank you!').addClass('complete');
			setTimeout(function(){
				location.reload();
			}, 1500);
			$('form').remove();
		} else {
			alert(r);
		}
	},
	check: function(){
		var t = $(this);
	}
});
</script>