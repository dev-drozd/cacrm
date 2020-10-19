{include="invoices/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Invoice [edit]#{id} 
		<span class="invoiceStatus">Unpaid</span>
		[/edit]
	</div>
	<div class="uForm">
		<div class="uTitle dClear">
			<div class="uName wid50">
				<div>
					[add]
					[quick_sell]
					[not-quick_sell]
						[estimate]
						[not-estimate]
							[user]
								<div class="invUsrSingle">Customer: <a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a></div>
								<input name="customer" type="hidden">
							[not-user]
								<div class="iGroup fw" id="customer">
									<label>Customer select</label>
									<input name="customer" type="hidden">
									<ul></ul>
								</div>
								<div class="sGroup">
									or <button type="button" class="btn btnSubmit" onclick="Page.get('/users/add/5')">Create new user</button>
								</div>
							[/user]
						[/estimate]
					[/quick_sell]
					[not-add]
						<input type="hidden" name="customer">
						Customer: <a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a>
						<p>{customer-address}</p>
					[/add]
				</div>
				<input type="hidden" name="object">
				<div class="aRight">
					[add]
					<div class="iGroup fw oSel" id="object">
						<label>Store select</label>
						<ul></ul>
					</div>
					[not-add]
					<div class="sGroup">
						{date}
					</div>[/add]
				</div>
			</div>
		</div>
		<div class="sGroup">
			<input type="hidden" name="inventory">
			<input type="hidden" name="services">
			<input type="hidden" name="invoices">
			<input type="hidden" name="purchases">
			<input type="hidden" name="tradein">
			<input type="hidden" name="addition">
			
		</div>
		<div class="tbl payInfo mInvoice[refund] reftbl[/refund]">
			<div class="tr">
				<div class="th">
					Item
					[refund][not-refund]<button type="button" class="btn btnMini [add]hdn[/add]" onclick="invoices.addInventory({id});"><span class="fa fa-plus"></span> Add</button>[/refund]
				</div>
				<div class="th w10">
					Qty
				</div>
				<div class="th w100">
					Amount
				</div>
				<div class="th w10">
					Tax
				</div>
			</div>
			[dev]
<!-- 			<div class="tr itm" id="tr_custom" data-type="custom">
				<div class="td">
					<span class="fa fa-times del"></span>
					<input class="quickEdit" placeholder="Add case" name="case" style="width:90%;">
				</div>
				<div class="td w10">
					<input type="number" name="quantity" class="quickEdit" min="1" value="1">
				</div>
				<div class="td w100">
					<input type="number" name="quantity" class="quickEdit" min="0" value="0">
				</div>
				<div class="td w100">
					<select name="tax">
						<option value="1">Yes</option>
						<option value="2">No</option>
					</select>
				</div>
			</div> -->
			[/dev]
			{onsite}
			{issues}
			{inventory}
			{additions}
			{purchases}
			{tradein}
		</div>
		
		{invoices} 
		
		[discount]
		<div class="tbl payInfo discount">
			<div class="tr discountTr">
				<div class="td">
					{discount-name}
				</div>
				<div class="td w10">
					
				</div>
				<div class="td w100">
					-{discount-percent}%
				</div>
				<div class="td w10">
					
				</div>
			</div>
		</div>
		[/discount]
		
		<div class="dClear">
			<div class="tbl payTotalInfo">
				<div class="tr">
					<div class="td aRight invInfo">
						Subtotal
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="subtotal">{subtotal}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo" style="display: flex;">
						<div class="tax_exempt">
							<button type="button" class="btn btnSubmit" onclick="invoices.onExempt(this);">Tax exempt</button>
							<input name="tax_exempt" type="text" placeholder="Form id:" onkeyup="invoices.total()" onblur="if(!this.value){$(this.parentNode).find('button').show();$(this).hide();}">
						</div>
						Tax
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="tax">{tax}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Total
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="total">{total}</span>
					</div>
				</div>
			</div>
		</div>
		<div class="dClear">
			<div class="tbl payTotalInfo">
				<div class="tr">
					<div class="td aRight invInfo">
						Paid
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="paid">{paid}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Due
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="due">{due}</span>
					</div>
				</div>
			</div>
		</div>
		
		[refund]
			<div class="iGroup">
				<label>{lang=Comment}</label>
				<textarea name="refund_comment" placeholder="{lang=CommentRequired}">{refund_comment}</textarea>
			</div>
		[/refund]
		
		[edit]
		<div class="iGroup bEnd">
			<label>{lang=Discount}</label>
			<select name="discount">
				<option value="0">Not selected</option>
				{discounts}
			</select>
			<button type="button" class="btn btnOk" onclick="invoices.discount({id}, this, invoices.create);"><span class="fa fa-check"></span></button>
		</div>
		<div class="iGroup bEnd" id="invoices">
			<label>{lang=MergeInvoices}</label>
			<input type="hidden" name="invoices">
			<ul></ul>
			<button type="button" class="btn btnOk" onclick="invoices.merge({id}, this, invoices.create);"><span class="fa fa-check"></span></button>
		</div>
		[/edit]
		
		<div class="sGroup">
			[refund]
				<button class="btn btnSubmit" onclick="invoices.createRefund(this, {id});">Create refund</button>
			[not-refund]
				<button class="btn btnSubmit" onclick="invoices.create(this, {id}[estimate], 1[/estimate]);">[add]{lang=createInvoice}[not-add]{lang=editInvoice}[/add]</button>
			[/refund]
		</div>
		
	</div>
</section>
<script>
$(function() {
	[user]
		$('input[name="customer"]').data({
			{user-id}: {
				name: '{js-customer-name} {js-customer-lastname}'
			}
		});
	[/user]
	[add]
	[estimate]
	[not-estimate]
	$.post('/users/all', {gId: 5}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '">' + v.name + '</li>';
				lId = v.id;
			});
			$('#customer > ul').html(items).sForm({
				action: '/users/all',
				data: {
					gId: 5,
					lId: lId,
					nIds: Object.keys($('input[name="customer"]').data() || {}).join(','),
					query: $('#customer > .sfWrap input').val() || ''
				},
				all: false,
				select: $('input[name="customer"]').data(),
				s: true,
				link: 'users/view'
			}, $('input[name="customer"]'));
		}
	}, 'json');
	[/estimate]
	
	$.post('/invoices/objects', {nIds: Object.keys($('input[name="object"]').data()).join(',')[store][not-store], all: 1[/store]}, function (r) {
		if (r){
			if(r.list.length > 1) {
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#object > ul').html(items).sForm({
					action: '/invoices/objects',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="object"]').data() || {}).join(','),
						query: $('#object > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="object"]').data(),
					s: true,
					link: 'objects/edit'
				}, $('input[name="object"]'), function() {
					$('input[name="object"]').data() ? $('.btnMini').show() : $('.btnMini').hide();
				});
			} else if (r.list.length == 1) {
				var cash_object = {};
				cash_object[r.list[0].id] = {
					name: r.list[0].name,
					tax: r.list[0].tax
				}
				$('input[name="object"]').data(cash_object);
				$('#object').append($('<div/>', {
					class: 'storeOne',
					html: r.list[0].name
				}));
				$('.btnMini').show();
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to stores or trying to enter not from the store',
					delay: 3
				});
				Page.get('/invoices');
			}
		}
	}, 'json');
	[/add]
	
	[edit]
	var inventories = {}, purchases = {}, tradein = {}, services = {}, additions = {};
	$('.mInvoice .tr').each(function(i, v) {
		if ($(v).find('.td').length) {
			if ($(v).attr('data-type') == 'stock') {
				inventories[$(v).attr('data-id')] = {
					name: $(v).find('.catname').text() + ' ' + $(v).find('.iname').text(),
					price: parseFloat($(v).find('.w100').text().replace(/(\D*)/i, '')) / parseInt($(v).find('input[name="quantity"]').val()),
					items: $(v).find('input[name="quantity"]').val(),
					quantity: $(v).find('input[name="quantity"]').attr('max')
				}
			} else if ($(v).attr('data-type') == 'purchase') {
				purchases[$(v).attr('data-id')] = {
					name: $(v).find('.iname').text(),
					price: parseFloat($(v).find('.w100').text().replace(/(\D*)/i, ''))
				}
			} else if ($(v).attr('data-type') == 'tradein') {
				tradein[$(v).attr('data-id')] = {
					name: $(v).find('.iname').text(),
					price: parseFloat($(v).find('.w100').attr('price').replace(/(\D*)/i, '')),
					purchase: parseFloat($(v).find('.w100').text().replace(/(\D*)/i, ''))
				}
			} else if ($(v).attr('data-type') == 'service') {
				services[$(v).attr('data-id')] = {
					name: $(v).find('.iname').text(),
					price: $(v).find('.w100').text().replace(/(\D*)/i, '')
				}
			} else if ($(v).attr('data-type') == 'addition') {
				additions[$(v).attr('data-id')] = {
					name: $(v).find('.iname').text(),
					price: $(v).find('.w100').text().replace(/(\D*)/i, '')
				}
			}
		}
	});
	
	$('input[name="customer"]').data({
		{customer-id}: {
			'name': '{customer-name} {customer-lastname}'
		}
	});
	$('input[name="object"]').data({
		{object}: {
			'name': '{object-name}',
			'tax': '{object-tax}'
		}
	});
	$('input[name="inventory"]').data(inventories);
	$('input[name="tradein"]').data(tradein);
	$('input[name="purchases"]').data(purchases);
	$('input[name="services"]').data(services);
	$('input[name="addition"]').data(additions);
	
	$.post('/invoices/all', {
		nIds: Object.keys($('input[name="invoices"]').data() || {}).join(',') + ',{id}',
		oId: {object},
		paid: 'unpaid'
	}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '">' + v.name + '</li>';
				lId = v;
			});
			$('#invoices > ul').html(items).sForm({
				action: '/invoices/all',
				data: {
					lId: lId,
					oId: {object}, 
					nIds: Object.keys($('input[name="invoices"]').data() || {}).join(','),
					query: $('#invoices > .sfWrap input').val() || '',
					paid: 'unpaid'
				},
				all: false,
				select: $('input[name="invoices"]').data(),
				link: 'invoices/view'
			}, $('input[name="invoices"]'));
		}
	}, 'json');
	[/edit]
});

