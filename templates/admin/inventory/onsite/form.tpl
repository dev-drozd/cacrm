{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	[edit][notconfirmed]
	<div class="mt dClear">
		{lang=servNotConf} <a href="#" class=" btnConfirmed" onclick="inventory.confirmOnsite({id}, this); return false;">{lang=Confirm}</a>
	</div>
	[/notconfirmed][/edit]
	<form class="uForm" method="post" onsubmit="inventory.addOnsite(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}" />
		</div>
		<div class="iGroup">
			<label>{lang=Description}</label>
			<textarea name="desc">{desc}</textarea>
		</div>
		<div class="iGroup">
			<label>{lang=Type}</label>
			<select name="type">
				<option value="call">{lang=OnCall}</option>
				<option value="hour">{lang=Hour}</option>
				<option value="week">{lang=Week}</option>
				<option value="month">{lang=Month}</option>
				<option value="year">{lang=Year}</option>
			</select>
		</div>
		<div class="iGroup onsite_time">
			<label>{lang=Time}, h</label>
			<input type="number" name="time" value="{time}" step="0.001" />
		</div>
		<div class="iGroup onsite_calls">
			<label>{lang=Calls}</label>
			<input type="number" name="calls" value="{calls}" />
		</div>
		<div class="iGroup">
			<label>{lang=Price}</label>
			<input type="number" name="price" value="{price}" step="0.001" />
		</div>
		<div class="iGroup">
			<label>{lang=Currency}</label>
			<select name="currency">
				{currency}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=addHourPay}</label>
			<input type="number" name="add_hour" value="{add_hour}" step="0.001" />
		</div>
		<div class="iGroup" id="store">
			<label>{lang=Object}</label>
			<input type="hidden" name="store" />
			<ul class="hdn"></ul>
		</div>
		<!-- <div class="iGroup cl dGroup">
			<label class="cl">{lang=StartPeriod}</label>
			<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="start_period" value="{start_period}" />
			<div id="sCalendar"></div>
		</div>
		<div class="iGroup cl dGroup">
			<label class="cl">{lang=EndPeriod}</label>
			<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="end_period" value="{end_period}" />
			<div id="eCalendar"></div>
		</div> -->
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	/* function changeType() {
		if ($('select[name="type"]').val() == 'call') {
			$('.onsite_time').hide();
			$('.onsite_calls').show();
		} else {
			$('.onsite_time').show();
			$('.onsite_calls').hide();
		}
	} */
	
	$(function() {
		$('input[name="store"]').data({objects});
		$('select[name="type"]').val('{type}').trigger('change');
		$.post('/objects/all', {nIds: Object.keys($('input[name="store"]').data() || {}).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				
				$('#store > ul').html(items).sForm({
					action: '/objects/all',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="store"]').data() || {}).join(','),
						query: $('#store > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="store"]').data()
				}, $('input[name="store"]'));
			}
		}, 'json');
	});
</script>