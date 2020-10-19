{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		{lang=FeedbackAnalytics}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="analytics.feedback(this); $(this).parents('.filterCtnr').hide();">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
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

<div class="mngContent">
	<div class="pnlTitle cl">{lang=StoreInfo}
	</div>
	<div id="freport"></div>
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