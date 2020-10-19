
<div class="ctnr">
	<nav class="breadcrumbs">
		<a href="/" onclick="Page.get(this.href); return false;">Main</a> >
		<b>{name}</b>
	</nav>
</div>
	
<div class="page" id="page-content">
	{content}
</div>

<div class="stores-near">
	<div class="sn-title">Store near you</div>
	<div class="sn-search flex">
		<input name="search" placeholder="Enter your zip or address" onkeyup="if (event.keyCode == 13) nearStore(this.value);">
		<span class="fa fa-search" onclick="nearStore($(this).prev().val());"></span>
	</div>
</div>


<div id="map"></div>

<div class="ctnr">
	<div class="services">
		<div class="se-title">Services</div>
		<div class="services-list flex">
			<a href="/support/on-site-technical-support" onclick="Page.get(this.href); return false;">On Site Technical Support and Computer Repair</a>
			<a href="/it-infrastructure-management" onclick="Page.get(this.href); return false;">IT Infrastructure Management & Support</a>
			<a href="/business-services/network-cable-installation" onclick="Page.get(this.href); return false;">Network Cable Installation Albany, Brooklyn & More</a>
			<a href="/computer-training" onclick="Page.get(this.href); return false;">Computer / Tech Training</a>
			<a href="/it-support-specialist" onclick="Page.get(this.href); return false;">IT Specialist Support Albany, NY</a>
			<a href="/network-design-and-setup" onclick="Page.get(this.href); return false;">Network Support / Maintenance Albany, Brooklyn & More</a>
			<a href="/business-services/off-site-data-backups" onclick="Page.get(this.href); return false;">Off Site Data Backups</a>
			<a href="/business-services/pos-software-development" onclick="Page.get(this.href); return false;">POS Software Development</a>
			<a href="sql-database-recovery" onclick="Page.get(this.href); return false;">SQL Database Recovery Albany, Brooklyn & More</a>
			<a href="/raid-data-recovery" onclick="Page.get(this.href); return false;">RAID Data Recovery Albany & Brooklyn</a>
			<a href="/business-services/cloud-services-for-small-business" onclick="Page.get(this.href); return false;">Cloud Services for Small Business Albany & Brooklyn</a>
		</div>
	</div>
</div>
[owner]
[/owner]