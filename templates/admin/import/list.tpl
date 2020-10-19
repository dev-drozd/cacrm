{include="analytics/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Saved files
        <a href="/import" onclick="Page.get(this.href); return false" class="btn addBtn">Import data</a>
	</div>
	
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">File name</div>
				<div class="th">Uploaded deals</div>
				<div class="th">Period</div>
				<div class="th">Saved date</div>
				<div class="th">Details</div>
			</div>
		</div>
		<div class="tBody userList">
			{import}
		</div>
	</div>
	
    <!-- <div class="saved_files">
        <div>
            <span>file name.csv</span>
            <span>Uploaded deals: 123</span>
            <span>Period: 01-01-2017 - 01-31-2017</span>
            <span><a href="#">Details</a></span>
        </div>
    </div> -->
</div>