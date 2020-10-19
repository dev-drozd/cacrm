<div class="repObj" id="eo_{id}" data-id="{id}">
	<div class="roName">{name}</div>
	<div class="roMoney">
		Transactions: {count}<br>
		Tax: <span class="blue">{tax}</span><br>
		Total: <span class="{tclass}">{total}</span><br>
		Cash: <span class="{cclass}">{cash}</span><br>
		Cash adjustments: <span class="{lclass}">{lack}</span><br>
		Credit: <span class="{crclass}">{credit}</span><br>
		Check: <span class="{chclass}">{check}</span><br>
		Purchases: <span class="blue">${purchases_total}</span><br>
		Tradeins: <span class="blue">${tradeins_total}</span><br>
		{expanses}
		Salary: <span class="blue">${salary_total}</span>
	</div>
	<div class="roFilters">
		<!-- <div class="iGroup w50 fw">
			<label>Report type</label>
			<select name="report_type" onchange="showST(this);">
				<option value="issue_status">Issue status changed</option>
				<option value="transactions">Transactions</option>
				<option value="status_issues">Filter by status</option>
			</select>
		</div> -->
		<div class="iGroup w50 fw">
			<label>Status</label>
			<select name="status">
				{statuses}
			</select>
		</div>
		<div class="dClear"></div>
		<div class="sGroup">
			<!-- <button class="btn btnSubmit" onclick="objects.miniReport(this, $('#eo_{id} select[name=\'report_type\']').val(), {id});">Show report</button> -->
			<button class="btn btnSubmit" onclick="objects.miniReport(this, 'status_issues', {id});">Show report</button>
		</div>
	</div>
	
			{timer}
			{status_changes}
			{transactions}
			{purchases}
			{tradeins}
			{salary}
	<div id="reps_{id}"></div>
</div>