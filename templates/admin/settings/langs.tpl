{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Languages}
		<button class="btn btnAddGr" onclick="Languages.create();">New languages</button>
	</div>
	<form class="uForm" method="post" onsubmit="Languages.save(this, event);">
		<div class="iGroup">
			<label>{lang=LangEdit}</label>
			<select name="lang" onchange="Languages.get(this.value);">
				{languages}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=LangModules}</label>
			<select name="module" onchange="Languages.getModule(this.value);">
				{modules}
			</select>
		</div>
		<div class="sTitle">Phrases</div>
		<div id="phrases">{phrases}</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Save</button>
		</div>
	</form>
</section>