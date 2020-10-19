{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=OnSiteServices}
		[create]<a href="/inventory/onsite/add" class="btn addBtn" onclick="Page.get(this.href); return false;">New onsite</a>[/create]
<!-- 		<div class="filters">
			<span class="fa fa-filter" onclick="$(this).next().toggle();"></span>
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
				[stock]
				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="0">All</option>
						<option value="internal">Internal</option>
						<option value="external">External</option>
					</select>
				</div>
				[/stock]
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'search\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div> -->
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="Services search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{services}
	</div>
	{include="doload.tpl"}
</section>