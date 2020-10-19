<div class="ctnr">
	<div class="page">
		<h1>Edit account</h1>
		<form class="edit-account" method="post" onsubmit="account.editUser(this, event, {id});">
			<div class="input-group">
				<label>Email</label>
				<input type="email" name="email" value="{email}">
			</div>
			<div class="input-group">
				<label>Name</label>
				<input type="text" name="name" value="{name}">
			</div>
			<div class="input-group">
				<label>Lastname</label>
				<input type="text" name="lastname" value="{lastname}">
			</div>
			<div class="input-group">
				<label>Phone</label>
				<div class="phoneZone">
					<div class="hPhone">
						<div>Country code</div>
						<div>Area code</div>
						<div>7-dt number</div>
						<div>Extension</div>
						<div>SMS</div>
					</div>
					{phone}
					[no-phone]
					<div class="sPhone">
						<span class="fa fa-times rd hide" onclick=""></span>
						<select name="phoneCode">
							<option value="0">None</option>
							<option value="+1">+1</option>
							<option value="+3">+3</option>
						</select>
						<span class="wr">(</span>
						<input type="number" name="code" onkeyup="phones.next(this, 3);" value="" max="999">
						<span class="wr">)</span>
						<input type="number" name="part1" onkeyup="phones.next(this, 7);" value="">
						<input type="number" name="part2" value="'.$n[3].'">
						<input type="checkbox" name="sms" onchange="phones.onePhone(this);">
					</div>
					[/no-phone]
					<span class="fa fa-plus plusNew nPhone" onclick="phones.newPhone();"></span>
				</div>
			</div>
			<div class="input-group">
				<label>Address</label>
				<textarea name="address">{address}</textarea>
			</div>
			<div class="input-group">
				<label>Password</label>
				<input type="password" name="password">
			</div>
			<div class="input-group">
				<label>Repeat Password</label>
				<input type="password" name="password2">
			</div>
			<div class="submit-group">
				<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
			</div>
		</form>
	</div>
</div>