var page = 0,
    query = '',
	mob = 0;
	
$.getJSON('/language/'+lang_code+'/site/javascript.json', function(r){
	window.lang = r;
});

//setInterval(function(){
//	$('iframe').eq(0).attr('src', $('iframe').eq(0).attr('src'));
//	}, 30000
//);

window.onload = function(){
    window.setTimeout(function() {
            window.addEventListener("popstate", function(e) {
                e.preventDefault();
                if (e.state)
                    Page.get(e.state.link, true);
                else
                    Page.get(window.location.pathname, true);
            }, false);
        }, 1
	);
	menu();
    Page.init();
	Chat.init();
};
	
// Is valid form
var is_valid = {
    name: function(name) {
        var pattern = new RegExp(/^[A-Za-za-яА-ЯїЇіІЄє']+$/);
        return pattern.test(name);
    },
    email: function(email) {
        var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(email);
    },
    phone: function(num) {
        var pattern = new RegExp(/^[0-9-(-+]+$/i);
        return pattern.test(num);
    },
    intval: function(num) {
        var pattern = new RegExp(/^[0-9+]+$/i);
        return pattern.test(num);
    }
}


function share(a){
	window.open(a, null, "width=600, height=400, top="+(screen.availHeight-500)/2+", left="+(screen.availWidth-600)/2+"");
}


// Search
var Search = function(a) {
    query = a;
    $.post(location.pathname, {
        query: a
    }, function(r) {
        page = 0;
        $('#res_count').text(r.res_count);
        $('.userList').html(r.content);
        if (r.left_count) $('.btnLoad').removeClass('hdn');
        else $('.btnLoad').addClass('hdn')
    }, 'json');
};

// Doload
var Doload = function(e) {
    page += 1;
    $.post(location.pathname, {
        query: query,
        page: page
    }, function(r) {
        $('#cat').append(r.content);
        if (!r.left_count) $(e).addClass('hdn');
    }, 'json');
};

// Login
function is_valid_email(a){
	var p = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
	return p.test(a);
}

function cart_login(a){
	a.preventDefault();
	let f = a.target, l = location.pathname;
	$.post('/?login', {
		email: f.login_email.value,
		password: f.login_password.value
	}, function(r){
		if (r == 'OK') {
			if (l == '/cart/login')
				cart.send(1);
				//location.href = location.origin + '/cart?run';
			else
				location.reload();
		} else if (r == 'IP') {
			alr.show({
				class: 'alrDanger',
				content: 'You can not login from this IP',
				delay: 2
			});
			$('.loginForm').show(0);
			$('.preloader').hide(0);
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'Wrong password. Please, try again.',
				delay: 2
			});
			$('.loginForm').show(0);
			$('.preloader').hide(0);
		}
	});
	return false;
}

function login(l){
	var email = $('#email').val(), password = $('#password').val();
	if(is_valid_email(email) && password.length > 0 && email.length > 0){
		$('.loginForm').hide(0);
		$('.preloader').show(0);
		$.post('/?login', {
			email: email,
			password: password
		}, function(r){
			if (r == 'OK') {
				if (l == '/cart/login')
					location.href = location.origin + '/cart';
				else
					location.reload();
			} else if (r == 'IP') {
				alr.show({
					class: 'alrDanger',
					content: 'You can not login from this IP',
					delay: 2
				});
				$('.loginForm').show(0);
				$('.preloader').hide(0);
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'Wrong password. Please, try again.',
					delay: 2
				});
				$('.loginForm').show(0);
				$('.preloader').hide(0);
			}
		});
	} else if (email.length == 0) {
		alr.show({
			class: 'alrDanger',
			content: 'Enter the email',
			delay: 2
		});
	} else if (!is_valid_email(email)) {
		alr.show({
			class: 'alrDanger',
			content: 'Email is not valid',
			delay: 2
		});
	} else {
		alr.show({
			class: 'alrDanger',
			content: 'Enter the password.',
			delay: 2
		});
	}
}

// Menu fixing
function menu() {
	$(window).scroll(function(event) {
		if ($(window).scrollTop() > 140)
			$('body').addClass('fixed');
		else 
			$('body').removeClass('fixed');
	});
}

function unsubscribe(){
	$('.page').html('<div class="err404 ctnr"><h1>Successfully unsubscribed</h1><p>You have successfully unsubscribed from our site.</p></div>');
}

// Page
var Page = {
    init: function(href) {
        page = 0, query = '';
        menu();
        //$('#page').checkbox();
		var opt = '';
		for(var i = 0; i < locations.length; i++){
			opt += '<option value="'+locations[i].id+'">'+locations[i].address+'</option>';
		}
		$('.store-select').html(opt);
        $('#page').select();
        $('#page').tabs();
		$('#page').inptel();
		ga('send', 'pageview');
		mob = 0;
		if ($('#map').length)
			initMap();
    },
    get: function(href, back) {
		//$('body').addClass('pre');
        if (!back) {
            history.pushState({
                link: href
            }, null, href);
        }
		
		if ($(window).width() <= 991)
			$('.hdr-nav > ul').hide();
		else
			$('.hdr-nav > ul').show();
		
        $.getJSON(href, function(r) {
            if (r == 'loggout') location.reload();
            document.title = r.title;
            $('#page').html(r.content);
			if (!mob) {
				if ($(window).width() <= 991) {
					$(window).scrollTop($('#page').offset().top);
					$('.nvg ul').slideUp();
				} else 
					$(window).scrollTop(0);
			}
			//$('body').removeClass('pre');
            Page.init(href);
        });
    }
};


