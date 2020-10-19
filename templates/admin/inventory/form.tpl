{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		[store][edit]<a href="https://yoursite.com/item/{uri}" target="_blank">view on the site</a>[/edit][/store]
		[edit][next]<a href="/inventory/edit/{next}" onclick="Page.get(this.href); return false;" class="btn btnNext">{lang=Next}</a>[/next]
		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">{lang=Options}</span>
			</span>
			<ul>
				<li><a href="/inventory/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewInventory}</a></li>
				<li><a href="/pdf/dbarcode/{id}" target="_blank" ><span class="fa fa-barcode"></span> {lang=Barcode}</a></li>
				<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delInventory}</a></li>
			</ul>
		</div>[/edit]
	</div>
	[edit][notconfirmed]
	<div class="mt dClear">
		{lang=InventoryAreNotConfirmed} <a href="#" class=" btnConfirmed" onclick="inventory.confirmed({id}, this); return false;" ondblclick="return false;">{lang=Confirm}</a>
	</div>
	[/notconfirmed][/edit]
	[backusr]
		<div class="breads">
			<a href="/users/view/{backusr}" onclick="Page.get(this.href); return false;"><span class="fa fa-arrow-left"></span> {lang=BackToUser}</a>
		</div>
	[/backusr]
	<form class="uForm" method="post" onsubmit="inventory.addInv(this, event[user][not-user], {id}[/user]);">
		[user]
			<input type="hidden" name="customer" value="{cid}" />
		[/user]
		<div class="iGroup" id="type_id">
			<label>{lang=Group}</label>
			<input type="hidden" name="type_id" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="category">
			<label>{lang=Brand}</label>
			<input type="hidden" name="category" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="model">
			<label>{lang=Model}</label>
			<input type="hidden" name="model" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup">
			<label>{lang=Barcode}</label>
			<input type="text" name="barcode" value="{barcode}">
		</div>
		[is_user]
		[not-is_user]
		<div class="iGroup curGroup">
			<label>{lang=PurchasePrice}</label>
			<div class="cur">
				<select name="purchase_currency" onchange="income();">
					{purchase-currency}
				</select>
			</div>
			<input type="number" name="purchase-price" value="{purchase-price}" step="0.001" min="0" onchange="income();" onkeyup="income();" />
		</div>
		<div class="iGroup curGroup">
			<label>{lang=SalePrice}</label>
			<div class="cur">
				<select name="currency" onchange="income();">
					{currency}
				</select>
			</div>
			<input type="number" name="price" value="{price}" step="0.001" min="0"  onchange="income();" onkeyup="income();" />
		</div>
		<div class="iGroup">
			<label>{lang=Income}</label>
			<div id="income"></div>
		</div>
		<div class="iGroup">
			<label>{lang=Quantity}</label>
			<input type="number" name="quantity" value="{quantity}" step="1" min="1" onchange="$(this).val() > 1 ? $('input[name=\'serial\']').val(0).parent().hide() : $('input[name=\'serial\']').parent().show();"/>
		</div>
		<!-- <div class="iGroup">
			<label>{lang=Vendors}</label>
			<select name="vendor">
				{options-vendors}
			</select>
		</div> -->
		[/is_user]
		[inventory]
		<div class="iGroup">
			<label>{lang=ModelSpecification}</label>
			<input type="text" name="smodel" value="{smodel}"/>
		</div>
		<div class="iGroup">
			<label>{lang=OS}</label>
			<select name="os">
				{options-os}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=OSVersion}</label>
			<input type="text" name="os_version" value="{os-version}" />
		</div>
		<div class="iGroup">
			<label>{lang=SerialNumber}</label>
			<input type="text" name="serial" value="{serial}"/>
		</div>
		<div class="iGroup">
			<label>{lang=WithCharger}</label>
			<input type="checkbox" name="charger" [charger]checked[/charger]/>
		</div>
		<div class="iGroup">
			<label>{lang=SaveFiles}</label>
			<input type="checkbox" name="save_data" [save-data]checked[/save-data] onchange="$(this).val() == 1 ? $('textarea[name=\'sd_comment\']').parent().show() : $('textarea[name=\'sd_comment\']').val('').parent().hide()"/>
		</div>
		<div class="iGroup [save-data][not-save-data]hdn[/save-data]" i>
			<label>{lang=SaveFilesComment}</label>
			<textarea name="sd_comment">{sd-comment}</textarea>
		</div>
		[/inventory]
		<span id="forms">{forms}</span>
		[inventory]


		<div class="sTitle">{lang=Location}</div>
		<div class="iGroup sfGroup" id="object">
			<label>{lang=Object}</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="status">
			<label>
				{lang=Status} (<a href="javascript:inventory.statusHis({id})">{lang=showHistory}</a>)
			</label>
			<input type="hidden" name="status" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup subloc" id="location">
			<label>
				{lang=Location}
			</label>
			<input type="hidden" name="location" />
			<ul class="hdn"></ul>
			<div id="sublocation">
				<select name="sublocation">
					<option value="0" name="sublocation">{lang=None}</option>
				</select>
			</div>
		</div>


		[customer-id]
		[not-customer-id]
			[backusr]
			[not-backusr]
			<div class="iGroup sfGroup" id="store_status">
				<label>{lang=storeStatus}</label>
				<input type="hidden" name="store_status" />
				<ul class="hdn"></ul>
			</div>
			[/backusr]
		[/customer-id]
		[not-inventory]
		<div class="iGroup sfGroup" id="object">
			<label>{lang=Object}</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		[/inventory]
		<div class="iGroup imgGroup">
			<label>{lang=Photos}</label>
			<div class="dragndrop">
				<span class="fa fa-download"></span>
				{lang=clickOrDrag}
			</div>
			<div class="thumbnails">{images}
			</div>
		</div>
		[store]
		<div class="sTitle">{lang=StoreInfo}</div>
		<div class="iGroup">
			<label>{lang=Description}</label>
			<textarea name="descr">{descr}</textarea>
		</div>
		<div class="iGroup sfGroup" id="storeCat">
			<label>{lang=Category}</label>
			<input type="hidden" name="storeCat" />
			<ul class="hdn"></ul>
		</div>		
		<div class="iGroup">
			<label>{lang=ToMain}</label>
			<input type="checkbox" name="main" [main]checked[/main]/>
		</div>
		<div class="sTitle">SEO</div>
		
		<div class="iGroup">
			<label>URL</label>
			<input type="text" name="pathname" value="{pathname}" maxlength="255" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		
		<div class="iGroup">
			<label>Title</label>
			<input type="text" name="title" value="{stitle}" maxlength="60">
		</div>
		
		<div class="iGroup">
			<label>Description</label>
			<textarea name="description" maxlength="255">{description}</textarea>
		</div>
		
		<div class="iGroup">
			<label>Keywords</label>
			<input type="text" name="keywords" value="{keywords}">
		</div>
		
		<div class="iGroup">
			<label>Canonical pathname</label>
			<input type="text" name="canonical" value="{canonical}" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		
		<div class="iGroup">
			<label>Publish on the site</label>
			<input type="checkbox" name="publish" [publish]checked[/publish]/>
		</div>
		
		[/store]
		<div class="sGroup">
			[edit][customer-id]
				<button class="btn btnPurchase" onclick="inventory.toObject({id}); return false;">{lang=MoveToStore}</button>
			[not-customer-id]
				[store][not-store]<button class="btn btnPurchase" onclick="inventory.toStore({id}); return false;">{lang=MoveToStore}</button>[/store]
			[/customer-id][/edit]
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	var cat = Object.keys($('input[name="category"]').data()).join(',');
	function delete_image(e){
		var drag = $('.dragndrop')[0], count = drag.count || 1;
		$(e.parentNode).remove();
		drag.delete.push($(e.parentNode).find('img').attr('src'));
		drag.count = count-1;
	}
	
	function quantity() {
		console.log($('input[name="quantity"]').val());
		if ($('input[name="quantity"]').val() > 1)
			$('input[name="serial"]').val('').parent().hide();
		else 
			$('input[name="serial"]').show();
	}
		
	function getBrands(v) {
		$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="category"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!v) {
					$('#model > input').removeData();
				}
				if (brand_id) {
					$('#brand_id > input').data(brand_id);
					brand_id = '';
				}
				$('#category > ul').html(items).sForm({
					action: '/inventory/allCategories',
					data: {
						lId: lId,
						query: $('#category > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="category"]').data(),
					s: true
				}, $('input[name="category"]'), inventory.getGroup);
			}
		}, 'json');
	}
	
	function getTypes(f) {
		var items = '', lId = 0;
		cat = Object.keys($('input[name="category"]').data()).join(',');
		$.post('/inventory/allTypes', {
			name: 1,
			brand: Object.keys($('input[name="category"]').data()).join(',')
		}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!f) {
					$('#model > input').removeData();
					$('#type_id > input').removeData();
					$('#type_id > input + div').remove();
					$('#type_id > input + ul').remove();
					$('#type_id > input').after($('<ul/>'));
				}
				if (type_id) {
					$('#type_id > input').data(type_id);
					type_id = '';
				}
				$('#type_id > ul').html(items).sForm({
					action: '/inventory/allTypes',
					data: {
						lId: lId,
						query: $('#type_id > .sfWrap input').val() || '',
						name: 1,
						brand: Object.keys($('input[name="category"]').data()).join(',') || 0
					},
					all: false,
					select: $('input[name="type_id"]').data(),
					s: true
				}, $('input[name="type_id"]'), function() {
					getOptions(Object.keys($('input[name="type_id"]').data()).join(','))
				});
			}
		}, 'json');
	}
	
	$(function() {
		[add]
			inventory.getGroup(0, 0, 1);
			$('input[name="status"]').data({11: {name: 'New'}});
			$('select[name="owner_type"]').val('internal').trigger('change');
		[/add]
		
		[edit]
			$('select[name="owner_type"]').val('{owner-type}').trigger('change');
			$('input[name="object_owner"]').data({object-owner});
			$('input[name="customer"]').data({customer-id});
			$('input[name="store_status"]').data({store-status-id});
			
			location_count = {location-count};
			count_id = {location-id};
			quantity();
		[/edit]
		$('input[name="type_id"]').data({type-id});
		type_id = {type-id};
		getTypes();
		
		if ({category-id} != []) {
			$('input[name="category"]').data({category-id});
			brand_id = {category-id};
			getBrands(1);
			if ({model-id} != []) {
				model_id = {model-id};
				$('input[name="model"]').data({model-id});
			}
			//inventory.getGroup(Object.keys({type-id}).join(','), {id}, 1);
		} else 
			getBrands(1);
		
		[store]
			$('input[name="storeCat"]').data({store-category-id});
			$.post('/store/allCategories', {gId: 5, nIds: Object.keys($('input[name="storeCat"]').data()).join(',')}, function (r) {
				if (r){
					var items = '', lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#storeCat > ul').html(items).sForm({
						action: '/store/allCategories',
						data: {
							gId: 5,
							lId: lId,
							query: $('#storeCat > .sfWrap input').val()
						},
						all: false,
						select: $('input[name="storeCat"]').data(),
						s: true
					}, $('input[name="storeCat"]'));
				}
			}, 'json');
		[/store]
		/* $('input[name="category"]').data({category-id});
		$('input[name="category"]').data({model-id}); -*/
		[user]
		$('input[name="customer"]').data({{cid} : 'Customer'});
		[not-user]
		$('input[name="customer"]').data({customer-id});
		[/user]
		$('input[name="object"]').data({object-id});
		[edit]
		$('input[name="status"]').data({status-id});
		status_id = {status-id};
		$('input[name="location"]').data({location-id});
		[inventory]
		
		[/inventory]
		[/edit]
		[inventory][not-inventory]
		$('input[name="object"]').data({object-id});
		[/inventory]
		
		[inventory]
		[user][not-user][edit][customer-id][not-customer-id]income();[/customer-id][/edit][/user]
		$.post('/invoices/objects', {}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (r.list.length > 1) {
					$('#object > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							query: $('#object > .sfWrap input').val() || ''
						},
						all: false,
						select: $('input[name="object"]').data(),
						s: true,
						link: 'objects/edit'
					}, $('input[name="object"]'), function() { getLocation(1); });
				} else if (r.list.length == 1) {
					var cash_object = {};
					cash_object[r.list[0].id] = {
						name: r.list[0].name
					}
					$('input[name="object"]').data(cash_object);
					$('#object').append($('<div/>', {
						class: 'storeOne',
						html: r.list[0].name
					}));
					getLocation(1);
				} else {
					$('#object').append($('<div/>', {
						class: 'storeOne',
						html: lang[172]
					}));
				}
			}
		}, 'json');
		
		$.post('/inventory/allStatuses', {nIds: Object.keys($('input[name="status"]').data()).join(',')}, function (r) {
			if (r){
				var items = '',
					lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				location_id = {location-id};
				$('#status > ul').html(items).sForm({
					action: '/inventory/allStatuses',
					data: {
						lId: lId,
						query: $('#status > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="status"]').data(),
					s: true
				}, $('input[name="status"]'), getLocation);
			}
		}, 'json');

		
		[customer-id]
		[not-customer-id]
			$.post('/inventory/allStoreStatuses', {nIds: Object.keys($('input[name="store_status"]').data()).join(',')}, function (r) {
				if (r){
					var items = '',
						lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '">' + v.name + '</li>';
						lId = v.id;
					});
					location_id = {location-id};
					$('#store_status > ul').html(items).sForm({
						action: '/inventory/allStoreStatuses',
						data: {
							lId: lId,
							query: $('#store_status > .sfWrap input').val() || ''
						},
						all: false,
						select: $('input[name="store_status"]').data(),
						s: true
					}, $('input[name="store_status"]'));
				}
			}, 'json');
		[/customer-id]
		
		
		[add][show]
			status_id = $('input[name="status"]').data();

			//getLocation(1);
			[/show]
		[/add]
		$('.dragndrop').upload({
			count: {count-images},
			multiple: true,
			max: 5,
			check: function(e){
				var self = this;
				if(!e.error){
					var img = new Image();
					img.src = URL.createObjectURL(e.file);
					img.setAttribute('onclick', 'showPhoto(this.src);');
					$('.thumbnails').append($('<div>', {
						class: 'thumb',
						html: img
					}).append($('<span/>', {
						class: 'fa fa-times'
					}).click(function(){
						delete self.files[e.file.name];
						$(this).parent().remove();
					})));
				} else if(e.error == 'max'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[169],
					   delay: 2
					});
				} else if(e.error == 'type'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[170],
					   delay: 2
					});
				} else if(e.error == 'size'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[171],
					   delay: 2
					});
				}
			}
		});
		[/inventory]
	});
	
	function changeOwner(el) {
		[user]
		[not-user]
		 if ($(el).val() == 'internal') {
			$('input[name="customer"]').removeData().parent().hide().find('.sfWrap > div > span').click();
			$('#object_owner').show();
		} else {
			$('input[name="object_owner"]').removeData().parent().hide().find('.sfWrap > div > span').click();;
			$('#customer').show();
		}
		[/user]		
	}
	
	function getOptions() {
		$.post('/inventory', {
			type_id: Object.keys($('input[name="type_id"]').data()).join(',')[edit],
			inventory: {id}[/edit]
		}, function(r) {
			$('#forms').html(r);
			Page.init();
		}, 'json'); 
	}
</script>