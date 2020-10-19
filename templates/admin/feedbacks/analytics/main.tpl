{include="feedbacks/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Feedback analytics
		<div class="filters">
			<span class="hnt hntTop" data-title="Filters" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw dGroup">
					<label>Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="analytics.feedback(this); $(this).parents('.filterCtnr').hide();">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<div class="tabs">
		<div class="tab" data-title="Total count" id="total_count">
			<div id="feedbacks_plot"></div>
		</div>
		<div class="tab" data-title="Middle value" id="middle_value">
			<div id="feedback_by_day"></div>
		</div>
	</div>
</div>

<div class="pnl fw lPnl">
	<div class="pnlTitle cl">Feedback from stores</div>
	<div id="freport"></div>
</div>

<div class="pnl fw lPnl">
	<div class="pnlTitle cl">Feedback from staffs</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>Staff</th>
				<th>Amount of feedback</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody id="sreport">
		</tbody>
	</table>
</div>
<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('.iGroup > input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.iGroup > input + div').hide();
		});
		
		analytics.feedback();
		$('#page').tabs();
		
		$('[data-value="middle_value"]').click(function() {
			if (!$('#feedback_by_day').html().trim().length)
				analytics.feedback_by_day();
		})
	});
</script>