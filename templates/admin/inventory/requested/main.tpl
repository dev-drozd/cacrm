{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=allRequests}
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw">
					<label>Status</label>
					<select name="status">
						<option value="0">All</option>
						<option value="confirmed">Confirmed</option>
						<option value="notconfirmed">Not confirmed</option>
					</select>
				</div>
				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="0">All</option>
						<option value="type">Types</option>
						<option value="brand">Brands</option>
						<option value="service">Services</option>
					</select>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'search\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
		<a href="#" class="btn addBtn" onclick="inventory.addRequest(); return false;">Add request</a>
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="Requests search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{requests}
	</div>
	{include="doload.tpl"}
</section>