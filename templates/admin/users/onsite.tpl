<div class="tr [del]deleted[/del]" id="onsite_{id}" data-calls="{has-calls}" data-time="{has-time}">
	<div class="td[hnt] hnt hntTop" data-title="{hnt}[/hnt]"><span class="thShort">Name: </span>{name}</div>
	<div class="td"><span class="thShort">Start date: </span>{date_start}</div>
	<div class="td dateEnd"><span class="thShort">End date: </span>{date_end}</div>
	<div class="td"><span class="thShort">Calls: </span><span id="calls_onsite_{id}">{calls}</span></div>
	<div class="td"><span class="thShort">Left time: </span><span id="time_onsite_{id}">{time_left}</span></div>
	<div class="td os_right">
		[del]
		[not-del]
			[use]
				[play]<a href="javascript:user.updateOnsiteNote({id}, 'pause', {onsite_id});" class="hnt hntTop" data-title="Pause"><span class="fa fa-pause osIcon"></span></a>
				[not-play]<a href="javascript:user.playOnsite({id}, 'play', {onsite_id});" class="hnt hntTop" data-title="Play"><span class="fa fa-play osIcon"></span></a>[/play]
				<a href="javascript:user.updateOnsiteNote({id}, 'stop', {onsite_id});" class="hnt hntTop" data-title="Stop"><span class="fa fa-stop osIcon"></span></a>
			[not-use]
				<span class="fa fa-check complited">Complited</span>
			[/use]
		[/del]
        <div class="uMore">
            [del]
            [not-del]
			<span class="fa fa-ellipsis-v" onclick="$(this).next().toggle(0);"></span>
            <ul>
                [invoice-id]<li><a href="/invoices/view/{invoice-id}" onclick="Page.get(this.href); return false;">View invoice</a></li>[/invoice-id]
                [use]
					<li><a href="javascript:user.updateOnsiteStaff({id});">Update assign to</a></li>
					<li><a href="javascript:user.editOnsite({id});">Edit</a></li>
				[not-use][cr_invoice]
                    [invoice]
                        <li><a href="/invoices/view/{add_invoice}" onclick="Page.get(this.href); return false;">Additional invoice</a></li>
                    [not-invoice]
                        <li><a href="javascript:user.sendOnsiteInvoice({id});">Create add. invoice</a></li>
                    [/invoice]
                [/cr_invoice][/use]
				<li><a href="javascript:user.onsiteStats({id});">Statistic</a></li>
				<li><a href="javascript:user.delOnsite({id});">Delete</a></li>
            </ul>
			[/del]
        </div>
	</div>
</div>