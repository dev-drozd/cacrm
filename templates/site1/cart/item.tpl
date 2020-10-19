<div class="cItem" id="cart_{id}">
    <div class="ciPhoto">
        [image]
            <img src="/uploads/images/inventory/{id}/preview_{image}">
        [not-image]
            <span class="fa fa-picture-o"></span>
        [/image]
    </div>
    <div class="ciName">
        <a href="/item/{id}" target="_blank">{name}</a>
    </div>
    <div class="ciQnt">
        1
    </div>
    <div class="ciPrice">
        {currency}{price}
    </div>
    <div class="ciDel">
        <span class="fa fa-times" onclick="cart.del({id}, {price});"></span>
    </div>
</div>