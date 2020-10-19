<div class="ctnr">

	<nav class="breadcrumbs">
		<a href="/" onclick="Page.get(this.href); return false;">Main</a> >
		<a href="/category" onclick="Page.get(this.href); return false;">Store</a> >
		[catname]<a href="/category/{store-category-id}" onclick="Page.get(this.href); return false;">{store-category}</a> > [/catname]
		<b>{name}</b>
	</nav>
	
	<div class="page">
		[edit]
		<div align="right">
			<a align="right" href="https://crm.yoursite.com/inventory/edit/{id}">Edit this inventory</a>
		</div>
		[/edit]
		<div class="category flex">
			<div class="products-categories">
				<ul>
					{categories}
				</ul>
			</div>
			<div class="product">
				<h1>{name}</h1>
				<div class="flex">
					<div class="pr-photos">
						<div class="main-photo">
						[img]
							<img src="/uploads/images/inventory/{id}/preview_{img}" onclick="showPhoto.show(this.src, 'iph');" id="iph">
						[not-img]
							<span class="fa fa-picture-o"></span>
						[/img]
						</div>
						[img]
						<div class="add-photos">
							{images}
						</div>
						[/img]
					</div>
					<div class="pr-info">
						<div class="pr-price">${price} <button class="btn" onclick="cart.add({id}, '{name}', '{price}');">Add to cart</button></div>
						<div class="pr-options">
							[model]<div><span>Model:</span> model</div>[/model]
							[os_name]<div><span>Model:</span> 345</div>[/os_name]
							{options}
						</div>
						<div class="pr-descr">
							{descr}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="stores-near">
		<div class="sn-title">Store near you</div>
		<div class="sn-search flex">
			<input name="search" placeholder="Enter your zip or address" onkeyup="if (event.keyCode == 13) nearStore(this.value);">
			<span class="fa fa-search" onclick="nearStore($(this).prev().val());"></span>
		</div>
	</div>
</div>

<div id="map"></div>