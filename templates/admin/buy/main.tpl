<style>.userList{width: auto;display: table-row-group;}</style>
{include="purchases/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=allPurchases}
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw dGroup">
					<label>Date</label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_stat" class="cl">
					<div id="calendar" data-multiple="1"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Status</label>
					<select name="status">
						<option value="0">Not selected</option>
						<option value="panding">Panding</option>
						<option value="confirmed">Confirmed</option>
						<option value="received">Received</option>
					</select>
				</div>
				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="0">All</option>
						<option value="active">Active</option>
						<option value="return">Return Requests</option>
						<option value="no_confirm">Unconfirmed</option>
					</select>
				</div>
				<div class="iGroup fw">
					<label>Ordered for</label>
					<select name="action" onchange="is_issue(this.value);">
						<option value="0">All</option>
						<option value="store">Store</option>
						<option value="customer">Customer</option>
						<option value="issue">Issue</option>
					</select>
				</div>
				<div class="iGroup fw" id="deposit" style="display: none;">
					<label>Deposid</label>
					<select name="payment">
						<option value="0">All</option>
						<option value="deposit">Deposid</option>
						<option value="no_deposit">No Deposid</option>
					</select>
				</div>
				<div class="iGroup fw" id="staff">
					<label>Customer</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="create">
					<label>Create staff</label>
					<input type="hidden" name="create">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="confirm">
					<label>Confirm staff</label>
					<input type="hidden" name="confirm">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup">
					<label>Verified</label>
					<input type="checkbox" name="verified">
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search2($('input[name=\'search\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Purchase search" onkeypress="if(event.keyCode == 13) Search2(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	[owner]
	<div class="totalTime totalMoney">
		<div class="flLeft">Q-ty: <span id="total_q">{total-q}</span></div>
		Total: $<span>{total}</span>
	</div>
	[/owner]
	<div class="userList">
		{orders}
	</div>
	{include="doload.tpl"}
</section>

<script>
function is_issue(v) {
	if (v == 'issue')
		$('#deposit').show();
	else 
		$('#deposit').hide().find('select').val('deposit').change();
}

$(function() {
	$('#calendar').calendar(function() {
		$('input[name="date_stat"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
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
					lId: lId,
					staff: 1,
					query: $('#create > .sfWrap input').val() || ''
				},
				select: $('input[name="create"]').data(),
				s: true
			}, $('input[name="create"]'));
			
			$('#confirm > ul').html(items).sForm({
				action: '/users/all',
				data: {
					lId: lId,
					staff: 1,
					query: $('#confirm > .sfWrap input').val() || ''
				},
				select: $('input[name="confirm"]').data(),
				s: true
			}, $('input[name="confirm"]'));
		}
	}, 'json');
	
	$.post('/users/all', {gId: 5}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '">' + v.name + '</li>';
				lId = v.id;
			});
			
			$('#staff > ul').html(items).sForm({
				action: '/users/all',
				data: {
					lId: lId,
					gId: 5,
					query: $('#staff > .sfWrap input').val() || ''
				},
				select: $('input[name="staff"]').data(),
				s: true
			}, $('input[name="staff"]'));
		}
	}, 'json');
	
	if (location.search) {
		var search = location.search,
			sDate = ($_GET('date_start') || ''),
			eDate = ($_GET('date_finish') || ''),
			sName = ($_GET('staff_name').replace(/%20/ig, ' ') || ''),
			oName = ($_GET('name').replace(/%20/ig, ' ') || ''),
			s = {},
			o = {};
			
		if ($_GET('staff') != 0) s[$_GET('staff')] = {name: sName};
		if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
		$('#calendar > input[name="date"]').val(sDate);
		$('#calendar > input[name="fDate"]').val(eDate);
		$('input[name="date_stat"]').val(sDate + ' / ' + eDate);
		$('input[name="staff"]').data(s);
		$('input[name="object"]').data(o);
	}
});
</script>
<script src="{theme}/new-js/purchases.js"></script>