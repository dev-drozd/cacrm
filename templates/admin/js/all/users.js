// Users
var user = {
	spage: 0,
	onsiteStats: function(id) {
		user.spage = 0;
		$.post('/users/onsite_stats', {id: id}, function(r) {
			mdl.open({
				id: 'onsiteStats',
				title: 'Statictic',
				width: 600, 
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'stats',
						html: r.content
					})
				}).append($('<button/>', {
					class: 'btn btnLoad' + (r.left_count > 0 ? '' : ' hdn'),
					onclick: 'user.doloadStats(this,' + id + ');',
					html: 'Load statistic'
				}))
			});
		}, 'json');
	}, doloadStats: function(e, id) {
		cash.cpage += 1;
		$.post('/users/onsite_stats', {
			id: id, 
			page: user.spage
		}, function(r) {
			$('.stats').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	},
	addNote: function(id) {
        var data = {
                id: id
            },
            err = null;
        $('textarea[name="note"]').css('border-color', '#ddd');
        if (!$('textarea[name="note"]').val()) {
            $('textarea[name="note"]').css('border-color', '#f00');
            alr.show({
                class: 'alrDanger',
                content: lang[78],
                delay: 2
            });
        } else {
            data.note = $('textarea[name="note"]').val();
            $.post('/users/send_note', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[77],
                        delay: 2
                    });
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
	createInvoice: function(id) {
        mdl.open({
            id: 'onsiteInvoice',
            title: 'Service price',
            content: $('<div/>', {
                class: 'uForm',
                html: $('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: 'Price for additional time'
                    })
                }).append($('<input/>', {
                    type: 'number',
                    name: 'onsite_price',
                    step: '0.001',
                    min: 0
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    type: 'button',
                    class: 'btn btnSubmit',
                    onclick: 'user.sendOnsiteInvoice(' + id + ');',
                    html: 'Send'
                })
            }))
        });
    },
    sendOnsiteInvoice: function (id) {
        if ($('input[name="onsite_price"]').val() <= 0) {
            alr.show({
                class: 'alrDanger',
                content: 'Price can not be empty',
                delay: 2
            });
        } else {
            $.post('/invoices/send_onsite', {
                id: id,
                price: $('input[name="onsite_price"]').val()
            }, function (r) {
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Invoice was successfully created',
                        delay: 2
                    });
                    Page.get('/invoices/edit/' + r);
                    mdl.close();
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
	playOnsite: function(id, a, os_id) {
		mdl.open({
				id: 'playService',
				title: 'Choose store',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
						class: 'iGroup',
						id: 'store',
						html: $('<label/>', {
							html: 'Store'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'store'
					})).append($('<ul/>', {
						class: 'hdn'
					}))
				}).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Note'
					})
				}).append($('<textarea/>', {
					name: 'onsite_note'
				}))).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit ac',
						type: 'button',
						onclick: 'user.updateOnsite(' + id + ', \'' + a + '\', ' + os_id + ');',
						html: 'Done'
					})
				}).append($('<button/>', {
						class: 'btn btnSubmit dc',
						type: 'button',
						onclick: 'mdl.close();',
						html: 'Cancel'
					}))),
				cb: function() {
					$.post('/users/objects', {}, function (r) {
						if (r) {
							var items = '', lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
								lId = v.id;
							});
							if (r.list.length > 1) {
								$('#store > ul').html(items).sForm({
									action: '/users/objects',
									data: {
										lId: lId,
										query: $('#store > .sfWrap input').val() || ''
									},
									all: false,
									select: $('input[name="store"]').data(),
									s: true
								}, $('input[name="store"]'));
							} else if (r.list.length == 1) {
								var cash_object = {};
								cash_object[r.list[0].id] = {
									name: r.list[0].name
								}
								$('input[name="store"]').data(cash_object);
								$('#store').append($('<div/>', {
									class: 'storeOne',
									html: r.list[0].name
								}));
							}
						}
					}, 'json');
				}
			});
	},
	updateOnsiteNote: function(id, a, os_id) {
		mdl.open({
				id: 'playService',
				title: 'Add note',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
							html: 'Note'
						})
					}).append($('<textarea/>', {
						name: 'onsite_note'
					}))
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit ac',
						type: 'button',
						onclick: 'user.updateOnsite(' + id + ', \'' + a + '\', ' + os_id + ');',
						html: 'Done'
					})
				}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'Cancel'
				})))
			});
	},
	updateOnsite: function(id, a, os_id) {
		var store = 0,
			err = null;
		if (a == 'play' && !Object.keys($('input[name="store"]').data()).join(',').length) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, select store',
				delay: 2
			});
		} else {
			if (a == 'play') store = Object.keys($('input[name="store"]').data()).join(',');
			$.post('/users/update_onsite', {
				id: id,
				action: a,
				onsite: os_id,
				store: store,
				note: $('textarea[name="onsite_note"]').val()
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Status Updated',
						delay: 2
					});
					Page.get(location.href);
					mdl.close();
				} else if (r == 'no_store') {
					alr.show({
						class: 'alrDanger',
						content: 'Please, select store',
						delay: 2
					});
				} else if (r == 'done') {
					alr.show({
						class: 'alrDanger',
						content: 'This service is already ' + (a == 'stop' ? 'stopped' : (a == 'play' ? 'played' : 'paused')) + '. Please, reload page and try again',
						delay: 2
					});
				} else if (r == 'complited') {
					alr.show({
						class: 'alrDanger',
						content: 'This service is already complited',
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
	addOnsite: function(id) {
		/* $.post('/inventory/AllOnsite', {}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
					lId = v.id;
				});
            } */
            $.post('/users/objects', {}, function(or) {
                var oitems = '', olId = 0;
                if (or.list) {
                    $.each(or.list, function(oi, ov) {
                        oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
                        olId = ov.id;
                    });
                }
                mdl.open({
                    id: 'addOnsite',
                    title: 'On site service',
                    content: $('<div/>', {
                        class: 'uForm',
                        html: $('<div/>', {
							class: 'iGroup fw price',
							id: 'object',
							html: $('<label/>', {
								html: 'Store'
							})
						}).append($('<input/>', {
							type: 'hidden',
							name: 'object'
						})).append($('<ul/>', {
							class: 'hdn',
							html: oitems
						}))
					}).append($('<div/>', {
                            class: 'iGroup fw price',
                            id: 'service',
                            html: $('<label/>', {
                                html: 'Service'
                            })
                        }).append($('<input/>', {
                            type: 'hidden',
                            name: 'service'
                        })).append($('<ul/>', {
                            class: 'hdn'
					}))).append($('<div/>', {
                        class: 'iGroup fw dGroup ',
                        html: $('<label/>', {
                            html: 'Start date'
                        })
                    }).append($('<input/>', {
                        type: 'text',
                        class: 'cl',				
                        name: 'date_start',
                        onclick: '$(this).next().show().parent().addClass(\'act\');'
                    })).append($('<div/>', {
                        id: 'sCalendar'
                    }))).append($('<div/>', {
                        class: 'iGroup fw dGroup ',
                        html: $('<label/>', {
                            html: 'End date'
                        })
                    }).append($('<input/>', {
                        type: 'text',
                        class: 'cl',				
                        name: 'date_end',
                        onclick: '$(this).next().show().parent().addClass(\'act\');'
                    })).append($('<div/>', {
                        id: 'eCalendar'
                    }))).append($('<div/>', {
                        class: 'sGroup',
                        html: $('<button/>', {
                            type: 'button',
                            class: 'btn btnSubmit',
                            onclick: 'user.sendService(this, ' + id + ');',
                            html: 'Done'
                        })
                    })),
                    cb: function() {
                        $('#sCalendar').calendar(function() {
                            $('input[name="date_start"]').val($('#sCalendar > input[name="date"]').val());
                            $('.dGroup > input + div').hide().parent().removeClass('act');
                        });
                        $('#eCalendar').calendar(function() {
                            $('input[name="date_end"]').val($('#eCalendar > input[name="date"]').val());
                            $('.dGroup > input + div').hide().parent().removeClass('act');
                        });
                        
                        $('.uForm').on('click', function(event) {
                            if (!$(event.target).hasClass('cl')) {
                                console.log($(event.target).hasClass('cl'));
                                $('.calendar').hide().parents('.iGroup').removeClass('act');
                            }
                        });
                        
                        /* $('#service > ul').html(items).sForm({
                            action: '/inventory/AllOnsite',
                            data: {
                                lId: lId,
                                query: $('#service > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="service"]').data(),
                            s: true
                        }, $('input[name="service"]')); */

                        $('#object > ul').html(oitems).sForm({
                            action: '/users/objects',
                            data: {
                                lId: olId,
                                query: $('#object > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="object"]').data(),
                            s: true
                        }, $('input[name="object"]'), user.getOnsite);
						
						user.getOnsite();
                    }
                });
            }, 'json');
		/* }, 'json'); */
	},
	getOnsite: function() {
		$.post('/inventory/AllOnsite', {oId: Object.keys($('input[name="object"]').data() || {}).join(',')}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
					lId = v.id;
				});
				
				if ($('#service > .sfWrap').length) {
					$('input[name="service"]').removeData();
					$('#service > .sfWrap').remove();
					$('#service').append($('<ul/>', {
						class: 'hdn'
					}));
				} 
				$('#service > ul').html(items).sForm({
					action: '/inventory/AllOnsite',
					data: {
						lId: lId,
						oId: Object.keys($('input[name="object"]').data() || {}).join(','),
						query: $('#service > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="service"]').data(),
					s: true
				}, $('input[name="service"]'));
			}
		}, 'json');
	},
	sendService: function(el, id) {
		loadBtn.start($(el));
		var data = {
			id: id,
			date_start: $('input[name="date_start"]').val(),
			date_end: $('input[name="date_end"]').val()
		}, err = null;
		if (!Object.keys($('input[name="service"]').data()).join(',')) 
			err = 'Please, choose service';
		else 
            data.service = Object.keys($('input[name="service"]').data()).join(',');
        if (!Object.keys($('input[name="object"]').data()).join(',')) 
			err = 'Please, choose object';
		else 
			data.object = Object.keys($('input[name="object"]').data()).join(',');
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/users/send_service', data, function(r) {
				loadBtn.stop($(el));
				if(r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'The service was successfully added',
						delay: 2
					});
					Page.get(location.href);
					mdl.close();
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
	webPhoto: function() {
		mdl.open({
			id: 'webPhoto',
			title: 'Make webcam capture',
			content: $('<div/>', {
				html: $('<div/>', {
					class: 'camera',
					html: $('<video/>', {
						id: 'video'
					})
				}).append($('<div/>', {
					class: 'aCenter',
					html: $('<button/>', {
						id: 'startbutton',
						class: 'btn btnWeb',
						type: 'button',
						html: 'Make capture'
					})
				})).append($('<img/>', {
					id: 'photo',
					class: 'hdn'
				}))
			}).append($('<canvas/>', {
				id: 'canvas'
			})).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'user.accPhoto()',
					html: 'Use photo'
				})
			})),
			cb: webcam.startup
		});
	}, accPhoto: function() {
		$('.imgGroup > figure > img').attr('src', $('#photo').attr('src')).attr('new', 1);
		$('dropdown').val('');
		mdl.close();
	}, newUsr: function() {
		mdl.open({
			id: 'newUser',
			title: 'New User',
			content: '<form class="uForm" method="post" onsubmit="user.addShort(this, event);">\
				<input type="hidden" name="group_id[]" value="5">\
				<div class="iGroup usr">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name">\
				</div>\
				<div class="iGroup usr">\
					<label>' + lang[161] + '</label>\
					<input type="text" name="lastname">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[162] + '</label>\
					<input type="text" name="zipcode">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[163] + '</label>\
					<input type="email" name="email">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[164] + '</label>\
					<input type="text" name="phone">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> ' + lang[28] + '</button>\
				</div>\
			</form>'
		});
	}, addShort: function(f, event) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
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
            } else if ((form[i].name == 'name' || form[i].name == 'lastname') && !$(f).find('input[name="company"]').val()) {
                if (!is_valid.name(form[i].value) || !is_valid.name(form[i].value)) {
                    err = lang[6];
                    break;
                }
            } 
			data.append(form[i].name, form[i].value);
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
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/users/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
				mdl.close();
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                
				var usr = {};
				usr[r] = {
				   name: $(f).find('input[name="name"]').val() + ' ' + $(f).find('input[name="lastname"]').val()
				}
				$('input[name="contact"]').data(usr);
				
				if($('#contact .sfWrap > div').length){
					$('#contact .sfWrap > div').find('i').triger('click');
				}
				$('#contact .sfWrap > div').prepend($('<span/>', {
				   class: 'nc',
				   id: r,
				   html: $(f).find('input[name="name"]').val() + ' ' + $(f).find('input[name="lastname"]').val()
				}).append($('<i/>')));
            } else if (r == 'err_email') {
				alr.show({
                    class: 'alrDanger',
                    content: lang[165],
                    delay: 2
                });
			} else if (r == 'Name_not_valid') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Name is incorrect',
                    delay: 2
                });
			} else if (r == 'Lastname_not_valid') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Lastname is incorrect',
                    delay: 2
                });
			} else if (r == 'err_login') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Login can not be empty',
                    delay: 2
                });
			} else if (r == 'err_image_type') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Image type is incorrect',
                    delay: 2
                });
			} else if (r == 'err_file_size') {
				alr.show({
                    class: 'alrDanger',
                    content: 'File is too large',
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
    }, comSelect: function(v) {
		if (v != 0) {
			console.log(v);
			$('.com').show()
			$('.usr').hide()
		} else {
			$('.com').hide()
			$('.usr').show()
		}
	}, add: function(f, event, id, steps) {
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
            /* } else if (form[i].name == 'phone') {
                if (!form[i].value.length) {
                    err = lang[4];
                    break;
                } */
            } else if (form[i].name == 'name' && $(f).find('input[name="company"]').val() == 0) {
                if (!is_valid.name(form[i].value) || !form[i].value.length) {
                    err = lang[6];
                    break;
                }
            } else if (form[i].name == 'lastname' && $(f).find('input[name="company"]').val() == 0) {
                if (!is_valid.name(form[i].value) || !form[i].value.length) {
                    err = 'Lastname can not be empty';
                    break;
                }
            } else if (form[i].name == 'cname' && $('input[name="company"]').val() == 1) {
				if (!form[i].value.length) {
					err = 'company name can not be empty';
					break;
				} 
            } else if (form[i].name == 'password') {
                if (form[i].value.length > 0 && form[i].value != $('input[name="password2"]').val()) {
                    err = lang[7];
                    break;
                }
			} else if (form[i].name == 'referral') {
                if (form[i].value == 0) {
                    err = 'Please, choose referal';
                    break;
                }
            }
			if ($.inArray(form[i].name, ['country', 'state', 'city', 'contact']) >= 0) {
				data.append(form[i].name, Object.keys($('input[name="' + form[i].name + '"]').data()).join(','));
			} else
				data.append(form[i].name, form[i].value.trim());
        }
		
		var phones = '',
			sms = 0;
		$('.sPhone').each(function (i, v) {
			$(v).find('input').css('border', '1px solid #ddd');
			if ($(v).find('select').val() || $(v).find('input').val()) {
				if ($(v).find('[name="phoneCode"]').val() == 0 || !$(v).find('[name="code"]').val() || !$(v).find('[name="part1"]').val()) {
					$(v).find('input').css('border', '1px solid #f00');
					err = 'Please, check phone number';
					return false;
				} else {
					if (phones.length > 0) phones += ',';
					phones += $(v).find('[name="phoneCode"]').val() + ' ' + $(v).find('[name="code"]').val() + ' ' + $(v).find('[name="part1"]').val() + ' ' + $(v).find('[name="part2"]').val();
					if ($(v).find('[name="sms"]').val() == 1) {
						data.append('sms', $(v).find('[name="phoneCode"]').val() + ' ' + $(v).find('[name="code"]').val() + ' ' + $(v).find('[name="part1"]').val())
						sms = 1;
					}
				}
			}
		});
        

        if (phones.length) {
            data.append('phone', phones);
            console.log(phones);
        } else if (!err) {
            err = 'Phone can not be empty';
        }
        if (!sms)
            data.append('sms', '');
		
		if ($('.dragndrop').length){
			var files = $('.dragndrop')[0].files;
			if (!$.isEmptyObject(files)) {
				for (var n in files) {
					data.append('image', files[n]);
				}
			}
		}
		if ($('.imgGroup > figure > img').attr('new')) {
			var pUsr = $('.imgGroup > figure > img').attr('src');
			data.append('image', b64Blob(pUsr.split(',')[1], 'image/png'));
			data.append('blob', 1);
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
		if ($('.dragndrop').length){
			var thumb = $('.dragndrop').prev();
			if (thumb[0].tagName != 'FIGURE' && id) data.append('del_image', true);
		}
		if (steps)
			data.append('steps', steps);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/users/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
				alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id)
                    Page.get(steps ? '/inventory/step/?user=' + r : '/users/view/' + r);
                else if(location.pathname.match(/\/users\/edit\/(.*)/i)){
					Page.get('/users/view/' + id);
				} else
                    Page.get('/inventory/step/?user=' + id);
				if ($('.proUser').attr('id') == 'u_' + r)
					$('li.user > a > u').html($('input[name="name"]').val().trim() + ' ' + $('input[name="lastname"]').val().trim());
			} else if (r == 'err_email') {
				alr.show({
					class: 'alrDanger',
					content: 'Email exists or incorrect',
					delay: 2
				});
			} else if (r == 'not_allowed') {
				alr.show({
					class: 'alrDanger',
					content: 'You are not allowed to do this',
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
    },
    del: function(id) {
        $.post('/users/del', {
            id: id
        }, function(r) {
            if (r == 'OK'){
				if ($('#user_' + id).length) {
					$('#user_' + id).addClass('deleted')
						.find('a[href="javascript:user.del('+id+');"]')
						.attr('href', 'javascript:user.restore('+id+');')
						.html('<span class="fa fa-times"></span> Restore user');
				} else 
					Page.get(location.href);
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, restore: function(id) {
        $.post('/users/restore', {
            id: id
        }, function(r) {
            if (r == 'OK'){
				if ($('#user_' + id).length) {
					$('#user_' + id).removeClass('deleted')
						.find('a[href="javascript:user.restore('+id+');"]')
						.attr('href', 'javascript:user.del('+id+');')
						.html('<span class="fa fa-times"></span>  Delete user');
				} else 
					Page.get(location.href);
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, sendDetails: function(e, ev) {
		ev.preventDefault();
		if (!$('textarea[name="emails"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: lang[141],
				delay: 2
			});
		} else {
			loadBtn.start($(ev.target).find('button[type="submit"]'));
			$.post('/users/sendDetails', {data: $('textarea[name="emails"]').val()}, function(r) {
				loadBtn.stop($(ev.target).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: lang[140],
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
}