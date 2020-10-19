{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Order #{id} 
		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul>
				<li><a href="javascript:store.delOrder({id});"><span class="fa fa-times"></span> Del order</a></li>
				<li><a href="/invoices/view/{invoice}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> View invoice</a></li>
			</ul>
		</div>
	</div>
	<div class="uForm">
		<div class="uTitle dClear">
			<div class="uName wid50">
				<div>
					Customer: 
					<p><a href="/users/view/{uid}" onclick="Page.get(this.href); return false;">{uname}</a></p>
					<p>{uphone}</p>
					<p>{uaddress}</p>
					<p class="daddress"><span>Delivery address:</span> {daddress}</p>
				</div>
				<div class="aRight">
					{date}
					<p class="line-info"><b>Payment method</b>: {payment}</p>
					<p class="line-info"><b>Delivery method</b>: {delivery}</p>
					<p class="line-info status"><b>Status</b>: {status} <a href="javascript:store.updateOrderStatus({id}, {status-id})"><span class="fa fa-check"></span> Update status</a></p>
				</div>
			</div>
			[note]
			<div class="order-note">
				{note}
			</div>
			[/note]
		</div>
		<div class="tbl payInfo">
			<div class="tr">
				<div class="th">
					Item
				</div>
				<div class="th w100">
					Amount
				</div>
				<div class="th w10">
					Tax
				</div>
			</div>
			{products}
			{delivery-line}
		</div>
			
		<div class="dClear dClear_paid">
			<div class="tbl payTotalInfo">
				<div class="tr">
					<div class="td aRight invInfo">
						Subtotal
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="subtotal">{subtotal}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Tax
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="tax">{tax}</span>
					</div>
				</div>
				<div class="tr">
					<div class="td aRight invInfo">
						Total
					</div>
					<div class="td tAmount">
						<span class="currency">{currency}</span><span id="total">{total}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>