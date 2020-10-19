<div class="sUser" id="inventory_{id}">
	<a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name} {category} {model}
			<p>{descr}</p>
		</div>
	</a>
	<div class="pInfo">
		<span>{price} $</span>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/inventory/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewInventory}</a></li>		
			<li><a href="/inventory/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editInventory}</a></li>
			<li><a href="javascript:inventory.del({id})"><span class="fa fa-times"></span> {lang=delInventory}</a></li>
		</ul>
	</div>
</div>