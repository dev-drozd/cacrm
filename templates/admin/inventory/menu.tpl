<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=Manage}  <a href="/inventory/types" onclick="Page.get(this.href); return false;"><span class="stng fa fa-cog"></span></a></div>
	<ul class="mng">
		<li class="dd">
			<a href="#" onclick="Page.get(this.href); return false;"><span class="fa fa-laptop" style="color: #777;"></span>{lang=add}</a>
			<ul>
				[add-iservice]<li><a href="/inventory/add/service" onclick="Page.get(this.href); return false;">{lang=Service}</a></li>[/add-iservice]
				<li><a href="/inventory/add/inventory" onclick="Page.get(this.href); return false;">{lang=Inventory}</a></li>
				[add-service]<li><a href="/inventory/onsite/add/" onclick="Page.get(this.href); return false;">{lang=OnSiteService}</a></li>[/add-service]
			</ul>
		</li>
		<li class="dd">
			<a href="/inventory/" onclick="Page.get(this.href); return false;"><span class="fa fa-desktop" style="color: #A2CE4E;"></span>{lang=AllSS}</a>
			<ul>
				<li><a href="/inventory/service" onclick="Page.get(this.href); return false;">{lang=Service}</a></li>
				<li><a href="/inventory/stock" onclick="Page.get(this.href); return false;">{lang=Inventory}</a></li>
				<li><a href="/inventory/upcharge" onclick="Page.get(this.href); return false;">{lang=UpchargeServices}</a></li>
			</ul>
		</li>
		<li>
			<a href="/inventory/tradein" onclick="Page.get(this.href); return false;"><span class="fa fa-tablet" style="color: #4ec0ce;"></span>{lang=TradeinConfirmation}</a>
		</li>
		<li class="dd">
			<a href="/inventory/transfer" onclick="Page.get(this.href); return false;"><span class="fa fa-exchange" style="color: #2196f3; font-size: 24px"></span>{lang=InventoryTransfers}</a>
			<ul>
				<li><a href="/inventory/transfer" onclick="Page.get(this.href); return false;">{lang=AllTransfers}</a></li>
				[create]<li><a href="/inventory/transfer/add" onclick="Page.get(this.href); return false;">{lang=NewTransfer}</a></li>
				<li><a href="/inventory/transfer/request" onclick="Page.get(this.href); return false;">{lang=TransferRequest}</a></li>[/create]
			</ul>
		</li>
		<li>
			<a href="/inventory/onsite" onclick="Page.get(this.href); return false;"><span class="fa fa-car" style="color: #ff9756; font-size: 24px"></span>{lang=OnSiteServices}</a>
		</li>
		<li>
			<a href="/inventory/requested" onclick="Page.get(this.href); return false;"><span class="fa fa-exclamation-circle" style="color: #dc3232; font-size: 24px"></span>{lang=allRequests}</a>
		</li>
		<li>
			<a href="/inventory/deleted" onclick="Page.get(this.href); return false;"><span class="fa fa-times" style="color: #777; font-size: 24px"></span>{lang=Deleted}</a>
		</li>
	</ul>
</aside>