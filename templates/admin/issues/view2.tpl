<style>
.uHide, .iHide, .aHide {
	display: none;
}
.bMore {
    display: inline-block;
    background: #f7f8fa;
    border: 1px solid #eef0f3;
    padding: 5px 7px;
    border-radius: 5px;
}
.payPrice {
    font-size: 1.2em;
    font-weight: bold;
}
.payPrice > p {
	padding: 16px;
}
.inv_info {
	padding: 4px 5px;
}
.sl_st {
    position: relative;
    overflow: hidden;
    padding: 0;
    float: left;
}
.sl_st > a {
    float: left;
    color: #749eb7;
    padding: 9px 11px;
    background-color: #f7f8fa;
    border: 1px solid #eef0f3;
    font-size: 15px;
    border-radius: 10px;
    margin: 2px;
    cursor: pointer;
    text-align: center;
    box-shadow: 1px 1px 1px rgba(80, 80, 80, 0.07);
}
.sl_st > a:hover {
	background-color: #749eb7;
	color: #f6f9ff;
	box-shadow: 2px 2px 2px rgba(80, 80, 80, 0.07);
}
.rowc {
    position: absolute;
    top: 50%;
    color: #749eb7;
    font-size: 85px;
    font-weight: bold;
    margin-top: 3px;
    cursor: pointer;
    font-family: serif;
    cursor: pointer;
    text-shadow: 2px 2px 2px rgba(80, 80, 80, 0.07);
	-webkit-touch-callout: none; /* iOS Safari */
	-webkit-user-select: none; /* Safari */
	-khtml-user-select: none; /* Konqueror HTML */
	-moz-user-select: none; /* Firefox */
	-ms-user-select: none; /* Internet Explorer/Edge */
	user-select: none;
    z-index: 1;
}
.rowc:hover {
	color: #78b0d2;
	text-shadow: 4px 4px 4px rgba(80, 80, 80, 0.07);
}
.rowc.l {
	left: -27px;
	display: none;
}
.rowc.r {
	right: -25px;
}
.slbSt {
	display: none;
	position: relative;
	margin-left: -15px;
	margin-top: 15px;
}

