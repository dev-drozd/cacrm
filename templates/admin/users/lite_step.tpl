{include="users/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> New customer
	</div>
	<form class="uForm" action="/users/send_lete_step" method="post" id="lite_step">
		<div class="iGroup">
			<label>{lang=Company}</label>
			<input type="checkbox" name="company" onchange="isCompany(this);">
		</div>
		<div class="iGroup com" style="display: none">
			<label>Name</label>
			<input type="text" name="cname">
		</div>
		<div class="iGroup usr">
			<label>{lang=Name}</label>
			<input type="text" name="name" required>
		</div>
		<div class="iGroup usr">
			<label>Last Name</label>
			<input type="text" name="lastname" required>
		</div>
		<div class="iGroup">
			<label>Email</label>
			<input type="email" name="email" required>
		</div>
		<div class="iGroup plusNew flex">
			<label>{lang=Phone}</label>
			<div class="phones">
				<div class="phone" data-code="+1">
					<input type="tel" name="phone[]" placeholder="(XXX) XXX-XXXX" class="sfWrap" value="" oninput="Phones.format(event);" onkeyup="Phones.format(event);" onblur="Phones.blur(this);" required>
					<input type="radio" name="sms" value="0" checked>
				</div>
				<span class="fa fa-plus" onclick="Phones.add(this.parentNode);"></span>
			</div>
		</div>
		<div class="iGroup">
			<label>Zipcode</label>
			<input type="text" name="zipcode" oninput="zipCode(this.value);" required>
			<input type="hidden" name="country">
			<input type="hidden" name="state">
		</div>
		<div class="iGroup">
			<label>{lang=Address}</label>
			<textarea name="address" required></textarea>
		</div>
		<div class="iGroup">
			<label>Referrals</label>
			<div name="referral" json='' class="sfWrap"></div>
		</div>
		<div class="iGroup com plusNew" style="display: none">
			<label>{lang=contactPerson}</label>
			<div id="cname" name="contact" json='/users/all?gId=5' res="list" search="ajax10" class="sfWrap"></div>
			<span class="fa fa-plus" onclick="user.newUsr(1)" style="line-height: 38px;"></span>
		</div>
		<a href="javascript:$('#caddf').slideToggle('slow');" class="addf">Addition fields</a>
		
		<!-- ADDITION CUSTOMER FIELDS -->
		<div id="caddf">
			<div class="iGroup usr">
				<label>{lang=Sex}</label>
				<div class="iRight">
					<input type="radio" name="sex" value="Male" data-label="Male">
					<input type="radio" name="sex" value="Female" data-label="Female">
				</div>
			</div>
			<div class="iGroup usr">
				<label>{lang=BirthDate}</label>
				<div class="iRight">
					<input type="date" name="bith_date" class="cl">
				</div>
			</div>
			<div class="iGroup">
				<label>{lang=AddressConfirmation}</label>
				<div name="addressConf" json='[{"id":"notConfirmed","name":"Not confirmed"},{"id":"passport","name":"Passport"},{"id":"idCard","name":"ID card"}]' class="sfWrap"></div>
			</div>
			<div class="iGroup">
				<label>{lang=Login}</label>
				<input type="text" name="login">
			</div>
			<div class="iGroup imgGroup">
				<label>Photo</label>
				<div class="dragndrop">
					<span class="fa fa-download"></span>
					Click or drag and drop file here
				</div>
			</div>
		</div>
		<!--/ ADDITION CUSTOMER FIELDS -->
		
		<!-- NEW INVENTORY -->
		<div class="sTitle">
			<span class="fa fa-chevron-right"></span> New inventory
		</div>
		<div class="iGroup plusNew" id="type_id">
			<label>Type</label>
			<div name="type_id" json='/inventory/allTypes' res="list" search="ajax10" class="sfWrap" onchange="reqInventory"></div>
			<span class="fa fa-plus" onclick="inventory.openType('div[name=type_id]')" style="line-height: 38px;"></span>
		</div>
		<div class="iGroup sfGroup plusNew" id="brand">
			<label>Brand</label>
			<div name="brand" json='/inventory/allCategories' res="list" search="ajax0" class="sfWrap" onchange="getModels" input></div>
			<span class="fa fa-plus" onclick="inventory.addCategory('div[name=brand]')" style="line-height: 38px;"></span>
		</div>
		<div class="iGroup sfGroup plusNew" id="model">
			<label>Model</label>
			<div name="model" json='' res="list" search="ajax0" class="sfWrap" disabled input></div>
			<span class="fa fa-plus" onclick="inventory.addModel('div[name=model]')" style="line-height: 38px;"></span>
		</div>
		<div class="iGroup">
			<label>Model Specification</label>
			<input type="text" name="smodel">
		</div>
		<div class="iGroup">
			<label>Serial Number</label>
			<input type="text" name="serial">
		</div>
		<div class="iGroup">
			<label>Save Files</label>
			<input type="checkbox" name="save_data" onchange="$(this).val() == 1 ? $('textarea[name=\'sd_comment\']').parent().show() : $('textarea[name=\'sd_comment\']').val('').parent().hide()"/>
		</div>
		<div class="iGroup">
			<label>With Charger</label>
			<input type="checkbox" name="charger"/>
		</div>
		<div class="iGroup sfGroup">
			<label>Store</label>
			<div name="store" json='/invoices/objects' res="list" search="ajax10" class="sfWrap" onchange="getLocations"></div>
		</div>
		<div class="iGroup sfGroup subloc">
			<label>Location</label>
			<div style="float: left;">
				<div name="location" json='' res="list" search="ajax10" class="sfWrap" disabled style="margin-right: 10px;" onchange="getSubLocations"></div>
				<div name="sublocation" json='' res="list" search="ajax10" class="sfWrap" disabled></div>
			</div>
		</div>
		<a href="javascript:$('#iaddf').slideToggle('slow');" class="addf">Addition fields</a>
		
		<!-- ADDITION CUSTOMER FIELDS -->
		<div id="iaddf">
			<div class="iGroup">
				<label>Barcode</label>
				<input type="text" name="barcode">
			</div>
			<div class="iGroup">
				<label>OS</label>
				<div name="os" json='' res="list" class="sfWrap"></div>
			</div>
			<div class="iGroup">
				<label>OS Version</label>
				<input type="text" name="os_version">
			</div>
			<div class="iGroup hdn">
				<label>Comment for saving files</label>
				<textarea name="sd_comment"></textarea>
			</div>
			<span id="inventory_types"></span>
<!-- 			<div class="iGroup">
				<label>Currency</label>
				<div name="currency" json='' res="list" class="sfWrap"></div>
			</div> -->
		</div>
		<!--/ ADDITION CUSTOMER FIELDS -->
		
		<!--/ NEW INVENTORY -->
		
		<!-- ADDING ISSUE -->
		<div class="sTitle">
			<span class="fa fa-chevron-right"></span> Adding issue
		</div>
		<div class="iGroup sfGroup price">
			<label>Add Inventory</label>
			<div name="inventory" json='/inventory/all?type=stock&noCust=1' res="list" search="ajax10" class="sfWrap" multiple></div>
		</div>
		<div class="iGroup sfGroup price">
			<label>Add Service</label>
			<div name="service" json='/inventory/all?type=service' res="list" search="ajax10" class="sfWrap" multiple></div>
		</div>
		<div class="iGroup">
			<label>Description</label>
			<textarea name="descr"></textarea>
		</div>
		<!--/ ADDING ISSUE -->
		
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit">	
				<span class="fa fa-save"></span> Complete
			</button>
		</div>
	</form>
</section>
<script>
$('div[json]').json_list();
$('[name=referral]')[0].load({referrals});
//$('[name=currency]')[0].load({currency});
$('[name=os]')[0].load({os-list});
$('div[name=brand]').find('input').blur(function(){
	if(this.value.length)
		$('[name=model]')[0].enabled();
	else
		$('[name=model]')[0].disabled();
});
function getModels(a){
	$('[name=model]')[0].load('/inventory/allModels?type='+a);
}
function getLocations(a){
	$('[name=location]')[0].load('/objects/get_locations?oId='+a);
}
function getSubLocations(a){
	var sublocation = [];
	for(var i = 1; i <= Number($(this).data().count); i++){
		sublocation.push({id: i, name: i});
	}
	if(sublocation.length > 0)
		$('[name=sublocation]')[0].load(sublocation);
	else
		$('[name=sublocation]')[0].disabled();
}
function reqInventory(a){
	if(a > 0){
		$('div[name=type_id],div[name=brand],div[name=model]').attr('required', true);
		$.post('/inventory', {
			step_type_id: a
		}, function(r){
			if(r)
				$('#inventory_types').html(r);
			else
				$('#inventory_types').empty();
		});
	} else {
		$('div[name=type_id],div[name=brand],div[name=model]').removeAttr('required');
		$('#inventory_types').empty();
	}
}
function isCompany(a){
	var usr = $('input[name=name],input[name=lastname]'),
		com = $('input[name=cname],div[name=contact]');
	if ($(a).val() != 0) {
		$('.com').show();
		$('.usr').hide();
		usr.removeAttr('required');
		com.attr('required', true);
	} else {
		$('.com').hide();
		$('.usr').show();
		usr.attr('required', true);
		com.removeAttr('required');
	}
}
function zipCode(a){
	return a.length ? $.getJSON('/geo/zipcode/'+a, function(a){
		$('input[name=country]').val(a.country || '');
		$('input[name=state]').val(a.state || '');
	}) : false;
}
$('#lite_step').ajaxSubmit({
	callback: function(r){
		if (r == 'err_email') {
			alr.show({
				class: 'alrDanger',
				content: lang[165],
				delay: 2
			});
		} else if (r == 'err_login') {
			alr.show({
				class: 'alrDanger',
				content: 'Login can not be empty',
				delay: 2
			});
		} else {
			r = JSON.parse(r);
			if(r.href)
				Page.get(r.href);
		}
	},
	check: function(){
		var t = $(this);
		//if(!t.find('div[name=inventory]').data().value.length && !t.find('div[name=service]').data().value.length){
		//	alr.show({
		//		class: 'alrDanger',
		//		content: 'You must select an inventory or service',
		//		delay: 2
		//	});
		//	return false;
		//}
	}
});
</script>