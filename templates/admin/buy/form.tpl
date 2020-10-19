{include="buy/menu.tpl"}
<section class="mngContent">
	[can_confirm_cond][arrows]<div class="arrows">
		[prev]<span class="fa fa-arrow-left" onclick="Page.get('/purchases/edit/{prev}');"></span>[/prev]
		[next]<span class="fa fa-arrow-right right" onclick="Page.get('/purchases/edit/{next}');"></span>[/next]
	</div>[/arrows][/can_confirm_cond]
	<div class="sTitle">
	<span class="fa fa-chevron-right"></span>{title}
	[confirmed]
		[del][not-del][received][not-received][edit]<a href="javascript:purchases.[in-store]reciveStock[not-in-store]receiveMdl[/in-store]({id});" class="btn addBtn">Receive</a>[/edit][/received][/del]
	[not-confirmed]
		[can_confirm][del][not-del][edit][can_confirm_cond]<a href="javascript:purchases.confirm({id});" class="btn addBtn">{lang=Confirm}</a>[/can_confirm_cond][/edit][/del][/can_confirm]
	[/confirmed]
	[edit]
		[del][not-del]
		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul>
				[delete]<li><a href="javascript:purchases.confirmDel({id}[confirm], 1[/confirm])"><span class="fa fa-times"></span> {lang=delPurchase}</a></li>[/delete]
				[confirm][rma]
					[can_confirm][rma_close][not-rma_close]<li><a href="javascript:purchases.rma({id}, 'pickup')"><span class="fa fa-car"></span> Pick up</a></li>[/rma_close][/can_confirm]
					[can_confirm]<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>[/can_confirm]
				[not-rma]
					[rma_request]
						[can_confirm]<li><a href="javascript:purchases.rma({id}, 'confirm')"><span class="fa fa-check"></span> Confirm returm</a></li>[/can_confirm]
						[can_confirm]<li><a href="javascript:purchases.rmaRestore({id})"><span class="fa fa-times"></span> Cancel returning</a></li>[/can_confirm]
					[not-rma_request]
						<li><a href="javascript:purchases.confirmRma({id})"><span class="fa fa-exclamation"></span> Return request</a></li>
					[/rma_request]
				[/rma][/confirm]
			</ul>
		</div>[/del][/edit]
		[edit]<a href="/im/{create-id}?text=[comments]RMA[not-comments]Purchase[/comments];{id}" onclick="Page.get(this.href); return false;" class="mesBtn"><span class="fa fa-exclamation-circle" aria-hidden="true"></span></a>[/edit]
	</div>
	
	[backusr]
		<div class="breads">
			<a href="/users/view/{backusr}" onclick="Page.get(this.href); return false;"><span class="fa fa-arrow-left"></span> Back to user</a>
		</div>
	[/backusr]
	[del]
		<div class="mt dClear">
			Purchase deleted
		</div>
	[/del]
	[can_confirm]
		[can_confirm_cond]
		[not-can_confirm_cond]
