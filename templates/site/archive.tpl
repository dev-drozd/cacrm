<div class="page">
	<div class="ctnr">
		<h1 align="center">{title}</h1>
		<div style="text-align: center">
			<img src="/uploads/images/work_archive/{id}/{image}" alt="{title}">
		</div>
		<div class="post-content">{content}</div>
	</div>
</div>
<script>
function this_quote(){
	quote.mdl();
	setTimeout(function(){
		$('textarea[name="issue"]').val('{issue}');
	}, 0);
}
</script>