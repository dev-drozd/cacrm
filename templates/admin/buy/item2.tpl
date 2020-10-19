<tr class="sUser [comments]{confirmed}[/comments][rma_request] returnRequest[/rma_request] {class}" id="purchase_{id}">
	<td data-label="ID:">
		{id}
	</td>
	<td>
		<div class="uThumb">
			[photo]<img src="/uploads/images/{id}/thumb_{photo}" onclick="showPhoto(this.src);">[not-photo]<span class="fa fa-desktop"></span>[/photo]
		</div>
	</td>
	<td data-label="Name:">
		<a href="/purchases/edit/{id}" onclick="Page.get(this.href); return false;">{name}</a>
	</td>
	<td data-label="Date:">
		{date}
	</td>
	<td data-label="Status:">
		<span [comment]class="hnt hntTop" data-title="{comment}"[/comment]>{status}</span>
	</td>
	<td data-label="Price:">
		{purchase-currency}{price}
	</td>
	<td data-label="Quantity:">
		{quantity}
	</td>
	<td data-label="Total:">
		{purchase-currency}{total}
	</td>
	<td>
		[notdel]
		<div class="uMore">
			<span class="fa fa-ellipsis-v" onclick="$(this).next().toggle(0);"></span>
			<ul>
				[edit]
					<li><a href="javascript:tab.send('{name}','/purchases/edit/{id}')"><span class="fa fa-star"></span> Add to tab</a></li>
					<li><a href="/purchases/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editPurchase}</a></li>
				[/edit]
				[delete]<li><a href="javascript:purchases.confirmDel({id})"><span class="fa fa-times"></span> {lang=delPurchase}</a></li>[/delete]
				[confirm]
					[received][not-received]<li><a href="javascript:purchases.[in-store]reciveStock[not-in-store]receiveMdl[/in-store]({id});"><span class="fa fa-archive"></span> Receive</a></li>[/received]
				[rma]
					[can_confirm]
						[rma_close][not-rma_close]<li><a href="javascript:purchases.rma({id}, 'pickup')"><span class="fa fa-car"></span> Pick up</a></li>[/rma_close]
						<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>
					[/can_confirm]
				[not-rma]
					[rma_request]
						[can_confirm]
							<li><a href="javascript:purchases.rmaReadComment({id});"><span class="fa fa-check"></span> Confirm returm</a></li>
							<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>
						[/can_confirm]
					[not-rma_request]
						<li><a href="javascript:purchases.confirmRma({id})"><span class="fa fa-exclamation"></span> Return request</a></li>
					[/rma_request]
				[/rma]
				[/confirm]
			</ul>
		</div>
		[/notdel]
	</td>
</tr>