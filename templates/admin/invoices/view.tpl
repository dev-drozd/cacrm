{include="invoices/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Invoice #{id} 
		<span class="invoiceStatus[conducted] paid[/conducted]">[conducted]Paid[not-conducted]Unpaid[/conducted]</span>
		<a href="/im/{staff-id}?text=Invoice;{id}" onclick="Page.get(this.href); return false;" class="mesBtn">
			<span class="fa fa-exclamation-circle" aria-hidden="true"></span>
		</a>
<!-- 		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul>
			[deleted]
				<li><a href="javascript:invoices.restore({id})"><span class="fa fa-arrow-left"></span> Restore invoice</a></li>
			[not-deleted]
				<li><a href="/invoices/print/{id}" target="_blank"><span class="fa fa-print"></span> {lang=printInvoice}</a></li>
				<li><a href="javascript:invoices.send_mail({id});"><span class="fa fa-envelope"></span> {lang=SendToEmail}</a></li>
				[estimate][not-estimate][order][not-order][can-edit][refund][not-refund][add]<li><a href="/invoices/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> {lang=editInvoice}</a></li>[/add][/refund][/can-edit][/order][/estimate]
				[issue]<li><a href="javascript:invoices.update({id})"><span class="fa fa-refresh"></span> Update invoice</a></li>[/issue]
				[owner]
					<li><a href="javascript:invoices.del({id})"><span class="fa fa-times"></span> {lang=delInvoice}</a></li>
				[not-owner]
					[del_invoice]<li><a href="javascript:invoices.del({id})"><span class="fa fa-times"></span> {lang=delInvoice}</a></li>[/del_invoice]
				[/owner]
				[estimate]
					<li><a href="javascript:invoices.estimateToInv({id})"><span class="fa fa-check"></span> Create invoice</a></li>
				[not-estimate]	
				[paid]
				[refund_confirm]
						[user_refund_confirm]<li><a href="javascript:invoices.refundConfirm({id})"><span class="fa fa-check"></span> Confirm Refund</a></li>[/user_refund_confirm]
						[user_refund_confirm]<li><a href="javascript:invoices.refundDecline({id})"><span class="fa fa-times"></span> Decline Refund</a></li>[/user_refund_confirm]
					[not-refund_confirm]
						[refund-invoice]
						[not-refund-invoice]
							[refund_request]<li><a href="/invoices/make_refund/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-arrow-left"></span> Refund</a></li>[/refund_request]
						[/refund-invoice]
					[/refund_confirm]
				[/estimate]
				[/paid]
			[/deleted]
			</ul>
		</div> -->
		<div class="flRight">
			
			[deleted]
				<div class="uCamera hnt hntBottom" data-title="Restore invoice" onclick="invoices.restore({id})">
					<span class="fa fa-arrow-left"></span>
				</div>
			[not-deleted]
				<!--[conducted][not-conducted]<li><a href="#" onclick="Page.get(this.href); return false;"><span class="fa fa-credit-card"></span> {lang=Checkout}</a></li>[/conducted]-->
				<div class="uCamera hnt hntBottom" data-title="{lang=printInvoice}" style="background: #36b1e6;" onclick="window.open('/invoices/print/{id}')">
					<span class="fa fa-print"></span>
				</div>
				<div class="uCamera hnt hntBottom" data-title="{lang=SendToEmail}" style="background: #55b9a7;" onclick="invoices.send_mail({id})">
					<span class="fa fa-envelope"></span>
				</div>
				[estimate][not-estimate][order][not-order][can-edit][refund][not-refund][add]
				<div class="uCamera hnt hntBottom" data-title="{lang=editInvoice}" style="background: #88b52d" onclick="Page.get('/invoices/edit/{id}')">
					<span class="fa fa-pencil"></span>
				</div>
				[/add][/refund][/can-edit][/order][/estimate]
				[issue]
<!-- 				<div class="uCamera hnt hntBottom" data-title="Update invoice" onclick="invoices.update({id})">
					<span class="fa fa-refresh"></span>
				</div> -->
				[/issue]
				[owner]
					<div class="uCamera hnt hntBottom" data-title="{lang=delInvoice}" style="background: #bb3c3c;" onclick="invoices.del({id})">
						<span class="fa fa-times"></span>
					</div>
				[not-owner]
					[del_invoice]
					<div class="uCamera hnt hntBottom" data-title="{lang=delInvoice}" style="background: #bb3c3c;" onclick="invoices.del({id})">
						<span class="fa fa-times"></span>
					</div>
					[/del_invoice]
				[/owner]
				[estimate]
					<div class="uCamera hnt hntBottom" data-title="Create invoice" onclick="invoices.estimateToInv({id})">
						<span class="fa fa-check"></span>
					</div>
				[not-estimate]	
				[paid]
				[refund_confirm]
						[user_refund_confirm]
						<div class="uCamera hnt hntBottom" data-title="Confirm Refund" onclick="invoices.refundConfirm({id})">
							<span class="fa fa-check"></span>
						</div>
						[/user_refund_confirm]
						[user_refund_confirm]
							<div class="uCamera hnt hntBottom" data-title="Decline Refund" onclick="invoices.refundDecline({id})">
								<span class="fa fa-times"></span>
							</div>
						[/user_refund_confirm]
					[not-refund_confirm]
						[refund-invoice]
						[not-refund-invoice]
							[refund_request]
								<div class="uCamera hnt hntBottom" data-title="Refund" style="background: #299CCE" onclick="Page.get('/invoices/make_refund/{id}')">
									<span class="fa fa-arrow-left"></span>
								</div>
							[/refund_request]
						[/refund-invoice]
					[/refund_confirm]
				[/estimate]
				[/paid]
			[/deleted]
		</div>
		
	</div>
	<div class="uForm">
		<div class="uTitle dClear">
			<div class="uName wid50">
				<div>
					Customer: <a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a>
					<p>{customer-address}</p>
					<p>Staff: <a href="/users/view/{staff-id}" onclick="Page.get(this.href); return false;">{staff-name} {staff-lastname}</a></p>
				</div>
				<div class="aRight">
					{date}
				</div>
			</div>
		</div>
		[paid][not-paid]
		[has_purchase]
			<div class="mt dClear">Purchase will not be confirmed before invoice paid</div>
		[/has_purchase]
		[refund_confirm]
			<div class="mt dClear">Refund panding for confirmation</div>
		[/refund_confirm]
		[discount][discount-confirmed][not-discount-confirmed]
			<div class="mt dClear">You can not paid invoice without discount confirmation</div>
		[/discount-confirmed][/discount]
		[/paid]
		<div class="tbl payInfo">
			<div class="tr">
				<div class="th">
					Item
					<button type="button" class="btn btnMini hdn" onclick="invoices.addInventory({id});"><span class="fa fa-plus"></span> Add inventory</button>
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
			{onsite}
			{issues}
			{inventory}
			{purchases}
			{additions}
			{tradein}
			{refund}
			{delivery}
		</div>
		
		{invoices} 
		
		[refund]
			<p class="refundComment"><b>{lang=RefundComment}:</b> {refund_comment}</p>
		[/refund]
		
		[discount]
		<div class="tbl payInfo discount">
			<div class="tr">
				<div class="td">
					{discount-name}
				</div>
				<div class="td">
					
				</div>
				<div class="td w100" style="text-align: right;">
					-{discount-percent}%
				</div>
				<div class="td" style="width: 200px">
					[discount-confirmed][not-discount-confirmed][confirm-discount]
						<a href="javascript:invoices.confirmDiscountInvoice({id});" ondblclick="return false;" data-title="Confirm discount" class="hnt hntTop"><span class="fa fa-check" id="discountConfirm"></span> Confirm discount</a>
					[/confirm-discount][/discount-confirmed]
				</div>
			</div>
		</div>
		[/discount]
		
		[store-discount]
		<div class="tbl payInfo discount">
			<div class="tr">
				<div class="td">
					Discount code: {store-discount}
				</div>
				<div class="td">
					
				</div>
				<div class="td w100" style="text-align: right;">
					-{store-discount-amount}%
				</div>
				<div class="td">
				</div>
			</div>
		</div>
		[/store-discount]
		
		[chacks]<div class="tbl payInfo discount">{chacks}</div>[/chacks]
			
		<div class="dClear dClear_paid">
			[conducted]<img src="{theme}/img/paid-red-small.png" class="paidImg">[/conducted]
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
					<div class="td aRight invInfo"[tax-exempt] style="display: flex;"[/tax-exempt]>
						[tax-exempt]<div class="tax_exempt">
							<label>Form id:</label> {tax-exempt}
						</div>
						[/tax-exempt]
						Tax
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="tax">{tax}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Service charge
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="charge" data-percent="0">{charge}</span>
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
		[deleted]
		[not-deleted]
			[estimate]
			[not-estimate]
				[conducted]
				[not-conducted]
				<!-- <div class="iGroup bEnd">
				<label>{lang=PartialPayment}</label>
					<input type="number" name="partial" step="0.1" min="0" max="{due}">
					<button type="button" class="btn btnOk" onclick="invoices.partial({id}, this, {purchace});"><span class="fa fa-check"></span></button>
				</div> -->
				[purchace]
				[not-purchace]
				[refund]
				[not-refund]
					[store-discount]
					[not-store-discount]
						[discount]
						[not-discount]
							<div class="sGroup">
								<button type="button" class="btn btnSubmit" onclick="invoices.addStoreDiscount({id}, this);"><span class="fa fa-percent"></span> Enter discount code</button>
							</div>
						[/discount]
						<div class="iGroup bEnd">
							<label>{lang=Discount}</label>
							<select name="discount">
								<option value="0">Not selected</option>
								{discounts}
							</select>
							<button type="button" class="btn btnOk" onclick="invoices.discount({id}, this);"><span class="fa fa-check"></span></button>
						</div>
					[/store-discount]
					<div class="iGroup bEnd" id="invoices">
						<label>{lang=MergeInvoices}</label>
						<input type="hidden" name="invoices">
						<ul></ul>
						<button type="button" class="btn btnOk" onclick="invoices.merge({id}, this);"><span class="fa fa-check"></span></button>
					</div>
				[/refund]
				[/purchace]
				[can_pay]
				<div class="iGroup">
					<label>{lang=PaymentMethod}</label>
					<select name="method" onchange="change();">
						<option value="0">No select</option>
						<option value="cash">Cash</option>
						[purchace][not-purchace]
						<option value="credit">Credit</option>
						[/purchace]
						<option value="check">Check</option>
					</select>
					<input type="number" name="cash" step="0.001" placeholder="Amount" class="method_num hdn" onkeyup="rest();">
					<input type="number" name="check" placeholder="Check number" class="method_num hdn">
				</div>
				[not-can_pay]
					[discountcf]<div class="mt dClear">Can not take payment while discount is unconfirmed</div>[/discountcf]
				[/can_pay]
				[purchace][not-purchace]
				<div class="iGroup rest hdn">
					<label class="method_num">{lang=ChangeOwed}</label>
					<input type="number" readonly name="rest" step="0.1" placeholder="{lang=ChangeOwed}" class="method_num">
				</div>
				[/purchace]
				[can_pay]
				<div class="sGroup">
					<button class="btn btnSubmit" onclick="invoices.makeTran({id}, this, {purchace});">Done transaction</button>
				</div>
				[/can_pay]
				[/conducted]
			[/estimate]
		[/deleted]
		{history}
	</div>
</section>
<script>
var cashCharge = {cash-charge},
	creditCharge = {credit-charge},mTotal = {total}, mDue = {due};
	
function rest() {
	var due = parseFloat($('#due').text()) < 0 ? -parseFloat($('#due').text()) : parseFloat($('#due').text());
	if (parseFloat($('input[name="cash"]').val()) > due) 
		$('input[name="rest"]').val((parseFloat($('input[name="cash"]').val()) - due).toFixed(2));
	else 
		$('input[name="rest"]').val(0);
}

function change() {
	var total = Number($('#total').text());
	switch ($('select[name="method"]').val()) {
		case 'cash':
			$('input[name="cash"]').removeClass('hdn');
			$('input[name="check"]').addClass('hdn');
			[charge][not-charge]
			var percent = (Number(mTotal)/100)*cashCharge;
			$('#charge').text(percent.toFixed(2)).attr('data-percent', cashCharge);
			$('#total').text((mTotal+percent).toFixed(2));
			$('#due').text((mDue+percent).toFixed(2));
			[/charge]
			$('.rest').removeClass('hdn');
		break;
		
		case 'credit':
			$('input[name="cash"]').removeClass('hdn');
			$('input[name="check"]').addClass('hdn');
			[charge][not-charge]
			var percent = (Number(mTotal)/100)*creditCharge;
			$('#charge').text(percent.toFixed(2)).attr('data-percent', creditCharge);
			$('#total').text((mTotal+percent).toFixed(2));
			$('#due').text((mDue+percent).toFixed(2));
			[/charge]
			$('.rest').addClass('hdn');
		break;
		
		case 'check':
			$('input[name="cash"]').removeClass('hdn');
			$('input[name="check"]').removeClass('hdn');
			[charge][not-charge]
			$('#charge').text('0.00');
			$('#total').text(mTotal);
			$('#due').text(mDue);
			[/charge]
			$('.rest').addClass('hdn');
		break;
		
		default:
			$('input[name="cash"]').addClass('hdn');
			$('input[name="check"]').addClass('hdn');
			[charge][not-charge]
			$('#charge').text('0.00');
			$('#total').text(mTotal);
			$('#due').text(mDue);
			[/charge]
			$('.rest').addClass('hdn');
		break;
	}
}

$(function() {
	$.post('/invoices/all', {
		nIds: Object.keys($('input[name="invoices"]').data() || {}).join(',') + ',{id}',
		oId: {object},
		paid: 'unpaid',
		partial: 1
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
					paid: 'unpaid',
					partial: 1
				},
				all: false,
				select: $('input[name="invoices"]').data()
			}, $('input[name="invoices"]'));
		}
	}, 'json');
})
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