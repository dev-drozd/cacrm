/**
 /**
 * @appointment Main functions
 * @authors     Alexandr Drozd, Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
var page = 0,
	page_stats = 0,
    query = '',
	model_id = '',
	type_id = '',
	brand_id = '',
	location_id = '',
	object_id = '';
	location_count = 0,
	doload = false,
	status_id = '',
	count_id = 0,
	online_users = null;

// Is valid form
var is_valid = {
    name: function(name) {
        var pattern = new RegExp(/^[a-zA-Za-яА-ЯїЇіІЄєЎўҐґ'0-9-_\.\s]+$/);
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

// Is mobile
function is_mobile() {
    var u = (navigator.userAgent || navigator.vendor || window.opera);
    return /(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(u) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
        u.substr(0, 4)
    );
}

//Scroll width
function scrollWidth() {
    return window.innerWidth - document.documentElement.clientWidth;
}

// Onload page
window.onload = function() {
    window.setTimeout(function() {
            window.addEventListener("popstate", function(e) {
                e.preventDefault();
                if (e.state)
                    Page.get(e.state.link, true);
                else
                    Page.get(window.location.pathname, true);
            }, false);
        },
        1);
    Page.init();
};

$(window).resize(function() {
    menu();
    $('.lMenu').removeClass('show');
});

// Menu events
function menu() {
    $('.dd > a').attr('onclick', '$(this).next().slideToggle(\'fast\');return false;');
}

// Page
var Page = {
	scroll: 0,
	check_top: function(){
		if(Page.scroll > 0 && $(window).scrollTop() >= Page.scroll){
			Page.scroll = 0;
			$('.top_page').attr('onClick', 'Page.up();').find('li').removeClass('fa-arrow-down').addClass('fa-arrow-up');
		} else if($(window).scrollTop() > 500)
			$('.top_page').fadeIn();
		else if(Page.scroll < 1)
			$('.top_page').fadeOut();
	},
	up: function(){
		this.scroll = window.scrollY;
		window.scrollTo(0,0);
		$('.top_page').attr('onClick', 'Page.down();').find('li').removeClass('fa-arrow-up').addClass('fa-arrow-down');
	},
	down: function(e){
		window.scrollTo(0, this.scroll);
		$('.top_page').attr('onClick', 'Page.up();').find('li').removeClass('fa-arrow-down').addClass('fa-arrow-up');
		this.scroll = 0;
	},
    init: function(href) {
        page = 0, query = '';
		doload = false;
        menu();
        $('#page').checkbox();
        $('#page').select();
        $('#page').tabs();
        $('#page').radio();
        if ($(window).width() <= 992) $('.lMenu').removeClass('show');
        $('.dd > ul').slideUp('fast');
		$('.navMore').slideUp('fast');
		clearInterval(online_users);
		if (!$('.new_guest').length)
			mdl.close();
    },
    get: function(href, back){
		$('body').addClass('pre');
		$('.notifications > .nfArea').hide();
        if (!back) {
            history.pushState({
                link: href
            }, null, href);
        }
        $.getJSON(href, function(r) {
            if (r == 'loggout') location.reload();
            document.title = r.title;
			if(r.new_msg > 0){
				var mntf = $('#new_msg > a'), sp = mntf.find('.newMsg');
				if(sp.length){
					sp.text(r.new_msg);
				} else {
					mntf.append($('<span/>', {
						class: 'newMsg',
						text: r.new_msg
					}));
				}
			} else {
				$('#new_msg > a > .newMsg').remove();
			}
			if(r.notify_count)
				$('.notifications > #notify').addClass('nfCount nfc').text(r.notify_count);
			else
				$('.notifications > #notify').removeClass('nfCount nfc').text('');
            $('#cpu').text(r.cpu);
            $('#time_script').text(r.time_script);
            $('#compile_tpl').text(r.compile_tpl);
            $('#query_cache').html(r.query_cache);
            $('#query_db').html(r.query_db);
            $('#points_top').html(r.points);
            $('#page').html(r.content);
			$('body').removeClass('pre');
            $(window).scrollTop(0);
			Page.scroll = 0;
			$('.top_page').hide();
            Page.init(href);
			
			/* if (r.purchases) {
				$.each(p_timer, function(i, v) {
					if (!r.purchases[i]) {
						clearInterval(p_timer[i]);
						delete p_timer[i];
					}
				});
				
				$.each(r.purchases, function(i, v) {
					if (!p_timer[i]) {
						p_timer[v.id] = setInterval(function() {
							show_notif(v);
						}, 300000);
					}
				});
			} */
        });
    }
};

var barTimer = undefined;

function devBarcode(t){
	clearTimeout(barTimer);
	var e = t.match(/DE\s([0-9]+)[.*]?/i);
	if(e){
		barTimer = setTimeout(function(){
			$.post('/ссылка', {
				id: parseInt(e[2])
			}, function(){
				
			});
		}, 200);	
	}
}

function checkBarcode(t){
	clearTimeout(barTimer);
	console.log(t);
	var e = t.match(/(US|IS|DE|OB|PU|IN)\s([0-9]+)[.*]?/i), l, i;
	if(e){
		barTimer = setTimeout(function(){
			console.log(e);
			i = parseInt(e[2])
			switch(e[1]){
				case 'US': l = '/users/view/'; break;
				case 'IS': l = '/issues/view/'; break;
				case 'DE': l = '/inventory/view/'; break;
				case 'OB': l = '/objects/edit/'; break;
				case 'PU': l = '/purchases/edit/'; break;
				case 'IN': l = '/invoices/view/'; break;
			}
			return l ? Page.get(l+i) : false;
		}, 200);	
	}
	return barTimer;
}

var searchTimer = undefined;

function mainSearch(a){
	if(a.length){
		clearTimeout(searchTimer);
		searchTimer = setTimeout(function(){
			$.post('/search', {
				q: a,
				type: $("[name='sGroup']").val()
			}, function(j){
				$('.searchResult').html(j.content).show();
			}, 'json');
		}, 200);
	} else
		$('.searchResult').hide();
}


// Search
var Search = function(a, t){
    query = a;
    $.post(location.pathname, {
        query: a,
		event: $('select[name="searchSel"]').val() || '',
		object: $('select[name="object"]').length ?  $('select[name="object"]').val() : Object.keys($('input[name="object"]').data() || {}).join(','),
		date_start: $('#calendar > input[name="date"]').val(),
		date_finish: $('#calendar > input[name="fDate"]').val(),
		status: $('select[name="status"]').val(),
		type: t || $('select[name="type"]').val(),
		group: $('select[name="group"]').val(),
		instore: $('input[name="instore"]').val(),
		pickedup: $('input[name="pickedup"]').val(),
		all: $('input[name="all"]').val(),
		updated: $('input[name="updated"]').val(),
		action: $('select[name="action"]').val(),
		payment: $('select[name="payment"]').val(),
		rating: $('select[name="rating"]').val(),
		staff: Object.keys($('input[name="staff"]').data() || {}).join(','),
		create: Object.keys($('input[name="create"]').data() || {}).join(','),
		confirm: Object.keys($('input[name="confirm"]').data() || {}).join(','),
		inv_type: Object.keys($('input[name="inv_type"]').data() || {}).join(','),
		profit: $('input[name="profit"]').val(),
		verified: $('input[name="verified"]').val(),
		craiglist: $('input[name="craiglist"]').val(),
		salary: $('input[name="salary"]').val(),
		tax: $('input[name="tax"]').val(),
		hours: $('input[name="hours"]').val()
    }, function(r) {
        page = 0;
		doload = false;
        $('#res_count').text(r.res_count);
		/* if ($('#calendar > input[name="date"]').length) {
			$('.userList').removeClass($('#calendar > input[name="date"]').val() ? 'tbl' : '')
					.addClass($('#calendar > input[name="date"]').val() ? '' : 'tbl');
		} */
		$('.userList').html(r.content);
        if (r.left_count > 0) $('.btnLoad').removeClass('hdn');
        else $('.btnLoad').addClass('hdn');
		
		if (r.salary) 
			$('#salary').show();
		else if ($('#salary').length)
			$('#salary').hide();
		
		if (r.hours) 
			$('#hours').show();
		else if ($('#hours').length)
			$('#hours').hide();
		
		if ($('.filterCtnr').length) $('.filterCtnr').hide();
		if ($('.totalTime').length && r.total) {
			if (r.total.total_sale) {
				$('#purchase').html(r.total.total_purchase);
				$('#sale').html(r.total.total_sale);
				$('#proceed').html(r.total.total_proceed);
			} else 
				$('.totalTime > span').html(r.total);
			if (parseInt(r.total_q))
				$('#total_q').html(r.total_q);
		} 
    }, 'json');
};

$(window).scroll(function() {
	if (!doload && $('.userList').length && !$('.btnLoad').hasClass('hdn')) {
		if ($(window).scrollTop() + $(window).height() >= $(document).height() - 500) {
			doload = true;
			Doload(document.getElementsByClassName('btnLoad')[0]);
		}
	}
});

// Doload
var Doload = function(e) {
	$(e).find('span').addClass('fa-spin');
    page += 1;
	$.post(($(e).attr('action') || location.pathname), {
		query: query,
		page: ($(e).attr('page') || page),
		id: ($(e).attr('data-id') || 0),
		event: $('select[name="searchSel"]').val() || '',
		object: $('select[name="object"]').length ?  $('select[name="object"]').val() : Object.keys($('input[name="object"]').data() || {}).join(','),
		date_start: $('#calendar > input[name="date"]').val(),
		date_finish: $('#calendar > input[name="fDate"]').val(),
		status: $('select[name="status"]').val(),
		group: $('select[name="group"]').val(),
		type: $('select[name="type"]').val(),
		instore: $('input[name="instore"]').val(),
		all: $('input[name="all"]').val(),
		updated: $('input[name="updated"]').val(),
		pickedup: $('input[name="pickedup"]').val(),
		action: $('select[name="action"]').val(),
		payment: $('select[name="payment"]').val(),
		rating: $('select[name="rating"]').val(),
		staff: Object.keys($('input[name="staff"]').data() || {}).join(','),
		create: Object.keys($('input[name="create"]').data() || {}).join(','),
		confirm: Object.keys($('input[name="confirm"]').data() || {}).join(','),
		inv_type: Object.keys($('input[name="inv_type"]').data() || {}).join(','),
		profit: $('input[name="profit"]').val(),
		verified: $('input[name="verified"]').val(),
		craiglist: $('input[name="craiglist"]').val(),
		salary: $('input[name="salary"]').val(),
		tax: $('input[name="tax"]').val(),
		hours: $('input[name="hours"]').val()
	}, function(r) {
		if (r.left_count >= 0) {
			$(e).find('span').removeClass('fa-spin');
			if ($(e).attr('action')) {
				$(e).parent().find('.tBody').append(r.content);
				$(e).attr('page', parseInt($(e).attr('page')) + 1);
			} else
				$('.userList').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
			$('#page').checkbox();
			$('#page').radio();
			if (r.total && $('#total').length) $('#total').html((parseFloat($('#total').text()) + parseFloat(r.total)).toFixed(2));
			doload = false;
		}
	}, 'json');
};

var quote = {
	createApp: function(f,e,i){
		e.preventDefault();
		loadBtn.start($(e.target).find('button[type="submit"]'));
		$.post('/quote/create_app', $(f).serializeArray(), function(r){
			if(r > 0)
				Page.get('/users/add_appointment?user='+r);
			loadBtn.stop($(e.target).find('button[type="submit"]'));
		});
		return false;
	}
};

// Settings
var Settings = {
	delFranchise: function(id) {
		$.post('/settings/delFranchise', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
                    class: 'alrSuccess',
                    content: 'Client was successfully deleted',
                    delay: 2
                });
				$('#client_' + id).addClass('deleted').find('.uMore li:last-child').remove();
			} else {
				alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
			}
		});
	},
	sendFranchise: function(f, event, id) {
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
            } else if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = 'Name can not be empty';
                    break;
                }
            } else if (form[i].name == 'timezone') {
                if (!form[i].value.length) {
                    err = 'Timezone can not be empty';
                    break;
                }
            } else if (form[i].name == 'ip') {
                if (!form[i].value.length) {
                    err = 'IP can not be empty';
                    break;
                }
            } else if (form[i].name == 'contract') {
                if (!form[i].value.length) {
                    err = 'Contract can not be empty';
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
        

        if (phones.length)
            data.append('phone', phones);
        else if (!err)
            err = 'Phone can not be empty';
		
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
            url: '/settings/sendFranchise',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (parseInt(r) > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: 'Client was successfully ' + (id ? 'saved' : 'added'),
                    delay: 2
                });
                Page.get('/settings/franchise/edit/' + r);
            } else {
				alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
			}
        });
    },
	sendRefDiscounts: function(el, event) {
		event.preventDefault();
		var data = {}, arr = [], err = null;
		$('.refPoints').each(function(i, v) {
			if ($.inArray($(v).find('input[name="ref"]').val(), arr) < 0) {
				arr[i] = $(v).find('input[name="ref"]').val();
				data[i] = {
					ref: $(v).find('input[name="ref"]').val(),
					discount: $(v).find('input[name="discount"]').val()
				}
			} else {
				err = 'You enter discount amount for ' + $(v).find('input[name="ref"]').val() + ' refferals twice';
			}
		});
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			$.post('/settings/send_ref_discounts', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Discounts were successfully sent',
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
	addRefDicount: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Minial refferals'
				})
			}).append($('<input/>', {
				type: 'number',
				name: 'ref'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Discount %'
            })
        }).append($('<input/>', {
            type: 'number',
            name: 'discount'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
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
						content: 'Points were successfully sent',
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
	addCurrency: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Name'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'name'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Symbol'
            })
        }).append($('<input/>', {
            type: 'text',
            name: 'symbol'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
	addEmail: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Login'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'login'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Password'
            })
        }).append($('<input/>', {
            type: 'password',
            name: 'password'
        }))).append($('<div/>', {
			class: 'sSide rSide',
			html: $('<label/>', {
				html: 'Default'
			})
		}).append($('<input/>', {
			type: 'checkbox',
			name: 'default',
			onchange: 'Settings.defaultEmail(this);'
		}))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        }))); 
		
		$('input[name="checkbox"]').checkbox();
    },
	sendCurrency: function(el, event) {
		event.preventDefault();
		var data = {}, arr = [], err = null;
		$('.refPoints').each(function(i, v) {
			if ($.inArray($(v).find('input[name="name"]').val(), arr) < 0) {
				arr[i] = $(v).find('input[name="name"]').val();
				data[i] = {
					name: $(v).find('input[name="name"]').val(),
					symbol: $(v).find('input[name="symbol"]').val()
				}
			} else {
				err = 'You enter the same currency twice';
			}
		});
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			$.post('/settings/send_currency', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Currency was successfully sent',
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
	defaultEmail: function(e, event) {
		if ($(e).val() == 1) {
			$('input[name="default"][value="1"]').attr('onchange', '').val(0).trigger('click');
			$(e).val(1).trigger('click');
			$('input[name="default"]').attr('onchange', 'Settings.defaultEmail(this);');
		}
	},
	sendEmails: function(el, event) {
		event.preventDefault();
		var data = {}, arr = [], err = null;
		$('.optGroup').each(function(i, v) {
			if ($(v).find('input[name="login"]').val() && $(v).find('input[name="password"]').val()) {
				if ($.inArray($(v).find('input[name="login"]').val(), arr) < 0) {
					arr[i] = $(v).find('input[name="login"]').val();
					data[i] = {
						login: $(v).find('input[name="login"]').val(),
						password: $(v).find('input[name="password"]').val(),
						default: $(v).find('input[name="default"]').val()
					}
				} else {
					err = 'You enter the same email twice';
				}
			}
		});
		if (err) {
			alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
		} else {
			$.post('/settings/send_emails', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Emails was successfully sent',
						delay: 2
					});
				} else if (r == 'no_def') {
					alr.show({
						class: 'alrDanger',
						content: 'Set default email, please',
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
						content: 'Mark-up was successfully sent',
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
	saveSMS: function(f, event) {
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
        $.post('/settings/save_sms', data, function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[1],
                    delay: 2
                })
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
    addGroup: function(id, name, pay, timer, week_hours, rating) {
        mdl.open({
            id: 'editGroup',
            width: 500,
            title: lang[144],
            content: '<div class="iGroup"><label>Group name:</label><input type="text" name="grName" value="' + (name || '') + '"></div>\
					<div class="iGroup"><label>Paying per hour:</label><input type="checkbox" name="pay" ' + (pay == 1 ? 'checked' : '') + '></div>\
					<div class="iGroup"><label>Timer:</label><input type="checkbox" name="timer" ' + (timer == 1 ? 'checked' : '') + '></div>\
					<div class="iGroup"><label>Rating:</label><input type="checkbox" name="rating" ' + (rating == 1 ? 'checked' : '') + '></div>\
					<div class="iGroup"><label>Week hours:</label><input type="number" step="0.001" name="week_hours" value="' + week_hours + '"></div>\
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
            data.rating = $('input[name="rating"]').val();
            data.week_hours = $('input[name="week_hours"]').val();
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
			if (['INPUT', 'SELECT', 'TEXTAREA'].indexOf(this.tagName) >= 0) {
				var val = $(this).val();
				if (!val) {
					err = this.name + ' ' + lang[142];
					return false;
				}
				data[this.name] = val;
			}
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
            else if (r == 'no_del') {
                alr.show({
                    class: 'alrDanger',
                    content: 'You can not delete this form',
                    delay: 2
                })
            } else {
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

// Webcam photo
var webcam = {
	width: 470,    
	height: 0,
  	streaming: false,
	video: null,
	canvas: null,
	photo: null,
	startbutton: null,
	startup: function() {
        webcam.video = document.getElementById('video');
        webcam.canvas = document.getElementById('canvas');
        webcam.photo = document.getElementById('photo');
        webcam.startbutton = document.getElementById('startbutton');

        navigator.getMedia = ( navigator.getUserMedia ||
                                navigator.webkitGetUserMedia ||
                                navigator.mozGetUserMedia ||
                                navigator.msGetUserMedia);

        navigator.getMedia({
                video: true,
                audio: false
            },
            function(stream) {
                if (navigator.mozGetUserMedia) {
                    webcam.video.mozSrcObject = stream;
                } else {
                    var vendorURL = window.URL || window.webkitURL;
                    webcam.video.src = vendorURL.createObjectURL(stream);
                }
                webcam.video.play();
            },
            function(err) {
                console.log("An error occured! " + err);
            }
        );

        webcam.video.addEventListener('canplay', function(ev){
            if (!webcam.streaming) {
                webcam.height = webcam.video.videoHeight / (webcam.video.videoWidth/webcam.width);
                
                if (isNaN(webcam.height)) {
                    webcam.height = webcam.width / (4/3);
                }
                
                webcam.video.setAttribute('width', webcam.width);
                webcam.video.setAttribute('height', webcam.height);
                webcam.canvas.setAttribute('width', webcam.width);
                webcam.canvas.setAttribute('height', webcam.height);
                webcam.streaming = true;
            }
        }, false);

        webcam.startbutton.addEventListener('click', function(ev){
            webcam.takepicture();
            ev.preventDefault();
        }, false);

        webcam.clearphoto();
    }, clearphoto: function () {
        var context = webcam.canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, webcam.canvas.width, webcam.canvas.height);

        var data = webcam.canvas.toDataURL('image/png');
        webcam.photo.setAttribute('src', data);
    }, takepicture: function () {
        var context = webcam.canvas.getContext('2d');
        if (webcam.width && webcam.height) {
            webcam.canvas.width = webcam.width;
            webcam.canvas.height = webcam.height;
            context.drawImage(webcam.video, 0, 0, webcam.width, webcam.height);

            var data = webcam.canvas.toDataURL('image/png');
            webcam.photo.setAttribute('class', '');
            webcam.photo.setAttribute('src', data);
        } else {
            webcam.clearphoto();
        }
    }
};

// Base64 to blob
function b64Blob(b64Data, contentType) {
	var byteCharacters = atob(b64Data);
	var byteNumbers = new Array(byteCharacters.length);
	for (var i = 0; i < byteCharacters.length; i++) {
		byteNumbers[i] = byteCharacters.charCodeAt(i);
	}
	var byteArray = new Uint8Array(byteNumbers);
	return new Blob([byteArray], {type: contentType});
}

// Users
var user = {
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
					mdl.open({
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
								onclick: 'user.sendService(this);',
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
								s: true,
								link: 'objects/edit'
							}, $('input[name="object"]'), user.getOnsite);
							
							user.getOnsite();

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
					});
				}, 'json');
			}, 'json'); 
		}, 'json'); 
	},
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
				mdl.open({
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
							onclick: 'user.sendSuspention(this, ' + id + ', ' + r.third + ')',
							html: 'Send'
						})
					})),
					cb: function() {
						$('#page').select();
					}
				});
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
		mdl.open({
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
					onclick: 'user.sendWriteup(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		});
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
	editOnsite: function(id) {
		date = $('#onsite_' + id + ' .dateEnd').html();
		if (date.indexOf('inf')) 
			date = '';
		else 
			date = date.split('-');
		mdl.open({
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
					onclick: 'user.sendEditOnsite(' + id + ', this);',
					html: 'Done'
				})
			})),
			cb: function () {
				$('#calendar').calendar(function() {
					$('input[name="date_end"]').val($('#calendar > input[name="date"]').val());
					$('.dGroup > input + div').hide().parent().removeClass('act');
				});
			}
		});
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

                    mdl.open({
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
                                onclick: 'user.sendUpdateStaff(' + id + ', this);',
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
                    });
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
		user.spage += 1;
		$.post('/users/onsite_stats', {
			id: id, 
			page: user.spage
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
                mdl.open({
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
                            s: true,
							link: 'objects/edit'
                        }, $('input[name="object"]'), user.getOnsite);
						
						user.getOnsite();

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
                });
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