// Account
var account = {
	sendApp: function(e) {
		loadBtn.start($(e));
		var data = {}, err = null;
		
		if (!$('input[name="sdate"]').val()) 
			err = 'Enter date';
		else if (!$('input[name="time"]').val())
			err = 'Enter time';
		else if (!Object.keys($('input[name="object"]').data())[0])
			err = 'Select store';
		else if (new Date($('input[name="sdate"]').val()) < new Date())
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
			loadBtn.stop($(e).find('button'));
		} else {
			$.post('/account/send_app', data, function(r) {
				loadBtn.stop($(e).find('button'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Appointment was successfully added',
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
	addApp: function(id) {
		$.post('/account/allObjects', {}, function(or) {
			var oitems = '', olId = 0;
			if (or.list) {
				$.each(or.list, function(oi, ov) {
					oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
					olId = ov.id;
				});
			}

			mdl.open({
					id: 'addAppointment',
					title: 'On appointment',
					content: $('<div/>', {
						class: 'uForm',
						html: '<div class="iGroup fw dGroup act">\
								<label>Date</label>\
								<input type="text" name="sdate" class="cl" onclick="$(this).next().show();">\
								<div id="calendar" class="hdn cl"></div>\
							</div>\
							<div class="iGroup fw">\
								<label>Time</label>\
								<input type="time" name="time">\
							</div>\
							<div class="iGroup fw sfGroup" id="object">\
								<label>Store</label>\
								<input type="hidden" name="object" />\
								<ul class="hdn"></ul>\
							</div>\
							<div class="sGroup">\
								<button class="btn btnSubmit" type="button" onclick="account.sendApp(this);"><span class="fa fa-save"></span> Send</button>\
							</div>'
					}),
					cb: function() {
						$('#calendar').calendar(function() {
							var birth_date = $('#calendar > input[name="date"]').val().split('-');
							$('input[name="sdate"]').val($('#calendar > input[name="date"]').val());
							$('.dGroup > input + div').hide().parent().removeClass('act');
						});
						
						$('.uForm').on('click', function(event) {
						if (!$(event.target).hasClass('cl')) {
							console.log($(event.target).hasClass('cl'));
							$('.calendar').hide().parents('.iGroup').removeClass('act');
						}
					});

						$('#object > ul').html(oitems).sForm({
							action: '/account/allObjects',
							data: {
								lId: olId,
								query: $('#object > .sfWrap input').val() || ''
							},
							all: false,
							select: $('input[name="object"]').data(),
							s: true
						}, $('input[name="object"]'));
					}
			});
		}, 'json');
	},
	addOnsite: function(id) {
		$.post('/account/allObjects', {}, function(or) {
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
						class: 'iGroup fw',
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
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'account.sendService(this, ' + id + ');',
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
						action: '/account/allObjects',
						data: {
							lId: olId,
							query: $('#object > .sfWrap input').val() || ''
						},
						all: false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'), account.getOnsite);
					
					account.getOnsite();
				}
			});
		}, 'json');
	},
	getOnsite: function() {
		$.post('/account/AllOnsite', {oId: Object.keys($('input[name="object"]').data() || {}).join(',')}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="' + v.currency + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
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
            date_end: $('input[name="date_end"]').val(),
            time: $('input[name="time"]').val()
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
			$.post('/account/send_service', data, function(r) {
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
	toggleList: function(el, ev) {
		if (!$(ev.target).parents('.td').hasClass('w100') && !$(ev.target).parents('.td').hasClass('w5'))
            $(el).toggleClass('open').next().slideToggle();
	}, login: function() {
		mdl.open({
			id: 'login',
			class: 'login',
			title: 'Login form',
			content: $('<div/>', {
				html: $('<div/>', {
					class: 'loginForm',
					html: $('<div/>', {
						class: 'lGroup',
						html: $('<span/>', {
							class: 'fa fa-user'
						})
					}).append($('<input/>', {
						type: 'text',
						placeholder: 'Login',
						name: 'login',
						id: 'email'
					}))
				}).append($('<div/>', {
					class: 'lGroup',
					html: $('<span/>', {
						class: 'fa fa-lock'
					})
				}).append($('<input/>', {
					type: 'password',
					id: 'password',
					placeholder: 'Password',
					name: 'password'
				}))).append($('<div/>', {
					class: 'lGroup',
					html: $('<button/>', {
						html: 'Login',
						class: 'btn btnLogin',
						type: 'button',
						onclick: 'login();'
					})
				})).append($('<div/>', {
					class: 'orReg',
					html: $('<span/>', {
						html: 'or'
					})
				}).append($('<button/>', {
					class: 'btn btnReg',
					html: 'Registration',
					onclick: 'account.registration();'
				})))
			}).append($('<div/>', {
				class: 'preloader',
				html: $('<span/>', {
					class: 'fa fa-cog fa-spin'
				})
			}))
		});
	}, registration: function() {
		$('#login').remove();
		mdl.open({
			id: 'reg',
			class: 'reg',
			title: 'Registration',
			content: $('<div/>', {
				class: 'regForm',
				html: $('<div/>', {
					class: 'regSteps',
					html: $('<span/>', {
						class: 'fStep',
						html: '1',
						onclick: '$(\'#reg .regForm\').removeClass(\'next\');'
					})
				}).append($('<span/>', {
						class: 'sStep',
						html: '2',
						onclick: 'account.authNext(this);'
					}))
		}).append($('<div/>', {
			class: 'step fStep',
			html: $('<div/>', {
					class: 'input-group fw w50',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					placeholder: 'Name',
					name: 'name'
				}))
			}).append($('<div/>', {
				class: 'input-group fw w50',
				html: $('<label/>', {
					html: 'Lastname'
				})
			}).append($('<input/>', {
				type: 'text',
				placeholder: 'Lastname',
				name: 'lastname'
			}))).append($('<div/>', {
				class: 'input-group fw',
				html: $('<label/>', {
					html: 'Phone'
				})
			}).append($('<input/>', {
				type: 'text',
				placeholder: 'Phone',
				name: 'phone'
			}))).append($('<div/>', {
				class: 'input-group fw',
				html: $('<label/>', {
					html: 'Address'
				})
			}).append($('<textarea/>', {
				cols: '3',
				placeholder: 'Address',
				name: 'address'
			}))).append($('<div/>', {
				class: 'input-group',
				html: $('<button/>', {
					html: 'Next',
					class: 'btn btnLogin',
					type: 'button',
					onclick: 'account.authNext(this);'
				})
			}))
		).append($('<div/>', {
			class: 'step sStep',
			html: $('<div/>', {
					class: 'input-group fw',
					html: $('<label/>', {
						html: 'Email'
					})
				}).append($('<input/>', {
					type: 'email',
					placeholder: 'Email',
					name: 'email'
				}))}).append($('<div/>', {
					class: 'input-group fw',
					html: $('<label/>', {
						html: 'Password'
					})
				}).append($('<input/>', {
					type: 'password',
					placeholder: 'Password',
					name: 'password'
				}))).append($('<div/>', {
					class: 'input-group fw',
					html: $('<label/>', {
						html: 'Repeat rassword'
					})
				}).append($('<input/>', {
					type: 'password',
					placeholder: 'Password',
					name: 'password2'
				}))).append($('<div/>', {
					class: 'a-right',
					html: $('<button/>', {
						html: 'Registration',
						class: 'btn btnLogin',
						type: 'button',
						onclick: 'account.auth(this);'
					})
				})
			))
		});
	}, editUser: function(f, event, id) {
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
            } else if (form[i].name == 'name' || form[i].name == 'lastname') {
                if (!is_valid.name(form[i].value) || !is_valid.name(form[i].value)) {
                    err = lang[6];
                    break;
                }
            } else if (form[i].name == 'password') {
                if (form[i].value.length > 0 && form[i].value != $('input[name="password2"]').val()) {
                    err = lang[7];
                    break;
                }
            }
            data.append(form[i].name, form[i].value);
        }
		var phones = '',
                sms = 0;
            $('.sPhone').each(function (i, v) {
                $(v).find('input').css('border', '1px solid #ddd');
                if ($(v).find('select').val() || $(v).find('input').val()) {
                    if (!$(v).find('[name="phoneCode"]').val() || !$(v).find('[name="code"]').val() || !$(v).find('[name="part1"]').val()) {
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
		
        /*var files = $('.dragndrop')[0].files;
        if (!$.isEmptyObject(files)) {
            for (var n in files) {
                data.append('image', files[n]);
            }
        }*/
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        //var thumb = $('.dragndrop').prev();
        //if (thumb[0].tagName != 'FIGURE' && id) data.append('del_image', true);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/account/editUser',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
				Page.get('/account/');
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[10],
                    delay: 2
                });
            }
        });
    }, auth: function (el) {
        loadBtn.start($(el));
        var data = {}, err = '';
		$(el).parents('.regForm').find('.input-group').find('label').next().css('border', '1px solid #b8b8b8');
        $(el).parents('.regForm').find('.input-group').each(function(i, v) { 
            if (!$(v).find('label').next().val()) {
                err = $(v).text() + ' can not be empty';
                $(v).find('label').next().css('border', '1px solid #f00');
				return false;
            } else {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            }
        })

        if (err) {
            loadBtn.stop($(el));
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
			if ($('input[name="password"]').val() == $('input[name="password2"]').val()) {
				$.post('/reg/', data, function (r) {
					loadBtn.stop($(el));
					if (r == 'OK') {
						alr.show({
							class: 'alrSuccess',
							content: 'You successfully registered on Your Company! Please, check Your email to confirm Your account.',
							delay: 2
						});
						if ($('.mdl').length)
							mdl.close();
						else 
							Page.get('/');
					} else {
						alr.show({
							class: 'alrDanger',
							content: 'It seems something wrong, Please, try again.',
							delay: 2
						});
					}
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'Passwords are not matched',
					delay: 2
				});
				$('input[name="password"]').css('border', '1px solid #f00');
				$('input[name="password2"]').css('border', '1px solid #f00');
			}            
        } 
    }, authNext: function(el) {
		var err = '';
		$(el).parents('.regForm').find('.fStep .input-group').find('label').next().css('border', '1px solid #ddd');
		$(el).parents('.regForm').find('.fStep .input-group').each(function(i, v) { 
            if (!$(v).find('label').next().val()) {
                err = $(v).text() + ' can not be empty';
                $(v).find('label').next().css('border', '1px solid #f00');
				return false;
            } 
        });
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$(el).parents('.regForm').addClass('next');
		}
	}, addDevice: function(id) {
		$.post('/account/allOS', {}, function(r) {
			if (r) {
				var os = '';
				$.each(r.list, function(i, v) {
					os += '<option value="' + v.id + '">' + v.name + '</option>';
				});
				$.post('/account/allCategories', {}, function(cr) {
					var cats = '', clId = 0;
					$.each(cr.list, function(i, v) {
                        cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
                        clId = v.id;
                    });
                    $.post('/account/allTypes', {}, function(tr) {
                        var types = '', tlId = 0;
                        $.each(tr.list, function(i, v) {
                            types += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            tlId = v.id;
                        });
						
						mdl.open({
							id: 'createDevice',
							title: 'Add device',
							content: $('<div/>', {
								class: 'form',
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
                                    html: types,
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
									class: 'hdn',
									html: cats
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
								class: 'iGroup fw',
								html: $('<label/>', {
									html: 'Model Specification'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'smodel'
							}))).append($('<div/>', {
								class: 'iGroup  fw',
								id: 'os',
								html: $('<label/>', {
									html: 'OS'
								})
							}).append($('<select/>', {
								name: 'os',
								html: os
							}))).append($('<div/>', {
								class: 'iGroup fw',
								html: $('<label/>', {
									html: 'OS version'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'os_ver'
							}))).append($('<div/>', {
								class: 'iGroup fw',
								html: $('<label/>', {
									html: 'Serial Number'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'sn'
							}))).append($('<div/>', {
								class: 'sGroup',
								html: $('<button/>', {
									type: 'button',
									class: 'btn btnSubmit',
									onclick: 'account.sendDev(this);',
									html: 'Send'
								})
							})),
							cb: function() {
								var lId = 0;
								$('#page').select();
								
								$('#brand > ul').sForm({
									action: '/account/allCategories',
									data: {
										lId: clId,
										nIds: Object.keys($('input[name="brand"]').data()).join(','),
										query: $('#brand > .sfWrap input').val()
									},
									all: false,
									select: $('input[name="brand"]').data(),
									s: true
                                }, $('input[name="brand"]'), account.getGroup);
                                
                                $('#type > ul').sForm({
									action: '/account/allTypes',
									data: {
										lId: tlId,
										nIds: Object.keys($('input[name="type"]').data()).join(','),
										query: $('#type > .sfWrap input').val()
									},
									all: false,
									select: $('input[name="type"]').data(),
									s: true
                                }, $('input[name="type"]'));
                                
                                account.getGroup();
							}
                        });
                }, 'json');
			}, 'json');
			}
		}, 'json');
	}, sendDev: function(f, id) {
        loadBtn.start($(f));
        var data = new FormData(),
            form = $(f).parents('.form'),
            err = null;
        if (id) data.append('id', id);
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if (!form.find('input[name="brand"]').data()) {
            err = lang[124];
        } else {
            if (form.find('select[name="type"]').val() == 0) {
                err = lang[123];
            } else {
                data.append('type', Object.keys(form.find('input[name="type"]').data()).join(','));
                data.append('smodel', form.find('input[name="smodel"]').val() || '');
                data.append('os', form.find('select[name="os"]').val() || '');
                data.append('os_version', form.find('input[name="os_ver"]').val() || '');
                data.append('serial', form.find('input[name="sn"]').val() || '');
                data.append('category', Object.keys(form.find('input[name="brand"]').data() || {}).join(','));
                data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
				data.append('intype', 'stock');
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
                url: '/account/send_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(f));
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
					if (r > 0) {
					   Page.get(location.pathname);
					   mdl.close();
					}
						
                }
            });
        }
	}, delDevice: function(id) {
        $.post('/account/del_inventory', {
            id: id
        }, function(r) {
            if (r == 'OK') {
                $('#dev_' + id).next().remove();
                $('#dev_' + id).remove();
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
	}, addIssue: function(id) {
		var items = '';
		$.post('/account/allObjects', {}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<option value="' + v.id + '"' + (i == 0 ? ' selected' : '') + '>' + v.name + '</option>';
					lId = v.id;
				});
				mdl.open({
					id: 'newIssue',
					title: 'Add issue',
					content: $('<div/>', {
						class: 'form',
						html: $('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Store'
							})
						}).append($('<select/>', {
							name: 'store',
							html: items
						}))
					}).append($('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Description'
						})
					}).append($('<textarea/>', {
						name: 'descr'
					}))).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'account.sendIssue(this, ' + id + ')',
							html: 'Send'
						})
					})),
					cb: function() {
						$('select').select();
					}
				});
			}
		}, 'json');
	}, sendIssue: function(f, id) {
		var f = $(f).parents('.form');
		if (!f.find('textarea[name="descr"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Description can not be empty',
				delay: 2
			});
		} else {
			$.post('/account/add_issue', {
				id: id,
				descr: f.find('textarea[name="descr"]').val(),
				store: f.find('select[name="store"]').val()
			}, function(r) {
				if (r) {
					alr.show({
						class: 'alrSuccess',
						content: 'Issue was successfully added',
						delay: 2
					});
					/*$('#dev_' + id).next().append($('<div/>', {
						class: 'issue',
						html: $('<div/>', {
							class: 'iId',
							html: r.id
						})
					}).append($('<div/>', {
						class: 'isDate',
						html: r.date
					})).append($('<div/>', {
						class: 'is',
						html: f.find('textarea[name="descr"]').val()
					})))*/
                    mdl.close();
                    Page.get(location.href);
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[9],
						delay: 2
					});
				}
			}, 'json')
		}
	}, getGroup: function(id, iId, f) {
		var items = '', lId = 0;
		$.post('/account/allModels', {
			brand: Object.keys($('input[name="brand"]').data()).join(',')
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
					action: '/account/allModels',
					data: {
						nIds: Object.keys($('input[name="model"]').data()).join(','),
						query: $('#model > .sfWrap input').val()
					},
					all: true,
					select: $('input[name="model"]').data(),
					s: true
				}, $('input[name="model"]'))
			}
		}, 'json');
    },
};

