/**
 * @Appointment Stores
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/

var Stores = {
    addExpanses: function(el) {
/*         $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Name'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'ex_name'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Value'
            })
        }).append($('<input/>', {
            type: 'number',
            name: 'ex_value',
			step: '0.001'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        }))); */
    },
    add: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'email') {
                if (!form[i].value.length || !is_valid.email(form[i].value)) {
                    err = form[i].value.length ? lang[2] : lang[3];
                    break;
                }
            } else if (form[i].name == 'phone') {
                if (!form[i].value.length || !is_valid.phone(form[i].value)) {
                    err = form[i].value.length ? lang[4] : lang[5];
                    break;
                }
            } else if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = lang[8];
                    break;
                }
            } else if (form[i].name == 'work_time_start' || form[i].name == 'work_time_end') {
                if (!form[i].value.length) {
                    err = 'Working time can not be empty';
                    break;
                }
            } else if (form[i].name == 'timezone') {
                if (!form[i].value.length) {
                    err = 'Timezone can not be empty';
                    break;
                }
            }
            data.append(form[i].name, form[i].value);
        }
        if (id) {
            data.append('managers', Object.keys($('input[name="managers"]').data()).join(','));
            data.append('staff', Object.keys($('input[name="staff"]').data()).join(','));
        }
		
		var options = {};
		if ($('#expanses .refPoints').length) {
			$('#expanses .refPoints').each(function(i, v) {
				options[$(v).find('input[name="ex_name"]').val()] = $(v).find('input[name="ex_value"]').val();
			});
		}
		data.append('options', JSON.stringify(options));
		
		var purchase_price = {}, arr = [];
		if ($('#purchase_price .refPoints').length) {
			$('#purchase_price .refPoints').each(function(i, v) {
				if ($.inArray($(v).find('input[name="min_price"]').val(), arr) < 0) {
					arr[i] = $(v).find('input[name="min_price"]').val();
					purchase_price[$(v).find('input[name="min_price"]').val()] = $(v).find('input[name="mark-up"]').val();
				} else {
					err = 'You enter mark-up amount for ' + $(v).find('input[name="min_price"]').val() + ' refferals twice';
				}
			});
		}
		data.append('purchase_price', JSON.stringify(purchase_price));
		
        var files = $('.dragndrop')[0].files;
        if (!$.isEmptyObject(files)) {
            for (var n in files) {
                data.append('image', files[n]);
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        var thumb = $('.dragndrop').prev();
        if (thumb[0].tagName != 'FIGURE') data.append('del_image', true);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/objects/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id) Page.get('/objects/edit/' + r);
				else Page.get('/objects/');
            }
        });
    },
    sel: function(objId, g, t, d) {
        $.post('/users/all', {
            id: objId,
            gId: g,
            nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '" data-image="' + v.image + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_' + objId,
                    title: t,
                    content: '<ul class="hdn">' + items + '</ul>\
						<div class="sGroup"><button class="btn btnMdl" onclick="Stores.get(\'' + d + '\', ' + objId + ');">' + lang[139] + '</button></div>',
                    cb: function() {
                        nIds = [];
                        $('#select_' + objId).find('ul').sForm({
                            action: '/users/all',
                            data: {
                                lId: lId,
                                id: objId,
                                gId: g,
                                query: $('#select_' + objId).find('input').val(),
                                nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="' + d + '"]').data(),
							link: 'users/view'
                        }, $('input[name="' + d + '"]'));
                    }
                });
            }
        }, 'json');
    },
    get: function(el, id) {
        //$('#select_' + id).find('ul').sForm('get', $('input[name="' + el + '"]'));
        mdl.close();
        userAppend($('#managers'), $('input[name="managers"]').data());
        userAppend($('#staff'), $('input[name="staff"]').data());
    },
    del: function(id) {
        $.post('/objects/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#object_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    rent: function() {
        if ($('input[name="rent_cost"]').val().length)
            $('#rentTotal').text((parseFloat($('input[name="rent_cost"]').val()) + parseFloat($('input[name="rent_cost"]').val() * $('input[name="tax"]').val() / 100)).toFixed(2));
    },
    openLocation: function(id, n, o, c) {
		mdl.open({
			id: 'openLocation',
			title: (id ? lang[17] : lang[18]) + ' ' + lang[92],
			content: '<div class="iGroup">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name" value="' + (n || '') + '">\
				</div>\
				<div class="iGroup">\
					<label>Count</label>\
					<input type="text" name="count" value="' + (c || '') + '">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit" onclick="Stores.sendLocation(this, ' + (id || 0) + ', ' + (o || 0) + ')">' + (id ? lang[17] : lang[18]) + '</button>\
				</div>'
		});
    },
    sendLocation: function(f, id, o) {
        f = $(f).parents('.mdlBody');
        var data = {
                id: id
            },
            err = null;
        f.find('input').css('border-color', '#ddd');
        loadBtn.start(f.find('button[type="submit"]'));
        if (!f.find('input[name="name"]').val().length) {
            err = lang[25];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
            data.object = o;
            data.count = f.find('input[name="count"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop(s.find('button[type="submit"]'));
        } else {
            $.post('/objects/save_location', data, function(r) {
                loadBtn.stop(f.find('button[type="submit"]'));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
                    console.log('fd');
                    mdl.close('openLocation');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delLocation: function(id) {
        $.post('/objects/del_location', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#location_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	report: function(el, main) {
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
			
			$.post('/objects/report_result', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
					if ($('#report').next().hasClass('profile-loader'))
						$('#report').next().remove();
				} else {
					$('#report').empty();
				}
				$('#page').select();
				$('#page').tabs();
			}, 'json');
		}
	}, 
	miniReport: function(el, v, id) {
		loadBtn.start($(el));
		var data = {
			object: id,
			date_start: $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar > input[name="fDate"]').val(),
			type: v,
			status: $('#eo_' + id + ' select[name="status"]').val()
		}
		
		$.post('/objects/mini_reports', data, function(r) {
			loadBtn.stop($(el));
			if (r) {
				if ($('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).length)
					$('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).replaceWith(r);
				else
					$('#reps_' + id).prepend(r);
			}
		});
	},
	addFormula: function(el) {
/*         $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Min price'
				})
			}).append($('<input/>', {
				type: 'number',
				step: '0.001',
				name: 'min_price'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Upcharge price'
            })
        }).append($('<input/>', {
            type: 'number',
			step: '0.001',
            name: 'mark-up'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        }))); */
    }
};