// Objects
var objects = {
	addFormula: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Min price'
				})
			}).append($('<input/>', {
				type: 'number',
				step: '0.001',
				name: 'min_price'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Upcharge price'
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
    addExpanses: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup optGroup refPoints',
            html: $('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
					html: 'Name'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'ex_name'
			}))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: 'Value'
            })
        }).append($('<input/>', {
            type: 'number',
            name: 'ex_value',
			step: '0.001'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
    add: function(f, event, id) {
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
            } else if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = lang[8];
                    break;
                }
            } else if (form[i].name == 'work_time_start' || form[i].name == 'work_time_end') {
                if (!form[i].value.length) {
                    err = 'Working time can not be empty';
                    break;
                }
            } else if (form[i].name == 'timezone') {
                if (!form[i].value.length) {
                    err = 'Timezone can not be empty';
                    break;
                }
            }
            data.append(form[i].name, form[i].value);
        }
        if (id) {
            data.append('managers', Object.keys($('input[name="managers"]').data()).join(','));
            data.append('staff', Object.keys($('input[name="staff"]').data()).join(','));
        }
		
		var options = {};
		if ($('#expanses .refPoints').length) {
			$('#expanses .refPoints').each(function(i, v) {
				options[$(v).find('input[name="ex_name"]').val()] = $(v).find('input[name="ex_value"]').val();
			});
		}
		data.append('options', JSON.stringify(options));
		
		var purchase_price = {}, arr = [];
		if ($('#purchase_price .refPoints').length) {
			$('#purchase_price .refPoints').each(function(i, v) {
				if ($.inArray($(v).find('input[name="min_price"]').val(), arr) < 0) {
					arr[i] = $(v).find('input[name="min_price"]').val();
					purchase_price[$(v).find('input[name="min_price"]').val()] = $(v).find('input[name="mark-up"]').val();
				} else {
					err = 'You enter mark-up amount for ' + $(v).find('input[name="min_price"]').val() + ' refferals twice';
				}
			});
		}
		data.append('purchase_price', JSON.stringify(purchase_price));
		
        var files = $('.dragndrop')[0].files;
        if (!$.isEmptyObject(files)) {
            for (var n in files) {
                data.append('image', files[n]);
            }
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
        var thumb = $('.dragndrop').prev();
        if (thumb[0].tagName != 'FIGURE') data.append('del_image', true);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/objects/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id) Page.get('/objects/edit/' + r);
				else Page.get('/objects/');
            }
        });
    },
    sel: function(objId, g, t, d) {
        $.post('/users/all', {
            id: objId,
            gId: g,
            nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '" data-image="' + v.image + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_' + objId,
                    title: t,
                    content: '<ul class="hdn">' + items + '</ul>\
						<div class="sGroup"><button class="btn btnMdl" onclick="objects.get(\'' + d + '\', ' + objId + ');">' + lang[139] + '</button></div>',
                    cb: function() {
                        nIds = [];
                        $('#select_' + objId).find('ul').sForm({
                            action: '/users/all',
                            data: {
                                lId: lId,
                                id: objId,
                                gId: g,
                                query: $('#select_' + objId).find('input').val(),
                                nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="' + d + '"]').data(),
							link: 'users/view'
                        }, $('input[name="' + d + '"]'));
                    }
                });
            }
        }, 'json');
    },
    get: function(el, id) {
        //$('#select_' + id).find('ul').sForm('get', $('input[name="' + el + '"]'));
        mdl.close();
        userAppend($('#managers'), $('input[name="managers"]').data());
        userAppend($('#staff'), $('input[name="staff"]').data());
    },
    del: function(id) {
        $.post('/objects/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#object_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    rent: function() {
        if ($('input[name="rent_cost"]').val().length)
            $('#rentTotal').text((parseFloat($('input[name="rent_cost"]').val()) + parseFloat($('input[name="rent_cost"]').val() * $('input[name="tax"]').val() / 100)).toFixed(2));
    },
    openLocation: function(id, n, o, c) {
		mdl.open({
			id: 'openLocation',
			title: (id ? lang[17] : lang[18]) + ' ' + lang[92],
			content: '<div class="iGroup">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name" value="' + (n || '') + '">\
				</div>\
				<div class="iGroup">\
					<label>Count</label>\
					<input type="text" name="count" value="' + (c || '') + '">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit" onclick="objects.sendLocation(this, ' + (id || 0) + ', ' + (o || 0) + ')">' + (id ? lang[17] : lang[18]) + '</button>\
				</div>'
		});
    },
    sendLocation: function(f, id, o) {
        f = $(f).parents('.mdlBody');
        var data = {
                id: id
            },
            err = null;
        f.find('input').css('border-color', '#ddd');
        loadBtn.start(f.find('button[type="submit"]'));
        if (!f.find('input[name="name"]').val().length) {
            err = lang[25];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
            data.object = o;
            data.count = f.find('input[name="count"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop(s.find('button[type="submit"]'));
        } else {
            $.post('/objects/save_location', data, function(r) {
                loadBtn.stop(f.find('button[type="submit"]'));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
                    console.log('fd');
                    mdl.close('openLocation');
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
    delLocation: function(id) {
        $.post('/objects/del_location', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#location_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	report: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/objects/report_result', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
					if ($('#report').next().hasClass('profile-loader'))
						$('#report').next().remove();
				} else {
					$('#report').empty();
				}
				$('#page').select();
				$('#page').tabs();
			}, 'json');
		}
	}, 
	miniReport: function(el, v, id) {
		loadBtn.start($(el));
		var data = {
			object: id,
			date_start: $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar > input[name="fDate"]').val(),
			type: v,
			status: $('#eo_' + id + ' select[name="status"]').val()
		}
		
		$.post('/objects/mini_reports', data, function(r) {
			loadBtn.stop($(el));
			if (r) {
				if ($('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).length)
					$('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).replaceWith(r);
				else
					$('#reps_' + id).prepend(r);
			}
		});
	}
}

// Options
var options = {
    dragEl: {},
    parentEl: {},
    clickEl: {},
    add: function(e) {
        var select = new Object,
            opts = ['input', 'number', 'textarea', 'checkbox', 'select'];
        select = $('<select/>', {
            name: 'type'
        }).change(function() {
            if ($(this).val() == 'select') {
                $(this).parents('.optGroup').append($('<div/>', {
                    class: 'selArea',
                    html: $('<div/>', {
                        class: 'dClear'
                    })
                }).append($('<label/>', {
                    html: lang[137]
                })).append($('<input/>', {
                    type: 'checkbox',
                    name: 'sMul'
                })).append($('<div/>', {
                    class: 'dClear'
                })).append($('<div/>', {
                    class: 'selOpts',
                    html: $('<label/>', {
                        class: 'lTitle',
                        html: lang[136]
                    })
                })).append($('<div/>', {
                    class: 'aRight',
                    html: $('<button/>', {
                        class: 'btn atnAo',
                        onclick: 'options.addSel(this); return false;',
                        html: lang[135]
                    })
                }))).checkbox();
            } else {
                if ($(this).parents('.optGroup').find('.selArea')) {
                    $(this).parents('.optGroup').find('.selArea').remove()
                }
            }
        });
        $.each(opts, function(i, val) {
            select.append($('<option/>', {
                value: val,
                html: lang[val]
            }));
        });
        $(e).parent().before($('<div/>', {
            class: 'iGroup optGroup',
            html: $('<div/>', {
                class: 'sSide',
                html: $('<label/>', {
                    html: lang[134]
                })
            }).append($('<input/>', {
                name: 'oName'
            }))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: lang[127]
            })
        }).append(select)).append($('<div/>', {
            class: 'sSide rSide',
            html: $('<label/>', {
                html: lang[133]
            })
        }).append($('<input/>', {
            type: 'checkbox',
            name: 'req'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })).prepend($('<span/>', {
            class: 'fa fa-bars',
            draggable: true,
            ondragover: 'options.dragover(event)',
            ondragstart: 'options.dragstart(event)',
            ondragend: 'options.dragend(event)',
            onmousedown: '$(this).parent().addClass(\'drag\');',
            onmouseup: '$(this).parent().removeClass(\'drag\');'
        })));
        $('#page').select();
        $('#page').checkbox();
    },
    addSel: function(el) {
        $(el).parents('.selArea').find('.selOpts').append($('<div/>', {
            class: 'dClear',
            html: $('<div/>', {
                class: 'sSide w100',
                html: $('<input/>', {
                    name: 'opt',
                    placeholder: lang[132] + '...'
                })
            })
        }).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })).prepend($('<span/>', {
            class: 'fa fa-bars opt',
            draggable: true,
            ondragover: 'options.dragover(event)',
            ondragstart: 'options.dragstart(event)',
            ondragend: 'options.dragend(event)',
            onmousedown: '$(this).parent().addClass(\'drag\');',
            onmouseup: '$(this).parent().removeClass(\'drag\');'
        })));
    },
    dragstart: function(e) {
        options.clickEl = e.target;
        options.dragEl = e.target.parentNode;
        options.parentEl = options.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', options.dragEl.textContent);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        options.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== options.clickEl && target.className == options.clickEl.className) {
            if (target.parentNode.nextSibling == options.dragEl)
                options.parentEl.insertBefore(options.dragEl, target.parentNode);
            else
                options.parentEl.insertBefore(options.dragEl, target.parentNode.nextSibling);
        }
    }
}

