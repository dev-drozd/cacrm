<style>
	.webcam {
		border: 1px dashed #C6CEDE;
		padding: 15px;
		border-radius: 4px;
	}
	.webcam > b{
		display: block;
		margin: auto;
		width: 100px;
	}
	.webcam > input {
		position: absolute;
		left: 0;
		opacity: 0;
		height: 100px;
	}
	form {
		width: 100%;
		margin-top: 20px;
	}
	fieldset {
		border-width: 1px;
		border-color: #fff;
		padding: 20px 15px;
		margin-left: -14px;
	}
	#step2 {
		margin-bottom: 500px;
	}
	button {
		width: 70%;
		background: #b6bd00;
		color: #fff;
		border: 0;
		font-size: 18px;
		line-height: 15px;
		padding: 15px;
		border-radius: 50px;
	}
	input, textarea, select {
		position: relative;
		background: transparent;
		font-size: 16px;
		border: none;
		border-bottom: 1px solid rgba(0,0,0,0.26);
		box-sizing: border-box;
		padding: 9px 0 8px;
		margin: 0 0 4px;
		transition: border-color .15s cubic-bezier(0.4,0.0,1,1);
		vertical-align: middle;
		width: 100%;
		outline: none;
		z-index: 1;
		color: black;
	}
	fieldset > p {
		position: relative;
		margin: 25px 0;
	}
	fieldset > p > label {
		display: block;
		text-align: left;
	}
	fieldset > p > span {
		display: block;
		position: absolute;
		top: 6px;
		text-align: left;
		transition: 300ms;
	}
	input:focus {
		border-bottom: 1px solid #57c1e8;
	}
	textarea:focus {
		border-bottom: 1px solid #57c1e8;
	}
	input:active + span, input:focus + span, input:valid + span {
		margin-top: -26px !important;
		color: #57c1e8;
		font-weight: bold;
	}
	textarea:active + span, textarea:focus + span, textarea:valid + span {
		margin-top: -26px !important;
		color: #57c1e8;
		font-weight: bold;
	}
	.sex {
		display:flex;
		display: -webkit-flex; /* Safari */
		-webkit-align-items: center;
		justify-content: center;
		align-items: center;
		flex-wrap: wrap;
		height: 100%;
	}
	.sex > label {
		flex: 1;
		display: block;
		text-align: center;
		padding: 20px;
		margin: 10px;
		width: 100%;
	}
	.sex > label > span {
		display: inline-block;
		width: 50px;
	}
	#step2 {
		margin-top: 50px;
		display:none;
	}
