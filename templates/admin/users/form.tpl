{include="users/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		[edit]<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul>
				<li><a href="/users/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> View user</a></li>
				[owner][not-owner][delete]<li><a href="javascript:user.[deleted]restore[not-deleted]del[/deleted]({id});"><span class="fa fa-times"></span> [deleted]{lang=restoreUser}[not-deleted]{lang=delUser}[/deleted]</a></li>[/delete][/owner]
			</ul>
		</div>[/edit]
	</div>
	[deleted]<div class="mt dClear">
			User deleted
		</div>[/deleted]
	<form class="uForm" method="post" onsubmit="user.add(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Company}</label>
			<input type="checkbox" name="company" onchange="user.comSelect(this);" [company] checked[/company]>
		</div>
		
		[multi-group]
		<div class="iGroup">
			<label>{lang=Group}</label>
			<select name="group_id[]" multiple>{options}</select>
		</div>
		[not-multi-group]
		<input type="hidden" name="group_id[]" value="{gid}">
		[/multi-group]
		[name]
		<div class="iGroup com"[company] style="display: block;"[not-company] style="display: none"[/company]>
			<label>{lang=Name}</label>
			<input type="text" name="cname" value="{cname}"[view-name]disabled[/view-name]>
		</div>
		<div class="iGroup com plusNew" id="contact"[company] style="display: block;"[not-company] style="display: none"[/company]>
			<label>{lang=contactPerson}</label>
			<input type="hidden" name="contact">
			<ul class="hdn"></ul>
			<span class="fa fa-plus" onclick="user.newUsr()"></span>
		</div>
		<div class="iGroup usr"[company] style="display: none;"[not-company] style="display: block"[/company]>
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}"[view-name]disabled[/view-name] onblur="checkExists();">
		</div>
		<div class="iGroup usr"[company] style="display: none;"[not-company] style="display: block"[/company]>
			<label>{lang=Lastname}</label>
			<input type="text" name="lastname" value="{lastname}"[view-name]disabled[/view-name] onblur="checkExists();">
		</div>
		<div class="exists_users"></div>
		[/name]
		<div class="iGroup usr"[company] style="display: none;"[/company]>
			<label>{lang=Sex}</label>
			<div class="iRight">
				<input type="radio" name="sex" value="Male" data-label="Male">
				<input type="radio" name="sex" value="Female" data-label="Female">
			</div>
		</div>
		<div class="iGroup usr"[company] style="display: none;"[/company]>
			<label>{lang=BirthDate}</label>
			<div class="iRight">
				<input type="text" name="bithDate" class="cl" onclick="$(this).next().show();" value="{month}-{day}-{year}">
				<div id="calendar" class="hdn cl" data-month="{month}" data-year="{year}" data-day="{day}"></div>
				<!-- <select name="month">
					<option value="1">January</option>
					<option value="2">February</option>
					<option value="3">March</option>
					<option value="4">April</option>
					<option value="5">May</option>
					<option value="6">June</option>
					<option value="7">July</option>
					<option value="8">August</option>
					<option value="9">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select>
				<input type="number" name="day" class="bDay" value="{day}">
				<input type="number" name="year" class="bYear" value="{year}"> -->
			</div>
		</div>
		[address]
		<div class="iGroup">
			<label>{lang=Zipcode}</label>
			<input type="text" name="zipcode" oninput="checkState(this.value);" onkeyup="checkState(this.value);" value="{zip-input}" [view-address]disabled[/view-address]>
		</div>
		<div class="iGroup" id="country">
			<label>{lang=Country}</label>
			<!--<input type="text" name="country" value="{country}" [view-address]disabled[/view-address]>-->
			<input type="hidden" name="country">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup" id="state">
			<label>{lang=State}</label>
			<!--<input type="text" name="state" value="{state}" [view-address]disabled[/view-address]>-->
			<input type="hidden" name="state">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup" id="city">
			<label>{lang=City}</label>
			<!--<input type="text" name="city" value="{city}" [view-address]disabled[/view-address]>-->
			<input type="hidden" name="city">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup">
			<label>{lang=Address}</label>
			<textarea name="address" [view-address]disabled[/view-address]>{address}</textarea>
		</div>
		
		<div class="iGroup">
			<label>{lang=AddressConfirmation}</label>
			<select name="addressConf" [view-address]disabled[/view-address]>
				<option value="notConfirmed" selected>Not confirmed</option>
				<option value="passport">Passport</option>
				<option value="idCard">ID card</option>
			</select>
		</div>
		[/address]
		[email]
		<div class="iGroup">
			<label>{lang=Login}</label>
			<input type="text" name="login" value="{login}"[view-email]disabled[/view-email]>
		</div>
		<div class="iGroup">
			<label>{lang=Email}</label>
			<input type="email" name="email" value="{email}"[view-email]disabled[/view-email]>
		</div>
		[/email]
		[phone]
		<div class="iGroup">
			<label>{lang=Phone}</label>
			<div class="phoneZone">
				<div class="hPhone">
					<div>Country code</div>
					<div>Area code</div>
					<div>7-dt number</div>
					<div>Extension</div>
					<div>SMS</div>
				</div>
				[edit]
                {phone}
				[not-edit]
					<div class="sPhone">
						<span class="fa fa-times rd hide" onclick=""></span>
						<select name="phoneCode">
							<option value="+1" selected>+1</option>
							<option value="+3">+3</option>
						</select>
						<span class="wr">(</span>
						<input type="number" name="code" onkeyup="phones.next(this, 3);" value="" max="999">
						<span class="wr">)</span>
						<input type="number" name="part1" onkeyup="phones.next(this, 7);" value="">
						<input type="number" name="part2" value="'.$n[3].'">
						<input type="checkbox" name="sms" checked onchange="phones.onePhone(this);">
					</div>
				[/edit]
                [view-phone][not-view-phone]<span class="fa fa-plus plusNewPhone nPhone" onclick="phones.newPhone();"></span>[/view-phone]
            </div>
		</div>
		[/phone]
		<div class="iGroup">
			<label>Referrals</label>
			<select name="referral">
				<option value="0">Not selected</option>
				{referrals}
			</select>
		</div>
		[pay]
		<div class="iGroup">
			<label>Pay</label>
			<input name="pay" type="number" step="0.1" min="0" value="{pay}" [pay-edit][not-pay-edit]disabled[/pay-edit]>
		</div>
		[/pay]
		<div class="iGroup">
			<label>Google authorization</label>
			<input type="checkbox" name="google_auth" onchange="googleAuth(this)"{google-auth}>
		</div>
		[password]
		<div class="iGroup">
			<label>{lang=Password}</label>
			<input type="password" name="password">
		</div>
		<div class="iGroup">
			<label>{lang=Password2}</label>
			<input type="password" name="password2">
		</div>
		[/password]
		[photo]
		<div class="iGroup imgGroup wc">
			<label>Photo</label>
			[ava]
				<figure>
					<img src="/uploads/images/users/{id}/thumb_{ava}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/ava]
			<div class="dragndrop">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
			<div class="dClear"></div>
			<div class="webPhoto">
				<div><span>Or make</span></div>
				<button class="btn btnWeb" type="button" onclick="user.webPhoto();">webcam capture</button>
			</div>
		</div>
		[/photo]
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script defer>
function googleAuth(a){
	if(a.checked && '{google-auth}' == '') window.location.replace('{google-lnk}');
}
$(function() {
	window.pendingZip = false;
	window.checkState = function(a){
		clearTimeout(window.pendingZip);
		window.pendingZip = setTimeout(function() {
			$('input[name="country"]').removeData().next().remove();
			$('input[name="country"]').after($('<ul/>'));
			$('input[name="state"]').removeData().next().remove();
			$('input[name="state"]').after($('<ul/>'));
			$('input[name="city"]').removeData().next().remove();
			$('input[name="city"]').after($('<ul/>'));
			if (a.length) {
				$.getJSON('/geo/zipcode/'+a, function(j){
					if(!$.isEmptyObject(j)){
						getCoutries(j.country);
						getStates(j.country, j.state, 1);
						getCities(j.state, a, 1, 1);
					} else {
						getCoutries();
					}
				});
			} else {
				getCoutries();
			}
		}, 200);
	};
	$('#calendar').calendar(function() {
		var birth_date = $('#calendar > input[name="date"]').val().split('-');
		$('input[name="bithDate"]').val(birth_date[1] + '-' + birth_date[2] + '-' + birth_date[0]);
		$('.iRight > input + div').hide();
	});
	$('body').on('click', function(event) {
		if (!$(event.target).hasClass('cl'))
			$('.iRight > input + div').hide();
	});
	/* company = location.pathname.split('/');
	if ({company} || company[3] == 'company')
		$('input[name="company"]').attr('checked', 'checked');
	user.comSelect({company}); */
	$('select[name="addressConf"]').val('{ver}');

	$('input[value="{sex}"]').trigger('click');
	
	$('input[name="contact"]').data({contact} || {});
	$.post('/users/all', {gId: 5, nCom: 1, nIds: Object.keys($('input[name="contact"]').data()).join(',')}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '">' + v.name + '</li>';
				lId = v.id;
			});
			$('#contact > ul').html(items).sForm({
				action: '/users/all',
				data: {
					gId: 5,
					nCom: 1,
					lId: lId,
					nIds: Object.keys($('input[name="contact"]').data()).join(','),
					query: $('#contact > .sfWrap input').val()
				},
				all: (r.count <= 20) ? true : false,
				select: $('input[name="contact"]').data(),
				s: true
			}, $('input[name="contact"]'));
		}
	}, 'json');
		
		
	$('.dragndrop').upload({
		check: function(e){
			var self = this;
			if(!e.error){
				var img = new Image();
				img.src = URL.createObjectURL(e.file);
				var thumb = $('.dragndrop').prev();
				if(thumb[0].tagName == 'FIGURE') thumb.remove();
				$(this).before($('<figure/>', {
					html: $('<img/>', {
						src: URL.createObjectURL(e.file)
					})
				}).append($('<span/>', {
					class: 'fa fa-times'
				}).click(function(){
					self.files = {};
					$(this).parent().remove();
				})));
			} else if(e.error == 'type'){
				alr.show({
				   class: 'alrDanger',
				   content: 'You can load only jpeg, jpg, png, gif images',
				   delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
				   class: 'alrDanger',
				   content: 'Upload size for images is more than allowable',
				   delay: 2
				});
			}
		}
	});
	var ids = '{group-ids}'.split(',');
	$.each(ids, function(i, e){
		$('select[name="group_id[]"] option[value="'+e+'"]').prop('selected', true).trigger('change');
	});
	
	
	getCoutries('{country}');	
	if ('{state}') {
		getStates('{country}', '{state}');
		if ({zipcode})
			getCities('{state}', {zipcode}, 1);
	}
	
});
	
