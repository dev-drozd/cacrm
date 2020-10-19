<section class="mngContent ffw">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{page-title}</div>
	<form class="uForm" method="post" onsubmit="faq.send(this, event, {id});">
		<div class="iGroup fw">
			<label>{lang=Title}</label>
			<input type="text" name="title" value="{title}">
		</div>
		<div class="iGroup fw">
			<label>{lang=Text}</label>
			<textarea name="content" id="editor">{content}</textarea>
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