// Inventory
var inventory = {
	delTransfer: function(id, el) {
		$.post('/inventory/del_transfer', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Transfer was succefully deleted',
					delay: 2
				});
				if ($('#transfer_' + id).length) {
					$('#transfer_' + id).addClass('deleted');
					$(el).parent().remove();
				} else 
					$(el).parents('.uMore').remove();
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
	confirmTransferMdl: function(id) {
		mdl.open({
			id: 'transferpopup',
			title: 'Transfer confirmation',
			content: $('<div/>', {
				class: 'transferConf',
				html: $('<div/>', {
					html: lang[167]
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					type: 'button',
					onclick: 'inventory.confirmTransfer(' + id + ');',
					html: 'Yes'
				})
			}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'No'
				})))
		});
	},
	confirmTransfer: function(id) {
		$.post('/inventory/confirm_transfer', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Transfer was succefully confirmed',
					delay: 2
				});
				if ($('#transfer_' + id).length)
					$('#transfer_' + id).removeClass('not-confirmed').addClass('confirmed');
				$('#confirm_tr_' + id).remove();
				if ($('.mdl').length)
					mdl.close();
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
	confirmTransferRequest: function(id, el) {
		$.post('/inventory/confirm_transfer_request', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Transfer request was succefully confirmed',
					delay: 2
				});
				if ($('#transfer_' + id).length)
					$('#transfer_' + id).removeClass('request');
				$(el).parent().remove();
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
	transfer: function(el, event, request) {
		event.preventDefault();
		loadBtn.start($(el).find('button[type="submit"]'));
		var data = {},
			err = null;
		if (!Object.keys($('input[name="from_object"]').data()).join(',')) {
			err = 'Please, select store, witch inventory will be send from';
		} else if (!Object.keys($('input[name="to_object"]').data()).join(',')) {
			err = 'Please, select store, witch inventory will be send to';
		} else if (Object.keys($('input[name="to_object"]').data()).join(',') == Object.keys($('input[name="from_object"]').data()).join(',')) {
			err = 'It can not be the same store';
		} else if (!request && !Object.keys($('input[name="receive_staff"]').data()).join(',')) {
			err = 'Plese, select staff who will be received inventory';
		} else if (!$('input[name="trIds"]').val()) {
			err = 'You must transfer something!';
		} else {
			data.from_object = Object.keys($('input[name="from_object"]').data()).join(',');
			data.to_object = Object.keys($('input[name="to_object"]').data()).join(',');
			data.receive_staff = !request ? Object.keys($('input[name="receive_staff"]').data()).join(',') : 0;
			data.trItems = $('input[name="trIds"]').val();
			data.request = request || 0;
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el).find('button[type="submit"]'));
		} else {
			$.post('/inventory/make_transfer', data, function(r) {
				loadBtn.stop($(el).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Inventory was successfully transfered',
						delay: 2
					});
					Page.get('/inventory/transfer');
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
		}
	},
	openStoreStatus: function(id, name) {
		mdl.open({
			id: 'openStoreStatus',
			title: (id ? lang[17] : lang[18]) + lang[121],
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
					onclick: 'inventory.sendStoreStatus(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		});
    },
    sendStoreStatus: function(el, id) {
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
            $.post('/inventory/sendStoreStatus', data, function(r) {
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
                    mdl.close('openStoreStatus');
                    Page.get(location.href);
                }
            }, 'json');
        }
    },
    delStoreStatus: function(id) {
        if (id) {
            $.post('/inventory/delStoreStatus', {
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
	ownerPrice: function(id, t, el) {
		$(el).html($('<input/>', {
			type: 'number',
			name: 'ownerPrice_' + id,
			value: $(el).html().replace(/[^0-9.]/g, ''),
			min: 0,
			step: '0.001',
			class: 'quickEdit',
			oldvalue: $(el).html().replace(/[^0-9.]/g, ''),
			currency: $(el).html().replace(/[0-9.\s]/g, ''),
			onblur: 'inventory.sendOwnerPrice(' + id + ', \'' + t + '\', this);'
		}));
		$(el).find('input').focus();
	},
	sendOwnerPrice: function(id, t, el) {
		if (!$('input[name="ownerPrice_' + id +'"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Price can not be empty',
				delay: 2
			});
		} else {
			$.post('/inventory/owner_price', {
				id: id,
				type: t,
				price: $('input[name="ownerPrice_' + id +'"]').val()
			}, function(r) {
				$(el).parent().html($(el).attr('currency') + (r == 'OK' ? $(el).val() : $(el).attr('oldvalue')));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Price was successfully updated',
						delay: 2
					});
				} else if (r == 'no_acc') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do this',
						delay: 2
					});
				} else if (r == 'empty') {
					alr.show({
						class: 'alrDanger',
						content: 'Price can not be 0 or less',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: err,
						delay: 2
					});
				}
			});
		}
	},
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
	confirmed_flag: 0,
	confirmed: function(id, el) {
		if (this.confirmed_flag)
			return false;
		this.confirmed_flag = 1;
		$(el).removeAttr('onlick');
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
				$(el).attr('onclik', 'inventory.confirmed(' + id + ', this); return false;');
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			inventory.confirmed_flag = 0;
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
	sendWarrantyPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('status_', ''),
                pr: $(v).find('input').val()
            }
        });
        if (data) {
            $.post('/inventory/stWarrantyPriority', data, function(r) {
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
					type: $('input[name="category"]').length ? "category" : "brand"
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
                items = '', lId = 0;
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
        } else if (form.find('input[name="smodel"]').length && !form.find('input[name="smodel"]').val()) {
            err = 'Model specification can not be empty';
		} else if (form.find('input[name="serial"]').length && !form.find('input[name="serial"]').val() && (!$(f).find('input[name="quantity"]').val() || $(f).find('input[name="quantity"]').val() == 1)) {
            err = 'Serial number can not be empty';
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
                data.append('currency', $(f).find('select[name="currency"]').val() || '');
                data.append('purchase_currency', $(f).find('select[name="purchase_currency"]').val() || '');
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
                data.append('store_status', Object.keys(form.find('input[name="store_status"]').data() || {}).join(','));
                
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
						content:  'Min sale price can not be less than $' + parseFloat(min_price(parseFloat($(f).find('input[name="purchase-price"]').val()))).toFixed(2),
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
    openStatus: function(id, data, name, forfeit, sms, smsForm, warranty, percent) {
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
					
					
					if (sms == 1) $('input[name="sms"]').val(1).trigger('click');
					if (forfeit != 0) $('input[name="fine"]').val(1).trigger('click');
					if (percent == 1) $('input[name="percent"]').val(1).trigger('click');
					if (data) {
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
				}).append(warranty == 0 ? $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: '% from total'
					})
				}).append($('<input/>', {
					type: 'checkbox',
					name: 'percent',
					value: (percent ? 1 : 0)
				})) : '').append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: lang[120]
					})
				}).append($('<input/>', {
					type: 'checkbox',
					onchange: 'inventory.changeStType(this);',
					name: 'fine'
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
						onclick: 'inventory.sendStatus(this, ' + id + (warranty ? ', 1' : '') + ')',
						html: id ? lang[113] : lang[91]
					})
				}))
			});
		}, 'json')
    },
	changeStSms: function(e) {
		if ($(e).val() > 0) 
			$('#sms').removeClass('hdn');
		else 
			$('#sms').addClass('hdn');
	},
    sendStatus: function(el, id, warranty) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
		if (warranty) data.warranty = 1;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = lang[16];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
			data.forfeit = f.find('input[name="forfeit"]').val();
			data.sms = f.find('input[name="sms"]').val();
			data.percent = f.find('input[name="percent"]').val();
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
    delStatus: function(id, warranty) {
        if (id) {
            $.post('/inventory/delStatus', {
                id: id,
				warranty: warranty ? 1 : 0
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
    sendLocation: function(f, event, id, warranty) {
        event.preventDefault();
        var data = {
                id: id
            },
            err = null;
		if (warranty) data.warranty = 1;
        $(f).find('input').css('border-color', '#ddd');
        loadBtn.start($(event.target).find('button[type="submit"]'));
        data.loc = Object.keys($('input[name="location"]').data() || {}).join(',');
        data.service = $('input[name="service"]').val();
        data.inventory = $('input[name="inventories"]').val();
        data.purchase = $('input[name="purchase"]').val();
        data.remove_purchase = $('input[name="remove_purchase"]').val();
        data.note = $('input[name="note"]').val();
		if (!warranty)
			data.assigned = $('input[name="assigned"]').val();
		
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
					Page.get('/inventory/' + (warranty ? 'warranty_statuses' : 'types/status'));
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
            } else if (r == 'no_images') {
                alr.show({
                    class: 'alrDanger',
                    content: 'Stock must have enough one image',
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

// Orders
var purchases = {
	cpage: 0,
	reciveStock: function(id) {
		mdl.open({
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
		});
	},
	receiveMdl: function(id) {
		mdl.open({
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
		});
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
			});
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
		mdl.open({
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
		});
	},
	confirmRma: function(id) {
		mdl.open({
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
		});
	},
	rmaReadComment: function(id) {
		$.post('/purchases/get_rma_comment', {
			id: id
		}, function(r) {
			mdl.open({
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
			});
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
		if ($(e).val() < parseFloat($(e).attr('min'))) 
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
}

// Min price formula
function min_price(a){
	for(var k in pf){
		if(a < k) return a+a/100*pf[k];
	}
	return a+a/100*pf[0];
}

function userAppend(el, data) {
    el.empty();
    $.each(data, function(i, v) {
        el.append($('<li/>', {
            'class': 'hnt hntTop',
            'data-title': v.name,
            'html': $('<a/>', {
                href: '/users/views/' + i,
                html: $('<img/>', {
                    src: v.photo ? '/uploads/images/users/' + i + '/thumb_' + v.photo : '/templates/admin/img/userDef.jpg',
                    onclick: 'showPhoto(this.src);'
                }),
                onclick: 'return false;'
            })
        }));
    });
}

var data;

// Issues
var issues = {
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
				mdl.open({
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
						/*var object = {};
						object[$('.devLocation').attr('data-object')] = {
							name: $('.devLocation').attr('data-oname')
						};
						$('input[name="object"]').data(object);*/
						
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


						//getLocation(1);
					}
				});
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
			mdl.open({
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
			});
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
					items += '<option value="' + v.id + '"' + (v.id == id ? ' selected' : '') + ' data-price="' + v.price + '">' + v.name + '</option>';
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
	}, updateStatus: function(id) {
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
					onblur: 'issues.newPur(this)'
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
					//getLocation(1);
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
						value: r.sale_price,
						placeholder: 'Sale price',
						onblur: 'purchases.minPrice(this)'
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
                })).append($('<ul/>'))).append(type == 'service' ? $('<div/>', {
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
            step: '0.1',
            min: '0',
			quantity: $(el).parent().prev().attr('data-quantity'),
			old: parseFloat($(el).attr('data-price')),
			inv_price: $(el).attr('data-inv_price') ? parseFloat($(el).attr('data-inv_price')) : 0,
            value: parseFloat($(el).attr('data-price')),
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
		} else if (parseFloat($('input[name="pPrice"]').val()) < parseFloat($('input[name="pPrice"]').attr('old'))) {
			err = 'You can only increase price';
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
}
var tax = 0;
// Invoices
var invoices = {
	sendDiscountCode: function(id, el) {
		loadBtn.start($(el));
		if ($('input[name="dis_code"]').val()) {
			$.post('/invoices/add_discount_code', {
				id: id,
				code: $('input[name="dis_code"]').val()
			}, function(r) {
				loadBtn.stop($(el));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Code successfully added',
						delay: 2
					});
					Page.get(location.href);
				} else if (r == 'no_user') {
					alr.show({
						class: 'alrDanger',
						content: 'It\'s not customer\'s discount code',
						delay: 2
					});
				} else if (r == 'no_date') {
					alr.show({
						class: 'alrDanger',
						content: 'Code has expired',
						delay: 2
					});
				} else if (r == 'no_code') {
					alr.show({
						class: 'alrDanger',
						content: 'Code does not exist',
						delay: 2
					});
				} else if (r == 'used') {
					alr.show({
						class: 'alrDanger',
						content: 'Code is already used',
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
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'Please, enter code',
				delay: 2
			});
		}
	},
	addStoreDiscount: function(id, el) {
		mdl.open({
			id: 'addDiscountCode',
			title: 'Enter discount code',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Discount code'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'dis_code'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'invoices.sendDiscountCode(' + id + ', this);',
					html: 'Send'
				})
			}))
		});
	},
	createEsimate: function(id) {
		if (!Object.keys($('input[name="customer"]').data())[0]) {
			alr.show({
				class: 'alrDanger',
				content: 'Plese, select customer',
				delay: 2
			});
		} else {
			$.post('/invoices/send_estimate', {
				id: id,
				customer: Object.keys($('input[name="customer"]').data())[0]
			}, function (r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'You successfully created invoice',
						delay: 2
					});
					Page.get('/invoices/view/' + id);
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
	estimateToInv: function(id) {
		mdl.open({
			id: 'estimate',
			title: 'Select customer',
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
				})).append($('<ul/>'))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: 'Send',
					onclick: 'invoices.createEsimate(' + id + ')'
				})
			})),
			cb: function() {
				$.post('/users/all', {gId: 5, nIds: Object.keys($('input[name="customer"]').data()).join(',')}, function (r) {
					if (r){
						var items = '', lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#customer > ul').html(items).sForm({
							action: '/users/all',
							data: {
								gId: 5,
								lId: lId,
								nIds: Object.keys($('input[name="customer"]').data() || {}).join(','),
								query: $('#customer > .sfWrap input').val() || ''
							},
							all: false,
							select: $('input[name="customer"]').data(),
							s: true,
							link: 'users/view'
						}, $('input[name="customer"]'));
					}
				}, 'json');
			}
		});
	},
	confirmDiscountInvoice: function(id) {
		$.post('/invoices/confirm_discount_invoise', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount was successfully confirmed',
					delay: 2
				});
				Page.get(location.href);
			} else if (r == 'no_acc') {
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
	refundDecline: function(id) {
		$.post('/invoices/refund_decline', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Refund was successfully declined',
					delay: 2
				});
				Page.get(location.href);
			} else if (r == 'no_acc') {
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
	sendRefundRequest: function(id) {
		$.post('/invoices/refund_request', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Refund request was successfully sent',
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
	}, refundConfirm: function(id) {
		$.post('/invoices/refund_confirm', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Refund was successfully confirmed',
					delay: 2
				});
				Page.get(location.href);
			} else if (r == 'no_acc') {
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
	refundRequest: function(id) {
		mdl.open({
			id: 'mdlRefund',
			title: 'Refund request',
			content: $('<div/>', {
				class: 'conQuestion',
				html: $('<div/>', {
					html: 'Are you sure?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					type: 'button',
					onclick: 'invoices.sendRefundRequest(' + id + ');',
					html: 'Yes'
				})
			}).append($('<button/>', {
					class: 'btn btnSubmit dc',
					type: 'button',
					onclick: 'mdl.close();',
					html: 'No'
				})))
		});
	},
	send_mail: function(id) {
		$.post('/invoices/send_mail/', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Mail was successfully sent',
					delay: 2
				});
			} else if (r.indexOf('no_email') >= 0) {
				var name = r.split('-');
				alr.show({
					class: 'alrDanger',
					content: 'E-mail address not provided for ' + name[1],
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
                            class: 'iGroup price quantity',
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
                            items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-catname="' + v.catname + '" data-quantity="' + v.quantity + '">' + v.name.replace('\'', '') + '</li>';
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
                            select: $('input[name="inventory"]').data(),
							link: 'inventory/view'
                        }, $('input[name="inventory"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    noCust: 1,
                    type: 'service',
					object: Object.keys($('input[name="object"]').data())[0]
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-catname="' + v.catname + '">' + v.name.replace('\'', '') + '</li>';
                            lId = v;
                        });
                        $('#services > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                noCust: 1,
                                type: 'service',
								object: Object.keys($('input[name="object"]').data())[0],
                                query: $('#services > .sfWrap input').val() || ''
                            },
                            all: false,
							select: $('input[name="services"]').data(),
							link: 'inventory/service/edit'
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
                            items += '<li data-value="' + v.id + '" data-price="' + currency_val[v.currency || 'USD'].symbol + parseFloat(v.price.replace(/[^0-9.]/g, '').trim()).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-catname="">' + v.name.replace('\'', '') + '</li>';
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
                            select: $('input[name="purchases"]').data(),
							link: 'purchases/edit'
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
                            items += '<li data-value="' + v.id + '" data-price="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.price.replace(/[^0-9.]/g, '').trim()).toFixed(2) + '" data-purchase="'+ currency_val[v.currency || 'USD'].symbol + parseFloat(v.purchase.replace(/[^0-9.]/g, '').trim()).toFixed(2) + '" data-currency="' + (v.currency || 'USD') + '" data-catname="">' + v.name.replace('\'', '') + '</li>';
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
                            select: $('input[name="tradein"]').data(),
							link: 'inventory/view'
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
							currency:  v.currency,
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
					price: $(v).find('.tr_sale > input').val(),
					currency: $(v).find('.tr_name').attr('currency')
				}
				$(v).find('input[name="sale"]').css('border-color', '#ddd');
				$(v).find('input[name="purchase"]').css('border-color', '#ddd');
			});
			$('input[name="tradein"]').removeData().data(tradein);	
			
			$.post('/invoices/send_prices', {tradein: tradein, object: Object.keys($('input[name="object"]').data())[0]}, function(r) {
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
			} else if (!$('input[name="salename"]').val()) {
				alr.show({
					class: 'alrDanger',
					content: 'Please enter sale name',
					delay: 2
				});
			} else {
				data.append('name', $('.pnName').text());
				data.append('photo', $('#purch .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('cprice', $('input[name="price"]').val());
				data.append('salename', $('input[name="salename"]').val());
				data.append('itemID', $('.itemId').text());
				data.append('link', $('input[name="new_purchase"]').val());
				data.append('customer_id', Object.keys($('input[name="customer"]').data())[0]);
				
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
							content: 'Min sale price can not be less than $' + parseFloat(min_price(parseFloat($('.pnPrice').text().replace(/(\D*)/i, '')))).toFixed(2),
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
            invoices.total();
        }
	},
    eachService: function(el, type) {
		if (el) {
			$.each(el, function (i, v) {
				if (!$('.mInvoice #tr_' + type + '_' + i).length) {
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
						}).append(' ' + v.name)
					}).append($('<div/>', {
						class: 'td w10',
						html: ['stock', 'service'].indexOf(type) >= 0 ? $('<input/>', {
							type: 'number',
							name: 'quantity',
							class: 'quickEdit',
							max: v.quantity,
							min: '1',
							value: '1',
							onchange: 'changeQuantity(this, \''+type+'\');',
							onkeyup: 'changeQuantity(this, \''+type+'\');'
						}) : '1'
					})).append($('<div/>', {
						class: 'td w100' + (type == 'tradein' ? ' nPay trIn' : ''),
						price: v.price,
						ondblclick: (type == 'tradein' ? '' : 'invoices.changeMorePrice(this, ' + i + ', \'' + type + '\');'),
						html: currency_val[v.currency || 'USD'].symbol + (type == 'tradein' ? '-' + parseFloat(v.purchase.replace(/[^0-9.]/g, '').trim()).toFixed(2) : ((parseFloat(typeof v.price == 'string' ? v.price.replace(/[^0-9.]/g, '').trim() : v.price) || 0)).toFixed(2))
					})).append($('<div/>', {
						class: 'td w10',
						html: (type == 'tradein' ? 'no' : 'yes')
					})))
				}
			});
		}
    },
	changeMorePrice: function(el, i, type) {
		$(el).html($('<input/>', {
			type: 'number',
			name: 'morePrice_' + i,
			value: $(el).html().replace(/[^0-9.]/g, ''),
			min: 0,
			step: '0.001',
			class: 'quickEdit',
			oldvalue: $(el).html().replace(/[^0-9.]/g, ''),
			currency: $(el).html().replace(/[0-9.\s]/g, ''),
			onblur: 'invoices.sendMorePrice(this, ' + i + ', \'' + type + '\');'
		}));
		$(el).find('input').focus();
	},
	sendMorePrice: function(el, i, type) {
		if (parseFloat($('input[name="morePrice_' + i +'"]').val()) <= parseFloat($('input[name="morePrice_' + i +'"]').attr('oldvalue'))) {
			alr.show({
				class: 'alrDanger',
				content: 'Price can not be less than ' + $('input[name="morePrice_' + i +'"]').attr('oldvalue'),
				delay: 2
			});
			$(el).parent().html($(el).attr('currency') + $(el).attr('oldvalue'));
		} else {
			$(el).parent().html($(el).attr('currency') + $(el).val());
			var inv = $('input[name="' + (type == 'stock' ? 'inventory' : (type + 's')) + '"]').data();
			inv[i].price = $(el).val();
			$('input[name="' + (type == 'stock' ? 'inventory' : (type + 's')) + '"]').data(inv);
		}
	},
    delInvItem: function(el, type, id) {
        type = (type == 'stock') ? 'inventory' : (type == 'tradein' ? type : type + 's');
        $(el).parent().parent().remove();
        delete $('input[name="' + type + '"]').data()[id];
        invoices.total();
    },
    delInInvoice: function(el, id) {
        $(el).parents('.tbl').remove();
        invoices.total();
		$.post('/invoices/del_invoice', {
			id: id,
			del: $(el).parents('.inTbl').attr('data-id')
		});
    },
    total: function(el) {
        var subtotal = 0,
			subtotal_end = 0,
			tradein = 0,
            pSub = 0,
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax,
            discount = 1;
        if ($('.tbl.payInfo.discount').length)
            discount = (100 + parseFloat($('.tbl.payInfo.discount .w100').text().replace(/[^0-9.]/g, ''))) / 100;
        $('.payInfo:not(.discount)').each(function(ind, val) {
            $(val).find('div > .td.w100:not(".nPay")').each(function(i, v) {
                if ($(v).parent().hasClass('discountTr'))
                    pSub *= ((100 + parseFloat($(v).text().replace(/[^0-9.]/g, ''))) / 100);
                else
                    pSub += parseFloat($(v).text().replace(/[^0-9.]/g, ''));
            });
            subtotal += pSub;
            pSub = 0;
        });
		
		 $('.td.w100.trIn').each(function(i, v) {
			tradein += parseFloat($(v).find('input').length ? $(v).find('input').val() : $(v).text().replace(/[^0-9.]/g, ''));
		});
		
		$('.td.w100.onsite_add_price').each(function(i, v) {
			subtotal += parseFloat($(v).find('input').length ? $(v).find('input').val() : $(v).text().replace(/[^0-9.]/g, ''));
		});
			
        subtotal *= discount;
		subtotal_end = subtotal + tradein;
        $('#subtotal').text(subtotal_end.toFixed(2));
        $('#tax').text((subtotal * tax / 100).toFixed(2));
        $('#total').text((subtotal_end + parseFloat($('#tax').text().replace(/[^0-9.]/g, ''))).toFixed(2));
        $('#due').text((parseFloat($('#total').text().replace(/[^0-9.]/g, '')) - parseFloat($('#paid').text().replace(/[^0-9.]/g, '')) || 0).toFixed(2));
		var c = (
			($('input[name="services"]').data().length ? $('input[name="services"]').data() : '') ||
			($('input[name="inventory"]').data().length ? $('input[name="inventory"]').data() : '') ||
			($('input[name="purchases"]').data().length ? $('input[name="purchases"]').data() : '') || 
			($('input[name="tradein"]').data().length ? $('input[name="tradein"]').data() : '')
		);
		$('.currency').text(c ? currency_val[c[Object.keys(c)[0]].currency || 'USD'].symbol : '$');
    },
    create: function(el, id, estimate) {
        loadBtn.start($(el));
        var data = {
                id: id,
				services: ''
            },
            err = null,
            subtotal = 0,
			tradein_arr = {},
			object = $('input[name="object"]').data(),
			customer = $('input[name="customer"]'),
			services = $('input[name="services"]'),
			inventory = $('input[name="inventory"]'),
			purchases = $('input[name="purchases"]'),
			tradein = $('input[name="tradein"]'),
			onsite_price = $('input[name="onsite_price"]'),
            tax = object[Object.keys(object)].tax;
        if (!estimate && (customer.length ? !Object.keys(customer.data()).join(',').length : 0) && parseFloat($('#total').text()) > quick_sell) {
            err = 'Customer can not be empty if amount more then $50';
        } else if (!Object.keys(object).join(',').length) {
            err = lang[63];
        } else if (!Object.keys(services.data()).join(',').length && !Object.keys(inventory.data()).join(',').length && !Object.keys(tradein.data()).join(',').length && !Object.keys(purchases.data()).join(',').length && !$('[data-type="onsite"]').length) {
            err = lang[62];
        } else {
            data.customer = customer.length ? Object.keys(customer.data()).join(',') : 0;
            data.object = Object.keys(object).join(',');
            data.inventory = JSON.stringify(inventory.data());
            data.services = JSON.stringify(services.data());
            data.purchases = JSON.stringify(purchases.data());
            data.tradein = JSON.stringify(tradein.data());
            data.estimate = estimate || 0;
			
			if (onsite_price.length) data.add_onsite_price = onsite_price.val()

            $('.payInfo.mInvoice > div > .td.w100').each(function(i, v) {
                subtotal += parseFloat($(v).text());
            });
			
            /* data.subtotal = subtotal;
            data.tax = (subtotal * tax / 100).toFixed(2);
            data.total = parseFloat(subtotal) + parseFloat((subtotal * tax / 100).toFixed(2)); */
			data.subtotal = $('#subtotal').text();
            data.tax = $('#tax').text();
            data.total = $('#total').text();
            data.paid = parseFloat($('#paid').text());

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
			console.log(data);
			return false;
            $.post('/invoices/send', data, function(r) {
                loadBtn.stop($(el));
                if (parseInt(r) > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
                        delay: 2
                    });
                    var edit = location.pathname.match(/(.*)\/invoices\/?(.*)?/i);
                    if (edit[2].indexOf('edit') < 0) Page.get('/invoices/view/' + r);
                } else if (r == 'no_customer') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You must select customer if total sum < $' + parseFloat(quick_sell).toFixed(),
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
    create2: function(el, id, estimate) {
        loadBtn.start($(el));
        var data = {
                id: id,
				services: ''
            },
            err = null,
            subtotal = 0,
			tradein_arr = {},
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax;
        if (!estimate && ($('input[name="customer"]').length ? !Object.keys($('input[name="customer"]').data()).join(',').length : 0) && parseFloat($('#total').text()) > quick_sell) {
            err = 'Customer can not be empty if amount more then $50';
        } else if (!Object.keys($('input[name="object"]').data()).join(',').length) {
            err = lang[63];
        } else if (!Object.keys($('input[name="services"]').data()).join(',').length && !Object.keys($('input[name="inventory"]').data()).join(',').length && !Object.keys($('input[name="tradein"]').data()).join(',').length && !Object.keys($('input[name="purchases"]').data()).join(',').length && !$('[data-type="onsite"]').length) {
            err = lang[62];
        } else {
            data.customer = $('input[name="customer"]').length ? Object.keys($('input[name="customer"]').data()).join(',') : 0;
            data.object = Object.keys($('input[name="object"]').data()).join(',');
            data.inventory = JSON.stringify($('input[name="inventory"]').data());
            data.services = JSON.stringify($('input[name="services"]').data());
            data.purchases = JSON.stringify($('input[name="purchases"]').data());
            data.tradein = JSON.stringify($('input[name="tradein"]').data());
            data.estimate = estimate || 0;
			
			if ($('input[name="onsite_price"]').length) data.add_onsite_price = $('input[name="onsite_price"]').val()

            $('.payInfo.mInvoice > div > .td.w100').each(function(i, v) {
                subtotal += parseFloat($(v).text());
            });
			
            /* data.subtotal = subtotal;
            data.tax = (subtotal * tax / 100).toFixed(2);
            data.total = parseFloat(subtotal) + parseFloat((subtotal * tax / 100).toFixed(2)); */
			data.subtotal = $('#subtotal').text();
            data.tax = $('#tax').text();
            data.total = $('#total').text();
            data.paid = parseFloat($('#paid').text());

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
                if (parseInt(r) > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
                        delay: 2
                    });
                    var edit = location.pathname.match(/(.*)\/invoices\/?(.*)?/i);
                    if (edit[2].indexOf('edit') < 0) Page.get('/invoices/view/' + r);
                } else if (r == 'no_customer') {
                    alr.show({
                        class: 'alrDanger',
                        content: 'You must select customer if total sum < $' + parseFloat(quick_sell).toFixed(),
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
	update: function(id) {
		$.post('/invoices/update', {
			id: id
		}, function(r) {
			if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Invoice was successfully updated',
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
    partial: function(id, el, p) {
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null;
        $('input[name="partial"]').css('border-color', '#ddd');
        if (parseFloat($('input[name="partial"]').val()) > Math.abs(parseFloat($('#due').text()))) {
            err = lang[61];
            $('input[name="partial"]').css('border-color', '#f00');
        } else if (!parseFloat($('input[name="partial"]').val())) {
            err = lang[60];
            $('input[name="partial"]').css('border-color', '#f00');
        } else {
            data.partial = parseFloat($('input[name="partial"]').val());
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
            } else if (r == 'confirmed') {
				alr.show({
                    class: 'alrDanger',
                    content: 'You can not delete invoice. Some purchases are already ordered',
                    delay: 2
                })
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	restore: function(id) {
        $.post('/invoices/restore', {
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
		var data = {
				issue: id,
				discount: parseFloat($('#discount').text()) > 0 ? '{"' + $('#discount').attr('data-id') + '":{"name":"' + $('#discount').attr('data-name') + '","percent":"'+ $('#discount').text() + '"}}' : ''
			},
			err = null;
		data.customer = c;
		data.object = o;
		data.subtotal = parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, ''));
		data.tax = ((parseFloat($('#doit').text().replace(/[^0-9.]/g, '')) > 0) ? parseFloat($('#doit').text().replace(/[^0-9.]/g, '')) : (parseFloat($('#totalPrice').text().replace('$', '')))) * parseFloat($('#tax').text()) / 100;
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
				} else if (r == 'issue_invoice') {
					alr.show({
						class: 'alrDanger',
						content: 'The issue already has invoice',
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
    }, saveForm: function(el, f, email) {
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
			$.post('/invoices/save_' + (email ? 'email_' : '') + 'form', data, function(r) {
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
			if (($('select[name="method"]').val() != 'cash' || p) && Math.abs(parseFloat($('input[name="cash"]').val())) > Math.abs(parseFloat($('#due').text()))) {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Amount exceeds due',
					delay: 2
				});
			} else if (!parseFloat($('input[name="check"]').val()) && $('select[name="method"]').val() == 'check') {
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
					} else if (r == 'null_invoice') {
						alr.show({
							class: 'alrDanger',
							content: 'Amount can not be null',
							delay: 2
						});
					} else if (r.split('_')[0] == 'hasissue') {
						alr.show({
							class: 'alrDanger',
							content: 'This device has purchases attached. Please, cancel all purchases if you do not need them for repair before taking ownership.',
							delay: 4
						});
						Page.get('/issues/view/' + r.split('_')[1]);
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
			if (parseFloat($('input[name="cash"]').val()) < parseFloat($('#due').text())) {
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
	}, createRefund: function(el, id) {
		var data = {
			}, 
			j = 0,
			comment = $('textarea[name="refund_comment"]').val(),
			currency = 'USD';
		
		$('input[type="checkbox"]').each(function(i, v) {
			if (!j)
				currency = $(v).parent().parent().attr('data-currency') || 'USD';
			if ($(v).val() == 1) {
				var item = $(v).attr('name').split('_'),
					items = parseFloat($(v).parent().next().find('input').length ? $(v).parent().next().find('input').val() : $(v).parent().next().text());
				data[item[1]] = {
					name: $(v).parent().text(),
					price: (parseFloat($(v).parent().next().next().text().replace(/[^0-9.]/g, '').trim()) / items * (-1)).toString(),
					currency: $(v).parent().parent().attr('data-currency') || 'USD',
					type: item[0],
					items: items.toString()
				}
				j++;
			}
		});
		
		if (!comment || comment.trim().length < 10) {
			alr.show({
				class: 'alrDanger',
				content: 'Comment are required and must have enough 10 symbols',
				delay: 2
			});
		} else if (!j) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, select items',
				delay: 2
			});
		} else {
			$.post('/invoices/create_refund', {
				refund_id: id,
				refund_info: JSON.stringify(data),
				refund_comment: comment,
				currency: currency
			}, function(r) {
				if (parseInt(r) > 0) {
					alr.show({
						class: 'alrSuccess',
						content: 'Refund request was created',
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
	}
}

// Bugs
var bugs = {
    add: function(id, imp) {
		var title = '', content = '', images = '';
		if (id) {
			title = $('#bug_' + id).find('.bugTitle > div:first-child > i').text();
			content = $('#bug_' + id).find('.bugContent > div:first-child').html();
			var images = '';
			$('#bug_' + id).find('.thumbnails > .thumb').each(function(i, v) {
				images += '<div class="thumb">' + $(v).html() + '<span class="fa fa-times" onclick="delete_image(this);"></span></div>';
			})
		}
        mdl.open({
            id: 'bugAdd',
            title: lang[55],
            content: $('<div/>', {
                class: 'bugForm',
                html: $('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[54]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'title',
					value: title || ''
                }))
            }).append($('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[53]
                    })
                }).append($('<textarea/>', {
                    name: 'content',
					html: content || ''
                    }))).append($('<div/>', {
                        class: 'iGroup imgGroup',
                        html: $('<label/>', {
                            html: lang[52]
                        })
                    }).append($('<div/>', {
                        class: 'dragndrop',
                        html: $('<span/>', {
                            class: 'fa fa-download'
                        })
                        }).append('Click or drag and drop file here')).append($('<div/>', {
                            class: 'thumbnails',
							html: images || ''
                        }))).append($('<div/>', {
                    class: 'sGroup',
                    html: $('<button/>', {
                        class: 'btn btnSubmit', 
                        html: lang[18],
                        type: 'button',
                        onclick: 'bugs.send(this, ' + (id  || 0) + ', ' + (imp || 0) + ');'
                    })
                }))
        });
		$('.dragndrop').upload({
			count: 0,
			multiple: true,
			max: 5,
			check: function(e){
				var self = this;
				if(!e.error){
					var img = new Image();
					img.src = URL.createObjectURL(e.file);
					img.setAttribute('onclick', 'showPhoto(this.src);');
					$('#bugAdd .thumbnails').append($('<div>', {
						class: 'thumb',
						html: img
					}).append($('<span/>', {
						class: 'fa fa-times'
					}).click(function(){
						delete self.files[e.file.name];
						$(this).parent().remove();
					})));
				} else if(e.error == 'max'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[51],
					   delay: 2
					});
				} else if(e.error == 'type'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[50],
					   delay: 2
					});
				} else if(e.error == 'size'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[49],
					   delay: 2
					});
				}
			}
		});
    }, send: function(el, id, imp) {
        loadBtn.start($(el));
        var data = new FormData(), err = null;
		if (id) data.append('id', id);
         $(el).parents('.bugForm')
			  .find('label').next()
			  .css('border', '1px solid #ddd');
         $(el).parents('.bugForm')
			  .find('.iGroup:not(.imgGroup)')
			  .each(function(i, v) {
            if (!$(v).find('label').next().val() && $(v).find('label').text().trim() != 'Comment') {
                err = $(v).text() + ' can not be empty';
                $(v).find('label')
					.next()
					.css('border', '1px solid #f00');
            } else {
                if ($(v).find('label').next().attr('name') == $(v).attr('id'))
					data.append(Object.keys($(v).find('label').next().attr('name'), $(v).find('label').next().data()).join(','));
                else {
					data.append($(v).find('label').next().attr('name'), $(v).find('label').next().val());
					if ($(v).find('label').next().attr('name') == 'comment') data.append('dev', 1);
					if (imp == 1) data.append('status', 'improvement');
				}
            }
        })
		
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

         if (err) {
            loadBtn.stop($(el));
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
				url: '/bugs/send',
				data: data
			}).done(function(r) {
                loadBtn.stop($(el));
                if (r) {
					r = JSON.parse(r);
					var new_images = '';
					if (r.images) {
						$.each(r.images, function(i, v) {
							new_images = '<div class="thumb"><img src="/uploads/images/bugs/' + r.id + '/thumb_' + v + '" onclick="showPhoto(this.src);"></div>'
						})
					}
                    if (id) {
						$('#bug_' + r.id + ' .bugTitle > div:first-child > i').html($('input[name="title"]').val());
						$('#bug_' + r.id + ' .bugContent > div:first-child').html($('textarea[name="content"]').val());
						if ($('select[name="status"]').val()) $('#bug_' + r.id + ' .bugTitle > div:first-child > span').removeClass($('#bug_' + r.id + ' .bugTitle > div:first-child > span').attr('class')).addClass('st_' + $('select[name="status"]').val()).html($('select[name="status"]').val().replace('_', ' '));
						if (del) {
							$.each(del, function() {
								$('#bug_' + r.id + ' .bugContent > .thumbnails').find('img[src*="' + this.split('thumb_')[1] + '"]').parent().remove();
								$('#bug_' + r.id + ' .bugComment > .thumbnails').find('img[src*="' + this.split('thumb_')[1] + '"]').parent().remove();
							});
						}
						if ($('textarea[name="comment"]').length) {
							if ($('#bug_' + id + ' .bugComment').length) {
								$('#bug_' + id + ' .bugComment').removeAttr('class').addClass('bugComment cm_' + $('select[name="status"]').val())
									.html(r.user + ': <span>' + $('textarea[name="comment"]').val()
									+ '</span><div class="thumbnails">' + new_images + '</div>');
							} else {
								$('#bug_' + id + ' .bugContent').append($('<div/>', {
									class: 'bugComment cm_' + $('select[name="status"]').val(),
									html: r.user + ': <span>' + $('textarea[name="comment"]').val()
									+ '</span><div class="thumbnails">' + new_images + '</div>'
								}))
							}
						} else {
							$('#bug_' + r.id + ' .bugContent > .thumbnails').append(new_images);
						}
					} else {
						$('.userList.bug').prepend($('<div/>', {
							class: 'sUser bug',
							id: 'bug_' + r.id,
							html: $('<div/>', {
								class: 'bugTitle',
								html: $('<div/>', {
									html: $('<i/>', {
										html: $('input[name="title"]').val()
									})
								}).prepend($('<b/>', {
									html: '#' + r.id + ' '
								})).append('<span/>', {
									class: 'fa fa-pencil bEdit',
									onclick: 'bugs.add({id});'
								}).append($('<span/>', {
									class: imp ? 'st_improvement' : 'st_opened',
									html: imp ? 'improvement' : 'opened'
								}))
							}).append($('<div/>', {
								class: 'date',
								html: r.user
							}).append($('<span/>', {
								html: '|'
							})).append(r.date))
						}).append($('<div/>', {
							class: 'bugContent',
							html: $('<div/>', {
								html: $('textarea[name="content"]').val()
							})
						}).append($('<div/>', {
							class: 'thumbnails',
							html: new_images
						}))));
					}
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
    }, edit: function(id) {
		var images = '', comment = '';
		if (id) {
			$('#bug_' + id).find('.bugComment > .thumbnails > .thumb').each(function(i, v) {
				images += '<div class="thumb">' + $(v).html() + '<span class="fa fa-times" onclick="delete_image(this);"></span></div>';
			})
			comment = $('#bug_' + id + ' .bugComment > span').text();
		}
        mdl.open({
            id: 'editBug',
            title: lang[48],
            content: $('<div/>', {
                class: 'bugForm',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[47]
                    })
                }).append($('<select/>', {
                    name: 'status',
                    html: $('<option/>', {
                        value: 'opened',
                        html: lang[46]
                    })
                }).append($('<option/>', {
                        value: 'processed',
                        html: lang[45]
                    })).append($('<option/>', {
                        value: 'closed',
                        html: lang[44]
                    })).append($('<option/>', {
                        value: 'rejected',
                        html: lang[43]
                    })).append($('<option/>', {
                        value: 'improvement',
                        html: 'Improvement'
                    })).append($('<option/>', {
                        value: 'final_closed',
                        html: 'Final closed'
                    })))
				}).append($('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[27]
                    })
                }).append($('<textarea/>', {
                    name: 'comment',
					html: comment || ''
                }))).append($('<div/>', {
					class: 'iGroup imgGroup',
					html: $('<label/>', {
						html: lang[52]
					})
				}).append($('<div/>', {
					class: 'dragndrop',
					html: $('<span/>', {
						class: 'fa fa-download'
					})
				}).append('Click or drag and drop file here')).append($('<div/>', {
					class: 'thumbnails',
					html: images || ''
				}))).append($('<div/>', {
                    class: 'sGroup',
                    html: $('<button/>', {
                        class: 'btn btnSubmit', 
                        html: lang[42],
                        type: 'button',
                        onclick: 'bugs.send(this, ' + id + ');'
                    })
                })),
            cb: function() { 
				$('select[name="status"]').val($('#bug_' + id + ' .bugTitle > div:first-child > span:not(.fa)').attr('class').replace('st_', ''));
                $('#page').select();
            }
        });
		$('.dragndrop').upload({
			count: 0,
			multiple: true,
			max: 5,
			check: function(e){
				var self = this;
				if(!e.error){
					var img = new Image();
					img.src = URL.createObjectURL(e.file);
					img.setAttribute('onclick', 'showPhoto(this.src);');
					$('#editBug .thumbnails').append($('<div>', {
						class: 'thumb',
						html: img
					}).append($('<span/>', {
						class: 'fa fa-times'
					}).click(function(){
						delete self.files[e.file.name];
						$(this).parent().remove();
					})));
				} else if(e.error == 'max'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[51],
					   delay: 2
					});
				} else if(e.error == 'type'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[50],
					   delay: 2
					});
				} else if(e.error == 'size'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[49],
					   delay: 2
					});
				}
			}
		});
    }
}

