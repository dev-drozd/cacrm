<div class="tr[dicline] dicline[/dicline][dicline_more] dicline_more[/dicline_more]" id="stat_{id}">
	<div class="td wIds"><span class="thShort">Store: </span>{object}</div>
	<div class="td"><span class="thShort">Type: </span>{type}[close][not-close]<br>{cr-type}[/close]</div>
	<div class="td wAmount"><span class="thShort">System amouny: </span>[show]{currency} {system}[not-show] --- [/show] [close][not-close]<span class="creditArea">[show]<br>$ {cr-system}[not-show] --- [/show]</span>[/close]</div>
	<div class="td wAmount"><span class="thShort">User amount: </span>[show]{currency} {amount} [lack]<span class="[min]minLack[not-min]plusLack[/min]">{lack}</span>[/lack][not-show] --- [/show] [close][not-close]<span class="creditArea">[show]<br>$ {cr-amount} [cr-lack]<span class="[cr-min]minLack[not-cr-min]plusLack[/cr-min]">{cr-lack}</span>[/cr-lack][not-show] --- [/show]</span>[/close]</div>
	<div class="td wAmount"><span class="thShort">Drawer amount: </span>[show]{currency} {drawer}[not-show] --- [/show][close][not-close]<br>---[/close]</div>
	<div class="td"><span class="thShort">Action: </span>{action}</div>
	<div class="td"><span class="thShort">Date: </span>{date}</div>
	<div class="td"><span class="thShort">Staff: </span>
		<a href="/users/view/{user-id}" target="_blank">{user-name} {user-lastname}</a>
	</div>
	<div class="td" style="width: 30px">
		[close]<a href="/invoices/history?type={type}&date={ddate}&object={object_id}&object_name={object}" onclick="Page.get(this.href); return false;"><span class="fa fa-money"></span></a>[/close]
		[dicline]<span class="fa fa-exclamation-circle diclineAdmin" onclick="cash.adminAccept({id})"></span>[/dicline]
	</div>
</div>