// Cart
var cart = {
	saveDelivery: function(v) {
		$.post('/cart/save_delivery', {
			v: v
		}, function(r) {});
	},
	pay: function(f, event, id) {
		event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
		var data = new FormData(),
            form = $(f).serializeArray(),
			err = null;
		
		data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'card-number') {
                if (!form[i].value.length || form[i].value.length != 16) {
                    err = form[i].value.length ? 'Card number is incorrect' : 'Card nu,ber can not be empty';
                    break;
                }
            } else if (form[i].name == 'code') {
                if (!form[i].value.length) {
                    err = 'CVV code can not be empty';
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
        } else {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/cart/send_pay',
				data: data
			}).done(function(r) {
				loadBtn.stop($(event.target).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Your invoice was successfully paid',
						delay: 2
					});
					Page.get('/account/');
				} else if (r == 'trans_failed') {
					alr.show({
						class: 'alrDanger',
						content: 'Transaction failed. Please try again later',
						delay: 2
					});
				} else if (r == 'card_failed') {
					alr.show({
						class: 'alrDanger',
						content: 'Transaction failed. Please, check you credit card info',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: 'It seems something wrong. Please try again later',
						delay: 2
					});
				}
			});
		}
	},
	add: function (id, name, price) {
		$.post('/cart/add', {id: id}, function (r) {
			if (r == 'in_cart') {
				alr.show({
					class: 'alrDanger',
					content: 'Item are alredy in cart',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrSuccess',
					content: 'Item was successfully added to cart',
					delay: 2
				});
				$('.mini-cart > ul').prepend($('<li/>', {
					id: 'miniCart_' + id,
					html: $('<a/>', {
						href: '/item/' + id,
						html: name,
						onclick: "Page.get(this.href); return false;"
					})
				}).append($('<span/>', {
					class: 'cartPrice',
					html: '$' + price
					})));
				$('.cartSubtotal > span:last-child').html('$' + (
					parseFloat($('.cartSubtotal > span:last-child').text().replace('$', '')) + parseFloat(price)
				));
				/*if (!$('.hCart > div > i').length) {
					$('.hCart > div').first().append($('<i/>', {
						html: '1'
					}))
					$('.miniCart').removeClass('nCart');
				} else {
					$('.hCart > div > i').html(parseInt($('.hCart > div > i').text()) + 1);
				}*/
			}
		});
	}, del: function (id, price) {
		$.post('/cart/del', { id: id }, function (r) {
			if (r) {
				$('#cart_' + id).remove();
				alr.show({
					class: 'alrSuccess',
					content: 'Item was successfully deleted from cart',
					delay: 2
				});
				if (parseInt($('.hCart > div > i').text()) == 1) 
					$('.hCart > div > i').remove()
				else 
					$('.hCart > div > i').html(parseInt($('.hCart > div > i').text()) - 1);
				$('#miniCart_' + id).remove();
				$('.cartSubtotal > span').html($('.cartSubtotal > span').text().replace(/[0-9.\s]/g, '') + (
					parseFloat($('.cartSubtotal > span').text().replace(/[^0-9.]/g, '')) - parseFloat(price)
				));
				$('#subtotal').html(
					(parseFloat($('#subtotal').text()) - parseFloat(price)).toFixed(2)
				);
				
				var tax = (parseFloat($('#tax').text()) - r * parseFloat(price) / 100).toFixed(2);
				$('#tax').html(tax < 0 ? 0 : tax);
				$('#total').html((parseFloat($('#subtotal').text()) + parseFloat($('#tax').text())).toFixed(2));
				if (!$('.hCart > div > i').length) $('.miniCart').addClass('nCart');
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'It\'s something wrong. Please, try again',
					delay: 2
				});
			}
		});
	}, clear: function () {
		$.post('/cart/clear', {}, function (r) {
			if (r == 'OK') {
				$('#subtotal').html(0);
				$('#tax').html(0);
				$('#total').html(0);
				$('[id*="cart"]').remove();
				$('[id*="miniCart"]').remove();
				$('.cartSubtotal > span').html('$0');
				$('.hCart > div > i').remove();
				$('.miniCart').addClass('nCart');
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'It\'s something wrong. Please, try again',
					delay: 2
				});
			}
		})
	}, changeDelivery: function() {
		var v = $('select[name="delivery"] option[value="' + $('select[name="delivery"]').val() + '"]');
		/*$('#del-cur').text(v.attr('data-symbol'));
		$('#del-price').text(v.attr('data-price'));*/
		$('#total').html(parseFloat(parseFloat($('#total').attr('data-total')) + parseFloat(v.attr('data-price'))).toFixed(2));
		//$('#subtotal').html(parseFloat(parseFloat($('#subtotal').attr('data-total')) + parseFloat(v.attr('data-price'))).toFixed(2));
		$.post('/cart/change_delivery', {
			id: $('select[name="delivery"]').val(),
			price: v.attr('data-price'),
			currency: v.attr('data-currency')
		});
	}, changePayment: function() {
		$.post('/cart/payment', {
			id: $('select[name="payment"]').val()
		});
	}, send: function(a) {
		/*event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));*/
		var data = new FormData(),
			err = null; 
		
		data.append('delivery', $('select[name="delivery"]').val());
		data.append('total', parseFloat($('#total').text()));
		data.append('tax', parseFloat($('#total').attr('data-tax')));
		data.append('currency', $('#total').attr('data-currency'));
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/cart/send',
				data: data
			}).done(function(r) {
				//loadBtn.stop($(event.target).find('button[type="submit"]'));
				if (parseInt(r) > 0) {
					if(a)
						location.href = '/cart/delivery/' + parseInt(r);
					else
						Page.get('/cart/delivery/' + parseInt(r));
				} else {
					alr.show({
						class: 'alrDanger',
						content: 'It\'s something wrong. Please, try again later.',
						delay: 2
					});
					if(a)
						location.reload();
					else
						Page.get(location.href);
				}
			});
		}
	}, sendDelivery: function(f, event, id) {
		event.preventDefault();
		var data = new FormData(),
			err = null; 
			
		data.append('id', id);
		data.append('country', Object.keys($('input[name="country"]').data())[0]);
		data.append('state', Object.keys($('input[name="state"]').data())[0]);
		data.append('city', Object.keys($('input[name="city"]').data())[0]);
		data.append('zipcode', $('input[name="zipcode"]').val());
		data.append('address', $('textarea[name="address"]').val());

		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/cart/send_delivery',
				data: data
			}).done(function(r) {
				if (parseInt(r) > 0) {
					Page.get('/cart/pay/' + parseInt(r));
				} else {
					alr.show({
						class: 'alrDanger',
						content: 'It\'s something wrong. Please, try again later.',
						delay: 2
					});
					Page.get(location.href);
				}
			});
		}
	}
}

