<div class="organizer">
	<div class="oDate">
		<span class="fa fa-chevron-left oNav" onclick="organizer.nav('prev');"></span>
		<span class="odMonth" onclick="organizer.month('next');">September</span>
		<span class="odYear" onclick="organizer.year();">2020</span>
		<span class="fa fa-chevron-right oNav" onclick="organizer.nav('next');"></span>
		
		<div class="filters ap cl">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr cl">
				<div class="fTitle cl">Filters</div>
				<div class="iGroup fw cl" id="object">
					<label>
						Store
						<span class="fa fa-eraser cl" onclick="organizer.clearDate();"></span>
					</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw dGroup cl">
					<label>
						Date
					</label>
					<input type="text" class="cl" onclick="$(this).next().show();" name="date_filter">
					<div id="fCalendar" data-multiple="1"></div>
				</div>
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="organizer.days($('.odYear').text(), $('.odMonth').attr('month'));">OK</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop  exportXls" data-title="Download XLS report" onclick="download();"><span class="fa fa-download"></span></span>
	</div>
	<div class="oWeek">
		<span class="owDay hol">Sun</span>
		<span class="owDay">Mon</span>
		<span class="owDay">Tue</span>
		<span class="owDay">Wed</span>
		<span class="owDay">Thu</span>
		<span class="owDay">Fri</span>
		<span class="owDay hol">Sat</span>
	</div>
	<div class="oDays">
	</div>
	<div class="months">
		<span class="month" data-month="1" onclick="organizer.selMonth(this);">January</span>
		<span class="month" data-month="2" onclick="organizer.selMonth(this);">February</span>
		<span class="month" data-month="3" onclick="organizer.selMonth(this);">March</span>
		<span class="month" data-month="4" onclick="organizer.selMonth(this);">April</span>
		<span class="month" data-month="5" onclick="organizer.selMonth(this);">May</span>
		<span class="month" data-month="6" onclick="organizer.selMonth(this);">June</span>
		<span class="month" data-month="7" onclick="organizer.selMonth(this);">July</span>
		<span class="month" data-month="8" onclick="organizer.selMonth(this);">August</span>
		<span class="month" data-month="9" onclick="organizer.selMonth(this);">September</span>
		<span class="month" data-month="10" onclick="organizer.selMonth(this);">October</span>
		<span class="month" data-month="11" onclick="organizer.selMonth(this);">November</span>
		<span class="month" data-month="12" onclick="organizer.selMonth(this);">December</span>
	</div>
</div>

<div class="orgTable">
</div>

<script>
var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	week = ['Sun', 'Mon', 'Tue', 'Wen', 'Thu', 'Fri', 'Sat'],
	sysMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	today = new Date(),
	user = {},
	date_start = '',
	date_end = '',
	time = {},
	edit = 0,
	select_options = '{select-options}'
	groups = '{groups}';
$(function() {
	organizer.days(today.getFullYear(), (today.getMonth()+ 1), today.getDate());
	
	$.post('/invoices/objects', {}, function (r) {
			if (r){
				if (r.list.length > 1) {
					var items = '', lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#object > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							query: $('#object> .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true,
						link: 'objects/edit'
					}, $('input[name="object"]'));
				} else if (r.list.length == 1) {
					var cash_object = {};
					cash_object[r.list[0].id] = {
						name: r.list[0].name
					}
					$('input[name="object"]').data(cash_object);
					$('#object').append($('<div/>', {
						class: 'storeOne',
						html: r.list[0].name
					}));
				} else {
					$('#object').hide();
				}
			}
		}, 'json');
		
		$('#fCalendar').calendar(function() {
			$('input[name="date_filter"]').val($('#fCalendar > input[name="date"]').val() + ' / ' + $('#fCalendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide();
		});
});

