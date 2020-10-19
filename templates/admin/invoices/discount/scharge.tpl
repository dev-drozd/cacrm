{include="invoices/discount/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Service charge
	</div>
	<form class="uForm" id="service_charge" method="post" action="/invoices/save_service_charge" onsubmit="sCharge(this, event);">
		<div class="iGroup">
			<label>Cash service charge</label>
			<input type="number" name="cash_charge" value="{cash-charge}" min="0" required>
		</div>
		<div class="iGroup">
			<label>Credit service charge</label>
			<input type="number" name="credit_charge" value="{credit-charge}" min="0" required>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
	</form>
</section>
<script>
$('#service_charge').ajaxSubmit({
	callback: function(r){
		alr.show({
			class: 'alrSuccess',
			content: 'Successfully saved.',
			delay: 2
		});
	}
});
</script>