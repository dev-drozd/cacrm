{include="invoices/discount/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Form</div>
	<form class="uForm" method="post" onsubmit="invoices.saveForm(this, event[email_form], 1[/email_form]);">
		<div class="iGroup fw">
			<label>{lang=Content}</label>
			<textarea name="content" id="editor">{content}</textarea>
		</div>
		<div class="iGroup">
			<label>{lang=AvailableTags}</label>
			<div class="aTags">
				<ul>
					<li>&#123;logo&#125;</li>
					<li>&#123;name&#125;</li>
					<li>&#123;address&#125;</li>
					<li>&#123;email&#125;</li>
					<li>&#123;cellphone&#125;</li>
					<li>&#123;subtotal&#125;</li>
					<li>&#123;total&#125;</li>
					<li>&#123;tax&#125;</li>
					<li>&#123;paid&#125;</li>
					<li>&#123;due&#125;</li>
					<li>&#123;date&#125;</li>
					<li>&#123;invoices&#125;</li>
					<li>&#123;issues&#125;</li>
					<li>&#123;inventory&#125;</li>
					<li>&#123;purchases&#125;</li>
					<li>&#123;discount-name&#125;</li>
					<li>&#123;discount-percent&#125;</li>
					<li>&#123;object-name&#125;</li>
					<li>&#123;invoice-barcode&#125;</li>
					<li>&#123;customer-barcode&#125;</li>
					<li>&#123;issue-barcode&#125;</li>
					<li>&#123;city&#125;</li>
					<li>&#123;zipcode&#125;</li>
					<li>&#123;type&#125;</li>
					<li>&#123;serial&#125;</li>
					<li>&#123;model&#125;</li>
					<li>&#123;quote&#125;</li>
					<li>&#123;store_name&#125;</li>
					<li>&#123;store_address&#125;</li>
					<li>&#123;store_cell&#125;</li>
					<li>&#123;opt_charger&#125;</li>
					<li>&#123;assigned&#125;</li>
					<li>&#123;issue_status&#125;</li>
					<li>&#123;onsite&#125;</li>
					<li>&#123;currency&#125;</li>
				</ul>
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
	</form>
</section>
<script>
	$(function() {
		$('#editor').fEditor();
	});
</script>