// Text toggle
function showText(e) {
	if ($(e).hasClass('show')) {
		$(e).removeClass('show').text('More text').prev().animate({
			height: 152
		}, 1000, function(){
			$(this).height('152px');
		});
	} else {
		$(e).addClass('show').text('Less text').prev().animate({
			height: $(e).prev().get(0).scrollHeight
		}, 1000, function(){
			$(this).height('auto');
		});
	}
}

// Phones
var phones = {
    newPhone: function () {
        $('.nPhone').before($('<div/>', {
            class: 'sPhone',
            html: $('<span/>', {
                class: 'fa fa-times rd',
                onclick: '$(this).parent().remove();'
            })
        }).append($('<select/>', {
            name: 'phoneCode',
            html: $('<option/>', {
                value: '0',
                html: 'None'
            })
        }).append($('<option/>', {
            value: '+1',
            html: '+1'
        })).append($('<option/>', {
            value: '+3',
            html: '+3'
        }))).append($('<span/>', {
            class: 'wr',
            html: '('
        })).append($('<input/>', {
            type: 'number',
            name: 'code',
			max: '999',
            onkeyup: 'phones.next(this, 3);'
        })).append($('<span/>', {
            class: 'wr',
            html: ')'
        })).append($('<input/>', {
            type: 'number',
            name: 'part1',
            onkeyup: 'phones.next(this, 7);'
        })).append($('<input/>', {
            type: 'number',
            name: 'part2'
        })).append($('<input/>', {
            type: 'checkbox',
            name: 'sms',
            onchange: 'phones.onePhone(this);'
        })));
        $('#page').select();
        $('#page').checkbox();
    },
    onePhone: function (el) {
        var v = $(el).val();
        $('input[name="sms"]').attr('onchange', '').val(0).prop('checked', 0);
        if (v == 1) $(el).val(1).prop('checked', 1);
        $('input[name="sms"]').attr('onchange', 'phones.onePhone(this);');
    }, next: function (e, l) {
        if ($(e).val().length == l) {
            if ($(e).next().hasClass('wr'))
                $(e).next().next().focus();
            else
                $(e).next().focus();
        }
    }
}

