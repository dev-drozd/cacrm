/**
 * @Appointment Users
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/

var Users = {
	concat_id: function(did, id, el) {
		$.post('/users/concat', {
			ids: did + ',' + id,
			main: id
		}, function(r) {
			if (r == 'OK')
				$(el).parent().parent().remove();
			else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	concat: function(e, id) {
		var ids = [], 
			main = '', 
			err = null;
		$(e).parent().parent().find('.dublicate').each(function() {
			if (!$(this).hasClass('none'))
				ids.push($(this).attr('data-id'));
		});
		
		if (ids.indexOf($('input[name="dublicates[' + id + ']"]:checked').val()) >= 0)
			main = $('input[name="dublicates[' + id + ']"]:checked').val();
		
		if (!main.length) 
			err = 'Select main profile';
		else if (ids.length < 2)
			err = 'No profiles for concatenating';
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/users/concat', {
				ids: ids,
				main: main
			}, function(r) {
				if (r == 'OK')
					$(e).parent().parent().addClass('done').find('.dub-button').remove();
				else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	},
	dontConcat: function(e) {
		$(e).parent().parent().toggleClass('none');
	},
	dublicateType: function(t) {
		page = 0;
		$.post('/users/dublicates', {
			page: 0,
			type: t
		}, function(r) {
			$('.userList.noTbl').html(r.content);
			$('#page').radio();
		}, 'json');
	},
    spage: 0,
	confirmApp: function(el, id, type) {
		$.post('/users/confirm_app', {
			id: id,
			type: type
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Appointment was successfully ' + (type ? 'confirmed' : 'diclined'),
					delay: 2
				});
				$(el).parent().remove();
			} else if (r == 'NO_ACC') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to this action',
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
	addApp: function(f, event, user_id) {
		event.preventDefault();
		loadBtn.start($(f).find('button'));
		var data = {
			user_id: user_id
		}, err = null;
		
		if (!$('input[name="sdate"]').val()) 
			err = 'Enter date';
		else if (!$('input[name="time"]').val())
			err = 'Enter time';
		else if (!Object.keys($('input[name="object"]').data())[0])
			err = 'Select store';
		else if (new Date($('input[name="sdate"]').val() + ' ' + $('input[name="time"]').val()) < new Date())
			err = 'Date can not be less from today\'s date';
		else {
			data.date = $('input[name="sdate"]').val();
			data.time = $('input[name="time"]').val();
			data.object = Object.keys($('input[name="object"]').data())[0];
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(f).find('button'));
		} else {
			$.post('/users/send_app', data, function(r) {
				loadBtn.stop($(f).find('button'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Appointment was successfully added',
						delay: 2
					});
					Page.get('/users/view/' + user_id);
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
	makeSuspention: function(id) {
		$.post('/users/is_suspention', {
			id: id
		}, function(r) {
			if (r.res == 'OK') {
/* 				mdl.open({
					id: 'makeSuspention',
					title: 'Make suspention',
					content: $('<div/>', {
						html: $('<div/>', {
							class: 'iGroup',
							html: $('<label/>', {
								html: 'Suspention'
							})
						}).append($('<select/>', {
							name: 'suspention',
							html: r.suspentions
						}))
					}).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Comment'
						})
					}).append($('<textarea/>', {
							name: 'comment'
					}))).append(
						r.third ? $('<div/>', {
							class: 'sumCash',
							html: 'This is third write up. User will get suspention'
						}) : ''
					).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							class: 'btn btnSubmit',
							onclick: 'Users.sendSuspention(this, ' + id + ', ' + r.third + ')',
							html: 'Send'
						})
					})),
					cb: function() {
						$('#page').select();
					}
				}); */
			} else if (r == 'no_acc') {
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
		}, 'json');
	},
	sendSuspention: function(el, id, third) {
		$.post('/users/send_suspention', {
			id: id,
			suspention: $('select[name="suspention"]').val(),
			comment: $('textarea[name="comment"]').val(),
			third: third
		}, function(r) {
			loadBtn.stop($(this));
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'You suspention was successfully sent',
					delay: 2
				});
				mdl.close();
			} else if (r == 'no_acc') {
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
	openWriteup: function(id, name) {
/* 		mdl.open({
			id: 'openStatus',
			title: (id ? 'Edit' : 'Open') + ' write up',
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
					onclick: 'Users.sendWriteup(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		}); */
    },
    sendWriteup: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = lang[16];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else
            data.name = f.find('input[name="name"]').val();
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/users/send_writeup', data, function(r) {
                loadBtn.stop($(this));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else if (r == 'no_acc') {
					alr.show({
                        class: 'alrDanger',
                        content: 'You are not allowed to do this',
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
    delWriteup: function(id) {
        if (id) {
            $.post('/users/del_writeup', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#writeup_' + id).remove();
				else if (r == 'no_acc') {
					alr.show({
                        class: 'alrDanger',
                        content: 'You are not allowed to do this',
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
        } else
            $(el).parents('.sUser').remove();
    },
	dashConfirmOnsite: function(id) {
		$.post('/users/dash_confirm_onsite', {
			id: id
		}, function(r) {
			if(parseInt(r) > 0) {
				alr.show({
					class: 'alrSuccess',
					content: 'The service was successfully confirmed',
					delay: 2
				});
				$('#onsite_unconf_' + id).remove();
				invoices.send_mail(r);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	dashDeleteOnsite: function(id) {
		$.post('/users/del_onsite', {
			id: id,
			dash: 1
		}, function(r) {
			if(r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'The service was successfully deleted',
					delay: 2
				});
				$('#onsite_unconf_' + id).remove();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	sendEditOnsite: function (id, el) { 
        $.post('/users/onsite_update_date', {
            id: id,
            date_end: $('input[name="date_end"]').val(),
        }, function (r) { 
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: 'Onsite service was successfully updated',
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
    },
	newOnsite: function() {
		$.post('/users/all', {gId: 5}, function(cr) {
            var citems = '', clId = 0;
            if (cr.list) {
                $.each(cr.list, function(ci, cv) {
                    citems += '<li data-value="' + cv.id + '">' + cv.name + '</li>';
                    clId = cv.id;
                });
            }
			$.post('/users/all', {staff: 1}, function(ur) {
				var uitems = '', ulId = 0;
				if (ur.list) {
					$.each(ur.list, function(ui, uv) {
						uitems += '<li data-value="' + uv.id + '">' + uv.name + '</li>';
						ulId = uv.id;
					});
				}

				$.post('/users/objects', {}, function(or) {
					var oitems = '', olId = 0;
					if (or.list) {
						$.each(or.list, function(oi, ov) {
							oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
							olId = ov.id;
						});
					}
/* 					mdl.open({
						id: 'addOnsite',
						title: 'On site service',
						content: $('<div/>', {
							class: 'uForm',
							html: $('<div/>', {
								class: 'iGroup fw',
								id: 'customer',
								html: $('<label/>', {
									html: 'Customer'
								})
							}).append($('<input/>', {
								type: 'hidden',
								name: 'customer'
							})).append($('<ul/>', {
								class: 'hdn',
								html: citems
							}))
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
							class: 'hdn',
							html: oitems
						}))).append($('<div/>', {
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
							onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
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
							onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
						})).append($('<div/>', {
							id: 'eCalendar'
						}))).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Time'
							})
						}).append($('<input/>', {
							type: 'time',
							name: 'time'
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
							class: 'hdn',
							html: uitems
						}))).append($('<div/>', {
							class: 'sGroup',
							html: $('<button/>', {
								type: 'button',
								class: 'btn btnSubmit',
								onclick: 'Users.sendService(this);',
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

							$('#object > ul').html(oitems).sForm({
								action: '/users/objects',
								data: {
									lId: olId,
									query: $('#object > .sfWrap input').val() || ''
								},
								all: false,
								select: $('input[name="object"]').data(),
								s: true,
								link: 'objects/edit'
							}, $('input[name="object"]'), Users.getOnsite);
							
							Users.getOnsite();

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
							
							$('#customer > ul').html(citems).sForm({
								action: '/users/all',
								data: {
									lId: clId,
									query: $('#customer > .sfWrap input').val() || '',
									gId: 5
								},
								all: false,
								select: $('input[name="customer"]').data(),
								s: true,
								link: 'users/view'
							}, $('input[name="customer"]'));
						}
					}); */
				}, 'json');
			}, 'json'); 
		}, 'json'); 
	},
	editOnsite: function(id) {
		date = $('#onsite_' + id + ' .dateEnd').html();
		if (date.indexOf('inf')) 
			date = '';
		else 
			date = date.split('-');
/* 		mdl.open({
			id: 'editOnsite',
			title: 'On site service',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup fw dGroup',
					html: $('<label/>', {
						html: 'End date'
					})
				}).append($('<input/>', {
					type: 'text',
					class: 'cl',
					name: 'date_end',
					onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
				})).append($('<div/>', {
					id: 'calendar',
					'data-year': date[0] || '',
					'data-month': date[1] || '',
					'data-day': date[2] || ''
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'Users.sendEditOnsite(' + id + ', this);',
					html: 'Done'
				})
			})),
			cb: function () {
				$('#calendar').calendar(function() {
					$('input[name="date_end"]').val($('#calendar > input[name="date"]').val());
					$('.dGroup > input + div').hide().parent().removeClass('act');
				});
			}
		}); */
	},
    sendUpdateStaff: function (id, el) { 
        $.post('/users/onsite_update_staff', {
            id: id,
            staff: Object.keys($('input[name="staff"]').data()).join(','),
            time: $('input[name="time"]').val()
        }, function (r) { 
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: 'Onsite service was successfully updated',
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
    },
    updateOnsiteStaff: function (id) {
        $.post('/users/onsite_info', {
            id: id
        }, function (r) {
            if (r.id == id) {
                $.post('/users/all', {staff: 1}, function(ur) {
                    var uitems = '', ulId = 0;
                    if (ur.list) {
                        $.each(ur.list, function(ui, uv) {
                            uitems += '<li data-value="' + uv.id + '">' + uv.name + '</li>';
                            ulId = uv.id;
                        });
                    }

/*                     mdl.open({
                        id: 'addOnsite',
                        title: 'On site service',
                        content: $('<div/>', {
                            class: 'uForm',
                            html: $('<div/>', {
                                class: 'iGroup fw',
                                html: $('<label/>', {
                                    html: 'Time'
                                })
                            }).append($('<input/>', {
                                type: 'time',
                                name: 'time'
                            }))
                        }).append($('<div/>', {
                            class: 'iGroup fw',
                            id: 'staff',
                            html: $('<label/>', {
                                html: 'Staff'
                            })
                        }).append($('<input/>', {
                            type: 'hidden',
                            name: 'staff'
                        })).append($('<ul/>', {
                            class: 'hdn',
                            html: uitems
                        }))).append($('<div/>', {
                            class: 'sGroup',
                            html: $('<button/>', {
                                type: 'button',
                                class: 'btn btnSubmit',
                                onclick: 'Users.sendUpdateStaff(' + id + ', this);',
                                html: 'Done'
                            })
                        })),
                        cb: function () {
                            var selected = {};
                            if (r.selected_staff_id > 0)
                                selected[r.selected_staff_id] = {
                                    name: r.name + ' ' + r.lastname
                                };
                            $('#staff > ul').html(uitems).sForm({
                                action: '/users/all',
                                data: {
                                    lId: ulId,
                                    query: $('#staff > .sfWrap input').val() || '',
                                    staff: 1
                                },
                                all: false,
                                select: selected,
                                s: true,
                                link: 'users/view'
                            }, $('input[name="staff"]'));
                        }
                    }); */
                }, 'json');
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        }, 'json');
    },
	delOnsite: function(id) {
		$.post('/users/del_onsite', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Onsite service was successfully deleted',
					delay: 2
				});
				$('#onsite_' + id).addClass('deleted').find('.uMore').html('');
				$('#onsite_' + id).find('.os_right').html('');
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	onsiteStats: function(id) {
		Users.spage = 0;
		$.post('/users/onsite_stats', {id: id}, function(r) {
/* 			mdl.open({
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
					onclick: 'Users.doloadStats(this,' + id + ');',
					html: 'Load statistic'
				}))
			}); */
		}, 'json');
	}, doloadStats: function(e, id) {
		Users.spage += 1;
		$.post('/users/onsite_stats', {
			id: id, 
			page: Users.spage
		}, function(r) {
			$('.stats').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	},
	delNote: function(id) {
		$.post('/users/del_note', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Note was successfully deleted',
					delay: 2
				});
				$('#note_' + id).remove();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	addNote: function(id, el) {
		loadBtn.start($(el));
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
			loadBtn.stop($(el));
        } else {
            data.note = $('textarea[name="note"]').val();
            $.post('/users/send_note', data, function(r) {
				loadBtn.stop($(el));
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
/*         mdl.open({
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
                    onclick: 'Users.sendOnsiteInvoice(' + id + ');',
                    html: 'Send'
                })
            }))
        }); */
    },
    sendOnsiteInvoice: function (id) {
        /* if ($('input[name="onsite_price"]').val() <= 0) {
            alr.show({
                class: 'alrDanger',
                content: 'Price can not be empty',
                delay: 2
            });
        } else { */
            $.post('/invoices/send_onsite', {
                id: id
                //price: $('input[name="onsite_price"]').val()
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
        //}    
    },
	playOnsite: function(id, a, os_id) {
/* 		mdl.open({
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
				class: 'iGroup',
				id: 'service_type',
				html: $('<label/>', {
					html: 'Service type'
				})
			}).append((parseInt($('#onsite_'+id).attr('data-calls')) && parseInt($('#onsite_'+id).attr('data-time'))) ? $('<select/>', {
				name: 'service_type',
				html: (parseInt($('#onsite_'+id).attr('data-calls')) ? $('<option/>', {
					value: 'call',
					html: lang[173]
				}) : '')
			}).append(parseInt($('#onsite_'+id).attr('data-time')) ? $('<option/>', {
				value: 'time',
				html: lang[174]
			}) : '') : (
				parseInt($('#onsite_'+id).attr('data-calls')) ? 'Available calls ' + $('#calls_onsite_' + id).html() : (parseInt($('#onsite_'+id).attr('data-time')) ? 'Available time ' + $('#time_onsite_' + id).html() : '')
			))).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'Note'
				})
			}).append($('<textarea/>', {
				name: 'onsite_note'
			}))).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'Message for user'
				})
			}).append($('<textarea/>', {
				name: 'user_message'
			}))).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					type: 'button',
					onclick: 'Users.updateOnsite(' + id + ', \'' + a + '\', ' + os_id + ');',
					html: 'Done'
				})
			}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'Cancel'
				}))),
			cb: function() {
				if ($('#onsite_' + id).attr('data-calls') == 1) 
					$('select[name="service_type"]').find('option[value="call"]').attr('selected', 'selected');
				else if ($('#onsite_' + id).attr('data-time') == 1)
					$('select[name="service_type"]').find('option[value="time"]').attr('selected', 'selected');
				$('select').select();
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
								s: true,
								link: 'objects/edit'
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
		}); */
	},
	updateOnsiteNote: function(id, a, os_id) {
/* 		mdl.open({
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
						onclick: 'Users.updateOnsite(' + id + ', \'' + a + '\', ' + os_id + ');',
						html: 'Done'
					})
				}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'Cancel'
				})))
			}); */
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
				note: $('textarea[name="onsite_note"]').val(),
				user_message: $('textarea[name="user_message"]').val(),
				service_type: ($('#service_type').length ? ($('select[name="service_type"]').length ? $('select[name="service_type"]').val() : $('#service_type').html().indexOf('time') >= 0 ? 'time' : 'call') : '')
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
        $.post('/users/all', {staff: 1}, function(ur) {
            var uitems = '', ulId = 0;
            if (ur.list) {
                $.each(ur.list, function(ui, uv) {
                    uitems += '<li data-value="' + uv.id + '">' + uv.name + '</li>';
                    ulId = uv.id;
                });
            }

            $.post('/users/objects?onsite=1', {}, function(or) {
                var oitems = '', olId = 0;
                if (or.list) {
                    $.each(or.list, function(oi, ov) {
                        oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
                        olId = ov.id;
                    });
                }
/*                 mdl.open({
                    id: 'addOnsite',
                    title: 'On site service',
                    content: $('<div/>', {
                        class: 'uForm',
                        html: $('<div/>', {
							class: 'iGroup fw',
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
                        onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
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
                        onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
                    })).append($('<div/>', {
                        id: 'eCalendar'
                    }))).append($('<div/>', {
                        class: 'iGroup fw',
                        html: $('<label/>', {
                            html: 'Time'
                        })
                    }).append($('<input/>', {
                        type: 'time',
                        name: 'time'
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
                        class: 'hdn',
                        html: uitems
                    }))).append($('<div/>', {
                        class: 'sGroup',
                        html: $('<button/>', {
                            type: 'button',
                            class: 'btn btnSubmit',
                            onclick: 'Users.sendService(this, ' + id + ');',
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

                        $('#object > ul').html(oitems).sForm({
                            action: '/users/objects',
                            data: {
                                lId: olId,
                                query: $('#object > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="object"]').data(),
                            s: true,
							link: 'objects/edit'
                        }, $('input[name="object"]'), Users.getOnsite);
						
						Users.getOnsite();

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
                    }
                }); */
            }, 'json');
		 }, 'json'); 
	},
	getOnsite: function() {
		$.post('/inventory/AllOnsite', {oId: Object.keys($('input[name="object"]').data() || {}).join(',')}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '">' + v.name + '</li>';
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
					s: true,
					link: 'inventory/onsite/edit'
				}, $('input[name="service"]'));
			}
		}, 'json');
	},
	sendService: function(el, id) {
		loadBtn.start($(el));
		var data = {
			id: (id || Object.keys($('input[name="customer"]').data())[0]),
			date_start: $('input[name="date_start"]').val(),
            date_end: $('input[name="date_end"]').val(),
            time: $('input[name="time"]').val(),
            staff: Object.keys($('input[name="staff"]').data()).join(',')
		}, err = null;
		if (!Object.keys($('input[name="service"]').data()).join(',')) 
			err = 'Please, choose service';
		else 
            data.service = Object.keys($('input[name="service"]').data()).join(',');
        if (!Object.keys($('input[name="object"]').data()).join(',')) 
			err = 'Please, choose object';
		else 
			data.object = Object.keys($('input[name="object"]').data()).join(',');
		if (!id && !Object.keys($('input[name="customer"]').data()).join(',')) 
			err = 'Please, choose customer';
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
				if(parseInt(r) > 0) {
					alr.show({
						class: 'alrSuccess',
						content: 'The service was successfully added',
						delay: 2
					});
					Page.get('/users/view/' + (id || Object.keys($('input[name="customer"]').data())[0]));
					mdl.close();
					invoices.send_mail(r);
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
/* 		mdl.open({
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
					onclick: 'Users.accPhoto()',
					html: 'Use photo'
				})
			})),
			cb: webcam.startup
		}); */
	}, accPhoto: function() {
		$('.imgGroup > figure > img').attr('src', $('#photo').attr('src')).attr('new', 1);
		$('dropdown').val('');
		mdl.close();
	}, newUsr: function(a) {
/* 		mdl.open({
			id: 'newUser',
			title: 'New User',
			content: '<form class="uForm" method="post" onsubmit="Users.addShort(this, event, '+a+');">\
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
		}); */
	}, addShort: function(f, event, n) {
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
				
				if(n){
					$('#cname').data().value = r;
					$('#cname').find('input').val($(f).find('input[name="name"]').val() + ' ' + $(f).find('input[name="lastname"]').val());
				} else {
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
				}
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
		if ($(v).val() != 0) {
			console.log(v);
			$('.com').show()
			$('.usr').hide()
		} else {
			$('.com').hide()
			$('.usr').show()
		}
	}, add: function(f, event, id, steps, app) {
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
		if (app)
			data.append('app', app);
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
                    Page.get(steps ? '/inventory/step/?user=' + r : (app ? '/users/add_appointment/?user=' + r : '/users/view/' + r));
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
						.find('a[href="javascript:Users.del('+id+');"]')
						.attr('href', 'javascript:Users.restore('+id+');')
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
						.find('a[href="javascript:Users.restore('+id+');"]')
						.attr('href', 'javascript:Users.del('+id+');')
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
};