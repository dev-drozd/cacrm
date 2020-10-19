{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<form class="uForm" method="post" onsubmit="Settings.sendForm(this, event, {id});">
		<div class="iGroup fw">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup">
			<label>{lang=Type}</label>
			<select name="type" multiple>{options}</select>
		</div>
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
					<li>&#123;photo&#125;</li>
					<li>&#123;price&#125;</li>
					<li>&#123;tax&#125;</li>
					<li>&#123;total_price&#125;</li>
					<li>&#123;device&#125;</li>
					<li>&#123;issue&#125;</li>
					<li>&#123;issue_status&#125;</li>
					<li>&#123;issue_dsc&#125;</li>
					<li>&#123;customer_barcode&#125;</li>
					<li>&#123;issue_barcode&#125;</li>
					<li>&#123;device_barcode&#125;</li>
					<li>&#123;date&#125;</li>
					<li>&#123;devices&#125;</li>
					<li>&#123;services&#125;</li>
					<li>&#123;invoices&#125;</li>
					<li>&#123;assigned&#125;</li>
					<li>&#123;address&#125;</li>
					<li>&#123;cellphone&#125;</li>
					<li>&#123;email&#125;</li>
					<li>&#123;quote_price&#125;</li>
					<li>&#123;intake_date&#125;</li>
					<li>&#123;opt_charger&#125;</li>
					<li>&#123;zip&#125;</li>
					<li>&#123;city&#125;</li>
					<li>&#123;country&#125;</li>
					<li>&#123;address&#125;</li>
					<li>&#123;tech_summery&#125;</li>
					<li>&#123;device_typ&#125;</li>
					<li>&#123;device_brand&#125;</li>
					<li>&#123;device_mod&#125;</li>
					<li>&#123;device_sn&#125;</li>
					<li>&#123;store_cell&#125;</li>
					<li>&#123;store_address&#125;</li>
					<li>&#123;store_name&#125;</li>
				</ul>
			</div>
		</div>
		<div class="iGroup">
			<label>Only transfer form tags</label>
			<div class="aTags">
				<ul>
					<li>&#123;logo&#125;</li>
					<li>&#123;send_name&#125;</li>
					<li>&#123;send_photo&#125;</li>
					<li>&#123;send_cellphone&#125;</li>
					<li>&#123;receive_name&#125;</li>
					<li>&#123;receive_photo&#125;</li>
					<li>&#123;receive_cellphone&#125;</li>
					<li>&#123;send_store&#125;</li>
					<li>&#123;receive_store&#125;</li>
					<li>&#123;send_store_cellphone&#125;</li>
					<li>&#123;receive_store_cellphone&#125;</li>
					<li>&#123;send_store_address&#125;</li>
					<li>&#123;receive_store_address&#125;</li>
					<li>&#123;inventory&#125;</li>
				</ul>
			</div>
		</div>
		<div class="iGroup">
			<label>Only order form tags</label>
			<div class="aTags">
				<ul>
					<li>&#123;logo&#125;</li>
					<li>&#123;customer_name&#125;</li>
					<li>&#123;customer_lastname&#125;</li>
					<li>&#123;customer_phone&#125;</li>
					<li>&#123;customer_address&#125;</li>
					<li>&#123;customer_email&#125;</li>
					<li>&#123;subtotal&#125;</li>
					<li>&#123;total&#125;</li>
					<li>&#123;tax&#125;</li>
					<li>&#123;currency&#125;</li>
					<li>&#123;date&#125;</li>
					<li>&#123;products&#125;</li>
					<li>&#123;delivery&#125;</li>
					<li>&#123;payment&#125;</li>
					<li>&#123;delivery-name&#125;</li>
					<li>&#123;delivery-price&#125;</li>
					<li>&#123;note&#125;</li>
				</ul>
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	$(function() {
		$('#editor').fEditor();
	});
</script>
