<div class="sUser" id="payment_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, store.sendPaymentPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href='javascript:store.openPayment({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:store.openPayment({id}, "{name}");'>
					<span class="fa fa-pencil"></span> {lang=editPayment}
				</a>
			</li>
			<li>
				<a href="javascript:store.delPayment({id})">
					<span class="fa fa-times"></span> {lang=delPayment}
				</a>
			</li>
		</ul>
	</div>
</div>