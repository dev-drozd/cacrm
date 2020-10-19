<aside class="sideNvg">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Add issue
	</div>
	<ul class="mng">
		<li class="active"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>Adding customer</a></li>
		<li class="active"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>Adding inventory</a></li>
		<li class="active arr"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>Adding issue</a></li>
	</ul>
</aside>
<section class="mngContent tr">
	<div class="sTitle spBottom">
		<span class="fa fa-chevron-right"></span> Step 1
	</div>
	<div class="sTitle spBottom">
		<span class="fa fa-chevron-right"></span> Step 2
	</div>
	<div class="sTitle spBottom">
		<span class="fa fa-chevron-right"></span> Step 3
	</div>
	<div class="bWhite">
		<form class="uForm" method="post" onsubmit="issues.add(this, event, 0, 1);">
			<div class="iGroup">
				<label>{lang=Description}</label>
				<textarea name="descr">{descr}</textarea>
			</div>
			<input type="hidden" name="inventory_id" value="{device-id}">
			<div class="iGroup sfGroup price" id="inventory">
				<label>{lang=addInventory}</label>
				<input type="hidden" name="inventory">
				<ul class="hdn"></ul>
			</div>
			<div class="iGroup sfGroup price" id="service">
				<label>{lang=addService}</label>
				<input type="hidden" name="service">
				<ul class="hdn"></ul>
			</div>
			 
			<div class="sGroup dClear">
				<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Done</button>
			</div>
		</form>
	</div>
</section>

<script>
	var type_id = 0;
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
		$.post('/inventory/allTypes', {
			name: 1
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
						query: $('#type_id > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="type_id"]').data(),
					s: true
				}, $('input[name="type_id"]'));
			}
		}, 'json');
	}
	
	$(function() {
		[edit]
		$('input[name="inventory"]').data({inventory-ids} || {});
		$('input[name="service"]').data({service-ids} || {});
		[/edit]
		$('input[name="status"]').data({status} || {});
		
		$.post('/inventory/all', {type: 'stock', noCust: 1, nIds: Object.keys($('input[name="inventory"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#inventory > ul').html(items).sForm({
					action: '/inventory/all',
					data: {
						type: 'stock',
						lId: lId,
						noCust: 1,
						nIds: Object.keys($('input[name="inventory"]').data() || {}).join(','),
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
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
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
	});
</script>