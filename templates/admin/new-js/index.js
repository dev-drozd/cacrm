/**
 /**
 * @appointment Index
 * @authors     Alexandr Drozd, Youlia Design
 * @copyright   Copyright Your Company 2020
 * @link        https://yoursite.com/
 * This code is copyrighted
*/

var barTimer = undefined,
	page = 0,
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
	online_users = null,
	is_valid = {
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
},  shortName = function(a){
	return a.split(' ').map(function(a){
			return a.substr(0, 1).toUpperCase()
	}).join('').replace('(', '');
},  is_mobile = function(){
    var u = (navigator.userAgent || navigator.vendor || window.opera);
    return /(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(u) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
        u.substr(0, 4)
    );
}, scrollWidth = function() {
    return window.innerWidth - document.documentElement.clientWidth;
}, menu = function(){
    $('.dd > a').attr('onclick', '$(this).next().slideToggle(\'fast\');return false;');
}, devBarcode = function(t){
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
}, checkBarcode = function(t){
	var type = $('#main-search-type').val();
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
}, searchTimer = undefined, mainSearch = function(a){
	var type = $('#main-search-type').val() || 'us';
	if(a.length){
		clearTimeout(searchTimer);
		searchTimer = setTimeout(function(){
			$.post('/search', {
				q: a//,
				//type: type
			}, function(j){
				$('.searchResult').html(j.content).show();
			}, 'json');
		}, 200);
	} else
		$('.searchResult').hide();
}, lprsz = function(){
	if($(window).width() <= 1410)
		$('body').addClass('nav-hide');
	else
		$('body').removeClass('nav-hide');
}, tab = {
	add: function(){
		box.open({
			id: 'tab',
			title: 'New Tab',
			content: '<div class="iGroup fw">\
				<label>Name</label>\
				<input name="title" value="'+document.title.replace('Your Company- ','')+'">\
			</div>',
			submit: ['Add', function(a){
				var title = this.title.value,
					link = location.href;
				tab.send(title, link);
				box.close('tab');
			}]
		});
	},
	send: function(a,b){
		$('.fav-tabs').append('<a href="'+b+'" onclick="Page.get(this.href); return false;">\
			'+a+'\
			<i class="fa fa-times" onclick="tab.remove(this)"></i>\
		</a>');
		$.post('/main/send_tab', {
			title: a,
			link: b
		})
	},
	remove: function(a){
		var tab = $(a.parentNode),
			title = tab.text().trim();
		tab.remove();
		$.post('/main/del_tab', {
			title: title
		});
	}
};

window.onload = function() {
    window.setTimeout(function(){
            window.addEventListener("popstate", function(e) {
                e.preventDefault();
                if (e.state)
                    Page.get(e.state.link, true);
                else
                    Page.get(window.location.pathname, true);
            }, false);
        },
    1);
};

//$(window).resize(function(){
    //menu();
	//lprsz();
    //$('.lMenu').removeClass('show');
//});

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
		$('.searchResult').hide();
		$('#main-search > input').val('');
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
		if($(window).width() <= 1410)
			$('body').removeClass('nav-hide');

		clearInterval(camtimer);
		$('body > header').addClass('main-loader');
		$('.notifications > .nfArea').hide();
        if (!back) {
            history.pushState({
                link: href
            }, null, href);
        }
        $.getJSON(href, function(r){
            if(r == 'loggout')
				location.reload();
            document.title = r.title;
			
			if(r.new_msg > 0){
				var mntf = $('#new_msg > a'), sp = mntf.find('.newMsg');
				if(sp.length)
					sp.text(r.new_msg);
				else {
					mntf.append($('<span/>', {
						class: 'newMsg',
						text: r.new_msg
					}));
				}
			} else
				$('#new_msg > a > .newMsg').remove();
			
			if(r.quotes_count > 0){
				var mntf = $('#new_quote > a'), sp = mntf.find('.newMsg');
				if(sp.length)
					sp.text(r.quotes_count);
				else {
					mntf.append($('<span/>', {
						class: 'newMsg',
						text: r.quotes_count
					}));
				}
			} else
				$('#new_quote > a > .newMsg').remove();
			
			if(r.notify_count)
				$('.notifications > #notify').addClass('nfCount nfc').text(r.notify_count);
			else
				$('.notifications > #notify').removeClass('nfCount nfc').text('');
			
            $('#cpu').text(r.cpu);
            $('#cpu_total').text(r.cpu);
            $('#time_script').text(r.time_script);
            $('#compile_tpl').text(r.compile_tpl);
            $('#query_cache').html(r.query_cache);
            $('#query_db').html(r.query_db);
            
            var pointsEl = $('#points_top');
            var currentPointsCount = parseInt(pointsEl.text()) || 0;
            var newPointsCount = parseInt(r.points) || 0;
            pointsEl.html(r.points);
            if (currentPointsCount != newPointsCount) {
            	setTimeout(function() {
            		var diff = newPointsCount - currentPointsCount;
            		pointsUpdateAnimation(diff);
            	}, 2000);
            }

            $('#page').html(r.content);
			$('body > header').removeClass('main-loader');
            $(window).scrollTop(0);
			Page.scroll = 0;
			$('.top_page').hide();
            Page.init(href);
        });
    }
}, leftPanel = {
	tab: function(a,b){
		if(b == 'staffs' || b == 'chat')
			$('#btn-add-group').show();
		else
			$('#btn-add-group').hide();
		if(b == 'nav' || b == 'chat'){
			$('.left-nav-tabs > a').removeClass('active');
			$('.left-nav-list,.left-chat-list').hide();
			$('.left-'+b+'-list').show();
		} else {
			Im.type = b == 'staffs' ? '' : b;
			$('.left-nav-tabs-botton > a').removeClass('active');
			Chat.preload = setTimeout(function(){
				$('.left-chat-list > ul').html('<li class="mini-chat-messages">\
					<div class="imAnim">\
						<span class="fa fa-pulse fa-spinner"></span>\
					</div>\
				</li>');
			}, b !== 'emails' ? 600 : 0);
			Chat.flag = (
				b == 'support' ? 1 : 0
			);
			if(b === 'emails'){
				$.post('/im/tab', {
					support: 2
				}, function(a){
					Im.f = false;
					$(".left-chat-list > ul").html(a.im.content);
				}, 'json');
			} else
				Chat.search();
		}
		$(a).addClass('active');
		return false;
	}
};

