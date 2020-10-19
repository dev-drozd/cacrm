<div class="sUser {confirmed}" id="discount_{id}">
	<div class="uInfo">
		{name}
	</div>
	<div class="pInfo">
		{percent}%
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href='javascript:invoices.openDiscount({id}, "{name}", {percent})'><span class="fa fa-pencil"></span> {lang=editDiscount}</a></li>[/edit]
			[add]<li><a href="javascript:invoices.delDiscount({id})"><span class="fa fa-times"></span> {lang=delDiscount}</a></li>[/add]
		</ul>
	</div>
</div>