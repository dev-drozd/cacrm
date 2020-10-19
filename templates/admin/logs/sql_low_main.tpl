{include="logs/menu.tpl"}

<div class="mngContent">
	<div class="pnlTitle">
		SQL low list
		<span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span>
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="{lang=activitySearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<table class="responsive" style="table-layout: fixed;">
		<thead>
			<tr>
				<th>{lang=Staff}</th>
				<th>Time</th>
				<th>SQL</th>
				<th>URL</th>
				<th>{lang=Date}</th>
			</tr>
		</thead>
		<tbody class="userList">
			{logs}
		</tbody>
	</table>
	{include="doload.tpl"}
</div>
<style>
td > code {
    display: block;
    height: 100px;
    overflow: hidden;
    transition: height .3s;
    max-height: max-content;
}
td > code.open {
    height: 1500px;
}
.userList {
    display: table-row-group !important;
}
</style>