</style>
<form action="/tablet/send_acception" method="post" name="customer" enctype="multipart/form-data" id="form">
	<img src="{theme}/img/logo.svg">
	<fieldset id="step1">
		<legend>Welcome</legend>
		<p>
			<input type="text" name="first_name" required>
			<span>First Name</span>
		</p>
		<p>
			<input type="text" name="last_name" required>
			<span>Last Name</span>
		</p>
		<b>Please indicate your sex</b>
		<p class="sex">
			<label>
				<span style="width: 72px;">
					<svg xmlns="http://www.w3.org/2000/svg" style="fill: #009aff;" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">
					<g><g><g><circle cx="498.4" cy="92.5" r="82.5"/><path d="M527.1,214.3c-20.6-4.2-42.9-4-63.1,0.8C326.9,231.1,282.4,389.9,295,510.9c5.5,52.3,87.9,52.9,82.4,0c-4.6-44.1-2.6-110.7,20.8-158c0,57.5,0,115,0,172.5c0,2,0.2,3.9,0.3,5.9c-0.1,0.9-0.3,1.7-0.3,2.7c0,136.9-0.2,273.7-5.9,410.5c-2.5,60.8,91.8,60.6,94.3,0c4.4-107.2,5.5-214.5,5.8-321.7c4.5,0.3,8.9,0.3,13.4,0c0.3,107.3,1.4,214.5,5.7,321.7c2.5,60.6,96.8,60.8,94.3,0C600.2,807.7,600,670.9,600,534c0-4.2-0.6-8.1-1.4-11.8c-0.1-59.4-2.4-118.9-1.7-178.3c28.1,47.5,30.6,120,25.8,167c-5.5,52.8,77,52.3,82.4,0C717.9,387.3,671.2,224.5,527.1,214.3z"/></g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></g>
					</svg>
				</span>
				<input type="radio" name="sex" value="Male" required>Male
			</label>
			<label>
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Ebene_1" x="0px" y="0px" viewBox="-279 396.8 35.7 49.1" enable-background="new -279 396.8 35.7 49.1" xml:space="preserve">
					<path fill="pink" d="M-243.3,425.4c0,0.7-0.3,1.4-0.8,1.9s-1.2,0.8-1.9,0.8c-0.9,0-1.7-0.4-2.2-1.2l-6.3-9.5h-1.3v3.7l6.9,11.5  c0.2,0.3,0.3,0.6,0.3,0.9c0,0.5-0.2,0.9-0.5,1.3c-0.4,0.4-0.8,0.5-1.3,0.5h-5.4v7.5c0,0.9-0.3,1.6-0.9,2.2c-0.6,0.6-1.3,0.9-2.2,0.9  h-4.5c-0.9,0-1.6-0.3-2.2-0.9s-0.9-1.3-0.9-2.2v-7.6h-5.4c-0.5,0-0.9-0.2-1.3-0.5c-0.4-0.4-0.5-0.8-0.5-1.3c0-0.3,0.1-0.6,0.3-0.9  l6.9-11.5v-3.7h-1.3l-6.3,9.5c-0.5,0.8-1.3,1.2-2.2,1.2c-0.7,0-1.4-0.3-1.9-0.8s-0.8-1.1-0.8-1.8c0-0.5,0.1-1,0.4-1.5l7.1-10.7  c1.4-2,3-3,4.9-3h10.7c1.9,0,3.6,1,4.9,3l7.1,10.7C-243.4,424.3-243.3,424.8-243.3,425.4z M-256.7,398.6c1.2,1.2,1.8,2.7,1.8,4.4  c0,1.7-0.6,3.2-1.8,4.4c-1.2,1.2-2.7,1.8-4.4,1.8c-1.7,0-3.2-0.6-4.4-1.8c-1.2-1.2-1.8-2.7-1.8-4.4c0-1.7,0.6-3.2,1.8-4.4  s2.6-1.8,4.4-1.8C-259.4,396.8-257.9,397.4-256.7,398.6z"/>
					</svg>
				</span>
				<input type="radio" name="sex" value="Female" required>Female
			</label>
		</p>
		<p>
			<input type="tel" name="phone" required>
			<span>Phone:</span>
		</p>
		<p>
			<input type="email" name="email" required>
			<span>Email:</span>
		</p>
		<p>
			<input type="date" name="date" required>
			<span>Birth Date:</span>
		</p>
		<p>
			<input type="text" name="zipcode" id="zipcode" required>
			<span>ZipCode:</span>
		</p>
		<p>
			<label>State</label>
			<select name="state" id="stt">
				{states}
			</select>
		</p>
		<p>
			<label>City</label>
			<select name="city" id="city"></select>
		</p>
		<p>
			<textarea name="address" required></textarea>
			<span>Address:</span>
		</p>
		<p>
			<input type="password" name="password" required>
			<span>Password:</span>
		</p>
		<p>
			<input type="password" name="password2" required>
			<span>Confirm password:</span>
		</p>
		<p class="webcam" id="webcam">
			<input type="file" accept="image/*;capture=camera" capture="camera" name="image" id="image">
			<b><svg style="fill: #57c1e8;" version="1.1" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="info"/><g id="icons"><path d="M19,7h-0.4c-0.4,0-0.7-0.2-0.9-0.6l-1.2-2.3c-0.3-0.7-1-1.1-1.8-1.1H9.2C8.5,3,7.8,3.4,7.4,4.1L6.3,6.4   C6.1,6.8,5.8,7,5.4,7H5c-2.2,0-4,1.8-4,4v6c0,2.2,1.8,4,4,4h14c2.2,0,4-1.8,4-4v-6C23,8.8,21.2,7,19,7z M12,17c-2.2,0-4-1.8-4-4   c0-2.2,1.8-4,4-4s4,1.8,4,4C16,15.2,14.2,17,12,17z" id="photo"/></g></svg></b>
			Click to make Selfie
		</p>
		<p id="capture"></p>
		<p style="border-top: 1px solid #ddd; padding-top: 30px; margin: 25px -15px;">
			<button type="submit">Next steep</button>
		</p>
	</fieldset>
	<fieldset id="step2">
		<legend>Adding a device</legend>
		<p>
			<label>Type</label>
			<select name="type" id="type" disabled>
				<option value="">None</option>
				<option value="131">Accessories</option>
				<option value="96">AIO (All In One)</option>
				<option value="154">Alarm Clock/House Radio</option>
				<option value="126">Ampoptionfier</option>
				<option value="123">Battery</option>
				<option value="153">Bezel</option>
				<option value="109">Cable</option>
				<option value="111">Camera</option>
				<option value="156">Capacitor</option>
				<option value="141">Case</option>
				<option value="139">Case Fan</option>
				<option value="114">CD/DVD Drive</option>
				<option value="150">Charger</option>
				<option value="134">Charging port</option>
				<option value="110">CPU</option>
				<option value="140">CPU Fan</option>
				<option value="142">DC Jack</option>
				<option value="92">Desktop</option>
				<option value="117">Desktop Case</option>
				<option value="152">Digitizer</option>
			</select>
		</p>
		<p>
			<label>Brand</label>
			<select name="brand" id="brand" disabled>
				<option value="">None</option>
				<option value="131">3D Connexion</option>
				<option value="5">Acer</option>
				<option value="51">Adata</option>
				<option value="119">Alcatel</option>
				<option value="122">Aloptioned</option>
				<option value="63">Amazon</option>
				<option value="29">AMD</option>
				<option value="94">Anker</option>
				<option value="37">Antec</option>
				<option value="50">AOC</option>
				<option value="110">APower</option>
				<option value="1">Apple</option>
				<option value="138">ASRock</option>
				<option value="2">Asus</option>
				<option value="97">Beats</option>
				<option value="123">Bixolon</option>
				<option value="134">Black and Decker</option>
				<option value="43">BOSE</option>
				<option value="32">Brother</option>
				<option value="145">Bywater</option>
			</select>
		</p>
		<p>
			<input type="text" name="model" list="model_list" id="model" disabled>
			<span>Model:</span>
			<datalist id="model_list"></datalist>
		</p>
<!-- 		<p>
			<label>Model</label>
			<select name="model" id="model" disabled>
				<option value="">None</option>
			</select>
		</p> -->
		<p><a href="#">Additional Information</a></p>
		<p style="border-top: 1px solid #ddd; padding-top: 30px; margin: 25px -15px;"><button type="submit">Complete</button></p>
	</fieldset>
</form>
<script>
window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {
  alert(errorMsg);

  return false;
};

document.getElementById('stt').onchange = function(e){
	console.log('ok');
	//ajax('https://admin.yoursite.com/geo/cities'+e.target.value, 'GET', function(r){
	//	console.log(r);
		//document.getElementById('city').innerHTML = a;
	//});
};

document.getElementById('form').onsubmit = function(e){
	if(!e.target.type.value.length){
		e.preventDefault();
		document.getElementById('step1').style.display = 'none';
		document.getElementById('step2').style.display = 'block';
		return false;
	}
};

document.getElementById('image').onchange = function(e){
	var capture = document.getElementById('capture');
	document.getElementById('webcam').style.display = 'none';
	capture.style.display = 'block';
	capture.innerHTML = '<img src="'+URL.createObjectURL(e.target.files[0])+'">';
};
</script>