function getCoutries(code) {
	$.get('/geo/countries', function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r, function(i, v) {
				if(code && i == code){
					var f = {};
					f[i] = {name: v};
					$('input[name="country"]').data(f);
				}
				items += '<li data-value="' + i + '">' + v + '</li>';
			});
			$('#country > ul').html(items).sForm({
				action: '/geo/countries',
				data: {
					nIds: Object.keys($('input[name="country"]').data() || {}).join(','),
					query: $('#country > .sfWrap input').val()
				},
				all: true,
				select: $('input[name="country"]').data(),
				s: true
			}, $('input[name="country"]'), code ? false : getStates);
		}
	}, 'json');
}

function getCities(c, zcode, f, v) {
	if ($('input[name="state"]').data()) {
		$.get('/geo/cities/' + (c || Object.keys($('input[name="state"]').data()).join(',')) + (v ? '/' + zcode : ''), function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r, function(i, v) {
					if(zcode && i == zcode){
						var f = {};
						f[i] = {name: v};
						$('input[name="city"]').data(f);
					}
					items += '<li data-value="' + i + '">' + v + '</li>';
				});
				if (!f && '{state}' != Object.keys($('input[name="state"]').data()).join(',')) {
					$('#city > input').removeData().next().remove();
					$('#city').append($('<ul/>'));
				}
				$('#city > ul').html(items).sForm({
					action: '/geo/cities/' + Object.keys($('input[name="state"]').data()).join(',') + (v ? '/' + zcode : ''),
					data: {
						nIds: Object.keys($('input[name="city"]').data()).join(','),
						query: $('#city > .sfWrap input').val()
					},
					all: true,
					select: $('input[name="city"]').data(),
					s: true
				}, $('input[name="city"]'), function() {
					$('input[name="zipcode"]').val(Object.keys($('input[name="city"]').data()).join(','));
				});
			}
		}, 'json');
	}
}

function getStates(c, code, f) {
	if ($('input[name="country"]').data()) {
		$.get('/geo/states/' + (c || Object.keys($('input[name="country"]').data()).join(',')), function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r, function(i, v) {
					if(code && i == code){
						var f = {};
						f[i] = {name: v};
						$('input[name="state"]').data(f);
					}
					items += '<li data-value="' + i + '">' + v + '</li>';
				});
				if (!code && '{country}' != Object.keys($('input[name="country"]').data()).join(',')) {
					$('#state > input').removeData().next().remove();
					$('#state').append($('<ul/>'));
					$('#city > input').removeData().next().remove();
					$('#city').append($('<ul/>'));
				}
				$('#state > ul').html(items).sForm({
					action: '/geo/states/' + Object.keys($('input[name="country"]').data()).join(','),
					data: {
						nIds: Object.keys($('input[name="state"]').data()).join(','),
						query: $('#state > .sfWrap input').val()
					},
					all: true,
					select: $('input[name="state"]').data(),
					s: true
				}, $('input[name="state"]'), (f && code) ? null : getCities);
			}
		}, 'json');
	}
}
</script>

