<div class="sUser" id="delivery_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, store.sendDeliveryPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href='javascript:store.openDelivery({id}, "{name}", {price}, "{currency}");'>
		{name}
		<br><i>{currency-symbol}{price}</i>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:store.openDelivery({id}, "{name}", {price}, "{currency}");'>
					<span class="fa fa-pencil"></span> {lang=editDelivery}
				</a>
			</li>
			<li>
				<a href="javascript:store.delDelivery({id})">
					<span class="fa fa-times"></span> {lang=delDelivery}
				</a>
			</li>
		</ul>
	</div>
</div>