function changeQuantity(e, t) {
	if (parseInt(e.value) > parseInt(e.max)) 
		e.value = e.max; 
	if (parseInt(e.value) < parseInt(e.min)) 
		e.value = e.min;
		
	var items = $('input[name="'+(t == 'stock' ? 'inventory' : (t == 'addition' ? t : 'services'))+'"]').data();
	items[$(e).parents('.tr').attr('data-id')].items = e.value;
	$('input[name="'+(t == 'stock' ? 'inventory' : (t == 'addition' ? t : 'services'))+'"]').data(items);
	
	$(e).parent().next().html('$' + parseFloat(parseFloat((items[$(e).parents('.tr').attr('data-id')].price + ' ').replace('$', '').trim()) * parseInt(e.value)).toFixed(2));
	invoices.total();
}
[modal]
$(document).ready(function(){
	setTimeout(function(){
		invoices.addInventory(0);
		setTimeout(function(){
			$('li[data-value="purch"]').click();
		},1);
	},1);
});
[/modal]
</script>
<style>
	@media (max-width: 767px) {
		.tbl.payInfo .td:nth-child(4) {
			width: 30%!important;
		}

		.tbl.payInfo .td:nth-child(2):before {
			content: 'Q-ty: ';
		}

		.tbl.payInfo .td:nth-child(3):before {
			content: 'Price: ';
		}

		.tbl.payInfo .td:nth-child(4):before {
			content: 'Tax: ';
		}
	}
</style>