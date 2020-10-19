<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Feedbacks of jobs
	</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>Issue</th>
				<th>Customer</th>
				<th>Phone</th>
				<th>Rating</th>
				<th>Comment</th>
				<th>Job date</th>
				<th>Feedback date</th>
				<th align="center">Action</th>
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
.miniRound {
	float: none;
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
</style>