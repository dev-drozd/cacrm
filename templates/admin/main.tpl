<div class="dClear">

	{tasks}
	<!-- test2 -->
	
	<div class="flex-row">
		<div class="col-7">
			<!-- START MANAGE BLOCK -->
			<div class="pnld">
			  <div class="pnlTitle cl ttl">
				  <span class="fa fa-dashboard"></span> Manage
			  </div>
			 <!--  <div class="main-search flex"> -->
<!-- 				<select id="main-search-type" name="sGroup" style="width: 150px;">
					<option value="us">Users</option>
					<option value="ob">Stores</option>
					<option value="de">Stock&amp;Service</option>
					<option value="pu">Purchases</option>
					<option value="is">Jobs</option>
					<option value="in">Invoices</option>
				</select> -->
				<!-- <input type="text" placeholder="search"> -->
				<!-- <button><span class="fa fa-search"></span></button> -->
			  <!-- </div> -->
			  
			  <div class="info-camera-main">
				<div class="fa fa-video-camera"></div>
				<div id="camera-comment"><a href="/users/view/29150" onclick="Page.get(this.href); return false;">		<img src="/uploads/images/users/1703/58b60b26b42cb6.03351607.jpg" align="left">		Ashley McNair	</a>	<span class="hnt hntTop" data-title="Go to the dialogue">		<span class="fa fa-exclamation-circle" onclick="Page.get('/im/29150?text=Camera;117101')"></span>	</span>	<p>15,59,40 left with a router (switch)</p></div>
			  </div>
			  <div class="flex main-panel">
				[users]
				<a href="/users/lite_step" onclick="Page.get(this.href); return false;">
					<i class="fa fa-user-plus" style="color:#47aebb82"></i>
					New customer
				</a>
				[/users]
				<a href="/inventory/add/inventory" onclick="Page.get(this.href); return false;">
					<i class="fa fa-laptop" style="color: #4ec0ce;"></i>
					New inventory
				</a>
				<a href="/inventory/add/service" onclick="Page.get(this.href); return false;">
					<i class="fa fa-list" style="color: #299CCE;"></i>
					New service
				</a>
				[invoces]
				<a href="/invoices/add" onclick="Page.get(this.href); return false;">
					<i class="fa fa-credit-card" style="color:#36b1e6"></i>
					Make purchase
				</a>
				[/invoces]
				[purchase]
				<a href="/purchases/add" onclick="Page.get(this.href); return false;">
					<i class="fa fa-cart-plus" style="color:#df9e63"></i>
					Purchase request
				</a>
				[/purchase]
				<a href="#" onclick="inventory.addRequest(); return false;">
					<i class="fa fa-bullhorn" style="color: #df8163;"></i>
					New request
				</a>
				[invoces]
				<a href="/invoices/add?estimate" onclick="Page.get(this.href); return false;">
					<i class="fa fa-thumbs-o-up" style="color:#df7563"></i>
					Estimate
				</a>
				[/invoces]
				[invoces]
				<a href="/invoices/add?quicksell" onclick="Page.get(this.href); return false;">
					<i class="fa fa-shopping-bag" style="color: #e45c5c;"></i>
					Quick sell
				</a>
				[/invoces]
				<!-- user.newOnsite(); -->
				<a href="#" onclick="Onsite.create(); return false">
					<i class="fa fa-truck" style="color: #A2CE4E;"></i>
					New onsite
				</a>
				<a href="/users/appointment" onclick="Page.get(this.href); return false;">
					<i class="fa fa-handshake-o" style="color: #7ece4e;"></i>
					New appointment
				</a>
				[add-invoice]
				<a href="/invoices/add" onclick="Page.get(this.href); return false;">
					<i class="fa fa-credit-card" style="color: #5bce4e;"></i>
					New invoce
				</a>
				[/add-invoice]
				<a href="#" onclick="$.get('/push'); alert('Form successfully sent'); return false;">
					<i class="fa fa-tablet" style="color: #4ece88;"></i>
					Launch customer registration
				</a>
			  </div>
			</div>
			<!-- END MANAGE BLOCK -->
			
			<!-- START DREAM-TEAM BLOCK -->
			<div class="pnld lPnl cl dreamTimePnl dream_back">
				<div class="pnlTitle cl">Dream team</div>
				<div class="dreamTeam">
					{top-stafs}
				</div>
			</div>
			<!-- END DREAM-TEAM BLOCK -->
		</div>
		
		<!-- START JOBS BLOCK -->
		<div class="col-5 pnld" style="margin-right: 0px;min-height: 500px;">
			<div class="pnlTitle cl ttl">
				Jobs
			</div>
			<div class="tab-cnt" onclick="issTabs(event, this)">
				<span data-status="0" class="active">All</span>
				<span data-status="-2">30 days</span>
				<span data-status="-1">Warranties</span>
				<span data-status="11">New</span>
				<span data-status="1">Diagnosed</span>
				<span data-status="7">Do it</span>
				<span data-status="4">Waiting parts</span>
			</div>
			<div class="unwrap" style="position: absolute;left: 0;top: 114px;right: 0;bottom: 42px;overflow-y: auto;">
				<div>
					<table class="responsive">
						<thead>
							<tr height="60">
							  <th scope="col">ID</th>
							  <th scope="col">CUSTOMER</th>
							  <th scope="col">TOTAL</th>
							  <th scope="col">PAID</th>
							  <th scope="col">STATUS</th>
							</tr>
						</thead>
						<tbody id="issues_list"></tbody>
					</table>
				</div>
			</div>
			<span class="btn btnLoad" onclick="Page.get('/activity/issues'); return false;" style="position: absolute;text-align: center;bottom: 0;">View all jobs</span>
		</div>
		<!-- END JOBS BLOCK -->
	</div>

	<div class="pnl fw lPnl" id="activity">
		<div class="tabs">		
		
			<div class="tab" id="act" data-title="Activity">
				<table class="responsive">
					<thead>
						<tr>
							<th class="wp20">Staff</th>
							<th>Event</th>
							<th>Store</th>
							<th class="wp20">Date</th>
						</tr>
					</thead>
					<tbody>{stats}</tbody>
				</table>
				<button class="btn btnLoad" onclick="Page.get('/activity/');">Show all</button>
			</div>
			
			<div class="tab" id="object_issues" data-title="Store jobs">
				<table class="issuesTbl responsive">
					<thead>
						<tr>
							<th>ID</th>
							<th>Store</th>
							<th>Date</th>
							<th>Total</th>
							<th>Device Type</th>
							<th>Assigned</th>
							<th>Location</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody id="object_issues_list">
					</tbody>
				</table>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/activity/issues/store');">Show all</button>
			</div>
			
			<div class="tab" id="my_issues" data-title="My jobs">
				<table class="responsive issuesTbl">
					<thead>
						<tr>
							<th>ID</th>
							<th>Customer</th>
							<th>Date</th>
							<th>Total</th>
							<th>Device Type</th>
							<th>Assigned</th>
							<th>Location</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody id="my_issues_list"></tbody>
				</table>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/activity/issues?staff=' + _user.id + '&staff_name=' + _user.name + ' ' + _user.lastname);">Show all</button>
			</div>
			
			<div class="tab" id="issue_transfer" data-title="job transfer">
				<table class="responsive">
					<thead>
						<div class="tr">
							<th class="w150">Job</th>
							<th>Date</th>
							<th class="wAmount">To store/staff</th>
							<th class="wAmount">Staff created</th>
							<th>To staff</th>
							<th>Confirmed</th>
						</div>
					</thead>
					<tbody id="issue_transfer_list"></tbody>
				</table>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
			</div>
			
			<div class="tab" id="onsite" data-title="Onsite"[e_onsite] data-counter="{e_onsite-count}"[/e_onsite]>
				<div class="tabs">
					<div class="tab" id="onsite_customers" data-title="Onsite customers">
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">ID</div>
									<div class="th">Service</div>
									<div class="th">Customer</div>
									<div class="th">Date</div>
									<div class="th">Total</div>
									<div class="th">Assigned</div>
								</div>
							</div>
							<div class="tBody" id="onsite_list">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
						<button class="btn btnLoad" onclick="Page.get('/activity/onsite/');">Show all</button>
					</div>
					<div class="tab" id="onsite_details" data-title="Onsite details">
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">Service</div>
									<div class="th">Customer</div>
									<div class="th">Date</div>
									<div class="th">Action</div>
									<div class="th" style="max-width: 300px">Comment</div>
									<div class="th">Assigned</div>
								</div>
							</div>
							<div class="tBody" id="onsite_details_list">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
						<button class="btn btnLoad" onclick="Page.get('/activity/onsite_details/');">Show all</button>
					</div>
					<div class="tab" id="onsite_invoices" data-title="Onsite invoices">
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">ID</div>
									<div class="th">Customer</div>
									<div class="th">Date</div>
									<div class="th">Paid</div>
									<div class="th">Due</div>
								</div>
							</div>
							<div class="tBody" id="onsite_invoices_list">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
						<button class="btn btnLoad" onclick="Page.get('/invoices/?action=yes');">Show all</button>
					</div>
					<div class="tab" id="onsite_unconfirmed" data-title="Onsite customer requests"[e_onsite] data-counter="{e_onsite-count}"[/e_onsite]>
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">ID</div>
									<div class="th">Service</div>
									<div class="th">Customer</div>
									<div class="th">Date</div>
									<div class="th">Total</div>
									<div class="th">Confirm</div>
								</div>
							</div>
							<div class="tBody" id="onsite_uncofirmed_list">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
					</div>
				</div>
			</div>
			
			<div class="tab" id="timer" data-title="Working time">
				<div class="tbl">
					<div class="tHead">
						<div class="tr">
							<div class="th wp20">Staff</div>
							<div class="th">Punch in</div>
							<div class="th">Punch out</div>
							<div class="th">Working time</div>
							[time-money]
								<div class="th">Salary</div>
								<div class="th">Points</div>
							[/time-money]
						</div>
					</div>
					<div class="tBody" id="timers_list">
					</div>
				</div>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/activity/timer/');">Show all</button>
			</div>
			
			<div class="tab" id="cash" data-title="Cash Statistics">
				<div class="tbl">
					<div class="tHead">
						<div class="tr">
							<div class="th w150">Store</div>
							<div class="th">Type</div>
							<div class="th wAmount">System Amount</div>
							<div class="th wAmount">User Amount</div>
							<div class="th wAmount">Drawer Amount</div>
							<div class="th">Action</div>
							<div class="th">Date</div>
							<div class="th wStaff">Staff</div>
							<div class="th" style="width: 30px"></div>
						</div>
					</div>
					<div class="tBody" id="cash_stat_list">
					</div>
				</div>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/activity/cash/');">Show all</button>
			</div>
			
			[time-money]
				<div class="tab" id="pos" data-title="POS">
					<div class="tabs">
						<div class="tab" id="store_report" data-title="Store roport">
							<div id="report"></div>
							<div class="profile-loader show">
								<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
							</div>
						</div>
						[owner]<div class="tab" id="drops_report" data-title="Drops">
							<div id="drops"></div>
							<div class="profile-loader show">
								<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
							</div>
						</div>[/owner]
					</div>
				</div>
			[/time-money]
			
			<div class="tab" id="confirm" data-title="Unconfirmed"[un] data-counter="{un-count}"[/un]>
				<div class="tabs">
					<div class="tab" id="unconf_inventory" data-title="Inventory"[un_inventory] data-counter="{un_inventory-count}"[/un_inventory]>
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">Name</div>
									<div class="th">Price</div>
									<div class="th">Date</div>
									<div class="th">Staff</div>
								</div>
							</div>
							<div class="tBody" id="conf_inventory">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
					</div>
					<div class="tab" id="unconf_discounts" data-title="Discounts"[un_discount] data-counter="{un_discount-count}"[/un_discount]>
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">Name</div>
									<div class="th">Reason</div>
									<div class="th">Date</div>
									<div class="th">Staff</div>
								</div>
							</div>
							<div class="tBody" id="conf_discounts">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
					</div>
					<div class="tab" id="unconf_warranty" data-title="Warranty requests"[un_warranty] data-counter="{un_warranty-count}"[/un_warranty]>
						<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">Job</div>
									<div class="th">Device</div>
									<div class="th">Reason</div>
									<div class="th">Staff</div>
									<div class="th">Date</div>
								</div>
							</div>
							<div class="tBody" id="conf_warranty">
							</div>
						</div>
						<div class="profile-loader show">
							<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						</div>
					</div>
				</div>
			</div>
			
			<div class="tab" id="feedbacks" data-title="Feedbacks">
				<table class="responsive">
					<thead>
						<tr>
							<th class="w10">
								ID
							</th>
							<th>
								Date
							</th>						
							<th>
								Customer
							</th>
							<th>
								Phone
							</th>
							<th>
								Job total
							</th>
							<th class="w100">
								Add feedback
							</th>
						</rt>
					</thead>
					<tbody id="feedbacks_list"></tbody>
				</table>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
			</div>
			
			<div class="tab" id="purchases" data-title="Purchases"[purchases] data-counter="{purchases-count}"[/purchases]>
				<div class="tbl purchasesTbl">
					<div class="tHead">
						<div class="tr">
							<div class="th" style="width: 50px">ID</div>
							<div class="th">Name</div>
							<div class="th">Store</div>
							<div class="th">Customer</div>
							<div class="th">Date</div>
							<div class="th">Total</div>
							<div class="th" style="width: 170px">Status</div>
						</div>
					</div>
					<div class="tBody" id="tbl_purchases">
					</div>
				</div>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/purchases/received');">Show all</button>
			</div>
			<div class="tab" id="appointments" data-title="Appointments">
				<div class="tbl appointmentsTbl">
					<div class="tHead">
						<div class="tr">
							<div class="th">ID</div>
							<div class="th">Customer</div>
							<div class="th">Date</div>
							<div class="th">Store</div>
							<div class="th">Confirmed staff</div>
						</div>
					</div>
					<div class="tBody" id="tbl_appointments">
					</div>
				</div>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/activity/appointments');">Show all</button>
			</div>
			<div class="tab" id="tasks" data-title="Tasks">
				<div class="tbl">
					<div class="tHead">
						<div class="tr">
							<div class="th">Staff creaded</div>
							<div class="th">Date/Time</div>
							<div class="th">Store/Staff</div>
							<div class="th" style="width: 40%;">Note</div>
						</div>
					</div>
					<div class="tBody" id="tbl_tasks">
					</div>
				</div>
				<div class="profile-loader show">
					<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
				</div>
				<button class="btn btnLoad" onclick="Page.get('/tasks');">Show all</button>
			</div>
		</div>
		<div class="filters ap cl global_filters">
			<span class="hnt hntTop" data-title="Filter" onclick="filters.show(this);" id="global_filters"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr cl">
				<div class="fTitle cl">Filters</div>
				<div class="iGroup fw dGroup cl dr app ts">
					<label>Date <span class="fa fa-eraser cl" onclick="clearDate();"></span></label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_activity" readonly>
					<div id="fCalendar" class="calendar-el" data-multiple="1"></div>
				</div>
				<div class="iGroup fw cl os dr app object-el ts" id="object">
					<label>Store</label>
					<select name="object" >
						{stores}
					</select>
				</div>
				<div class="iGroup fw app staff-el ts" id="staff">
					<label id="staff_label">Staff</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw cl cf app" style="display: none;">
					<label>Status</label>
					<select name="status">
						<option value="0">Not selected</option>
						<option value="accept">Accepted</option>
						<option value="dicline">Diclined</option>
					</select>
				</div>
				<div class="iGroup fw cl cf" style="display: none;">
					<label>Type</label>
					<select name="type">
						<option value="0">Not selected</option>
						<option value="cash">Cash</option>
						<option value="credit">Credit</option>
						<option value="check">Check</option>
					</select>
				</div>
				<div class="iGroup fw cl cf" style="display: none;">
					<label>Action</label>
					<select name="action">
						<option value="0">Not selected</option>
						<option value="open">Open</option>
						<option value="close">Close</option>
					</select>
				</div>
				<div class="iGroup fw if" style="display: none;">
					<label>Status</label>
					<select name="status_issues" id="status_issues" onchange="if (this.value > 0) $('select[name=\'status_current\']').val(0).trigger('change');">
						{statuses}
					</select>
				</div>
				<div class="iGroup fw if os payment" style="display: none;">
					<label>Payment</label>
					<select name="payment">
						<option value="0">Not selected</option>
						<option value="paid">Paid</option>
						<option value="unpaid">Unpaid</option>
					</select>
				</div>
				<div class="iGroup fw if" style="display: none;">
					<label>Current Status</label>
					<select name="status_current" id="status_current" onchange="if (this.value > 0) $('#status_issues').val(0).trigger('change');">
						{statuses}
					</select>
				</div>
				<div class="iGroup cl if" style="display: none;">
					<label>Picked up</label>
					<input type="checkbox" name="pickedup">
				</div>
				<div class="iGroup cl if" style="display: none;">
					<label>Show all</label>
					<input type="checkbox" name="all">
				</div>
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="getFilters();">OK</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	
	<div id="stats">
		<div class="pnl lPnl">
			<div class="pnlTitle cl ttl" onclick="setFullStat(event, 0);">{lang=Sales}
				<div class="filters cl">
					<span class="hnt hntTop cl" data-title="Filter" onclick="filters.show(this);" id="stats_filters"><span class="fa fa-filter cl"></span></span>
					<div class="filterCtnr cl">
						<div class="fTitle cl">Filters</div>
						<div class="iGroup fw dGroup cl">
							<label>Date</label>
							<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_stat" readonly>
							<div id="calendar" class="calendar-el" data-multiple="1"></div>
						</div>
						<div class="iGroup fw dGroup cl">
							<label>Compare period</label>
							<input class="cl" type="number" min="0" name="compare_period">
						</div>
						<div class="iGroup fw cl object-el" id="object_stat">
							<label>Store</label>
							<select name="object_stat">
								{stores}
							</select>
							</div>
						<div class="iGroup cl">
							<label>Cash</label>
							<input type="checkbox" name="pay_cash" checked>
						</div>
						<div class="iGroup cl">
							<label>Credit</label>
							<input type="checkbox" name="pay_credit" checked>
						</div>
						<div class="iGroup cl">
							<label>Check</label>
							<input type="checkbox" name="pay_check" checked>
						</div>
						<div class="iGroup cl">
							<label>Merchaine</label>
							<input type="checkbox" name="pay_merchaine" checked>
						</div>
						<div class="cashGroup cl">
							<button type="button" class="btn btnSubmit ac cl" onclick="analytics.sales(this); $(this).parents('.filterCtnr').hide();">OK</button>
							<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div id="stat"></div>
		</div>
		
		<div class="pnl rPnl">
			<div class="pnlTitle cl ttl" onclick="setFullStat(event, 1);">{lang=Efficiency}
				<div class="filters cl">
					<span class="hnt hntTop cl" data-title="Filter" onclick="filters.show(this);" id="efficiency_filters"><span class="fa fa-filter cl"></span></span>
					<div class="filterCtnr cl">
						<div class="fTitle">Filters</div>
						<div class="iGroup fw dGroup cl">
							<label>Date</label>
							<input type="text" class="cl" onclick="$(this).next().show().parent().addClass('act');" name="date_points" readonly>
							<div id="calendar2" class="calendar-el" data-multiple="1"></div>
						</div>
						<div class="iGroup fw cl object-el" id="object_points">
							<label>Store</label>
							<select name="object_points" >
								{stores}
							</select>
						</div>
						<div class="iGroup fw cl" id="user_points">
							<label>User</label>
							<input type="hidden" name="user_points">
							<ul class="hdn"></ul>
						</div>
						<div class="cashGroup cl">
							<button type="button" class="btn btnSubmit ac cl" onclick="analytics.points(this); $(this).parents('.filterCtnr').hide();">OK</button>
							<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div id="eff"></div>
		</div>
		
		<div class="pnl lPnl">
			<div class="pnlTitle cl ttl" onclick="setFullStat(event, 2);">Feedbacks
				<div class="filters cl">
					<span class="hnt hntTop cl" data-title="Filter" onclick="filters.show(this);" id="feedbacks_filters"><span class="fa fa-filter cl"></span></span>
					<div class="filterCtnr cl">
						<div class="fTitle">Filters</div>
						<div class="iGroup fw dGroup cl">
							<label>Date</label>
							<input type="text" class="cl" onclick="$(this).next().show().parent().addClass('act');" name="date_feedbacks" readonly>
							<div id="calendar3" class="calendar-el" data-multiple="1"></div>
						</div>
						<div class="cashGroup cl">
							<button type="button" class="btn btnSubmit ac cl" onclick="analytics.feedback(this); $(this).parents('.filterCtnr').hide();">OK</button>
							<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div id="feedbacks_plot"></div>
		</div>
		
		<div class="pnl rPnl">
			<div class="pnlTitle cl ttl" onclick="setFullStat(event, 3);">New customers <span id="total_customers"></span>
				<div class="filters cl">
					<span class="hnt hntTop cl" data-title="Filter" onclick="filters.show(this);" id="customers_filters"><span class="fa fa-filter cl"></span></span>
					<div class="filterCtnr cl">
						<div class="fTitle">Filters</div>
						<div class="iGroup fw dGroup cl">
							<label>Date</label>
							<input type="text" class="cl" onclick="$(this).next().show().parent().addClass('act');" name="date_users" readonly>
							<div id="calendar4" class="calendar-el" data-multiple="1"></div>
						</div>
						<div class="cashGroup cl">
							<button type="button" class="btn btnSubmit ac cl" onclick="analytics.new_users(this); $(this).parents('.filterCtnr').hide();">OK</button>
							<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div id="users_plot"></div>
		</div>
		
		<div class="pnl lPnl">
			<div class="pnlTitle cl ttl" onclick="setFullStat(event, 4);">Website visitors
				<div class="filters cl">
					<span class="hnt hntTop cl" data-title="Filter" onclick="filters.show(this);"id="visitors_filters"><span class="fa fa-filter cl"></span></span>
					<div class="filterCtnr cl">
						<div class="fTitle">Filters</div>
						<div class="iGroup fw dGroup cl">
							<label>Date</label>
							<input type="text" class="cl" onclick="$(this).next().show().parent().addClass('act');" name="date_visitors" readonly>
							<div id="calendar5" class="calendar-el" data-multiple="1"></div>
						</div>
						<div class="cashGroup cl">
							<button type="button" class="btn btnSubmit ac cl" onclick="analytics.site_visitors(this); $(this).parents('.filterCtnr').hide();">OK</button>
							<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div id="site_stat_plot"></div>
		</div>
		
		<div class="pnl rPnl">
			<div class="pnlTitle cl ttl">Visitors online</div>
			<div id="visitors_online">
				<span>0</span>
			</div>
		</div>
	</div>
	
	<div class="pnl fw lPnl cl">
	[dvi]
		<div class="pnlTitle cl">{lang=Online}</div>
		<ul class="uiGroup uOnline cl">
			{works}
		</ul>
	[not-dvi]
		<div class="pnlTitle cl">{lang=Online}</div>
		<ul class="uiGroup uOnline cl">
			{works}
		</ul>
	[/dvi]
	</div>
