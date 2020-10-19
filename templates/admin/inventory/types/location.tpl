{include="inventory/types/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=editLocation} 
	[default]<a href='javascript:inventory.openStatus({id}, {point-group}, "{name}", {forfeit}, {sms}, {smsForm}, 0, {percent})' class="eBtn"><span class="fa fa-pencil"></span></a>[/default]</div>
	<form class="uForm" method="post" onsubmit="inventory.sendLocation(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}" disabled />
		</div>
		[forfeit]
		[not-forfeit]
		<div class="iGroup">
			<label>{lang=Points}</label>
			<table class="tPoints">
				<thead>
					<tr>
						<th>{lang=Time}</th>
						<th>{lang=Points}</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		[/forfeit]
		<!-- <div class="iGroup sfGroup" id="service">
			<label>Required {lang=Service}:</label>
			<input type="hidden" name="service" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="inventories">
			<label>Required {lang=Inventory}:</label>
			<input type="hidden" name="inventories" />
			<ul class="hdn"></ul>
		</div> -->
		<div class="iGroup sfGroup">
			<label>Required {lang=Service}:</label>
			<input type="checkbox" name="service" [rservice]checked[/rservice]/>
		</div>
		<div class="iGroup sfGroup">
			<label>Required Parts:</label>
			<input type="checkbox" name="inventories" [rinventories]checked[/rinventories]/>
		</div>
		<div class="iGroup sfGroup">
			<label>Confirm purchase:</label>
			<input type="checkbox" name="purchase" [purchase]checked[/purchase]/>
		</div>
		<div class="iGroup sfGroup">
			<label>Assigned:</label>
			<input type="checkbox" name="assigned" [assigned]checked[/assigned]/>
		</div>
		<div class="iGroup sfGroup">
			<label>Remove purchase:</label>
			<input type="checkbox" name="remove_purchase" [remove_purchase]checked[/remove_purchase]/>
		</div>
		<div class="iGroup sfGroup">
			<label>Required note:</label>
			<input type="checkbox" name="note" [note]checked[/note]/>
		</div>
		<div class="iGroup sfGroup" id="object">
			<label>{lang=Objects}:</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup object showObject" id="location">
			<label>{lang=Locations}:</label>
			<input type="hidden" name="location" />
			<ul class="hdn"></ul>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
$(function() {
	$.each({point-group}, function(i, v) {
		$('.tPoints > tbody').append($('<tr/>', {
			html: $('<td/>', {
				html: i
			})
		}).append($('<td/>', {
			html: v
		})));
	});
	$('input[name="location"]').data({locations});
	getLocList();
	
	$.post('/objects/all', {}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
				lId = v.id;
			});
			$('#object > ul').html(items).sForm({
				action: '/objects/all',
				data: {
					lId: lId,
					query: $('#object> .sfWrap input').val() || ''
				},
				all: (r.count <= 20) ? true : false,
				select: $('input[name="object"]').data(),
				s: true
			}, $('input[name="object"]'), function() {
				getLocList(Object.keys($('input[name="object"]').data()).join(','));
			});
		}
	}, 'json');
});

function getLocList(e, f) {	
	$.post('/objects/get_locations', {nIds: Object.keys($('input[name="location"]').data()).join(','), oId: e || 0}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '" data-object="' + v.object + '" data-objectid="' + v.object_id + '">' + v.name + '</li>';
					lId = v.id;
			});
			if (e) {
				$('input[name="location"]').next().remove();
				$('input[name="location"]').after($('<ul/>'));
			}
			$('#location > ul').html(items).sForm({
				action: '/objects/get_locations',
				data: {
					lId: lId,
					oId: e || 0,
					nIds: Object.keys($('input[name="location"]').data() || {}).join(','),
					query: $('#location input').val() || ''
				},
				all: (r.count <= 20) ? true : false,
				select: $('input[name="location"]').data()
			}, $('input[name="location"]'), function() {

			}, {
				oId: 'object',
				nIds: 'location'
			});
			$('#location .sfWrap > div > span').hide('fast', function() {
				$('#location .sfWrap > div > span[data-objectid="' + e + '"]').show();
			});
		}
	}, 'json');
}
</script>