var organizer = {
	res: {},
	objects: {},
	days: function(y, m, dd) {
		$('.odMonth').html(month[m - 1]).attr('month', m);
		$('.odYear').html(y);
		
		var width = new Date(sysMonth[m - 1] + ' 1, ' + y).getDay();
		$('.oDays').html($('<div/>', {
			class: 'odSpace',
			style: 'width: calc(100% / 7 * ' + width + ' - 10px);' + (width ? '' : ' display: none;')
		}));
		
		$.post('/organizer/days', objToUrl({
			month: m,
			year: y,
			object: Object.keys($('input[name="object"]').data() || {}).join(','),
			group: $('select[name="group"]').val() || 0,
			date_start: $('#fCalendar input[name="date"]').val(),
			date_end: $('#fCalendar input[name="fDate"]').val()
		}), function(r) {
			organizer.res = r.days;
			organizer.objects = r.objects;
			$('.orgTable').html(r.table);
			for (var d = 1; d <= new Date(y, m, 0).getDate(); d++) {
				var res = '',
					c = 0;
				$.each(r.days[d], function(i, v) {
					res += '<a href="/users/view/' + v.staff + '" title="' + v.name + ' ' + v.lastname + '" onclick="Page.get(this.href); return false;">' + (
						v.image ? '<img src="/uploads/images/users/' + v.staff + '/thumb_' + v.image + '" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>'
					) + '</a>';
					c ++;
					if (c == 2) return false;
				});
				if (c > 0) res += '<span class="fa fa-ellipsis-h miniRound" onclick="organizer.single(this);"></span>';
				
				$('.oDays').append($('<div/>', {
					class: 'odDay cl' + (dd == d ? ' active' : '') + ($.inArray(new Date(sysMonth[m - 1] + ' ' + d + ', ' + y).getDay(), [0, 6]) >= 0 ? ' hol' : ''),
					month: m > 10 ? '0' + m : m,
					html: $('<span/>', {
						html: d
					})
				}).click(function(event) {
					if ($(event.target).hasClass('cl') && r.is_admin) {
						$(this).parent().find('.focus').removeClass('focus');
						$(this).addClass('focus');
						organizer.add();
					}
				}).append($('<div/>', {
					class: 'orgUsers',
					html: res 
				})));
			}
		}, 'json');
		$('.filterCtnr').hide();
	}, nav: function(s) {
		m = parseInt($('.odMonth').attr('month')) + (s == 'next' ? 1 : -1);
		y = parseInt($('.odYear').text());
		if (m > 12) {
			m = 1;
			y++;
		} else if (m < 1) {
			m = 12;
			y--;
		}
		organizer.days(y, m);
	}, year: function() {
		$('.odYear').attr('onclick', '').html($('<input/>', {
			type: 'number',
			step: '1',
			min: '0',
			autofocus: 'true',
			class: 'cl',
			value: $('.odYear').text()
		}).blur(function() {
			$('.odYear').text($('.odYear > input').val() || new Date().getFullYear()).attr('onclick', 'organizer.year();');
			organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
		}));
		$('.odYear > input').focus();
	}, month: function() {
		$('.organizer').addClass('sMonth');
		$('.month.active').removeClass('active');
		$('.month[data-month="' + $('.odMonth').attr('month') + '"]').addClass('active');
	}, selMonth: function(e) {
		$('.odMonth').attr('month', $(e).attr('data-month')).text($(e).text());
		organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
		$('.organizer').removeClass('sMonth');
	}, add: function(id, day) {
		if (id) {
			var i = ($.map(organizer.res[day], function(a, i){
				if (a['id'] == id)
					return i;
			})).join();
			
			var row = organizer.res[day][i];
			user = {};
			user[row['staff']] = {
				name: row['name'] + ' ' + row['lastname']
			};
			date_start = row['date_start'];
			date_end = row['date_end'];
			time = JSON.parse(row['time']);
			edit = 1;
		}
		mdl.open({
			id: 'newCalendar',
			width: '800',
			title: 'User worktime',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
				class: 'tabs',
				html: $('<div/>', {
					class: 'tab',
					id: 'orgInfo',
					'data-title': 'Info',
					html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Group'
						})
					}).append($('<select/>', {
						type: 'hidden',
						onchange: 'getUsers();',
						id: 'group'
					}))
				}).append($('<div/>', {
						class: 'iGroup hw',
						id: 'staff',
						html: $('<label/>', {
							html: 'Staff'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'staff'
					})).append($('<ul/>', {
						class: 'hdn'
					}))).append($('<div/>', {
						class: 'iGroup dGroup',
						html: $('<label/>', {
							html: 'Start Date'
						})
					}).append($('<input/>', {
						type: 'text',
						onclick: '$(".calendar").hide().parents(".iGroup").removeClass("act"); $(this).next().show().parent().addClass("act");',
						class: 'cl',
						name: 'date_start'
					})).append($('<div/>', {
						id: 'calendar_start'
					}))).append($('<div/>', {
						class: 'iGroup dGroup',
						html: $('<label/>', {
							html: 'End Date'
						})
					}).append($('<input/>', {
						type: 'text',
						class: 'cl',
						onclick: '$(".calendar").hide().parents(".iGroup").removeClass("act"); $(this).next().show().parent().addClass("act");',
						name: 'date_end'
					})).append($('<div/>', {
						id: 'calendar_end'
					})))
				}).append($('<div/>', {
					class: 'tab',
					id: 'orgWeek',
					'data-title': 'Working time',
					html: $('<div/>', {
						class: 'oWeekCheck',
						html: $('<label/>', {
							html: 'Same time'
						})
					}).append($('<input/>', {
						type: 'checkbox',
						onchange: 'organizer.sameTime(\'start\', this.value)'
					})).append($('<input/>', {
						type: 'checkbox',
						onchange: 'organizer.sameTime(\'end\', this.value)'
					}))
				}).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '0',
						html: $('<label/>', {
							html: 'Sunday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[0] ? time[0]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[0] ? time[0]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '1',
						html: $('<label/>', {
							html: 'Monday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[1] ? time[1]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[1] ? time[1]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '2',
						html: $('<label/>', {
							html: 'Tuesday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[2] ? time[2]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[2] ? time[2]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '3',
						html: $('<label/>', {
							html: 'Wednesday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[3] ? time[3]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[3] ? time[3]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '4',
						html: $('<label/>', {
							html: 'Thursday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[4] ? time[4]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[4] ? time[4]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '5',
						html: $('<label/>', {
							html: 'Friday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[5] ? time[5]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[5] ? time[5]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))).append($('<div/>', {
						class: 'oWeekDay',
						'data-week': '6',
						html: $('<label/>', {
							html: 'Saturday'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'start_time',
						value: time[6] ? time[6]['start'] : ''
					})).append($('<input/>', {
						type: 'time',
						name: 'end_time',
						value: time[6] ? time[6]['end'] : ''
					})).append($('<select/>', {
						name: 'object',
						html: select_options
					}))))
			}).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'organizer.send(this' + (id ? ', ' + id : '') + ');',
							html: 'Done'
						})
					})),
			cb: function() {
				if (edit) {
					var st_date = date_start.split('-'),
					e_date = date_end.split('-');
					
					$('#calendar_start').attr('data-year', st_date[0]);
					$('#calendar_start').attr('data-month', st_date[1]);
					$('#calendar_start').attr('data-day', st_date[2]);
					$('input[name="date_start"]').val(date_start);
					
					$('#calendar_end').attr('data-year', e_date[0]);
					$('#calendar_end').attr('data-month', e_date[1]);
					$('#calendar_end').attr('data-day', e_date[2]);
					$('input[name="date_end"]').val(date_end);
					
					$('input[name="staff"]').data(user);
					
					$('.oWeekDay').each(function(i, v) {
						$(v).find('select').val(time[i] ? time[i]['object'] : 0);
					});
					st_date = '';
					e_date = '';
					user = {};
					edit = 0;
					time = {};
				} else {
					$('#calendar_start').attr('data-year', $('.odYear').text());
					$('#calendar_start').attr('data-month', $('.odMonth').attr('month'));
					$('#calendar_start').attr('data-day', $('.odDay.focus > span').text());
					$('#calendar_start').attr('data-day', $('.odDay.focus > span').text());
					$('input[name="date_start"]').val($('.odYear').text() + '-' + (
						parseInt($('.odMonth').attr('month')) < 10 ? '0' + $('.odMonth').attr('month') : $('.odMonth').attr('month')
					) + '-' + (
						parseInt($('.odDay.focus > span').text()) < 10 ? '0' + $('.odDay.focus > span').text() : $('.odDay.focus > span').text()
					));
				}
				$('#group').html(groups);
				$('#page').tabs();
				$('#page').select();
				$('#page').checkbox();
				
				$.post('/users/all', {gId: 4}, function (r) {
					if (r){
						var items = '', lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#staff > ul').html(items).sForm({
							action: '/users/all',
							gId: 4,
							data: {
								lId: lId,
								query: $('#staff > .sfWrap input').val() || ''
							},
							all: (r.count <= 20) ? true : false,
							select: $('input[name="staff"]').data(),
							s: true
						}, $('input[name="staff"]'));
					}
				}, 'json');
		
				$('#calendar_start').calendar(function() {
					$('input[name="date_start"]').val($('#calendar_start > input[name="date"]').val());
					$('.dGroup > input + div').hide();
					$('.dGroup.act').removeClass('act');
				});
				
				$('#calendar_end').calendar(function() {
					$('input[name="date_end"]').val($('#calendar_end > input[name="date"]').val());
					$('.dGroup > input + div').hide();
					$('.dGroup.act').removeClass('act');
				});
			}
		});
	}, send: function(e, id) {
		var f = $(e).parents('.uForm'),
			data = {
				id: id,
				week: {}
			},
			err = null,
			start = '';
		f.find('select').next().css('border', '1px solid #ddd');
		if (!Object.keys($('input[name="staff"]').data()).join(',')) {
			err = 'User can not be empty';
		} else if (!$('input[name="date_start"]').val()) {
			err = 'Start date can not be empty';
		} else {
			data.staff = Object.keys($('input[name="staff"]').data()).join(',');
			data.date_start = $('input[name="date_start"]').val();
			data.date_end = $('input[name="date_end"]').val();
			
			f.find('.oWeekDay').each(function(i, v) {
				if ($(v).find('input[name="start_time"]').val()) {
					if ($(v).find('input[name="start_time"]').val()) start = $(v).find('input[name="start_time"]').val();
					if (!$(v).find('select').val()) {
						err = 'Select object';
						$(v).find('select').next().css('border', '1px solid #f00');
					} else {
						data.week[$(v).attr('data-week')] = {
							start: $(v).find('input[name="start_time"]').val(),
							end: $(v).find('input[name="end_time"]').val(),
							object: $(v).find('select[name="object"]').val()
						}
					}
				} 
			});
			
			if (!start.length) {
				err = 'Start time can not be empty';
			}
				
			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/organizer/send', data, function(r) {
					if (r == 'OK') {
						organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
						mdl.close();
					} else if (r == 'ERR') {
						alr.show({
							class: 'alrDanger',
							content: lang[9],
							delay: 2
						});
					} else if (r == 'no_hours') {
						alr.show({
							class: 'alrDanger',
							content: 'This staff have no so mutch working hours in week',
							delay: 2
						});
					} else if (r.indexOf('store_hours') >= 0) {
						alr.show({
							class: 'alrDanger',
							content: 'The store in ' + r.replace('store_hours_', '') + ' have no so mutch working hours in week',
							delay: 2
						});
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				});
			}
		}
	}, single: function(el) {
		$('.odDay.focus').removeClass('focus');
		var day = $(el).parents('.odDay').addClass('focus').find('span').text();
		
		$.post('/organizer/is_edit', {}, function(r) {
			mdl.open({
				id: 'mdlSingle',
				width: 600,
				title: day + ' ' + $('.odMonth').text() + ' ' + $('.odYear').text(),
				content: $('<div/>', {
					class: 'tbl'
				}),
				cb: function() {
					$.each(organizer.res[day], function(i, s) {
						var time = JSON.parse(s.time),
							weekDay = new Date(sysMonth[parseInt($('.odMonth').attr('month')) - 1] + ' ' + day + ', ' + $('.odYear').text()).getDay();
						if (time[weekDay]) {
							$('#mdlSingle .tbl').append($('<div/>', {
									class: 'tr',
									html: $('<div/>', {
										class: 'td orgUsr',
										style: 'width: 50%;',
										html: $('<a/>', {
											href: '/users/view/' + s.staff,
											onclick: 'Page.get(this.href); return false;',
											html: (s.image ? $('<img/>', {
												src: '/uploads/images/users/' + s.staff + '/thumb_' + s.image,
												class: 'miniRound'
											}) : $('<span/>', {
													class: 'fa fa-user-secret miniRound'
												}))
									}).append(s.name + ' ' + s.lastname)
								}).append((r.edit == 'OK' ? $('<span/>', {
												class: 'fa fa-pencil orEdit',
												onclick: 'organizer.add(' + s.id + ', ' + $(el).parents('.odDay').find('span').text() + ');'
											}) : '')).append((r.del == 'OK' ? $('<span/>', {
												class: 'fa fa-arrow-right orSwap',
												onclick: 'organizer.swap(' + s.id + ', ' + $(el).parents('.odDay').find('span').text() + ');'
											}) : '')).append((r.del == 'OK' ? $('<span/>', {
												class: 'fa fa-times orDel',
												onclick: 'organizer.del(' + s.id + ', this);'
											}) : ''))
							}).append($('<div/>', {
								class: 'td',
								html: organizer.objects[time[weekDay].object]
							})).append($('<div/>', {
								class: 'td',
								html: time[weekDay].start + ' - ' + time[weekDay].end
							})));
						}
					})
				}
			});
		}, 'json');
	}, swap: function(id, day) {
		mdl.open({
			id: 'swap',
			title: 'Shedule swap',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Group'
						})
					}).append($('<select/>', {
						type: 'hidden',
						onchange: 'getUsers();',
						id: 'group'
					}))
				}).append($('<div/>', {
						class: 'iGroup hw',
						id: 'staff',
						html: $('<label/>', {
							html: 'Staff'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'staff'
					})).append($('<ul/>', {
						class: 'hdn'
					}))).append($('<input/>', {
						type: 'hidden',
						name: 'date_swap',
						value: $('.odYear').text() + '-' + parseInt($('.odMonth').attr('month')) + '-' + day
					})).append($('<div/>', {
						class: 'iGroup dGroup',
						html: $('<label/>', {
							html: 'Start Time'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'time_start'
					}))).append($('<div/>', {
						class: 'iGroup dGroup',
						html: $('<label/>', {
							html: 'End Time'
						})
					}).append($('<input/>', {
						type: 'time',
						name: 'time_end'
					}))).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							html: 'Save',
							onclick: 'organizer.sendSwap(' + id + ', this);'
						})
					})),
				cb: function() {
					$('#group').html(groups);
					$('#page').select();
					
					$.post('/users/all', {gId: 4}, function (r) {
						if (r){
							var items = '', lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#staff > ul').html(items).sForm({
								action: '/users/all',
								gId: 4,
								data: {
									lId: lId,
									query: $('#staff > .sfWrap input').val() || ''
								},
								all: (r.count <= 20) ? true : false,
								select: $('input[name="staff"]').data(),
								s: true
							}, $('input[name="staff"]'));
						}
					}, 'json');
				}
			});
	}, sendSwap: function(id, el) {
		loadBtn.start($(el));
		var data = {
			id: id
		}, err = null;
		
		if (!Object.keys($('input[name="staff"]').data())[0])
			err = 'Please, select staff';
		else if (!$('input[name="time_start"]').val())
			err = 'Please, enter start time';
		else if (!$('input[name="time_end"]').val())
			err = 'Please, enter end time';
		else {
			data.staff = Object.keys($('input[name="staff"]').data())[0];
			data.time_start = $('input[name="time_start"]').val();
			data.time_end = $('input[name="time_end"]').val();
			data.date = $('input[name="date_swap"]').val();
		}
			
		if (err) {
			loadBtn.stop($(el));
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/organizer/swap', data, function(r) {
				loadBtn.stop($(el));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'You successfully has make a schedule swap',
						delay: 2
					});
					mdl.close();
					organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
				} else if (r == 'no_hours') {
					alr.show({
						class: 'alrDanger',
						content: 'This staff have no so mutch working hours in week',
						delay: 2
					});
				} else if (r.indexOf('store_hours') >= 0) {
					alr.show({
						class: 'alrDanger',
						content: 'The store in ' + r.replace('store_hours_', '') + ' have no so mutch working hours in week',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	}, del: function(id, el) {
		$.post('/organizer/del', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'User was successfully deleted',
					delay: 2
				});
				$(el).parent().parent().remove();
				organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do this',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	}, clearDate: function() {
		$('input[name="object"]').removeData();
		$('input[name="object"]').next().find('input').parent().find('span').click();
		$('select[name="group"]').val(0).trigger('change');
		$('#fCalendar input[name="date"]').val('');
		$('#fCalendar input[name="fDate"]').val('');
		$('input[name="date_filter"]').val('');
		$('#fCalendar .calDay.active').removeClass('active');
		$('.filterCtnr').hide();
		organizer.days($('.odYear').text(), $('.odMonth').attr('month'));
	}, sameTime: function(t, ev) {
		if (ev == 1) {
			$('.oWeekDay input[name="' + t + '_time"]').each(function(i, v) {
				if ($(v).val()) {
					$('.oWeekDay input[name="' + t + '_time"]').val($(v).val());
					return false;
				}
			})
		}
	}
}

function getUsers() {
	$.post('/users/all', {gId: $('#group').val()}, function (r) {
		if (r){
			var items = '', lId = 0;
			$.each(r.list, function(i, v) {
				items += '<li data-value="' + v.id + '">' + v.name + '</li>';
				lId = v.id;
			});
			$('#staff > input').next().remove();
			$('#staff > input').after($('<ul/>'));
			$('#staff > ul').html(items).sForm({
				action: '/users/all',
				gId: $('#group').val(),
				data: {
					lId: lId,
					query: $('#staff > .sfWrap input').val() || ''
				},
				all: (r.count <= 20) ? true : false,
				select: $('input[name="staff"]').data(),
				s: true
			}, $('input[name="staff"]'));
		}
	}, 'json');
}

function download() {
	location.href = location.origin + '/xls/organizer?year=' + $('.odYear').text() + 
		'&month=' + $('.odMonth').attr('month') + 
		'&object=' + Object.keys($('input[name="object"]').data()).join(',') + 
		'&group=' + $('select[name="group"]').val() + 
		'&date_start=' + $('#fCalendar input[name="date"]').val() +
		'&date_end=' + $('#fCalendar input[name="fDate"]').val();
}
</script>