// Send feedback
function sendFeedback(f, event) {
	event.preventDefault();
	var data = {},
		err = null;
	if (!$(f).find('input[name="full_name"]').val())
		err = 'Name can not be empty';
	else if (!$(f).find('input[name="email"]').val())
		err = 'Email can not be empty';
	else if (!$(f).find('textarea[name="message"]').val())
		err = 'Message can not be empty';
	else {
		data = {
			name: $(f).find('input[name="full_name"]').val(),
			email: $(f).find('input[name="email"]').val(),
			mes: $(f).find('textarea[name="message"]').val(),
			to: $('.map-list .active').attr('data-email') || ''
		}
	}
	
	if (err) {
		alr.show({
			class: 'alrDanger',
			content: err,
			delay: 2
		});
	} else {
		$.post('/contact-us/send_feedback', data, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Feedback was successfully sent',
					delay: 2
				});
				$(f).find('input[name="full_name"]').val('');
				$(f).find('input[name="email"]').val('');
				$(f).find('textarea[name="message"]').val('');
			} else if (r == 'err_email') {
				alr.show({
					class: 'alrDanger',
					content: 'Email is incorrect. Please, check it',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: 'It\'s something wrong. Please, try again',
					delay: 2
				});
			}
		});
	}
}

// Close calendar
function closeCalendar() {
	$('.calendar').hide().parent().removeClass('act');
}

