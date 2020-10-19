<div class="acordion">
	<div class="acordionTitle[can_edit] adm[/can_edit]" onclick="accordeon(this);">
		{title}
		[can_edit]
			<span class="fa fa-pencil" onclick="Page.get('/faq/edit/{id}');"></span>
		[/can_edit]
		[can_del]
			<span class="fa fa-times" onclick="faq.del({id}, this);"></span>
		[/can_del]
	</div>
	<div class="acordionContent hide">{content}</div>
</div>