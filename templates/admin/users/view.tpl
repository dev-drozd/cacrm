{include="users/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=userInfo}
		<a href="javascript:sendMessage.mdl({id}, 'User');" class="mesBtn"><span class="fa fa-exclamation-circle" aria-hidden="true"></span></a>
		<div class="uMore">
			<span class="togMore" onclick="$('.uMore > ul').show();">
				<span class="fa fa-ellipsis-v showMob"></span>
				<span class="showFll">Options</span>
			</span>
			<ul style="width: 185px;">
				<li class="dd">
					<a href="#" onclick="$(this).next().slideToggle('fast');return false;"><span class="fa fa-file-text-o"></span>Forms</a>
					<ul>
						{forms-list}
					</ul>
				</li>
				<li><a href="/users/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> Edit user</a></li>
				[seo-master]<li><a href="#" onclick="seoInvite({id}); return false;"><span class="fa fa-external-link-square"></span> Invite to the SEO panel</a></li>[/seo-master]
			</ul>
		</div>
		<div class="flRight">
			[user-auth]
			<a href="/users/user_auth?id={id}" class="fastMes hnt hntBottom" data-title="Login as user" style="background: #666">
				<span class="fa fa-user"></span>
			</a>
			[/user-auth]
		[ncustomer]
			[page-owner][not-page-owner]<a href="/im/{id}" onclick="Page.get(this.href); return false;" class="fastMes hnt hntBottom" data-title="Send im"><span class="fa fa-envelope"></span></a>[/page-owner]
			[suspention]<div class="uCamera hnt hntBottom" data-title="Make suspention" style="background: #bb3c3c;" onclick="user.makeSuspention({id});"><span class="fa fa-vcard"></span></div>[/suspention]
			<div class="uCamera hnt hntBottom" data-title="Jobs" onclick="Page.get('/activity/issues?staff={id}&staff_name={name}%20{lastname}');" style="background: #36b1e6;"><span class="fa fa-wrench"></span></div>
			<div class="uCamera hnt hntBottom" data-title="Camera notes" onclick="Page.get('/users/camera/{id}/updated');" style="background: #c56060;"><span class="fa fa-file-text"></span></div>
			<div class="uCamera hnt hntBottom" data-title="Activity" onclick="Page.get('/users/camera/{id}');"><span class="fa fa-camera"></span></div>
			<div class="uPoints hnt hntBottom" data-title="Points" onclick="Page.get('/users/point_details/{id}');"><span class="fa fa-money"></span>: {points}</div>
			<div class="uTime hnt hntBottom" data-title="Working time" [time-show]onclick="Page.get('/users/time/{id}');"[/time-show]><span class="fa fa-clock-o"></span>: {time}</div>
		[/ncustomer]
			<div class="uCamera hnt hntBottom" data-title="Barcode" onclick="location.href = '/pdf/ubarcode/{id}'"><span class="fa fa-barcode"></span></div>
		</div>
	</div>
	<div class="userInfo">
		[deleted]<div class="mt dClear">
			User deleted
		</div>[/deleted]
		[not-valid-phone]<div class="mt dClear">Cusomer has invalid phone number. Please, check it.</div>[/not-valid-phone]
		<div class="uTitle dClear">
			<figure>
				[ava]<div><img src="/uploads/images/users/{id}/thumb_{ava}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-ava]<span class="fa fa-user-secret"></span>[/ava]
			</figure>
			<div class="uName">
				<div>
					{name} {lastname}
					<p><b>Phone: </b>{phone}</p>
					<p><b>Email: </b> <a href="mailto:{email}">{email}</a></p>
					<p><b>Reg date: </b>{reg-date}</p>
					<p><b>Last visit: </b>{last-date}</p>
					[ip]<p><b>IP: </b> <a href="https://www.infobyip.com/ip-{ip}.html" target="__blank">{ip}</a></p>[/ip]
					<p><b>Group:</b> {group}</p>
					<p>{country} {state}
					{city} {address} ({zipcode})
					[ver]<span class="hnt hntTop" data-title="{ver}"><span class="fa fa-check"></span></span>[/ver]</p>
				</div>
				<div class="address">
					[rating]
					<div class="feedback-middle">
						Feedback:<br>
						<span><a href="/issues/feedbacks?staff={id}&sname={name} {lastname}" onclick="Page.get(this.href); return false;">{feedback}</a></span>
					</div>
					[/rating]
					[appointment]
					<div class="appointment-middle">
						Appointment:<br>
						{appointment_date} on {appointment_store}<br>
						<span class="btn-notif notif-confirm fa fa-check" onclick="user.confirmApp(this, {appointment}, 1);"></span>
						<span class="btn-notif notif-dicline fa fa-times" onclick="user.confirmApp(this, {appointment}, 0);"></span>
					</div>
					[/appointment]
				</div>
			</div>
		</div>
		[dbl]
			<div class="sTitle">Dublicated</div>
			<div class="dClear">
				{dbl}
			</div>
		[/dbl]
		<div class="newUserItems dClear">
			<div id="user_add_devices" onclick="Page.get('/inventory/add?user={id}&backusr={id}');" style="display: none;">
				<span class="fa fa-laptop"></span>
				{lang=Devices}
				<span class="fa fa-plus"></span>
			</div>
			
			<div id="user_add_invoices" onclick="Page.get('/invoices/add?user={id}');" style="display: none;">
				<span class="fa fa-credit-card"></span>
				{lang=Invoices}
				<span class="fa fa-plus"></span>
			</div>
			
			<div id="user_add_onsite" onclick="user.addOnsite({id});" style="display: none;">
				<span class="fa fa-car"></span>
				{lang=OnSiteServices}
				<span class="fa fa-plus"></span>
			</div>
			
			<div id="user_add_purchases" onclick="Page.get('/invoices/add?modal&user={id}');" style="display: none;">
				<span class="fa fa-shopping-cart"></span>
				Purchases
				<span class="fa fa-plus"></span>
			</div>
		</div>

		<div id="user_devices">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="sTitle">{lang=Devices} <a href="/inventory/add?user={id}&backusr={id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div>
			<div class="uDetails">
				<div class="tbl tblDev">
					<div class="tHead">
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
								OS
							</div>
							<div class="th">
								Location
							</div>
							<div class="th w100">
								Options
							</div>
						</div>
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/devices" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>
		
		<div id="user_invoices">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="sTitle">{lang=Invoices} <a href="/invoices/add?user={id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div>
			<div class="uDetails">
				<div class="tbl">
					<div class="tHead">
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
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/invoices" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>
		
		<div id="user_onsite">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="sTitle">{lang=OnSiteServices} <a href="#" onclick="user.addOnsite({id}); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div>
			<div class="uDetails">
				<div class="tbl">
					<div class="tHead">
						<div class="tr">
							<div class="th">
								Name
							</div>
							<div class="th">
								Date start
							</div>					
							<div class="th">
								Date end
							</div>
							<div class="th">
								Left calls
							</div>
							<div class="th">
								Left time
							</div>
							<div class="th w100">
								Action
							</div>
						</div>
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/onsite_services" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>
		
		<div id="user_purchases">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="sTitle">Purchases <a href="/invoices/add?modal&user={id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div>
			<div class="uDetails">
				<div class="tbl" id="tblNotes">
					<div class="tHead">
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
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/purchases" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>

		<div class="sTitle">Appointments <a href="/users/add_appointment?user={id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-plus"></span></a></div>
		<div id="user_appointments">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="uDetails">
				<div class="tbl" id="tblAppointments" style="width: 100%">
					<div class="tHead">
						<div class="tr">
							<div class="th" style="width: 150px">
								Date
							</div>
							<div class="th" style="width: 150px">
								Store
							</div>
							<div class="th">
								Staff
							</div>
							<div class="th">
								Note
							</div>
						</div>
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/appointments" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>
		
		<div class="sTitle">Notes</div>
		<div id="user_notes">
			<div class="profile-loader show">
				<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
			</div>
			<div class="uDetails">
				<div class="tbl" id="tblNotes">
					<div class="tHead">
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
							<div class="th w5">
								Event
							</div>
						</div>
					</div>
					<div class="tBody">
					</div>
				</div>
			</div>
			<button class="btn btnLoad hdn" onclick="Doload(this);" action="/users/notes" page="0" data-id="{id}"><span class="fa fa-refresh"></span> Load more</button>
		</div>
	
		<div class="addNote">
			<div class="sTitle">Leave Note</div>
			<div class="iGroup fw">
				<textarea name="note"></textarea>
			</div>
			<div class="sGroup">
				<button type="button" class="btn btnSubmit" onclick="user.addNote({id}, this);">Add Note</button>
			</div>
		</div>
	
	</div>
</section>
<script>
var seoInvite = function(a){
	$.post('/users/seo_invite', {id: a});
	alr.show({
		class: 'alrSuccess',
		content: 'Invitation Sent Successfully!',
		delay: 2
	});
	return false;
}
	$(function() {
		$.post('/users/devices', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_devices .tBody').html(r.content);
				$('#user_add_devices').remove();
				$('#user_devices .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_devices .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_devices .btnLoad').attr('page')) + 1);
			} else {
				$('#user_devices').remove();
				$('#user_add_devices').show();
			}
		}, 'json');
		
		$.post('/users/invoices', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_invoices .tBody').html(r.content);
				$('#user_add_invoices').remove();
				$('#user_invoices .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_invoices .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_invoices .btnLoad').attr('page')) + 1);
			} else {
				$('#user_invoices').remove();
				$('#user_add_invoices').show();
			}
		}, 'json');
		
		$.post('/users/onsite_services', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_onsite .tBody').html(r.content);
				$('#user_add_onsite').remove();
				$('#user_onsite .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_onsite .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_onsite .btnLoad').attr('page')) + 1);
			} else {
				$('#user_onsite').remove();
				$('#user_add_onsite').show();
			}
		}, 'json');
		
		$.post('/users/purchases', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_purchases .tBody').html(r.content);
				$('#user_add_purchases').remove();
				$('#user_purchases .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_purchases .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_purchases .btnLoad').attr('page')) + 1);
			} else {
				$('#user_purchases').remove();
				$('#user_add_purchases').show();
			}
		}, 'json');
		
		$.post('/users/notes', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_notes .tBody').html(r.content);
				$('#user_notes .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_notes .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_notes .btnLoad').attr('page')) + 1);
			} else
				$('#user_notes').remove();
		}, 'json');
		
		$.post('/users/appointments', {
			id: {id}
		}, function(r) {
			if (r.res_count) {
				$('#user_appointments .tBody').html(r.content);
				$('#user_appointments .profile-loader').removeClass('show');
				if (r.left_count > 0)
					$('#user_notes .btnLoad').removeClass('hdn').attr('page', parseInt($('#user_appointments .btnLoad').attr('page')) + 1);
			} else
				$('#user_appointments').remove();
		}, 'json');
	});
</script>