{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> Emails
	</div>
    <form onsubmit="Settings.sendEmails(this, event);">
		{items}
        <div class="iGroup addOpt">
			<button class="btn btnSubmit ao" onclick="Settings.addEmail(this); return false;"><span class="fa fa-plus"></span> Add Option</button>
		</div>
        <div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
    </form>
</section>