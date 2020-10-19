{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		Import file
		<a href="/import/list" onclick="Page.get(this.href); return false" class="btn addBtn">Saved files</a>
	</div>
	<div class="iGroup imgGroup">
		<label>Upload file</label>
		<div class="dragndrop">
			<span class="fa fa-download"></span>
			Click or drag file here
		</div>
	</div>
</div>

<div class="pnl fw lPnl" style="display:none;">
	<div class="pnlTitle">
		Import data
		<div class="uploaded_data">
			<span>Uploaded deals: <i id="deals"></i></span>
			<span>Found in system: <i id="found">0</i></span>
			<span>Period: <i id="period"></i></span>
			<span style="display: none;">Purchases: <i id="purchases"></i></span>
			<span><a href="javascript:save_import();" id="save_link">Save</a></span>
			<span style="display: none;"><a href="javascript:verify();" id="verify_link">Verify</a></span>
		</div>
		<div class="filters ap cl">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr cl" style="z-index: 20;">
				<div class="fTitle cl">Filters</div>
				<div class="iGroup fw dGroup">
					<label>Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date">
					<div id="calendar" data-multiple="true"></div>
				</div>
			</div>
		</div>
	</div>
	<div onscroll='scroller("scroller", "import_table")' style="overflow:scroll; height: 10; width: 100%" id="scroller">
		<span style="display: block; height: 1px;"></span>
	</div>
	<div class="importRows" id="import_table" onscroll='scroller("import_table", "scroller")'>
	</div>
	<div class="imAnim" style="height:200px;display: none;" id="progress">
		<span class="fa fa-pulse fa-spinner"></span>
	</div>
</div>

<script>
var data = {},
	tran = null;

$(function() {
	$('.dragndrop').upload({
		types: [
			'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'vnd.ms-excel',
			'msexcel',
			'x-msexcel',
			'x-ms-excel',
			'x-msexcel',
			'x-excel',
			'x-dos_ms_excel',
			'xls',
			'x-xls',
			'csv'
		],
		check: function(e){
			if(!e.error){
				$('#import_table').html($('<div/>', {
					class: 'imAnim',
					style: 'height:200px',
					html: $('<span/>', {
						class: 'fa fa-pulse fa-spinner'
					})
				})).parent().show();
				var form = new FormData();
				form.append('file', e.file);
				$.ajax({
					url: '/import/upload',
					data: form,
					dataType: 'json',
					processData: false,
					contentType: false,
					type: 'POST',
					success: function(a){
						data = a.data;
						$('#import_table').html(a.table);
						$('#deals').text(a.deals);
						$('#found').text('0');
						$('#period').text(a.period);
						$('#page').select();
						$('#scroller > span').width($('#import_table tr').width());
						$('body').hntJS({
							left: $('#import_table').offset().left + $('#import_table').width()/2
						});
						$('#save_link').show();
						data.titles.forEach(function(item, i) {
							if (item == 'Reference ID') {
								tran = i;
								$('#verify_link').parent().show();
							}
						});
					}
				});
				this.files = {};
			} else if(e.error == 'max'){
				alr.show({
					class: 'alrDanger',
					content: 'To many files',
					delay: 2
				});
			} else if(e.error == 'type'){
				alr.show({
					class: 'alrDanger',
					content: 'Wrong file type',
					delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
					class: 'alrDanger',
					content: lang[171],
					delay: 2
				});
			}
		}
	});
	
	$('#calendar').calendar(function() {
		$('.iGroup > input[name="date"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
		$('.iGroup > input[name="date"] + div').hide();
		$('.filterCtnr').hide();
		compare();
	});
	
	$('body').on('click', function(event) {
		if (!$(event.target).hasClass('cl')) {
			$('.dGroup > input + div').hide();
			$('.filterCtnr').hide();
		}
	})
	
	[id]
		$('#import_table').html($('<div/>', {
			class: 'imAnim',
			style: 'height:200px',
			html: $('<span/>', {
				class: 'fa fa-pulse fa-spinner'
			})
		})).parent().show();
		$('#save_link').hide();
		var form = new FormData();
		form.append('id', {id});
		$.ajax({
			url: '/import/upload',
			data: form,
			dataType: 'json',
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(a){
				data = a.data;
				$('#import_table').html(a.table);
				$('#deals').text(a.deals);
				$('#found').text('0');
				$('#period').text(a.period);
				$('#page').select();
				$('#scroller > span').width($('#import_table tr').width());
				$('body').hntJS({
					left: $('#import_table').offset().left + $('#import_table').width()/2
				});
				data.titles.forEach(function(item, i) {
					if (item == 'Reference ID') {
						tran = i;
						$('#verify_link').parent().show();
					}
				});
			}
		});
	[/id]
});

function compare() {
	var form = {
		date: {}
	};
	
	$('#import_table thead th').each(function(i, v) {
		if ($(this).find('select').val() && $(this).find('select').val() != '0'){
			form[$(this).find('select').val()] = data[data.titles[$(this).attr('data-id')]].join('|:|');
		} else if ($(this).attr('data-field') == 'Reference ID' && tran) {
			form['transaction'] = data[data.titles[tran]].join('|:|')
		}
	});
	
	form.date[0] = $('#calendar > input[name="date"]').val();
	form.date[1] = $('#calendar > input[name="fDate"]').val();
	$('#import_table').hide();
	$('#progress').show();
	$.post('/import/compare', form, function(r){
		$('tr.td_green').removeClass('td_green').attr('onclick', '');
		$('table > tbody > tr.hntJS + tr > td').empty();
		$('table > tbody > tr.hntJS').attr('data-title', '0 Matches').attr('onclick', 'selectPurchase(this);').each(function(a,b){
			var ids = [];
			for(var i = 0; i < r.res.length; i++){
				var lid = r.res[i].id, j = 0, x = 0;
				for(var n in r.res[i]){
					if(['id', 'show_name', 'show_link', 'show_price', 'show_currency'].indexOf(n) >= 0) continue;
					if (n == 'transaction' && !r.res[i][n]) continue;
					if($(b).find('td[data-value="'+r.res[i][n].replace('"','\'')+'"]').length || $(b).find('td[data-value="'+r.res[i]['transaction']+'"]').length) x++;
					j++;
				}
				if(j > 0 && j == x){
					var v = $(b).attr('data-title'), count = Number(v.replace(' Matches', ''));
					ids.push(lid);
					if ($(b).next().find('a').length < 5)
						$(b).next().find('td').append('<div class="check-import">\
														<div class="radio"><input type="radio" name="' + $(b).attr('id') + '[]" id="' + r.res[i].id + $(b).attr('id') + '"' + ($(b).find('td[data-value="'+r.res[i]['transaction']+'"]').length ? ' checked' : '') + '><label for="' + r.res[i].id + $(b).attr('id') + '"></label></div>\
														<a href="/purchases/edit/' + r.res[i].id + '" onclick="Page.get(this.href); return false;">' + r.res[i].show_name + '</a> \
														<div class="pur_info">Link: <a href="' + r.res[i].show_link + '">view</a> | Price: ' + currency_val[r.res[i].show_currency || 'USD'].symbol + ' ' + r.res[i].show_price + '</div>\
													</div>');
					$(b).attr('ids', (ids.length > 1 ? ids[0] : lid)).attr('data-title', (count+1)+' Matches').addClass('td_green').attr('onclick', 'window.open("/purchases'+(ids.length > 1 ? '?ids='+ids.join(',') : '/edit/'+lid)+'");');
				}
				
			}
		});
		if (r.purchases == 'none')
			$('#purchases').parent().hide();
		else 
			$('#purchases').html(r.purchases).parent().show();
		
		if(r.res.length){
			alr.show({
				class: 'alrSuccess',
				content: 'Found '+r.res.length+' matches',
				delay: 2
			});
			$('#found').text(r.res.length);	

			var setCheck;
			$('input[type="radio"]').unbind('click');
			$('input[type="radio"]').click(function() {
				if (setCheck != this || this.name != setCheck.name)
					setCheck = this;
				else {
					this.checked = false;
					setCheck = null;
				}
			});
			
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'No matches found',
				delay: 2
			});
			$('#found').text(0);
		}
	$('#import_table').show();
	$('#progress').hide();
	}, 'json');
}

