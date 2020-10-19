// Cash
var cash = {
	cpage: 0,
	amount_cash: 0,
	amount_credit: 0,
	amount_hands: 0,
	amount_system: 0,
	show: 1,
	openCash: function(t, a) {
		cash.amount_cash = 0;
		cash.amount_credit = 0;
		cash.amount_hands = 0;
		cash.amount_system = 0;
		$.post('/cash/open', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			action: a,
			type: 'cash'
		}, function(r) {
			if (r.amount == 'DONE') {
				alr.show({
					class: 'alrDanger',
					content: lang[148] + (a == 'open' ? lang[149] : lang[150]) + lang[147],
					delay: 2
				});
			} else if (r.amount == 'ERR') {
				alr.show({
					class: 'alrDanger',
					content: lang[39] + (a == 'open' ? lang[40] : lang[41]),
					delay: 2
				});
			} else if (r.amount == 'no_action') {
				alr.show({
					class: 'alrDanger',
					content: 'Please, reload page and select action',
					delay: 2
				});
			} else {
				cash.amount_cash = r.amount;
				cash.amount_system = r.amount;
				cash.show = r.show;
				mdl.open({
				id: 'openCash',
				title: (a == 'open' ? lang[38] : lang[151]),
				content: $('<div/>', {
					class: 'mdlCash',
					html: $('<div/>', {
						class: 'sumCash',
						html: (r.show == 1 ? ('Enter Total Drawer Amount<br>$ ' + r.amount) : 'Enter Total Drawer Amount')
					})
				}).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Amount'
					})
				}).append($('<input/>', {
					type: 'number',
					min: '0',
					step: '0.1',
					name: 'hands'
				}))).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn  btnSubmit ac',
						html: lang[30],
						onclick: a == 'close' ? 'cash.acceptClose(\'cash\', \'' + a + '\', 0, \'' + (r == 'NO' ? '0' : r) + '\');' : 'cash.accept(\'cash\', \'' + a + '\');'
					})
				}).append($('<button/>', {
					type: 'button',
					class: 'btn btnSubmit dc',
					html: lang[29],
					onclick: 'mdl.close();'//'cash.confirmDicline(\'cash\', \'' + a + '\', \'' + (r == 'NO' ? '0' : r) + '\');' //'cash.dicline(\'cash\', \'' + a + '\', \'' + (r == 'NO' ? '0' : r) + '\');'
				})))
				});
			}
		}, 'json');
	}, confirmDicline: function(t, a, d) {
		mdl.close('openCash');
		mdl.open({
			id: 'decOpenCash',
			title: (a == 'open' ? lang[38] : lang[151]),
			content: $('<div/>', {
				class: 'mdlCash',
				html: $('<div/>', {
					class: 'sumCash',
					html: 'Are you sure you want to ' + a + ' cash?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn  btnSubmit ac',
					html: lang[30],
					onclick: 'cash.dicline(\'cash\', \'' + a + '\', \'' + d + '\');'
				})
			}).append($('<button/>', {
					type: 'button',
					class: 'btn btnSubmit dc',
					html: 'Recalculate',
					onclick: 'mdl.close();'
				})))
		});
	}, accept: function(t, a, d, out) {
		if (parseFloat(t == 'cash' ? cash.amount_cash : cash.amount_credit) != parseFloat($('input[name="hands"]').val()) && a == 'open') {
			cash.amount_hands = $('input[name="hands"]').val();
			cash.amount_cash = $('input[name="hands"]').val();
			cash.amount_credit = $('input[name="hands"]').val();
			mdl.close();
			cash.dicline(t, a, d);
		} else {
			var data = {}, err = null;
			if (a == 'close') {
				$('.mdl input').css('border', '1px solid #ddd');
				if (!$('input[name="out"]').val() && out == 'out') {
					err = 'Please, enter value';
					$('input[name="out"]').css('border', '1px solid #f00');
				} else if (!$('input[name="leave"]').val() && !out) {
					err = 'Please, enter value';
					$('input[name="leave"]').css('border', '1px solid #f00');
				} else {
					//data.amount = $('input[name="leave"]').val();
					data.system = cash.amount_system;
					if (out == 'out')
						data.out = $('input[name="out"]').val();
					else
						data.amount = $('input[name="leave"]').val();
				}
			} else {
				//data.amount = (t == 'cash' ? cash.amount_cash : cash.amount_credit);
				data.amount = $('input[name="hands"]').val();
				data.system = cash.amount_system;
			}
			if ($('input[name="hands"]').length) cash.amount_hands = $('input[name="hands"]').val();
			data.lack = cash.amount_system - cash.amount_hands;
			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/cash/send', {
					type: t, 
					action: 'accept', 
					acType: a, 
					object: Object.keys($('input[name="object"]').data()).join(','),
					data: data,
					d: d || 0 //dicline if d
				}, function(r) {
					if (r) {
						mdl.close();
						if (a == 'open' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'close\')').html(lang[34]);
							var crd = setTimeout(function() {
								cash.openCredit();
								clearTimeout(crd);
							}, 2000);
						} else if (a == 'close' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'open\')').html(lang[33]);
							if (out == 'out') var crd = setTimeout(function() {
								cash.leave(t, a, r.id);
								clearTimeout(crd);
							}, 500);
						}
						
						if ($('#cashHistory .noContent')) $('.noContent').remove();
						if (!d) {
							if (t == 'cash') {
								$('#cashHistory .tBody').prepend($('<div/>', {
									class: 'tr' + (Math.abs(data.lack) > 0.001 ? ' dicline' : ''),
									id: 'stat_' + r.id,
									onclick: (d ? ' cash.adminAccept(' + r.id + ');' : ''),
									html: $('<div/>', {
										class: 'td wIds',
										html: $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name
									})
								}).append($('<div/>', {
									class: 'td',
									html: 'cash<br>credit'
								})).append($('<div/>', {
									class: 'td',
									html: (cash.show == 1 ? '$ ' + parseFloat(cash.amount_system).toFixed(2) : ' --- ') + '<br><span class="creditArea"></span>'
								})).append($('<div/>', {
									class: 'td',
									html: (cash.show == 1 ? '$ ' + parseFloat((cash.amount_hands || 0)).toFixed(2) : ' --- ') + (data.lack > 0.001 ? '<span class="minLack">-' + parseFloat(data.lack).toFixed(2) + '</span>' : (data.lack < -0.001 ? '<span class="plusLack">' + parseFloat(data.lack).toFixed(2) + '</span>' : '')) + '<br><span class="creditArea"></span>'
								})).append($('<div/>', {
									class: 'td',
									html: (cash.show == 1 ? '$ ' + parseFloat((data.amount || 0)).toFixed(2) : ' --- ')
								})).append($('<div/>', {
									class: 'td',
									html: lang[a]
								})).append($('#drops').length ? $('<div/>', {
									class: 'td',
									html: (a == 'close' ? '$' + data.out : '---')
								}) : '').append($('<div/>', {
									class: 'td',
									html: r.date
								})).append($('<div/>', {
									class: 'td',
									html: r.user
								})).append($('<div/>', {
									class: 'td',
									style: 'width: 50px',
									html: $('<a/>', {
										href: a == 'close' ? '/invoices/history/?type=' + t + '&date=' + r.ddate + '&object=' + Object.keys($('input[name="object"]').data()).join(',') + '&object_name=' + $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name : '',
										onclick: 'Page.get(this.href); return false;',
										html: 'Invoices'
									})
								})));
							} else if (t == 'credit' && a == 'open') {
								$('#cashHistory .tBody .tr:first-child .td:nth-child(3) .creditArea').html((cash.show == 1 ? '$ ' + parseFloat(cash.amount_system).toFixed(2) : ' --- '));
								$('#cashHistory .tBody .tr:first-child .td:nth-child(4) .creditArea').html((cash.show == 1 ? '$ ' + parseFloat((cash.amount_hands || 0)).toFixed(2) : ' --- ') + (data.lack > 0.001 ? '<span class="minLack">-' + parseFloat(data.lack).toFixed(2) + '</span>' : (data.lack < -0.001 ? '<span class="plusLack">' + parseFloat(data.lack).toFixed(2) + '</span>' : '')));
							}
						} else {
							$('#stat_' + d + ' .td:nth-child(5)').html('$' + data.amount);
						}
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				}, 'json');
			}
		}
	}, dicline: function(t, a, s) {
		mdl.close();
		/* mdl.open({
			id: 'mdlDicline',
			title: lang[36],
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[35]
					})
				}).append($('<input/>', {
					type: 'number',
					min: 0,
					step: 0.1,
					placeholder: 0,
					name: 'amount',
					value: cash.amount_hands
				}))
			}).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[27]
					})
				}).append($('<textarea/>', {
					placeholder: lang[27],
					name: 'comment'
				}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: lang[28],
					onclick: 'cash.sendDicline(\'' + t + '\', \'' + a + '\', \'' + s + '\');'
				})
			}))
		}); */
		
		mdl.open({
			id: 'mdlDicline',
			title: lang[36],
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'amNot',
					html: $('<div/>', {
						html: 'Are you sure?'
					})
			})}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn  btnSubmit ac',
						html: lang[30],
						onclick: 'cash.sendDicline(\'' + t + '\', \'' + a + '\'' + (s ? ', ' + s : '') + ');'
					})
				}).append($('<button/>', {
						type: 'button',
						class: 'btn btnSubmit dc',
						html: 'Recalculate',
						onclick: 'mdl.close();'
					})))
		});
	}, sendDicline: function(t, a, s) {
		var data = {}, err = null;
		/* $('.mdl input, .mdl textarea').css('border', '1px solid #ddd');
		if (!$('input[name="amount"]').val()) {
			err = 'leave can not be empty';
			$('input[name="amount"]').css('border', '1px solid #f00');
		} else if (!$('textarea[name="comment"]').val()) {
			err = 'Comment can not be empty';
			$('textarea[name="comment"]').css('border', '1px solid #f00');
		} else { */
			data.amount = (t == 'cash' ? cash.amount_cash : cash.amount_credit);
			data.system = cash.amount_system;
			data.lack = cash.amount_system - (t == 'cash' ? cash.amount_cash : cash.amount_credit);
		//}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/cash/send', {
					type: t, 
					action: 'dicline', 
					acType: a, 
					object: Object.keys($('input[name="object"]').data()).join(','),
					data: data
				}, function(r) {
					if (r.id > 0) {
						mdl.close();
						if (a == 'close') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'open\')').html(lang[33]);
							cash.acceptClose(t, a, r.id, 0, 1);
						} else if (a == 'open' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'close\')').html(lang[34]);
							var crd = setTimeout(function() {
								cash.openCredit();
								clearTimeout(crd);
							}, 2000);
						}
						if ($('#cashHistory .noContent')) $('.noContent').remove();
						$('#cashHistory .tBody').prepend($('<div/>', {
							class: 'tr dicline',
							id: 'stat_' + r.id,
							onclick: 'cash.adminAccept(' + r.id + ');',
							html: $('<div/>', {
								class: 'td wIds',
								html: $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name
							})
						}).append($('<div/>', {
							class: 'td',
							html: 'cash'
						})).append($('<div/>', {
							class: 'td',
							html: (cash.show == 1 ? '$ ' + data.system : ' --- ')
						})).append($('<div/>', {
							class: 'td',
							html: (cash.show == 1 ? '$ ' + (data.system - data.lack) : ' --- ') + (data.lack > 0.001 ? '<span class="minLack">-' + parseFloat(data.lack).toFixed(2) + '</span>' : (data.lack < -0.001 ? '<span class="plusLack">' + parseFloat(data.lack * -1).toFixed(2) + '</span>' : ''))
						})).append($('<div/>', {
							class: 'td',
							html: (cash.show == 1 ? '$ ' + (data.amount || 0) : ' --- ')
						})).append($('<div/>', {
							class: 'td',
							html: lang[a]
						})).append($('#drops').length ? $('<div/>', {
							class: 'td',
							html: (a == 'close' ? '$0' : '---')
						}) : '').append($('<div/>', {
							class: 'td',
							html: r.date
						})).append($('<div/>', {
							class: 'td',
							html: r.user
						})).append($('<div/>', {
							class: 'td',
							style: 'width: 50px',
							html: a == 'close' ? $('<a/>', {
								href: '/invoices/history/?type=' + t + '&date=' + r.ddate + '&object=' + Object.keys($('input[name="object"]').data()).join(',') + '&object_name=' + $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name,
								onclick: 'Page.get(this.href); return false;',
								html: 'Invoices'
							}) : ''
						})));
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
			}, 'json');
		}
	}, change: function(e) {
		$('input[name="out"]').val(parseFloat($('input[name="out"]').attr('data-value')) - $(e).val());
	}, acceptClose: function(t, a, d, s, ac) {
		cash.amount_cash = parseFloat($('input[name="hands"]').val());
		cash.amount_hands = parseFloat($('input[name="hands"]').val());
		if (parseFloat($('input[name="hands"]').val()) != cash.amount_cash && !ac) {
			cash.dicline(t, a, d);
		} else {
			mdl.close();
			mdl.open({
				id: 'mdlClose',
				title: 'Input cash',
				content: $('<div/>', {
					class: 'diclineForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Drop Amount'
						})
					}).append($('<input/>', {
						type: 'number',
						min: 0,
						step: 0.1,
						placeholder: 0,
						name: 'out'
					}))
				}).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						html: lang[28],
						onclick: 'cash.accept(\'cash\', \'' + a + '\', ' + (
							d ? d : 0
						) + ', \'out\');'
					})
				}))
			});
		}
	}, leave: function(t, a, d) {
		console.log(d);
		mdl.open({
			id: 'mdlClose',
			title: 'Input cash',
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[32]
					})
				}).append($('<input/>', {
					type: 'number',
					min: 0,
					step: 0.1,
					placeholder: 0,
					name: 'leave'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: lang[28],
					onclick: 'cash.accept(\'cash\', \'' + a + '\', ' + (
						d ? d : 0
					) + ');'
				})
			}))
		});
	}, openCredit: function() {
		cash.amount_cash = 0;
		cash.amount_credit = 0;
		cash.amount_hands = 0;
		cash.amount_system = 0;
		$.post('/cash/open', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			action: 'open',
			type: 'credit'
		}, function(r) {
			if (r) {
				if (r.amount == 'DONE') {
					alr.show({
						class: 'alrDanger',
						content: 'Yu can not open cash twice a day',
						delay: 2
					});
				} else {
					cash.amount_system = r.amount;
					mdl.open({
						id: 'openCredit',
						title: lang[31],
						content: $('<div/>', {
							class: 'mdlCredit',
							html: $('<div/>', {
								class: 'sumCash',
								html: (cash.show == 1 ? 'Credit Settlement<br>$ ' + r.amount : 'Credit Settlement')
							})
						}).append($('<div/>', {
							class: 'iGroup',
							html: $('<label/>', {
								html: 'Amount'
							})
						}).append($('<input/>', {
							type: 'number',
							min: '0',
							step: '0.1',
							name: 'hands'
						}))).append($('<div/>', {
							class: 'cashGroup',
							html: $('<button/>', {
								type: 'button',
								class: 'btn  btnSubmit ac',
								html: lang[30],
								onclick: 'cash.accept(\'credit\', \'open\');'
							})
						}).append($('<button/>', {
								type: 'button',
								class: 'btn btnSubmit dc',
								html: lang[29],
								onclick: 'cash.dicline(\'credit\', \'open\', ' + (r == 'NO' ? '0' : r) + ');'
							})))
					});
				}
			}
		}, 'json');
	}, adminAccept: function(id) {
		$.post('/cash/getComment', {
			id: id
		}, function(r) {
			if (r) {
				mdl.open({
					id: 'admAccept',
					title: 'Leave a comment',
					content: $('<div/>', {
						class: 'comForm',
						html: $('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: r.user
							})
						}).append($('<div/>', {
							class: 'userComment',
							html: r.content
						}))
					}).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: lang[27]
							})
						}).append($('<textarea/>', {
							name: 'comment'
						}))).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							html: lang[28],
							onclick: 'cash.sendAdm(' + id + ');'
						})
					}))
				})
			}
		}, 'json');
	}, sendAdm: function(id) {
		var data = {
			id: id
		}, err = null;
		$('textarea[name="comment"]').css('border', '1px solid #ddd');
		if (!$('textarea[name="comment"]').val()) {
			err = lang[26];
			$('textarea[name="comment"]').css('border', '1px solid #f00');
		} else {
			data.comment = $('textarea[name="comment"]').val();
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/cash/sendCom', data, function(r) {
				if (r == 'OK') {
					$('#stat_' + id).removeClass('dicline').find('.td').first().prepend(' <span class="fa fa-check"></span>');
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
	}, selectObject: function() {
		page = 0;
		$.post('/cash', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			page: page,
			query: '',
			type: $('select[name="type"]').val(),
			action: $('select[name="action"]').val(),
			status: $('select[name="status"]').val(),
			staff: Object.keys($('input[name="staff"]').data()).join(','),
			date_start: $('#fCalendar input[name="date"]').val(),
			date_end: $('#fCalendar input[name="fDate"]').val()
		}, function(r) {
			if (r) {
				cash.amount_cash = 0;
				cash.amount_credit = 0;
				cash.show = 1;
				$('#cashHistory').removeClass('hdn').find('.tBody').html(r.content);
				$('.tbl + button').addClass(r.more);
				if (page == 0) {
					$('.btnOCash').removeAttr('disabled').attr('onclick', 'cash.openCash(\'cash\', \'' + (r.status || 'open') + '\')').html((r.status || 'Open ') + ' cash');
				}
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json');
	}, doload: function(e) {
		page += 1;
		$.post(location.pathname, {
			id: Object.keys($('input[name="object"]').data()).join(',') || 0,
			query: query,
			page: page
		}, function(r) {
			$('.tBody').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	}, comments: function(id) {
		cash.cpage = 0;
		$.post('/cash/comments', {id: id}, function(r) {
			mdl.open({
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
					html: 'Load commetns'
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
						onclick: 'cash.sendComment(' + id + ');',
						html: 'Send'
					})
				})))
			});
		}, 'json');
	}, sendComment(id) {
		if (!$('textarea[name="comment"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Comment can not be empty',
				delay: 2
			});
		} else {
			$.post('/cash/send_comment', {
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
		cash.cpage += 1;
		$.post('/cash/comments', {
			id: id, 
			page: cash.cpage
		}, function(r) {
			$('.comments').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	}
}