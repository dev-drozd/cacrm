<div class="sUser" id="upcharge_{id}">
	<a [edit]href="javascript:inventory.addUpcharge({id}, '{name}', '{price}');"[/edit] class="uInfo">
			{name}
			<p class="trade_price"><br/>Price: <span>${price}</span></p>
	</a>
	[action]
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href="javascript:inventory.addUpcharge({id}, '{name}', '{price}');"><span class="fa fa-pencil"></span> {lang=editUpcharge}</a></li>[/edit]
			[del]<li><a href="javascript:inventory.delUpcharge({id})"><span class="fa fa-times"></span> {lang=delUpcharge}</a></li>[/del]
		</ul>
	</div>
	[/action]
</div>