// Cash
var cash = {
	cpage: 0,
	amount_cash: 0,
	amount_credit: 0,
	amount_hands: 0,
	amount_system: 0,
	show: 1,
	currency: 'USD',
	sendUpdateInfo: function(id, el, type, action, field, cash_id) {
		if ($(el).attr('old-value') == $('input[name="cash_val"]').val()) {
			$(el).parent().html('$ ' + $(el).attr('old-value'));
		} else {
			$.post('/cash/update_info', {
				id: id,
				cash_id: cash_id,
				field: field,
				value: $('input[name="cash_val"]').val()
			}, function(r) {
				if (r.res == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Value was succefully saved',
						delay: 2
					});
					$('#stat_' + cash_id).replaceWith(r.html)
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
	updateInfo: function(id, el, type, action, field, cash_id) {
		var val = parseFloat($(el).text().replace(/\D\s/i, ''));
		$(el).html($('<input/>', {
			type: 'number',
			name: 'cash_val',
			onblur: 'cash.sendUpdateInfo(' + id + ', this, "' + type + '", "' + action + '", "' + field + '", "' + cash_id + '");',
			value: val,
			'old-value': val,
			class: 'quickEdit'
		}));
		$('input[name="cash_val"]').focus();
	},
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
				cash.currency = r.currency;
				mdl.open({
				id: 'openCash',
				title: (a == 'open' ? lang[38] : lang[151]),
				content: $('<div/>', {
					class: 'mdlCash',
					html: $('<div/>', {
						class: 'sumCash',
						html: (r.show == 1 ? ('Enter Total Drawer Amount<br> ' + currency_val[r.currency || 'USD'].symbol + r.amount) : 'Enter Total Drawer Amount')
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
						onclick: a == 'close' ? 'cash.acceptClose(\'cash\', \'' + a + '\', 0, \'' + (r == 'NO' ? '0' : r) + '\', this);' : 'cash.accept(\'cash\', \'' + a + '\', null, null, this);',
						ondblclick: 'return false;'
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
	}, accept: function(t, a, d, out, el) {
		loadBtn.start($(el));
		if (parseFloat(t == 'cash' ? cash.amount_cash : cash.amount_credit) != parseFloat($('input[name="hands"]').val()) && a == 'open') {
			cash.amount_hands = $('input[name="hands"]').val();
			cash.amount_cash = $('input[name="hands"]').val();
			cash.amount_credit = $('input[name="hands"]').val();
			$('.mdl').hide();
			cash.dicline(t, a, d);
		} else {
			var data = new FormData(), err = null;
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
					data.append('system', cash.amount_system);
					if (out == 'out')
						data.append('out', $('input[name="out"]').val());
					else
						data.append('amount', $('input[name="leave"]').val());
				}
			} else {
				//data.amount = (t == 'cash' ? cash.amount_cash : cash.amount_credit);
				data.append('amount', $('input[name="hands"]').val());
				data.append('system', cash.amount_system);
			}
			if ($('input[name="hands"]').length) cash.amount_hands = $('input[name="hands"]').val();
			data.append('lack', cash.amount_system - cash.amount_hands);
			data.append('type', t);
			data.append('action', 'dicline');
			data.append('acType', a);
			data.append('d', d || 0);
			data.append('object', Object.keys($('input[name="object"]').data()).join(','));
			data.append('currency', cash.currency);
			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				if ($('.dragndrop').length){
					var files = $('.dragndrop')[0].files;
					if (!$.isEmptyObject(files)) {
						for (var n in files) {
							data.append('image', files[n]);
						}
					}
				}
				$.ajax({
					type: 'POST',
					dataType: 'json',
					processData: false,
					contentType: false,
					url: '/cash/send',
					data: data
				}).done(function(r) {
					//loadBtn.stop($(el));
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
									html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + parseFloat(cash.amount_system).toFixed(2) : ' --- ') + '<br><span class="creditArea"></span>'
								})).append($('<div/>', {
									class: 'td',
									html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + parseFloat(cash.amount_hands || 0).toFixed(2) : ' --- ') + (data.lack > 0.001 ? '<span class="minLack">-' + parseFloat(data.lack).toFixed(2) + '</span>' : (data.lack < -0.001 ? '<span class="plusLack">' + parseFloat(data.lack).toFixed(2) + '</span>' : '')) + '<br><span class="creditArea"></span>'
								})).append($('<div/>', {
									class: 'td',
									html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + parseFloat(data.amount || 0).toFixed(2) : ' --- ')
								})).append($('<div/>', {
									class: 'td',
									html: lang[a]
								})).append($('#drops').length ? $('<div/>', {
									class: 'td',
									html: (a == 'close' ? currency_val[cash.currency || 'USD'].symbol + data.out : '---')
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
				});
			}
		}
	}, dicline: function(t, a, s) {
		$('.mdl').hide();
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
						onclick: 'cash.sendDicline(\'' + t + '\', \'' + a + '\'' + (s ? ', ' + s : '') + ', this);'
					})
				}).append($('<button/>', {
						type: 'button',
						class: 'btn btnSubmit dc',
						html: 'Recalculate',
						onclick: 'mdl.close();'
					})))
		});
	}, sendDicline: function(t, a, s, el) {
		loadBtn.start($(el));
		var data = new FormData(), err = null;
		/* $('.mdl input, .mdl textarea').css('border', '1px solid #ddd');
		if (!$('input[name="amount"]').val()) {
			err = 'leave can not be empty';
			$('input[name="amount"]').css('border', '1px solid #f00');
		} else if (!$('textarea[name="comment"]').val()) {
			err = 'Comment can not be empty';
			$('textarea[name="comment"]').css('border', '1px solid #f00');
		} else { */
			data.append('amount', (t == 'cash' ? cash.amount_cash : cash.amount_credit));
			data.append('system', cash.amount_system);
			data.append('lack', cash.amount_system - (t == 'cash' ? cash.amount_cash : cash.amount_credit));
			data.append('type', t);
			data.append('action', 'dicline');
			data.append('acType', a);
			data.append('object', Object.keys($('input[name="object"]').data()).join(','));
			data.append('currency', cash.currency);
		//}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			if ($('.dragndrop').length){
				var files = $('.dragndrop')[0].files;
				if (!$.isEmptyObject(files)) {
					for (var n in files) {
						data.append('image', files[n]);
					}
				}
				
			}
			$.ajax({
				type: 'POST',
				dataType: 'json',
				processData: false,
				contentType: false,
				url: '/cash/send',
				data: data
			}).done(function(r) {
				//loadBtn.stop($(el));
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
						html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + data.system : ' --- ')
					})).append($('<div/>', {
						class: 'td',
						html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + (data.system - data.lack) : ' --- ') + (data.lack > 0.001 ? '<span class="minLack">-' + parseFloat(data.lack).toFixed(2) + '</span>' : (data.lack < -0.001 ? '<span class="plusLack">' + parseFloat(data.lack * -1).toFixed(2) + '</span>' : ''))
					})).append($('<div/>', {
						class: 'td',
						html: (cash.show == 1 ? currency_val[cash.currency || 'USD'].symbol + (data.amount || 0) : ' --- ')
					})).append($('<div/>', {
						class: 'td',
						html: lang[a]
					})).append($('#drops').length ? $('<div/>', {
						class: 'td',
						html: (a == 'close' ? currency_val[cash.currency || 'USD'].symbol + '0' : '---')
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
			});
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
						) + ', \'out\', this);',
						ondblclik: 'return false;'
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
					) + ', null, this);',
					ondblclick: 'return false;'
				})
			}))
		});
	}, openCredit: function() {
		cash.amount_cash = 0;
		cash.amount_credit = 0;
		cash.amount_hands = 0;
		cash.amount_system = 0;
		cash.currency = 'USD';
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
					cash.currency = r.currency;
					mdl.open({
						id: 'openCredit',
						title: lang[31],
						content: $('<div/>', {
							class: 'mdlCredit',
							html: $('<div/>', {
								class: 'sumCash',
								html: (cash.show == 1 ? 'Credit Settlement<br> ' + currency_val[r.currency || 'USD'].symbol + r.amount : 'Credit Settlement')
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
							class: 'iGroup imgGroup fw',
							html: $('<label/>', {
								html: 'Credit Settelement'
							})
						}).append($('<div/>', {
							class: 'dragndrop',
							html: $('<span/>', {
								class: 'fa fa-download'
							})
						}).append('Click or drag and drop file here'))).append($('<div/>', {
							class: 'cashGroup',
							html: $('<button/>', {
								type: 'button',
								class: 'btn  btnSubmit ac',
								html: lang[30],
								onclick: 'cash.accept(\'credit\', \'open\', null, null, this);',
								ondblclick: 'return false;'
							})
						}).append($('<button/>', {
								type: 'button',
								class: 'btn btnSubmit dc',
								html: lang[29],
								onclick: 'cash.dicline(\'credit\', \'open\', ' + (r == 'NO' ? '0' : r) + ', this);'
							}))),
						cb: function() {
							$('.dragndrop').upload({
								check: function(e){
									var self = this;
									if(!e.error){
										var img = new Image();
										img.src = URL.createObjectURL(e.file);
										var thumb = $('.dragndrop').prev();
										if(thumb[0].tagName == 'FIGURE') thumb.remove();
										$(this).before($('<figure/>', {
											html: $('<img/>', {
												src: URL.createObjectURL(e.file)
											})
										}).append($('<span/>', {
											class: 'fa fa-times'
										}).click(function(){
											self.files = {};
											$(this).parent().remove();
										})));
									} else if(e.error == 'type'){
										alr.show({
										   class: 'alrDanger',
										   content: 'You can load only jpeg, jpg, png, gif images',
										   delay: 2
										});
									} else if(e.error == 'size'){
										alr.show({
										   class: 'alrDanger',
										   content: 'Upload size for images is more than allowable',
										   delay: 2
										});
									}
								}
							});
						}
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
			date_start: $('#calendar input[name="date"]').val(),
			date_end: $('#calendar input[name="fDate"]').val()
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
				if (r.left_count) $('.btnLoad').removeClass('hdn');
				else $('.btnLoad').addClass('hdn');
				$('.filterCtnr').hide();
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
			page: page,
			type: $('select[name="type"]').val(),
			action: $('select[name="action"]').val(),
			status: $('select[name="status"]').val(),
			staff: Object.keys($('input[name="staff"]').data()).join(','),
			date_start: $('#calendar input[name="date"]').val(),
			date_end: $('#calendar input[name="fDate"]').val()
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
	}, sendComment: function(id) {
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

// Get Location
function getLocation(f) {
	if (!$('#location > ul').length) {
		$('#location > input').removeData();
		$('#location > .sfWrap').remove();
		$('#location').append($('<ul/>'));
	}
    $.post('/objects/get_locations', {
        sId: Object.keys($('input[name="status"]').data() || {}).join(',') || 11,
        nIds: Object.keys($('input[name="location"]').data()).join(','),
		oId: Object.keys($('input[name="object"]').data() || {}).join(',') || $('.devLocation').attr('data-object') || 0,
		warranty: (($('#location').length && $('#location').attr('warranty') == 1) ? 1 : 0)
    }, function(r) {
        if (r) {
            var items = '',
                lId = 0;
            $.each(r.list, function(i, v) {
                items += '<li data-value="' + v.id + '" data-count="' + v.count + '">' + v.name + '</li>';
                lId = v;
            });
				var st = $('input[name="status"]').data() || {};
				if (location_id && !f || Object.keys(status_id)[0] == Object.keys(st)[0]) {
					$('#location > input').data(location_id);
					location_id = '';
					object_id = '';
				}

            $('#location > ul').html(items).sForm({
                action: '/objects/get_locations',
                data: {
                    lId: lId,
                    sId: Object.keys($('input[name="status"]').data() || {}).join(',') || 11,
                    oId: Object.keys($('input[name="object"]').data() || {}).join(',') || $('.devLocation').attr('data-object') || object_id || 0,
                    query: $('#location > .sfWrap input').val() || '',
					warranty: (($('#location').length && $('#location').attr('warranty') == 1) ? 1 : 0)
                },
                all: (r.count <= 20) ? true : false,
                select: $('input[name="location"]').data(),
                s: true
            }, $('input[name="location"]'), function() {
					$('#sublocation').find('select').empty();
					getCount(f);
			}, {
				oId: 'object',
				sId: 'status'
			});
        }
    }, 'json');
}

// Get Count
function getCount(f) {
	var options = '<option value="0">Not selected</option>',
		c = Object.keys($('input[name="location"]').data() || {}).join(',') ? $('input[name="location"]').data()[Object.keys($('input[name="location"]').data() || {}).join(',')]['count'] : 0,
		l = $('input[name="location"]').data() || {};
    if (c > 0) {
		for(var i = 1; i <= c; i++) {
			options += '<option value="' + i + '"' + ((Object.keys(count_id)[0] == Object.keys(l)[0]) && location_count == i ? ' selected' : '') + '>' + i + '</option>';
		}
		if (!location_id) location_count = 0;
		$('#sublocation').show().find('select').html(options);
		if ($('#sublocation .sWrap').length) {
			$('#sublocation .sWrap').remove();
			$('#page').select();
		}
	} else {
		$('#sublocation').hide().find('select').val(0).trigger('change');
	}
}

// Categories
var categories = {
    dragEl: {},
    mouse: {},
    parentEl: {},
    clickEl: {},
    dragstart: function(e) {
        options.mouse = e;
        options.clickEl = e.target;
        options.dragEl = e.target.parentNode;
        options.parentEl = options.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', options.dragEl.textContent);
        console.log(options.mouse);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        options.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== options.clickEl && target.className == options.clickEl.className) {
            if (target.parentNode.nextSibling == options.dragEl)
                options.parentEl.insertBefore(options.dragEl, target.parentNode);
            else {
                options.parentEl.insertBefore(options.dragEl, target.parentNode.nextSibling);
            }
        }
    }
}

// Store
var store = {
    id: 0,
	sortBlogCategory: function() {
		var data = {};
		$('.userList ol > li').each(function(i, v) {
			data[$(v).attr('data-id')] = {
				sort: $(v).index(),
				parent: $(v).parent().parent().attr('data-id') || 0
			}
		});
		$.post('/store/blog_cat_priority', data, function() {});
	},
	sendBlogCategory: function(f, event, id) {
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
            data.append('pathname', form.find('input[name="pathname"]').val());
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/store/sendBlogCategory',
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
    delBlogCategory: function(id) {
        $.post('/store/delBlogCategory', {
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
	addBlogCategory: function(id, n, pn, p, t) {
		mdl.open({
			id: 'select_par',
			title: (id ? lang[17] : lang[18]) + ' ' + lang[129],
			content: '<form  method="post" onsubmit="store.sendBlogCategory(this, event, ' + (id || 0) + ');">\
				<div class="iGroup">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name" value="' + (n || '') + '">\
				</div>\
				<div class="iGroup">\
					<label>URI</label>\
					<input type="text" name="pathname" value="' + (pn || '') + '">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
				</div>\
			</form>'
		});
    },
	sendServiceCategory: function(f, event, id) {
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
            data.append('parent', Object.keys(form.find('input[name="parent"]').data()));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/store/sendServiceCategory',
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
    delServiceCategory: function(id) {
        $.post('/store/delServiceCategory', {
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
	addServiceCategory: function(id, n, p, t) {
        $.post('/store/allServiceCategories', {
            id: id || '',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '', lId = 0;
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
				mdl.open({
					id: 'select_par',
					title: (id ? lang[17] : lang[18]) + ' ' + lang[129],
					content: '<form  method="post" onsubmit="store.sendServiceCategory(this, event, ' + (id || 0) + ');">\
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
							action: '/store/allServiceCategories',
							data: {
								lId: lId,
								query: $('#parent > .sfWrap input').val() || '',
								nIds: Object.keys(p || {}).join(',') + ',' + id
							},
							all: (r.count <= 20) ? true : false,
							select: p,
							s: true
						}, $('input[name="parent"]'));
						
					}
				});
		    }
        }, 'json');
    },
	sendDiscount: function() {
		var err = null;
		if (!$('input[name="amount"]').val())
			err = 'Amount can not be null';
		else if (!Object.keys($('input[name="customer"]').data())[0])
			err = 'Customer can not be null';
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/store/send_discount', {
				amount: $('input[name="amount"]').val(),
				customer: Object.keys($('input[name="customer"]').data())[0]
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Discount was successfully added',
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
	addDiscount: function(el) {
		mdl.open({
			id : 'add_discount',
			title: 'Add discount',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Amount, %'
					})
				}).append($('<input/>', {
					type: 'number',
					name: 'amount',
					min: '0',
					step: '0.001'
				}))
			}).append($('<div/>', {
				class: 'iGroup',
				id: 'customer',
				html: $('<label/>', {
					html: 'Customer'
				})
			}).append($('<input/>', {
				type: 'hidden',
				name: 'customer'
			})).append($('<ul/>', {
				class: 'hdn'
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'store.sendDiscount(this);',
					html: 'Send'
				})
			})),
			cb: function() {
				$.post('/users/all', {gId: 5}, function (r) {
					if (r){
						var items = '', lId = 0;
						$.each(r.list, function(i, v) {
							items += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						});
						$('#customer > ul').html(items).sForm({
							action: '/users/all',
							data: {
								gId: 5,
								lId: lId,
								nIds: Object.keys($('input[name="customer"]').data()).join(','),
								query: $('#customer > .sfWrap input').val()
							},
							all: false,
							select: $('input[name="customer"]').data(),
							s: true,
							link: 'users/view'
						}, $('input[name="customer"]'));
					}
				}, 'json');
			}
		});
	},
	delDiscount: function(id) {
        $.post('/store/del_discount', {
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
	updateOrderStatus: function(id, status) {
		$.post('/store/get_order_status', {
			id: id, 
			status: status
		}, function(r) {
			mdl.open({
				id: 'orderStatus',
				title: 'Update status',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Status'
						})
					}).append($('<select/>', {
						name: 'status',
						html: r
					}))
				}).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'store.sendUpdateStatus(' + id + ');',
						html: 'Update'
					})
				})),
				cb: function() {
					$('#page').select();
				}
			})
		})
	},
	sendUpdateStatus: function(id) {
		$.post('/store/update_order_status', {
			id: id, 
			status: $('select[name="status"]').val()
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Status was successfully updates',
					delay: 2
				});
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				})
			}
		});
	},
	delOrder: function(id) {
		if (id) {
            $.post('/store/delOrder', {
                id: id
            }, function(r) {
                if (r == 'OK') {
                    if ($('#order_' + id).length) 
						$('#order_' + id).remove();
					else 
						Page.get('/store/orders');
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else {
            if ($('#order_' + id).length) 
				$('#order_' + id).remove();
			else 
				Page.get('/store/orders');
		}
	},
	openPayment: function(id, name) {
		mdl.open({
			id: 'openStatus',
			title: (id ? lang[17] : lang[18]) + ' payment method',
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
					onclick: 'store.sendPayment(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		});
    },
	sendPayment: function(el, id) {
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
            $.post('/store/sendPayment', data, function(r) {
                loadBtn.stop($(this));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Payment method was successfully sent',
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
    delPayment: function(id, warranty) {
        if (id) {
            $.post('/store/delPayment', {
                id: id,
				warranty: warranty ? 1 : 0
            }, function(r) {
                if (r == 'OK')
                    $('#payment_' + id).remove();
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
	sendPaymentPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('payment_', '')
            }
        });
        if (data) {
            $.post('/store/stPaymentPriority', data, function(r) {
                //console.log(r);
            });
        }
    },
	openDelivery: function(id, name, price, currency) {
		forms = '';
		if (currency_val) {
			$.each(currency_val, function(i, v) {
				forms += '<option value="' + i + '" ' + ((currency == i || (!currency && i == 'USD')) ? 'selected' : '') + '>' + i + '(' + v.symbol + ')</option>';
			});
		}
		mdl.open({
			id: 'openStatus',
			title: (id ? lang[17] : lang[18]) + ' delivery method',
			cb: function() {
				$('#page').select();
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
					class: 'iGroup curGroup',
					html: $('<label/>', {
						html: 'Form'
					})
				}).append($('<div/>', {
					class: 'cur',
					html: $('<select/>', {
						name: 'currency',
						html: forms
				})
			})).append($('<input/>', {
				type: 'number',
				name: 'price',
				min: 0,
				step: 0.001,
				value: price || 0
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					onclick: 'store.sendDelivery(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		});
    },
	sendDelivery: function(el, id) {
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
			data.price = f.find('input[name="price"]').val();
			data.currency = f.find('select[name="currency"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/store/sendDelivery', data, function(r) {
                loadBtn.stop($(this));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: 'Delivery method was successfully sent',
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
    delDelivery: function(id, warranty) {
        if (id) {
            $.post('/store/delDelivery', {
                id: id,
				warranty: warranty ? 1 : 0
            }, function(r) {
                if (r == 'OK')
                    $('#delivery_' + id).remove();
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
	sendDeliveryPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('delivery_', '')
            }
        });
        if (data) {
            $.post('/store/stDeliveryPriority', data, function(r) {
                //console.log(r);
            });
        }
    },
	openStatus: function(id, name, form, color, alt_color) {
		$.post('/settings/allForms', {}, function(r) {
			forms = '<option value="0">Not seleted</option>';
			if (r) {
				$.each(r.list, function(i, v) {
					forms += '<option value="' + v.id + '" ' + (form == v.id ? 'selected' : '') + '>' + v.name + '</option>';
				});
			}
			mdl.open({
				id: 'openStatus',
				title: (id ? lang[17] : lang[18]) + lang[121],
				cb: function() {
					$('#page').select();
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
							html: 'Form'
						})
					}).append($('<select/>', {
						name: 'form',
						html: forms
				}))).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Color'
						})
					}).append($('<input/>', {
						type: 'text',
						name: 'color',
						 class: 'colorPicker',
						value: color || ''
					}))).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Alternative color'
						})
					}).append($('<input/>', {
						type: 'text',
						name: 'alt_color',
						class: 'colorPicker',
						value: alt_color || ''
					}))).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						onclick: 'store.sendStatus(this, ' + id + ')',
						html: id ? lang[113] : lang[91]
					})
				})),
				cb: function() {
					$('#page').colorPicker();
					$('#page').select();
				}
			});
		}, 'json')
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
			data.form = f.find('select[name="form"]').val();
			data.color = f.find('input[name="color"]').val();
			data.alt_color = f.find('input[name="alt_color"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/store/sendOrderStatus', data, function(r) {
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
    delStatus: function(id, warranty) {
        if (id) {
            $.post('/store/delOrderStatus', {
                id: id,
				warranty: warranty ? 1 : 0
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
	sendPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('status_', '')
            }
        });
        if (data) {
            $.post('/store/stPriority', data, function(r) {
                //console.log(r);
            });
        }
    },
	sendSlide: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
		data.append('id', id);
		 for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = 'The field can not be empty';
                    break;
                }
            } 
			data.append(form[i].name, form[i].value);
        }
		switch($(f).find('select[name="link_type"]').val()) {
			case 'none':
				data.append('link_id', '');
			break;
			
			case 'custom':
				data.append('link_id', $(f).find('input[name="custom"]').val());
			break;
			
			case 'item':
			case 'categry':
			case 'blog':
				data.append('link_id', Object.keys($(f).find('input[name="' + $(f).find('select[name="link_type"]').val() + '"]').data()).join(','));
			break;
		}
		
		$('.dragndrop').each(function(i, v) {
			if ($(v).length){
				var files = $(v)[0].files;
				if (!$.isEmptyObject(files)) {
					for (var n in files) {
						data.append($(v).attr('data-type'), files[n]);
					}
				}
			}
			if ($(v).parents('.imgGroup').find('figure > img').attr('new')) {
				var pUsr = $(v).parents('.imgGroup').find('figure > img').attr('src');
				data.append($(v).attr('data-type'), b64Blob(pUsr.split(',')[1], 'image/png'));
				data.append('blob', 1);
			}
			
			var thumb = $(v).prev();
			if (thumb[0].tagName != 'FIGURE' && id) data.append('del_' + $(v).attr('data-type'), true);
		});
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
				url: '/store/send_slide',
				data: data
			}).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Slide was succefully ' + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
					Page.get('/store/slider/edit/' + r);
                } else if (r == 'err_file_size') {
					alr.show({
                        class: 'alrDanger',
                        content: 'Uploaded photos are too large',
                        delay: 2
                    });
				} else if (r == 'err_image_type') {
					alr.show({
                        class: 'alrDanger',
                        content: 'Uploaded photos are have wrong type',
                        delay: 2
                    });
				} else if (r == 'no_access') {
					alr.show({
                        class: 'alrDanger',
                        content: 'You hav no access to do that',
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
	delSlide: function(id) {
        $.post('/store/del_slide', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#slide_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	selectIcon: function(icon) {
		$('input[name="icon"]').val($(icon).attr('class').replace('fa fa-', '')).next().attr('class', $(icon).attr('class'));
		mdl.close();
	},
	openServiceIcon: function() {
		mdl.open({
			id: 'selectIcon',
			title: 'Select Icon',
			content: '<div class="mdlIcons">\
				<span class="fa fa-address-book" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-address-book-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-address-card" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-address-card-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-adjust" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-american-sign-language-interpreting" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-anchor" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-archive" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-area-chart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-arrows" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-arrows-h" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-arrows-v" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-asl-interpreting" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-assistive-listening-systems" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-asterisk" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-at" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-audio-description" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-automobile" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-balance-scale" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-ban" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bank" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bar-chart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bar-chart-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-barcode" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bars" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bath" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery-empty" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery-full" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery-half" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery-quarter" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-battery-three-quarters" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bed" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-beer" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bell" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bell-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bell-slash" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bell-slash-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bicycle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-binoculars" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-birthday-cake" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-blind" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bluetooth" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bluetooth-b" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bolt" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bomb" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-book" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bookmark" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bookmark-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-braille" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-briefcase" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bug" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-building" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-building-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bullhorn" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bullseye" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-bus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cab" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calculator" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar-check-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar-minus-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar-plus-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-calendar-times-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-camera" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-camera-retro" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-car" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-caret-square-o-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-caret-square-o-left" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-caret-square-o-right" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-caret-square-o-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cart-arrow-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cart-plus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-certificate" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-check" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-check-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-check-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-check-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-check-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-child" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-circle-o-notch" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-circle-thin" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-clock-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-clone" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-close" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cloud" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cloud-download" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cloud-upload" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-code" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-code-fork" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-coffee" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cog" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cogs" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-comment" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-comment-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-commenting" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-commenting-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-comments" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-comments-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-compass" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-copyright" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-creative-commons" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-credit-card" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-credit-card-alt" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-crop" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-crosshairs" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cube" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cubes" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-cutlery" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-dashboard" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-database" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-deaf" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-deafness" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-desktop" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-diamond" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-dot-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-download" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-drivers-license" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-drivers-license-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-edit" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-ellipsis-h" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-ellipsis-v" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-envelope" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-envelope-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-envelope-open" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-envelope-open-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-envelope-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-eraser" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-exchange" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-exclamation" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-exclamation-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-exclamation-triangle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-external-link" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-external-link-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-eye" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-eye-slash" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-eyedropper" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-fax" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-feed" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-female" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-fighter-jet" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-archive-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-audio-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-code-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-excel-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-image-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-movie-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-pdf-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-photo-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-picture-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-powerpoint-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-sound-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-video-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-word-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-file-zip-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-film" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-filter" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-fire" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-fire-extinguisher" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-flag" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-flag-checkered" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-flag-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-flash" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-flask" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-folder" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-folder-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-folder-open" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-folder-open-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-frown-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-futbol-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-gamepad" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-gavel" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-gear" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-gears" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-gift" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-glass" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-globe" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-graduation-cap" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-group" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-grab-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-lizard-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-paper-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-peace-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-pointer-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-rock-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-scissors-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-spock-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hand-stop-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-handshake-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hard-of-hearing" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hashtag" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hdd-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-headphones" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-heart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-heart-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-heartbeat" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-history" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-home" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hotel" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-1" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-2" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-3" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-end" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-half" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-hourglass-start" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-i-cursor" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-id-badge" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-id-card" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-id-card-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-image" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-inbox" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-industry" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-info" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-info-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-institution" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-key" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-keyboard-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-language" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-laptop" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-leaf" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-legal" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-lemon-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-level-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-level-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-life-bouy" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-life-buoy" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-life-ring" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-life-saver" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-lightbulb-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-line-chart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-location-arrow" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-lock" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-low-vision" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-magic" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-magnet" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mail-forward" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mail-reply" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mail-reply-all" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-male" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-map" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-map-marker" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-map-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-map-pin" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-map-signs" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-meh-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-microchip" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-microphone" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-microphone-slash" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-minus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-minus-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-minus-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-minus-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mobile" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mobile-phone" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-money" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-moon-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mortar-board" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-motorcycle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-mouse-pointer" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-music" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-navicon" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-newspaper-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-object-group" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-object-ungroup" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-paint-brush" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-paper-plane" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-paper-plane-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-paw" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-pencil" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-pencil-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-pencil-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-percent" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-phone" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-phone-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-photo" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-picture-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-pie-chart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plane" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plug" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plus-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plus-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-plus-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-podcast" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-power-off" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-print" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-puzzle-piece" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-qrcode" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-question" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-question-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-question-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-quote-left" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-quote-right" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-random" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-recycle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-refresh" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-registered" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-remove" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-reorder" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-reply" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-reply-all" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-retweet" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-road" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-rocket" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-rss" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-rss-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-s15" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-search" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-search-minus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-search-plus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-send" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-send-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-server" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-share" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-share-alt" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-share-alt-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-share-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-share-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-shield" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-ship" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-shopping-bag" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-shopping-basket" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-shopping-cart" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-shower" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sign-in" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sign-language" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sign-out" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-signal" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-signing" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sitemap" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sliders" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-smile-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-snowflake-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-soccer-ball-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-alpha-asc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-alpha-desc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-amount-asc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-amount-desc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-asc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-desc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-numeric-asc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-numeric-desc" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sort-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-space-shuttle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-spinner" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-spoon" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-square" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-square-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star-half" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star-half-empty" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star-half-full" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star-half-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-star-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sticky-note" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sticky-note-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-street-view" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-suitcase" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-sun-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-support" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tablet" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tachometer" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tag" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tags" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tasks" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-taxi" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-television" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-terminal" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-0" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-1" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-2" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-3" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-4" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-empty" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-full" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-half" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-quarter" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thermometer-three-quarters" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thumb-tack" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thumbs-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thumbs-o-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thumbs-o-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-thumbs-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-ticket" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-times" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-times-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-times-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-times-rectangle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-times-rectangle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tint" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-left" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-off" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-on" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-right" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-toggle-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-trademark" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-trash" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-trash-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tree" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-trophy" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-truck" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tty" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-tv" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-umbrella" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-universal-access" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-university" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-unlock" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-unlock-alt" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-unsorted" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-upload" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-circle" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-circle-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-plus" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-secret" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-user-times" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-users" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-vcard" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-vcard-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-video-camera" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-volume-control-phone" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-volume-down" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-volume-off" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-volume-up" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-warning" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-wheelchair" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-wheelchair-alt" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-wifi" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-window-close" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-window-close-o" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-window-maximize" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-window-minimize" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-window-restore" onclick="store.selectIcon(this);"></span>\
				<span class="fa fa-wrench" onclick="store.selectIcon(this);"></span>\
			</div>'
		});
	},
	sendService: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if (!$(v).find('label').next().val() && $.inArray($(v).find('label').next().attr('name'), ['name', 'content']) > 0) {
                err = 'The field can not be empty';
                $(v).find('label').next().css('border-color', '#f00');
            } else if ($(v).find('label').next().attr('name')) {
                $(v).find('label').next().css('border-color', '#ddd');
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            }
        });
        if (!$('textarea[name="content"]').val())
            err = 'Description can not be empty';
        else {
            data['content'] = $('textarea[name="content"]').val();
        }
		data['price'] = $('input[name="price"]').val()
		data['currency'] = $('select[name="currency"]').val()
		data['category'] =  $('input[name="category"]').data() ? Object.keys($('input[name="category"]').data())[0] : 0
		data['blog'] =  $('input[name="blog"]').data() ? Object.keys($('input[name="blog"]').data())[0] : 0
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/store/send_service', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r <= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Service was successfully ' + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get('/store/services/edit/' + r);
                }
            });
        }
    },
    delService: function(id) {
        $.post('/store/del_service', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#service_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendPost: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if (!$(v).find('label').next().val() && $.inArray($(v).find('label').next().attr('name'), ['name', 'descr']) > 0) {
                err = 'The field can not be empty';
                $(v).find('label').next().css('border-color', '#f00');
            } else if ($(v).find('label').next().attr('name')) {
                $(v).find('label').next().css('border-color', '#ddd');
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            }
        });
        if (!$('textarea[name="content"]').val())
            err = lang[25];
        else {
            data['content'] = $('textarea[name="content"]').val()
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/store/send_post', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r.id <= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[22] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    if (r.delivery) {
                        store.id = r.id;
                        store.mail(r.delivery, 0, true);
                    } else
                        Page.get('/store/blog/edit/' + r.id);
                }
            }, 'json');
        }
    },
    delBlog: function(id) {
        $.post('/store/del_post', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#post_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendPage: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if (!$(v).find('label').next().val() && $.inArray($(v).find('label').next().attr('name'), ['name', 'descr']) > 0) {
                err = 'The field can not be empty';
                $(v).find('label').next().css('border-color', '#f00');
            } else if ($(v).find('label').next().attr('name')) {
                $(v).find('label').next().css('border-color', '#ddd');
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            }
        });
        if (!$('textarea[name="content"]').val())
            err = lang[25];
        else {
            data['content'] = $('textarea[name="content"]').val()
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/store/send_page', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r.id <= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[22] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get('/store/pages/edit/' + r.id);
                }
            }, 'json');
        }
    },
    delPage: function(id) {
        $.post('/store/del_page', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#page_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    mail: function(delivery, lId, first) {
        if (first) {
            $('#all').text(delivery.count);
            $('#sended').text(0);
        }
        $.post('/store/delivery', {
            lid: lid
        }, function(r) {
            $('#sended').text(parseInt($('#sended').text()) + 1);
            if (r.delivery)
                store.mail(r.delivery, r.lId, false);
            else
                Page.get('/store/blog/edit/' + store.id);
        })
    }, addCategory: function(id, n, p) {
        $.post('/store/allCategories', {
            id: id || '',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: (id ? lang[17] : lang[18]) + lang[19],
                    content: '<form  method="post" onsubmit="store.sendCategory(this, event, ' + (id || 0) + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup">\
							<input type="hidden" name="parent">\
							<div class="lbl">' + lang[21] + '</div>\
							<ul class="hdn">' + items + '</ul>\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        nIds = [];
                        $('input[name="parent"]').data(p);
                        $('#select_par').find('ul').sForm({
                            action: '/store/allCategories',
                            data: {
                                lId: lId,
                                query: $('#select_par').find('input').val(),
                                nIds: Object.keys(p || {}).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: p,
                            s: true
                        }, $('input[name="parent"]'));
                    }
                });
            }
        }, 'json');
    }, sendCategory: function(f, event, id) {
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
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/store/sendCategory',
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
        $.post('/store/delCategory', {
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
    }, sendIntroducing: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {},
            form = $(f);
        
        data.title = form.find('input[name="title"]').val();
        data.content = form.find('textarea[name="content"]').val();
        
        $.post('/store/save_introducing', data, function (r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[15],
                    delay: 2
                });
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                });
            }
        });  
    }, sortCategory: function() {
		var data = {};
		$('.userList ol > li').each(function(i, v) {
			data[$(v).attr('data-id')] = {
				sort: $(v).index(),
				parent: $(v).parent().parent().attr('data-id') || 0
			}
		});
		$.post('/store/cat_priority', data, function() {});
	}
}

