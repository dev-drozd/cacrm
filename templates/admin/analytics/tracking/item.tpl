<div class="tr">
	<div class="td">
		<a href="/[stock]inventory/view[not-stock]purchases/edit[/stock]/{id}" onclick="Page.get(this.href); return false;">{name}</a>
	</div>
	<div class="td">
		{type}
	</div>
	<div class="td">
		[customer]
			<a href="/users/view/{c_id}" onclick="Page.get(this.href); return false;">{c_name} {c_lastname}</a>
		[not-customer]
			{store}
		[/customer]
	</div>
	<div class="td">
		<b>Purchase: </b> {purchase_price}<br>
		<b>Sale: </b> {price}<br>
		<b>Income: </b> {income}
	</div>
	<div class="td">
		<ul>
			<li>{date_create} - Created by {cr_staff} in 
				[stock]
					{object_owner}
				[not-stock]
					{store}
					[issue] 
						for <a href="/issues/view/{issue}" onclick="Page.get(this.href); return false;">Issue #{issue}</a>
					[/issue]
				[/stock]
			</li>
			{history}
		</ul>
	</div>
</div>