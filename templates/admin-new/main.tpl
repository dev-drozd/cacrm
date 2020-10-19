<div class="flex-row">
	<div class="col-8">
		<div class="block">
		  <header>
			<h3><span class="fa fa-dashboard"></span> Manage</h3>
		  </header>
		  <div class="main-search flex">
			<select id="main-search-type" name="sGroup" style="width: 150px;">
				<option value="us">Users</option>
				<option value="ob">Stores</option>
				<option value="de">Stock&amp;Service</option>
				<option value="pu">Purchases</option>
				<option value="is">Jobs</option>
				<option value="in">Invoices</option>
			</select>
			<input type="text" placeholder="search">
			<button><span class="fa fa-search"></span></button>
		  </div>
		  
		  <div class="info-camera-main"></div>
		  <div class="flex main-panel">
			[users]
			<a href="/users/lite_step" onclick="Page.get(event);">
				<i class="fa fa-user-plus" style="color:#47aebb82"></i>
				New customer
			</a>
			[/users]
			<a href="/inventory/add/inventory" onclick="Page.get(event);">
				<i class="fa fa-laptop" style="color: #4ec0ce;"></i>
				New inventory
			</a>
			<a href="/inventory/add/service" onclick="Page.get(event);">
				<i class="fa fa-list" style="color: #299CCE;"></i>
				New service
			</a>
			[invoces]
			<a href="/invoices/add" onclick="Page.get(event);">
				<i class="fa fa-credit-card" style="color:#36b1e6"></i>
				Make purchase
			</a>
			[/invoces]
			[purchase]
			<a href="/purchases/add" onclick="Page.get(event);">
				<i class="fa fa-cart-plus" style="color:#df9e63"></i>
				Purchase request
			</a>
			[/purchase]
			<a href="#" onclick="inventory.addRequest(); return false;">
				<i class="fa fa-bullhorn" style="color: #df8163;"></i>
				New request
			</a>
			[invoces]
			<a href="/invoices/add?estimate" onclick="Page.get(event);">
				<i class="fa fa-thumbs-o-up" style="color:#df7563"></i>
				Estimate
			</a>
			[/invoces]
			[invoces]
			<a href="/invoices/add?quicksell" onclick="Page.get(event);">
				<i class="fa fa-shopping-bag" style="color: #e45c5c;"></i>
				Quick sell
			</a>
			[/invoces]
			<a href="#" onclick="user.newOnsite(); return false">
				<i class="fa fa-truck" style="color: #A2CE4E;"></i>
				New onsite
			</a>
			<a href="/users/appointment" onclick="Page.get(event);">
				<i class="fa fa-handshake-o" style="color: #7ece4e;"></i>
				New appointment
			</a>
			[add-invoice]
			<a href="/invoices/add" onclick="Page.get(event);">
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
		<div class="block dream-team">
			<header>
				<h3>Dream team</h3>
			</header>
			<div class="flex">{top-stafs}</div>
		</div>
	</div>
	<div class="col-4 block">
	  <header>
		<h3>Jobs <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <table></table>
	</div>
</div>

<div class="flex-row">
   <div onclick="analytics.show(event)" class="col-6 block">
	  <header>
		<h3>{lang=Sales} <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_stat" readonly>
	 <div id="sales_graph"></div>
   </div>
   <div onclick="analytics.show(event)" class="col-6 block">
	  <header>
		<h3>{lang=Efficiency} <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_points" readonly>
	  <input type="hidden" name="user_points">
	  <div id="efficiency_graph"></div>
   </div>
</div>

<div class="flex-row">
   <div onclick="analytics.show(event)" class="col-6 block">
	  <header>
		<h3>Feedbacks <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_feedbacks" readonly>
	  <div id="feedbacks_plot"></div>
   </div>
   <div onclick="analytics.show(event)" class="col-6 block">
	  <header>
		<h3>New customers <span id="total_customers"></span> <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_users" readonly>
	  <div id="users_plot"></div>
   </div>
</div>

<div class="flex-row">
   <div onclick="analytics.show(event)" class="col-6 block">
	  <header>
		<h3>Website visitors <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_visitors" readonly>
	  <div id="site_stat_plot"></div>
   </div>
   <div class="col-6 block">
	  <header>
		<h3>Visitors online</h3>
	  </header>
	  Some Content
   </div>
</div>