// Income
function income() {
    $('#income').html('<b>' + ($('select[name="currency"] > option[value="' + $('select[name="currency"]').val() + '"]').attr('data-symbol')) + (parseFloat($('input[name="price"]').val() || 0) - parseFloat($('input[name="purchase-price"]').val() || 0)) + '</b>');
}

// Select all
function selectAll(name, val) {
    $('select[name="' + name + '"] > option').each(function(i, v) {
        $(v).prop('selected', val > 0 ? val : 0).trigger('change');
    })
}

// More text
function hideArea(el) {
    $(el).text($(el).text() == 'More' ? 'Less' : 'More').next().next().slideToggle('fast');
}

// Total
var total = {
    single: function(pr, t) {
		$(t).text(0);
        $(pr).each(function(i, v) {
            $(t).text((parseFloat($(t).text().replace(/[^0-9.]/g, '')) + parseFloat($(v).text().replace(/[^0-9.]/g, ''))).toFixed(2));
        })
        $(t).prepend($('#issue').attr('data-currency'));
    },
    fTotal: function() {
        total.single('.servPrice', '#serv_total');
        total.single('.miniServPrice', '#miniServ_total');
        total.single('.invPrice', '#inv_total');
        total.single('.purPrice', '#pur_total');
        total.single('#serv_total, #inv_total, #pur_total', '#totalPrice');
		
		$('#doit').text($('#doit').text().replace(/[0-9.\s]/g, '') + (parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, '')) * ((100 - parseFloat($('#discount').text())) / 100)).toFixed(2));
		if (parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, '')) > 0 && !$('#create_invoice').length && !$('#view_invoice').length)
			$('.uMore > ul').append($('<li>', {
				id: 'create_invoice',
				html: $('<a/>', {
					href: 'javascript:invoices.issCreate(' + parseInt($('#issue').attr('data-id'))
							+ ', ' + parseInt($('#issue').attr('data-customer'))
							+ ', ' + parseInt($('#issue').attr('data-object')) 
							+ ', ' + parseInt($('#issue').attr('data-discount')) + ');',
					html: $('<span/>', {
						class: 'fa fa-credit-card'
					})
				}).append('Create invoice')
			}))
		
		if (parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, '')) != parseFloat($('#totalPrice').attr('data-total'))) {
			$.post('/issues/send_total', {
				id: parseInt($('#totalPrice').attr('data-id')),
				total: parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, ''))
			}, function(r) {
				$('#totalPrice').attr('data-total', parseFloat($('#totalPrice').text().replace(/[^0-9.]/g, '')));
			});
		}
    }, 
	miniTotal: function(lc) {
		var tPrice = $('#pur_total').length ? parseFloat($('#pur_total').text().replace(/[^0-9.]/g, '')).toFixed(2) : 0;
		
		if ($('input[name="set_inventory"]').data()) {
			$.each($('input[name="set_inventory"]').data(), function(i, v) {
				tPrice = (parseFloat(tPrice) + parseFloat(v.price.replace(/[^0-9.]/g, ''))).toFixed(2);
			});
		}
		
		if ($('input[name="set_services"]').data()) {
			$.each($('input[name="set_services"]').data(), function(i, v) {
				tPrice = (parseFloat(tPrice) + parseFloat(v.price.replace(/[^0-9.]/g, ''))).toFixed(2);
			});
		}
		
		$('.tPrice > div').html($('#doit').text().replace(/[0-9.\s]/g, '') + tPrice);
		
		/* $.post('/inventory/allStatuses', {
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
				location_id = lc;
				console.log(lc);
                $('#status > div').remove();
                $('#status').append($('<ul/>'));
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
                }, $('input[name="status"]'), getLocation, {
                    set_inventory : 'set_inventory', 
                    set_services : 'set_services'
                });
            }
        }, 'json'); */
	}
}

