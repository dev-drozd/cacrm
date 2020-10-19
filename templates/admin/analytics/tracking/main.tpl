{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		{lang=InventoryTracking}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				[manager]
				[not-manager]
				<div class="iGroup fw" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				[/manager]
				<div class="iGroup fw" id="create">
					<label>Created Staff</label>
					<input type="hidden" name="create">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="type">
					<label>Type</label>
					<select name="type">
						<option value="0">All</option>
						<option value="stock">Stock</option>
						<option value="purchase">Purchases</option>
					</select>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'searchText\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="Search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">{lang=Stock}</div>
				<div class="th" style="width: 7%;">{lang=Type}</div>
				<div class="th" style="width: 10%;">{lang=Owner}</div>
				<div class="th" style="width: 12%;">{lang=Price}</div>				
				<div class="th" style="width: 40%;">{lang=Chronology}</div>
			</div>
		</div>
		<div class="tBody userList">
			{inventory}
		</div>
	</div>
	{include="doload.tpl"}
</div>
<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		$.post('/invoices/objects', {}, function (r) {
			if (r){
				if (r.list.length > 1) {
					var items = '', lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#object > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							query: $('#object > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'));
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
				} else {
					$('#object').hide();
				}
			}
		}, 'json');
		
		$.post('/users/all', {staff: 1}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				
				$('#create > ul').html(items).sForm({
					action: '/users/all',
					data: {
						staff: 1,
						lId: lId,
						nIds: Object.keys($('input[name="create"]').data() || {}).join(','),
						query: $('#create > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="create"]').data(),
					s: true
				}, $('input[name="create"]'));
			}
		}, 'json');
		
		$.post('/inventory/allTypes', {}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				
				$('#inv_type > ul').html(items).sForm({
					action: '/inventory/allTypes',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="inv_type"]').data() || {}).join(','),
						query: $('#inv_type > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="inv_type"]').data(),
					s: true
				}, $('input[name="inv_type"]'));
			}
		}, 'json');
	});
</script>