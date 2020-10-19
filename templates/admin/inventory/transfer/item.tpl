<div class="sUser transferItem {confirmed} {deleted} {requested}" id="transfer_{id}">
	<a class="sName" href="/inventory/transfer/view/{id}" onclick="Page.get(this.href); return false;">
		From {from_store}<br>
		Created: {send_staff}
	</a>
	<a class="sName" href="/inventory/transfer/view/{id}" onclick="Page.get(this.href); return false;">
		To {to_store}<br>
		Confirm: {receive_staff}
	</a>
	<div class="uMore">
	[del]
	[not-del]
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href="/inventory/transfer/view/{id}" onclick="Page.get(this.href); return false;">
					<span class="fa fa-eye"></span> View transfer
				</a>
			</li>
			[can_confirm]
				[requested]
					<li>
						<a href="javascript:inventory.confirmTransferRequest({id}, this);">
							<span class="fa fa-check"></span> Confirm transfer request
						</a>
					</li>
				[not-requested]
					[time_confirm]
						<li id="confirm_tr_{id}">
							<a href="javascript:inventory.confirmTransferMdl({id});">
								<span class="fa fa-check"></span> Confirm transfer 
							</a>
						</li>
					[/time_confirm]
				[/requested]
			[/can_confirm]
			[can_del]<li>
				<a href="javascript:inventory.delTransfer({id}, this);">
					<span class="fa fa-times"></span> {lang=del} transfer
				</a>
			</li>[/can_del]
		</ul>
	[/del]
	</div>
</div>