// Printing
function to_print(a, b) {
    var modal = window.open(a, '_blank');
    modal.onload = function() {
        this.document.title = b;
        //this.print();
    }
}

// Timer
var timer = {
	time: undefined,
	tick: undefined,
	getCreate: function(a, b, c, d){
		$(a).replaceWith('<input type="time" value="'+b+'" onblur="timer.changeSeconds(this, '+c+', \''+d+'\')">');
	},
	winStart: function() {
		//timer.time = new Date(parseInt($('.timerClock').attr('value')) * 1000);
		timer.time = parseInt($('.timerClock').attr('value'));
		timer.timeStart($('.timerClock').next().find('.fa').hasClass('fa-pause'), true);	
	},
	timeStart: function(p, f) {
		var _t = timer;
		_t.tick = setTimeout(function() {
			$('.timerClock').attr('value', parseInt($('.timerClock').attr('value')) + 1);
			//_t.time.setSeconds(_t.time.getSeconds() + (p ? 1 : 0));
			_t.time += (p ? 1 : 0);
			/* h = _t.time.getUTCHours();
			m = _t.time.getUTCMinutes();
			s = _t.time.getUTCSeconds(); */
			s = _t.time % 60;
			m = (_t.time % 3600 - s) / 60;
			h = Math.abs((_t.time % 86400 - s - m * 60) / 3600);
			s = Math.abs(s);
			m = Math.abs(m);
			$('.timerClock').text(((_t.time < 0) ? '-' : '') + h + ':' + (
				m < 10 ? '0' + m : m
			) + ':' + (
				s < 10 ? '0' + s : s)
			)
			if (p) _t.timeStart(true);
		}, f ? 0 : 1000);
	}, confirm:function(e) {
		var text = '';
		switch (e) {
			case 'start':
				text = lang[156];
				side = 1;
			break;
			
			case 'pause':
				text = lang[157];
				side = 0;
			break;
			
			case 'stop':
				side = 0;
				text = lang[158];
			break;
		}
		$.post('/timer/getTime', {side: side, event: e}, function(r) {
			mdl.open({
				id: 'confirmation',
				title: 'Please, confirm',
				content: $('<div/>', {
					class: 'aCenter',
					html: $('<div/>', {
						html: text + '<br>' + r
					})
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit ac',
						onclick: 'timer.accept(\'' + e + '\');',
						html: lang[159]
					})
				}).append($('<button/>', {
						class: 'btn btnSubmit dc',
						onclick: 'mdl.close();',
						html: lang[160]
					})))
			});
		});
	}, accept: function(e) {
		var btn = $('.timerClock').next().find('.fa'),
			decode = {};
		$.post('/timer', {
			a: e, 
			id: $('.timerClock').attr('data-id')}, function(r) {
				if (r.indexOf('}') > 0) decode = JSON.parse(r);
			if(r == 'IP'){
				alr.show({
					class: 'alrDanger',
					content: 'You can not login from this IP',
					delay: 2
				});
			} else if (decode.id > 0 && r.indexOf('}') > 0) {
				switch(e.trim()) {
					case 'start':
						btn.removeClass('fa-play').addClass('fa-pause').attr('onclick', 'timer.confirm(\'pause\')');
						$('.timerClock').attr('data-id', decode.id);
						timer.timeStart(true);
					break;
					
					case 'pause': 
					case 'stop': 
						btn.removeClass('fa-pause').addClass('fa-play').attr('onclick', 'timer.confirm(\'start\')');
						timer.timeStop(e);
					break;
				}
				mdl.close();
				if (decode.cash == 1) 
					Page.get('/cash');
			} else if (r) {
				alr.show({
					class: 'alrDanger',
					content: r,
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
	}, timeStop: function(e) {
		if ($('.timerClock').attr('data-id') > 0) {
			clearTimeout(timer.tick);
			if (e == 'stop') $('.timerClock').attr('data-id', '');
		} else 
			alr.show({
				class: 'alrDanger',
				content: lang[14],
				delay: 2
			});
	}, changeSeconds: function(a, b, c){
		if(a.value){
			$(a).replaceWith('<span onclick="timer.getCreate(this, \''+a.value+'\', '+b+', \''+c+'\')">'+a.value+'</span>');
			$.post('/timer/create_time', {
				time: a.value,
				id: b,
				type: c
			}, function(j){
				if(j == 'OK'){
					$.getJSON(location.href, function(r) {
						$('#page').html(r.content);
					});
				}
			});
		}
	}, week: function() {
		if ($('#week').length)
			mdl.close('week');
		else {
			$.post('/timer/week', {}, function(r) {
				if (r) {
					mdl.open({
						id: 'week',
						width: '700px',
						class: 'weekInfo',
						title: 'Your punch card for this week',
						content: r
					});
				}
			});
		}
	}
}

// Round(plots)
function round100(n, s, p) {
	if (n % s) {
		if (p) return (n + s - n % s);
		else return (n - s - n % s);
	} else {
		return n;
	}
}

// Analytics
var analytics = {
	online_users: function() {
		$.post('/analytics/online_users', data, function(r) {
			if (parseInt(r))
				$('#visitors_online > span').html(parseInt(r));
		});
	},
	site_visitors: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar5 > input[name="date"]').val(),
				date_finish: $('#calendar5 > input[name="fDate"]').val()
			};
		loadBtn.start($(e));
		date = [$('#calendar5 > input[name="date"]').val(), $('#calendar5 > input[name="fDate"]').val()];
		
		$.post('/analytics/site_stat', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var minY = 0,
					maxY = 0;
				
				for (n in r.data['hosts']) {
					if (r.data['hosts'][n][1] > maxY)
						maxY = r.data['hosts'][n][1];
				}
				
				minY = maxY;
				for (n in r.data['visitors']) {
					if (r.data['visitors'][n][1] < minY)
						minY = r.data['visitors'][n][1];
				}
				
				stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				
				$('#site_stat_plot').empty();
				makeStat.init({
					id: 'site_stat_plot',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[146]) + ': '], 
					y: [minY, maxY, stepY, true, 'Count: '],
					data: [
						{
							'id': 'hosts',
							'points': r.data['hosts'],
							'color': '#36b1e6'
						},
						{
							'id': 'visitors',
							'points': r.data['visitors'],
							'color': '#ff0000'
						}
					]
				}); 
				f.hide();
				$('#visitors_online > span').html(r.data.online);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	inventory_plot: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar_plot > input[name="date"]').val(),
				date_finish: $('#calendar_plot > input[name="fDate"]').val(),
				object: Object.keys($('input[name="object_stat"]').data()).join(','),
				inventory: 1
			};
		loadBtn.start($(e));
		date = [$('#calendar_plot > input[name="date"]').val(), $('#calendar_plot > input[name="fDate"]').val()];
		
		$.post('/analytics/sold_plot', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv['profit'].map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv['sold'].map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv['sold'],
							'color': '#36b1e6'
						},
						{
							'points': inv['purchase'],
							'color': '#ff0000'
						},
						{
							'points': inv['profit'],
							'color': '#b7bd06'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	tradein_plot: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date_start: $('#calendar_plot > input[name="date"]').val(),
				date_finish: $('#calendar_plot > input[name="fDate"]').val(),
				object: Object.keys($('input[name="object_stat"]').data()).join(','),
				tradein: 1,
				purchases: 1
			};
		loadBtn.start($(e));
		date = [$('#calendar_plot > input[name="date"]').val(), $('#calendar_plot > input[name="fDate"]').val()];
		
		$.post('/analytics/sold_plot', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv['profit'].map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv['sold'].map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last || 23), r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv['sold'],
							'color': '#36b1e6',
							'id': 'sold'
						},
						{
							'points': inv['purchase'],
							'color': '#ff0000',
							'id': 'purchase'
						},
						{
							'points': inv['profit'],
							'color': '#b7bd06',
							'id': 'profit'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	},
	new_users: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/new_users', {
			date_start: $('#calendar4 > input[name="date"]').val(),
			date_finish: $('#calendar4 > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt(maxY - minY), 10, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#users_plot').empty();
				makeStat.init({
					id: 'users_plot',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Date: '], 
					y: [minY, maxY, stepY, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'id': 'plot_new_users'
						},
						{
							'points': r.returned,
							'color': '#88b52d',
							'id': 'plot_returned_users'
						}
					],
					plusx: 0
				}); 
				$('#total_customers').text('(Total: ' + r.users + ')');
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	feedback: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/feedback_report', {
			date_start: $('#calendar3').length ? $('#calendar3 > input[name="date"]').val() : $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar3').length ? $('#calendar3 > input[name="fDate"]').val() : $('#calendar > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#feedbacks_plot').empty();
				makeStat.init({
					id: 'feedbacks_plot',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Rating: '], 
					y: [0, maxY, 10, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'dash': true,
							'id': 'by_count'
						}
					],
					plusx: 1
				}); 
				$('#freport').html(r.stores);
				if (e && $('[data-value="middle_value"]').hasClass('active'))
					analytics.feedback_by_day();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	feedback_by_day: function(e) {
		loadBtn.start($(e));
		$.post('/analytics/feedback_by_day', {
			date_start: $('#calendar3').length ? $('#calendar3 > input[name="date"]').val() : $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar3').length ? $('#calendar3 > input[name="fDate"]').val() : $('#calendar > input[name="fDate"]').val()
		}, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				
				$('#feedback_by_day').empty();
				makeStat.init({
					id: 'feedback_by_day',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last - 1) || 5, r.step, r.labels, 'Rating: '], 
					y: [0, 5, 1, true, 'Count: '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6',
							'dash': true,
							'id': 'by_day'
						}
					],
					plusx: 1
				}); 
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json')
	},
	expansesReport: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/analytics/expanses_report', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
				} else {
					$('#report').empty();
				}
			}, 'json');
		}
	},
	purchasesReport: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.type = $('select[name="type"]').val();
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/analytics/purchases_report', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
				} else {
					$('#report').empty();
				}
			}, 'json');
		}
	},
	sales: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_stat"]').val(),
				object: $('select[name="object_stat"]').val(),
				cash: $('input[name="pay_cash"]').val(),
				credit: $('input[name="pay_credit"]').val(),
				check: $('input[name="pay_check"]').val(),
				merchaine: $('input[name="pay_merchaine"]').val(),
				compare_period: $('input[name="compare_period"]').val()
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/sales', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last-1) || 23, r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					],
					red_line: 4000,
					black_line: 4,
					violet_line: true,
					compare_line: $('input[name="compare_period"]').val() || 0
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	}, points: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_points"]').val(),
				object: $('select[name="object_points"]').val(),
				user: Object.keys($('input[name="user_points"]').data()).join(',')
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/points', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#eff').empty();

				makeStat.init({
					id: 'eff',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last-1) || 23, r.step, r.labels, ((data.date && date[0] == date[1]) ? lang[145] : lang[146]) + ': '], 
					y: [minY, maxY, stepY, true, lang[115] + ': '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					]
				}); 
				f.hide();
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