var sRs = 0;

function sortRes(a){
	var sort = 'sort-'+a.getAttribute('data-sort'), type = a.getAttribute('data-sort-type');
	$.post(location.href, {
		[sort]: type
	}, function(r){
		console.log(r);
		$('.userList').html(r.content);
	}, 'json');
	if(type == '0')
		a.setAttribute('data-sort-type', '1');
	else
		a.setAttribute('data-sort-type', '0');
	return false;
}

var Rec = {
	status: false,
	check: function(){
		return window.hasOwnProperty('webkitSpeechRecognition');
	},
	on: function(a,b){
		console.log(arguments);
		var k = a.keyCode, t = $(a.target), p = t.attr('placeholder');
		if(k == 113 && !Rec.status && Rec.check()){
			
			t.attr('placeholder', 'Speak into the microphone');
			Rec.status = true;
			var r = new webkitSpeechRecognition();
			
			r.continuous = false;
			r.interimResults = false;

			r.lang = "en-US";
			//r.ru = "ru-RU";
			r.start();

			r.onresult = function(e){
				console.log(b);
				if(typeof b === 'function'){
					var c = b;
					b = '';
				}
				$(a.target).val(e.results[0][0].transcript).trigger(b || 'keyup');
				if(c) c($(a.target).val());
				r.stop();
			};

			r.onerror = function(e){
				r.stop();
			};
			r.onend = function(e){
				Rec.status = false;
				t.attr('placeholder', p);
			};
		}
	}
};

function objToUrl(a){
	let s = '', l;
	for(var k in a){
		if(a[k])
			s += (
				s ? '&' : ''
			)+k+'='+encodeURI(a[k]);
	}
	l = s ? location.pathname+'?'+s : location.href;
	history.pushState({
		link: l
	}, null, l);
	return a;
}

