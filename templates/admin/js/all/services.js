console.log('HERE');
// Inventory
var inventory = {
	addUpcharge: function(id, name, price) {
		mdl.open({
			id: 'addUpcharge',
			title: 'Add request',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'name',
					value: name || ''
				}))
			}).append($('<div/>', {
				class: 'iGroup',
				html: $('<label/>', {
					html: 'Price'
				})
			}).append($('<input/>', {
				type: 'number',
				name: 'price',
				step: '0.001',
				min: '0',
				value: price || ''
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					html: 'Send',
					onclick: 'inventory.sendUpcharge(this' + (id ? ', ' + id : '') + ');'
				})
			}))
		});
	},
	sendUpcharge: function(el, id) {
		loadBtn.start($(el));
		var data = {
				id: id
			}, 
			err = null;
		
		$(el).parents('.uForm').find('input').css('border', '1px solid #ddd');
		if (!$('input[name="name"]').val()) {
			err = 'Please, enter name';
			$('input[name="name"]').css('border', '1px solid #f00');
		} else if (!$('input[name="price"]').val()) {
			err = 'Please, enter price';
			$('input[name="price"]').css('border', '1px solid #f00');
		} else {
			data.name = $('input[name="name"]').val();
			data.price = $('input[name="price"]').val();
		}
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/inventory/send_upcharge', data, function(r) {
				if (r == 'forbidden') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r == 'OK') {
					Page.get(location.href);
					mdl.close();
					alr.show({
						class: 'alrSuccess',
						content: 'Upcharge service was successfully ' + (id ? 'edited' : 'added'),
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(el));
			});
		}
	},
	delUpcharge: function(id) {
        $.post('/inventory/del_upcharge', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#upcharge_' + id).remove();
			else if (r == 'no_acc') {
                alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    onsiteForms: function (el, event) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
		var data = {}, 
			err = null,
			f = $(el);
			
        data.sms_form = $('textarea[name="sms_form"]').val();
        data.email_form = $('textarea[name="email_form"]').val();
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(event.target).find('button[type="submit"]'));
		} else {
			$.post('/inventory/send_onsite_forms', data, function(r) {
				if (r == 'no_acc') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Forms were successfully saved',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(event.target).find('button[type="submit"]'));
			});
		}
    },
	delRequest: function(id) {
        $.post('/inventory/del_request', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#request_' + id).remove();
			else if (r == 'no_acc') {
                alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	requestConfirm: function(id) {
		$.post('/inventory/confirm_request', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Request successfully confirmed',
					delay: 2
				});
				$('#request_' + id).removeClass('not-confirmed').addClass('confirmed').find('.nIssue > a').remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
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
	changeRequestType: function() {
		if ($('select[name="type_request"]').val() == 'service') 
			$('.typePrice').show();
		else
			$('.typePrice').hide().find('input').val(0);
	},
	addRequest: function(id, name, type, price) {
		mdl.open({
			id: 'addRequest',
			title: 'Add request',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Type'
					})
				}).append($('<select/>', {
					name: 'type_request',
					onchange: 'inventory.changeRequestType();',
					html: $('<option/>', {
						value: 'type',
						html: 'Type'
					})
				}).append($('<option/>', {
					value: 'brand',
					html: 'Brand'
				})).append($('<option/>', {
					value: 'service',
					html: 'Service'
				})))
			}).append($('<div/>', {
				class: 'iGroup',
				html: $('<label/>', {
					html: 'Name'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'name',
				value: name || ''
			}))).append($('<div/>', {
				class: 'iGroup typePrice',
				style: 'display: none;',
				html: $('<label/>', {
					html: 'Price'
				})
			}).append($('<input/>', {
				type: 'number',
				name: 'price',
				step: '0.1',
				min: '0',
				value: price || ''
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					html: 'Send',
					onclick: 'inventory.sendRequest(this' + (id ? ', ' + id : '') + ');'
				})
			})),
			cb: function() {
				$('#page').select();
				$('select[name="type_request"]').val(type || 'type').trigger('change');
			}
		});
	},
	sendRequest: function(el, id) {
		loadBtn.start($(el));
		var data = {
				id: id
			}, 
			err = null;
		
		$(el).parents('.uForm').find('input').css('border', '1px solid #ddd');
		if (!$('input[name="name"]').val()) {
			err = 'Please, enter name';
			$('input[name="name"]').css('border', '1px solid #f00');
		} else if ($('select[name="type_request"]').val() == 'service' && !$('input[name="price"]').val()) {
			err = 'Please, enter price';
			$('input[name="price"]').css('border', '1px solid #f00');
		} else {
			data.type = $('select[name="type_request"]').val();
			data.name = $('input[name="name"]').val();
			data.price = $('input[name="price"]').val();
		}
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/inventory/send_request', data, function(r) {
				if (r == 'forbidden') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r == 'OK') {
					Page.get(location.href);
					mdl.close();
					alr.show({
						class: 'alrSuccess',
						content: 'Request was successfully ' + (id ? 'edited' : 'added'),
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(el));
			});
		}
	},
	addOnsite: function(el, event, id) {
		event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
		var data = {
				id: id
			}, 
			err = null,
			f = $(el);
			
		f.find('label').next().css('border', '1px solid #ddd');
		f.find('.iGroup').each(function(i, v) {
			if ($.inArray($(v).find('label').next().attr('name'), ['name', ($('select[name="type"]').val() == 'call' ? 'calls' : 'time'), 'price']) >= 0 && !$(v).find('label').next().val()) {
				err = 'Please, enter ' + $(v).find('label').text();
				$(v).find('label').next().css('border', '1px solid #f00');
			} else
				data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
		});
		data.store = Object.keys(f.find('input[name="store"]').data()).join(',');
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(event.target).find('button[type="submit"]'));
		} else {
			$.post('/inventory/send_onsite', data, function(r) {
				if (r == 'forbidden') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r > 0) {
					Page.get('/inventory/onsite/edit/' + r);
					alr.show({
						class: 'alrSuccess',
						content: 'On site service was successfully ' + (id ? 'edited' : 'added'),
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(event.target).find('button[type="submit"]'));
			});
		}
	},
	delOnsite: function(id) {
        $.post('/inventory/del_onsite', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#onsite_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	confirmOnsite: function(id, el) {
        $.post('/inventory/confirm_onsite', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Service successfully confirmed',
					delay: 2
				});
				$(el).parent().remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
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
	confirmed: function(id, el) {
		$.post('/inventory/confirm', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Service successfully confirmed',
					delay: 2
				});
				if ($('#inventory_' + id).length)
					$('#inventory_' + id).removeClass('not-confirmed').find('.confBtn > a').remove();
				else 
					$(el).parent().remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
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
	addModel: function(id, n, p) {
        $.post('/inventory/allCategories', {
            id: id || '',
			type: 'inventory',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'add_model',
                    title: (id ? lang[17] : lang[18]) + ' Model',
                    content: '<form  method="post" onsubmit="inventory.sendModel(this, event, ' + (id || 0) + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup" id="brand">\
							<label>Brand</label>\
							<input type="hidden" name="brand">\
							<ul class="hdn">' + items + '</ul>\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        nIds = [];
                        $('input[name="brand"]').data(p);
                        $('#brand > ul').sForm({
                            action: '/inventory/allCategories',
                            data: {
                                lId: lId,
                                query: $('#brand > input').val(),
                                nIds: Object.keys(p || {}).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: p,
                            s: true
                        }, $('input[name="brand"]'));
                    }
                });
            }
        }, 'json');
    },
    sendModel: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null;
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        data.append('id', id);
        if (!form.find('input[name="name"]').val().length) {
            form.find('input[name="name"]').css('border', '1px solid #f00');
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        } else {
            data.append('name', form.find('input[name="name"]').val());
            data.append('brand', Object.keys(form.find('input[name="brand"]').data()).join(','));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/sendModel',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[8],
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close();
                }
            });
        }
    },
    delModel: function(id) {
        $.post('/inventory/delModel', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invModel_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	newType: function() {
		mdl.open({
			id: 'newType',
			title: 'New Type',
			content: $('<div/>', {
				class: 'form',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'name'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'inventory.sendNewType(this);',
					html: 'Send'
				})
			}))
		})
	}, sendNewType: function(el) {
		var f = $(el).parents('.form');
		loadBtn.start($(el));
		if (!f.find('input[name="name"]').val()){
			alr.show({
				class: 'alrDanger',
				content: 'Name can not be empty',
				delay: 2
			});
		} else {
			$.post('/inventory/new_type', {name: f.find('input[name="name"]').val()}, function(r) {
				if (r > 0) {
				   $('select[name="type_id"]').append($('<option/>', {
					   value: r,
					   html: f.find('input[name="name"]').val()
				   }));
				   $('select[name="type_id"]').next().remove();
				   $('select[name="type_id"]').val(r);
				   $('#page').select();
				   mdl.close();
				}
			});
		}
	}, newBrand: function() {
		mdl.open({
			id: 'newBrand',
			title: 'New Brand',
			content: $('<div/>', {
				class: 'form',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'name'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'inventory.sendNewBrand(this);',
					html: 'Send'
				})
			}))
		})
	}, sendNewBrand: function(el) {
		var f = $(el).parents('.form');
		loadBtn.start($(el));
		if (!f.find('input[name="name"]').val()){
			alr.show({
				class: 'alrDanger',
				content: 'Name can not be empty',
				delay: 2
			});
		} else {
			$.post('/inventory/new_brand', {name: f.find('input[name="name"]').val()}, function(r) {
				if (r > 0) {
				   var brand = {};
				   brand[r] = {
					   name: f.find('input[name="name"]').val()
				   }
				   $('input[name="category"]').data(brand);
				   $('#category .sfWrap > div').prepend($('<span/>', {
					   class: 'nc',
					   id: r,
					   html: f.find('input[name="name"]').val()
				   }).append($('<i/>')));
				   mdl.close();
				}
			});
		}
	}, dub: function(id) {
		$.post('/inventory/dub_service', {id: id}, function(r) {
			if (r > 0) {
				Page.get('/inventory/edit/service/' + r);
				alr.show({
					class: 'alrSuccess',
					content: 'Service was successfully dublicated',
					delay: 2
				})
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
    sendPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('status_', ''),
                pr: $(v).find('input').val()
            }
        });
        if (data) {
            $.post('/inventory/stPriority', data, function(r) {
                //console.log(r);
            });
        }
    },
    getGroup: function(id, iId, f) {
		var items = '', lId = 0;
		$.post('/inventory/allModels', {
			type: Object.keys($('input[name="category"]').length ? $('input[name="category"]').data() : $('input[name="brand"]').data()).join(',')
		}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!f) {
					$('#model > input').removeData();
					$('#model > input').next().remove();
					$('#model').append($('<ul/>', {
						html: items
					}));
				}
				if (!brand_id && model_id) {
					$('#model > input').data(model_id);
					model_id = '';
				}
				$('#model > ul').sForm({
					action: '/inventory/allModels',
					data: {
						query: $('#model > .sfWrap input').val(),
						type: Object.keys($('input[name="category"]').length ? $('input[name="category"]').data() : $('input[name="brand"]').data()).join(',')
					},
					all: false,
					select: $('input[name="model"]').data(),
					s: true
				}, $('input[name="model"]'), null, {
					type: "category"
				})
			
				if (id) {
					$.post('/inventory', {
						type_id: id,
						inventory: iId
					}, function(r) {
						$('#forms').html(r);
						Page.init();
						if ($('.mdl').length) {
							p = ($(window).height() - $('.mdl').height()) / 2;
							$('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
						}
					}, 'json');
				}
			}
		}, 'json');
    },
    addGroup: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id,
                opts: {}
            },
            form = $(f),
            err = null;
        form.find('input').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data['name'] = form.find('input[name="name"]').val();
            /* data['type'] = form.find('select[name="type"]').val();
            data['brand'] = Object.keys(form.find('input[name="brand"]').data()).join(','); */
            form.find('.optGroup').each(function(i, val) {
                data.opts[i] = {
                    type: $(val).find('select[name="type"]').val(),
                    req: $(val).find('input[name="req"]').val()
                }
                if (!$(val).find('input[name="oName"]').val().length) {
                    err = lang[16];
                    $(val).find('input[name="oName"]').css('border-color', '#f00');
                } else
                    data.opts[i].name = $(val).find('input[name="oName"]').val();
                data.opts[i].id = $(val).find('input[name="oName"]').attr('data-id');
                if (data.opts[i].type == 'select') {
                    data.opts[i].mult = $(val).find('input[name="sMul"]').val();
                    data.opts[i].sOpts = {};
                    $(val).find('input[name="opt"]').each(function(sI, sVal) {
                        if (!$(sVal).val().length) {
                            err = lang[130];
                            $(sVal).css('border-color', '#f00');
                        } else
                            data.opts[i].sOpts[$(sVal).attr('data-id') || sI] = $(sVal).val();
                    });
                }
            });
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/send', data, function(res) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (res == 'ERROR') {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[131] + (res == 'SAVE' ? lang[24] : lang[23]),
                        delay: 2
                    });
                    Page.get('/inventory/types/edit/' + id);
                }
            });
        }
    },
    del: function(id) {
        $.post('/inventory/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
				if ($('#inventory_' + id).length)
					$('#inventory_' + id).remove();
				else 
					Page.get('/inventory');
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    delGroup: function (id) {
       $.post('/inventory/delGroup', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invGroup_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendCategory: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null;
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        data.append('id', id);
        if (!form.find('input[name="name"]').val().length) {
            form.find('input[name="name"]').css('border', '1px solid #f00');
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        } else {
            data.append('name', form.find('input[name="name"]').val());
            //data.append('type', form.find('select[name="type"]').val());
            //$('.mdl').find('ul').sForm('get', $('input[name="parent"]'));
            data.append('parent', Object.keys(form.find('input[name="parent"]').data()));
			//data.append('type', Object.keys(form.find('input[name="types"]').data()));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/sendCategory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[8],
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('select_par');
                }
            });
        }
    },
    delCategory: function(id) {
        $.post('/inventory/delCategory', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invCategory_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    selParent: function(t, d, id) {
        $.post('/inventory/allCategories', {
            nIds: Object.keys($('input[name="' + d + '"]').data()).join(','),
            id: id
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: t,
                    content: '<ul class="hdn">' + items + '</ul>\
						<div class="sGroup"><button class="btn btnMdl" onclick="inventory.getParent(\'' + d + '\');">' + lang[130] + '</button></div>',
                    cb: function() {
                        nIds = [];
                        $('#select_par').find('ul').sForm({
                            action: '/inventory/allCategories',
                            data: {
                                lId: lId,
                                query: $('#select_par').find('input').val(),
                                nIds: Object.keys($('input[name="' + d + '"]').data()).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="' + d + '"]').data(),
                            s: true
                        }, $('input[name="' + el + '"]'));
                    }
                });
            }
        }, 'json');
    },
    getParent: function(el) {
        //$('#select_par').find('ul').sForm('get', $('input[name="' + el + '"]'));
        mdl.close('select_par');
    },
    addCategory: function(id, n, p, t) {
        $.post('/inventory/allCategories', {
            id: id || '',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
				/* $.post('/inventory/allTypes', {
					id: id || '',
					nIds: Object.keys(p || {}).join(',')
				}, function(r) {
					if (r) {
						types = '';
						$.each(r.list, function(i, v) {
							types += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						}); */
						mdl.open({
							id: 'select_par',
							title: (id ? lang[17] : lang[18]) + ' ' + lang[129],
							content: '<form  method="post" onsubmit="inventory.sendCategory(this, event, ' + (id || 0) + ');">\
								<div class="iGroup">\
									<label>' + lang[20] + '</label>\
									<input type="text" name="name" value="' + (n || '') + '">\
								</div>\
								<div class="iGroup" id="parent">\
									<input type="hidden" name="parent">\
									<div class="lbl">' + lang[128] + '</div>\
									<ul class="hdn">' + items + '</ul>\
								</div>\
								<div class="sGroup">\
									<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
								</div>\
							</form>',
							cb: function() {
								nIds = [];
								$('input[name="parent"]').data(p);
								$('#parent > ul').sForm({
									action: '/inventory/allCategories',
									data: {
										lId: lId,
										query: $('#parent > .sfWrap input').val() || '',
										nIds: Object.keys(p || {}).join(',') + ',' + id
									},
									all: (r.count <= 20) ? true : false,
									select: p,
									s: true
								}, $('input[name="parent"]'));
								
								/* $('input[name="types"]').data(t);
								$('#types > ul').sForm({
									action: '/inventory/allTypes',
									data: {
										lId: lId,
										query: $('#types > .sfWrap input').val() || '',
										nIds: Object.keys(p || {}).join(',') + ',' + id
									},
									all: (r.count <= 20) ? true : false,
									select: t,
									s: true
								}, $('input[name="types"]')); */
							}
						});
					/* }
				}, 'json'); */
		    }
        }, 'json');
    },
    openType: function(id, n, t, c, bId, bName) {
        $.post('/inventory/allCategories', {
            nIds: Object.keys(c || {}).join(','),
            type: t || ''
        }, function(r) {
            if (r) {
                var items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: (id ? lang[17] : lang[18]) + ' ' + lang[127],
                    content: '<form  method="post" onsubmit="inventory.sendType(this, event, ' + id + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        $('#page').select();
						if (bId) {
							var br = {};
							br[bId] = {
								name: bName
							}
							$('input[name="brand"]').data(br);
						}
						/* $.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data() || {}).join(',')}, function (r) {
							if (r){
								var items = '', lId = 0;
								$.each(r.list, function(i, v) {
									items += '<li data-value="' + v.id + '">' + v.name + '</li>';
									lId = v.id;
								});
								$('#brand > ul').html(items).sForm({
									action: '/inventory/allCategories',
									data: {
										lId: lId,
										nIds: Object.keys($('input[name="brand"]').data() || {}).join(','),
										query: $('#brand > .sfWrap input').val() || ''
									},
									all: (r.count <= 20) ? true : false,
									select: $('input[name="brand"]').data(),
									s: true
								}, $('input[name="brand"]'));
							}
						}, 'json'); */
                    }
                });
            }
        }, 'json');
    },
    sendType: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id,
                opts: {}
            },
            form = $(f),
            err = null;
        form.find('input').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data['name'] = form.find('input[name="name"]').val();
            data['type'] = form.find('select[name="type"]').val();
			//$('.catType ul').sForm('get', $('input[name="category"]'));
            //data['brand'] = Object.keys(form.find('input[name="brand"]').data()).join(',');
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/send', data, function(res) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (res == 'ERROR') {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[125] + (res == 'SAVE' ? lang[24] : lang[23]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('select_par');
                }
            });
        }
    },
    catChange: function(t, el) {
		if (t != 'service')
			$('#brand').show();
		else
			$('#brand').hide();
       /*  $.post('/inventory/allCategories', {
            type: t || ''
        }, function(r) {
            if (r) {
                var items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                el.find('input').val('').parent().find('.sfWrap').replaceWith($('<ul/>', {
                    class: 'hdn',
                    html: items
                }));
                el.find('ul').sForm({
                    action: '/inventory/allCategories',
                    data: {
                        type: t || '',
                        lId: lId,
                        query: $('#select_par').find('input').val()
                    },
                    all: (r.count <= 20) ? true : false,
                    s: true
                });
            }
        }, 'json'); */
    },
    addInv: function(f, event, id, n) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null,
			intype = location.pathname.indexOf('service') >= 0 ? 'service' : 'stock';
        data.append('id', id);
        
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if ((!form.find('input[name="category"]').length && !form.find('input[name="category"]').next().find('input').val()) && !form.find('input[name="name"]').length) {
            err = lang[124];
        } else if (!form.find('input[name="category"]').length && !form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else if (form.find('input[name="type_id"]').length && !Object.keys(form.find('input[name="type_id"]').data()).join(',')) {// && !form.find('input[name="type_id"]').next().find('input').val()
            err = 'Type can not be empty';
        } else if (form.find('input[name="category"]').length && !Object.keys(form.find('input[name="category"]').data()).join(',')) {// && !form.find('input[name="model"]').next().find('input').val()
            err = 'Brand can not be empty';
        } else if (form.find('input[name="model"]').length && !Object.keys(form.find('input[name="model"]').data()).join(',') && !form.find('input[name="model"]').next().find('input').val()) {
            err = 'Model can not be empty';
        } else if (!Object.keys(form.find('input[name="object"]').data()).join(',')) {
            err = 'Please, select store';
        } else if (form.find('input[name="location"]').length && !Object.keys(form.find('input[name="location"]').data()).join(',') && !form.find('input[name="location"]').next().find('input').val()) {
            err = 'Location can not be empty';
        } else if (form.find('select[name="sublocation"] > option[value="1"]').length && form.find('select[name="sublocation"]').val() == 0) {
            err = 'Sublocation can not be empty';
        } else {
            if ($(f).find('select[name="type_id"]').val() == 0) {
                err = lang[123];
            } else {
                /* $('form > div:not(.sTitle)').each(function(i, v) {
					if (!$(v).find('label').next().val() && $(v).find('label').next().attr('name')) 
						data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data() || {}).join(',');
					else if($(v).find('label').next().attr('name')) 
						data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
				}); */
                //data.customer = Object.keys(form.find('input[name="customer"]').data() || {}).join(',');
                if ($(f).find('input[name="name"]').val()) data.append('name', $(f).find('input[name="name"]').val());
                data.append('price', $(f).find('input[name="price"]').val());
                data.append('purchase-price', $(f).find('input[name="purchase-price"]').val());
                //if ($(f).find('select[name="type_id"]').val()) data.append('type', $(f).find('select[name="type_id"]').val());
                data.append('smodel', $(f).find('input[name="smodel"]').val() || '');
                data.append('os', $(f).find('select[name="os"]').val() || '');
                data.append('vendor', $(f).find('select[name="vendor"]').val() || '');
                data.append('owner_type', $(f).find('select[name="owner_type"]').val() || 'external');
                data.append('location_count', $(f).find('select[name="sublocation"]').val() || '');
                data.append('os_version', $(f).find('input[name="os_version"]').val() || '');
                data.append('serial', $(f).find('input[name="serial"]').val() || '');
                data.append('barcode', $(f).find('input[name="barcode"]').val() || '');
                data.append('descr', $(f).find('textarea[name="descr"]').val() || '');
                data.append('purchace', $(f).find('input[name="purchace"]').val() || '');
                data.append('quantity', $(f).find('input[name="quantity"]').val() || '');
                data.append('main', $(f).find('input[name="main"]').val());
                data.append('charger', $(f).find('input[name="charger"]').val());
                data.append('opt_charger', $(f).find('input[name="opt_charger"]').val());
                data.append('save_data', $(f).find('input[name="save_data"]').val());
                data.append('sd_comment', $(f).find('textarea[name="sd_comment"]').val());
                data.append('parts', $(f).find('input[name="parts"]').val());
                data.append('time', $(f).find('input[name="time"]').val());
				
				data.append('category_new', $(f).find('input[name="category"]').next().find('input').val());
				data.append('category', Object.keys($(f).find('input[name="category"]').data() || {}).join(','));
				
				data.append('type_new', $(f).find('input[name="type_id"]').next().find('input').val());
				data.append('type', Object.keys($(f).find('input[name="type_id"]').data() || {}).join(','));
				
				data.append('model_new', $(f).find('input[name="model"]').next().find('input').val());
				data.append('model', Object.keys($(f).find('input[name="model"]').data() || {}).join(','));
				
				data.append('location_new', form.find('input[name="location"]').next().find('input').val());
				data.append('location', Object.keys(form.find('input[name="location"]').data() || {}).join(','));

                data.append('object', Object.keys(form.find('input[name="object"]').data() || {}).join(','));
                data.append('object_owner', Object.keys(form.find('input[name="object_owner"]').data() || {}).join(','));
                data.append('status', Object.keys(form.find('input[name="status"]').data() || {}).join(','));
                data.append('storeCat', Object.keys(form.find('input[name="storeCat"]').data() || {}).join(','));
                
                //data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                var options = {};
				if (n) {
					data.append('customer', form.find('input[name="customer"]').val());
					data.append('intype', 'stock');
				} else {
					data.append('customer', Object.keys(form.find('input[name="customer"]').data() || {}).join(','));
					if (location.pathname.indexOf('service') >= 0)
						data.append('intype', 'service');
					else
						data.append('intype', 'stock');
				}
				if (location.pathname.indexOf('service') >= 0) {
					i = 0;
					form.find('#steps > .iGroup').each(function(i, val) {
						if ($(val).find('input').val())
							options[i] = {
								id: $(val).find('input').attr('data-id') || i,
								name: $(val).find('input').val()
							}
						i ++;
					});
				} else {
					form.find('#forms > .iGroup').each(function(i, val) {
						if ($(val).find('label').next().val())
							options[$(val).find('label').next().attr('data-id')] = $(val).find('label').next().val();
					});
				}
                
                data.append('opts', JSON.stringify(options));
                if ($('.dragndrop').length) {
                    var files = $('.dragndrop')[0].files,
                        del = $('.dragndrop')[0].delete;
                    if (del) {
                        $.each(del, function() {
                            data.append('delete[]', this.split('thumb_')[1]);
                        });
                    }
                    if (!$.isEmptyObject(files)) {
                        for (var n in files) {
                            data.append('image[]', files[n]);
                        }
                    }
                }
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else if (r == 'no_acc') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do this',
						delay: 2
					});
				} else if (r == 'cat_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Brand exists',
						delay: 2
					});
				} else if (r == 'tp_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Type exists',
						delay: 2
					});
				} else if (r == 'mdl_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Model exists',
						delay: 2
					});
				} else if (r == 'loc_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Locations exists. Check, please, maybe you can not use the location with current status',
						delay: 2
					});
				} else if (r == 'no_object') {
					alr.show({
						class: 'alrDanger',
						content: 'Please, select object',
						delay: 2
					});
				} else if (r == 'min_price') {
					alr.show({
						class: 'alrDanger',
						content:  'Min sale price can not be less than $' + parseFloat(min_price($(f).find('input[name="purchase-price"]').val())).toFixed(2),
						delay: 2
					});
				} else if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[122],
                        delay: 2
                    });
					if (location.pathname.indexOf('service') >= 0)
						Page.get('/inventory/service');
					else {
						if ($_GET('backusr'))
							Page.get('/users/view/' + parseInt($_GET('backusr')));
						else
							Page.get(n ? '/issues/step/' + r : '/inventory/view/' + r);
					}
                }
            });
        }
    },
	changeStType: function(el) {
		console.log(el.value);
		if (el.value == 0) {
			console.log(1);
			$('#points').show();
			$('#addPoint').show();
			$('#minus').hide();
			$('input[name="forfeit"]').val(0);
		} else {
			$('#points').hide();
			$('#addPoint').hide();
			$('#minus').show();
		}
	},
    openStatus: function(id, data, name, forfeit, sms, smsForm) {
		$.post('/settings/allForms', {}, function(r) {
			forms = '';
			if (r) {
				$.each(r.list, function(i, v) {
					forms += '<option value="' + v.id + '" ' + (smsForm == v.id ? 'selected' : '') + '>' + v.name + '</option>';
				});
			}
			mdl.open({
				id: 'openStatus',
				title: (id ? lang[17] : lang[18]) + lang[121],
				cb: function() {
					if (smsForm) $('select[name="smForm"]').val(smsForm);
					$('#page').select();
					$('#page').checkbox();
				},
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: lang[20]
						})
					}).append($('<input/>', {
						type: 'text',
						name: 'name',
						value: name || ''
					}))
				}).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: lang[120]
					})
				}).append($('<input/>', {
					type: 'checkbox',
					onchange: 'inventory.changeStType(this);',
					name: 'fine',
					value: (forfeit ? 1 : 0)
				}))).append($('<div/>', {
					id: 'points',
					class: forfeit ? 'hdn' : ''
				})).append($('<div/>', {
					id: 'minus',
					class: forfeit ? '' : 'hdn',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<input/>', {
							type: 'text',
							name: 'forfeit',
							value: forfeit || '',
							placeholder: lang[120]
						})
					})
				})).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Send sms'
					})
				}).append($('<input/>', {
					type: 'checkbox',
					name: 'sms',
					onchange: 'inventory.changeStSms(this);',
					value: (sms ? 1 : 0)
				}))).append($('<div/>', {
					id: 'sms',
					class: sms ? '' : 'hdn',
					html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Form'
						})
					}).append($('<select/>', {
							name: 'smsForm',
							html: forms
						}))
				})).append($('<div/>', {
					class: 'iGroup sGroup',
					id: 'addPoint',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						onclick: 'inventory.addPoint(this)',
						html: lang[119]
					})
				})).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						onclick: 'inventory.sendStatus(this, ' + id + ')',
						html: id ? lang[113] : lang[91]
					})
				}))
			});
			
			if (data) {
				if (sms == 1) $('input[name="sms"]').trigger('click');
				if (forfeit) $('input[name="fine"]').trigger('click');
				$.each(data, function(i, v) {
					$('#points').html($('<div/>', {
						class: 'iGroup pGroup',
						html: $('<div/>', {
							class: 'sSide',
							html: $('<label/>', {
								html: lang[116]
							})
						}).append($('<input/>', {
							type: 'number',
							min: 0,
							name: 'time',
							value: i
						}))
					}).append($('<div/>', {
						class: 'sSide',
						html: $('<label/>', {
							html: lang[115]
						})
					}).append($('<input/>', {
						type: 'number',
						min: 0,
						name: 'points',
						value: v
					}))).append($('<span/>', {
						class: 'fa fa-times',
						onclick: '$(this).parent().remove();'
					})))
				})
				p = ($(window).height() - $('.mdl').height()) / 2;
				$('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
			}
		}, 'json')
    },
	changeStSms: function(e) {
		if ($(e).val() > 0) 
			$('#sms').removeClass('hdn');
		else 
			$('#sms').addClass('hdn');
	},
    sendStatus: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = lang[16];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
			data.forfeit = f.find('input[name="forfeit"]').val();
			data.sms = f.find('input[name="sms"]').val();
			data.smsForm = f.find('select[name="smsForm"]').val();
            data.pointGroup = {};
            f.find('.pGroup').each(function(i, v) {
                if (!$(v).find('input[name="time"]').val().length) {
                    err = lang[119];
                    $(v).find('input[name="time"]').css('border-color', '#f00');
                } else if (!$(v).find('input[name="points"]').val().length) {
                    err = lang[118];
                    $(v).find('input[name="points"]').css('border-color', '#f00');
                } else {
                    data.pointGroup[$(v).find('input[name="time"]').val()] = $(v).find('input[name="points"]').val();
                }
            });
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/sendStatus', data, function(r) {
                loadBtn.stop($(this));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
                    mdl.close('openStatus');
                    Page.get(location.href);
                }
            }, 'json');
        }
    },
    delStatus: function(id) {
        if (id) {
            $.post('/inventory/delStatus', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#status_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
    statusHis: function(id) {
        $.post('/inventory/status_history', {id: id}, function(r) {
			if (r) {
				var items = '';
				$.each(r.list, function(i, v) {
					items += '<div class="iGroup stGroup"><label>' + v.status + '</label><label>' + v.name + ' ' + v.lastname + '</label><label>' + v.date + '</label></div>';
				});
				mdl.open({
					id: 'statusHis',
					width: 800,
					title: lang[117],
					content: items
				})
			} 
		}, 'json'); 
    },
    addPoint: function(el) {
        e = $(el).parent();
        $('#points').append($('<div/>', {
            class: 'iGroup pGroup',
            html: $('<div/>', {
                class: 'sSide',
                html: $('<label/>', {
                    html: lang[116]
                })
            }).append($('<input/>', {
                type: 'number',
                min: 0,
                name: 'time'
            }))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: lang[115]
            })
        }).append($('<input/>', {
            type: 'number',
            min: 0,
            name: 'points'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
        p = ($(window).height() - $('.mdl').height()) / 2;
        $('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
    },
    addLocation: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup lGroup',
            html: $('<input/>', {
                type: 'text',
                name: 'location',
                placeholder: lang[92]
            })
        }).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
    sendLocation: function(f, event, id) {
        event.preventDefault();
        var data = {
                id: id
            },
            err = null;
        $(f).find('input').css('border-color', '#ddd');
        loadBtn.start($(event.target).find('button[type="submit"]'));
        data.loc = Object.keys($('input[name="location"]').data() || {}).join(',');
        data.service = $('input[name="service"]').val();
        data.inventory = $('input[name="inventories"]').val();
        data.purchase = $('input[name="purchase"]').val();
		
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/location', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
					Page.get('/inventory/types/status');
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
    openOS: function(id, name) {
        mdl.open({
            id: 'openOS',
            title: (id ? lang[17] : lang[18]) + ' OS',
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[20]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'name',
                    value: name || ''
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendOS(this, ' + id + ')',
                    html: id ? lang[113] : lang[91]
                })
            }))
        });
    },
    sendOS: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = 'Name can not be empty';
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/save_os', data, function(r) {
                loadBtn.stop($(this));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[112],
                        delay: 2
                    });
                    mdl.close('openOS');
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
    delOS: function(id) {
        if (id) {
            $.post('/inventory/del_os', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#os_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
	openVendor: function(id, name) {
        mdl.open({
            id: 'openVendor',
            title: (id ? lang[17] : lang[18]) + ' Vendor',
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[20]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'name',
                    value: name || ''
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendVendor(this, ' + id + ')',
                    html: id ? lang[113] : lang[91]
                })
            }))
        });
    },
    sendVendor: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = 'Name can not be empty';
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/sendVendor', data, function(r) {
                loadBtn.stop($(this));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Vendor was successfully saved',
                        delay: 2
                    });
                    mdl.close('openVendor');
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
    delVendor: function(id) {
        if (id) {
            $.post('/inventory/delVendor', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#vendor_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
    toObject: function(id) {
        mdl.open({
            id: 'openMove',
            title: lang[111],
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[110]
                    })
                }).append($('<input/>', {
                    type: 'number',
                    min: '0',
                    step: '0.1',
                    name: 'purchase'
                }))
            }).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[109]
                })
            }).append($('<input/>', {
                type: 'number',
                name: 'sale',
                min: '0',
                step: '0.1'
            }))).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendToObject(this, ' + id + ')',
                    html: lang[91]
                })
            }))
        });
    },
	tradeConfirm: function(id, p) {
		mdl.open({
			id: 'confirmTradein',
			title: 'Tradein confirmation',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Price'
					})
				}).append($('<input/>', {
					type: 'number',
					step: '0.1',
					min: '0',
					name: 'price',
					value: p
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'inventory.sendTradeConf(this, ' + id + ')',
					html: 'Confirm'
				})
			}))
		})
	},
	sendTradeConf: function(el, id) {
		loadBtn.start($(el));
		var data = {
			id: id,
			price: 0
		}
		if (!$(el).parents('.uForm').find('input[name="price"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, enter price',
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			data.price = $(el).parents('.uForm').find('input[name="price"]').val();
			$.post('/inventory/confirm_tradein', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'You successfully confirmed tradein',
						delay: 2
					});
					Page.get(location.href);
					loadBtn.start($(el));
					mdl.close();
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
					loadBtn.stop($(el));
				}
			});
		}
	},
    sendToObject: function(el, id) {
        var form = $(el).parents('.form');
        if (!form.find('input[name="purchase"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[108],
                delay: 2
            });
            form.find('input[name="purchase"]').css('border-color', '#f00');
        } else {

            if (!form.find('input[name="sale"]').val()) {
                mdl.open({
                    id: 'openSend',
                    title: 'Warning',
                    content: $('<div/>', {
                        class: 'form',
                        html: $('<div/>', {
                            class: 'warning',
                            html: lang[107]
                        })
                    }).append($('<div/>', {
                        class: 'sGroup',
                        html: $('<button/>', {
                            class: 'btn btnSubmit',
                            onclick: 'inventory.contToObject(' + id + ')',
                            html: lang[106]
                        })
                    }).append($('<button/>', {
                        class: 'btn btnSubmit red',
                        onclick: 'mdl.close("openSend")',
                        html: lang[105]
                    })))
                });
            } else {
                inventory.contToObject(id);
            }
        }
    },
    contToObject: function(id) {
        var data = {
            id: id,
            purchase: $('#openMove').find('input[name="purchase"]').val(),
            sale: $('#openMove').find('input[name="sale"]').val(),
			object: Object.keys($('input[name="object"]').data()).join(',')
        }
        $.post('/inventory/to_object', data, function(r) {
            if (r == 'OK') {
                /* 				alr.show({
                					class: 'alrSuccess',
                					content: 'Inventory was successfully added to object',
                					delay: 2
                				}); */
                // Create invoice
                var iData = {};
                iData.customer = Object.keys($('input[name="customer"]').data()).join(',');
                iData.object = Object.keys($('input[name="object"]').data()).join(',');
                iData.inventory = id;
                iData.subtotal = -parseFloat($('input[name="purchase"]').val());
                iData.tax = 0;
                iData.total = iData.subtotal;
                iData.paid = 0;
                iData.purchase = 1;

                $.post('/invoices/send', iData, function(r) {
                    if (r > 0) {
                        alr.show({
                            class: 'alrSuccess',
                            content: lang[58],
                            delay: 2
                        });
                        mdl.close('openMove');
                        Page.get('/invoices/view/' + r);
                    } else {
                        alr.show({
                            class: 'alrDanger',
                            content: lang[13],
                            delay: 2
                        });
                    }
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
    toStore: function(id) {
        $.post('/inventory/to_store', {
            id: id
        }, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[104],
                    delay: 2
                });
                Page.get(location.href);
                mdl.close("openMove");
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