function hideCalendar(e) {
	if ($(e.target).hasClass('cl')) $(e.target).parents('.filterCtnr').find('.calendar').hide();
}
$('body').on('click', function(e) {
	if (!$(e.target).hasClass('cl')) $('.calendar').hide();
});


function $_GET(param) {
	var result =  window.location.search.match(
		new RegExp("(\\?|&)" + param + "(\\[\\])?=([^&]*)")
	);
	return result ? result[3] : false;
}

// Unconfirmed
var dashbord = {
	tasks: function() {
		$.post('/main/tasks', {}, function(r) {
			$('#tbl_tasks').html(r);
			$('#tbl_tasks').parent().next().remove();
		})
	},
	appointments: function() {
		$.post('/main/appointments', {}, function(r) {
			$('#tbl_appointments').html(r);
			$('#tbl_appointments').parent().next().remove();
		})
	},
	uncofirmed: function() {
		$.post('/main/uncofirmed', {}, function(r) {
			$('#conf_services').html(r.services);
			$('#conf_inventory').html(r.inventory);
			$('#conf_discounts').html(r.discounts);
			$('#conf_warranty').html(r.warranty);
			$('#conf_services').parent().next().remove();
			$('#conf_inventory').parent().next().remove();
			$('#conf_discounts').parent().next().remove();
			$('#conf_warranty').parent().next().remove();
			/* $('body').hntJS({
				left: $('.hntJS').offset().left
			}); */
		}, 'json')
	}, drops: function() {
		$.post('/main/drops', {}, function(r) {
			$('#drops').html(r);
			$('#drops').next().remove();
		})
	}, onsite: function() {
		$.post('/main/onsite', {}, function(r) {
			$('#onsite_list').html(r.list);
			$('#onsite_details_list').html(r.details_list);
			$('#onsite_invoices_list').html(r.invoices_list);
			$('#onsite_uncofirmed_list').html(r.uncofirmed_list);
			$('#onsite_list').parent().next().remove();
			$('#onsite_details_list').parent().next().remove();
			$('#onsite_invoices_list').parent().next().remove();
			$('#onsite_uncofirmed_list').parent().next().remove();
		}, 'json');
	}, issues: function() {
		$.post('/main/issues', {}, function(r) {
			$('#issues_list').html(r.issues);
			$('#store_issues_list').html(r.store_issues);
			$('#issues_list').parent().next().remove();
			$('#store_issues_list').parent().next().remove();
		}, 'json');
	}, issue_transfer: function() {
		$.post('/main/issue_transfer', {}, function(r) {
			$('#issue_transfer_list').html(r);
			$('#issue_transfer_list').parent().next().remove();
		});
	}, my_issues: function() {
		$.post('/main/my_issues', {}, function(r) {
			$('#my_issues_list').html(r);
			$('#my_issues_list').parent().next().remove();
		});
	}, object_issues: function() {
		$.post('/main/store_issues', {}, function(r) {
			$('#object_issues_list').html(r);
			$('#object_issues_list').parent().next().remove();
		});
	}, timer: function() {
		$.post('/main/timer', {}, function(r) {
			$('#timers_list').html(r);
			$('#timers_list').parent().next().remove();
		});
	}, cash_stat: function() {
		$.post('/main/cash_stat', {}, function(r) {
			$('#cash_stat_list').html(r);
			$('#cash_stat_list').parent().next().remove();
		});
	}, feedbacks: function() {
		$.post('/main/feedbacks', {}, function(r) {
			$('#feedbacks_list').html(r);
			$('#feedbacks_list').parent().next().remove();
		});
	}, purchases: function() {
		$.post('/main/purchases', {}, function(r) {
			$('#tbl_purchases').html(r.purchases);
			$('#tbl_purchases').parent().next().remove();
		}, 'json');
	}, confirm_drop: function(id, a, el) {
		var data = {
			id: id,
			action: a
		};
		$.post('/cash/confirm_drop', data, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Drop was successfully ' + lang[a],
					delay: 2
				});
				$('#drop_' + id).remove();
				/* if (a == 'confirm') {
					$(el).removeClass('fa-check').removeClass('gr').addClass('fa-times').addClass('rd').text(' Unconfirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'unconfirm\', this);');
					$('#drop_' + id).removeClass('unconfirmed').addClass('confirmed').appendTo($('#drops .tBody'));
				} else {
					$(el).removeClass('fa-times').removeClass('rd').addClass('fa-check').addClass('gr').text(' Confirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'confirm\', this);');
					$('#drop_' + id).removeClass('confirmed').addClass('unconfirmed').prependTo($('#drops .tBody'));
				} */
			} else if (r == 'ERR') {
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
		})
	},
	confirmDropObject: function(id, el) {
		var data = {
			id: id
		};
		$.post('/cash/confirm_drop_object', data, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Drop was successfully confirmed',
					delay: 2
				});
				$('#drop_object_'+id).html('Confirmed');
				/* if (a == 'confirm') {
					$(el).removeClass('fa-check').removeClass('gr').addClass('fa-times').addClass('rd').text(' Unconfirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'unconfirm\', this);');
					$('#drop_' + id).removeClass('unconfirmed').addClass('confirmed').appendTo($('#drops .tBody'));
				} else {
					$(el).removeClass('fa-times').removeClass('rd').addClass('fa-check').addClass('gr').text(' Confirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'confirm\', this);');
					$('#drop_' + id).removeClass('confirmed').addClass('unconfirmed').prependTo($('#drops .tBody'));
				} */
			} else if (r == 'ERR') {
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
		})
	}
}
var Languages = {
	save: function(form, event){
		var form = $(form).serializeArray();
		event.preventDefault();
		$.post('/settings/saveLang', form, function(r){
			if (r == 'OK'){
				alr.show({
					class: 'alrSuccess',
					content: lang[1],
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[1],
					delay: 2
				});
			}
		});
	},
	get: function(a){
		$.post('/settings/getLang', {
			lang: a
		}, function(a){
			$('select[name="module"]').html(a).next().remove().parent().select();
		});
	},
	getModule: function(a){
		var exp = a.split('_');
		$.post('/settings/getLangModule', {
			lang: $('select[name="lang"]').val(),
			type: exp[0],
			module: exp[1]
		}, function(r){
			$('#phrases').html(r);
		});
	}
}

