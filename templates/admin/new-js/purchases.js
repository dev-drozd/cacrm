/**
 * @Appointment Purchases
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/

var Purchases = {
	cpage: 0,
	reciveStock: function(id) {
/* 		mdl.open({
			id: 'reciveStock',
			title: 'Select specification',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup fw',
					id: 'type',
					html: $('<label/>', {
						html: 'Type'
					})
				}).append($('<input/>', {
					type: 'hidden',
					name: 'type'
				})).append($('<ul/>', {
					class: 'hdn'
				}))
			}).append($('<div/>', {
				class: 'iGroup fw',
				id: 'brand',
				html: $('<label/>', {
					html: 'Brand'
				})
			}).append($('<input/>', {
				type: 'hidden',
				name: 'brand'
			})).append($('<ul/>', {
					class: 'hdn'
			}))).append($('<div/>', {
				class: 'iGroup fw',
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
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					html: 'Send',
					onclick: 'purchases.receiveMdl(' + id + ')'
				})
			})),
			cb: function() {

				$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data()).join(',')}, function (r) {
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
								query: $('#brand > .sfWrap input').val() || ''
							},
							all: false,
							select: $('input[name="brand"]').data(),
							s: true
						}, $('input[name="brand"]'), inventory.getGroup);
					}
				}, 'json');
				
				
				$.post('/inventory/allTypes', {
					name: 1,
					brand: Object.keys($('input[name="brand"]').data()).join(',')
				}, function (r) {
					if (r){
						var bitems = '', blId = 0;
						$.each(r.list, function(i, v) {
							bitems += '<li data-value="' + v.id + '">' + v.name + '</li>';
							blId = v.id;
						});
						$('#type > ul').html(bitems).sForm({
							action: '/inventory/allTypes',
							data: {
								lId: blId,
								query: $('#type > .sfWrap input').val() || '',
								name: 1,
								brand: Object.keys($('input[name="brand"]').data() || [])[0] || 0
							},
							all: false,
							select: $('input[name="type"]').data(),
							s: true
						}, $('input[name="type"]'));
					}
				}, 'json');
			}
		}); */
	},
	receiveMdl: function(id) {
/* 		mdl.open({
			title: 'Receive purchase',
			content: $('<div/>', {
				class: 'conQuestion',
				html: $('<div/>', {
					html: 'Are you sure that you have received purchase #' + id + '?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					type: 'button',
					onclick: 'purchases.receive(' + id + ');',
					html: 'Yes'
				})
			}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'No'
				})))
		}); */
	},
	receive: function(id) {
		$.post('/purchases/receive', {
			id: id,
			type: $('input[name="type"]').length ? Object.keys($('input[name="type"]').data())[0] : 0,
			brand: $('input[name="brand"]').length ? Object.keys($('input[name="brand"]').data())[0] : 0,
			model: $('input[name="model"]').length ? Object.keys($('input[name="model"]').data())[0] : 0,
			model_new: $('#model .sfWrap input').length ? $('#model .sfWrap input').val() : ''
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Purchase was successfully received',
					delay: 2
				});
				Page.get(location.href);
			} else if (r == 'no_info') {
				alr.show({
					class: 'alrDanger',
					content: 'Please, select fields',
					delay: 2
				});
			} else if (r == 'mdl_exists') {
				alr.show({
					class: 'alrDanger',
					content: 'Model name exists',
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
	comments: function(id) {
		purchases.cpage = 0;
		$.post('/purchases/comments', {id: id}, function(r) {
/* 			mdl.open({
				id: 'cashComments',
				title: 'Comments',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'comments',
						html: r.content
					})
				}).append($('<button/>', {
					class: 'btn btnLoad' + (r.left_count > 0 ? '' : ' hdn'),
					onclick: 'cash.doloadComments(this,' + id + ');',
					html: 'Load comments'
				})).append($('<div/>', {
					class: 'commentForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Leave comment'
						})
					}).append($('<textarea/>', {
							name: 'comment'
						}))
				}).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'purchases.sendComment(' + id + ');',
						html: 'Send'
					})
				})))
			}); */
		}, 'json');
	}, sendComment: function(id) {
		if (!$('textarea[name="comment"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Comment can not be empty',
				delay: 2
			});
		} else {
			$.post('/purchases/send_comment', {
				id: id,
				text: $('textarea[name="comment"]').val()
			}, function(r) {
				if (r.res == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Comment was successfully added',
						delay: 2
					});
					$('.comments').append(r.html);
					$('textarea[name="comment"]').val('');
                    p = ($(window).height() - $('.mdl').height()) / 2;
                    $('.mdlWrap').css('padding-top', (p < 70 ? 70 : p) + 'px');
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			}, 'json');
		}
	}, doloadComments: function(e, id) {
		purchases.cpage += 1;
		$.post('/purchases/comments', {
			id: id, 
			page: purchases.cpage
		}, function(r) {
			$('.comments').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	},
	rmaRestore: function(id) {
		$.post('/purchases/rma_restore', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Purchase was successfully restored',
					delay: 2
				});
				if ($('#purchase_' + id).hasClass('returnRequest'))
					$('#purchase_' + id).removeClass('returnRequest').find('.purchComments').remove();
				else
					$('#purchase_' + id).remove();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	confirmDel: function(id, c) {
/* 		mdl.open({
			title: 'Confirm deletion',
			content: $('<div/>', {
				class: 'conQuestion',
				html: $('<div/>', {
					html: c ? 'Purchase was paid by customer! Are sure you want to delete it?' : 'Are you sure?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					type: 'button',
					onclick: 'purchases.del(' + id + ');',
					html: 'Yes'
				})
			}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'No'
				})))
		}); */
	},
	confirmRma: function(id) {
/* 		mdl.open({
			title: 'Confirm request',
			content: $('<div/>', {
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'RMA comment'
					})
				}).append($('<textarea/>', {
					name: 'rma_comment',
					placeholder: 'Comment are required'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'purchases.rma(' + id + ');',
					html: 'Send'
				})
			}))
		}); */
	},
	rmaReadComment: function(id) {
		$.post('/purchases/get_rma_comment', {
			id: id
		}, function(r) {
/* 			mdl.open({
				id: 'rma_comment',
				title: 'Confirm RMA reason',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'sumCash',
						html: r || 'Request has no comment'
					})
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn  btnSubmit ac',
						html: 'Accept',
						onclick: 'purchases.rma(' + id + ', \'confirm\', 1);'
					})
				}).append($('<button/>', {
						type: 'button',
						class: 'btn btnSubmit dc',
						html: 'Dicline',
						onclick: 'purchases.rma(' + id + ', \'confirm\', 0);'
					})))
			}); */
		});
	},
	rma: function(id, a, t) {
		//if (!$('textarea[name="rma_comment"]').length  || ($('textarea[name="rma_comment"]').length && $('textarea[name="rma_comment"]').val().trim().length >= 10)) {
			$.post('/purchases/send_rma', {
				id: id, 
				action: a, 
				type: t,
				comment: $('textarea[name="rma_comment"]').length ? $('textarea[name="rma_comment"]').val() : ''
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: (a == 'confirm' ? 'Purchase returning was confirmed' : (a == 'pickup' ? 'Purchase are picked up' : 'Return request was sent')),
						delay: 2
					});
					if (!a) {
						$('#purchase_' + id).addClass('returnRequest').find('.uMore li:last-child').remove();
						$('#purchase_' + id).find('.uMore').append($('<span/>', {
							class: 'fa fa-comment purchComments',
							onclick: 'purchases.comments(' + id + ');'
						}));
					} else if (a == 'confirm')
						$('#purchase_' + id).remove();
					else 
						$('#purchase_' + id).removeClass('unpaid').addClass('paid').find('.uMore li:last-child').remove();
					
					if ($('.mdl').length)
						mdl.close();
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
		/* } else {
			alr.show({
				class: 'alrDanger',
				content: 'Comment are required and must have enough 10 symbols',
				delay: 2
			});
		} */
	},
	confirm: function(id) {
		$.post('/purchases/confirm_purchase', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Purchase was successfully confirmed',
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
	changePrice: function() {
		price = $('.uForm').find('input[name="price"]').val().replace(/\D(\.?)\D/ig, '');
		$('input[name="total"]').val((price * $('input[name="quantity"]').val()).toFixed(2));
		$('input[name="proceeds"]').val(((parseFloat($('input[name="sale"]').val()) - parseFloat($('input[name="price"]').val()))*$('input[name="quantity"]').val()).toFixed(2));
	}, /* getLink: function(el) {
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
					if (el.value.indexOf('ebay.') > 0) {
						var price = $(r).find('#mm-saleDscPrc').length ? $(r).find('#mm-saleDscPrc').text().trim() : $(r).find('#prcIsum').text().trim();
						$(r).find('#itemTitle').find('span').remove();
						$(el).parents('.uForm').find('input[name="name"]').val($(r).find('#itemTitle').text().replace('Details about', '').trim());
						$(el).parents('.uForm').find('input[name="price"]').val(price.replace(',', '').replace('$', '').replace('US', '').trim());
						$(el).parents('.uForm').find('input[name="quantity"]').val(1);
						$(el).parents('.uForm').find('input[name="total"]').val($(r).find('#prcIsum').text().trim().replace(/\D(\.?)\D/ig, ''));
						$(el).parents('.uForm').find('input[name="estimated"]').val($(r).find('#delSummary .vi-acc-del-range > b').text().trim() ? 'Between ' + $(r).find('#delSummary .vi-acc-del-range > b').text().trim() : '');
						$(el).parents('.uForm').find('input[name="tracking"]').val($(r).find('#descItemNumber').length ? $(r).find('#descItemNumber').text().trim() : '');
						if ($(r).find('#icImg').attr('src')) {
							$(el).parents('.uForm').find('.imgGroup').removeClass('hdn').find('img').attr('src', $(r).find('#icImg').attr('src').split('?')[0]);
						}
					} else if (el.value.indexOf('amazon.') > 0) {
						$(el).parents('.uForm').find('input[name="name"]').val($(r).find('#productTitle').text().trim());
						$(el).parents('.uForm').find('input[name="price"]').val($(r).find('#priceblock_dealprice').text().replace('$', '').trim());
						$(el).parents('.uForm').find('input[name="tracking"]').val(el.value.match(/\/dp\/(.*)[\/|?]/i) ? el.value.match(/\/dp\/(.*)[\/|?]/i)[1] : '');
						$(el).parents('.uForm').find('input[name="quantity"]').val(1);
						if ($(r).find('#landingImage').attr('src')) {
							$(el).parents('.uForm').find('.imgGroup').removeClass('hdn').find('img').attr('src', $(r).find('#landingImage').attr('src').split('?')[0]);
						}
					}
				break;
            }
        }, 'html');
    }, */
	getLink: function(el) {
		var link = ((el.value.indexOf('http://') >= 0 || el.value.indexOf('https://') >= 0) ? el.value : 'http://' + el.value);
		$('button[type="submit"]').attr('disabled', 'disabled');
        $.get('/parser?lnk=' + link, function(r) {
            switch (r) {
                case 'no_responce':
                    alr.show({
                        class: 'alrDanger',
                        content:  lang[13],
                        delay: 2
                    });
				break;

                default:
					$(el).parents('.uForm').find('input[name="name"]').val(r.title);
					$(el).parents('.uForm').find('input[name="price"]').val(r.price.replace(/[^0-9.]/g, ''));
					$(el).parents('.uForm').find('input[name="sale"]').val(r.sale_price).attr('min', r.sale_price);
					$(el).parents('.uForm').find('input[name="quantity"]').val(1);
					$(el).parents('.uForm').find('input[name="total"]').val(r.price.replace(/[^0-9.]/g, ''));
					$(el).parents('.uForm').find('input[name="tracking"]').val(r.code);
					if (r.image)
						$(el).parents('.uForm').find('.imgGroup').removeClass('hdn').find('img').attr('src', r.image);
					if (r.estimated)
						$(el).parents('.uForm').find('input[name="estimated"]').val(r.estimated);
				break;
            }
			$('button[type="submit"]').removeAttr('disabled');
        }, 'json');
    },
	minPrice: function(e) {
		if (parseFloat($(e).val()) < parseFloat($(e).attr('min'))) 
			$(e).val($(e).attr('min'));
	},
    send: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (!(form[i].value.length || $('input[name="' + form[i].name + '"]').data()) && $.inArray(form[i].name, ['comment', 'customer', 'tracking', 'ship-tracking', 'estimated']) < 0 && form[i].name.length) {
                err = lang[101] + form[i].name + lang[102];
                $('input[name="' + form[i].name + '"]').css('border-color', '#f00');
                break;
            } else if (!$('input[name="' + form[i].name + '"]').val() && form[i].name == 'salename') {
                err = 'Sale name can not be empty';
                $('input[name="' + form[i].name + '"]').css('border-color', '#f00');
                break;
            } else if ($('input[name="' + form[i].name + '"]').val() <= 0 && form[i].name == 'sale') {
				err = 'Please, enter sale price';
                $('input[name="' + form[i].name + '"]').css('border-color', '#f00');
                break;
			}
            if (form[i].name == 'object') {
				if (!Object.keys($('input[name="object"]').data()).join(','))
					err = 'Please, select store';
				else
					data.append(form[i].name, Object.keys($('input[name="' + form[i].name + '"]').data() || {}));
			} else if (form[i].name == 'customer')
				data.append(form[i].name, Object.keys($('input[name="' + form[i].name + '"]').data() || {}));
            else if (form[i].name == 'price')
				data.append(form[i].name, $('[name="price"]').val().replace(/(\D*)/i, ''));
            else
                data.append(form[i].name, form[i].value);
        }
        if ($('.imgGroup img').attr('src'))
            data.append('photo', $('.imgGroup img').attr('src'));
        else
            data.append('del_photo', true);
		
		data.append('currency', $('select[name="currency"]').val());
		data.append('purchase_currency', $('select[name="purchase_currency"]').val());
		
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
            url: '/purchases/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'min_price') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Min sale price can not be less than $' + parseFloat(min_price(parseFloat($('[name="price"]').val().replace(/(\D*)/i, '')))).toFixed(2),
                    delay: 2
                });
			} else if (r.indexOf('min_price') >= 0) {
				alr.show({
                    class: 'alrDanger',
                    content: 'Min sale price can not be less than $' + r.split('_')[2],
                    delay: 2
                })
			} else if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
				if ($_GET('usr'))
					Page.get('/users/view/' + parseInt($_GET('usr')));
				else
					Page.get('/purchases/edit/' + r);
            } else if (r == 'enter_price') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Please, enter price',
                    delay: 2
                });
			} else if (r == 'no_photo') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Please, load photo',
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
        $.post('/purchases/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#purchase_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
			if ($('.mdl').length)
				mdl.close();
        });
    }
};