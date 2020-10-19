<div class="tr dev" id= "inventory_{id}" [has_issue]onclick="issues.toggleList(this, event);"[/has_issue][trade-in] style="background: #ff000014;"[/trade-in]>
	<div class="td w10">
		<span class="fa [has_issue]fa-chevron-right[not-has_issue]fa-circle[/has_issue] isOpen"></span> <strong>#{id}</strong>
	</div>
	<div class="td">
		<span class="thShort">Type: </span>{type}
	</div>
	<div class="td">
		<span class="thShort">Category: </span>{category}
	</div>
	<div class="td">
		<span class="thShort">Model: </span>{model}
	</div>
	<div class="td">
		<span class="thShort">OS: </span>{os}
	</div>
	<div class="td">
		<span class="thShort">Location: </span>{location}
	</div>
	<div class="td w100">
	[trade-in]
		<a href="/pdf/dbarcode/{id}" target="_blank" class="hnt hntTop" data-title="Barcode"><span class="fa fa-barcode"></span></a>
		<a href="/inventory/view/{id}[user]?backusr={user-id}[/user]" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="View device"><span class="fa fa-eye"></span></a>
		<a href="/invoices/view/{invoice-id}" target="_blank" class="hnt hntTop" data-title="View invoice"><span class="fa fa-share"></span> Trade in</a>
	[not-trade-in]
		<a href="/pdf/dbarcode/{id}" target="_blank" class="hnt hntTop" data-title="Barcode"><span class="fa fa-barcode"></span></a>
		<a href="/inventory/view/{id}[user]?backusr={user-id}[/user]" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="View device"><span class="fa fa-eye"></span></a>
		<a href="/inventory/edit/{id}[user]?backusr={user-id}[/user]" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Edit device"><span class="fa fa-pencil"></span></a>
		<!--<a href="#" class="hnt hntTop" onclick="issues.addIssue(0, '{user-name} {user-lastname}', '{category} {model}'); return false;" data-title="Add issue"><span class="fa fa-plus"></span></a>-->
		<a href="/issues/add/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Work order"><span class="fa fa-plus"></span></a>
		<a href="#" class="hnt hntTop" onclick="inventory.del({id}); return false;" data-title="Delete device"><span class="fa fa-times"></span></a>
	[/trade-in]
	</div>
</div>
<div class="tr issues">
	[has_issue]
	<div class="issue head">
		<div class="iId">
			id
		</div>
		<div class="isDate">
			Date
		</div>
		<div class="is">
			Description
		</div>
		<div class="iAuthor">
			Staff
		</div>
	</div>
	{issues}
	[/has_issue]
</div>