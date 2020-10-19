{include="inventory/types/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=OnsiteForms}</div>
    <form onsubmit="inventory.onsiteForms(this, event);">
        <div class="iGroup fw">
            <label>{lang=SMSForm}</label>
            <textarea name="sms_form">{sms_onsite}</textarea>
        </div>
        <div class="iGroup fw">
            <label>{lang=EmailForm}</label>
            <textarea name="email_form">{form_onsite}</textarea>
        </div>
        <div class="iGroup">
			<label>{lang=>AvailableTags}</label>
			<div class="aTags">
				<ul>
					<li>&#123;logo&#125;</li>
					<li>&#123;customer_name&#125;</li>
					<li>&#123;customer_lastname&#125;</li>
					<li>&#123;customer_phone&#125;</li>
					<li>&#123;customer_address&#125;</li>
					<li>&#123;customer_email&#125;</li>
					<li>&#123;service_name&#125;</li>
					<li>&#123;price&#125;</li>
					<li>&#123;currency&#125;</li>
					<li>&#123;used_time&#125;</li>
					<li>&#123;time_left&#125;</li>
					<li>&#123;date&#125;</li>
					<li>&#123;service_start_date&#125;</li>
					<li>&#123;service_end_date&#125;</li>
				</ul>
			</div>
		</div>
        <div class="sGroup">
            <button class="btn btnSubmit" type="submit">{lang=Send}</button>
        </div>
    </form>
</section>
<script>
	$(function() {
		$('textarea').fEditor();
	});
</script>