.edprice {
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
.edprice::before {
    content: "\f040";
    float: right;
    margin-left: 2px;
    margin-top: -1px;
}
.tblDev > .tr:hover {
	background: #F7F8FA;
}
.tr:hover > .td {
	background: #F7F8FA;
	color: #777;
}
.profit {
    text-transform: uppercase;
    font-size: 32px !important;
    padding: 18px !important;
}
</style>
<section class="mngContent fullw" id="issue" data-id="{id}" data-currency="{currency}" data-customer="{customer-id}" data-object="{object-id}" data-discount="{discount-id}">
	[arrows]
	<div class="arrows">
		[prev]<span class="fa fa-arrow-left" onclick="Page.get('/issues/view/{prev}');"></span>[/prev]
		[next]<span class="fa fa-arrow-right right" onclick="Page.get('/issues/view/{next}');"></span>[/next]
	</div>
	[/arrows]
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>[internal]Internal [/internal]{lang=Issue} #{id}
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
					<li><a href="/issues/edit/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Edit issue"><span class="fa fa-pencil"></span> {lang=editIssue}</a></li>
				[/invoice]
				[staff][not-staff]<li><a href="javascript:issues.addFeedback({id});"><span class="fa fa-comment"></span> Add Feedback</a></li>[/staff]
				[switch-assigned]<li><a href="javascript:issues.assigned({id});"><span class="fa fa-user"></span> Switch assigned</a></li>[/switch-assigned]
				<li><a href="javascript:issues.transfer({id});"><span class="fa fa-arrow-right"></span> Transfer</a></li>
				<li><a href="javascript:issues.del({id});"><span class="fa fa-times"></span> Delete</a></li>
			</ul>
		</div>
		<div class="flRight">
			<div class="profit tooltip" style="margin: -18px 10px;border-radius: 0px;">
				Profit: <span id="income">{currency}</span>
				<div style="text-align: left;width: 350px;color: #666;font-size: 10px;z-index: 999999999;">
					<b>Estimated income:</b>
						<span id="est_income">0</span>
					<br />
					<b>Expanses:</b>
						<span id="exps">$ {total-income}</span>
					<br />
					<b>Purchase:</b>
						$ <span id="in-parts">0</span>
					<br />
					<b>Purchase prifit:</b>
						<span id="purchprf">0</span>
<!-- 					Salary+Expanses/jobs last 30 day - parts + services + profit
					<br>
					<b style="font-size: 15px;">-(({income-salary}+{income-expanses})/{income-count})-<span id="in-parts">0</span>+<span id="in-service">0</span>+<span id="in-profit">0</span></b> -->
				</div>
			</div>
			[finish]
			<div class="uCamera hnt hntBottom hnts" data-title="Post on site" onclick="Page.get('/issues/req_pub/{id}')" style="background: #7ca1b7;">
				<span class="fa fa-book"></span>
			</div>
			[/finish]
			<div class="uCamera hnt hntBottom hnts" data-title="Open forms" onclick="issues.openDualWindow({id})" style="background: #7aa62c;">
				<span class="fa fa-file-text-o"></span>
			</div>
			[buy]<div class="uCamera hnt hntBottom hnts" data-title="Buy this Device " onclick="issues.buyDevice({id})" style="background: #c56060;"><span class="fa fa-shopping-cart"></span></div>[/buy]
			[invoice][not-invoice]<div class="uCamera hnt hntBottom hnts" data-title="Check out" onclick="invoices.issCreate({id}, {customer-id}, {object-id}, {discount-id})" style="background: #36b1e6;"><span class="fa fa-usd"></span></div>[/invoice]
			<div class="uCamera hnt hntBottom hnts" data-title="Barcode" onclick="location.href = '/pdf/barcode/{id}'"><span class="fa fa-barcode"></span></div>
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
					<p><!-- <a href="javascript:$('.uHide').slideToggle();" class="bMore">Show more <span class="fa fa-arrow-down"></span></a> --></p>
					<br />
					<p>{customer-email}</p>
					<p><i>{country-name} {state-name} {city-name} {customer-address}</i></p>
				</div>
				<div class="slbSt">
					<div class="sl_st"></div>
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
				<div class="slbSt">
					<div class="rowc l" onclick="stSlide()">‹</div>
					<div class="sl_st"></div>
					<div class="rowc r" onclick="stSlide(1)">›</div>
				</div>
			</div>
		</div>
	</div>
	[/customer]
		
	<div class="uTitle dClear">
		<div class="uName flLeft">
			<div>
				<div class="inv_info">
					<p>
						<a href="/inventory/view/{device-id}" target="_blank" class="eBtn">{type-name} {category} {model-name} {model}</a> <a href="/inventory/edit/{device-id}" target="_blank" class="eBtn"><span class="fa fa-pencil"></span></a>
					</p>
					<p><b>S/N:</b> {serial}</p>
					<p><b>OS:</b> {os} {version-os}</p>
					<p><b>Charger:</b> {charger}</p>
					[password]<b>Password</b>: {password}[/password]
					<ul class="opts">{options}</ul>
				</div>
			</div>
			<div class="address">
					[pickup-status]
						<p class="devStatus" data-id="0"><b>Status</b>: Picked up
							<a href="#" onclick="issues.updateStatus({id}); return false;" class="changeSt">
								<span class="fa fa-pencil"></span> Change
							</a>
						</p>
					[not-pickup-status]
						<p class="devStatus" data-id="[confirmed_warranty]{warranty-status-id}[not-confirmed_warranty]{status-id}[/confirmed_warranty]"[confirmed_warranty] data-warranty="(Warranty)"[/confirmed_warranty]><b>Status</b>: [confirmed_warranty]{warranty-status}[not-confirmed_warranty]<span>{status}</span>[/confirmed_warranty]
							<a href="#" onclick="issues.updateStatus({id}); return false;" class="changeSt">
								<span class="fa fa-pencil"></span> Change
							</a>
						</p>
					[/pickup-status]
					<p class="devObject"><b>Store</b>: {object}</p>
					<p class="devLocation" data-id="{location-id}" data-count="{location-count}" data-object="{object-id}" data-oname="{object}" location_count="{sublocation}"><b>Location</b>: {location} 
						<a href="#" onclick="issues.updateStatus({id}); return false;" class="changeSt">
							<span class="fa fa-pencil"></span> Change
						</a>
					</p>
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
					[confirmed]<!-- <a href="#" onclick="issues.updateStatus({id}, {nconfirmed}); return false;"><span class="fa fa-check" style="color: #A2CE4E;"></span>Update status</a> -->[/confirmed]
					[/warranty_request]
			</div>
		</div>
		<!-- <div class="moreBtn" onclick="hideArea(this);" style="margin: 9px 0px; padding: 4px;">More</div> -->
		<div class="dClear"></div>
		<div class="moreArea" id="details">
			<div id="tax" class="hdn">{object-tax}</div>
			
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
					<div class="tr">
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
			
			<a style="color: #299CCE;margin: 15px;" href="#" onclick="$(this).next().slideToggle(); $(this).remove(); return false;" class="aServ">
				<span class="fa fa-plus"></span> ADD NOTE
			</a>
			
			<div class="addNote hdn">
				<div class="sTitle">{lang=leaveNote}</div>
				<div class="iGroup fw">
					<textarea name="note"></textarea>
				</div>
				<div class="sGroup">
					<button type="button" class="btn btnSubmit" onclick="issues.addNote({id});">{lang=addNote}</button>
				</div>
			</div>
					
		</div>
	</div>	
		
	<div class="uTitle dClear issueInfo">
	
		<div class="fl50">
			<input type="hidden" name="set_services">
			<input type="hidden" name="set_inventory">
			<input type="hidden" name="purchase">
			<div class="tbl tblDev" id="tbl_services">
				<div class="tr">
					<div class="th w100">
						Name
					</div>
					<div class="th">
						Quantity
					</div>
					<div class="th">
						Price
					</div>
					<div class="th">
						Type
					</div>
					<div class="th w10">
						Options
					</div>
				</div>
				{services}
				{inventory}
				{purchases}
	
				<div class="tr" style="text-align: center;"><div class="td"><a style="color: #299CCE;" href="javascript:issues.addInv({id}, '{service-ids}', 'service');" class="aServ" style="margin-top: 10px; text-align: left;"><span class="fa fa-plus"></span> ADD ITEM</a></div></div>

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
		<div class="fl50">
			<div class="payPrice">
				<p>Intake Tech: <a href="/users/view/{intake-id}" target="_blank" class="intakeTech">{intake-name} {intake-lastname}</a></p>
				<p>Intake Date: {date}</p>
				<p>Total: <b><span id="totalPrice" data-total="{total}" data-id="{id}">0</span></b></p>
				<p>Due: <b>$ {due}</b></p>
				<!--<p>Quote: <b><span id="quote">0</span></b> <a href="javascript:issues.editQuote({id}, this);">[invoice][not-invoice]<span class="fa fa-pencil" id="quoteBtn"></span>[/invoice]</a></p>-->
				
				
				<p>Discount: 
					<b>
						<span id="discount" data-id="{discount-id}" data-name="{discount-name}">{discount}</span>% {discount-name}
					</b>
					<a href="javascript:issues.editDiscount({id}, this, {discount-id}, {discount-confirmed});">
						[invoice-done][not-invoice-done]<span class="fa fa-pencil" id="discountBtn"></span>[/invoice-done]
					</a>
					[confirm-discount]
						<a href="javascript:issues.confirmDiscount({id});" ondblclick="return false;">
							[invoice-done][not-invoice-done]<span class="fa fa-check" id="discountConfirm"></span>[/invoice-done]
						</a>
					[/confirm-discount]
				</p>
				<textarea id="discount_reason" class="hdn">{discount-reason}</textarea>
				<p>Do it: <b><span id="doit">{currency}0</span></b> <!--<a href="javascript:issues.editDoit({id}, this);">[invoice][not-invoice]<span class="fa fa-pencil" id="doitBtn"></span>[/invoice]</a>--></p>
				
				<!--[service-price]<button class="btn btnInvisible" type="button" onclick="issues.invisible({id});"><span class="fa fa-plus"></span> Increase price</button>[/service-price]-->
				<input type="hidden" name="upcharge">
			</div>
		</div>
		<div class="dClear"></div>
	</div>
		
	<!-- Status notes -->

	<div class="isSpace"></div>
	
	<div class="userInfo">
		<!-- Tabs -->
		<div class="tabs">
			<div class="tab" id="info" data-title="Info">
				<!-- Services -->
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
								Options
							</div>
						</div>
						{feedback}
					</div>
				</div>
				[not-feedback]
					<div class="isSpace"></div>
				[/feedback]
				
				<!-- Notes -->
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
								Options
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
	</div>
</section>
<script>
	var type_id = 0, income = {income}, income2 = {total-income};
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
		$('.slbSt').hide();
		$.post('/issues/is_warranty', {
			id: {id}
		}, function(r) {
			$.post('/inventory/allStatuses', {
					nIds: $('.devStatus').attr('data-id'),
					set_inventory: Object.keys($('input[name="set_inventory"]').data()).join(','),
					set_services: Object.keys($('input[name="set_services"]').data()).join(','),
					warranty: r
				}, function (res) {
				if (res){
				console.log();
					if(res.list.length !== 0){
						$('.sl_st > a:visible:first').prev().length
						$('.slbSt').show();
						var tpl = '';
						for(var k in res.list){
							if($('.devStatus').attr('data-id') != k)
							tpl += '<a href="#" onclick="javascript:issues.updateStatus({id}, '+k+'); return false;">'+res.list[k].name+'</a>';
						}
						$('.sl_st').html(tpl);
					}
				}
			}, 'json');
		});
	});
	function stSlide(a){
		if(a){
			if($('.sl_st > a:visible:last').position().top > 50){
				$('.sl_st > a:visible:first').hide();
				if($('.sl_st > a:last').prev().position().top <= 50)
					$('.rowc.r').hide();
				$('.rowc.l').show();
			}
		} else {
			var prev = $('.sl_st > a:visible').prev();
			if(prev){
				prev.show();
				if($('.sl_st > a:last').prev().position().top > 50)
					$('.rowc.r').show();
				if(!$('.sl_st > a:visible:first').prev().length)
					$('.rowc.l').hide();
			}
		}
	}
</script>
<!-- <script src="{theme}/new-js/issues.js"></script> -->