</div>

<script>
	var issTabs = (a,b) => {
		$('body > header').addClass('main-loader');
		$(b).find('span').removeClass('active');
		var st = $(a.target).addClass('active').data('status');
		$.getJSON('/main/new_issues?status='+st, function(a){
			if(a.issues) $('#issues_list').html(a.issues);
			$('body > header').removeClass('main-loader');
		});
	};
	$(function() {
	
		$('.tab-cnt').mousewheel(function(e, delta) {
			this.scrollLeft -= (delta * 40);
			e.preventDefault();
		});
		
		online_users = setInterval(analytics.online_users, 300000);
				
				
		$('#activity .tabs').click(function(event) {
			if ($(event.target).attr('data-value')) {
				$('.cf').hide();
				$('.if').hide();
				$('.os').hide();
				$('.app').hide();
				$('.filters.ap').show();
				$('#staff').show();
				$('#staff_label').html('Staff');
				switch ($(event.target).attr('data-value')) {
					case 'cash':
						if (!$('#cash_stat_list').html().trim().length)
							Dashboard.cash_stat();
						$('.cf').show();
					break;
					
					case 'issues':
					case 'my_issues':
					case 'all_issues':
					case 'store_issues':
					case 'object_issues':
						$('.if').show();
						$('#object').show();
						if ($(event.target).attr('data-value') == 'my_issues') {
							$('#staff').hide();
							if (!$('#my_issues_list').html().trim().length)
								Dashboard.my_issues();
						} else if ($(event.target).attr('data-value') == 'store_issues')
							$('.filters.ap').hide();
						else if ($(event.target).attr('data-value') == 'object_issues' && !$('#object_issues_list').html().trim().length)
							Dashboard.object_issues();
						else
							$('#staff_label').html('Assigned to');
					break;
					
					case 'appointments':
						if (!$('#tbl_appointments').html().trim().length)
							Dashboard.appointments();
						$('.app').show();
					break;
					
					[time-money]
					case 'pos':
					case 'store_report':
					case 'drops_report':
						$('#staff').hide();
						if (!$('#report').html().length) {
							objects.report(null, 1);
							[owner]Dashboard.drops();[/owner]
						}
						if ($(event.target).attr('data-value') == 'drops_report')
							$('.dr').show();
					break;
					[/time-money]
					
					case 'confirm':
						$('.filters.ap').hide();
						if ($('#conf_inventory').html().length < 10)
							Dashboard.uncofirmed();
					break;
					
					case 'issue_transfer':
						$('.filters.ap').hide();
						if ($('#issue_transfer_list').html().length < 10)
							Dashboard.issue_transfer();
					break;
					
					case 'onsite':
					case 'onsite_details':
					case 'onsite_invoices':
					case 'onsite_unconfirmed':
						$('.os').show();
						if ($(event.target).attr('data-value') == 'onsite_details' || $(event.target).attr('data-value') == 'onsite_invoices')
							$('.payment').hide();
						if ($(event.target).attr('data-value') == 'onsite_unconfirmed')
							$('.os').hide();
						if ($('#onsite_list').html().length < 10)
							Dashboard.onsite();
					break;
					
					case 'purchases':
						$('#object').show();
						Dashboard.purchases();
					break;
					
					case 'timer':
						if (!$('#timers_list').html().trim().length)
							Dashboard.timer();
					break;
					
					case 'feedbacks':
						if (!$('#feedbacks_list').html().trim().length)
							Dashboard.feedbacks();
					break;
					
					case 'tasks':
						$('.ts').show();
						Dashboard.tasks();
					break;
				}
			}
		})
		
		//if (!$('#issues_list').html().trim().length)
			//Dashboard.issues();
			Dashboard.issues();
							
		analytics.sales();
		analytics.points();
		analytics.feedback();
		analytics.new_users();
		analytics.site_visitors();
		
		if ($('.calendar-el').length) {
			var arr = [];
			$('.calendar-el').each(function(i, v) {
				arr[i] = '#' + $(v).attr('id');
				
			});
			calendar.init({el: arr});
		}
		
		//if (is_mobile())
			//$('#activity').remove();
		$.getJSON('/camera/main', function(r){
			clearInterval(camtimer);
			camtimer = setInterval(function(){
				var i = Math.floor(Math.random() * r.length);
				if($('.info-camera-main').is(":hover")){
					console.log('hover');
					return;
				} else
					set_cam(r[i]);
			}, 10000);
			set_cam(r[Math.floor(Math.random() * r.length)]);
		});
	});
	function set_cam(j){
		$('#camera-comment').html('<a href="/users/view/'+j.user_id+'" onclick="Page.get(this.href); return false;">\
			<img src="'+(j.image ? '/uploads/images/users/'+j.user_id+'/thumb_'+j.image : '/uploads/images/users/1703/58b60b26b42cb6.03351607.jpg')+'" align="left">\
			'+j.name+' '+j.lastname+'\
		</a>\
		<span class="hnt hntTop" data-title="Go to the dialogue">\
			<span class="fa fa-exclamation-circle" onclick="Page.get(\'/im/'+j.user_id+'?text=Camera;'+j.id+'\')"></span>\
		</span>\
		<p>'+j.camera_event+'</p>').show();
	}
				
	function getFilters(issues) {
		var object= $('select[name="object'+(issues ? '_issues_select' : '')+'"]').val() || 0,
			staff = Object.keys($((issues ? '.issues_filters ' : '.global_filters') + 'input[name="staff"]').data() || {}).join(',') || 0,
			page = $('#activity .tUl li.active').attr('data-value');
		
		if (issues) {
			Page.get('/activity/issues/' + 
				'?sDate=' + $('#ifCalendar > input[name="date"]').val() +
				'&eDate=' + $('#ifCalendar > input[name="fDate"]').val() +
				'&object=' + object +
				'&name=' + (object ? $('select[name="object_issues_select"] > option[value="'+object+'"]').text() : '') +
				'&staff=' + staff +
				'&staff_name=' + (staff ? $('input[name="staff"]').data()[staff].name : '') +
				'&type=' + $('.issues_filters select[name="type"]').val() +
				'&status=' + $('.issues_filters select[name="status"]').val() +
				'&current_status=' + $('.issues_filters select[name="status_current"]').val() +
				'&action=' + $('.issues_filters select[name="action"]').val() +
				'&payment=' + $('.issues_filters select[name="payment"]').val() +
				'&pickedup=' + $('.issues_filters input[name="pickedup"]').val() +
				'&all=' + $('.issues_filters input[name="all"]').val() +
				'&status_issues=' + $('.issues_filters select[name="status_issues"]').val()
			);
		} else {
			switch (page) {
				case 'purchases':
					Page.get('/purchases' + 
							'?date_start=' + $('#fCalendar > input[name="date"]').val() +
							'&date_finish=' + $('#fCalendar > input[name="fDate"]').val() +
							'&staff=' + staff +
							'&staff_name=' + (staff ? $('input[name="staff"]').data()[staff].name : '') +
							'&object=' + object +
							'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '')
						);
				break;
				
				case 'pos':
				case 'store_report':
				case 'drops_report':
					if ($('#pos .tUl li.active').attr('data-value') != 'drops_report') {
						Page.get('/objects/report' + 
							'?sDate=' + $('#fCalendar > input[name="date"]').val() +
							'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
							'&object=' + object +
							'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '')
						);
					} else {
						Page.get('/cash/drops' + 
							'?sDate=' + $('#fCalendar > input[name="date"]').val() +
							'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
							'&object=' + object +
							'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '')
						);
					}
				break;
				
				case 'tasks':
					Page.get('/tasks' + 
						'?sDate=' + $('#fCalendar > input[name="date"]').val() +
						'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
						'&staff=' + staff +
						'&staff_name=' + (staff ? $('input[name="staff"]').data()[staff].name : '') +
						'&object=' + object +
						'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '')
					);
				break;
				
				case 'onsite':
				case 'onsite_customers':
				case 'onsite_details':
				case 'onsite_invoices':
					if ($('#onsite .tUl li.active').attr('data-value') != 'onsite_customers') {
						Page.get(($('#onsite .tUl li.active').attr('data-value') == 'onsite_invoices' ? '/invoices/' : '/activity/onsite_details') + 
							'?sDate=' + $('#fCalendar > input[name="date"]').val() +
							'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
							'&staff=' + staff +
							'&staff_name=' + (staff ? $('input[name="staff"]').data()[staff].name : '') +
							'&object=' + object +
							'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '') +
							($('#onsite .tUl li.active').attr('data-value') == 'onsite_invoices' ? '&action=yes' : '')
						);
					} else {
						Page.get('/activity/onsite' + 
							'?sDate=' + $('#fCalendar > input[name="date"]').val() +
							'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
							'&staff=' + staff +
							'&staff_name=' + (staff ? $('input[name="staff"]').data()[staff].name : '') +
							'&payment=' + $('select[name="payment"]').val() +
							'&object=' + object +
							'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '')
						);
					}
				break;
				
				default:
					Page.get('/activity/' + (page != 'act' ? (page == 'my_issues' ? 'issues' : (page == 'object_issues' ? 'issues/store' : page)) : '') + 
						'?sDate=' + $('#fCalendar > input[name="date"]').val() +
						'&eDate=' + $('#fCalendar > input[name="fDate"]').val() +
						'&object=' + object +
						'&name=' + (object ? $('select[name="object"] > option[value="'+object+'"]').text() : '') +
						'&staff=' + (page == 'my_issues' ? _user.id : staff) +
						'&staff_name=' + (page == 'my_issues' ? _user.name + ' ' + _user.lastname : (staff ? $('input[name="staff"]').data()[staff].name : '')) +
						'&type=' + $('select[name="type"]').val() +
						'&status=' + $('select[name="status"]').val() +
						'&current_status=' + $('select[name="status_current"]').val() +
						'&action=' + $('select[name="action"]').val() +
						'&payment=' + $('select[name="payment"]').val() +
						'&pickedup=' + $('input[name="pickedup"]').val() +
						'&all=' + $('input[name="all"]').val() +
						'&status_issues=' + $('select[name="status_issues"]').val()
					);
				break;
			}
		}
	} 
	
		
	function clearDate() {
		$('.calendar > input[name="date"]').val('');
		$('.calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
		$('.filterCtnr').hide();
	}
	
	function setFullStat(e, index) {
		if ($('#stats').hasClass('toFull' + index) && $(e.target).hasClass('ttl'))
			$('#stats').removeClass('toFull' + index);
		else
			$('#stats').attr('class', 'toFull' + index);
	}
	
	
	var filters = {
		last: '',
		show: function(e) {
			$(e).next().toggle();
			
			if ($(e).attr('id') != filters.last && $(e).next().is(':visible')) {
				if (filters.last) {
					
					$('#' + filters.last).next().hide();
					
					/*$('#' + filters.last).next().find('.calendar *').each(function() {
						$(this).off("click");
					});
					
					$('#' + filters.last).next().find(".calendar").each(function() {
						$(this).html('');
					});*/
				}
				
				filters.last = $(e).attr('id');
				
				/*if ($(e).next().find('.calendar-el').length) {
					var calid = $(e).next().find('.calendar-el').attr('id');
					$('#' + calid).calendar(function() {
						$('#' + calid).prev().val($('#' + calid + ' > input[name="date"]').val() + ' / ' + $('#' + calid + ' > input[name="fDate"]').val());
						$('.dGroup > input + div').hide().parent().removeClass('act');
					});
				}*/
				
				var objid = $(e).parent().find('.object-el').attr('id'),
					stid = $(e).parent().find('.staff-el').attr('id')
				
				/*$.post('/invoices/objects', {}, function (r) {
					if (r){
						if (r.list.length > 1) {
							var items = '', lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
								lId = v.id;
							});
							$(e).parent().find('.object-el > ul').html(items).sForm({
								action: '/invoices/objects',
								data: {
									lId: lId,
									query: $(e).parent().find('.object-el > .sfWrap input').val() || ''
								},
								all: (r.count <= 20) ? true : false,
								select: $(e).parent().find('.object-el > input[name="'+objid+'"]').data(),
								s: true
							}, $(e).parent().find('.object-el > input[name="'+objid+'"]'));

						} else if (r.list.length == 1) {
							var cash_object = {};
							cash_object[r.list[0].id] = {
								name: r.list[0].name
							}
							$(e).parent().find('.object-el > input[name="'+objid+'"]').data(cash_object);
							$(e).parent().find('.object-el').append($('<div/>', {
								class: 'storeOne',
								html: r.list[0].name
							}));
						} else {
							$(e).parent().find('.object-el').hide();
						}
					}
				}, 'json');*/
				
				$.post('/users/all', {staff: 1}, function (r) {
					if (r){
						var items = '', lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#'+stid+' > ul').html(items).sForm({
							action: '/users/all',
							data: {
								staff: 1,
								lId: lId,
								query: $('#'+stid+' > .sfWrap input').val() || ''
							},
							all: false,
							select: $('input[name="'+stid+'"]').data(),
							s: true
						}, $('input[name="'+stid+'"]'));
					}
				}, 'json');
			}
		}
	}
	
</script>