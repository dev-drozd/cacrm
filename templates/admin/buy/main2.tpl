<style>.userList{width: auto;display: table-row-group;}</style>
{include="buy/menu.tpl"}
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
<!-- 				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="0">All</option>
						<option value="active">Active</option>
						<option value="return">Return Requests</option>
						<option value="no_confirm">Unconfirmed</option>
					</select>
				</div> -->
				<div class="iGroup fw">
					<label>Ordered for</label>
					<select name="action" onchange="is_issue(this.value);">
						<option value="0">All</option>
						<option value="store">Store</option>
						<option value="customer">Customer</option>
						<option value="issue">Issue</option>
					</select>
				</div>
<!-- 				<div class="iGroup fw" id="deposit" style="display: none;">
					<label>Deposid</label>
					<select name="payment">
						<option value="0">All</option>
						<option value="deposit">Deposid</option>
						<option value="no_deposit">No Deposid</option>
					</select>
				</div> -->
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
		<input type="text" name="search" placeholder="Purchase search" oninput="Search2(this.value)" onkeypress="if(event.keyCode == 13) Search2(this.value);" oninput="checkBarcode(this.value);" value="{query}">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tab-cnt" onclick="pTabs(event, this)">
		<span data-status="0">All</span>
		<span data-status="pending">Pending</span>
		<span data-status="partial">Partial</span>
		<span data-status="confirmed">Confirmed</span>
		<span data-status="unconfirmed">Unconfirmed</span>
		<span data-status="recived">Received</span>
		<span data-status="with_deposit">With deposit</span>
<!-- 		<span data-status="store">Store</span>
		<span data-status="partial_deposit">Partial deposit</span> -->
		<span data-status="rma">RMA</span>
		<span data-status="deleted">Deleted</span>
	</div>
	[owner]
	<div class="totalTime totalMoney">
		<div class="flLeft">Q-ty: <span id="total_q">{total-q}</span></div>
		Total: $<span id="total">{total}</span>
	</div>
	[/owner]
	<table class="responsive">
		<thead>
			<tr>
				<th><a data-sort="id" data-sort-type="0" href="#" onclick="return sortRes(this);">#</a></th>
				<th><a data-sort="photo" data-sort-type="0" href="#" onclick="return sortRes(this);">IMAGE</a></th>
				<th><a data-sort="name" data-sort-type="0" href="#" onclick="return sortRes(this);">NAME</a></th>
				<th><a data-sort="date" data-sort-type="0" href="#" onclick="return sortRes(this);">DATE</a></th>
				<th><a data-sort="status" data-sort-type="0" href="#" onclick="return sortRes(this);">STATUS</a></th>
				<th><a data-sort="price" data-sort-type="0" href="#" onclick="return sortRes(this);">PRICE</a></th>
				<th><a data-sort="quantity" data-sort-type="0" href="#" onclick="return sortRes(this);">QUANTITY</a></th>
				<th><a data-sort="total" data-sort-type="0" href="#" onclick="return sortRes(this);">TOTAL</a></th>
				<th>OPTIONS</th>
			</tr>
		</thead>
		<tbody class="userList">
			{orders}
		</tbody>
	</table>
	{include="doload.tpl"}
</section>

<script>
var pTabs = (a,b) => {
		$('body > header').addClass('main-loader');
		$(b).find('span').removeClass('active');
		var st = $(a.target).addClass('active').data('status'),
			h = location.href.replace(location.pathname, '/buy'+(
				st ? '/'+st : ''
			));
		history.pushState({
			link: h
		}, null, h);
		$.post(h, {st: true}, function(a){
			if(a.content){
				$('.userList').html(a.content);
				$('#total_q').text(a.total_q);
				$('#total').text(a.total);
			}
			$('body > header').removeClass('main-loader');
		}, 'json');
	};
function is_issue(v) {
	if (v == 'issue')
		$('#deposit').show();
	else 
		$('#deposit').hide().find('select').val('deposit').change();
}

var objects = {};

$(function() {
	$('[data-status="'+(
		location.pathname.split('/')[2] || 0
	)+'"]').addClass('active');
	$('#calendar').calendar(function() {
		$('input[name="date_stat"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
		$('.dGroup > input + div').hide().parent().removeClass('act');
	});
	$.post('/invoices/objects', {}, function (r) {
		if (r){
			if (r.list.length > 1) {
				for(let k in r.list){
					objects[r.list[k].id] = r.list[k].name;
				}
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
		if ($_GET('object') != 0) o[$_GET('object')] = {name: objects[$_GET('object')]};
		$('#calendar > input[name="date"]').val(sDate);
		$('#calendar > input[name="fDate"]').val(eDate);
		$('input[name="date_stat"]').val(sDate + ' / ' + eDate);
		$('input[name="staff"]').data(s);
		$('input[name="object"]').data(o);
	}
});
</script>
<script src="{theme}/new-js/purchases.js"></script>