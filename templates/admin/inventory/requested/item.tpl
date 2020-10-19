<div class="sUser {confirmed}" id="request_{id}">
	<a href="javascript:inventory.addRequest({id}, '{name}', '{type}'[service], '{price}'[/service]);" class="uInfo">
			{name}
			<p class="trade_price">{type}
			[service]<br/>Price: <span>${price}</span>[/service]</p>
	</a>
	<div class="pStatus confBtn">
			Create: {cr-user}<br>{cr-date}
			[confirmed]<br>Confirm: {cn-user}<br>{cn-date}[/confirmed]
	</div>
	<div class="pStatus nIssue">
		[confirm]
			[confirmed]
			[not-confirmed]
				<a href="javascript:inventory.requestConfirm({id});" class="trCOnf">Confirm</a>
			[/confirmed]
		[/confirm]
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href="javascript:inventory.addRequest({id}, '{name}', '{type}'[service], '{price}'[/service]);"><span class="fa fa-pencil"></span> {lang=editRequest}</a></li>[/edit]
			[del]<li><a href="javascript:inventory.delRequest({id})"><span class="fa fa-times"></span> {lang=del}</a></li>[/del]
		</ul>
	</div>
</div>