<div class="ctnr">

	<div class="account">
		<div class="paTitle">Edit account</div>
		<div class="dClear">
			{include="/account/left.tpl"}
			<div class="iSide">
				<form class="uForm" method="post" onsubmit="account.editUser(this, event, {id});">
					<div class="iGroup">
						<label>Email</label>
						<input type="email" name="email" value="{email}">
					</div>
					<div class="iGroup">
						<label>Name</label>
						<input type="text" name="name" value="{name}">
					</div>
					<div class="iGroup">
						<label>Lastname</label>
						<input type="text" name="lastname" value="{lastname}">
					</div>
					<div class="iGroup">
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
					<div class="iGroup">
						<label>Address</label>
						<textarea name="address">{address}</textarea>
					</div>
					<div class="iGroup">
						<label>Password</label>
						<input type="password" name="password">
					</div>
					<div class="iGroup">
						<label>Repeat Password</label>
						<input type="password" name="password2">
					</div>
					<div class="iGroup imgGroup">
						<label>Photo</label>
						[photo]
							<figure>
								<img src="/uploads/images/users/{id}/thumb_{image}" onclick="showPhoto(this.src);">
								<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
							</figure>
						[/photo]
						<div class="dragndrop">
							<span class="fa fa-download"></span>
							Click or drag and drop file here
						</div>
					</div>
					<div class="sGroup">
						<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>