// Issues
var issues = {
	invisible: function(id, price) {
		mdl.open({
			id: 'inv_service',
			title: 'Invisible service',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup price',
					id: 'upcharge',
					html: $('<label/>', {
						html: 'Upcharge service'
					})
				}).append($('<ul/>', {
					class: 'hdn'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'issues.send_invisible(' + id + ');',
					html: 'Send'
				})
			})),
			cb: function() {
				$.post('/inventory/allUpcharge', {
					nIds: Object.keys($('input[name="upcharge"]').data() || {}).join(',')}, function (r) {
					if (r){
						var items = '',
						lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#upcharge > ul').html(items).sForm({
							action: '/inventory/allUpcharge',
							data: {
								lId: lId,
								nIds: Object.keys($('input[name="upcharge"]').data() || {}).join(','),
								query: $('#upcharge > .sfWrap input').val() || '',
							},
							all: false,
							select: $('input[name="upcharge"]').data(),
							s: true
						}, $('input[name="upcharge"]'));
					}
				}, 'json');
			}
		});
	},
	send_invisible: function(id) {
		$.post('/issues/send_invisible', {
			id: id, 
			service: Object.keys($('input[name="upcharge"]').data()).join(',')
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Service was successfully updated',
					delay: 2
				});
				mdl.close();
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	addFeedback: function(issue_id, id) {
		mdl.open({
			id: 'add_feedback',
			title: 'Add feedback',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Rating'
					})
				}).append($('<div/>', {
					class: 'stRating',
					id: 'fb_ratting',
					html: '<span class="fa fa-star rStart" ratting="1"></span><span class="fa fa-star rStart" ratting="2"></span><span class="fa fa-star rStart" ratting="3"></span><span class="fa fa-star rStart" ratting="4"></span><span class="fa fa-star rStart" ratting="5"></span>'
				}))
			}).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'Comment'
				})
			}).append($('<textarea/>', {
				name: 'feed_comment'
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'issues.sendFeedback(event, '+issue_id+', '+id+')',
					html: 'Send'
				})
			}))
		});
		$('.stRating > .fa-star').bind('mouseover mouseout click', function(e){
			e.preventDefault();
			switch(e.type){
				
				case 'mouseover':
					var count = $(e.target).attr('ratting');
					for(var i = 0; i < count; i++){
						$('.stRating > .fa-star').eq(i).addClass('hover');
					}
				break;
				
				case 'mouseout':
					$('.stRating > .fa-star').removeClass('hover');
				break;
				
				case 'click':
					var count = $(e.target).attr('ratting');
					$('#fb_ratting').val(count);
					$('.stRating > .fa-star').removeClass('active');
					for(var i = 0; i < count; i++){
						$('.stRating > .fa-star').eq(i).addClass('active');
					}
				break;
			}
		});
	},
	sendFeedback: function(event, issue_id, id){
		event.preventDefault();
		loadBtn.start($(event.target));
		var ratting = parseInt($('#fb_ratting').val()),
			comment = $('textarea[name="feed_comment"]').val();
		if(ratting > 0){
			$.post('/issues/send_feedback', {
				issue_id: issue_id,
				ratting: ratting,
				comment: comment
			}, function(r){
				if(r == 'OK'){
					alr.show({
						class: 'alrSuccess',
						content: 'Complete',
						delay: 2
					});
					mdl.close();
					Page.get(location.href);
				}
			});	
		} else {
			alr.show({
				class: 'alrDanger',
				content: lang.err_ratting,
				delay: 2
			});
		}
		loadBtn.stop($(event.target));
	},
	editDiscount: function(is, el, id, c) {
		$.post('/users/is_admin', {}, function(u) {
			adm = 0;
			if (u == 'OK') adm = 1;
			$.post('/invoices/allDiscounts', {}, function(r) {
				var items = '<option value="0">Not selected</option>';
				$.each(r.list, function(i, v) {
					items += '<option value="' + v.id + '"' + (v.id == id ? ' selected' : '') + '>' + v.name + '</option>';
				});
				mdl.open({
					id: 'discount_mdl',
					title: 'Select discount',
					content: $('<div/>', {
						class: 'uForm',
						html: $('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Discount'
							})
						}).append($('<select/>', {
							name: 'discount_id',
							html: items
						}))
					}).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Reason'
						})
					}).append($('<textarea/>', {
						name: 'reason',
						html: $('#discount_reason').val()
					}))).append($('<div/>', {
						class: 'sGroup',
						html: adm && ! c ? $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit left',
							onclick: 'issues.confirmDiscount(' + is + ');',
							html: 'Confirm'
						}) : ''
					}).append($('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'issues.sendDiscount(' + is + ');',
							html: 'Save'
						}))),
					cb: function() {
						$('#page').select();
					}
				})
			}, 'json');
		});
	},
	confirmDiscount: function(id) {
		$.post('/issues/send_field', {
			id: id,
			field: 'discount_confirmed',
			value: 1,
			full: 1
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount successfully confirmed',
					delay: 2
				});
				mdl.close();
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	sendDiscount: function(is) {
		$.post('/issues/send_discount', {
			id: is,
			discount: $('select[name="discount_id"]').val(),
			reason: $('textarea[name="reason"]').val()
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount successfully updated',
					delay: 2
				});
				mdl.close();
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	newInv: function() {
		$.post('/inventory/allOS', {}, function(r) {
			if (r) {
				var os = '';
				$.each(r.list, function(i, v) {
					os += '<option value="' + v.id + '">' + v.name + '</option>';
				});
				/* $.post('/inventory/allCategories', {}, function(r) {
					var cats = '';
					$.each(r.list, function(i, v) {
						cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
					});
						$.post('/inventory/allTypes', {
							name: 1
						}, function(r) {
							var types = '';
							$.each(r.list, function(i, v) {
								types += '<li data-value="' + v.id + '">' + v.name + '</li>';
							}); */
						
						/* mdl.open({
							id: 'createDevice',
							title: 'Add device',
							content: $('<div/>', {
								class: 'form',
								html: $('<div/>', {
								class: 'iGroup',
									id: 'type_id',
									html: $('<label/>', {
										html: 'Type'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'type_id'
								})).append($('<ul/>', {
									class: 'hdn',
									html: ''
								}))
								}).append($('<div/>', {
									class: 'iGroup',
									id: 'brand',
									html: $('<label/>', {
										html: 'Brand'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'brand'
								})).append($('<ul/>', {
									class: 'hdn',
									html: ''
								}))).append($('<div/>', {
								class: 'iGroup',
									id: 'model',
									html: $('<label/>', {
										html: 'Model'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'model'
								})).append($('<ul/>', {
									class: 'hdn'
								}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Model Specification'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'smodel'
							}))).append($('<div/>', {
								class: 'iGroup',
								id: 'os',
								html: $('<label/>', {
									html: 'OS'
								})
							}).append($('<select/>', {
								name: 'os',
								html: os
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'OS version'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'os_ver'
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Serial Number'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'sn'
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Price'
								})
							}).append($('<input/>', {
								type: 'number',
								min: '0',
								step: '0.1',
								name: 'price'
							}))).append($('<div/>', {
								class: 'sGroup',
								html: $('<button/>', {
									type: 'button',
									class: 'btn btnSubmit',
									onclick: 'issues.sendDev(this, event);',
									html: 'Send'
								})
							})),
							cb: function() {
								var lId = 0;
								$('#page').select();
								
								getTypes(1);
								getBrands(1);
								inventory.getGroup(0,0,1);
							}
						}); */
				/* }, 'json');
			}, 'json'); */
			}
		}, 'json');
		
	}, newServ: function() {
		/* $.post('/inventory/allCategories', {}, function(r) {
			var cats = '';
			$.each(r.list, function(i, v) {
				cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
			});
				$.post('/inventory/allTypes', {
					name: 1
				}, function(r) {
					var types = '';
					$.each(r.list, function(i, v) {
						types += '<li data-value="' + v.id + '">' + v.name + '</li>';
					}); */
			
			/* 			mdl.open({
							id: 'createDevice',
							title: 'Add service',
							content: $('<div/>', {
								class: 'form',
								html: $('<div/>', {
									class: 'iGroup',
									html: $('<label/>', {
										html: 'Name'
									})
								}).append($('<input/>', {
									type: 'text',
									name: 'name'
							}))}).append($('<div/>', {
									class: 'iGroup',
									html: $('<label/>', {
										html: 'Price'
									})
								}).append($('<input/>', {
									type: 'number',
									min: '0',
									step: '0.1',
									name: 'price'
								}))).append($('<div/>', {
									class: 'sGroup',
									html: $('<button/>', {
										type: 'button',
										class: 'btn btnSubmit',
										onclick: 'issues.sendServ(this, event);',
										html: 'Send'
									})
							}))
						}); */
				/* }, 'json');
		}, 'json'); */
		
	}, sendDev: function(f, event) {
		event.preventDefault();
        loadBtn.start($(f).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).parents('.form'),
            err = null;
       
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if (!Object.keys(form.find('input[name="brand"]').data()).join(',') && !form.find('input[name="brand"]').next().find('input').val().length) {
            err = lang[124];
        } else if (!Object.keys(form.find('input[name="type_id"]').data()).join(',') && !form.find('input[name="type_id"]').next().find('input').val().length) {
            err = 'Select type';
        } else if (!Object.keys(form.find('input[name="model"]').data()).join(',') && !form.find('input[name="model"]').next().find('input').val().length) {
            err = 'Select model';
        } else if (!form.find('input[name="brand"]').length && !form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            if (form.find('select[name="type"]').val() == 0) {
                err = lang[123];
            } else {
                if (form.find('input[name="name"]').val()) data.append('name', form.find('input[name="name"]').val());
                data.append('price', form.find('input[name="price"]').val());
                
                data.append('smodel', form.find('input[name="smodel"]').val() || '');
                data.append('os', form.find('select[name="os"]').val() || '');
                data.append('os_version', form.find('input[name="os_ver"]').val() || '');
                data.append('serial', form.find('input[name="sn"]').val() || '');
                data.append('intype', 'stock');
				
				data.append('type', Object.keys(form.find('input[name="type_id"]').data() || {}).join(','));
				data.append('type_new', form.find('input[name="type_id"]').next().find('input').val());
				
				data.append('category', Object.keys(form.find('input[name="brand"]').data() || {}).join(','));
				data.append('category_new', form.find('input[name="brand"]').next().find('input').val());
				
                data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                data.append('model_new', form.find('input[name="model"]').next().find('input').val());
				
				data.append('exist_inventory', $('input[name="inventory_id"]').val());
				
				if ($('input[name="issue_id"]').length) data.append('issue_id', $('input[name="issue_id"]').val());
			}
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_short_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(f).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    
					if (r == 'no_obj') {
						alr.show({
							class: 'alrDanger',
							content: 'You issue device is not in store. Or you are not logged on store. Fix it and try again.',
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
							content: 'Locations exists',
							delay: 2
						});
					} else {
						r = JSON.parse(r);
						if (r.id > 0) {
							var inv = {};
							inv[r.id] = {
							   name: r.name,
							   price: form.find('input[name="price"]').val()
							}
							$('input[name="inventory"]').data(inv);
							$('#inventory .sfWrap > div').prepend($('<span/>', {
							   class: 'nc',
							   id: r.id,
							   'data-price': form.find('input[name="price"]').val(),
							   html: r.name
							}).append($('<i/>')));
							mdl.close('createDevice');

							alr.show({
								class: 'alrSuccess',
								content: lang[122],
								delay: 2
							});
						}
					}
						
                }
            });
        }
	}, sendServ: function(f, event) {
		event.preventDefault();
        loadBtn.start($(f).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).parents('.form'),
            err = null;
       
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            if (form.find('select[name="type"]').val() == 0) {
                err = lang[123];
            } else {
                if (form.find('input[name="name"]').val()) data.append('name', form.find('input[name="name"]').val());
                data.append('price', form.find('input[name="price"]').val());
				data.append('intype', 'service');
				
				data.append('type', Object.keys(form.find('input[name="type_id"]').data() || {}).join(','));
				data.append('type_new', form.find('input[name="type_id"]').next().find('input').val());
				
				data.append('category', Object.keys(form.find('input[name="brand"]').data() || {}).join(','));
				data.append('category_new', form.find('input[name="brand"]').next().find('input').val());
				
                data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                data.append('model_new', form.find('input[name="model"]').next().find('input').val());
			}
        }
		form.find('#forms > .iGroup').each(function(i, val) {
			if ($(val).find('label').next().val())
				options[$(val).find('label').next().attr('data-id')] = $(val).find('label').next().val();
		});
		data.append('opts', JSON.stringify(options));
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(f).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[122],
                        delay: 2
                    });
					if (r == 'cat_exists') {
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
							content: 'Locations exists',
							delay: 2
						});
					} else if (r > 0) {
					   var inv = {};
					   inv[r] = {
						   name: form.find('input[name="name"]').val(),
						   price: form.find('input[name="price"]').val()
					   }
					   $('input[name="service"]').data(inv);
					   $('#service .sfWrap > div').prepend($('<span/>', {
						   class: 'nc',
						   id: r,
						   'data-price': form.find('input[name="price"]').val(),
						   html: form.find('input[name="name"]').val()
					   }).append($('<i/>')));
					   mdl.close('createDevice');
					}
						
                }
            });
        }
	}, updateStatus: function(id) {
		/* $.post('/inventory/get_sublocations', {
			location: $('.devLocation').attr('data-id'),
			inventory: $('input[name="inventory_id"]').val()
		}, function(r) {
			if (r > 0) location_count = r;
			else location_count = 0; */
			mdl.open({
				id: 'stUpdate',
				title: 'Update issue',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						id: 'status',
						html: $('<label/>', {
							html: 'Status'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'status'
					})).append($('<ul/>', {
						class: 'hdn'
					}))
				}).append($('<div/>', {
					class: 'iGroup fw',
					id: 'location',
					html: $('<label/>', {
						html: 'Location'
					})
				}).append($('<input/>', {
					type: 'hidden',
					name: 'location'
				})).append($('<ul/>', {
					class: 'hdn'
				}))).append($('<div/>', {
					class: 'iGroup fw',
					id: 'sublocation',
					html: $('<label/>', {
						html: 'Sublocation'
					})
				}).append($('<select/>', {
					name: 'sublocation'
				}))).append($('<div/>', {
					class: 'iGroup price fw',
					id: 'set_inventory',
					html: $('<label/>', {
						html: 'Inventory'
					})
				}).append($('<ul/>'))).append($('<div/>', {
					class: 'iGroup price fw',
					id: 'set_services',
					html: $('<label/>', {
						html: 'Services'
					})
				}).append($('<ul/>'))).append($('<div/>', {
					class: 'iGroup tPrice',
					html: $('<label/>', {
						html: 'Price'
					})
				}).append($('<div/>', {
					html: ''
				}))).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Important note'
					})
				}).append($('<input/>', {
					type: 'checkbox',
					name: 'important',
					checked: $('.descrIssue').hasClass('important')
				}))).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'issues.sendUpdate(' + id + ', this)',
						html: 'Send'
					})
				})),
				cb: function() {
					$('#page').checkbox();
					$('#page').select();
					var st = {},
						lc = {};
					location_count = $('.devLocation').attr('location_count');
					if ($('.devStatus').attr('data-id') != 0) st[$('.devStatus').attr('data-id')] = {name: $('.devStatus').attr('data-id') == 5 ? $('.devStatus').attr('data-title') : $('.devStatus').text().replace('Status: ', '')};
					if ($('.devLocation').text().replace('Location: ', '').length) {
						lc[$('.devLocation').attr('data-id')] = {
							name: $('.devLocation').text().replace('Location: ', ''),
							count: $('.devLocation').attr('data-count')
						};
					}
					$('input[name="status"]').data(st);
					if ($('.devLocation').text().replace('Location: ', '').length) {
						location_id = lc;
						object_id  = $('.devLocation').attr('data-object');
					}
					$.post('/inventory/allStatuses', {
							nIds: Object.keys($('input[name="status"]').data()).join(','),
							set_inventory: Object.keys($('input[name="set_inventory"]').data()).join(','),
							set_services: Object.keys($('input[name="set_services"]').data()).join(',')
						}, function (r) {
						if (r){
							var items = '',
							lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#status > ul').html(items).sForm({
								action: '/inventory/allStatuses',
								data: {
									lId: lId,
									nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
									query: $('#status > .sfWrap input').val() || '',
									set_inventory: Object.keys($('input[name="set_inventory"]').data()).join(','),
									set_services: Object.keys($('input[name="set_services"]').data()).join(',')
								},
								all: false,
								select: $('input[name="status"]').data(),
								s: true
							}, $('input[name="status"]'), function() {
									getLocation(1);
							}, {
								set_inventory : 'set_inventory', 
								set_services : 'set_services'
							});
						}
					}, 'json');
					
					$.post('/inventory/all', {
						type: 'stock',
						noCust: 1,
						nIds: Object.keys($('input[name="set_inventory"]').data()).join(',')}, function (r) {
						if (r){
							var items = '',
							lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#set_inventory > ul').html(items).sForm({
								action: '/inventory/all',
								data: {
									lId: lId,
									nIds: Object.keys($('input[name="set_inventory"]').data() || {}).join(','),
									query: $('#set_inventory > .sfWrap input').val() || '',
									type: 'stock',
									noCust: 1,
								},
								all: false,
								select: $('input[name="set_inventory"]').data()
							}, $('input[name="set_inventory"]'), function() {
								total.miniTotal(lc);
							});
						}
					}, 'json');
					
					$.post('/inventory/all', {
						type: 'service',
						nIds: Object.keys($('input[name="set_services"]').data()).join(',')}, function (r) {
						if (r){
							var items = '',
							lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#set_services > ul').html(items).sForm({
								action: '/inventory/all',
								data: {
									lId: lId,
									type: 'service',
									nIds: Object.keys($('input[name="set_services"]').data() || {}).join(','),
									query: $('#set_services > .sfWrap input').val() || ''
								},
								all: false,
								select: $('input[name="set_services"]').data()
							}, $('input[name="set_services"]'), function() {
								total.miniTotal(lc);
							});
						}
					}, 'json');
					
					total.miniTotal(lc);
					//getLocation(1);
				}
			});
		//});
	}, sendUpdate: function(id, el) {
        loadBtn.start($(el).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(el).parents('.uForm'),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if ($(v).find('label').next().attr('name') == 'descr' && !$(v).find('label').next().val()) {
                err = lang[100];
			} else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().find('option[value="1"]').length && $(v).find('label').next().val() == '0') {
				err = 'Sublocation can not be empty';
            } else if ($(v).find('label').next().attr('name') == 'descr' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else {
                data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data()).join(',');
            }
        });
		data['inventory_id'] = $('input[name="inventory_id"]').val();
		data['important'] = $('input[name="important"]').val();
		
		data['set_inventory'] = Object.keys($('input[name="set_inventory"]').data()).join(',');
		data['set_services'] = Object.keys($('input[name="set_services"]').data()).join(',');
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el).find('button[type="submit"]'));
        } else {
            $.post('/issues/send_update', data, function(r) {
                loadBtn.stop($(el).find('button[type="submit"]'));
                if (r == 'set_service') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'This status needs enough one service',
                        delay: 2
                    });
                } else if (r == 'set_inventory') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'This status needs enough one inventory',
                        delay: 2
                    });
                } else if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
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
    }, pickUp: function(id, el) {
        loadBtn.start($(el).find('button[type="submit"]'));
        var data = {
                id: id,
				inventory_id: $('input[name="inventory_id"]').val()
            },
            form = $(el).parents('.uForm'),
            err = null;
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el).find('button[type="submit"]'));
        } else {
            $.post('/issues/pickup', data, function(r) {
                loadBtn.stop($(el).find('button[type="submit"]'));
                if (r == 'OK') {
					alr.show({
                        class: 'alrSuccess',
                        content: 'Device was successfully picked up',
                        delay: 2
                    });
                    Page.get('/issues/view/' + id);
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
	DoloadStats: function(e, id) {
		page_stats += 1;
		$.post('/issues/doload_stats', {
			page: page_stats,
			id: id
		}, function(r) {
			$('#ChangeLog .stats_tbl').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	},
    add: function(f, event, id, i) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                inventory_id: i,
				id: id || 0
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if ($(v).find('label').next().attr('name') == 'descr' && !$(v).find('label').next().val()) {
                err = lang[100];
            } else if ($(v).find('label').next().attr('name') == 'location' && !Object.keys($(v).find('label').next().data()).join(',').length) {
                err = 'Location can not be empty';
            } else if ($(v).find('label').next().attr('name') == 'descr' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else {
                data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data()).join(',');
            }
            data['inventory_id'] = $('input[name="inventory_id"]').val();
            data['location_count'] = $('select[name="sublocation"]').val();
        });
		if (form.find('select[name="sublocation"] > option[value="1"]').length && form.find('select[name="sublocation"]').val() == 0)
                err = 'Subocation can not be empty';
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/issues/send', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get('/issues/view/' + r);
                }
            });
        }
    },
    del: function(id) {
        $.post('/issues/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#issue_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    addIssue: function(id, name, device, descr, st, lc, iv, sr) {
        mdl.open({
            id: 'addIssue',
            title: lang[99],
            content: $('<form/>', {
                method: 'post',
                onsubmit: 'issues.add(this, event,' + id + ');',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[98]
                    })
                }).append($('<label/>', {
                    class: 'mdlInfo',
                    html: name
                }))
            }).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[97]
                })
            }).append($('<label/>', {
                class: 'mdlInfo',
                html: device
            }))).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[96]
                })
            }).append($('<textarea/>', {
                name: 'descr',
                html: id ? $('#issue_' + id + ' > .isDescr').text().trim() : ''
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'service',
                html: $('<label/>', {
                    html: lang[95]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'service'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'inventory',
                html: $('<label/>', {
                    html: lang[94]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'inventory'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'status',
                html: $('<label/>', {
                    html: lang[93]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'status'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'location',
                html: $('<label/>', {
                    html: lang[92]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'location'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'submit',
                    html: lang[91]
                })
            }).append($('<button/>', {
                class: 'btn btnPurchase',
                html: lang[90],
                onclick: 'issues.makePur(); return false;'
            }))),
            cb: function() {
                if (id) {
                    /* $('input[name="status"]').data(st);
					$('input[name="location"]').data(lc);
					$('input[name="inventory"]').data(iv);
					$('input[name="service"]').data(sr); */
                    getLocation();
                }
                $.post('/inventory/allStatuses', {
                    nIds: Object.keys($('input[name="status"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#status > ul').html(items).sForm({
                            action: '/inventory/allStatuses',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
                                query: $('#status > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="status"]').data(),
                            s: true
                        }, $('input[name="status"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    type: 'stock',
                    nIds: Object.keys($('input[name="inventory"]').data()).join(',') + (id ? ', ' + id : '')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#inventory > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                type: 'stock',
                                lId: lId,
                                nIds: Object.keys($('input[name="inventory"]').data() || {}).join(',') + (id ? ',' + id : ''),
                                query: $('#inventory > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="inventory"]').data()
                        }, $('input[name="inventory"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    type: 'service',
                    nIds: Object.keys($('input[name="service"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#service > ul').html(items).sForm({
                            action: '/objects/all',
                            data: {
                                type: 'service',
                                lId: lId,
                                nIds: Object.keys($('input[name="service"]').data() || {}).join(','),
                                query: $('#service > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="service"]').data()
                        }, $('input[name="service"]'));
                    }
                }, 'json');
            }
        });
    },
    toggleList: function(el, ev) {
        if (!$(ev.target).parents('.td').hasClass('w100') && !$(ev.target).parents('.td').hasClass('w5'))
            $(el).toggleClass('open').next().slideToggle();
    },
    addPur: function(id, pIds, cid) {
        mdl.open({
            id: 'addPurchase',
            title: lang[89],
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup sfGroup fw price',
                    id: 'purchase',
                    html: $('<label/>', {
                        html: lang[66]
                    })
                }).append($('<input/>', {
                    type: 'hidden',
                    name: 'purchase'
                })).append($('<ul/>'))
            }).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'New purchase'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'new_purchase',
				onblur: 'issues.newPur(this)'
			}))).append($('<div/>')).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendPur(this, ' + id + ', \'' + pIds + '\', \'' + cid + '\');',
                    html: lang[42]
                })
            })),
            cb: function() {
                $.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchase"]').data()).join(',') + pIds
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#purchase > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                nIds: Object.keys($('input[name="purchase"]').data() || {}).join(',') + pIds,
                                query: $('#purchase > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="purchase"]').data()
                        }, $('input[name="purchase"]'));
                    }
                }, 'json');
            }
        });
    },
	newPur: function(el) {
		$(el).parent().next().empty();
		$.post('/purchases/ebay', {
            url: el.value
        }, function(r) {
            switch (r) {
                case 'ERR':
                    alr.show({
                        class: 'alrDanger',
                        content:  lang[13],
                        delay: 2
                    });
                    break;

                case 'NOP':
                    alr.show({
                        class: 'alrDanger',
                        content: lang[103],
                        delay: 2
                    });
                    break;

                default:
                    $(r).find('#itemTitle').find('span').remove();
					
					$(el).parent().next().append($('<div/>', {
						class: 'pnThumb',
						html: $(r).find('#icImg').attr('src').length ? $('<img/>', {
							src: $(r).find('#icImg').attr('src')
						}) : ''
					})).append($('<div/>', {
						class: 'pNewText',
						html: $('<div/>', {
							class: 'pnName',
							html: $(r).find('#itemTitle').text().replace('Details about', '').trim()
						})
					})).append($('<div/>', {
						class: 'itemId',
						html: $(r).find('#descItemNumber').text()
					})).append($('<div/>', {
						class: 'pnPrice',
						html: $(r).find('#mm-saleDscPrc').length ? $(r).find('#mm-saleDscPrc').text().trim() : $(r).find('#prcIsum').text().trim()
					})).append($('<input/>', {
						type: 'number',
						class: 'npPrice',
						name: 'price',
						step: '0.001',
						min: '0',
						placeholder: 'Sale price'
					}));
                    break;
            }
        }, 'html');
	},
    sendPur: function(f, id, cid) {
        form = $(f).parent().parent().parent();
       /*  var data = {
                id: id,
                field: 'purchase_ids',
                value: {},
                prices: {}
            }, */
		var data = new FormData(),
            err = null;
		data.append('id', id);
		data.append('field', 'purchase_ids');
        loadBtn.start($(f));
        if (!form.find('input[name="purchase"]').data() && !form.find('input[name="new_purchase"]').data())
            err = lang[88];
        else if (form.find('input[name="purchase"]').data()) {
            /* data.value = Object.keys(form.find('input[name="purchase"]').data()).join(',') ? Object.keys(form.find('input[name="purchase"]').data()).join(',') + ',' : '';
            $.each(form.find('input[name="purchase"]').data(), function(i, v) {
                data.prices[i] = v.price;
            });
			if (form.find('input[name="new_purchase"]').data()) {
				data.cid = cid;
				data.name = $('.pnName').text();
				data.photo = $('#addPurchase .pnThumb > img').attr('src');
				data.price = $('.pnPrice').text().replace(/(\D*)/i, '');
				data.cprice = $('input[name="price"]').val() || 0;
				data.link = $('input[name="new_purchase"]').val();
			} */
			data.append('value', Object.keys(form.find('input[name="purchase"]').data()).join(',') ? Object.keys(form.find('input[name="purchase"]').data()).join(',') + ',' : '');
			var prices = {};
			$.each(form.find('input[name="purchase"]').data(), function(i, v) {
                prices[i] = v.price;
            });
			data.append('prices', prices);
			if (form.find('input[name="new_purchase"]').val()) {
				data.append('cid', cid);
				data.append('name', $('.pnName').text());
				data.append('photo', $('#addPurchase .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('itemID', $('.itemId').text());
				data.append('cprice', $('input[name="price"]').val() || 0);
				data.append('link', $('input[name="new_purchase"]').val());
				if ($('input[name="price"]').val() == 0)
					err = 'Price can not be 0';
			}
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f));
        } else {
            //$.post('/issues/send_field', data, function(r) {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/issues/send_field',
				data: data
			}).done(function(r) {
                loadBtn.stop($(f));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[87] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('addPurchase');
                } else if (r == 'min_price') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Min sale price can not be less than $' + parseFloat(min_price($('.pnPrice').text().replace(/(\D*)/i, '').replace(',', ''))).toFixed(2),
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
    addInv: function(id, sIds, type, devId, cid) {
        name = (type == 'stock' ? lang[85] : lang[86]);
        mdl.open({
            id: 'addService',
            title: lang[84] + (devId ? lang[85] : lang[86]),
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup sfGroup fw price',
                    id: name,
                    html: $('<label/>', {
                        html: devId ? lang[82] : lang[83]
                    })
                }).append($('<input/>', {
                    type: 'hidden',
                    name: name
                })).append($('<ul/>'))
            }).append(type == 'service' ? $('<div/>', {
				class: 'iGroup fw price hdn reqP',
				id: 'inventory',
				html: $('<label/>', {
					html: 'Inventory'
				})
			}).append($('<ul/>', {
				class: 'hdn'
			})) : '').append(type == 'service' ? $('<div/>', {
				class: 'iGroup fw price hdn reqP',
				id: 'purchase',
				html: $('<label/>', {
					html: 'Purchases'
				})
			}).append($('<ul/>', {
				class: 'hdn'
			})) : '').append(type == 'service' ? $('<div/>', {
				class: 'iGroup fw price hdn reqP',
				html: $('<label/>', {
					html: 'New purchase'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'new_purchase',
				onblur: 'issues.newPur(this)'
			})) : '').append(type == 'service' ? $('<div/>', {
				class: 'hdn reqP'
			}) : '').append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendInv(this, ' + id + ', \'' + (devId ? 'stock' : 'service') + '\', \'' + sIds + '\', \'' + cid + '\');',
                    html: lang[42]
                })
            })),
            cb: function() {
				if (type == 'service') {
					$('input[name="' + name + '"]').data($('input[name="set_services"]').data());
				}
                $.post('/inventory/all', {
                    type: type,
                    noCust: 1,
                    nIds: Object.keys($('input[name="' + name + '"]').data()).join(',') + (type != 'service' ? sIds : '') + (devId ? ',' + devId : '')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
                            lId = v.id;
                        });
						
                        $('#' + name + ' > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                type: type,
                                lId: lId,
                                nIds: Object.keys($('input[name="' + name + '"]').data()).join(',') + (type != 'service' ? sIds : '') + (devId ? ',' + devId : ''),
                                query: $('#' + name + ' > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="' + name + '"]').data()
                        }, $('input[name="' + name + '"]'), (type == 'service' ? issues.reqParts : ''));
                    }
                }, 'json');
				
				$.post('/inventory/all', {
                    type: 'stock',
                    noCust: 1,
                    nIds: Object.keys($('input[name="set_inventory"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#inventory > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                type: 'stock',
                                lId: lId,
                                nIds: Object.keys($('input[name="set_inventory"]').data()).join(','),
                                query: $('#inventory > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="set_inventory"]').data()
                        }, $('input[name="set_inventory"]'));
                    }
                }, 'json');
				
				$.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchase"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#purchase > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                nIds: Object.keys($('input[name="purchase"]').data() || {}).join(','),
                                query: $('#purchase > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="purchase"]').data()
                        }, $('input[name="purchase"]'));
                    }
                }, 'json');
				
				if (type == 'service') {
					issues.reqParts();
				}
            }
        });
    },
	reqParts: function() {
		var req = 0;
		$.each($('input[name="service"]').data(), function(i, v) {
			if (v.req)
				req = 1;
		});
		if (req)
			$('.reqP').removeClass('hdn');
		else 
			$('.reqP').addClass('hdn');
		p = ($(window).height() - $('#addService > .mdl').height()) / 2;
		$('#addService').css('padding-top', (p < 20 ? 20 : p) + 'px');		
	},
    sendInv: function(f, id, type, cid) {
        form = $(f).parent().parent().parent();
        var data = new FormData(),
            err = null,
            name = (type == 'stock' ? 'inventory' : 'service');
		data.append('id', id);
		data.append('field', (type == 'stock' ? 'inventory_ids' : 'service_ids'));
		
        loadBtn.start($(f));
        if (!form.find('input[name="' + name + '"]').data())
            err = lang[81];
        else { 
			data.append('value', Object.keys(form.find('input[name="' + name + '"]').data()).join(',') + ',');
			data.append('inventory', Object.keys($('input[name="set_inventory"]').data() || {}).join(',') + ',');
			data.append('purchases', Object.keys($('input[name="purchase"]').data() || {}).join(',') ? Object.keys($('input[name="purchase"]').data()).join(',') + ',' : '');
			var prices = {};
			$.each($('input[name="purchase"]').data(), function(i, v) {
                prices[i] = v.price;
            });
			data.append('prices', prices);
			if ($('input[name="new_purchase"]').val()) {
				data.append('cid', cid);
				data.append('name', $('.pnName').text());
				data.append('photo', $('#addService .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('itemID', $('.itemId').text());
				data.append('cprice', $('input[name="price"]').val() || 0);
				data.append('link', $('input[name="new_purchase"]').val());
				if ($('input[name="price"]').val() == 0)
					err = 'Price can not be 0';
			}
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f));
        } else {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/issues/send_field',
				data: data
			}).done(function(r) {
                loadBtn.stop($(f));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('addService');
                } else if (r == 'min_price') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Min sale price can not be less than $' + parseFloat(min_price($('.pnPrice').text().replace(/(\D*)/i, '').replace(',', ''))).toFixed(2),
                        delay: 2
                    });
				} else if (r == 'req') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You must enough one inventory or part to use this services',
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
    delInv: function(id, type, issue) {
        var data = {
            id: issue,
            field: (type == 'stock' ? 'inventory_ids' : 'service_ids'),
            value: id,
            del: true
        };
        $.post('/issues/send_field', data, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[74] + (id ? lang[23] : lang[24]),
                    delay: 2
                });
                if (type == 'service') {
                    $('#service_com_' + id).remove();
                    $('#sSteps_' + id).remove();
                    $('#miniService_' + id).remove();
                }
                $('#' + (type == 'stock' ? 'inventory_' : 'service_') + id).remove();
				total.fTotal();
				var dSelect = $('input[name="' + (type == 'stock' ? 'set_inventory' : 'set_services') + '"]').data();
                delete dSelect[id];
				$('input[name="' + (type == 'stock' ? 'set_inventory' : 'set_services') + '"]').removeData().data(dSelect);
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    },
    addStep: function(el, id){
        var data = {
                id: id,
                sId: $(el).parent().parent().attr('id').replace('sSteps_', ''),
                value: $(el).attr('id'),
                del: $(el).hasClass('done') ? 1 : 0
            },
            serv = $(el).parents('.servSteps').find('.servTitle');
        $.post('/issues/send_step', data, function(r) {
            if (r != 'OK') {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            } else {
                $(el).toggleClass('done');
                serv.find('.num').text($(el).hasClass('done') ? (parseInt(serv.find('.num').text()) + 1) : (parseInt(serv.find('.num').text()) - 1));
                serv.find('.number').text((parseInt(serv.find('.num').text()) / parseInt(serv.find('.full').text()) * 100).toFixed(0));
            }
        })
    },
    editDoit: function(id, el) {
        $('#doit').hide().after($('<input/>', {
            type: 'number',
			min: 0,
            name: 'doit',
            value: parseInt($('#doit').text())
        }));
        $('#doitBtn').removeClass('fa-pencil').addClass('fa-check').parent().attr('href', 'javascript:issues.sendDoit(' + id + ')');
    },
    editQuote: function(id, el) {
        $('#quote').hide().after($('<input/>', {
            type: 'number',
			min: 0,
            name: 'quote',
            value: parseInt($('#quote').text())
        }));
        $('#quoteBtn').removeClass('fa-pencil').addClass('fa-check').parent().attr('href', 'javascript:issues.sendQuote(' + id + ')');
    },
    sendDoit: function(id, el) {
        var data = {
                id: id,
                field: 'doit',
                value: 0,
                full: 1
            },
            err = null;
        if (!$('input[name="doit"]').val()) {
            err = lang[80];
            $('input[name="doit"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="doit"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_field', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $('#doit').text('$' + parseFloat(data.value).toFixed(2)).show().next().remove();
                    $('#doitBtn').removeClass('fa-check').addClass('fa-pencil').parent().attr('href', 'javascript:issues.editDoit(' + id + ')');
                }
            });
        }
    },
    sendQuote: function(id, el) {
        var data = {
                id: id,
                field: 'quote',
                value: 0,
                full: 1
            },
            err = null;
        if (!$('input[name="quote"]').val()) {
            err = lang[80];
            $('input[name="quote"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="quote"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_field', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $('#quote').text('$' + parseFloat(data.value).toFixed(2)).show().next().remove();
                    $('#quoteBtn').removeClass('fa-check').addClass('fa-pencil').parent().attr('href', 'javascript:issues.editQuote(' + id + ')');
									total.fTotal();
                }
            });
        }
    },
    addComment: function(s, i) {
        mdl.open({
            id: 'addComment',
            title: lang[79],
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'iGroup fw',
                    html: $('<textarea/>', {
                        id: 'comment',
                        html: $('#service_com_' + s + ' > span').text()
                    })
                })
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendComment(\'' + s + '\', ' + i + ');',
                    html: lang[79]
                })
            }))
        })
    },
    sendComment: function(s, i) {
        var data = {
            id: i,
            sId: s,
            value: $('#comment').val(),
            del: 0
        };
        $.post('/issues/send_comment', data, function(r) {
            if (r != 'OK') {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            } else {
                Page.get(location.href);
				mdl.close();
            }
        })
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
            $.post('/issues/send_note', data, function(r) {
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
    pPrice: function(el, id, iss) {
        console.log(parseFloat($(el).prev().text()));
        $(el).prev().hide().after($('<input/>', {
            type: 'number',
            name: 'pPrice',
            step: '0.1',
            min: '0',
            value: parseFloat($(el).prev().text())
        }));
        $(el).removeClass('fa-pencil').addClass('fa-check').attr('onclick', 'issues.sendPPrice(this, ' + id + ', ' + iss + ')');
    },
    sendPPrice: function(el, id, iss) {
        var data = {
                id: iss,
                value: 0,
                pur: id
            },
            err = null;
        if (!$('input[name="pPrice"]').val()) {
            err = lang[76];
            $('input[name="pPrice"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="pPrice"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_price', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $(el).prev().prev().text(data.value).show().next().remove();
                    $(el).removeClass('fa-check').addClass('fa-pencil').attr('onclick', 'issues.pPrice(this, ' + id + ', ' + iss + ')');
					total.fTotal();
                }
            });
        }
    },
    delPur: function(id, issue) {
        var data = {
            id: issue,
            field: 'purchase_ids',
            value: id,
            del: true
        };
        $.post('/issues/send_field', data, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[74] + (id ? lang[23] : lang[24]),
                    delay: 2
                });
                $('#purchase_' + id).remove();
				total.fTotal();
				var dSelect = $('input[name="purchase"]').data();
                delete dSelect[id];
				$('input[name="purchase"]').removeData().data(dSelect);
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