function clearDate() {
	$('#calendar > input[name="date"]').val('');
	$('#calendar > input[name="fDate"]').val('');
	$('.iGroup > input[name="date"]').val('');
}

var arescrolling = 0;
function scroller(from,to) {
	if (arescrolling) return; 
	arescrolling = 1;
	document.getElementById(to).scrollLeft = document.getElementById(from).scrollLeft;
	arescrolling = 0;
}

function save_import() {
	var form = new FormData();
	form.append('file', $('.dragndrop')[0].files[Object.keys($('.dragndrop')[0].files)[0]]);
	form.append('deals', $('#deals').text());
	form.append('period', $('#period').text());
	$.ajax({
		url: '/import/save_file',
		data: form,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(a){
			if (a == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'File was successfully saved',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}
	});
}

function verify (e) {
	if (tran != null) {
		var transactions = {};
		$('input[type="radio"]').each(function(i, v) {
			transactions[$(v).attr('id').split('line_')[0]] = '';
		});
		$('input[type="radio"]:checked').each(function(i, v) {
			transactions[$(v).attr('id').split('line_')[0]] = $(v).parents('tr').prev().find('td:nth-child(' + (tran + 1) + ')').text();
		});
		$.post('/import/verified', transactions, function(res) {
			
		});
	}
}

function selectPurchase(e) {
	if (tran) {
		
		mdl.open({
			id: 'verify_purchase',
			title: 'Select purchase',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup fw price bobject',
					id: 'purchase_select',
					html: $('<label/>', {
						html: 'Purchases'
					})
				}).append($('<input/>', {
					type: 'hidden',
					name: 'purchase_select'
				})).append($('<ul/>', {
					class: 'hdn'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: 'Send'
				}).click(function() {
					if (Object.keys($('input[name="purchase_select"]').data()).join(',')) {
						var id = Object.keys($('input[name="purchase_select"]').data())[0];
						$.post('/import/verify_purchace', {
							transaction: $(e).find('td:nth-child(' + (tran+1) + ')').text(),
							id: id
						},  function(r) {
							if (r == 'OK') {
								$(e).addClass('td_green').attr('ids', id).attr('onclick', 'window.open("/purchases/edit/' + id + '");').attr('data-title', '1 Matches');
								mdl.close();
							} else {
								alr.show({
									class: 'alrDanger',
									content: lang[13],
									delay: 2
								});
							}
						})
					} else {
						alr.show({
							class: 'alrDanger',
							content: 'Please, select purchase',
							delay: 2
						});
					}
				})
			})),
			cb: function() {
				$.post('/import/purchases', {}, function(r) {
					if (r) {
						var items = '',
							lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-object="' + v.object + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#purchase_select > ul').html(items).sForm({
							action: '/import/purchases',
							data: {
								nIds: Object.keys($('input[name="purchase"]').data() || {}).join(','),
								query: $('#purchase_select > .sfWrap input').val() || ''
							},
							all: false,
							select: $('input[name="purchase_select"]').data(),
							link: 'purchases/edit',
							s: true
						}, $('input[name="purchase_select"]'));
					}
				}, 'json');
			}
		});
	}
}
</script>
