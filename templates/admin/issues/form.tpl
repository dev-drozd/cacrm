{include="issues/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<form class="uForm" method="post" onsubmit="issues.add(this, event[edit], {id}[not-edit], 0, {id}[/edit]);">
		<div class="iGroup">
			<label>{lang=Description}</label>
			<textarea name="descr">{descr}</textarea>
		</div>
		<input type="hidden" name="inventory_id" value="{device-id}">
		<input type="hidden" name="object">
		<div class="iGroup sfGroup price bobject" id="inventory">
			<label>{lang=addInventory}</label>
			<input type="hidden" name="inventory">
			<ul class="hdn"></ul>
			<!--<span class="fa fa-plus" onclick="issues.newInv()"></span>  plusNew -->
		</div>
		<div class="iGroup sfGroup price" id="service">
			<label>{lang=addService}</label>
			<input type="hidden" name="service">
			<ul class="hdn"></ul>
			<!--<span class="fa fa-plus" onclick="issues.newServ()"></span>  plusNew -->
		</div>
		[confirmed]
		<div class="iGroup sfGroup" id="status">
			<label>{lang=Status}</label>
			<input type="hidden" name="status">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup subloc" id="location">
			<label>{lang=Location}</label>
			<input type="hidden" name="location">
			<ul class="hdn"></ul>
			<div id="sublocation">
				<select name="sublocation">
					<option value="0" name="sublocation">None</option>
				</select>
			</div>
		</div>
		[/confirmed] 
		<div class="sGroup dClear">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>

<script>
	function getBrands(v) {
		$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data()).join(',')}, function (r) {
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
				$('#brand > ul').html(items).sForm({
					action: '/inventory/allCategories',
					data: {
						lId: lId,
						query: $('#brand > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="brand"]').data(),
					s: true
				}, $('input[name="brand"]'), inventory.getGroup);
			}
		}, 'json');
	}
	
	function getTypes(f) {
		var items = '', lId = 0;
		cat = Object.keys($('input[name="brand"]').data()).join(',');
		$.post('/inventory/allTypes', {
			name: 1,
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
						brand: Object.keys($('input[name="brand"]').data()).join(',') || 0
					},
					all: false,
					select: $('input[name="type_id"]').data(),
					s: true
				}, $('input[name="type_id"]'));
			}
		}, 'json');
	}
	
	$(function() {
		object_id = {object-id} || {};
		$('input[name="object"]').data({object-id} || {});
		[edit]
		location_id = {location} || {};
		$('input[name="inventory"]').data({inventory-ids} || {});
		$('input[name="service"]').data({service-ids} || {});
		[confirmed]
		$('input[name="location"]').data({location} || {});
		[/confirmed]
		[/edit]
		[confirmed]
		$('input[name="status"]').data({status} || {});
		
		$.post('/inventory/allStatuses', {nIds: Object.keys($('input[name="status"]').data()).join(',')}, function (r) {
			if (r){
				var items = '',
				lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#status > ul').html(items).sForm({
					action: '/inventory/allStatuses',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
						query: $('#status > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="status"]').data(),
					s: true
				}, $('input[name="status"]'), getLocation);
			}
		}, 'json');
		[/confirmed]
		$.post('/inventory/all', {type: 'stock', noCust: 1, nIds: Object.keys($('input[name="inventory"]').data()).join(',')[edit] + ({id} ? ',' + {id} : '')[not-edit] + ({device-id} ? ',' + {device-id} : '')[/edit]}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + v.currency + '" data-object="' + v.object + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#inventory > ul').html(items).sForm({
					action: '/inventory/all',
					data: {
						type: 'stock',
						lId: lId,
						noCust: 1,
						nIds: Object.keys($('input[name="inventory"]').data() || {}).join(',')[edit] + ({id} ? ',' + {id} : '')[not-edit] + ({device-id} ? ',' + {device-id} : '')[/edit],
						query: $('#inventory > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="inventory"]').data(),
					s: false
				}, $('input[name="inventory"]'));
			}
		}, 'json');
		
		$.post('/inventory/all', {type: 'service', nIds: Object.keys($('input[name="service"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + v.currency + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#service > ul').html(items).sForm({
					action: '/inventory/all',
					data: {
						type: 'service',
						lId: lId,
						nIds: Object.keys($('input[name="service"]').data() || {}).join(','),
						query: $('#service > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="service"]').data(),
					s: false
				}, $('input[name="service"]'));
			}
		}, 'json');
		
		/*$.post('/purchases/all', {
			nIds: Object.keys($('input[name="purchases"]').data()).join(',')
		}, function(r) {
			if (r) {
				var items = '',
					lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="' + v.price + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#purchases > ul').html(items).sForm({
					action: '/purchases/all',
					data: {
						nIds: Object.keys($('input[name="purchases"]').data() || {}).join(','),
						query: $('#purchases > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="purchases"]').data()
				}, $('input[name="purchases"]'));
			}
		}, 'json'); */
	});
</script>