// Salary
var salary = {
    sendPointsEdit: function (id, el) {
        if (!$(el).val()) {
            alr.show({
                class: 'alrDanger',
                content: 'Value can not be empty',
                delay: 2
            });
        } else if ($(el).val() == parseFloat($(el).attr('old-value'))) { 
            $(el).parent().html($(el).val());
        } else {
            $.post('/salary/edit_points', {
                id: id,
                value: $(el).val(),
                oldValue: $(el).attr('old-value')
            }, function (r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Points was successfully sent',
                        delay: 2
                    });
                    $(el).parent()
                        .attr('ondblclick', 'salary.pointsEdit(' + id + ', this)')
                        .html($(el).val());
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                    $(el).parent().html($(el).attr('old-value'));
                }
            })
        }
    },
    pointsEdit: function (id, el) {
        $(el).attr('ondblclick', '').html($('<input/>', {
            name: 'new_points',
            type: 'number',
            class: 'quickEdit',
            value: $(el).text(),
            'old-value': $(el).text(),
            onblur: 'salary.sendPointsEdit(' + id + ', this);'
        }));
        $(el).find('input').focus();
    },
	pointsPayout: function(id, el) {
		//if ($(el).prev().val() > 0) {
			$.post('/salary/points_payout', {
				id: id,
				amount: $(el).prev().val()
			}, function(r) {
				if (r == 'OK') {
					var parent = $(el).parent().parent();
					$(el).prev().val(0);
					parent.find('.pPoints').text(parseFloat(parseFloat(parent.find('.pPoints').text().replace(/[^0-9.-]/g, '')) + parseFloat(parent.find('.pCurrent').text().replace(/[^0-9.-]/g, ''))).toFixed(2));
					parent.find('.pCurrent').text(0);
					alr.show({
						class: 'alrSuccess',
						content: 'Payout was successfully sent',
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
				class: 'alrSuccess',
				content: 'Amount can not be 0 or less',
				delay: 2
			});
		} */
	},
	makeSalary: function(id, object, amount, el) {
		$.post('/salary/make', {
				id: id, 
				object_id: object,
				amount: amount,
				lDate: $('#calendar > input[name="fDate"]').val(),
				sDate: $('#calendar > input[name="date"]').val()
			}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Payment done',
					delay: 2
				});
				$(el).parent().parent().remove();
			} else if (r == 'DONE') {
				alr.show({
					class: 'alrDanger',
					content: 'Staff has already get salary on that period. Please, check payment period',
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

// Close calendar
function closeCalendar() {
	$('.calendar').hide().parent().removeClass('act');
}

// FAQ
var faq = {
	send: function(f, event, id) {
		event.preventDefault();
		loadBtn.start($(f).find('button[type="submit"]'));
		var data = {
				id: id
			},
			err = null;
		if (!$(f).find('input[name="title"]').val())
			err = 'Title can not be empty';
		else if (!$(f).find('textarea[name="content"]').val())
			err = 'Content can not be empty';
		else {
			data.title = $(f).find('input[name="title"]').val();
			data.content = $(f).find('textarea[name="content"]').val();
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/faq/send', data, function(r) {
				loadBtn.stop($(f).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'FAQ was succefully sent',
						delay: 2
					});
					Page.get('/faq');
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
		}
	}, del: function(id, el) {
		$.post('/faq/del', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'FAQ was succefully deleted',
					delay: 2
				});
				$(el).parent().parent().remove();
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
	}
}

// Camera
var camera = {
	cpage: 0,
	showVideo: function (id) {
		mdl.open({
			id: 'showVideo',
			title: 'Camera shoot',
			content: '<div class="video-camera">\
				<video width="470" height="265" controls src="/video.mov">\
					Your browser does not support the video tag.\
				</video>\
			</div>'
		});
	},
	comments: function(id) {
		camera.cpage = 0;
		$.post('/camera/comments', {id: id}, function(r) {
			mdl.open({
				id: 'cameraComments',
				title: 'Comments',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'comments',
						html: r.content
					})
				}).append($('<button/>', {
					class: 'btn btnLoad' + (r.left_count > 0 ? '' : ' hdn'),
					onclick: 'camera.doloadComments(this,' + id + ');',
					html: 'Load commetns'
				})).append($('<div/>', {
					class: 'commentForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Status'
						})
					}).append($('<select/>', {
							name: 'status',
							html: r.statuses
						}))
					}).append($('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Leave comment'
						})
					}).append($('<textarea/>', {
						name: 'comment'
					}))).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'camera.sendComment(' + id + ');',
							html: 'Send'
						})
				}))),
				cb: function() {
					$('select').select();
				}
			});
		}, 'json');
	}, sendComment: function(id) {
		if (!$('textarea[name="comment"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Comment can not be empty',
				delay: 2
			});
		} else {
			if (!parseInt($('select[name="status"]').val())) {
				alr.show({
					class: 'alrDanger',
					content: 'Status can not be empty',
					delay: 2
				});
			} else {
				$.post('/camera/send_comment', {
					id: id,
					text: $('textarea[name="comment"]').val(),
					status: $('select[name="status"]').val()
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
		}
	}, doloadComments: function(e, id) {
		camera.cpage += 1;
		$.post('/camera/comments', {
			id: id, 
			page: camera.cpage
		}, function(r) {
			$('.comments').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	}, openStatus: function(id, name) {
		mdl.open({
			id: 'openStatus',
			title: (id ? lang[17] : lang[18]) + lang[121],
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
					onclick: 'camera.sendStatus(this, ' + id + ')',
					html: id ? lang[113] : lang[91]
				})
			}))
		});
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
        } else
            data.name = f.find('input[name="name"]').val();
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/camera/send_status', data, function(r) {
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
    delStatus: function(id) {
        if (id) {
            $.post('/camera/del_status', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#status_' + id).remove();
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
    }, sendPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('status_', ''),
                pr: $(v).find('input').val()
            }
        });
        if (data) {
            $.post('/camera/st_priority', data, function(r) {
                //console.log(r);
            });
        }
    }, addActivity: function() {
		$.post('/users/all', {staff: 1}, function(ur) {
            var uitems = '', ulId = 0;
            if (ur.list) {
                $.each(ur.list, function(ui, uv) {
                    uitems += '<li data-value="' + uv.id + '">' + uv.name + '</li>';
                    ulId = uv.id;
                });
            }

            $.post('/invoices/objects', {all: 1}, function(or) {
                var oitems = '', olId = 0;
                if (or.list) {
                    $.each(or.list, function(oi, ov) {
                        oitems += '<li data-value="' + ov.id + '">' + ov.name + '</li>';
                        olId = ov.id;
                    });
                }
				
				$.post('/camera/allStatus', {}, function(sr) {
					var sitems = '', slId = 0;
					if (sr.list) {
						$.each(sr.list, function(si, sv) {
							sitems += '<option value="' + sv.id + '">' + sv.name + '</option>';
							slId = sv.id;
						});
					}
				
					mdl.open({
						id: 'add_activity',
						title: 'Add activity',
						content: $('<form/>', {
							class: 'uForm',
							html: $('<div/>', {
								class: 'iGroup fw price',
								id: 'store',
								html: $('<label/>', {
									html: 'Store'
								})
							}).append($('<input/>', {
								type: 'hidden',
								name: 'store'
							})).append($('<ul/>', {
								class: 'hdn',
								html: oitems
							}))
						}).append($('<div/>', {
							class: 'iGroup fw dGroup ',
							html: $('<label/>', {
								html: 'Date'
							})
						}).append($('<input/>', {
							type: 'text',
							class: 'cl',				
							name: 'date_start',
							onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
						})).append($('<div/>', {
							id: 'sCalendar'
						}))).append($('<div/>', {
							class: 'iGroup fw wp50',
							html: $('<label/>', {
								html: 'Time'
							})
						}).append($('<input/>', {
							type: 'time',
							name: 'time'
						}))).append($('<div/>', {
							class: 'iGroup fw wp50',
							html: $('<label/>', {
								html: 'End Time'
							})
						}).append($('<input/>', {
							type: 'time',
							name: 'end_time'
						}))).append($('<div/>', {
							class: 'dCLear'
						})).append($('<div/>', {
							class: 'iGroup fw',
							id: 'mdl_staff',
							html: $('<label/>', {
								html: 'Staff'
							})
						}).append($('<input/>', {
							type: 'hidden',
							name: 'mdl_staff'
						})).append($('<ul/>', {
							class: 'hdn',
							html: uitems
						}))).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Status'
							})
						}).append($('<select/>', {
							name: 'status',
							html: $('<option/>', {
								value: '0',
								html: 'No status'
							})
						}).append(sitems))).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Activity'
							})
						}).append($('<textarea/>', {
							name: 'activity'
						}))).append($('<div/>', {
							class: 'sGroup',
							html: $('<button/>', {
								type: 'button',
								class: 'btn btnSubmit',
								onclick: 'camera.sendActivity(this);',
								html: 'Done'
							})
						})),
						cb: function() {
							$('select').select();
							
							$('#sCalendar').calendar(function() {
								$('input[name="date_start"]').val($('#sCalendar > input[name="date"]').val());
								$('.dGroup > input + div').hide().parent().removeClass('act');
							});
							
							$('.uForm').on('click', function(event) {
								if (!$(event.target).hasClass('cl')) {
									console.log($(event.target).hasClass('cl'));
									$('.calendar').hide().parents('.iGroup').removeClass('act');
								}
							});
							
							$('#store > ul').html(oitems).sForm({
								action: '/invoices/objects',
								data: {
									lId: olId,
									query: $('#object > .sfWrap input').val() || '',
									all: 1
								},
								all: false,
								select: $('input[name="store"]').data(),
								s: true,
								link: 'objects/edit'
							}, $('input[name="store"]'));
							
							$('#mdl_staff > ul').html(uitems).sForm({
								action: '/users/all',
								data: {
									lId: ulId,
									query: $('#staff > .sfWrap input').val() || '',
									staff: 1
								},
								all: false,
								select: $('input[name="mdl_staff"]').data(),
								s: false,
								link: 'users/view'
							}, $('input[name="mdl_staff"]'));
						}
					});
				}, 'json');
			}, 'json');
		}, 'json'); 
	}, 
	sendActivity: function() {
		if ($('textarea[name="activity"]').val()) {
			var data = {
				object: Object.keys($('input[name="store"]').data()).join(','),
				staff: Object.keys($('input[name="mdl_staff"]').data()).join(','),
				date: $('input[name="date_start"]').val(),
				time: $('input[name="time"]').val(),
				end_time: $('input[name="end_time"]').val(),
				activity: $('textarea[name="activity"]').val(),
				status: $('select[name="status"]').val()
			}, err = null;
			if (!data.object) 
				err = 'Please, enter object';
			else if (!data.staff)
				err = 'Please, enter staff';
			else if (!data.date)
				err = 'Please, enter date';
			else if (!data.time)
				err = 'Please, enter time';
			else if (!parseInt(data.status))
				err = 'Please, enter status';

			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/camera/add_activity', data, function(r) {
					if (r == 'OK') {
						alr.show({
							class: 'alrSuccess',
							content: 'Activity was successfully added',
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
			}
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'Enter activity',
				delay: 2
			});
		}
	}
}

// Accordeon
function accordeon(el) {
	if ($(el).next().hasClass('hide')) {
		$(el).parent().parent().find('.open').removeClass('open').addClass('hide').slideUp('fast');
		$(el).next().slideDown('fast').removeClass('hide').addClass('open');
	} else
		$(el).next().removeClass('open').addClass('hide').slideUp('fast');
}

var sendMessage = {
	mdl: function(id, type) {
		$.post('/users/all', {staff: 1}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
			}
			mdl.open({
				id: 'sendMessage',
				title: 'Select staff',
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'iGroup fw',
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
						onclick: 'sendMessage.send(' + id + ', \'' + type + '\');'
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
			});
		}, 'json');
	}, send: function(id, type) {
		Page.get('/im/' + Object.keys($('input[name="staff"]').data())[0] + '?text=' + type + ';' + id);
	}
}

function checkExists() {
	if ($('input[name="name"]').val().trim() && $('input[name="lastname"]').val().trim()) {
		$.post('/users/check_by_name', {
			name: $('input[name="name"]').val().trim(),
			lanstname: $('input[name="lastname"]').val().trim()
		}, function(r) {
			if (r.res == 'OK') {
				var list = '',
					c = r.data.length;
				$.each(r.data, function() {
					list += '<a href="/users/view/' + this.id + '" onclick="Page.get(this.href); return false">' +
						(this.image ? '<img src="/uploads/images/users/' + this.id + '/' + this.image + '" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>') +
						this.name + ' ' + this.lastname +
						'<span class="exists_info">(<b>' + this.email + '</b>, ' + this.phone + ')</span>\
					</a>';
				});
				$('.exists_users').html('<h1>' + r.count + (r.count == 1 ? ' user' : ' users') + ' found' + (r.count > 10 ? '<span class="last10">(Printed last 10 results)</span>' : '') + '</h1>' + list);
			} else 
				$('.exists_users').empty();
		}, 'json');
	}
}

var agents = {
	addStore: function(f, event, id) {
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
            } else if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = lang[8];
                    break;
                }
            } else if (form[i].name == 'work_time_start' || form[i].name == 'work_time_end') {
                if (!form[i].value.length) {
                    err = 'Working time can not be empty';
                    break;
                }
            } else if (form[i].name == 'timezone') {
                if (!form[i].value.length) {
                    err = 'Timezone can not be empty';
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
        

        if (phones.length)
            data.append('phone', phones);
        else if (!err)
            err = 'Phone can not be empty';

        if (!sms)
            data.append('sms', '');
		
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
				url: '/agents/stores/send',
				data: data
			}).done(function(r) {
				loadBtn.stop($(event.target).find('button[type="submit"]'));
				if (r > 0) {
					alr.show({
						class: 'alrSuccess',
						content: lang[8],
						delay: 2
					});
					if (!id) Page.get('/agents/stores/edit/' + r);
					else Page.get('/agents/stores/');
				}
			});
		}
    }, delStore: function(id) {
		$.post('/agents/stores/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#store_' + id).addClass('deleted');
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        });
	}
}

var tasks = {
	add: function() {
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
				
				mdl.open({
					id: 'add_task',
					title: 'Add task',
					content: $('<div/>', {
							class: 'uForm',
							html: $('<div/>', {
								class: 'iGroup fw price',
								id: 'task_object',
								html: $('<label/>', {
									html: 'Store'
								})
							}).append($('<input/>', {
								type: 'hidden',
								name: 'task_object'
							})).append($('<ul/>', {
								class: 'hdn',
								html: oitems
							}))
						}).append($('<div/>', {
							class: 'iGroup fw',
							id: 'task_staff',
							html: $('<label/>', {
								html: 'Staff'
							})
						}).append($('<input/>', {
							type: 'hidden',
							name: 'task_staff'
						})).append($('<ul/>', {
							class: 'hdn',
							html: uitems
						}))).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Show when'
							})
						}).append($('<select/>', {
							name: 'task_type',
							onchange: 'tasks.type(this);',
							html: '<option value="1">Store opened</option><option value="2">Store closed</option><option value="3">Custom date</option>'
						}))).append($('<div/>', {
							class: 'iGroup fw dGroup taskDate',
							html: $('<label/>', {
								html: 'Date'
							})
						}).append($('<input/>', {
							type: 'text',
							class: 'cl',				
							name: 'task_date',
							onclick: 'closeCalendar(); $(this).next().show().parent().addClass(\'act\');'
						})).append($('<div/>', {
							id: 'task_calendar'
						}))).append($('<div/>', {
							class: 'iGroup fw taskDate',
							html: $('<label/>', {
								html: 'Time'
							})
						}).append($('<input/>', {
							type: 'time',
							name: 'task_time'
						}))).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Note'
							})
						}).append($('<textarea/>', {
							name: 'task_note'
						}))).append($('<div/>', {
							class: 'sGroup',
							html: $('<button/>', {
								type: 'button',
								class: 'btn btnSubmit',
								onclick: 'tasks.send(this);',
								html: 'Done'
							})
						})),
					cb: function() {
						$('#page').select();
						
						$('#task_calendar').calendar(function() {
                            $('input[name="task_date"]').val($('#task_calendar > input[name="date"]').val());
                            $('.dGroup > input + div').hide().parent().removeClass('act');
                        });
						
						$('#task_object > ul').html(oitems).sForm({
                            action: '/users/objects',
                            data: {
                                lId: olId,
                                query: $('#task_object > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: $('input[name="task_object"]').data(),
                            s: true,
							link: 'objects/edit'
                        }, $('input[name="task_object"]'));

                        $('#task_staff > ul').html(uitems).sForm({
                            action: '/users/all',
                            data: {
                                lId: ulId,
                                query: $('#task_staff > .sfWrap input').val() || '',
                                staff: 1
                            },
                            all: false,
                            select: $('input[name="task_staff"]').data(),
                            s: true,
							link: 'users/view'
                        }, $('input[name="task_staff"]'));
					}
				});
				
			}, 'json');
		 }, 'json');
	},
	type: function(e) {
		if ($(e).val() == 3)
			$('.taskDate').show();
		else
			$('.taskDate').hide();
	},
	send: function(b) {
		loadBtn.start($(b));
		var data = {},
			err = null;
			
		data.object_id = $('input[name="task_object"]').data() ? Object.keys($('input[name="task_object"]').data())[0] : 0;
		data.user_id = $('input[name="task_staff"]').data() ? Object.keys($('input[name="task_staff"]').data())[0] : 0;
		
		if (!data.object_id && !data.user_id)
			err = 'Select store or user';
		
		data.type = $('select[name="task_type"]').val();
		data.date = $('input[name="task_date"]').val();
		data.time = $('input[name="task_time"]').val();
		
		if (data.type == 3 && !data.date)
			err = 'Please, enter date';
		
		data.note = $('textarea[name="task_note"]').val().trim();
	
		if (!data.note) 
			err = 'Please, enter note';
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/tasks/send', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Task successfully added',
						delay: 2
					});
					mdl.close();
				} else if (r == 'date') {
					alr.show({
						class: 'alrDanger',
						content: 'Incorrect date',
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
	complite: function(id) {
		$.post('/tasks/complite', {
			id: id
		}, function(r) {
			if (r == 'OK') {
				$('#task_'+id).remove();
				alr.show({
					class: 'alrSuccess',
					content: 'You successfully complited this task',
					delay: 2
				});
			} else if (r == 'complited') {
				$('#task_'+id).remove();
				alr.show({
					class: 'alrDanger',
					content: 'This task is already complited',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		})
	}
}