<section class="mngContent fullw" id="issue" data-id="{id}" data-currency="{currency}" data-customer="{customer-id}" data-object="{object-id}" data-discount="{discount-id}">
	[arrows]<div class="arrows">
		[prev]<span class="fa fa-arrow-left" onclick="Page.get('/issues/view/{prev}');"></span>[/prev]
		[next]<span class="fa fa-arrow-right right" onclick="Page.get('/issues/view/{next}');"></span>[/next]
	</div>[/arrows]
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Issue} #{id} 
		[staff]
		[not-staff]
			<a href="/im/{staff-id}?text=Issue;{id}" onclick="Page.get(this.href); return false;" class="mesBtn"><span class="fa fa-exclamation-circle" aria-hidden="true"></span></a>
		[/staff]
		<div class="uMore">
			<span class="togMore" onclick="$(this).next().show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul>
				<li class="dd">
					<a href="#" onclick="$(this).next().slideToggle('fast');return false;"><span class="fa fa-file-text-o"></span>Forms</a>
					<ul>
						{forms-list}
						<li><a href="/pdf/barcode/{id}" target="_blank">Barcode</a></li>
					</ul>
				</li>
				[invoice]
					<li id="view_invoice"><a href="/invoices/view/{invoice}" onclick="Page.get(this.href);"><span class="fa fa-credit-card"></span> {lang=viewInvoice}</a></li>
				[not-invoice]
					[is-services][create-invoice][confirmed]<li id="create_invoice"><a href="javascript:invoices.issCreate({id}, {customer-id}, {object-id}, {discount-id})"><span class="fa fa-credit-card"></span> {lang=createInvoice}</a></li>[/confirmed][/create-invoice][/is-services]
					<li><a href="/issues/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> {lang=editIssue}</a></li>
				[/invoice]
				[staff][not-staff]<li><a href="javascript:issues.addFeedback({id});"><span class="fa fa-comment"></span> Add Feedback</a></li>[/staff]
				[switch-assigned]<li><a href="javascript:issues.assigned({id});"><span class="fa fa-user"></span> Switch assigned</a></li>[/switch-assigned]
				<li><a href="javascript:issues.transfer({id});"><span class="fa fa-arrow-right"></span> Transfer</a></li>
				<li><a href="javascript:issues.del({id});"><span class="fa fa-times"></span> Delete</a></li>
			</ul>
		</div>
		<div class="flRight">
			[buy]<div class="uCamera hnt hntBottom" data-title="Buy this Device " onclick="issues.buyDevice({id})" style="background: #c56060;"><span class="fa fa-shopping-cart"></span></div>[/buy]
			[invoice][not-invoice]<div class="uCamera hnt hntBottom" data-title="Check out" onclick="invoices.issCreate({id}, {customer-id}, {object-id}, {discount-id})" style="background: #36b1e6;"><span class="fa fa-usd"></span></div>[/invoice]
			<div class="uCamera hnt hntBottom" data-title="Barcode" onclick="location.href = '/pdf/barcode/{id}'"><span class="fa fa-barcode"></span></div>
		</div>
		<!--[invoice][not-invoice]<button class="btn btnStatus" type="button" onclick="issues.updateStatus({id});">Update status</button>[/invoice]-->
	</div>
	<input type="hidden" name="inventory_id" value="{device-id}">
	<input type="hidden" name="issue_id" value="{id}">
	<div class="userInfo">
	[customer]
	<div class="userInfo">
		[unconfirmed_services]<div class="mt dClear">Services {unconfirmed_services} pending for confirmation</div>[/unconfirmed_services]
		[unconfirmed_inventory]<div class="mt dClear">Inventory {unconfirmed_inventory} pending for confirmation</div>[/unconfirmed_inventory]
		[unconfirmed_discount]<div class="mt dClear">Discount {unconfirmed_discount} pending for confirmation</div>[/unconfirmed_discount]
		[show-purchase-message]<div class="mt dClear">You have to get payment for purchased parts before it would send for confirmation to purchasing department</div>[/show-purchase-message]
		[not-valid-phone]<div class="mt dClear">Cusomer has invalid phone number. Please, check it.</div>[/not-valid-phone]

		<div class="uTitle dClear">
			<figure>
				[ava]<div><img src="/uploads/images/users/{customer-id}/thumb_{customer-image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
			</figure>
			<div class="uName">
				<div>
					<p><a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a> <a href="/users/edit/{customer-id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-pencil"></span></a></p>
					<p><b>{customer-phone}</b></p>
					<p>{customer-email}</p>
					<p><i>{country-name} {state-name} {city-name} {customer-address}</i></p>
				</div>
			</div>
		</div>
	</div>
	[not-customer]
	<div class="userInfo">
		<div class="uTitle dClear">
			<figure>
				[object-ava]<div><img src="/uploads/images/stores/{object-id}/thumb_{object-image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-object-ava]<span class="fa fa-user-secret"></span>[/object-ava]
			</figure>
			<div class="uName">
				<div>
					<p>{object} <a href="/objects/edit/{object-id}"  onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-pencil"></span></a></p>
					<p>{object-phone}</p>
				</div>
			</div>
		</div>
	</div>
	[/customer]
		
	<div class="uTitle dClear">
		<div class="uName flLeft">
			<div>
				<div class="inv_info">
					<p><a href="/inventory/view/{device-id}" target="_blank" class="eBtn">{type-name} {category} {model-name} {model}</a> <a href="/inventory/edit/{device-id}" target="_blank" class="eBtn"><span class="fa fa-pencil"></span></a></p>
					<p><b>S/N:</b> {serial}</p>
					<p><b>OS:</b> {os} {version-os}</p>
					<p><b>Charger:</b> {charger}</p>
					<ul class="opts">{options}</ul>
				</div>
			</div>
			<div class="address">
					[pickup-status]
						<p class="devStatus" data-id="0"><b>Status</b>: Picked up</p>
					[not-pickup-status]
						<p class="devStatus" data-id="[confirmed_warranty]{warranty-status-id}[not-confirmed_warranty]{status-id}[/confirmed_warranty]"[confirmed_warranty] data-warranty="(Warranty)"[/confirmed_warranty]><b>Status</b>: [confirmed_warranty]{warranty-status}[not-confirmed_warranty]{status}[/confirmed_warranty]</p>
					[/pickup-status]
					<p class="devObject"><b>Store</b>: {object}</p>
					<p class="devLocation" data-id="{location-id}" data-count="{location-count}" data-object="{object-id}" data-oname="{object}" location_count="{sublocation}"><b>Location</b>: {location}</p>
					<p class="devAssigned">
						<b>Assigned to</b>: <a href="/users/view/{staff-id}" target="_blank">{staff-name} {staff-lastname}</a>
						[switch-assigned]<a href="#" onclick="issues.assigned({id}); return false;" class="warSt"><span class="fa fa-user"></span>Switch assigned</a>[/switch-assigned]
					</p>
					[can_be_warranty]
						[warranty]
							[confirm_warranty]<div class="alr alrInfo alrSMS fw warrantyConf">Warranty status needs confirmation. Please, contact responsible person to confirm it.</div>
							<div class="warranty-reason">{warranty-reason}</div>
							<button type="submit" class="btn btnInvisible" onclick="issues.confirmWarranty({id}, {device-id});">Confirm Warranty</button>[/confirm_warranty]
						[not-warranty]
							<button type="submit" class="btn btnInvisible" onclick="issues.mdlWarranty({id}, {device-id});">Create Warranty</button>
						[/warranty]
					[/can_be_warranty]
					<!--[show][confirmed]<a href="#" onclick="issues.updateStatus({id}, {nconfirmed}); return false;"><span class="fa fa-check" style="color: #A2CE4E;"></span>Update status</a>[/confirmed][/show]-->

					[warranty_request]
					[not-warranty_request]
					[confirmed]<a href="#" onclick="issues.updateStatus({id}, {nconfirmed}); return false;"><span class="fa fa-check" style="color: #A2CE4E;"></span>Update status</a>[/confirmed]
					[/warranty_request]
			</div>
		</div>
		<div class="moreBtn" onclick="hideArea(this)">More</div>
		<div class="dClear"></div>
		<div class="moreArea hdn" id="details">
			<div id="tax" class="hdn">{object-tax}</div>
			<ul>
				<li><b>S/N</b>:{serial}</li>
				<li><b>OS</b>: {os} {version-os}</li>
				<li><b>Object</b>: {object}</li>
				{options}
			</ul>
		</div>
	</div>	
		
	<div class="uTitle dClear issueInfo">
	
		<div class="fl50">
			<div class="tbl tblDev" id="tbl_services">
				<div class="tr">
					<div class="th">
						Name
					</div>
					<div class="th w100">
						Quantity
					</div>
					<div class="th w100">
						Price
					</div>
				</div>
				{miniServices}
				[is-services]
				<div class="tr bt1">
					<div class="th w10">
						Total
					</div>
					<div class="th">
					</div>
					<div class="th w100" id="miniServ_total">
						0
					</div>
				</div>
				[/is-services]
				[invoice-done][not-invoice-done]<a href="javascript:issues.addInv({id}, '{service-ids}', 'service');" class="aServ"><span class="fa fa-plus"></span> Add Service</a>[/invoice-done]
			</div>
		</div>
		<div class="fl50">
			<div class="payPrice">
				<p>Intake Tech: <a href="/users/view/{intake-id}" target="_blank" class="intakeTech">{intake-name} {intake-lastname}</a></p>
				<p>Intake Date: {date}</p>
				<p>Total: <b><span id="totalPrice" data-total="{total}" data-id="{id}">0</span></b></p>
				<!--<p>Quote: <b><span id="quote">0</span></b> <a href="javascript:issues.editQuote({id}, this);">[invoice][not-invoice]<span class="fa fa-pencil" id="quoteBtn"></span>[/invoice]</a></p>-->
				<p>Discount: <b><span id="discount" data-id="{discount-id}" data-name="{discount-name}">{discount}</span>% {discount-name}</b> <a href="javascript:issues.editDiscount({id}, this, {discount-id}, {discount-confirmed});">[invoice-done][not-invoice-done]<span class="fa fa-pencil" id="discountBtn"></span>[/invoice-done]</a> [confirm-discount]<a href="javascript:issues.confirmDiscount({id});" ondblclick="return false;">[invoice-done][not-invoice-done]<span class="fa fa-check" id="discountConfirm"></span>[/invoice-done]</a>[/confirm-discount]</p>
				<textarea id="discount_reason" class="hdn">{discount-reason}</textarea>
				<p>Do it: <b><span id="doit">{currency}0</span></b> <!--<a href="javascript:issues.editDoit({id}, this);">[invoice][not-invoice]<span class="fa fa-pencil" id="doitBtn"></span>[/invoice]</a>--></p>
				<!--[service-price]<button class="btn btnInvisible" type="button" onclick="issues.invisible({id});"><span class="fa fa-plus"></span> Increase price</button>[/service-price]-->
				<input type="hidden" name="upcharge">
			</div>
		</div>
		<div class="dClear"></div>
	</div>
		
	<div class="sTitle">Status notes</div>
	<div class="uDetails">
		<div class="tbl tblDev" id="tblStatusNotes">
			<div class="tr">
				<div class="th w10">
					Date
				</div>
				<div class="th w100">
					Status
				</div>
				<div class="th w100">
					Staff
				</div>
				<div class="th">
					Note
				</div>
			</div>
			<div class="tr dev">
				<div class="td w10[important] important[/important]">
					<span class="thShort">Date: </span>{date}
				</div>
				<div class="td w100[important] important[/important]">
					<span class="thShort">Status: </span>New
				</div>
				<div class="td w100[important] important[/important]">
					<span class="thShort">Staff: </span><a href="/users/view/{intake-id}" target="_blank" class="intakeTech">{intake-name} {intake-lastname}</a>
				</div>
				<div class="td[important] important[/important]">
					<span class="thShort">Note: </span>{descr}
				</div>
			</div>
			{status-notes}
		</div>
	</div>

	<div class="isSpace"></div>
	
	<div class="userInfo">
		<div class="tabs">
			<div class="tab" id="info" data-title="Info">
				<input type="hidden" name="set_services">
				[is-services]
				<div class="sTitle">{lang=Services} [invoice-done][not-invoice-done]<a href="#" onclick="issues.addInv({id}, '{service-ids}', 'service', null, {customer-id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>[/invoice-done]</div><!--onclick="issues.addIssue({id}, '{customer-name} {customer-lastname}', '{category} {model}'); return false;"-->
				<div class="uDetails">
					<div class="tbl tblDev" id="tbl_services">
						<div class="tr">
							<div class="th w10">
								ID
							</div>
							<div class="th">
								Name
							</div>
							<div class="th w100">
								Quantity
							</div>
							<div class="th w100">
								Price
							</div>
							<div class="th w100">
								Event
							</div>
						</div>
						{services}
						
						<div class="tr bt1">
							<div class="th w10">
								Total
							</div>
							<div class="th">
							</div>
							<div class="th w100" id="serv_total">
								0
							</div>
						</div>
						
					</div>
				</div>
				<div class="chList" data-issue="{id}">
				</div>
				[/is-services]
				
				<input type="hidden" name="set_inventory">
				<div class="sTitle">{lang=Inventory} [invoice-done][not-invoice-done]<a href="#" onclick="issues.addInv({id}, '{inventory-ids}', 'stock', {device-id}, {customer-id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>[/invoice-done]</div>
				<div class="uDetails">
					[is-inventory]
					<div class="tbl tblDev" id="tbl_inventory">
						<div class="tr">
							<div class="th w10">
								ID
							</div>
							<div class="th">
								Type
							</div>
							<div class="th">
								Category
							</div>
							<div class="th">
								Model
							</div>
							<div class="th">
								Location
							</div>
							<div class="th">
								Price
							</div>
							<div class="th w100">
								Event
							</div>
						</div>
						{inventory}
						<div class="tr bt1">
							<div class="th w10">
								Total
							</div>
							<div class="th">
							</div>
							<div class="th">
							</div>
							<div class="th">
							</div>
							<div class="th">
							</div>
							<div class="th" id="inv_total">
								0
							</div>
							<div class="th w100">
							</div>
						</div>
					</div>
					[not-is-inventory]
					<div class="isSpace"></div>
					[/is-inventory]
				</div>
				
				<input type="hidden" name="purchase">
				<div class="sTitle">{lang=Purchases} [show][invoice-done][not-invoice-done]<a href="#" onclick="issues.addPur({id}, '{purchase-ids}', {customer-id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>[/invoice-done][/show]</div>
				<div class="uDetails">
					[is-purchase]
					<div class="tbl tblDev" id="tbl_purchases">
						<div class="tr">
							<div class="th w10">
								ID
							</div>
							<div class="th">
								Name
							</div>
							<div class="th">
								Status
							</div>
							<div class="th">
								Link
							</div>
							<div class="th">
								Price
							</div>
							<div class="th w100">
								Event
							</div>
						</div>
						{purchases}
						<div class="tr bt1">
							<div class="th w10">
								Total
							</div>
							<div class="th">
							</div>
							<div class="th">
							</div>
							<div class="th">
							</div>
							<div class="th" id="pur_total">
								0
							</div>
							<div class="th w100">
							</div>
						</div>
					</div>
					[not-is-purchase]
					<div class="isSpace"></div>
					[/is-purchase]
				</div>
				
				<div class="sTitle">Feedbacks [staff][not-staff]<a href="#" onclick="issues.addFeedback({id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a>[/staff]</div>
				[feedback]
				<div class="uDetails mBottom">
					<div class="tbl tblDev">
						<div class="tr">
							<div class="th">
								Date
							</div>					
							<div class="th">
								Staff
							</div>
							<div class="th">
								Rating
							</div>
							<div class="th">
								Comment
							</div>
							<div class="th w100">
								Event
							</div>
						</div>
						{feedback}
					</div>
				</div>
				[not-feedback]
					<div class="isSpace"></div>
				[/feedback]
				
				<div class="sTitle">{lang=Notes}</div>
					[is-notes]
					<div class="uDetails">
						<div class="tbl tblDev" id="tblNotes">
							<div class="tr">
								<div class="th w10">
									Date
								</div>
								<div class="th w100">
									Staff
								</div>
								<div class="th">
									Note
								</div>
							</div>
							{notes}
						</div>
					</div>
					[not-is-notes]
					<div class="isSpace"></div>
					[/is-notes]
				
				<div class="addNote">
					<div class="sTitle">{lang=leaveNote}</div>
					<div class="iGroup fw">
						<textarea name="note"></textarea>
					</div>
					<div class="sGroup">
						<button type="button" class="btn btnSubmit" onclick="issues.addNote({id});">{lang=addNote}</button>
					</div>
				</div>
			</div>
			<div class="tab" id="ChangeLog" data-title="{lang=Stats}">
				<div class="sTitle">{lang=ChangeLog}</div>
				<div class="uDetails">
					<div class="tbl tblDev stats_tbl">
						<div class="tr">
							<div class="th w10">
								Date
							</div>
							<div class="th w100">
								Staff
							</div>
							<div class="th">
								Action
							</div>
						</div>
						{stats}
					</div>
					<button class="btn btnLoad{more}" onclick="issues.DoloadStats(this, {id});">Load more</button>
				</div>
				
				<div class="sTitle">{lang=Invoices}</div>
				<div class="uDetails">
					<div class="tbl tblDev">
						<div class="tr">
							<div class="th w10">
								ID
							</div>
							<div class="th">
								Date
							</div>					
							<div class="th">
								Amount
							</div>
							<div class="th">
								Paid
							</div>
							<div class="th">
								Due
							</div>
							<div class="th">
								Status
							</div>
							<div class="th w100">
								Event
							</div>
						</div>
						{invoices}
					</div>
				</div>
			</div>
			<div class="tab" id="AssignedTo" data-title="Assigned to">
				<div class="tbl tblDev">
						<div class="tr">
							<div class="th w10">
								Date
							</div>
							<div class="th">
								Staff
							</div>
						</div>
						{assigned_tbl}
					</div>
			</div>
	</div>
