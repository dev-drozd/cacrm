{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=All} {title}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw">
					<label>{lang=Status}</label>
					<select name="status">
						<option value="0">{lang=All}</option>
						<option value="confirmed">{lang=Confirmed}</option>
						<option value="notconfirmed">{lang=Unconfirmed}</option>
					</select>
				</div>
				<div class="iGroup fw" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="create">
					<label>{lang=CreateStaff}</label>
					<input type="hidden" name="create">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="confirm">
					<label>{lang=ConfirmStaff}</label>
					<input type="hidden" name="confirm">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>{lang=HasIssue}</label>
					<select name="type">
						<option value="0">All</option>
						<option value="1">Has issue</option>
						<option value="2">No issue</option>
					</select>
				</div>
				<div class="iGroup">
					<label>Craiglist</label>
					<input type="checkbox" name="craiglist">
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search2($('input[name=\'search\']').val(), '{type}');">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="mngSearch">
		<input type="text" name="search" value="{query}" placeholder="Inventory search" onkeypress="if(event.keyCode == 13) Search2(this.value, '{type}');" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val(), '{type}');">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	[tradein]
	[not-tradein]
	<div class="usLiHead">
		<div class="sUser head">
			<div class="invInfo wp30">
				{lang=Name}
			</div>
			<div class="invInfo wp10">
				[stock]{lang=Qty}[/stock]
			</div>
			<div class="invInfo wp15">
				{lang=Store}
			</div>
			<div class="invInfo" style="width: 30px">
				CL
			</div>
			<div class="invInfo wp10">
				{lang=Price}
			</div>
			<div class="invInfo wp10">
				[stock]{lang=PurchasePrice}[/stock]
			</div>
			<div class="invInfo wp15">
				{lang=Created}
				[stock]/{lang=Confirmed}[/stock]
			</div>
			[add-service]
				<div class="invInfo" style="width: 30px">
					
				</div>
			[/add-service]
		</div>
	</div>
	[/tradein]
	<div class="userList">
		{inventory}
	</div>
	{include="doload.tpl"}
</section>
<script>
$(function() {
	[store]
		$('input[name="object"]').data({store});
	[/store]
	$.post('/invoices/objects', {all: 1}, function (r) {
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
						query: $('#object> .sfWrap input').val() || '',
						all: 1
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
				staff: 1,
				data: {
					lId: lId,
					query: $('#create > .sfWrap input').val() || ''
				},
				select: $('input[name="create"]').data(),
				s: true
			}, $('input[name="create"]'));
			
			$('#confirm > ul').html(items).sForm({
				action: '/users/all',
				staff: 1,
				data: {
					lId: lId,
					query: $('#confirm > .sfWrap input').val() || ''
				},
				select: $('input[name="confirm"]').data(),
				s: true
			}, $('input[name="confirm"]'));
		}
	}, 'json');
})
</script>