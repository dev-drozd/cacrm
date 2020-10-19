<tr class="tr[dicline] dicline[/dicline][dicline_more] dicline_more[/dicline_more]" id="stat_{id}" data-credit="{cr-data}">
	<td class="td wIds" data-label="Store:">{object}</td>
	<td class="td hideMob" data-label="Type:">{type}[close][not-close]<br>{cr-type}[/close]</td>
	<td class="td wAmount" data-label="System Amount:"><span class="thShort">{lang=SystemCash}: </span>[show]{currency} {system}[not-show] --- [/show] [close][not-close]<span class="creditArea">[show]<br><span class="thShort">{lang=SystemCredit}: </span>{currency} {cr-system}[not-show] --- [/show]</span>[/close]</td>
	<td class="td wAmount" data-label="User Amount:">[show]<span class="thShort">{lang=UserAmount}: </span><span>{currency} {amount} </span>[lack]<span class="[min]minLack[not-min]plusLack[/min]"[owner] ondblclick="cash.updateInfo({id}, this, 'cash', '{action}', 'lack', {id});"[/owner]>{lack}</span>[/lack][not-show] --- [/show] 
							[close][not-close]<span class="creditArea">[show]<br><span class="thShort">{lang=UserCredit}: </span><span>{currency} {cr-amount}</span> [cr-lack]<span class="[cr-min]minLack[not-cr-min]plusLack[/cr-min]"[owner] ondblclick="cash.updateInfo({cr_id}, this, 'credit', '{action}', 'lack', {id});"[/owner]>{cr-lack}</span>[/cr-lack][not-show] --- [/show]</span>[/close]</td>
	<td class="td wAmount" data-label="Drawer Amount:"><span class="thShort">{lang=DrawerCash}: </span>[show]<span[owner] ondblclick="cash.updateInfo({id}, this,  'cash', '{action}', 'amount', {id});"[/owner]>{currency} {drawer}</span>[not-show] --- [/show][close][not-close]<br>---[/close]</td>
	[owner]<td class="td" data-label="Drop:"><span class="thShort">{lang=DropCash}: </span>[close]<span ondblclick="cash.updateInfo({id}, this, 'cash', '{action}', 'out_cash', {id});">{drop}</span>[not-close]---[/close]</td>[/owner]
	<td class="td" data-label="Action:"><span class="thShort">{lang=Action}: </span>{action}</td>
	<td class="td" data-label="Date:"><span class="thShort">{lang=Date}: </span>{date}</td>
	<td class="td" data-label="Staff:">
		<span class="thShort">{lang=Staff}: </span><a href="/users/view/{user-id}" target="_blank">{user-name} {user-lastname}</a>
	</td>
	<td class="td" data-label="Opts:" style="width: 119px;">
		<a href="/im/{user-id}?text=Cash;{id}" class="mesBtn">
			<span class="fa fa-exclamation-circle" style="color: #ca1313;font-size: 16px;" aria-hidden="true"></span>
		</a>
		[close]
			<a href="/invoices/history?type={type}&date={ddate}&object={object_id}&object_name={object}&from_cash={amount},{lack},{system}" target="_blank" class="hnt hntTop" data-title="Invoices">
				<span class="fa fa-money"></span>
			</a>
		[not-close]
			[credit]<a href="/invoices/history?type=-check&date={credit-date}&object={object_id}&object_name={object}&from_cash={cr-amount},{cr-lack}" target="_blank" class="hnt hntTop" data-title="Invoices">
				<span class="fa fa-credit-card"></span>
			</a>[/credit]
		[/close]
		<a href="javascript:cash.comments({id})">
			<span class="fa fa-comment cashCom"></span>
		</a>
		[close][not-close][credit_check]<span class="fa fa-image cashCom" onclick="showPhoto('/uploads/images/cash/{cr_id}/{credit_check}');"></span>[/credit_check][/close]
	</td>
</tr>