{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=viewDevice} 
		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">{lang=Options}</span>
			</span>
			<ul>
				[edit-inventory]<li><a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=editInventory}</a></li>[/edit-inventory]
				<li><a href="/pdf/dbarcode/{id}" target="_blank" ><span class="fa fa-barcode"></span> {lang=Barcode}</a></li>
				[edit-inventory]<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delInventory}</a></li>[/edit-inventory]
			</ul>
		</div>
		<div class="flRight">
			<div class="uCamera hnt hntBottom" data-title="Barcode" onclick="location.href = '/pdf/dbarcode/{id}'"><span class="fa fa-barcode"></span></div>
		</div>
		<a href="javascript:sendMessage.mdl({id}, 'Stock');" class="mesBtn"><span class="fa fa-exclamation-circle" aria-hidden="true"></span></a>
	</div>
	<div class="userInfo">
		<div class="uTitle dClear">
			<div class="dClear">
				<div class="invName">
					{type} {category} {model_name} {model}
				</div>
				<div class="uName flLeft">
					<div class="address">
						<b>{lang=SN}</b>:{serial}<br/>
						<b>{lang=OS}</b>: {os} {version-os}<br/>
						<b>{lang=Charger}</b>: {charger}<br/>
						<b>{lang=Purchase}</b>: {purchase-currency}{purchase-price}<br/>
						<b>{lang=Income}</b>: {currency}{income-price}
						[password]<br/><b>Password</b>: {password}[/password]
					</div>
					<div class="address">
						<b>{lang=Store}</b>: {object}<br/>
						<b>{lang=Status}</b>: {status}<br/>
						<b>{lang=Location}</b>: {location} {sublocation}<br/>
						<b>{lang=Quantity}</b>: {quantity}
					</div>
				</div>
				[not-customer]
				<div class="sPrice">
					<div class="selling">{lang=Selling}</div>
					<span>{price}{currency}</span>
				</div>
				[/not-customer]
			</div>
			<div class="dClear w100p" id="details">
				<ul>
					{options}
				</ul>
			</div>
			[tradein]
			<div class="mt dClear mtInfo">
				<div class="[cn]pw50[/cn]">
					<h3>{lang=TradeinCreated}</h3>
					{lang=Date}: {cr-date}<br>
					{lang=Staff}: <a href="/users/view/{cr-user}" target="_blank">{cr-name}</a><br>
					{lang=Price}: {currency}{cr-price}
				</div>
				[cn]
				<div class="pw50">
					<h3>{lang=TradeinConfirmed}</h3>
					{lang=Date}: {cn-date}<br>
					{lang=Staff}: <a href="/users/view/{cn-user}" target="_blank">{cn-name}</a><br>
					{lang=Price}: {currency}{cn-price}
				</div>
				[/cn]
			</div>
			[/tradein]
		</div>
		[customer]
		<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=owner} <a href="/users/edit/{customer-id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-pencil"></span></a></div>
		<div class="userInfo">
			<div class="uTitle dClear">
				<figure>
					[ava]<div><img src="/uploads/images/users/{customer-id}/thumb_{customer-image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
				</figure>
				<div class="uName">
					<div>
						<a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a> 
						<a href="/users/edit/{customer-id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span></a>
						<p>{customer-phone}</p>
					</div>
					<div class="address">
						{customer-address} [ver]<span class="hnt hntTop" data-title="{customer-ver}"><span class="fa fa-check"></span></span>[/ver]
					</div>
				</div>
			</div>
		</div>
		[not-customer]
		<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=owner} <a href="/objects/edit/{object-id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-pencil"></span></a></div>
		<div class="userInfo">
			<div class="uTitle dClear">
				<figure>
					[object-ava]<div><img src="/uploads/images/stores/{object-id}/thumb_{object-image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-object-ava]<span class="fa fa-user-secret"></span>[/object-ava]
				</figure>
				<div class="uName">
					<div>
						<a href="/objects/edit/{object-id}" onclick="Page.get(this.href); return false;">{object}</a>
						<p>{object-phone}</p>
					</div>
					<div class="address">
						{object-address}
					</div>
				</div>
			</div>
		</div>
		[/customer]
		<div class="sTitle">{lang=Issues} <a href="/issues/add/{id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div><!--onclick="issues.addIssue({id}, '{customer-name} {customer-lastname}', '{category} {model}'); return false;"-->
		<div class="uDetails">
			<div class="tbl tblDev">
				<div class="tr">
					<div class="th w10">
						ID
					</div>
					<div class="th isDate">
						{lang=Date}
					</div>
					<div class="th">
						{lang=Description}
					</div>
					<div class="th">
						{lang=Staff}
					</div>
					<div class="th w100">
						{lang=Event}
					</div>
				</div>
				{issues}
		</div>
		[history]
		<div class="sTitle">{lang=Stats}</div>
		<div class="uDetails">
			<div class="tbl tblDev">
				<div class="tr">
					<div class="th w10">
						{lang=Date}
					</div>
					<div class="th w100">
						{lang=Staff}
					</div>
					<div class="th">
						{lang=Event}
					</div>
				</div>
				{stats}
		</div>
	</div>
	[/history]
	
	[invoices]
		<div class="sTitle">{lang=Invoices}</div>
		<div class="uDetails">
			<div class="tbl tblDev">
				<div class="tr">
					<div class="th w10">
						ID
					</div>
					<div class="th">
						{lang=Date}
					</div>					
					<div class="th">
						{lang=Amount}
					</div>
					<div class="th">
						{lang=Paid}
					</div>
					<div class="th">
						{lang=Due}
					</div>
					<div class="th">
						{lang=Status}
					</div>
					<div class="th w100">
						{lang=Options}
					</div>
				</div>
				{invoices}
			</div>
		</div>
		[/invoices]
	
	[transfers]
		<div class="sTitle">{lang=Transfers}</div>
		<div class="uDetails">
			<div class="tbl tblDev tblTransfers">
				<div class="tr">
					<div class="th">
						{lang=FromStore}
					</div>
					<div class="th">
						{lang=CreateDate}
					</div>
					<div class="th">
						{lang=CreateManager}
					</div>
					<div class="th">
						{lang=ToStore}
					</div>
					<div class="th">
						{lang=Confirm date}
					</div>
					<div class="th">
						{lang=CofirmManager}
					</div>
				</div>
				{transfers}
			</div>
		</div>
	[/transfers]
</section>