<!-- 			<div class="mt dClear">
				[purchase_done]
					Purchase's issue has no statuses 'Do it' or 'Waiting parts'
				[not-purchase_done]
					You can not confirm purchase without deposit
				[/purchase_done]
			</div> -->
			[purchase_done][not-purchase_done]
				<div class="mt dClear">You can not confirm purchase without deposit</div>
			[/purchase_done]
		[/can_confirm_cond]
	[/can_confirm]
	[edit]
		[issue_error]
			<div class="mt dClear">
				This purchase is dublicate with {issue}for <a href="/issues/view/{issue-id}">issue #{issue-id}({issue-status})</a>
			</div>
		[/issue_error]
	[/edit]
	<form class="uForm" method="post" onsubmit="purchases.send(this, event, {id});">
		<!--<div class="tabs">
			<div class="tab" id="getCustomer" data-title="Customer">-->
			[add]
				<div class="iGroup sfGroup" id="customer">
					<label>{lang=Customer}</label>
					<!-- <div id="customer_id" name="contact" json='/users/all?gId=5' res="list" search="ajax10" class="sfWrap"></div> -->
					<input type="hidden" name="customer" />
					<ul class="hdn"></ul>
				</div>
			[not-add]
				[customer-id]
				<div class="iGroup sfGroup">
					<label>{lang=Customer}</label>
					<div class="isId">
						<a href="/users/view/{customer_id}" onclick="Page.get(this.href); return false;">{customer-name}</a>
					</div>
				</div>
				[/customer-id]
			[/add]
			<!--</div>
			<div class="tab" id="getIssue" data-title="Issue">
				<div class="iGroup sfGroup" id="issue">
					<label>issuse selection</label>
				</div>
			</div>
		</div>-->
		<div class="iGroup sfGroup" id="object">
			<label>{lang=Object}</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		[issue]
		<div class="iGroup sfGroup">
			<label>{lang=Issue}</label>
			<div class="isId">
				<a href="/issues/view/{issue-id}" onclick="Page.get(this.href); return false;">View issue #{issue-id} ({issue-status})</a>
			</div>
		</div>
		[/issue]
		[add]
		[not-add]
			[invoice]
			<div class="iGroup sfGroup">
				<label>Invoice</label>
				<div class="isId">
					<a href="/invoices/view/{invoice}" onclick="Page.get(this.href); return false;">Invoice #{invoice}</a>
					[quantity]<br>Left: {left}[/quantity]
				</div>
			</div>
			[not-invoice]
				[in-store]
					<div class="iGroup sfGroup">
						<label>Type</label>
						<div class="isId">
							In stock
						</div>
					</div>
				[/in-store]
			[/invoice]
		[/add]
		[create]
		<div class="iGroup sfGroup">
			<label>{lang=Created}</label>
			<div class="uBlock">
				<a href="/users/view/{create-id}" onclick="Page.get(this.href); return false;">[create-image]<img src="/uploads/images/users/{create-id}/thumb_{create-image}" class="miniRound">[not-create-image]<span class="fa fa-user-secret miniRound"></span>[/create-image] {create-name} {create-lastname}</a>
				<div>{create-date}</div>
			</div>
		</div>
		[/create]
		[confirm]
		<div class="iGroup sfGroup">
			<label>{lang=Confirmed}</label>
			<div class="uBlock">
				<a href="/users/view/{confirm-id}" onclick="Page.get(this.href); return false;">[confirm-image]<img src="/uploads/images/users/{confirm-id}/thumb_{confirm-image}" class="miniRound">[not-confirm-image]<span class="fa fa-user-secret miniRound"></span>[/confirm-image] {confirm-name} {confirm-lastname}</a>
				<div>{confirm-date}</div>
			</div>
		</div>
		[/confirm]
		[edited]
		<div class="iGroup sfGroup">
			<label>{lang=LastEdited}</label>
			<div class="uBlock">
				<a href="/users/view/{edited-id}" onclick="Page.get(this.href); return false;">[edited-image]<img src="/uploads/images/users/{edited-id}/thumb_{edited-image}" class="miniRound">[not-edited-image]<span class="fa fa-user-secret miniRound"></span>[/edited-image] {edited-name} {edited-lastname}</a>
				<div>{edited-date}</div>
			</div>
		</div>
		[/edited]
		
		
		<div class="iGroup">
			<label>{lang=Link}</label>
			<input type="text" name="link" value="{link}" [edit][confirm]readonly onclick="window.open(this.value,'_blank');"[not-confirm]ondblclick="window.open(this.value,'_blank');" oninput="purchases.getLink(this);"[/confirm][not-edit] oninput="purchases.getLink(this);"[/edit]>
		</div>
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup">
			<label>Sale name</label>
			<input type="text" name="salename" value="{sale-name}">
		</div>
		<div class="iGroup curGroup">
			<label>{lang=Price}</label>
			<div class="cur">
				<select name="purchase_currency" onchange="purchases.changePrice();">
					{purchase-currency}
				</select>
			</div>
			<input type="number" step="0.001" name="price" value="{price}" onchange="purchases.changePrice();" onkeyup="purchases.changePrice();">
		</div>
		<div class="iGroup">
			<label>Shipment cost:</label>
			<input type="number" step="0.001" min="0" name="shipment_cost" value="{shipment-cost}" oninput="purchases.addShipment(this.value)">
		</div>
		<div class="iGroup">
			<label>{lang=EstimatedDate}</label>
			<input type="text" name="estimated" value="{estimated}">
		</div>
		<div class="iGroup"[in-store][not-in-store] style="display: none;"[/in-store] id="quantity">
			<label>{lang=Quantity}</label>
			<input type="number" name="quantity" value="{quantity}" min="1" onchange="purchases.changePrice();" onkeyup="purchases.changePrice();">
		</div>
		<div class="iGroup">
			<label>{lang=Total}</label>
			<input type="number" name="total" value="{total}" readonly step="0.001">
		</div>
		<div class="iGroup curGroup">
			<label>{lang=SalePrice}</label>
			<div class="cur">
				<select name="currency" onchange="purchases.changePrice();">
					{currency}
				</select>
			</div>
			<input type="number" name="sale" value="{sale}" onchange="purchases.changePrice();" onkeyup="purchases.changePrice();" onblur="purchases.minPrice(this);" step="0.001" required="required">
		</div>
		<div class="iGroup">
			<label>{lang=Proceeds}</label>
			<input type="number" name="proceeds" value="{proceeds}" readonly step="0.001">
		</div>
		<div class="iGroup imgGroup [photo][not-photo]hdn[/photo]">
			<label>{lang=Photo}</label>
			<figure>
				<img src="[photo]/uploads/images/{id}/thumb_{photo}[/photo]" onclick="showPhoto(this.src);" />
				<span class="fa fa-times" onclick="$(this).prev().attr('src', '').parents('.imgGroup').addClass('hdn');"></span>
			</figure>
		</div>
		[del]
		[not-del]
		<div class="iGroup">
			<label>{lang=itemID}</label>
			<input type="text" name="tracking" value="{tracking}" [edit]readonly[/edit]>
		</div>
		[edit]
		<div class="iGroup">
			<label>{lang=trakingNumber}</label>
			<input type="text" name="ship-tracking" value="{ship-tracking}">
		</div>
		[/edit]
		[/del]
		<div class="iGroup">
			<label>{lang=Comment}</label>
			<textarea name="comment">{comment}</textarea>
		</div>
		[edit-btn]
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
		[/edit-btn]
	</form>
