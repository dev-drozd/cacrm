{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> {lang=TransferInventories}
	</div>
	<form class="uForm" method="post" onsubmit="inventory.transfer(this, event);">
		<div class="iGroup">
			<div class="w50 sfGroup" id="from_object">
				<label>{lang=FromStore}</label>
				[view]
					<div class="storeOne">{from_store}</div>
				[not-view]
					<input type="hidden" name="from_object">
					<ul class="hdn"></ul>
				[/view]
			</div>
			<div class="w50 sfGroup" id="to_object">
				<label>{lang=ToStore}</label>
				[view]
					<div class="storeOne">{to_store}</div>
				[not-view]
					<input type="hidden" name="to_object">
					<ul class="hdn"></ul>
				[/view]
			</div>
		</div>
		<div class="iGroup">
			<div class="w50 sfGroup">
				<label>{lang=ResponsibleForSending}</label>
				<div class="storeOne">{send_staff}</div>
			</div>
			<div class="w50 sfGroup" id="receive_staff">
				<label>{lang=ResponsibleForReceiving}</label>
				[view]
					<div class="storeOne">{receive_staff}</div>
				[not-view]
					<input type="hidden" name="receive_staff">
					<ul class="hdn"></ul>
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
		<div class="sGroup">
			<button type="submit" class="btn btnSubmit">{lang=Send}</button>
		</div>
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
						html: lang[172]
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