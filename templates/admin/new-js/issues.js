/**
 * @Appointment Issues
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/

var Issues = {
	buyDevice: function(a){
		box.open({
			title: 'Buy this device',
			content: '\
			<div class="iGroup">\
				<label>Purchase price:</label>\
				<input type="number" name="pprice" min="0" step="0.001" oninput="issues.buyMinPrice(this.value)" required>\
			</div>\
			<div class="iGroup">\
				<label>Sale price:</label>\
				<input type="number" name="sprice" min="0" step="0.001" id="bsprice" required>\
			</div>\
			<input type="hidden" name="id" value="'+a+'">',
			submit: ['<span class="fa fa-shopping-cart"></span> Buy', function(a){
				$.post('/inventory/buy_device', {
					id: this.id.value,
					sprice: this.sprice.value,
					pprice: this.pprice.value
				}, function(){
					Page.get(location.href);
				});
				box.close(a);
			}]
		});
	},
	buyMinPrice: function(a){
		$.get('/inventory/buy_min_price?price='+a, function(a){
			$('#bsprice').attr('min', a);
		});
	},
	sendTransfer: function(id, el) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		data.issue_id = id;	
		data.store_id = Object.keys($('#object > input').data())[0];
		data.staff_id = Object.keys($('#staff > input').data())[0];
		data.location_id = Object.keys($('#location > input').data())[0];
		data.location_count = $('select[name="sublocation"]').val();
		
		if (!data.store_id)
			err = 'Select store';
		
		if (!data.location_id)
			err = 'Select location';
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/issues/transfer', data, function(r) {
				loadBtn.stop($(el));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Transfer successfully created',
						delay: 2
					});
					mdl.close();
				} else if (r == 'PENDING') {
					alr.show({
						class: 'alrDanger',
						content: 'Transfer is already done and pending for confirmation',
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
	transfer: function(id) {
		$.post('/users/all', {staff: 1}, function(ur) {
            var uitems = '', ulId = 0;
            if (ur.list) {
                $.each(ur.list, function(ui, uv) {
                    uitems += '<li data-value="' + uv.id + '">' + uv.name + '</li>';
                    ulId = uv.id;
                });
            }

            $.post('/objects/all', {
				nIds: $('.devLocation').attr('data-object')
			}, function(or) {
                var oitems = '', olId = 0;
                if (or.list) {
                    $.each(or.list, function(oi, ov) {
                        oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
                        olId = ov.id;
                    });
                }
/* 				mdl.open({
					id: 'transfer',
					title: 'Transfer issue',
					content: $('<div/>', {
						class: 'uForm',
						html: $('<input/>', {
							type: 'hidden',
							name: 'status'
						})
					}).append($('<div/>', {
						class: 'iGroup fw',
						id: 'object',
						html: $('<label/>', {
							html: 'Store'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'object'
					})).append($('<ul/>', {
						class: 'hdn'
					}))).append($('<div/>', {
						class: 'iGroup fw',
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
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'issues.sendTransfer(' + id + ', this)',
							html: 'Send'
						})
					})),
					cb: function() {
						$('#page').select();
						
						$('#object > ul').html(oitems).sForm({
                            action: '/objects/all',
                            data: {
                                lId: olId,
								nIds: $('.devLocation').attr('data-object'),
                                query: $('#object > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="object"]').data(),
                            s: true,
							link: 'objects/edit'
                        }, $('input[name="object"]'), getLocation);

                        $('#staff > ul').html(uitems).sForm({
                            action: '/users/all',
                            data: {
                                lId: ulId,
                                query: $('#staff > .sfWrap input').val() || '',
                                staff: 1
                            },
                            all: false,
                            select: $('input[name="staff"]').data(),
                            s: true,
							link: 'users/view'
                        }, $('input[name="staff"]'));

						var st = {};
						
						if ($('.devStatus').attr('data-id') != 0) st[$('.devStatus').attr('data-id')] = {name: $('.devStatus').text().replace('Status: ', '')};
						$('input[name="status"]').data(st);
						status_id = st;
					}
				}); */
			}, 'json');
		}, 'json'); 
	},
	transferConfirm: function(id){
		$.post('/issues/confirm_transfer', {id: id}, function(r){
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'You transfer confirmed issue',
					delay: 2
				});
				Page.get('/issues/view/'+id);
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'Oops, It looks like a bug',
					delay: 2
				});
			}
		});
	},
	confirmStoreIssue: function(id) {
		$.post('/issues/confirm_store_issue', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'You successfully confirmed issue',
					delay: 2
				});
				Page.get('/issues/view/' + id);
			} else if (r == 'confirmed') {
				alr.show({
					class: 'alrDanger',
					content: 'Issue is already confirmed',
					delay: 2
				});
				$('#issue_store_' + id).remove();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	assignedConfirm: function(id, type) {
		$.post('/issues/confirm_assigned', {
			id: id,
			type: type
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'You successfully ' + (type ? 'confirmed' : 'diclined') + ' request',
					delay: 2
				});
				$('#notif_' + id).remove();
			} else if (r == 'not_staff') {
				alr.show({
					class: 'alrDanger',
					content: 'You trying to ' + (type ? 'confirmed' : 'diclined') + ' not your request',
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
	assigned: function(id) {
		$.post('/users/all', {staff: 1}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
			}
/* 			mdl.open({
				id: 'assigned',
				title: 'Switch assigned',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'iGroup',
						id: 'staff',
						html: $('<label/>', {
							html: 'Staff'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'staff'
					})).append($('<ul/>'))
				}).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						html: 'Send',
						type: 'button',
						onclick: 'issues.sendAssigned(' + id + ')'
					})
				})),
				cb: function() {
					$('#staff > ul').html(items).sForm({
						action: '/users/all',
						data: {
							lId: lId,
							query: $('#staff > .sfWrap input').val() || '',
							staff: 1
						},
						all: false,
						s: true,
						link: 'users/view'
					}, $('input[name="staff"]'));
				}
			}); */
		}, 'json');
	},
	sendAssigned: function(id) {
		if (Object.keys($('input[name="staff"]').data()).join(',')) {
			$.post('/issues/send_assigned', {
				id: id,
				staff: Object.keys($('input[name="staff"]').data()).join(',')
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'You successfully sent request for switching assigned staff',
						delay: 2
					});
					mdl.close();
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
				content: 'Please, select staff',
				delay: 2
			});
		}
	},
	addRandomFeedback: function(user_id) {
		var rating = parseInt($('#feedback_' + user_id).attr('data-stars')),
			star = '';
		for(j = 1; j <= 5; j++){
			star += '<span class="fa fa-star rStart mini r_' + rating + (
				j <= rating ? ' active' : ''
			) + '" ratting="' + j + '"></span>';
		}
/* 		mdl.open({
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
					html: star
				}))
			}).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'Comment'
				})
			}).append($('<textarea/>', {
				name: 'feed_comment',
				html: $('#comment_' + user_id).text()
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'issues.sendRandomFeedback(event, '+user_id+')',
					html: 'Send'
				})
			}))
		}); */
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
	sendRandomFeedback: function(event, user_id){
		event.preventDefault();
		loadBtn.start($(event.target));
		var ratting = parseInt($('#fb_ratting').val()),
			comment = $('textarea[name="feed_comment"]').val();
		if(ratting > 0){
			$.post('/issues/send_random_feedback', {
				user_id: user_id,
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
	confirmWarranty: function(id, device) {
		$.post('/issues/confirm_warranty', {
			issue: id,
			device: device
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Warranty request was successfully confirmed',
					delay: 2
				});
				Page.get(location.href);
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
	},
	mdlWarranty: function(id, device) {
		mdl.open({
			id: 'warranty_mdl',
			title: 'Warranty reason',
			content: $('<div/>', {
				class: 'form',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Plase, enter warranty reason'
					})
				}).append($('<textarea/>', {
					id: 'warranty_comment'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'issues.warranty(' + id + ', ' + device + ');',
					text: 'Send'
				})
			}))
		});
	},
	warranty: function(id, device) {
		if ($('#warranty_comment').val().trim().length > 10) {
			$.post('/issues/request_warranty', {
				issue: id,
				device: device,
				comment: $('#warranty_comment').val()
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Warranty request was successfully sent',
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
	} else {
		alr.show({
			class: 'alrDanger',
			content: 'Please, enter warranty reason. It must has enought 10 symbols',
			delay: 3
		});
	}
	},
	invisible: function(id, price) {
/* 		mdl.open({
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
		}); */
	},
	send_invisible: function(id) {
		$.post('/issues/send_invisible', {
			id: id, 
			service: JSON.stringify($('input[name="upcharge"]').data())
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
	updateRate: function(id){
		
	},
	starFeedback: function(a, b, c, d){
		b.preventDefault();
		switch(b.type){
			
			case 'mouseover':
				var count = $(b.target).attr('ratting');
				for(var i = 1; i <= count; i++){
					$(a).find('span').eq(i).addClass('hover');
				}
			break;
			
			case 'mouseout':
				$(a).find('span').removeClass('hover');
			break;
			
			case 'click':
				var count = $(b.target).attr('ratting'), rate = $(a).attr('data-rate');
				$(a).attr('data-rate', count);
				$(a).find('span').removeClass('active').removeClass('hover').removeClass('r_'+rate);
				for(var i = 1; i <= count; i++){
					$(a).find('span').eq(i).addClass('active').addClass('r_'+count);
				}
				if(c > 0){
					$.post('/issues/feedbacks_update_ratting', {
						id: c,
						ratting: count,
						random: d || 0
					}, function(r){
						console.log(r);
					});
				}
			break;
		}
	},
	addFeedback: function(issue_id, id) {
/* 		mdl.open({
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
		}); */
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
					items += '<option value="' + v.id + '"' + (v.id == id ? ' selected' : '') + ' data-price="' + v.price + '">' + v.name + '</option>';
				});
/* 				mdl.open({
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
				}) */
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
	sendDiscount: function (is) {
		var dis = $('select[name="discount_id"]').val();
		$.post('/issues/send_discount', {
			id: is,
			discount: '{"' + dis + '":{"name":"' + $('select[name="discount_id"] > option[value="' + dis + '"]').text() + '","price":"' + $('select[name="discount_id"] > option[value="' + dis + '"]').attr('data-price') + '"}}',
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
	}, updateStatus: function(id,b,c) {
		$.post('/issues/is_warranty', {
			id: id
		}, function(r) {
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
					class: 'iGroup fw hdn',
					id: 'req_note',
					html: $('<label/>', {
						html: 'Status note'
					})
				}).append($('<textarea/>', {
					name: 'status_note'
				}))).append($('<div/>', {
					class: 'iGroup fw',
					id: 'location',
					warranty: r,
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
					class: 'iGroup price bobject fw',
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
					class: 'iGroup fw price',
					id: 'purchase',
					html: $('<label/>', {
						html: 'Purchases'
					})
				}).append($('<ul/>', {
					class: 'hdn'
				}))).append($('<div/>', {
					class: 'iGroup fw price',
					html: $('<label/>', {
						html: 'New purchase'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'new_purchase',
					oninput: 'issues.newPur(this)'
				}))).append($('<div/>', {
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
						onclick: 'issues.sendUpdate(' + id + ', this' + (r == 1 ? ', 1' : '') + ')',
						html: 'Send'
					})
				})),
				cb: function() {
					$('#page').checkbox();
					$('#page').select();
					var st = {},
						lc = {};
					location_count = $('.devLocation').attr('location_count');
					count_id = location_count;
					
					if ($('.devStatus').attr('data-id') != 0) st[$('.devStatus').attr('data-id')] = {name: $('.devStatus').text().replace('Status: ', '')};
					if ($('.devLocation').text().replace('Location: ', '').length) {
						lc[$('.devLocation').attr('data-id')] = {
							name: $('.devLocation').text().replace('Location: ', ''),
							count: $('.devLocation').attr('data-count')
						};
					}
					$('input[name="status"]').data(st);
					status_id = st;
					if ($('.devLocation').text().replace('Location: ', '').length) {
						location_id = lc;
						object_id  = $('.devLocation').attr('data-object');
					}
					count_id = lc;
					$.post('/inventory/allStatuses', {
							nIds: Object.keys($('input[name="status"]').data()).join(','),
							set_inventory: Object.keys($('input[name="set_inventory"]').data()).join(','),
							set_services: Object.keys($('input[name="set_services"]').data()).join(','),
							warranty: r
						}, function (res) {
						if (res){
							var items = '',
							lId = 0;
							$.each(res.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-req="'+v.note+'">' + v.name + '</li>';
								lId = v.id;
							});
							$('#status > ul').html(items).sForm({
								action: '/inventory/allStatuses',
								data: {
									lId: lId,
									nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
									query: $('#status > .sfWrap input').val() || '',
									set_inventory: Object.keys($('input[name="set_inventory"]').data()).join(','),
									set_services: Object.keys($('input[name="set_services"]').data()).join(','),
									warranty: r
								},
								all: false,
								select: $('input[name="status"]').data(),
								s: true
							}, $('input[name="status"]'), function() {
									getLocation(1);
									issues.reqNote();
							}, {
								set_inventory : 'set_inventory', 
								set_services : 'set_services'
							});
							$('#status li[data-value='+b+']').click();
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
								items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-object="' + v.object + '">' + v.name + '</li>';
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
								select: $('input[name="set_inventory"]').data(),
								link: 'inventory/view'
							}, $('input[name="set_inventory"]'), function() {
								total.miniTotal(lc);
							});
						}
					}, 'json');
					
					$.post('/purchases/all', {
						nIds: Object.keys($('input[name="purchase"]').data()).join(',')
					}, function(r) {
						if (r) {
							var items = '',
								lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#purchase > ul').html(items).sForm({
								action: '/purchases/all',
								data: {
									nIds: Object.keys($('input[name="purchase"]').data() || {}).join(','),
									query: $('#purchase > .sfWrap input').val() || ''
								},
								all: false,
								select: $('input[name="purchase"]').data(),
								link: 'purchases/edit'
							}, $('input[name="purchase"]'));
						}
					}, 'json');
					
					$.post('/inventory/all', {
						type: 'service',
						object: $('#issue').attr('data-object'),
						nIds: Object.keys($('input[name="set_services"]').data()).join(',')}, function (r) {
						if (r){
							var items = '',
							lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#set_services > ul').html(items).sForm({
								action: '/inventory/all',
								data: {
									lId: lId,
									type: 'service',
									object: $('#issue').attr('data-object'),
									nIds: Object.keys($('input[name="set_services"]').data() || {}).join(','),
									query: $('#set_services > .sfWrap input').val() || ''
								},
								all: false,
								select: $('input[name="set_services"]').data(),
								link: 'inventory/service/edit'
							}, $('input[name="set_services"]'), function() {
								total.miniTotal(lc);
							});
						}
					}, 'json');
					
					total.miniTotal(lc);
				}
			});
		});

	}, 
	reqNote: function() {
		var item = $('input[name="status"]').data();
		
		item = item[Object.keys(item)[0]];
		if (item.req)
			$('#req_note').removeClass('hdn');
		else
			$('#req_note').addClass('hdn');
	},
	sendUpdate: function(id, el, warranty) {
        loadBtn.start($(el).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(el).parents('.uForm'),
            err = null;
		if (warranty) data.warranty = 1;
        form.find('.iGroup').each(function(i, v) {
            if ($(v).find('label').next().attr('name') == 'descr' && !$(v).find('label').next().val()) {
                err = lang[100];
			} else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().find('option[value="1"]').length && $(v).find('label').next().val() == '0') {
				err = 'Sublocation can not be empty';
            } else if ($.inArray($(v).find('label').next().attr('name'), ['descr', 'price', 'new_purchase', 'salename']) && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else {
                data[$(v).find('label').next().attr('name')] = (typeof $(v).find('label').next().data() == 'object' ? Object.keys($(v).find('label').next().data()).join(',') : $(v).find('label').next().val());
            }
        });
		data['inventory_id'] = $('input[name="inventory_id"]').val();
		data['important'] = $('input[name="important"]').val();
		data['status_note'] = $('textarea[name="status_note"]').val();
		
		data['set_inventory'] = JSON.stringify($('input[name="set_inventory"]').data());
		data['set_services'] = JSON.stringify($('input[name="set_services"]').data());
		
		data['purchases'] = JSON.stringify($('input[name="purchase"]').data());
		
		if ($('input[name="new_purchase"]').val()) {
			data['name'] = $('.pnName').text();
			data['photo'] = $('#addService .pnThumb > img').attr('src');
			data['price'] = $('.pnPrice').text().replace(/(\D*)/i, '');
			data['itemID'] = $('.itemId').text();
			data['cprice'] = ($('input[name="price"]').val() || 0);
			data['link'] = $('input[name="new_purchase"]').val();
			data['salename'] = $('input[name="salename"]').val();
			if ($('input[name="price"]').val() == 0)
				err = 'Price can not be 0';
			if (!$('input[name="salename"]').val())
				err = 'Sale name can not be empty';
		}
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
                        content: 'This status needs enough one part',
                        delay: 2
                    });
                } else if (r == 'assigned') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You are not assigned to this issue',
                        delay: 2
                    });
                } else if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74],
                        delay: 2
                    });
                    Page.get(location.href);
					mdl.close();
                } else if (r == 'confirmed') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You can not delete purchases. Someones are already ordered',
                        delay: 2
                    });
                } else if (r == 'receive_purchases') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You must receive all of the purchases to change the issue status',
                        delay: 2
                    });
                } else if (r.indexOf('min_price') >= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Min price for new purchase must be enought $' + r.split('_')[2],
                        delay: 2
                    });
                } else if (r == 'no_note') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Please, enter status note',
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
            } else if ($(v).find('label').next().attr('name') == 'service' && !Object.keys($(v).find('label').next().data()).join(',').length) {
                err = 'Services can not be empty';
            } else if ($(v).find('label').next().attr('name') == 'descr' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else if ($(v).find('label').next().attr('name') == 'location' || $(v).find('label').next().attr('name') == 'status') {
                data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data()).join(',');
            } else {
                data[$(v).find('label').next().attr('name')] = JSON.stringify($(v).find('label').next().data());
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
                } else if (parseInt(r) > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get('/issues/view/' + r);
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
        $.post('/issues/del', {
            id: id
        }, function(r) {
            if (r == 'OK') {
				if ($('#issue_' + id).length)
					$('#issue_' + id).remove();
				else 
					Page.get('/');
            } else {
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
                            select: $('input[name="inventory"]').data(),
							link: 'inventory/view'
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
                            select: $('input[name="service"]').data(),
							link: 'inventory/service/edit'
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
                    class: 'iGroup sfGroup fw',
                    id: 'object',
                    html: $('<label/>', {
                        html: 'Store'
                    })
                }).append($('<input/>', {
					type: 'hidden',
					name: 'object',
				})).append($('<ul/>'))
            }).append($('<div/>', {
				class: 'iGroup sfGroup fw price bobject',
				id: 'purchase',
				pIds: pIds,
				html: $('<label/>', {
					html: lang[66]
				})
			}).append($('<ul/>'))).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'New purchase'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'new_purchase',
				oninput: 'issues.newPur(this)'
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
				$.post('/invoices/objects', {}, function (r) {
					if (r){
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
							s: true
						}, $('input[name="object"]'), issues.cbPur);
					}
				}, 'json');
				
				issues.cbPur();
                /* $.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchase"]').data()).join(',') + pIds,
					store: 1
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-object="' + v.object + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#purchase > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                nIds: Object.keys($('input[name="purchase"]').data() || {}).join(',') + pIds,
                                query: $('#purchase > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="purchase"]').data(),
							link: 'purchases/edit'
                        }, $('input[name="purchase"]'));
                    }
                }, 'json'); */
            }
        });
    },
	cbPur: function() {
		if (!$('#purchase > ul').length) {
			$('#purchase > .sfWrap').remove();
			$('#purchase').append($('<ul/>'));
		}
	
		$.post('/purchases/all', {
			nIds: Object.keys($('input[name="purchase"]').data()).join(','),
			store: 1,
			object_id: Object.keys($('input[name="object"]').data())[0]
		}, function(r) {
			if (r) {
				var items = '',
					lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-object="' + v.object + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#purchase > ul').html(items).sForm({
					action: '/purchases/all',
					data: {
						nIds: Object.keys($('input[name="purchase"]').data() || {}).join(','),
						query: $('#purchase > .sfWrap input').val() || '',
						store: 1,
						object_id: Object.keys($('input[name="object"]').data())[0]
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="purchase"]').data(),
					link: 'purchases/edit'
				}, $('input[name="purchase"]'));
			}
		}, 'json');
	},
	/* newPur: function(el) {
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
					var title = '', img = '', price = 0, itemid = '';
					if (el.value.indexOf('ebay.') > 0) {
						$(r).find('#itemTitle').find('span').remove();
						price = ($(r).find('#mm-saleDscPrc').length ? $(r).find('#mm-saleDscPrc').text().trim() : $(r).find('#prcIsum').text().trim()).replace(',', '').replace('$', '').replace('US', '').trim(),
						title = $(r).find('#itemTitle').text().replace('Details about', '').trim();
						if ($(r).find('#icImg').attr('src')) {
							img = $(r).find('#icImg').attr('src').split('?')[0];
						}
						itemid = $(r).find('#descItemNumber').text();
					} else if (el.value.indexOf('amazon.') > 0) {
						price = $(r).find('#priceblock_dealprice').text().replace('$', '').trim(),
						title = $(r).find('#productTitle').text().trim();
						if ($(r).find('#icImg').attr('src')) {
							img = $(r).find('#landingImage').attr('src').split('?')[0];
						}
						itemid = el.value.match(/\/dp\/(.*)[\/|?]/i) ? el.value.match(/\/dp\/(.*)[\/|?]/i)[1] : '';
					}
                    $(r).find('#itemTitle').find('span').remove();
					
					$(el).parent().next().append($('<div/>', {
						class: 'pnThumb',
						html: img ? $('<img/>', {
							src: img
						}) : ''
					})).append($('<div/>', {
						class: 'pNewText',
						html: $('<div/>', {
							class: 'pnName',
							html: title
						})
					})).append($('<div/>', {
						class: 'itemId',
						html: itemid
					})).append($('<div/>', {
						class: 'pnPrice',
						html: price
					})).append($('<input/>', {
						type: 'number',
						class: 'npPrice',
						name: 'price',
						step: '0.001',
						min: '0',
						placeholder: 'Sale price'
					})).append($('<input/>', {
						type: 'text',
						class: 'npPrice',
						name: 'salename',
						placeholder: 'Sale name'
					}));
                    break;
            }
        }, 'html');
	}, */
	newPur: function(el) {
		$(el).parent().next().empty();
		$.get('/parser?lnk=' + el.value, function(r) {
            switch (r) {
                case 'no_responce':
                    alr.show({
                        class: 'alrDanger',
                        content:  lang[13],
                        delay: 2
                    });
                    break;

                default:
					var title = r.title, 
						img = r.image || '', 
						price = r.price, 
						itemid = r.code;

					$(el).parent().next().append($('<div/>', {
						class: 'pnThumb',
						html: img ? $('<img/>', {
							src: img
						}) : ''
					})).append($('<div/>', {
						class: 'pNewText',
						html: $('<div/>', {
							class: 'pnName',
							html: title
						})
					})).append($('<div/>', {
						class: 'itemId',
						html: itemid
					})).append($('<div/>', {
						class: 'pnPrice',
						html: price
					})).append($('<input/>', {
						type: 'number',
						class: 'npPrice',
						name: 'price',
						step: '0.001',
						min: r.sale_price,
						value: Math.ceil(r.sale_price),
						placeholder: 'Sale price',
						onchange: 'purchases.minPrice(this)'
					})).append($('<input/>', {
						type: 'text',
						class: 'npPrice',
						name: 'salename',
						placeholder: 'Sale name'
					}));
                    break;
            }
        }, 'json');
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
		data.append('field', 'purchase_info');
        loadBtn.start($(f));
        if (!$('input[name="purchase"]').data() && !form.find('input[name="new_purchase"]').data())
            err = lang[88];
        else if ($('input[name="purchase"]').data()) {
            data.append('value', JSON.stringify($('input[name="purchase"]').data() || {}));
			if (form.find('input[name="new_purchase"]').val()) {
				data.append('cid', cid);
				data.append('customer_id', $('#issue').attr('data-customer'));
				data.append('name', $('.pnName').text());
				data.append('photo', $('#addPurchase .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('itemID', $('.itemId').text());
				data.append('cprice', $('input[name="price"]').val() || 0);
				data.append('salename', $('input[name="salename"]').val());
				data.append('link', $('input[name="new_purchase"]').val());
				if ($('input[name="price"]').val() == 0)
					err = 'Price can not be 0';
				if (!$('input[name="salename"]').val())
					err = 'Sale name can not be empty';
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
                /* } else if (r == 'min_price') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Min sale price can not be less than $' + parseFloat(min_price(parseFloat($('.pnPrice').text().replace(/(\D*)/i, '').replace(',', '')))).toFixed(2),
                        delay: 2
                    }); */
                } else if (r.indexOf('min_price') >= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Min price for new purchase must be enought $' + r.split('_')[2],
                        delay: 2
                    });
                } else if (r == 'confirmed') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You can not delete purchases. Someones are already ordered',
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
        name = (type == 'stock' ? 'inventory' : 'service');
        mdl.open({
            id: 'addService',
            title: lang[84] + (devId ? lang[85] : lang[86]),
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup sfGroup fw',
					id: 'object',
					html: $('<label/>', {
						html: 'Store'
					})
				}).append($('<input/>', {
					type: 'hidden',
					name: 'object',
				})).append($('<ul/>'))
            }).append($('<div/>', {
                    class: 'iGroup sfGroup fw price' + (type == 'stock' ? ' bobject' : ''),
                    id: name,
                    html: $('<label/>', {
                        html: devId ? lang[82] : lang[83]
                    })
                }).append($('<input/>', {
                    type: 'hidden',
                    name: name
                })).append($('<ul/>'))).append('<div class="iGroup"><button onclick="addField('+id+', \'service\');" class="btn btnMdl" style="display: block;width: 100%;margin-left: 0px;">Additional field</button></div>').append(type == 'service' ? $('<div/>', {
				class: 'iGroup fw price bobject hdn reqP',
				id: 'inventory',
				html: $('<label/>', {
					html: 'Inventory'
				})
			}).append($('<ul/>', {
				class: 'hdn'
			})) : '').append(type == 'service' ? $('<div/>', {
				class: 'iGroup fw price hdn reqP bobject',
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
				oninput: 'issues.newPur(this)'
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
				$.post('/invoices/objects', {}, function (r) {
					if (r){
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
							s: true
						}, $('input[name="object"]'), issues.cbInv);
					}
				}, 'json');
				
				issues.cbInv();
				if (type == 'service') {
					$('input[name="' + name + '"]').data($('input[name="set_services"]').data());
				}
				if (type == 'stock') {
					$('input[name="' + name + '"]').data($('input[name="set_inventory"]').data());
				}
                
				
				if (type == 'service') {
					issues.reqParts();
				}
            }
        });
    },
	cbInv: function() {
		var name = ($('input[name="service"]').length ? 'service' : 'inventory');
		
		if (!$('#' + name + ' > ul').length) {
			$('#' + name + ' > .sfWrap').remove();
			$('#' + name).append($('<ul/>'));
		}
		
		$.post('/inventory/all', {
			type: ($('input[name="service"]').length ? 'service' : 'stock'),
			noCust: 1,
			nIds: Object.keys($('input[name="' + name + '"]').data()).join(','),
			oId: Object.keys($('input[name="object"]').data())[0]
		}, function(r) {
			if (r) {
				var items = '',
					lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '"' + ($('input[name="inventory"]').length ? ' data-object="' + v.object + '"' : '') + '>' + v.name + '</li>';
					lId = v.id;
				});
				
				$('#' + name + ' > ul').html(items).sForm({
					action: '/inventory/all',
					data: {
						type: ($('input[name="service"]').length ? 'service' : 'stock'),
						lId: lId,
						nIds: Object.keys($('input[name="' + name + '"]').data()).join(','),
						query: $('#' + name + ' > .sfWrap input').val() || '',
						oId: Object.keys($('input[name="object"]').data())[0]
					},
					all: false,
					select: $('input[name="' + name + '"]').data(),
					link: 'inventory/' + ($('input[name="service"]').length ? 'service/edit' : 'view')
				}, $('input[name="' + name + '"]'), ($('input[name="service"]').length ? issues.reqParts : ''));
			}
		}, 'json');
		
		if (name == 'service') {
			if (!$('#inventory > ul').length) {
				$('#inventory > .sfWrap').remove();
				$('#inventory').append($('<ul/>'));
			}
			$.post('/inventory/all', {
				type: 'stock',
				noCust: 1,
				nIds: Object.keys($('input[name="set_inventory"]').data()).join(','),
				oId: Object.keys($('input[name="object"]').data())[0]
			}, function(r) {
				if (r) {
					var items = '',
						lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-object="' + v.object + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#inventory > ul').html(items).sForm({
						action: '/inventory/all',
						data: {
							type: 'stock',
							lId: lId,
							nIds: Object.keys($('input[name="set_inventory"]').data()).join(','),
							query: $('#inventory > .sfWrap input').val() || '',
							oId: Object.keys($('input[name="object"]').data())[0]
						},
						all: false,
						select: $('input[name="set_inventory"]').data(),
						link: 'inventory/view'
					}, $('input[name="set_inventory"]'));
				}
			}, 'json');
			
			$.post('/purchases/all', {
				nIds: Object.keys($('input[name="purchase"]').data()).join(','),
				object_id: Object.keys($('input[name="object"]').data())[0]
			}, function(r) {
				if (!$('#purchase > ul').length) {
					$('#purchase > .sfWrap').remove();
					$('#purchase').append($('<ul/>'));
				}
				if (r) {
					var items = '',
						lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-object="' + v.object + '">' + v.name + '</li>';
						lId = v.id;
					});
					$('#purchase > ul').html(items).sForm({
						action: '/purchases/all',
						data: {
							nIds: Object.keys($('input[name="purchase"]').data() || {}).join(','),
							query: $('#purchase > .sfWrap input').val() || '',
							object_id: Object.keys($('input[name="object"]').data())[0]
						},
						all: false,
						select: $('input[name="purchase"]').data(),
						link: 'purchases/edit'
					}, $('input[name="purchase"]'));
				}
			}, 'json');
		}
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
		data.append('field', (type == 'stock' ? 'inventory_info' : 'service_info'));
		
        loadBtn.start($(f));
        if (!form.find('input[name="' + name + '"]').data())
            err = lang[81];
        else { 
			data.append('value', JSON.stringify(form.find('input[name="' + name + '"]').data()));
            data.append('inventory', JSON.stringify($('input[name="set_inventory"]').data()));
			data.append('purchases', JSON.stringify($('input[name="purchase"]').data()));
			if ($('input[name="new_purchase"]').val()) {
				data.append('cid', cid);
				data.append('name', $('.pnName').text());
				data.append('photo', $('#addService .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('itemID', $('.itemId').text());
				data.append('cprice', $('input[name="price"]').val() || 0);
				data.append('link', $('input[name="new_purchase"]').val());
				data.append('salename', $('input[name="salename"]').val());
				if ($('input[name="price"]').val() == 0)
					err = 'Price can not be 0';
				if (!$('input[name="salename"]').val())
					err = 'Sale name can not be empty';
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
                        content: 'Min sale price can not be less than $' + parseFloat(min_price(parseFloat($('.pnPrice').text().replace(/(\D*)/i, '').replace(',', '')))).toFixed(2),
                        delay: 2
                    });
				} else if (r.indexOf('min_price') >= 0) {
					alr.show({
                        class: 'alrDanger',
                        content: 'Min price for new purchase must be enought $' + r.split('_')[2],
                        delay: 2
                    });
				} else if (r == 'req') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You must enough one inventory or part to use this services',
                        delay: 2
                    });
				} else if (r == 'confirmed') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You can not delete purchases. Someones are already ordered',
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
				field: (type == 'stock' ? 'inventory_info' : 'service_info'),
				value: id,
			},
			info = $('input[name="' + (type == 'stock' ? 'set_inventory' : 'set_services') + '"]').data();
		delete info[id];
		data.value = JSON.stringify(info);
		
        $.post('/issues/del_field', data, function(r) {
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
	sendPQuantity: function(el, id, issue) {
		var data = {
			id: id,
			issue_id: issue
		}, err = null;
		
		if ($(el).val() < 1) 
			err = 'Quoantity is incorrect';
		else
			data.quantity = $(el).val()
		
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			if ($(el).val() == parseFloat($(el).attr('data-quantity'))) {				
				$('#miniService_' + id).find('.msPrice').text((parseFloat($(el).attr('price')) + parseFloat($(el).attr('inv_price'))) * parseInt($(el).val()));
				$('#service_' + id).find('.servPrice').text((parseFloat($(el).attr('price')) + parseFloat($(el).attr('inv_price'))) * parseInt($(el).val()));
				
				$('#miniService_' + id).find('.servQuantity').attr('ondblclick',  'ondblclick="issues.serviceQuantity(this, \''+id+'\', '+issue+', '+$(el).attr('quantity')+');"').html('<span class="thShort">Quantity: </span>'+$(el).attr('quantity'));
				$('#service_' + id).find('.servQuantity').attr('ondblclick',  'ondblclick="issues.serviceQuantity(this, \''+id+'\', '+issue+', '+$(el).attr('quantity')+');"').html('<span class="thShort">Quantity: </span>'+$(el).attr('quantity'));
				total.fTotal();
			} else {
				$.post('/issues/send_quantity', data, function(r) {
					if (r == 'OK') {
						var info = $('input[name="set_services"]').data();
						info[id].quantity = $(el).val();
						$('input[name="set_services"]').data(info);
						
						$('#miniService_' + id).find('.msPrice').text((parseFloat($(el).attr('price')) + parseFloat($(el).attr('inv_price'))) * parseInt($(el).val()));
						$('#service_' + id).find('.servPrice').text((parseFloat($(el).attr('price')) + parseFloat($(el).attr('inv_price'))) * parseInt($(el).val()));
						
						$('#miniService_' + id).find('.servQuantity').attr('ondblclick',  'ondblclick="issues.serviceQuantity(this, \''+id+'\', '+issue+', '+$(el).attr('quantity')+');"').html('<span class="thShort">Quantity: </span>'+$(el).val());
						$('#service_' + id).find('.servQuantity').attr('ondblclick',  'ondblclick="issues.serviceQuantity(this, \''+id+'\', '+issue+', '+$(el).attr('quantity')+');"').html('<span class="thShort">Quantity: </span>'+$(el).val());
						total.fTotal();
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
	},
	serviceQuantity: function(el, id, issue, quantity) {
		$(el).attr('ondblclick', '').html($('<input/>', {
            type: 'number',
            name: 'pQuantity',
            step: '1',
            min: '1',
			quantity: $(el).attr('data-quantity'),
			price: parseFloat($(el).next().find('.qPrice').attr('data-price')),
			inv_price: parseFloat($(el).next().find('.qPrice').attr('data-inv_price')),
            value: parseFloat($(el).attr('data-quantity')),
			onblur: 'issues.sendPQuantity(this, \'' + id + '\', ' + issue + ')'
        }));
		$('input[name="pQuantity"]').focus();
	},
    pPrice: function(el, id, iss, type) {
        $(el).attr('ondblclick', '').html($('<input/>', {
            type: 'number',
            name: 'pPrice',
            step: '0.01',
            min: '0.00',
			style: 'position: absolute;margin-left: -15px;',
			quantity: $(el).parent().prev().attr('data-quantity'),
			old: parseFloat($(el).attr('data-price')),
			inv_price: $(el).attr('data-inv_price') ? parseFloat($(el).attr('data-inv_price')) : 0,
            value: parseFloat($(el).attr('data-price')).toFixed(2),
			onblur: 'issues.sendPPrice(this, \'' + id + '\', ' + iss + ', \'' + type + '\')'
        }));
		$('input[name="pPrice"]').focus();
        //$(el).removeClass('fa-pencil').addClass('fa-check').attr('onclick', '');
    },
    sendPPrice: function(el, id, iss, type) {
        var data = {
                id: iss,
                value: 0,
                pur: id,
				type: type,
				old: $('input[name="pPrice"]').attr('old')
            },
            err = null;
        if (!$('input[name="pPrice"]').val()) {
            err = lang[76];
            $('input[name="pPrice"]').css('border-color', '#f00');
		//} else if (parseFloat($('input[name="pPrice"]').val()) < parseFloat($('input[name="pPrice"]').attr('old'))) {
			//err = 'You can only increase price';
		} else {
			var inp_type = ((type == 'purchase_info') ? 'purchase' : ((type == 'inventory_info') ? 'set_inventory' : 'set_services'));
			var info = $('input[name="' + inp_type + '"]').data();
			info[id].price = parseFloat($('input[name="pPrice"]').val()) + ($('input[name="pPrice"]').attr('inv_price') ? parseFloat($('input[name="pPrice"]').attr('inv_price')) : 0);
			$('input[name="' + inp_type + '"]').data(info);
            data.value = JSON.stringify(info);
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
			if ($('input[name="pPrice"]').val() == $('input[name="pPrice"]').attr('old')) {
				$(el).parent().text((parseFloat($('input[name="pPrice"]').val()) + parseFloat($('input[name="pPrice"]').attr('inv_price'))) * parseFloat($('input[name="pPrice"]').attr('quantity'))).attr('ondblclick', 'issues.pPrice(this, \'' + id + '\', ' + iss  + ', \'' + type + '\')');
			} else {
				$.post('/issues/send_price', data, function(r) {
					loadBtn.stop($(event.target));
					if (!r) {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					} else if (r == 'less') {
						alr.show({
							class: 'alrDanger',
							content: 'You can only increase price',
							delay: 2
						});
					} else {
						alr.show({
							class: 'alrSuccess',
							content: lang[75],
							delay: 2
						});
						
						if (type == 'service_info') {
							$('#miniService_' + id).find('.msPrice').text($('input[name="pPrice"]').val()).attr('ondblclick', 'issues.pPrice(this, \'' + id + '\', ' + iss  + ', \'' + type + '\')');
							$('#service_' + id).find('.servPrice').text($('input[name="pPrice"]').val()).attr('ondblclick', 'issues.pPrice(this, \'' + id + '\', ' + iss  + ', \'' + type + '\')');
						} else 
							$(el).parent().text($('input[name="pPrice"]').val()).attr('ondblclick', 'issues.pPrice(this, \'' + id + '\', ' + iss  + ', \'' + type + '\')');
						total.fTotal();
					}
				});
			}
        }
    },
    delPur: function(id, issue) {
        var data = {
            id: issue,
            field: 'purchase_info',
            value: id,
			value_id: id,
            del: true
		}, purchases = $('input[name="purchase"]').data();
		delete purchases[id];
		data.value = JSON.stringify(purchases);
        $.post('/issues/del_field', data, function(r) {
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
            } else if (r == 'confirmed') {
				alr.show({
                    class: 'alrDanger',
                    content: 'You can not delete this purchase, it\'s already ordered',
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
};