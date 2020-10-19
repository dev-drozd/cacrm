// Settings
var Settings = {
	sendRef: function(el, event) {
		event.preventDefault();
		var data = {}, arr = [], err = null;
		$('.refPoints').each(function(i, v) {
			if ($.inArray($(v).find('input[name="min_ref"]').val(), arr) < 0) {
				arr[i] = $(v).find('input[name="min_ref"]').val();
				data[i] = {
					min_ref: $(v).find('input[name="min_ref"]').val(),
					points: $(v).find('input[name="points"]').val()
				}
			} else {
				err = 'You enter points amount for ' + $(v).find('input[name="min_ref"]').val() + ' refferals twice';
			}
		});
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			$.post('/settings/send_ref_points', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Points was successfully sended',
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
	},
    addRefPoints: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Minial refferals'
				})
			}).append($('<input/>', {
				type: 'number',
				name: 'min_ref'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Points'
            })
        }).append($('<input/>', {
            type: 'number',
            name: 'points'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
	sendFormula: function(el, event) {
		event.preventDefault();
		var data = {}, arr = [], err = null;
		$('.refPoints').each(function(i, v) {
			if ($.inArray($(v).find('input[name="min_price"]').val(), arr) < 0) {
				arr[i] = $(v).find('input[name="min_price"]').val();
				data[i] = {
					min_price: $(v).find('input[name="min_price"]').val(),
					mark_up: $(v).find('input[name="mark-up"]').val()
				}
			} else {
				err = 'You enter mark-up amount for ' + $(v).find('input[name="min_price"]').val() + ' refferals twice';
			}
		});
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			$.post('/settings/send_price_formula', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Mark-up was successfully sended',
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
	},
    addFormula: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Minial refferals'
				})
			}).append($('<input/>', {
				type: 'number',
				step: '0.001',
				name: 'min_price'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Mark-up'
            })
        }).append($('<input/>', {
            type: 'number',
			step: '0.001',
            name: 'mark-up'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
	openRefferal: function(id, name) {
		mdl.open({
            id: 'editGroup',
            width: 500,
            title: lang[153],
            content: '<div class="iGroup"><label>' + lang[154] + ':</label><input type="text" name="refName" value="' + (name || '') + '"></div>\
					<div class="sGroup"><button class="btn btnSubmit" type="submit" onclick="Settings.sendRefferal(' + id + ');"><span class="fa fa-save"></span> ' + (id ? lang[42] : lang[91]) + '</button></div>'
        });
	}, sendRefferal: function(id) {
		var data = {
            id: id
        }
        if (!$('input[name="refName"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
        } else {
            data.name = $('input[name="refName"]').val();
            $.post('/settings/send_refferal', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[155],
                        delay: 2
                    });
                    mdl.close('editGroup');
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
    delRefferal: function(id) {
        $.post('/settings/del_refferal', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#refferal_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, privileges: function(name) {
        $('[name^="privileges"]').each(function(i, e) {
            $(e).attr('name', e.name.replace(/privileges\[(\w+)\]\[(\w+)\]/g, function(a, b) {
                return a.replace(b, name);
            }))
        });
        $.post('/settings/privileges', {
            group: name
        }, function(e) {
            $.each(e, function(a, b) {
                var el = $('[name="privileges[' + name + '][' + a + ']"]');
                el.find('option').prop('selected', false).trigger('change');
                if ($.isArray(b)) {
                    $.each(b, function(i, v) {
                        el.find('option[value="' + v + '"]').prop('selected', true).trigger('change');
                    });
                } else {
                    if (el.attr('type') == 'checkbox')
                        el.prop('checked', b == 1 ? true : false).val(b);
                    else
                        el.find('option[value="' + b + '"]').prop('selected', true).trigger('change');
                }
            });
        }, 'json');
    },
    save: function(f, event) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {},
            form = $(f).find('[name]'),
            err = null;
        for (var i = 0; i < form.length; i++) {
            data[form[i].name] = $(form[i]).val();
        }
        console.log(data);
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        $.post('/settings/save', data, function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[1],
                    delay: 2
                })
            } else if (r == 'RELOAD') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[1],
                    delay: 2
                });
                var time = setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    },
    tab: function(a, p) {
        location.hash = a + (p ? '#' + p : '');
        $('.uForm > .tab').hide();
        $('.uForm > .tab[id="' + a + '"]').show();
        $('#stitle').text(a);
        if (location.hash.match(/Groups/))
            $('.btnAddGr').show();
        else
            $('.btnAddGr').hide();
        if (a != 'general') $('#' + a).html('<div class="spnCtnr"><span class="fa fa-cog fa-spin fa-3x fa-fw"></span></div>').load(location.pathname + '/' + a.toLocaleLowerCase(), Page.init);
    },
    addGroup: function(id, name, pay, timer) {
        mdl.open({
            id: 'editGroup',
            width: 500,
            title: lang[144],
            content: '<div class="iGroup"><label>Group name:</label><input type="text" name="grName" value="' + (name || '') + '"></div>\
					<div class="iGroup"><label>Paying per hour:</label><input type="checkbox" name="pay" ' + (pay == 1 ? 'checked' : '') + '></div>\
					<div class="iGroup"><label>Timer:</label><input type="checkbox" name="timer" ' + (timer == 1 ? 'checked' : '') + '></div>\
					<div class="sGroup"><button class="btn btnSubmit" type="submit" onclick="Settings.sendGroup(' + id + ');"><span class="fa fa-save"></span> ' + (id ? lang[42] : lang[91]) + '</button></div>',
			cb: function() {
				$('#page').checkbox();
			}
        });
    },
    sendGroup: function(id) {
        var data = {
            id: id
        }
        if (!$('input[name="grName"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
        } else {
            data.name = $('input[name="grName"]').val();
            data.pay = $('input[name="pay"]').val();
            data.timer = $('input[name="timer"]').val();
            $.post('/settings/add_group', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[143],
                        delay: 2
                    });
                    mdl.close('editGroup');
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
    delGroup: function(id) {
        $.post('/settings/del_group', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#group_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendForm: function(a, b, c) {
        b.preventDefault();
        loadBtn.start($(b.target).find('button[type="submit"]'));
        var data = {
                id: c
            },
            err = null;
        $.each($(a).find('[name]'), function() {
            var val = $(this).val();
            if (!val) {
                err = this.name + ' ' + lang[142];
                return false;
            }
            data[this.name] = val;
        });
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(b.target).find('button[type="submit"]'));
            return;
        }
        $.post('/settings/save_form', data, function(e) {
            if (e) Page.get('/settings/forms/edit/' + e);
            loadBtn.stop($(b.target).find('button[type="submit"]'));
        });
    },
    delForm: function(id) {
        $.post('/settings/del_form', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#form_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, sendPoints: function(el, event) {
		event.preventDefault();
		$.post('/settings/save_points', $(el).serializeArray(), function(r) {
			if (r == 'OK') {
				Page.get('/settings/points');
			} else {
				alr.show({
					class: 'alrShow',
					content: lang[13],
					delay: 2
				});
			}
		})
	}
};