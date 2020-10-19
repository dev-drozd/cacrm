var calendar = {
	month: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	week: ['Sun', 'Mon', 'Tue', 'Wen', 'Thu', 'Fri', 'Sat'],
	c: {},
	o: {},
	init: function(o) {
		var today = new Date();
		
		if (o.el) {
			calendar.o = o;
			for(n in o.el) {
				var el = $(o.el[n]);
				el.addClass('calendar cl').append($('<input/>', {
					type: 'hidden',
					name: 'date'
				}));
				if (el.attr('data-multiple')) {
					el.append($('<input/>', {
						type: 'hidden',
						name: 'fDate'
					}));
				}
				
				el.append($('<div/>', {
					class: 'calHeader cl',
					html: $('<span/>', {
						class: 'calArrow cl',
						html: '&lsaquo;',
						onclick: 'calendar.selMonth(this, \'prev\');'
					})
				}).append($('<span/>', {
					class: 'calCur cl',
					html: $('<span/>', {
						class: 'calMonth cl',
						html: $('<i/>', {
							class: 'cl',
							html: calendar.month[today.getMonth()]
						}),
						onclick: '$(this).toggleClass(\'active\');'
					}).append($('<span/>'))
				}).append($('<span/>', {
					class: 'calYear cl',
					html: today.getFullYear(),
					onclick: 'calendar.yearClick(this);'
				}))).append($('<span/>', {
					class: 'calArrow cl',
					html: '&rsaquo;',
					onclick: 'calendar.selMonth(this, \'next\');'
				}))).append($('<div/>', {
					class: 'calContent cl',
					html: $('<div/>', {
						class: 'calWeek cl'
					})
				}).append($('<div/>', {
					class: 'calDays cl'
				})));
				
				$.each(calendar.month, function (j, m) {
					el.find('.calMonth > span').append($('<span/>', {
						'data-value': j,
						class: 'cl',
						html: m,
						onclick: 'calendar.days(\''+$(o.el[n]).attr('id')+'\', $(this).parents(\'.calendar\').find(\'.calYear\').text(), parseInt($(this).attr(\'data-value\')) + 1);'
					}))
				});
				el.find('.calMonth').find('span[data-value="' + today.getMonth() + '"]').addClass('active');
				
				$.each(calendar.week, function (j, d) {
					el.find('.calWeek').append($('<span/>', {
						class: 'cl',
						value: j,
						html: d
					}))
				});
				
				if (el.attr('data-year') && el.attr('data-month'))
					calendar.days($(o.el[n]).attr('id'), el.attr('data-year'), el.attr('data-month'), el.attr('data-day'));
				else
					calendar.days($(o.el[n]).attr('id'), today.getFullYear(), today.getMonth() + 1);
			}
		}
	},
	days: function(id, y, m, dd) {
		$('#' + id + ' .calDays').html($('<span/>', {
			style: 'width: ' + (new Date(calendar.month[m - 1] + ' 1, ' + y).getDay() * 14.286) + '%'
		}));
		for (var d = 1; d <= new Date(y, m, 0).getDate(); d++) {
			$('#' + id + ' .calDays').append($('<span/>', {
				class: 'calDay cl' + (dd == d ? ' active' : ''),
				month: m > 10 ? '0' + m : m,
				html: d,
				onclick: 'calendar.clickDays(this, \''+id+'\', '+y+', '+m+', '+d+');'
			}));
		}
		
		$('#' + id + ' .calMonth > i').html(calendar.month[m - 1]);
		$('#' + id + ' .calYear').html(y);
		$('#' + id + ' .calMonth > span > .active').removeClass('active').parent().find('span[data-value="' + (m - 1) + '"]').addClass('active');
	},
	clickDays: function(e, id, y, m, dd, c) {
		if (calendar.c[id] == 1) {
			var val = $('#' + id + ' input[name="date"]').val().split('-');
			if (parseInt($('#' + id + ' .calYear').text() + '' + (
					$(e).attr('month') < 10 ? '0' + $(e).attr('month') : $(e).attr('month')
				) + '' + ($(e).text().length < 2 ? '0' + $(e).text() : $(e).text())) >= 
			parseInt(val[0] + val[1] + val[2])) {
				$(e).addClass('active');
				var month = parseInt($('#' + id + ' .calMonth > span').find('.active').attr('data-value')) + 1,
					day = parseInt($(e).text());
				$('#' + id + ' input[name="fDate"]').val(
					parseInt($('#' + id + ' .calYear').text()) + '-' + (month < 10 ? '0' + month : month)
					+ '-' + (day < 10 ? '0' + day : day)
				);
				calendar.c[id] = 0;
				$('#' + id).prev().val($('#' + id + ' > input[name="date"]').val() + ' / ' + $('#' + id + ' > input[name="fDate"]').val())
				$('#' + id).hide();
				if (calendar.o.cb) calendar.o.cb();
			}
		} else {
			$('#' + id).find('.calDays > .active').removeClass('active');
			$(e).addClass('active');
			var month = parseInt($('#' + id + ' .calMonth > span').find('.active').attr('data-value')) + 1,
				day = parseInt($(e).text());
			$('#' + id + ' input[name="date"]').val(
				parseInt($('#' + id + ' .calYear').text()) + '-' + (m < 10 ? '0' + m : m)
				+ '-' + (day < 10 ? '0' + day : day)
			);
			if ($('#' + id).attr('data-multiple'))                                
				calendar.c[id] = 1;
			else {
				$('#' + id).prev().val($('#' + id + ' > input[name="date"]').val() + ' / ' + $('#' + id + ' > input[name="fDate"]').val())
				$('#' + id).hide();
				if (calendar.o.cb) 
					calendar.o.cb();
			}
		}
	},
	selMonth: function(e, s) {
		m = parseInt($(e).parents('.calendar').find('.calMonth > span').find('.active').attr('data-value')) + (s == 'next' ? 2 : 0);
		y = parseInt($(e).parents('.calendar').find('.calYear').text());
		if (m > 12) {
			m = 1;
			y++;
		} else if (m < 1) {
			m = 12;
			y--;
		}
		calendar.days($(e).parents('.calendar').attr('id'), y, m);
	},
	yearClick: function(el) {
		$(el).removeAttr('onclick');
		var y = $(el).text();
		$(el).html($('<input/>', {
			type: 'number',
			class: 'cl',
			name: 'year',
			step: 1,
			value: y
		}).blur(function() {
			y = $(el).parents('.calendar').find('.calYear > input').val();
			calendar.days($(el).parents('.calendar').attr('id'), y, parseInt($(el).parents('.calendar').find('.calMonth .active').attr('data-value')) + 1);
			calendar.yearBind(el, y)
		}));
		$(document).on('keydown', function (event) {
			if (event.keyCode == 13) {
				y = $(e).find('.calYear > input').val();
				calendar.days($(el).parents('.calendar').attr('id'), y, parseInt($(e).find('.calMonth .active').attr('data-value')) + 1);
				calendar.yearBind(el, y)
			} else if (event.keyCode == 27) {
				calendar.yearBind(el)
			}
			return event;
		});
	},
	yearBind: function(el, y) {
		$(el).parents('.calendar').find('.calYear').html(y);
		$(document).off('keydown');
		$(el).on('click', function () { 
			calendar.yearClick(el);
		});
	}
}