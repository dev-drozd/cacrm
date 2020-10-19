{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Transfer inventories
		[view]
			[show_uMore]
				<div class="uMore">
					<span class="fa fa-ellipsis-v" onclick="$(this).next().toggle(0);"></span>
					<ul>
						[forms]
							<li class="dd">
								<a href="#" onclick="$(this).next().slideToggle('fast');return false;"><span class="fa fa-file-text-o"></span>Forms</a>
								<ul>
									{forms}
								</ul>
							</li>
						[/forms]
						[del]
						[not-del]
							[can_confirm]
								[requested]
									<li>
										<a href="javascript:inventory.confirmTransferRequest({id});">
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
								<a href="javascript:inventory.delTransfer({id});">
									<span class="fa fa-times"></span> {lang=del} transfer
								</a>
							</li>[/can_del]
						[/del]
					</ul>
				</div>
			[/show_uMore]
		[/view]
	</div>
	[del]
		<div class="mt dClear">
			Transfer is deleted
		</div>
	[not-del]
		[view]
			[time_confirm]
			[not-time_confirm]
				<div class="mt dClear">
					You can confirm transfer in {time_confirm} minutes
				</div>
				<script>
					var rTime = setTimeout(function() {
						clearTimeout(rTime);
						Page.get(location.href);
					}, {sec_confirm} * 1000);
				</script>
			[/time_confirm]
		[/view]
	[/del]
	<form class="uForm" method="post" onsubmit="inventory.transfer(this, event[request], 'request'[/request]);">
		<div class="iGroup">
			<div class="w50 sfGroup" id="from_object">
				<label>From store</label>
				[view]
					<div class="storeOne">{from_store}</div>
				[not-view]
					<input type="hidden" name="from_object">
					<ul class="hdn"></ul>
				[/view]
			</div>
			<div class="w50 sfGroup" id="to_object">
				<label>To store</label>
				[view]
					<div class="storeOne">{to_store}</div>
				[not-view]
					<input type="hidden" name="to_object">
					<ul class="hdn"></ul>
				[/view]
			</div>
		</div>
		<div class="iGroup">
			[request]
			[not-request]
			<div class="w50 sfGroup">
				<label>Responsible for sending</label>
				<div class="storeOne">{send_staff}</div>
			</div>
			[/request]
			<div class="w50 sfGroup" id="receive_staff">
				<label>
				[request]
					Requested staff
				[not-request]
					Responsible for receiving
				[/request]
				</label>
				[view]
					<div class="storeOne">{receive_staff}</div>
				[not-view]
					[request]
						<div class="storeOne">{receive_staff}</div>
					[not-request]
						<input type="hidden" name="receive_staff">
						<ul class="hdn"></ul>
					[/request]
				[/view]
			</div>
		</div>
		<div class="iGroup">
			[view]
				{inventory}
			[not-view]
				<div class="transfer"></div>
			[/view]
		</div>
		[view]
			[requested]
			[not-requested]
				[del]
				[not-del]
					[confirmed]
						<div class="trConf green">{receive_staff} confirmed transfer</div>
					[not-confirmed]
						<div class="trConf red">{receive_staff} not yet confirmed transfer</div>
					[/confirmed]
				[/del]
			[/requested]
		[not-view]
			<div class="sGroup">
				<button type="submit" class="btn btnSubmit">Send</button>
			</div>
		[/view]
	</form>
</section>

<script>
	function newItems() {
		$('.transfer').transfer({
			action: '/inventory/all',
			data: {
				type: 'stock',
				lId: 0,
				noCust: 1,
				oId: Object.keys($('input[name="from_object"]').data()).join(',')
			}
		});
	}
	
	function receiveStaff() {
		if ($('#receive_staff > .sfWrap').length) {
			$('#receive_staff > .sfWrap').remove();
			$('#receive_staff').append($('<ul/>', {
				class: 'hdn'
			}));
		}
		
		[request]
		[not-request]
		$.post('/objects/staff', {id: Object.keys($('input[name="to_object"]').data()).join(','), nIds: Object.keys($('input[name="receive_staff"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#receive_staff > ul').html(items).sForm({
					action: '/objects/staff',
					data: {
						id: Object.keys($('input[name="to_object"]').data()).join(','),
						lId: lId,
						query: $('#receive_staff > .sfWrap input').val()
					},
					all: false,
					select: $('input[name="receive_staff"]').data(),
					s: true
				}, $('input[name="receive_staff"]'));
			}
		}, 'json');
		[/request]
	}
	
	$(function() {
		$.post('/invoices/objects', {}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (r.list.length > 1) {
					$('#from_object > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							query: $('#from_object > .sfWrap input').val() || ''
						},
						all: false,
						select: $('input[name="from_object"]').data(),
						s: true,
						link: 'objects/edit'
					}, $('input[name="from_object"]'), newItems);
				} else if (r.list.length == 1) {
					var cash_object = {};
					cash_object[r.list[0].id] = {
						name: r.list[0].name
					}
					$('input[name="from_object"]').data(cash_object);
					$('#from_object').append($('<div/>', {
						class: 'storeOne',
						html: r.list[0].name
					}));
					newItems();
				} else {
					$('#from_object').append($('<div/>', {
						class: 'storeOne',
						html: 'You can not add inventory without being in store'
					}));
				}
			}
		}, 'json');
		
		$.post('/objects/all', {}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#to_object > ul').html(items).sForm({
					action: '/objects/all',
					data: {
						lId: lId,
						query: $('#to_object > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="to_object"]').data(),
					s: true,
					link: 'objects/edit'
				}, $('input[name="to_object"]'), receiveStaff);
			}
		}, 'json');
	});
</script>