</section>

<script>
	$(function() {
	//$('div[json]').json_list();
		$('input[name="customer"]').data({customer-id});
		$('input[name="object"]').data({object-id});
		
		[add]
		$.post('/users/all', {gId: 5, nIds: Object.keys($('input[name="customer"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#customer > ul').html(items).sForm({
					action: '/users/all',
					data: {
						gId: 5,
						lId: lId,
						nIds: Object.keys($('input[name="customer"]').data()).join(','),
						query: $('#customer > .sfWrap input').val()
					},
					all: false,
					select: $('input[name="customer"]').data(),
					s: true,
					link: 'users/view'
				}, $('input[name="customer"]')[just-customer], function() {
					if (Object.keys($('input[name="customer"]').data())[0])
						$('#quantity').hide();
					else
						$('#quantity').show();
				}[/just-customer]);
			}
		}, 'json');
		[/add]
		
		$.post('/invoices/objects', {}, function (r) {
			if(r.list.length > 1) {
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#object > ul').html(items).sForm({
					action: '/invoices/objects',
					data: {
						lId: lId,
						query: $('#object > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="object"]').data(),
					s: true,
					link: 'objects/edit'
				}, $('input[name="object"]'));
			} else if (r.list.length == 1) {
				var cash_object = {};
				cash_object[r.list[0].id] = {
					name: r.list[0].name
				}
				$('input[name="object"]').data(cash_object);
				$('#object').append($('<div/>', {
					class: 'storeOne',
					html: r.list[0].name
				}));
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to stores or trying to enter not from the store',
					delay: 3
				});
				Page.get('/purchases');
			} 
		}, 'json');
		
	});
</script>
<script src="{theme}/new-js/purchases.js"></script>