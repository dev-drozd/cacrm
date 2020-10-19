// Invoices
var invoices = {
	send_mail: function(id) {
		$.post('/invoices/send_mail/', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Mail was successfully sended',
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
    openDiscount: function(id, n, p, c) {
		$.post('/users/is_admin', {}, function(r){
			mdl.open({
				id: 'openDiscount',
				title: (id ? lang[17] : lang[18]) + ' Discount',
				content: '<div class="iGroup">\
							<label>' + lang[73] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup">\
							<label>' + lang[72] + '</label>\
							<input type="number" name="percent" value="' + (p || 0) + '" step="0.1" min=""0>\
						</div>\
						<div class="sGroup">\
							' + (($('#discount_' + id).hasClass('not-confirmed') && r == 'OK') ? '<button class="btn btnSubmit left" onclick="invoices.confirmDiscount(this, ' + (id || 0) + ')">Confirm</button>' : '') + '\
							<button class="btn btnSubmit" onclick="invoices.sendDiscount(this, ' + (id || 0) + ')">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>',
			});
		});
    },
	confirmDiscount: function(el, id) {
		loadBtn.start($(el));
		$.post('/invoices/confirm_discount', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount was successfully confirmed', 
					delay: 2
				});
				$('#discount_' + id).removeClass('not-confirmed').addClass('confirmed');
				mdl.close();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13], 
					delay: 2
				});
			}
		});
	},
    sendDiscount: function(f, id) {
        f = $(f).parents('.mdlBody');
        var data = {
                id: id
            },
            err = null;
        f.find('input').css('border-color', '#ddd');
        loadBtn.start(f.find('button[type="submit"]'));
        if (!f.find('input[name="name"]').val()) {
            err = lang[25];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else if (!f.find('input[name="percent"]').val()) {
            err = lang[25];
            f.find('input[name="percent"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
            data.percent = f.find('input[name="percent"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop(f.find('button[type="submit"]'));
        } else {
            $.post('/invoices/send_discount', data, function(r) {
                loadBtn.stop(f.find('button[type="submit"]'));
                if (r) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[71],
                        delay: 2
                    });
                    mdl.close('openDiscount');
                    $('.userList').prepend($('<div/>', {
                        class: 'sUser ' + r.confirmed,
                        id: 'discount_' + r.id,
                        html: $('<a/>', {
                            class: 'uInfo',
                            html: data.name
                        })
                    }).append($('<div/>', {
                        class: 'pInfo',
                        html: data.percent
                    })).append($('<div/>', {
                        class: 'uMore',
                        html: $('<span/>', {
                            class: 'fa fa-ellipsis-h',
                            onclick: '$(this).next().toggle(0)'
                        })
                    }).append($('<ul/>', {
                        html: $('<li>', {
                            html: $('<a/>', {
                                href: 'javascript:invoices.openDiscount(' + r.id + ', "' + data.name + '", ' + data.percent + ')',
                                html: $('<span/>', {
                                    class: 'fa fa-pencil'
                                })
                            }).append(lang[70])
                        })
                    }).append($('<li>', {
                        html: $('<a/>', {
                            href: 'javascript:invoices.delDiscount(' + r.id + ')',
                            html: $('<span/>', {
                                class: 'fa fa-times'
                            })
                        }).append(lang[69])
                    })))));
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            }, 'json');
        }
    },
    delDiscount: function(id) {
        $.post('/invoices/del_discount', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#discount_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    addInventory: function() {
        mdl.open({
            id: 'addInv',
            title: lang[18],
			width: 600,
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'tabs',
                    html: $('<div/>', {
                        class: 'tab',
                        'data-title': lang[68],
                        id: 'inv',
                        html: $('<div/>', {
                            id: 'inventory',
                            class: 'iGroup price',
                            html: $('<ul/>')
                        })
                    })
                }).append($('<div/>', {
                    class: 'tab',
                    'data-title': lang[67],
                    id: 'serv',
                    html: $('<div/>', {
                        id: 'services',
                        class: 'iGroup price',
                        html: $('<ul/>')
                    })
                })).append($('<div/>', {
                    class: 'tab',
                    'data-title': 'Order parts',
                    id: 'purch',
                    html: $('<div/>', {
                        id: 'purchases',
                        class: 'iGroup',
                        html: $('<ul/>')
                    })
                }).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'New purchase'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'new_purchase',
					onblur: 'issues.newPur(this)'
				}))).append($('<div/>'))).append($('<div/>', {
                    class: 'tab',
                    'data-title': 'Buy from customer',
                    id: 'buy_inventory',
                    html: $('<div/>', {
                        id: 'tradein',
                        class: 'iGroup',
                        html: $('<ul/>')
                    })
                }).append($('<div/>', {
					class: 'newTradeins',
					id: 'newTradein',
					html: $('<table/>', {
						class: 'tradein_table',
						html: $('<thead/>', {
							html: $('<tr/>', {
								html: $('<th/>', {
									html: 'Name'
								})
							}).append($('<th/>', {
								html: 'Purchase price'
							})).append($('<th/>', {
								html: 'Sale price'
							}))
						})
					}).append($('<tbody/>'))
				})))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    type: 'button',
                    class: 'btn btnSubmit',
                    onclick: 'invoices.chooseInv();',
                    html: lang[65]
                })
            })),
            cb: function() {
                $('#page').tabs();
                $.post('/inventory/all', {
                    noCust: 1,
                    type: 'stock',
                    nIds: Object.keys($('input[name="inventory"]').data() || {}).join(','),
                    oId: Object.keys($('input[name="object"]').data() || {}).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price).toFixed(2) + '" data-catname="' + v.catname + '">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#inventory > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                noCust: 1,
                                type: 'stock',
                                nIds: Object.keys($('input[name="inventory"]').data() || {}).join(','),
                                query: $('#inventory > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="inventory"]').data()
                        }, $('input[name="inventory"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    noCust: 1,
                    type: 'service'
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price).toFixed(2) + '" data-catname="' + v.catname + '">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#services > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                noCust: 1,
                                type: 'service',
                                query: $('#services > .sfWrap input').val() || ''
                            },
                            all: false
                        }, $('input[name="services"]'));
                    }
                }, 'json');

                $.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchases"]').data() || {}).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price.replace('US', '').replace('$', '').trim()).toFixed(2) + '" data-catname="">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#purchases > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="purchases"]').data() || {}).join(','),
                                query: $('#purchases > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="purchases"]').data()
                        }, $('input[name="purchases"]'));
                    }
                }, 'json');
				
				$.post('/inventory/all', {
                    nIds: Object.keys($('input[name="purchases"]').data() || {}).join(','),
					user: Object.keys($('input[name="customer"]').data() || {}).join(','),
					by_user: 1
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price.replace('US', '').replace('$', '').trim()).toFixed(2) + '" data-purchase="$ ' + parseFloat(v.purchase.replace('US', '').replace('$', '').trim()).toFixed(2) + '" data-catname="">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#tradein > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="tradein"]').data() || {}).join(','),
                                query: $('#tradein > .sfWrap input').val() || '',
								user: Object.keys($('input[name="customer"]').data() || {}).join(','),
								by_user: 1
                            },
                            all: false,
                            select: $('input[name="tradein"]').data()
                        }, $('input[name="tradein"]'), invoices.newTradein);
                    }
                }, 'json');
            }
        });
    },
	newTradein: function() {
		var tradein_ids = Object.keys($('input[name="tradein"]').data());
		console.log(tradein_ids);
		$('#newTradein tbody > tr').each(function(i, v) {
			if ($.inArray($(v).attr('data-id'), tradein_ids) < 0)
				$(v).remove();
		})
		if ($('input[name="tradein"]').data()) {
			$.each($('input[name="tradein"]').data(), function(i, v) {
				if (!$('#trade_mdl_' + i).length) {
					$('#newTradein tbody').append($('<tr/>', {
						id: 'trade_mdl_' + i,
						'data-id': i,
						html: $('<td/>', {
							class: 'tr_name',
							html: v.name
						})
					}).append($('<td/>', {
						class: 'tr_purchase',
						html: $('<input/>', {
							type: 'number',
							step: '0.001',
							name: 'purchase',
							value: v.purchase
						})
					})).append($('<td/>', {
						class: 'tr_sale',
						html: $('<input/>', {
							type: 'number',
							step: '0.001',
							name: 'sale',
							value: v.price
						})
					})));
				}
			});
		} else {
			$('#newTradein tbody').clear();
		}
		
		/*  */
	},
    chooseInv: function() {
        var tradein = {};
		if ($('#newTradein tbody > tr').length) {
			$('#newTradein tbody > tr').each(function(i, v) {
				tradein[$(v).attr('data-id')] = {
					name: $(v).find('.tr_name').text(),
					purchase: $(v).find('.tr_purchase > input').val(),
					price: $(v).find('.tr_sale > input').val()
				}
				$(v).find('input[name="sale"]').css('border-color', '#ddd');
				$(v).find('input[name="purchase"]').css('border-color', '#ddd');
			});
			$('input[name="tradein"]').removeData().data(tradein);	
			
			$.post('/invoices/send_prices', {tradein: tradein}, function(r) {
				if (r == 'OK')
					invoices.sendPurchase();
				else if (r.length) {
					r = JSON.parse(r);
					if (r.error == 'min_price') {
						$.each(r.ids, function(i, v) {
							$('#trade_mdl_' + v + ' input[name="sale"]').css('border-color', '#f00');
						});
						alr.show({
							class: 'alrDanger',
							content: 'Check min prices for sale',
							delay: 2
						});
					} else if (r.error == 'empty_price') {
						$('#trade_mdl_' + r.id + ' input[name="sale"]').css('border-color', '#f00');
						$('#trade_mdl_' + r.id + ' input[name="purchase"]').css('border-color', '#f00');
						alr.show({
							class: 'alrDanger',
							content: 'Prices can not be empty',
							delay: 2
						});
					}
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		} else 
			invoices.sendPurchase();
    },
	sendPurchase: function() {
		var err = null,
			data = new FormData();
		if ($('input[name="new_purchase"]').val().length) {
			if (!$('input[name="price"]').val()) {
				alr.show({
					class: 'alrDanger',
					content: 'Please enter sale price',
					delay: 2
				});
			} else {
				data.append('name', $('.pnName').text());
				data.append('photo', $('#purch .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('cprice', $('input[name="price"]').val());
				data.append('itemID', $('.itemId').text());
				data.append('link', $('input[name="new_purchase"]').val());
				
				$.ajax({
					type: 'POST',
					processData: false,
					contentType: false,
					url: '/invoices/newPurchase',
					data: data
				}).done(function(r) {
					console.log(r);
					loadBtn.stop($(event.target));
					if (r == 'min_price') {
						alr.show({
							class: 'alrDanger',
							content: 'Min sale price can not be less than $' + parseFloat(min_price($('.pnPrice').text().replace(/(\D*)/i, ''))).toFixed(2),
							delay: 2
						});
					} else if (r > 0) {
						alr.show({
							class: 'alrSuccess',
							content: 'Purchase successfully created',
							delay: 2
						});
						var purchases = $('input[name="purchases"]').data();
						purchases[r] = {
							name: $('.pnName').text(),
							price: $('input[name="price"]').val()
						};
						$('input[name="purchases"]').data(purchases);
						invoices.selectAll();
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				});
			}
		} else 
			invoices.selectAll();
	},
	selectAll: function() {
		 if (!Object.keys($('input[name="inventory"]').data()).join(',') && !Object.keys($('input[name="tradein"]').data()).join(',') && !Object.keys($('input[name="services"]').data()).join(',') && !Object.keys($('input[name="invoices"]').data()).join(',') && !Object.keys($('input[name="purchases"]').data()).join(',')) {
            alr.show({
				class: 'alrDanger',
				content: 'You did not choose the service!',
				delay: 2
			});
		 } else {
            var inv = $('input[name="inventory"]').data(),
                serv = $('input[name="services"]').data(),
				invc = $('input[name="invoices"]').data(),
				purch = $('input[name="purchases"]').data(),
				tradein = $('input[name="tradein"]').data();
			
            invoices.eachService(inv, 'stock');
            invoices.eachService(serv, 'service');
            invoices.eachService(purch, 'purchase');
            invoices.eachService(tradein, 'tradein');

            mdl.close('addInv');
			$('input[name="services"]').removeData()
            invoices.total();
        }
	},
    eachService: function(el, type) {
		if (el) {
			$.each(el, function(i, v) {
				if (!$('.mInvoice #tr_' + type + '_' + i).length || type == 'service') {
					$('.tbl.payInfo.mInvoice').append($('<div/>', {
						class: 'tr itm',
						id: 'tr_' + type + '_' + i,
						'data-id': i,
						'data-type': type,
						html: $('<div/>', {
							class: 'td',
							html: $('<span/>', {
								class: 'fa fa-times del',
								onclick: 'invoices.delInvItem(this, "' + type + '", ' + i + ')'
							})
						}).append(' ' + (($(v).attr('catname') && $(v).attr('catname') != 'null') ? ($(v).attr('catname') + ' ') : '') + $(v).attr('name'))
					}).append($('<div/>', {
						class: 'td w10',
						html: '1'
					})).append($('<div/>', {
						class: 'td w100' + (type == 'tradein' ? ' nPay trIn' : ''),
						html: (type == 'tradein' ? $('<input/>', {
							name: 'tradein_price',
							type: 'number',
							step: '0.1',
							min: '0',
							price: v.price,
							readonly: 'readonly',
							onchange: 'invoices.total();',
							value: parseFloat($(v)[0].purchase.replace('$', '').trim()).toFixed(2)
						}) : '$' + parseFloat($(v)[0].price ? $(v)[0].price.replace('$', '').trim() : 0).toFixed(2))
					})).append($('<div/>', {
						class: 'td w10',
						html: (type == 'tradein' ? 'no' : 'yes')
					})))
				}
			});
		}
    },
    delInvItem: function(el, type, id) {
        type = (type == 'stock') ? 'inventory' : type;
        $(el).parent().parent().remove();
        delete $('input[name="' + type + '"]').data()[id];
        invoices.total();
    },
    delInInvoice: function(el) {
        $(el).parents('.tbl').remove();
        invoices.total();
    },
    total: function(el) {
        var subtotal = 0,
			subtotal_end = 0,
			tradein = 0,
            pSub = 0,
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax,
            discount = 1;
        if ($('.tbl.payInfo.discount').length)
            discount = (100 + parseFloat($('.tbl.payInfo.discount .w100').text().replace('$', ''))) / 100;
        $('.payInfo:not(.discount)').each(function(ind, val) {
            $(val).find('div > .td.w100:not(".nPay")').each(function(i, v) {
                if ($(v).parent().hasClass('discountTr'))
                    pSub *= ((100 + parseFloat($(v).text().replace('$', ''))) / 100);
                else
                    pSub += parseFloat($(v).text().replace('$', ''));
            });
            subtotal += pSub;
            pSub = 0;
        });
		
		 $('.td.w100.trIn').each(function(i, v) {
			tradein += parseFloat($(v).find('input').length ? $(v).find('input').val() : $(v).text().replace('$', ''));
		});
		
		$('.td.w100.onsite_add_price').each(function(i, v) {
			subtotal += parseFloat($(v).find('input').length ? $(v).find('input').val() : $(v).text().replace('$', ''));
		});
			
        subtotal *= discount;
		subtotal_end = subtotal - tradein;
        $('#subtotal').text(subtotal_end.toFixed(2));
        $('#tax').text((subtotal * tax / 100).toFixed(2));
        $('#total').text((subtotal_end + parseFloat($('#tax').text().replace('$', ''))).toFixed(2));
        $('#due').text((parseFloat($('#total').text().replace('$', '')) - parseFloat($('#paid').text().replace('$', '')) || 0).toFixed(2));
    },
    create: function(el, id) {
        loadBtn.start($(el));
        var data = {
                id: id,
				services: ''
            },
            err = null,
            subtotal = 0,
			tradein_arr = {},
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax;
		if ($('.mInvoice .tr[data-type="service"]').length) {
			$('.mInvoice .tr[data-type="service"]').each(function(i, v) {
				if (data.services.length) data.services += ',';
				data.services += $(v).attr('data-id');
			})
		}
        if (!Object.keys($('input[name="customer"]').data()).join(',').length) {
            err = lang[64];
        } else if (!Object.keys($('input[name="object"]').data()).join(',').length) {
            err = lang[63];
        } else if (!data.services && !Object.keys($('input[name="inventory"]').data()).join(',').length && !Object.keys($('input[name="tradein"]').data()).join(',').length && !Object.keys($('input[name="purchases"]').data()).join(',').length && !$('[data-type="onsite"]').length) {
            err = lang[62];
        } else {
            data.customer = Object.keys($('input[name="customer"]').data()).join(',');
            data.object = Object.keys($('input[name="object"]').data()).join(',');
            data.inventory = Object.keys($('input[name="inventory"]').data()).join(',');
            data.purchases = Object.keys($('input[name="purchases"]').data()).join(',');
            data.tradein = Object.keys($('input[name="tradein"]').data()).join(',');
			
			if ($('input[name="onsite_price"]').length) data.add_onsite_price = $('input[name="onsite_price"]').val()

            $('.payInfo.mInvoice > div > .td.w100').each(function(i, v) {
                subtotal += parseFloat($(v).text());
            });
			
			$('.td.w100.trIn').each(function(i, v) {
				tradein_arr[$(v).parent().attr('data-id')] = {
					purchase: parseFloat($(v).find('input').length ? $(v).find('input').val() : Math.abs(parseFloat($(v).text().replace('$', '').trim()))),
					price: parseFloat($(v).find('input').length ? $(v).find('input').attr('price') : 0)
				};
			});
			
            /* data.subtotal = subtotal;
            data.tax = (subtotal * tax / 100).toFixed(2);
            data.total = parseFloat(subtotal) + parseFloat((subtotal * tax / 100).toFixed(2)); */
			data.subtotal = $('#subtotal').text();
            data.tax = $('#tax').text();
            data.total = $('#total').text();
            data.paid = parseFloat($('#paid').text());
			data.trade_arr = tradein_arr;

            if (id) {
                data.invoices = '';
                $('.inTbl').each(function(i, v) {
                    data.invoices += $(v).attr('data-id') + ',';
                });
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/send', data, function(r) {
                loadBtn.stop($(el));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
                        delay: 2
                    });
                    var edit = location.pathname.match(/(.*)\/invoices\/?(.*)?/i);
                    if (edit[2].indexOf('edit') < 0) Page.get('/invoices/view/' + r);
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
    partial: function(id, el, p) {
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null;
        $('input[name="partial"]').css('border-color', '#ddd');
        if ($('input[name="partial"]').val() > Math.abs(parseFloat($('#due').text()))) {
            err = lang[61];
            $('input[name="partial"]').css('border-color', '#f00');
        } else if (!$('input[name="partial"]').val()) {
            err = lang[60];
            $('input[name="partial"]').css('border-color', '#f00');
        } else {
            data.partial = $('input[name="partial"]').val();
            data.purchace = p;
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/partial', data, function(r) {
                loadBtn.stop($(el));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
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
    discount: function(id, el, cb) {
        if (cb) cb(null, id);
        loadBtn.start($(el));
        var data = {
            id: id,
            discount: $('select[name="discount"]').val()
        };
        $.post('/invoices/discount', data, function(r) {
            loadBtn.stop($(el));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[58],
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
    },
    merge: function(id, el, cb) {
        if (cb) cb(null, id);
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null;
        if (!Object.keys($('input[name="invoices"]').data()).join(',')) {
            err = lang[59];
        } else {
            data.invoice = Object.keys($('input[name="invoices"]').data()).join(',');
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/add_invoice', data, function(r) {
                loadBtn.stop($(el));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content:lang[58],
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
    del: function(id) {
        $.post('/invoices/del', {
            id: id
        }, function(r) {
            if (r == 'OK') {
                if ($('#invoice_' + id).length)
                    $('#invoice_' + id).remove();
                else
                    Page.get('/invoices/');
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    issCreate: function(id, c, o, d) {
		/* if (parseFloat($('#doit').text().replace('$', '')) == 0) {
			alr.show({
				class: 'alrDanger',
				content: 'You can not create invoice before enter doit price!',
				delay: 2
			})
		} else { */
			var data = {
					issue: id,
					discount: d
				},
				err = null,
				inventory = '',
				services = '',
				purchases = '';
			/* $('#tbl_inventory > .tr > .td:first').each(function(i, v) {
				inventory += $(v).text().trim().replace('#', '') + ',';
			});
			$('#tbl_services > .tr > .td:first').each(function(i, v) {
				services += $(v).text().trim().replace('#', '') + ',';
			});
			$('#tbl_purchases > .tr > .td:first').each(function(i, v) {
				purchases += $(v).text().trim().replace('#', '') + ',';
			}); */
			data.customer = c;
			data.object = o;
			/* data.inventory = inventory;
			data.services = services;
			data.purchases = purchases; */
			data.subtotal = parseFloat($('#totalPrice').text().replace('$', ''));
			data.tax = ((parseFloat($('#doit').text().replace('$', '')) > 0) ? parseFloat($('#doit').text().replace('$', '')) : (parseFloat($('#totalPrice').text().replace('$', '')))) * parseFloat($('#tax').text()) / 100;
			//data.doit = ((parseFloat($('#doit').text().replace('$', '')) > 0) ? parseFloat($('#doit').text().replace('$', '')) + data.tax : 0);
			//data.total = (parseFloat($('#doit').text().replace('$', '')) > 0) ? data.doit : data.subtotal + data.tax;
			data.total = data.subtotal + data.tax;
			data.paid = 0;

			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/invoices/send', data, function(r) {
					if (r > 0) {
						alr.show({
							class: 'alrSuccess',
							content: lang[58],
							delay: 2
						});
						Page.get('/invoices/view/' + r);
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				});
			}
		//}
    }, saveForm: function(el, f) {
		f.preventDefault();
		loadBtn.start($(el).find('button[type="submit"]'));
		var data = {};
		if (!$('textarea[name="content"]').val()) {
			loadBtn.stop($(el).find('button[type="submit"]'));
			alr.show({
				class: 'alrDanger',
				content: lang[57],
				delay: 2
			});
		} else {
			data.content = $('textarea[name="content"]').val();
			$.post('/invoices/save_form', data, function(r) {
				loadBtn.stop($(el).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: lang[56],
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
	}, makePayment: function(el, id, p) {
		loadBtn.start($(el));
			var data = {};
			if (!$('input[name="cash"]').val()) {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Enter amount',
					delay: 2
				});
			} else if (($('select[name="method"]').val() != 'cash' || p) && Math.abs($('input[name="cash"]').val()) > Math.abs(parseFloat($('#due').text()))) {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Amount exceeds due',
					delay: 2
				});
			} else if (!$('input[name="check"]').val() && $('select[name="method"]').val() == 'check') {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Enter check number',
					delay: 2
				});
			} else {
				data.id = id;
				data.method = $('select[name="method"]').val();
				data.amount = parseFloat($('input[name="cash"]').val()) - ($('input[name="rest"]').length ? parseFloat($('input[name="rest"]').val()) : 0);
				data.check = $('input[name="check"]').val();
				$.post('/invoices/make_tran', data, function(r) {
					loadBtn.stop($(el));
					if (r == 'OK') {
						alr.show({
							class: 'alrSuccess',
							content: 'Transaction is done',
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
	}, makeTran: function(id, el, p) {
		if ($('select[name="method"]').val() == 0) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, select payment method',
				delay: 2
			});
		} else if (!$('input[name="cash"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, enter amount',
				delay: 2
			});
		} else {
			if ($('input[name="cash"]').val() < parseFloat($('#due').text())) {
				mdl.open({
					id: 'appliedPartial',
					title: 'Partial payment',
					content: $('<div/>', {
						class: 'aCenter',
						html: 'Are you applied to make partial payment?'
					}).append($('<div/>', {
						class: 'cashGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit ac',
							onclick: 'invoices.makePayment(this, ' + id + ', ' + p + ');',
							html: 'Yes'
						})
					}).append($('<button/>', {
						type: 'button',
						class: 'btn btnSubmit dc',
						onclick: 'mdl.close();',
						html: 'No'
					})))
				})
			} else {
				invoices.makePayment(el, id, p);
			}
		}
	}
}