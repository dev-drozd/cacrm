<div class="ctnr">

	<div class="account">
		<div class="paTitle">Personal account</div>
		<div class="dClear">
			{include="/account/left.tpl"}
			<div class="iSide">
				<div class="uName">
					{name} {lastname}
					<span class="fa fa-pencil" onclick="Page.get('/account/edit');"></span>
				</div>
				
				<ul class="uInfo">
					<li>Email: {email}</li>
					<li>Phone: {phone}</li>					
					<li>Address: {address}</li>
				</ul>

				<ul class="roundInfo dClear">
					<li>
						<span>{points}</span>
						points
					</li>
					<li>
						<span>{devcount}</span>
						devices
					</li>
					<li>
						<span>10</span>
						orders
					</li>
				</ul>

				<div class="liTitle">Devices <span class="fa fa-plus" onclick="account.addDevice();"></span></div>
				<div class="tbl tblDev">
					<div class="tr">
						<div class="th w10">
							ID
						</div>
						<div class="th">
							Type
						</div>
						<div class="th">
							Brand
						</div>
						<div class="th">
							Model
						</div>
						<div class="th">
							OS
						</div>
						<div class="th">
							Status
						</div>
						<div class="th w100">
							Event
						</div>
					</div>
						{devices}
					</div> 
			
				<div class="liTitle">Orders</div>
				<div class="tbl tblDev">
					<div class="tr">
						<div class="th w10">
							ID
						</div>
						<div class="th">
							Date
						</div>
						<div class="th">
							Status
						</div>
						<div class="th">
							Price
						</div>
					</div>
					<div class="tr dev">
						<div class="td w10">
							<strong>#4</strong>
						</div>
						<div class="td">
							2020-05-31 04:16:41
						</div>
						<div class="td">
							confirmed
						</div>
						<div class="td">
							300$
						</div>
					</div>
				</div>
				
				<div class="liTitle">Invoices</div>
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
					</div>
					{invoices}
				</div>

                <div class="liTitle">On site services <span class="fa fa-plus" onclick="account.addOnsite({id});"></span></div>
				<div class="tbl tblDev">
					<div class="tr">
						<div class="th w10">
							Name
						</div>
						<div class="th">
							Date start
						</div>					
						<div class="th">
							Date end
						</div>
						<div class="th">
							Left hours
						</div>
						<div class="th">
							Left time
						</div>
						<div class="th">
							Status
						</div>
					</div>
					{onsite}
				</div>
				
				<div class="liTitle">Appointments <span class="fa fa-plus" onclick="account.addApp({id});"></span></div>
				<div class="tbl tblDev">
					<div class="tr">
						<div class="th w10">
							ID
						</div>
						<div class="th">
							Date
						</div>					
						<div class="th">
							Store
						</div>
					</div>
					{appointments}
				</div>
				
                <div class="liTitle">Refferals</div>
                <ul class="refferals dClear">
					{referrals}
                </ul>
			</div>
		</div>
	</div>
</div>

<script>
    var brand_id = 0, model_id = 0;
</script>