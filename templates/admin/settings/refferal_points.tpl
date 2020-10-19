{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> {lang=RefferalPoints}
	</div>
    <form onsubmit="Settings.sendRef(this, event);">
		{items}
        <div class="iGroup addOpt">
			<button class="btn btnSubmit ao" onclick="Settings.addRefPoints(this); return false;"><span class="fa fa-plus"></span> Add Option</button>
		</div>
        <div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
    </form>
</section>