// Search
var Search2 = function(a, t){
    query = a;

    $.post(location.pathname, objToUrl({
		'query': a,
		'long_title': $('input[name="long_title"]').val(),
		'long_description': $('input[name="long_description"]').val(),
		'title': $('input[name="title"]').val(),
		'description': $('input[name="description"]').val(),
		'keywords': $('input[name="keywords"]').val(),
		'canonical': $('input[name="canonical"]').val(),
		'navigation': $('input[name="navigation"]').val(),
		'image': $('input[name="image"]').val(),
		'event': $('select[name="searchSel"]').val() || '',
		'object': $('select[name="object"]').length ?  $('select[name="object"]').val() : Object.keys($('input[name="object"]').data() || {}).join(','),
		'date_start': $('#calendar > input[name="date"]').val(),
		'date_finish': $('#calendar > input[name="fDate"]').val(),
		'status': $('select[name="status"]').val(),
		'type': t || $('select[name="type"]').val(),
		'current_status': t || $('select[name="current_status"]').val(),
		'group': $('select[name="group"]').val(),
		'instore': $('input[name="instore"]').val(),
		'pickedup': $('input[name="pickedup"]').val(),
		'all': $('input[name="all"]').val(),
		'updated': $('input[name="updated"]').val(),
		'action': $('select[name="action"]').val(),
		'payment': $('select[name="payment"]').val(),
		'rating': $('select[name="rating"]').val(),
		'staff': Object.keys($('input[name="staff"]').data() || {}).join(','),
		'create': Object.keys($('input[name="create"]').data() || {}).join(','),
		'confirm': Object.keys($('input[name="confirm"]').data() || {}).join(','),
		'inv_type': Object.keys($('input[name="inv_type"]').data() || {}).join(','),
		'profit': $('input[name="profit"]').val(),
		'verified': $('input[name="verified"]').val(),
		'craiglist': $('input[name="craiglist"]').val(),
		'salary': $('input[name="salary"]').val(),
		'tax': $('input[name="tax"]').val(),
		'hours': $('input[name="hours"]').val()
	}), function(r) {
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

function pointsUpdateAnimation(diff) {
	var animEl = $('<div class="new_points_anim">'+diff+'</div>').appendTo('body');
	if (diff < 0) {
		animEl.css('color', '#E64A19');
	}

	var pointsEl = $('#points_top').removeClass('bounce');
	setTimeout(function() {
		animEl.remove();
		pointsEl.addClass('bounce');
	}, 1500);
}

var UsrPhone = {
	box: function(a,b){
		mdl.open({
			id: 'usr-confirm',
			title: 'Please, confirm',
			content: $('<div/>', {
				class: 'aCenter',
				html: $('<div/>', {
					html: 'Is the customer phone incorrect?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit ac',
					html: lang[159]
				}).click(function(){
					$.post('/users/phone-incorrect', {
						id: a
					});
					$(b).remove();
					mdl.close();
				})
			}).append($('<button/>', {
				class: 'btn btnSubmit dc',
				onclick: 'mdl.close();',
				html: lang[160]
			})))
		});
	}
}

function quoteBox(){
	$('#quote-alert').remove();
	mdl.open({
		id: 'quote-alert',
		title: 'There are unanswered quote requests',
		content: $('<div/>', {
			class: 'aCenter',
			html: $('<div/>', {
				html: 'Answer quote NOW!'
			})
		}).append($('<div/>', {
			class: 'cashGroup',
			html: $('<button/>', {
				class: 'btn btnSubmit ac',
				html: 'answer'
			}).click(function(){
				Page.get('/quote');
				mdl.close();
			})
		}).append($('<button/>', {
			class: 'btn btnSubmit dc',
			onclick: 'mdl.close();',
			html: 'later'
		})))
	});
}

function msgToApp(i){
	box.open({
		id: 'msg-to-app',
		title: 'Message to app',
		content: '<div class="iGroup fw">\
			<label>Name</label>\
			<input name="title" value="Attention!" required>\
		</div>\
		<div class="iGroup fw">\
			<label>Name</label>\
			<textarea name="message" required></textarea>\
		</div>',
		submit: ['Submit', function(a){
			var title = this.title.value,
				message = this.message.value;
			$.post('/settings/send_to_app', {
				id: i,
				title: title,
				message: message
			});
			box.close('msg-to-app');
		}]
	});
}

var Onsite = {
	create: function(){
		box.open({
			id: 'new-onsite',
			title: 'New on site service',
			content: '<div class="iGroup fw">\
				<label>Customer</label>\
				<div name="customer" json=\'/users/all?gId=5\' res="list" search="ajax10" class="sfWrap" required></div>\
			</div>\
			<div class="iGroup fw">\
				<label>Store</label>\
				<div name="store" json=\'/invoices/objects\' res="list" search="ajax4" class="sfWrap" required></div>\
			</div>\
			<div class="iGroup fw">\
				<label>Service</label>\
				<div name="service" json=\'/inventory/AllOnsite\' res="list" search="ajax10" class="sfWrap" required></div>\
			</div>\
			<div class="iGroup fw">\
				<label>Date of service</label>\
				<input type="date" name="date" style="width: 69%;" required>\
				<input type="time" name="time" style="width: 30%;" required>\
			</div>\
			<div class="iGroup fw">\
				<label>Staff</label>\
				<div name="staff" json=\'/users/all?staff=1\' res="list" search="ajax10" class="sfWrap" required></div>\
			</div>\
			<div class="iGroup fw">\
				<label>Issue</label>\
				<textarea name="issue" required></textarea>\
			</div>',
			submit: ['Create', function(a){
				$.post('/onsite/send', {
					customer_id: $('div[name=customer]').data('value'),
					store_id: $('div[name=store]').data('value'),
					service_id: $('div[name=service]').data('value'),
					staff_id: $('div[name=staff]').data('value'),
					date: this.date.value,
					time: this.time.value,
					issue: this.issue.value
				}, function(){
					alr.show({
						class: 'alrSuccess',
						content: 'Onsite service created successfully',
						delay: 3
					});
				});
				box.close('new-onsite');
			}]
		});
		$('#new-onsite div[json]').json_list();
	}
}