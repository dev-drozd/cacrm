<aside class="sideNvg">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> {lang=AddInventory}
	</div>
	<ul class="mng">
		<li class="active"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>{lang=AddingCustomer}</a></li>
		<li class="active arr"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>{lang=AddingInventory}</a></li>
		<li class="inactive"><a href="#" onclick="return false;"><span class="fa fa-times" style="color: #d0d0d4;"></span>{lang=AddingIssue}</a></li>
	</ul>
</aside>
<section class="mngContent tr">
	<div class="sTitle spBottom">
		<span class="fa fa-chevron-right"></span> {lang=Step} 1
	</div> 
	<div class="sTitle spBottom">
		<span class="fa fa-chevron-right"></span> {lang=Step} 2
	</div>
	<div class="bWhite">
		<form class="uForm" method="post" onsubmit="inventory.addInv(this, event, {id}, 1);">
			[user]
			<input type="hidden" name="customer" value="{cid}" />
			[/user]
			<div class="iGroup" id="type_id">
				<label>{lang=Group}</label>
				<input type="hidden" name="type_id" value="{type-get}">
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
				<label>{lang=ModelSpecification}</label>
				<input type="text" name="smodel" value="{smodel}"/>
			</div>
			<div class="iGroup">
				<label>{lang=Barcode}</label>
				<input type="text" name="barcode">
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
				<label>{lang=SaveFiles}</label>
				<input type="checkbox" name="save_data" [save-data]checked[/save-data] onchange="$(this).val() == 1 ? $('textarea[name=\'sd_comment\']').parent().show() : $('textarea[name=\'sd_comment\']').val('').parent().hide()"/>
			</div>
			<div class="iGroup [save-data][not-save-data]hdn[/save-data]" i>
				<label>{lang=SaveFilesComment}</label>
				<textarea name="sd_comment">{sd-comment}</textarea>
			</div>
			<div class="iGroup">
				<label>{lang=WithCharger}</label>
				<input type="checkbox" name="charger"/>
			</div>
			<div class="iGroup">
				<label>{lang=Currency}</label>
				<select name="currency">
					{currency}
				</select>
			</div>
			<span id="forms">{forms}</span>
			<div class="iGroup sfGroup" id="object">
				<label>{lang=Object}</label>
				<input type="hidden" name="object" />
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
						<option value="0" name="sublocation">{lang=NotSelected}</option>
					</select>
				</div>
			</div>
			<div class="sGroup">
				<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {lang=Next}</button>
			</div>
		</form>
	</div>
</section>
<script>
	//var type_id = {type-id};	
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
			[type-get]
				$('#type_id ul > li[data-value={type-get}]').click();
			[/type-get]
		}, 'json');
	}
	
	$(function() {
		status_id = {status-id};
			inventory.getGroup(0, 0, 1);
		
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
			
		//$('input[name="category"]').data({category-id});
		//$('input[name="object"]').data({object-id});
		[edit]
		$('input[name="location"]').data({location-id});
		//getLocation();
		[/edit]
		$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="category"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#category > ul').html(items).sForm({
					action: '/inventory/allCategories',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="category"]').data() || {}).join(','),
						query: $('#category > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="category"]').data(),
					s: true
				}, $('input[name="category"]'));
			}
			[brand]
				$('#category ul > li[data-value={brand}]').click();
			[/brand]
		}, 'json');
		
		$.post('/invoices/objects', {nIds: Object.keys($('input[name="object"]').data()).join(',')}, function (r) {
			if (r){
				if (r.list.length == 1) {
					oId = {}; 
					oId[r.list[0].id] = {
							name: r.list[0].name
						};
					$('#object').append($('<div/>', {
						class: 'storeOne',
						html: r.list[0].name
					}));
					$('input[name="object"]').data(oId);
					getLocation();
				} else {
					var items = '', lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#object > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							nIds: Object.keys($('input[name="object"]').data() || {}).join(','),
							query: $('#object > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'), getLocation);
				}	
			}
		}, 'json');
		
		//getLocation();
	});
	
	function getOptions() {
		$.post('/inventory', {
			type_id: Object.keys($('input[name="type_id"]').data()).join(',')[edit],
			inventory: {id}[/edit]
		}, function(r) {
			$('#forms').html(r);
			Page.init();
		}, 'json'); 
	}
	setTimeout(function(){
			[model]$('#model ul > li[data-value={model}]').click();[/model]
	}, 1000);
</script>