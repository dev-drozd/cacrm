{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		Users profit
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Type}</label>
					<select name="type">
						<option value="0">All</option>
						<option value="stock">Stock</option>
						<option value="service">Service</option>
						<option value="purchase">Purchase</option>
						<option value="tradein">Tradein</option>
					</select>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search2($('input[name=\'searchText\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<!-- <span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span> -->
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">Staff</div>
				<div class="th">Working time</div>
				<div class="th">Profit per hour</div>
				<div class="th">Points per hour</div>
				<div class="th">Amount</div>
				<div class="th">Details</div>
			</div>
		</div>
		<div class="tBody userList">
			{profit}
		</div>
	</div>
	{include="doload.tpl"}
</div>
<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
	});
</script>