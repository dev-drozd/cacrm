{include="invoices/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=allInvoices}
		<div class="filters cl">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr cl">
				<div class="fTitle cl">Filters</div>
				<div class="iGroup fw dGroup cl">
					<label>Date <span class="fa fa-eraser cl" onclick="clearDate();"></span></label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_activity">
					<div id="calendar" data-multiple="1"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="staff">
					<label>Customer</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Status</label>
					<select name="status">
						<option value="0">Not selected</option>
						<option value="transactions">Transactions</option>
						<option value="paid">Paid</option>
						<option value="unpaid">Unpaid</option>
						<option value="partial">Partial</option>
					</select>
				</div>
				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="0">Not selected</option>
						<option value="cash">Cash</option>
						<option value="credit">Credit</option>
						<option value="check">Check</option>
					</select>
				</div>
				<div class="iGroup fw">
					<label>Onsite service</label>
					<select name="action">
						<option value="0">All</option>
						<option value="yes">Has onsite service</option>
						<option value="no">No onsite service</option>
					</select>
				</div>
				{profit}
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="Search2($('input[name=\'msText\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<div class="mngSearch">
		<input type="text" value="{query}" placeholder="{lang=invoiceSearch}" name="msText" onkeypress="if(event.keyCode == 13) Search2(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="usLiHead">
		<div class="sUser head">
			<div class="invInfo wp10">
				{lang=id}
			</div>
			<div class="invInfo wpDate">
				{lang=date}
			</div>
			<div class="invInfo wp15">
				{lang=customer}
			</div>
			<div class="invInfo wp15">
				{lang=phone}
			</div>
			<div class="invInfo wp10">
				{lang=type}
			</div>
			<div class="invInfo wp10">
				{lang=amount}
			</div>
			<div class="invInfo wp10">
				{lang=paid}
			</div>
			<div class="invInfo wp10">
				{lang=due}
			</div>
			<div class="uMore">
				{lang=options}
			</div>
		</div>
	</div>
	<div class="totalTime totalMoney">
		Total: $<span>{total}</span>
	</div>
	<div class="userList">
		{invoices}
	</div>
	{include="doload.tpl"}
</section>

<script>
[add][not-add]$('a[href="/invoices/add"]').parent().remove();[/add]
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		if (location.search) {
			var search = location.search,
				sDate = ($_GET('date_start') || ''),
				eDate = ($_GET('date_finish') || ''),
				type = $_GET('type'),
				status = $_GET('status'),
				action = $_GET('action'),
				oName = ($_GET('object_name') || $_GET('name') || '').replace(/%20/ig, ' '),
				o = {};
			
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="date_activity"]').val(sDate + ' / ' + eDate);
			
			if (type)
				$('select[name="type"]').val(type).trigger('change');
			if (status)
				$('select[name="status"]').val(status).trigger('change');
			if (action)
				$('select[name="action"]').val(action).trigger('change');
			if ($_GET('object') != 0) {
				o[$_GET('object')] = {name: oName};
				$('input[name="object"]').data(o);
			}
			//Search2($('input[name="searchText"]').val());
		}
		
		$.post('/users/all', {gId: 5}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				
				$('#staff > ul').html(items).sForm({
					action: '/users/all',
					gId: 5,
					data: {
						lId: lId,
						query: $('#staff > .sfWrap input').val() || ''
					},
					select: $('input[name="staff"]').data(),
					s: true
				}, $('input[name="staff"]'));
			}
		}, 'json');
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
							query: $('#object> .sfWrap input').val() || ''
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

	});
	
	function clearDate() {
		$('.calendar > input[name="date"]').val('');
		$('.calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
	}
</script>