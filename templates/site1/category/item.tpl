<div class="igItem">
	<div class="imgs[one] one[/one]">
		<a href="/item/{id}" onclick="Page.get(this.href); return false;">
			[no]
				<span class="fa fa-picture-o"></span>
			[not-no]
				{images}
			[/no]
		</a>
	</div>
	<div class="info">
		<div class="name">
			{name}
		</div>
		<div class="object">
			{object}
		</div>
		<span class="fa fa-shopping-cart" onclick="cart.add({id}, '{name}', '{price}');">Add to cart</span>
		<div class="price">
			${price}
		</div>
	</div>
</div>