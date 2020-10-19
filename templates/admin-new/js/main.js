function set_cam(j){
	$('.info-camera-main').html('<a href="/users/view/'+j.user_id+'" onclick="Page.get(this.href); return false;">\
		<img src="'+(j.image ? '/uploads/images/users/'+j.user_id+'/thumb_'+j.image : '/uploads/images/users/1703/58b60b26b42cb6.03351607.jpg')+'" align="left">\
		'+j.name+' '+j.lastname+'\
	</a>\
	<span class="fa fa-exclamation-circle"onclick="Page.get(\'/im/'+j.user_id+'?text=Camera;'+j.id+'\')"></span>\
	<p>'+j.camera_event+'</p>').show();
}

// Analytics
var analytics = {
	online_users: function() {
		$.post('/analytics/online_users', data, function(r) {
			if (parseInt(r))
				$('#visitors_online > span').html(parseInt(r));
		});
	},
	site_visitors: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar5 > input[name="date"]').val(),
				date_finish: $('#calendar5 > input[name="fDate"]').val()
			};
		loadBtn.start($(e));
		date = [$('#calendar5 > input[name="date"]').val(), $('#calendar5 > input[name="fDate"]').val()];
		
		$.post('/analytics/site_stat', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var minY = 0,
					maxY = 0;
				
				for (n in r.data['hosts']) {
					if (r.data['hosts'][n][1] > maxY)
						maxY = r.data['hosts'][n][1];
				}
				
				minY = maxY;
				for (n in r.data['visitors']) {
					if (r.data['visitors'][n][1] < minY)
						minY = r.data['visitors'][n][1];
				}
				
				stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				
				$('#site_stat_plot').empty();
				makeStat.init({
					id: 'site_stat_plot',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[146]) + ': '], 
					y: [minY, maxY, stepY, true, 'Count: '],
					data: [
						{
							'id': 'hosts',
							'points': r.data['hosts'],
							'color': '#36b1e6'
						},
						{
							'id': 'visitors',
							'points': r.data['visitors'],
							'color': '#ff0000'
						}
					]
				}); 
				f.hide();
				$('#visitors_online > span').html(r.data.online);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	inventory_plot: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar_plot > input[name="date"]').val(),
				date_finish: $('#calendar_plot > input[name="fDate"]').val(),
				object: Object.keys($('input[name="object_stat"]').data()).join(','),
				inventory: 1
			};
		loadBtn.start($(e));
		date = [$('#calendar_plot > input[name="date"]').val(), $('#calendar_plot > input[name="fDate"]').val()];
		
		$.post('/analytics/sold_plot', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv['profit'].map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv['sold'].map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv['sold'],
							'color': '#36b1e6'
						},
						{
							'points': inv['purchase'],
							'color': '#ff0000'
						},
						{
							'points': inv['profit'],
							'color': '#b7bd06'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	tradein_plot: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar_plot > input[name="date"]').val(),
				date_finish: $('#calendar_plot > input[name="fDate"]').val(),
				object: Object.keys($('input[name="object_stat"]').data()).join(','),
				tradein: 1,
				purchases: 1
			};
		loadBtn.start($(e));
		date = [$('#calendar_plot > input[name="date"]').val(), $('#calendar_plot > input[name="fDate"]').val()];
		
		$.post('/analytics/sold_plot', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv['profit'].map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv['sold'].map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv['sold'],
							'color': '#36b1e6',
							'id': 'sold'
						},
						{
							'points': inv['purchase'],
							'color': '#ff0000',
							'id': 'purchase'
						},
						{
							'points': inv['profit'],
							'color': '#b7bd06',
							'id': 'profit'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	new_users: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/new_users', {
			date_start: $('#calendar4 > input[name="date"]').val(),
			date_finish: $('#calendar4 > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt(maxY - minY), 10, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#users_plot').empty();
				makeStat.init({
					id: 'users_plot',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Date: '], 
					y: [minY, maxY, stepY, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'id': 'plot_new_users'
						},
						{
							'points': r.returned,
							'color': '#88b52d',
							'id': 'plot_returned_users'
						}
					],
					plusx: 0
				}); 
				$('#total_customers').text('(Total: ' + r.users + ')');
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	feedback: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/feedback_report', {
			date_start: $('#calendar3').length ? $('#calendar3 > input[name="date"]').val() : $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar3').length ? $('#calendar3 > input[name="fDate"]').val() : $('#calendar > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#feedbacks_plot').empty();
				makeStat.init({
					id: 'feedbacks_plot',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Rating: '], 
					y: [0, maxY, 10, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'dash': true,
							'id': 'by_count'
						}
					],
					plusx: 1
				}); 
				$('#freport').html(r.stores);
				if (e && $('[data-value="middle_value"]').hasClass('active'))
					analytics.feedback_by_day();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	feedback_by_day: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/feedback_by_day', {
			date_start: $('#calendar3').length ? $('#calendar3 > input[name="date"]').val() : $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar3').length ? $('#calendar3 > input[name="fDate"]').val() : $('#calendar > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#feedback_by_day').empty();
				makeStat.init({
					id: 'feedback_by_day',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Rating: '], 
					y: [0, 5, 1, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'dash': true,
							'id': 'by_day'
						}
					],
					plusx: 1
				}); 
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	expansesReport: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/analytics/expanses_report', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
				} else {
					$('#report').empty();
				}
			}, 'json');
		}
	},
	purchasesReport: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.type = $('select[name="type"]').val();
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/analytics/purchases_report', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
				} else {
					$('#report').empty();
				}
			}, 'json');
		}
	},
	sales: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_stat"]').val(),
				object: $('select[name="object_stat"]').val(),
				cash: $('input[name="pay_cash"]').val(),
				credit: $('input[name="pay_credit"]').val(),
				check: $('input[name="pay_check"]').val(),
				merchaine: $('input[name="pay_merchaine"]').val(),
				compare_period: $('input[name="compare_period"]').val()
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/sales', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#sales_graph').empty();
				makeStat.init({
					id: 'sales_graph',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last-1) || 23, r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					],
					red_line: 4000,
					black_line: 4,
					violet_line: true,
					compare_line: $('input[name="compare_period"]').val() || 0
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	}, points: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_points"]').val(),
				object: $('select[name="object_points"]').val(),
				user: Object.keys($('input[name="user_points"]').data()).join(',')
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/points', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#efficiency_graph').empty();

				makeStat.init({
					id: 'efficiency_graph',
					s: 60, 
					w: 1569,
					h: 550, 
					x: [0, (r.last-1) || 23, r.step, r.labels, ((data.date && date[0] == date[1]) ? lang[145] : lang[146]) + ': '], 
					y: [minY, maxY, stepY, true, lang[115] + ': '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	show: function(a){
		var m = $(a.currentTarget);
		if(m.hasClass('stat-modal')){
			m.find('> div').css('transform', 'rotateX(-90deg)');
			setTimeout(function(){
				m.removeClass('stat-modal').find('> div').css('transform', '');
			}, 300);
		} else {
			m.addClass('stat-modal').find('> div').css('transition', 'none');
			setTimeout(function(){
				m.find('> div').css('transition', '0.3s').css('transform', 'rotateX(0deg)');
			}, 100);
		}
	}
}