function closeWindow() {
	mdl.open({
		id: 'closeWindow',
		title: 'Share us',
		class: 'share-social',
		content: '<p>' + lang[175] + '</p>\
			<ul class="share-discount">\
				<li onclick="share(\'https://www.facebook.com/sharer/sharer.php?u='+location.origin+'/?ref=' + user.id + '\');"><span class="fa fa-facebook"></span> Facebook</li>\
				<li onclick="share(\'https://twitter.com/home?status='+location.origin+'/?ref='+user.id+'\');"><span class="fa fa-twitter"></span> Twitter</li>\
				<li onclick="share(\'https://plus.google.com/share?url='+location.origin+'/?ref='+user.id+'\');"><span class="fa fa-google-plus"></span> Google+</li>\
			</ul>'
	});
	
	var d = new Date();
    d.setTime(d.getTime() + 60*60*1000);
    document.cookie = 'showWindow=1;expires='+ d.toUTCString() + ';path=/';
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function scrollID(el, event) {
	event.preventDefault();
	
	var h = $(el).attr('href').replace('#', '');
	$('body').scrollTop($('a[name="'+h+'"]').offset().top);
}

var quote = {
	mdl: function(a) {
		mdl.open({
			id: 'quote-mdl',
			title: a ? 'Call request' : 'Get quote',
			content: '<div class="quote-steps">\
				<div class="qs-step active">\
				'+(user == undefined ? 
					'<div class="input-group fw">\
						<label>Company </label>\
						<input type="text" placeholder="Company name" name="company">\
					</div>\
					<div class="input-group fw">\
						<label>Name <span class="red">*</span></label>\
						<input type="text" placeholder="Name" name="name">\
					</div>\
					<div class="input-group fw">\
						<label>Lastname <span class="red">*</span></label>\
						<input type="text" placeholder="Lastname" name="lastname">\
					</div>\
					<div class="input-group fw">\
						<label>Phone <span class="red">*</span></label>\
						<input type="tel" name="phone" placeholder="+X (XXX) XXX-XXXX" value="+1">\
					</div>\
					<div class="input-group fw">\
						<label>Email <span class="red">*</span></label>\
						<input type="email" placeholder="Email" name="email">\
					</div>' : '')+'\
					<div class="input-group fw">\
						<label>Store <span class="red">*</span></label>\
						<select name="store"></select>\
					</div>\
					<div class="input-group fw">\
						<label>Your issue <span class="red">*</span></label>\
						<textarea cols="3" placeholder="Issues" name="issue"></textarea>\
					</div>\
					<div class="submit-group">\
						<button class="btn btnLogin" type="button" onclick="quote.send(this);">Send</button>\
					</div>\
				</div>\
			</div>',
			cb: function() {
				var opt = '';
				for(var i = 0; i < locations.length; i++){
					opt += '<option value="'+locations[i].id+'">'+locations[i].address+'</option>';
				}
				$('select[name=store]').html(opt);
				$('#page').select();
				$('#quote-mdl').inptel();
			}
		});
	},
	mdl2: function() {
		$.post('/account/allOS', {}, function(r) {
			if (r) {
				var os = '';
				$.each(r.list, function(i, v) {
					os += '<option value="' + v.id + '">' + v.name + '</option>';
				});
				$.post('/account/allCategories', {}, function(cr) {
					var cats = '', clId = 0;
					$.each(cr.list, function(i, v) {
                        cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
                        clId = v.id;
                    });
                    $.post('/account/allTypes', {}, function(tr) {
                        var types = '', tlId = 0;
                        $.each(tr.list, function(i, v) {
                            types += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            tlId = v.id;
                        });
						$.post('/account/allObjects', {}, function(ob) {
							var objects = '', olId = 0;
							$.each(ob.list, function(i, v) {
								objects += '<option value="' + v.id + '">' + v.name + '</option>';
								olId = v.id;
							});
							mdl.open({
								id: 'quote-mdl',
								title: 'Get quote',
								content: '<div class="quote-steps">\
									'+(user == undefined ? 
									'<div class="qs-step active">\
										<div class="input-group fw">\
											<label>Name <span class="red">*</span></label>\
											<input type="text" placeholder="Name" name="name">\
										</div>\
										<div class="input-group fw">\
											<label>Lastname <span class="red">*</span></label>\
											<input type="text" placeholder="Lastname" name="lastname">\
										</div>\
										<div class="input-group fw">\
											<label>Phone <span class="red">*</span></label>\
											<input type="text" placeholder="Phone" name="phone">\
										</div>\
										<div class="input-group fw">\
											<label>Email <span class="red">*</span></label>\
											<input type="email" placeholder="Email" name="email">\
										</div>\
										<div class="input-group fw">\
											<label>Address <span class="red">*</span></label>\
											<textarea cols="3" placeholder="Address" name="address"></textarea>\
										</div>\
										<div class="submit-group">\
											<button class="btn btnLogin" type="button" onclick="quote.next(this);">Next</button>\
										</div>\
									</div>' : '')+'\
									<div class="qs-step'+(user == undefined ? '' : ' active')+'">\
										<div class="input-group fw" id="type">\
											<label>Type <span class="red">*</span></label>\
											<input type="hidden" name="type">\
											<ul class="hdn"></ul>\
										</div>\
										<div class="input-group fw" id="brand">\
											<label>Brand <span class="red">*</span></label>\
											<input type="hidden" name="brand">\
											<ul class="hdn"></ul>\
										</div>\
										<div class="input-group fw" id="model">\
											<label>Model <span class="red">*</span></label>\
											<input type="hidden" name="model">\
											<ul class="hdn"></ul>\
										</div>\
										<div class="input-group fw">\
											<label>Model Specification <span class="red">*</span></label>\
											<input type="text" name="smodel">\
										</div>\
										<div class="input-group fw">\
											<label>OS <span class="red">*</span></label>\
											<select name="os">'+os+'</select>\
										</div>\
										<div class="input-group fw">\
											<label>OS version</label>\
											<input type="text" name="os_ver">\
										</div>\
										<div class="input-group fw">\
											<label>Serial number</label>\
											<input type="text" name="sn">\
										</div>\
										<div class="submit-group">\
											'+(user == undefined ? '<button class="btn btnLogin" style="float: left;" type="button" onclick="quote.prev(this);">Previous</button>' : '')+'\
											<button class="btn btnLogin" type="button" onclick="quote.next(this);">Next</button>\
										</div>\
									</div>\
									<div class="qs-step">\
										<div class="input-group fw">\
											<label>Your issue <span class="red">*</span></label>\
											<textarea cols="3" placeholder="Issues" name="issue"></textarea>\
										</div>\
										<div class="input-group fw dGroup act">\
											<label>Date <span class="red">*</span></label>\
											<input type="text" name="sdate" class="cl" onclick="$(this).next().show();">\
											<div id="calendar" class="hdn cl calendar"></div>\
										</div>\
										<div class="input-group fw">\
											<label>Time <span class="red">*</span></label>\
											<input type="time" name="time">\
										</div>\
										<div class="input-group  fw">\
											<label>Store <span class="red">*</span></label>\
											<select name="store">'+objects+'</select>\
										</div>\
										<div class="submit-group">\
											<button class="btn btnLogin" style="float: left;" type="button" onclick="quote.prev(this);">Previous</button>\
											<button class="btn btnLogin" type="button" onclick="quote.send(this);">Send</button>\
										</div>\
									</div>\
								</div>',
								cb: function() {
									$('#page').select();
									
									$('#calendar').calendar(function() {
										$('input[name="sdate"]').val($('#calendar > input[name="date"]').val());
										$('.dGroup > input + div').hide().parent().removeClass('act');
									});
									
									brand_id = 0;
									model_id = 0;
									
									$('#brand > ul').html(cats).sForm({
										action: '/account/allCategories',
										data: {
											lId: clId,
											nIds: Object.keys($('input[name="brand"]').data()).join(','),
											query: $('#brand > .sfWrap input').val()
										},
										all: false,
										select: $('input[name="brand"]').data(),
										s: true
									}, $('input[name="brand"]'), account.getGroup);
									
									$('#type > ul').html(types).sForm({
										action: '/account/allTypes',
										data: {
											lId: tlId,
											nIds: Object.keys($('input[name="type"]').data()).join(','),
											query: $('#type > .sfWrap input').val()
										},
										all: false,
										select: $('input[name="type"]').data(),
										s: true
									}, $('input[name="type"]'));
									
								}
							});
						}, 'json');	
					}, 'json');
			}, 'json');
			}
		}, 'json');
	}, next: function(el) {
		$(el).parent().parent().removeClass('active').next().addClass('active');
	}, prev: function(el) {
		$(el).parent().parent().removeClass('active').prev().addClass('active');
	}, send: function(e, event) {
		var data = {}, err = null;
		
		$(e).parents('.quote-steps').find('.req').removeClass('req');
		
		$(e).parents('.quote-steps').find('[name]').each(function(i, v) {
			var val = $(v).parent().attr('id') ? Object.keys($(v).data())[0] : $(v).val()
			if (['sn', 'os_ver', 'date'].indexOf($(v).attr('name')) < 0 && !val) {
				err = 'Please, fill required fields' + $(v).attr('name');
				$(v).parent().addClass('req');
			} else
				data[$(v).attr('name')] = val;
			
		});
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$(e).attr('disabled', true);
			$.post('/quote', data, function(r) {
				if (r == 'OK') {
					$('.quote-steps textarea, .quote-steps input').val('');
					alr.show({
						class: 'alrSuccess',
						content: 'Your request has been successfully sent.',
						delay: 2
					});
					mdl.close();
				} else if (r == 'email_err') {
					alr.show({
						class: 'alrDanger',
						content: 'Email already exists. Please, login and try again',
						delay: 2
					});
				} else if (r == 'err_date') {
					alr.show({
						class: 'alrDanger',
						content: 'Date can not be less than today',
						delay: 2
					});
				} else if (r == 'enter_req') {
					alr.show({
						class: 'alrDanger',
						content: 'Enter required fields',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				$(e).removeAttr('disabled');
			});
		}
	}
}

function initMap(){
	map = '',
	marker = '',
	bounds = new google.maps.LatLngBounds(),
	geocoder = new google.maps.Geocoder(),
	near = null;
			
	map = new google.maps.Map(document.getElementById('map'), {
		center: locations[0],
		scrollwheel: false,
		zoom: 16,
		mapTypeId: google.maps.MapTypeId['roadmap'] 
	});

	for (n in locations) {
		marker = new google.maps.Marker({
			position: locations[n],
			map: map,
			icon: {
				url: '/templates/site/img/marker.png',
				scaledSize: new google.maps.Size(121, 76)
			},
			infowindow: new google.maps.InfoWindow({
				content: locations[n]['address']+'<br>'+locations[n]['phone']
			})
		});
		bounds.extend(marker.position);
		
		google.maps.event.addListener(marker, 'click', function () {
			this['infowindow'].open(map, this);
		});
	}

	if (locations.length > 1)
		map.fitBounds(bounds);
	else
		map.setZoom(16);
}

function nearStore(v) {
	var addBounds = new google.maps.LatLngBounds();
	for (n in locations) {
		addBounds.extend(locations[n]);
	}
	geocoder.geocode( {'address': 'USA ' + v}, function(results, status) {
		if (near) {
			near.setMap(null);
			near = null;
		}
		if (status == google.maps.GeocoderStatus.OK) {
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location
			});
			addBounds.extend(results[0].geometry.location)
			map.fitBounds(addBounds);
			near = marker;
		}
	});
}

function rad(x) {return x*Math.PI/180;}
function find_closest_marker(lat, lng) {
	if (lat && lng) {
		var R = 6371; // radius of earth in km
		var distances = [];
		var closest = -1;
		for( i=0;i<locations.length; i++ ) {
			var mlat = locations[i]['lat'];
			var mlng = locations[i]['lng'];
			var dLat  = rad(mlat - lat);
			var dLong = rad(mlng - lng);
			var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
				Math.cos(rad(lat)) * Math.cos(rad(lat)) * Math.sin(dLong/2) * Math.sin(dLong/2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
			var d = R * c;
			distances[i] = d;
			if ( closest == -1 || d < distances[closest] ) {
				closest = i;
			}
		}

		$('.hdr-phone').html('<a href="tel:1-'+locations[closest].phone+'">1-'+locations[closest].phone+'</a>');
		$('.neare_store').html(locations[closest].name);
		locations.unshift(...locations.splice(closest,1));
		window.map_id = locations[closest].map_id;
		console.log('ok');
	}
}

$.fn.signature = function(){
	function init(a){
		var cvs = $('<canvas/>')[0];
		var ctx = cvs.getContext("2d");

		if (!ctx) {
			throw new Error("Failed to get canvas' 2d context");
		}

		cvs.width = 500 ;
		cvs.height = 250 ;
		
		ctx.fillStyle = "#fff";
		ctx.strokeStyle = "#444";
		ctx.lineWidth = 1.2;
		ctx.lineCap = "round";

		ctx.fillRect(0, 0, cvs.width, cvs.height);

		ctx.fillStyle = "#3a87ad";
		ctx.strokeStyle = "#3a87ad";
		ctx.lineWidth = 1;
		ctx.moveTo(20,220);
		ctx.lineTo(454,220);
		ctx.stroke();

		ctx.fillStyle = "#fff";
		ctx.strokeStyle = "#444";
		
		
		var disableSave = true;
		var pixels = [];
		var cpixels = [];
		var xyLast = {};
		var xyAddLast = {};
		var calculate = false;
		var inp = $('<input/>', {
			type: 'text',
			name: 'signature',
			required: true
		});

		{
			function remove_event_listeners() {
				cvs.removeEventListener('mousemove', on_mousemove, false);
				cvs.removeEventListener('mouseup', on_mouseup, false);
				cvs.removeEventListener('touchmove', on_mousemove, false);
				cvs.removeEventListener('touchend', on_mouseup, false);

				document.body.removeEventListener('mouseup', on_mouseup, false);
				document.body.removeEventListener('touchend', on_mouseup, false);
			}

			function get_board_coords(e) {
				var x, y;

				if (e.changedTouches && e.changedTouches[0]) {
					var offsety = cvs.offsetTop || 0;
					var offsetx = cvs.offsetLeft || 0;

					x = e.changedTouches[0].pageX - offsetx;
					y = e.changedTouches[0].pageY - offsety;
				} else if (e.layerX || 0 == e.layerX) {
					x = e.layerX;
					y = e.layerY;
				} else if (e.offsetX || 0 == e.offsetX) {
					x = e.offsetX;
					y = e.offsetY;
				}

				return {
					x : x,
					y : y
				};
			};

			function on_mousedown(e) {
				e.preventDefault();
				e.stopPropagation();

				cvs.addEventListener('mousemove', on_mousemove, false);
				cvs.addEventListener('mouseup', on_mouseup, false);
				cvs.addEventListener('touchmove', on_mousemove, false);
				cvs.addEventListener('touchend', on_mouseup, false);

				document.body.addEventListener('mouseup', on_mouseup, false);
				document.body.addEventListener('touchend', on_mouseup, false);

				empty = false;
				var xy = get_board_coords(e);
				ctx.beginPath();
				pixels.push('moveStart');
				ctx.moveTo(xy.x, xy.y);
				pixels.push(xy.x, xy.y);
				xyLast = xy;
			};

			function on_mousemove(e, finish) {
				e.preventDefault();
				e.stopPropagation();

				var xy = get_board_coords(e);
				var xyAdd = {
					x : (xyLast.x + xy.x) / 2,
					y : (xyLast.y + xy.y) / 2
				};

				if (calculate) {
					var xLast = (xyAddLast.x + xyLast.x + xyAdd.x) / 3;
					var yLast = (xyAddLast.y + xyLast.y + xyAdd.y) / 3;
					pixels.push(xLast, yLast);
				} else {
					calculate = true;
				}

				ctx.quadraticCurveTo(xyLast.x, xyLast.y, xyAdd.x, xyAdd.y);
				pixels.push(xyAdd.x, xyAdd.y);
				ctx.stroke();
				ctx.beginPath();
				ctx.moveTo(xyAdd.x, xyAdd.y);
				xyAddLast = xyAdd;
				xyLast = xy;

			};

			function on_mouseup(e) {
				remove_event_listeners();
				disableSave = false;
				ctx.stroke();
				pixels.push('e');
				calculate = false;
				inp.val(cvs.toDataURL("image/png"));
			};

		}
		cvs.addEventListener('mousedown', on_mousedown, false);
		cvs.addEventListener('touchstart', on_mousedown, false);
		$(a).append(cvs)
			.append($('<a/>', {
			href: '#',
			text: 'Clear'
		}).click(function(e){
			e.preventDefault();
			$(a).empty();
			init(a);
			return false;
		})).append(inp);
	}
	this.each(function(){
		init(this);
	});
};

function signatureSave() {
	var canvas = document.getElementById("signature");
	var dataURL = canvas.toDataURL("image/png");
	document.getElementById("saveSignature").src = dataURL;
};

function signatureClear() {
	var parent = document.getElementById("canvas");
	var child = document.getElementById("newSignature");
	parent.removeChild(child);
	empty = true;
	signatureCapture();
	//var canvas = document.getElementById("signature");
	//var ctx = canvas.getContext("2d");
	//ctx.clearRect(0, 0, canvas.width, canvas.height);
}