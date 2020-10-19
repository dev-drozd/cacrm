{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<form class="uForm" method="post" onsubmit="store.sendIntroducing(this, event, {id});">
		<div class="iGroup fw">
			<label>{lang=Title}</label>
			<input type="text" name="title" value="{intTitle}">
		</div>
		<div class="iGroup fw">
			<label>{lang=Text}</label>
			<textarea name="content" id="editor">{intContent}</textarea>
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