</section>
<script>
	var type_id = 0;
	function getBrands(v) {
		$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#brand > ul').html(items).sForm({
					action: '/inventory/allCategories',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="brand"]').data() || {}).join(','),
						query: $('#brand > .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="brand"]').data(),
					s: true
				}, $('input[name="brand"]'), getTypes);
			}
		}, 'json');
	}
	
	function getTypes(f) {
		var items = '', lId = 0;
		cat = Object.keys($('input[name="brand"]').data()).join(',');
		$.post('/inventory/allTypes', {
			name: 1,
			brand: Object.keys($('input[name="brand"]').data()).join(',')
		}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!f) {
					$('#model > input').removeData();
					$('#type_id > input').removeData();
					$('#type_id > input + div').remove();
					$('#type_id > input').after($('<ul/>'));
				}
				$('#type_id > ul').html(items).sForm({
					action: '/inventory/allTypes',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="type_id"]').data() || {}).join(','),
						query: $('#type_id > .sfWrap input').val() || '',
						name: 1,
						brand: Object.keys($('input[name="brand"]').data()).join(',') || 0
					},
					all: false,
					select: $('input[name="type_id"]').data(),
					s: true
				}, $('input[name="type_id"]'), inventory.getGroup, {
					brand: "brand"
				});
				/*$('select[name="type_id"]').html('<option value="0">No selected</option>' + items).val(type_id).next().remove();
				$('#page').select();*/
			}
		}, 'json');
	}
	
	$(function() {
		$('input[name="set_inventory"]').data({set-inventory});
		$('input[name="set_services"]').data({set-services});
		$('input[name="purchase"]').data({set-purchases});
		$('input[name="upcharge"]').data({upcharge});
		[is-services]
		$.each({serv_json} || {}, function(i, v) {
			var steps = '', done = 0;
			$.each(v.steps, function(ind, val) {
				steps += '<li id="' + ind + '"' + (val.value ? ' class="done"' : '') + ' onclick="issues.addStep(this, {id});">' + val.name + '</li>';
				if (val.value) done++;
			});
			$('.chList').append(v.steps.length ? $('<div/>', {
				id: 'sSteps_' + i,
				class: 'servSteps',
				html: $('<div/>', {
					class: 'servTitle',
					html: v.name + '. Steps <span class="num">' + done + '</span>/<span class="full">' + Object.keys(v.steps).length + '</span> (<span class="number">' + (done/Object.keys(v.steps).length*100).toFixed(0) + '</span>/100%)'
				})
			}).append($('<ul/>', {
				html: steps
			})) : '');
			
		});
		[/is-services]
		total.fTotal();
		//$('#quote').text('$' + {quotePrice} || $('#totalPrice').text());
	});
</script>
<script src="{theme}/new-js/issues.js"></script>