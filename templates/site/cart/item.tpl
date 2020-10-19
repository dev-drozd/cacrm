<div class="cart-product" id="cart_{id}">
	<div class="cp-img">
		[image]
            <img src="/uploads/images/inventory/{id}/preview_{image}">
        [not-image]
            <span class="fa fa-picture-o"></span>
        [/image]
	</div>
	<div class="cp-name">
		<a href="/item/{id}" target="_blank">{name}</a>
	</div>
	<div class="cp-descr">
		{descr}
	</div>
	<div class="cp-price">
		{currency}{price}
	</div>
	<div class="cp-remove">
			<a href="javascript:cart.del({id}, {price});">Remove</a>
	</div>
</div>