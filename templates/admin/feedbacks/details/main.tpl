<div class="pnl fw lPnl">
	<div class="pnlTitle">
		FEEDBACK FROM STAFFS
	</div>
	<div id="calendar" hidden>
		<input type="date" name="date" value="{date-start}">
		<input type="date" name="fDate" value="{date-finish}">
		<input type="number" name="all" value="{staff-id}">
	</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>ID</th>
				<th>Staff</th>
				<th>Customer</th>
				<th>Phone</th>
				<th>Rating</th>
				<th>Comment</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody class="userList">
			{feedbacks}
		</tbody>
	</table>
	{include="doload.tpl"}
</div>
<style>
.userList {
    display: contents;
    width: 100%;
}
.nFb > td {
	background: #fffad0 !important;
}
.wFb > td {
	background: #c7ffc7 !important;
}
.pFb > td {
	background: #ffdada !important;
}
.act-btn {
	padding: 0 10px !important;
}
.act-btn:first-child, .act-btn:first-child:hover {
	color: #d04646;
}
.userList > tr > .usrt {
	line-height: 50px;
}
</style>