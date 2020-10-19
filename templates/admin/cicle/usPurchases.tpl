<div class="tr" id="purchase_{id}">
	<div class="td w10">
		<span class="thShort">ID: </span>{id}
	</div>
	<div class="td">
		<span class="thShort">Name: </span>{name}
	</div>
	<div class="td">
		<span class="thShort">Status: </span>{status}
	</div>
	<div class="td noWrap">
		<span class="thShort">Link: </span>{link}
	</div>
	<div class="td">
		<span class="thShort">Price: </span>{currency}<span class="purPrice">{price}</span>
	</div>
	<div class="td w100">
		<a href="/purchases/edit/{id}?usr={user-id}" onclick="Page.get(this.href); return false;" class="hnt hntTop green" data-title="Edit purchase"><span class="fa fa-pencil"></span></a>
		<a href="javascript:purchases.del({id}, {user-id}, 'usr')" class="hnt hntTop" data-title="Del purchase"><span class="fa fa-times"></span></a>
	</div>
</div>