<!-- START MANAGE BLOCK -->
<!-- <div class="flex blocks">
   <div style="width: 60%; flex: 2;">
	  <header>
		<h3><span class="fa fa-dashboard"></span> Manage</h3>
	  </header>
	  <div class="main-search flex">
		<select id="main-search-type" name="sGroup" style="width: 150px;">
			<option value="us">Users</option>
			<option value="ob">Stores</option>
			<option value="de">Stock&amp;Service</option>
			<option value="pu">Purchases</option>
			<option value="is">Jobs</option>
			<option value="in">Invoices</option>
		</select>
		<input type="text" placeholder="search">
		<button><span class="fa fa-search"></span></button>
	  </div>
	  
	  <div class="info-camera-main"></div>
	  <div class="flex main-panel">
		[users]
		<a href="/users/lite_step" onclick="Page.get(event);">
			<i class="fa fa-user-plus" style="color:#47aebb82"></i>
			New customer
		</a>
		[/users]
		<a href="/inventory/add/inventory" onclick="Page.get(event);">
			<i class="fa fa-laptop" style="color: #4ec0ce;"></i>
			New inventory
		</a>
		<a href="/inventory/add/service" onclick="Page.get(event);">
			<i class="fa fa-list" style="color: #299CCE;"></i>
			New service
		</a>
		[invoces]
		<a href="/invoices/add" onclick="Page.get(event);">
			<i class="fa fa-credit-card" style="color:#36b1e6"></i>
			Make purchase
		</a>
		[/invoces]
		[purchase]
		<a href="/purchases/add" onclick="Page.get(event);">
			<i class="fa fa-cart-plus" style="color:#df9e63"></i>
			Purchase request
		</a>
		[/purchase]
		<a href="#" onclick="inventory.addRequest(); return false;">
			<i class="fa fa-bullhorn" style="color: #df8163;"></i>
			New request
		</a>
		[invoces]
		<a href="/invoices/add?estimate" onclick="Page.get(event);">
			<i class="fa fa-thumbs-o-up" style="color:#df7563"></i>
			Estimate
		</a>
		[/invoces]
		[invoces]
		<a href="/invoices/add?quicksell" onclick="Page.get(event);">
			<i class="fa fa-shopping-bag" style="color: #e45c5c;"></i>
			Quick sell
		</a>
		[/invoces]
		<a href="#" onclick="user.newOnsite(); return false">
			<i class="fa fa-truck" style="color: #A2CE4E;"></i>
			New onsite
		</a>
		<a href="/users/appointment" onclick="Page.get(event);">
			<i class="fa fa-handshake-o" style="color: #7ece4e;"></i>
			New appointment
		</a>
		[add-invoice]
		<a href="/invoices/add" onclick="Page.get(event);">
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
	<div class="block">
	  <header>
		<h3>Jobs <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <table></table>
	</div>
</div> -->
<!-- END MANAGE BLOCK -->

<!-- <div class="block block-one">
  <header>
	<h3>Jobs <span class="fa fa-filter h-filter"></span></h3>
  </header>
  <table></table>
</div> -->

<!-- <div class="block dream-team">
  <header>
	<h3>Dream team</h3>
  </header>
  <div class="flex">{top-stafs}</div>
</div> -->

<!-- ANALITIC BLOCKS -->
<!-- <div class="flex blocks">
   <div onclick="analytics.show(event)">
	  <header>
		<h3>{lang=Sales} <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_stat" readonly>
	 <div id="sales_graph"></div>
   </div>
   <div onclick="analytics.show(event)">
	  <header>
		<h3>{lang=Efficiency} <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_points" readonly>
	  <input type="hidden" name="user_points">
	  <div id="efficiency_graph"></div>
   </div>
</div> -->

<!-- <div class="flex blocks">
   <div onclick="analytics.show(event)">
	  <header>
		<h3>Feedbacks <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_feedbacks" readonly>
	  <div id="feedbacks_plot"></div>
   </div>
   <div onclick="analytics.show(event)">
	  <header>
		<h3>New customers <span id="total_customers"></span> <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_users" readonly>
	  <div id="users_plot"></div>
   </div>
</div> -->

<!-- <div class="flex blocks">
   <div onclick="analytics.show(event)">
	  <header>
		<h3>Website visitors <span class="fa fa-filter h-filter"></span></h3>
	  </header>
	  <input type="hidden" name="date_visitors" readonly>
	  <div id="site_stat_plot"></div>
   </div>
   <div>
	  <header>
		<h3>Visitors online</h3>
	  </header>
	  Some Content
   </div>
</div> -->
<!--/ ANALITIC BLOCKS -->

<!-- <div class="flex blocks">
   <div>
	  <header>
		<h3>{lang=Online}</h3>
	  </header>
	  <ul class="working_staff">{works}</ul>
   </div>
</div> -->

<link rel="stylesheet" href="{theme}/css/chart.css">
<script src="{theme}/js/chart.js"></script>
<script src="{theme}/js/main.js"></script>
<script>
$(document).ready(function(){
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
	analytics.sales();
	analytics.points();
	analytics.feedback();
	analytics.new_users();
	analytics.site_visitors();
});
</script>