{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		[edit][next]<a href="/inventory/edit/service/{next}" onclick="Page.get(this.href); return false;" class="btn btnNext">{lang=Next}</a>[/next][/edit]
	</div>
	[edit][notconfirmed]
	<div class="mt dClear">
		{lang=ServiceAreNotConfirmed} <a href="#" class=" btnConfirmed" onclick="inventory.confirmed({id}, this); return false;">{lang=Confirm}</a>
	</div>
	[/notconfirmed][/edit]
	<form class="uForm" method="post" onsubmit="inventory.addInv(this, event, {id});">
		<!--<div class="iGroup" id="type_id">
			<label>{lang=Group}</label>
			<input type="hidden" name="type_id" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="category">
			<label>{lang=Brand}</label>
			<input type="hidden" name="category" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup sfGroup" id="model">
			<label>{lang=Model}</label>
			<input type="hidden" name="model" />
			<ul class="hdn"></ul>
		</div>-->
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}" />
		</div>
		<div class="iGroup">
			<label>{lang=Price}, $</label>
			<input type="number" name="price" value="{price}" step="0.01" min="0" />
		</div>
		<div class="iGroup">
			<label>{lang=Currency}</label>
			<select name="currency">
				{currency}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Time}, min</label>
			<input type="number" name="time" value="{time}" step="0.1" min="0" />
		</div>
		<div class="iGroup">
			<label>{lang=PartsRequire}</label>
			<input type="checkbox" name="parts" [parts]checked[/parts] />
		</div>
		<div class="sTitle">{lang=Progress}</div>
		<span id="steps">{steps}</span>
		<div class="sGroup">
			<button class="btn btnSubmit" type="button" onclick="newStep();">{lang=NewStep}</button>
		</div>

		<div class="sTitle">{lang=StoreInfo}</div>
		<div class="iGroup sfGroup" id="object">
			<label>{lang=Object}</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup">
			<label>{lang=Description}</label>
			<textarea name="descr">{descr}</textarea>
		</div>
		<div class="iGroup sfGroup" id="storeCat">
			<label>{lang=Category}</label>
			<input type="hidden" name="storeCat" />
			<ul class="hdn"></ul>
		</div>		
		<div class="iGroup">
			<label>{lang=ToMain}</label>
			<input type="checkbox" name="main" [main]checked[/main]/>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	var type_id = {type-id};
	
	function newStep() {
		$('#steps').append($('<div/>', {
			class: 'iGroup optGroup sInput',
			html: $('<span/>', {
				class: 'fa fa-bars',
				draggable: true,
				ondragover: 'options.dragover(event)',
				ondragstart: 'options.dragstart(event)',
				ondragend: 'options.dragend(event)',
				onmousedown: '$(this).parent().addClass(\'drag\');',
				onmouseup: '$(this).parent().removeClass(\'drag\');'
			})
		}).append($('<div/>', {
			class: 'sSide fw',
			html: $('<input/>', {
				name: 'oName',
				type: 'text'
			})
		})).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
	}

	/*function getTypes(f) {
		var items = '', lId = 0;
		cat = Object.keys($('input[name="category"]').data()).join(',');
		$.post('/inventory/allTypes', {
			name: 1,
			brand: Object.keys($('input[name="category"]').data()).join(',')
		}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!f) {
					$('#model > input').removeData();
					$('#type_id > input').removeData();
					$('#type_id > input + div').remove();
					$('#type_id > input + ul').remove();
					$('#type_id > input').after($('<ul/>'));
				}
				if (type_id) {
					$('#type_id > input').data(type_id);
					type_id = '';
				}
				$('#type_id > ul').html(items).sForm({
					action: '/inventory/allTypes',
					data: {
						lId: lId,
						query: $('#type_id > .sfWrap input').val() || '',
						name: 1,
						brand: Object.keys($('input[name="category"]').data()).join(',') || 0
					},
					all: false,
					select: $('input[name="type_id"]').data(),
					s: true
				}, $('input[name="type_id"]'));
			}
		}, 'json');
	}*/
	
	$(function() {
		/*[add]
			inventory.getGroup(0, 0, 1);
		[/add]
		
		 $('input[name="type_id"]').data({type-id});
		type_id = {type-id};
		getTypes();
		
		if ({category-id} != []) {
			$('input[name="category"]').data({category-id});
			brand_id = {category-id};
			getBrands(1);
			if ({model-id} != []) {
				model_id = {model-id};
				$('input[name="model"]').data({model-id});
			}
			//inventory.getGroup(Object.keys({type-id}).join(','), {id}, 1);
		} else 
			getBrands(1); */
		
		$('input[name="storeCat"]').data({store-category-id});
		$.post('/store/allCategories', {gId: 5, nIds: Object.keys($('input[name="storeCat"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#storeCat > ul').html(items).sForm({
					action: '/store/allCategories',
					data: {
						gId: 5,
						lId: lId,
						nIds: Object.keys($('input[name="storeCat"]').data()).join(','),
						query: $('#storeCat > .sfWrap input').val()
					},
					all: false,
					select: $('input[name="storeCat"]').data(),
					s: true
				}, $('input[name="storeCat"]'));
			}
		}, 'json');
		
		/* $('input[name="category"]').data({category-id});
		$.post('/inventory/allServiceCategories', {nIds: Object.keys($('input[name="category"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});

				$('#category > ul').html(items).sForm({
					action: '/inventory/allServiceCategories',
					data: {
						lId: lId,
						query: $('#category > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="category"]').data(),
					s: true
				}, $('input[name="category"]'));
			}
		}, 'json'); */

		$('input[name="object"]').data({object-id});
		$.post('/objects/all', {nIds: Object.keys($('input[name="object"]').data()).join(',')}, function (r) {
			if (r){
				var items = '<li data-value="all">All stores</li>', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#object > ul').html(items).sForm({
					action: '/objects/all',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="object"]').data() || {}).join(','),
						query: $('#object > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="object"]').data(),
					link: 'objects/edit'
				}, $('input[name="object"]'));
			}
		}, 'json');
	});
	
</script>