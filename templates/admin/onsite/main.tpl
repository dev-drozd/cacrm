<style>.userList{width: auto;display: table-row-group;}</style>
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Onsite services
	</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Search onsite" oninput="Search2(this.value)" onkeypress="if(event.keyCode == 13) Search2(this.value);" oninput="checkBarcode(this.value);" value="{query}">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">Search <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>#</th>
				<th>Staff</th>
				<th>Customer</th>
				<th>Service</th>
				<th>Price</th>
				<th>Date of service</th>
				<th>Date create</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody class="userList">
			{onsites}
		</tbody>
	</table>
	{include="doload.tpl"}
</section>