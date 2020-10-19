 /**
 * @appointment Im
 * @authors     Alexandr Drozd, Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
var Im = {
	page: 1,
	f: false,
	upage: 1,
	uf: false,
	sp: false,
	type: '',
	getYoutube: function(a, b){
		var e = $(a),
			w = e.width(),
			h = e.height(),
			p = e.height()/e.width(),
			frame = $('<iframe/>', {
				width: w,
				height: h,
				frameborder: 0,
				allowfullscreen: true,
				src: '//www.youtube.com/embed/'+b+(b.indexOf('?') > 0 ? '&' : '?')+'autoplay=true'
		});
		function rs(){
			if(location.pathname.match(/im/i)){
				frame.height(frame.width()*p);
			} else
				$(window).unbind('resize', rs);
		}
		e.replaceWith(frame);
		$(window).resize(rs);
		rs();
	},
	createGroup: function(a){
		mdl.open({
			id: 'chat_group',
			title: 'Chat group',
			content: '<div class="uForm">\
				<div class="iGroup fw">\
					<label>Name</label>\
					<input type="text" name="title" value="">\
				</div>\
				<div class="iGroup fw">\
					<label>Staffs</label>\
					<div id="chat-group-staffs" name="chat-group-staffs" json=\'/users/all?staff=1\' res="list" search="ajax10" class="sfWrap" multiple></div>\
				</div>\
				<div class="sGroup">\
					<button type="button" class="btn btnSubmit" onclick="Im.sendGroup()">Create</button>\
				</div>\
			</div>',
			cb: function(){
				$('#chat_group div[json]').json_list();
			}
		});
	},
	sendGroup: function(){
		var _b = $('#chat_group'),
			_n = _b.find('input[name=title]').val(),
			_s = _b.find('#chat-group-staffs').data().value;
		$.post('/im/send_group', {
			name: _n,
			ids: _s
		}, function(r){
			if(r.id > 0)
				$('.left-chat-list > ul').prepend('<li class="mini-chat-person" data-id="g-'+r.id+'" onclick="Chat.open(this);"><span class="fa fa-user-secret miniRound"></span>'+r.name+'</li>');
		}, 'json');
		mdl.close('chat_group');
	},
	history: function(){
		if (Im.f) return;
		Im.f = true;
		support = location.pathname.indexOf('support') != -1 ? 'support/' : (location.pathname.indexOf('emails') != -1 ? 'emails/' : '');
		$.post('/im/'+ support + 'history', {
			page: Im.page,
			id: $('.imDialog').attr('data-id')
		}, function(r) {
			if (r.res_count) {
				var h = document.querySelector('.imDialog').scrollHeight;
				Im.page ++;
				Im.f = false;
				$('.imDialog').prepend(r.content);
				$('.imDialog').scrollTop(document.querySelector('.imDialog').scrollHeight - h);
			}
		}, 'json');
	},
	readEmail: function(a){
		$.post('/im/read-email', {email: a}, function(){
			$('.imTabs > span:last').click();
		});
	},
	tab: function(a,b){
		Chat.flag = (
			b == 1 ? 1 : 0
		);
		Im.type = b == 1 ? 'support' : (
			b == 2 ? 'emails' : ''
		);
		var e = $(a).parent();
		if(b){
			Im.sp = true;
			$('.imUser.gc').hide();
		} else {
			Im.sp = false;
			$('.imUser.gc').show();
		}
		if (b == 2) {
			$('.btnIm + ul').hide();
			$('.newEmail, .emails_list').show();
		} else {
			$('.btnIm + ul').show();
			$('.newEmail, .emails_list').hide();
		}
		history.pushState({
				link: '/im'+(b ? (b == 1 ? '/support' : '/emails') : '')
			}, null, '/im'+(b ? (b == 1 ? '/support' : '/emails') : '')
		);
		if(b){
			Im.f = true;
			$('.imDialog').html('<div class="imNoCont">No chats</div>');
		} else {
			Im.open($('.imUser.gc')[0]);
		}
		$.post('/im/tab', {
			support: b
		}, function(a){
			if(b)
				Im.f = false;
			$('.imDialogs').html(a.im.content);
		}, 'json');
		return b ? (b == 1 ? e.removeClass('email').addClass('support') : e.removeClass('support').addClass('email')) : e.removeClass('support').removeClass('email');
	},
	setFocus: function(){
		var a = $('textarea'), b = a.val();
		a.val('').focus().val(b);
	},
	tooltip: function(a,b,c){
		var el = $(a), div = el.find('div');
		if(typeof a.myData == 'undefined'){
			a.myData = '';
			div.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
			$.getJSON('/tags/'+b.toLowerCase()+'/'+c, function(j){
				var h = '';
				if(j.status == 'OK'){
					if(j.images)
						j.photo = '/uploads/images/inventory/'+j.id+'/'+j.images.split('|')[0];
					if(j.photo)
						h += '<img src="'+j.photo+'">';
					if(j.event)
						h += '<h4>Event: '+j.event+'</h4>';
					if(j.title)
						h += '<h4>'+j.title+'</h4>';
					if(j.descr)
						h += '<p>'+j.descr+'</p>';
					if(j.store)
						h += '<p class="ttPrice">Store: '+j.store+'</p>';
					if(j.price)
						h += '<p class="ttPrice">Price: $'+j.price.replace('$', '')+'</p>';
					if(j.amount)
						h += '<p class="ttPrice">Amount: $'+j.amount.replace('$', '')+'</p>';
					if(j.lack)
						h += '<p class="ttPrice">Missing amount: '+(parseFloat(j.lack) > 0 ? '<span class="plusLack">$'+j.lack+'</span>' : (
						parseFloat(j.lack) < 0 ? '<span class="minLack">$'+j.lack+'</span>' : '$'+j.lack
						))+'</p>';
					if(j.date)
						h += '<p class="ttPrice">Date: '+j.date+'</p>';
					div.html(h);
				}
				a.myData = h;
			});
		}
	},
	tagLink: function(a,b){
		var url = location.origin;
		switch(a.toLowerCase()){
			
			case 'purchase':
			case 'rma':
				url += '/purchases/edit/'+b;
			break;
			
			case 'issue':
				url += '/issues/view/'+b;
			break;

			case 'bug':
				url += '/bugs/'+b;
			break;
			
			case 'invoice':
				url += '/invoices/view/'+b;
			break;

			case 'stock':
				url += '/inventory/view/'+b;
			break;
			
			case 'user':
				url += '/users/view/'+b;
			break;
			
			case 'cash':
				url += '/cash';
			break;

			case 'camera':
				return false;
			break;
			
		}
		window.open(url);
		return false;
	},
	undo: function(id){
		if(id){
			$.post('/im/undo', {
				id: id,
				sid: $('.imDialog').attr('data-id')
			}, function(r){
				var msg = $('.imMes[data-msg="'+id+'"] .imMesText'), date = msg.find('.imDate').text();
				msg.html('<span class="fa fa-times imDel" onclick="Im.del('+id+')"></span><span class="imDate">'+date+'</span>'+r.msg);
			}, 'json');
		}
	},
	paste: function(e){
		var items = (e.clipboardData  || e.originalEvent.clipboardData).items, data = {files:[]};
		var upload = $('.imageUpload')[0];
		for(var i = 0; i < items.length; i++){
			if(items[i].type.indexOf("image") === 0){
				e.preventDefault();
				var file = items[i].getAsFile();
				file.name = 'file '+(i+1);
				data.files.push(file);
			}
		}
		upload.check(data);
		console.log(e);
	},
	init: function(){
		var arr = location.pathname.match(/\/im\/([0-9]+)/i);
		if(arr){
			$('.imLeft .imUser[data-id="'+arr[1]+'"]').addClass('active').find('.imCount').remove();
		} else {
			
		}
	},
	search: function(e){
		$.post(location.href, {
			query: e.value
		}, function(r){
			Im.upage = 1;
			Im.uf = false;
			$('.imDialogs').html(r.content);
		}, 'json');
	},
	open: function(e){
		Im.page = 1;
		Im.f = true;
		var el = $(e), id = el.attr('data-id'),
			count = Number(el.find('.imCount').text()),
			notif = $('#new_msg > a > .newMsg'),
			support = (Im.type ? Im.type+'/' : '');//location.pathname.indexOf('support') != -1 ? 'support/' : (location.pathname.indexOf('emails') != -1 ? 'emails/' : '');
			
		console.log(support);
		if(id == 'all' || id == '0')
			$('.sendAll').show();
		else
			$('.sendAll').hide();
		if(count > 0 && notif){
			var msg = Number(notif.text())-count;
			if(msg > 0) notif.text(msg);
			else notif.remove();
		}
		$('.imDialog').html($('<div/>', {
			class: 'imAnim',
			html: $('<span/>', {
				class: 'fa fa-pulse fa-spinner'
			})
		}));
		$('.newEmail-info').remove();
		$('.imLeft .imUser.active').removeClass('active');
		el.addClass('active').find('.imCount').remove();
		history.pushState({
			link: '/im/'+support+id
		}, null, '/im/'+support+id);
		$('.imDialog').attr('data-id', id);
		$('.imLeft').removeClass('open');
		$.post('/im/'+support+'history', {
			id: id
		}, function(r){
			Im.f = false;
			$('.imDialog').html(r.content).scrollTop(999999999);
			Im.setFocus();
		}, 'json');
	},
	send_flag: false,
	send: function(e) {
		if ((this.send_flag || !$('#message').val().trim()) && !$('.filesUpload').length && !$('.imageUpload').length)
			return false;
		this.send_flag = true;
		if ($('.newEmail-info').length && (!$('input[name="nmEmail"]').val() || !$('input[name="nmSubject"]').val()))  {
			alr.show({
				class: 'alrDanger',
				content: 'Enter email and subject',
				delay: 2
			});
			return false;
		}
		if ($('.imNoCont').length) $('.imNoCont').remove();
		$('.imMessage button').attr('disabled', 'disabled').prepend($('<span/>', {
			class: 'fa fa-spin fa-circle-o-notch'
		}));
		var arr = location.pathname.match(/\/im\/(?:(support|emails)\/)?(.+)/i), msg = $('#message').val().trim(),
			data = new FormData(), support = location.pathname.indexOf('support') != -1 ? 'support/' : (location.pathname.indexOf('emails') != -1 ? 'emails/' : ''),
			email = (arr && arr[1] == 'emails' ? true: false);
		data.append('id', ($('.newEmail-info').length ? ($('input[name="nmEmail"]').val() + ':|:' + $('input[name="nmSubject"]').val()) : (arr ? arr[2] : 0)));
		data.append('message', msg);
		data.append('all', $('#sendAll').val());
		
		if ($('.newEmail-info').length)
			data.append('new', 1);
		
		if ($('select[name="nmFrom"]').length) 
			data.append('from', $('select[name="nmFrom"]').val());
		
		if($('#sendAll').val() === '1'){
			$('#sendAll').val(0).click();
		}
		if ($('.filesUpload').length) {
			var files = $('.filesUpload')[0].files;
			if (!$.isEmptyObject(files)) {
				for (var n in files) {
					data.append('files[]', files[n]);
				}
			}
		}
		if ($('.imageUpload').length) {
			var files = $('.imageUpload')[0].files;
			if (!$.isEmptyObject(files)) {
				for (var n in files) {
					data.append('images[]', files[n]);
				}
			}
		}
		if(msg.length || files || images){
			if (email) {
				var d = new Date();
				$('.imDialog').append(Im.compile({
					'type': 'messages',
					'id': '',
					'uid': '',
					'sid': '',
					'image': '',
					'name': 'admin@yoursite.com',
					'lastname': '',
					'message': msg,
					'my': true,
					'date': d.getFullYear() + '-' + (d.getMonth() + 1 < 10 ? '0' + (d.getMonth() + 1) : (d.getMonth() + 1)) + '-' + d.getDate()
				})).scrollTop(999999999);
				$('#message').val('');
			}
			if(support)
				$('.block').remove();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				processData: false,
				contentType: false,
				url: '/im/'+support+'send',
				data: data
			}).done(function(r) {
				Im.send_flag = false;
				$('.imMessage button').attr('disabled', false).find('span').remove();
				if (r == 'err_file_type') {
					alr.show({
						class: 'alrDanger',
						content: 'Uploading files types are prohibited',
						delay: 2
					});
				} else if (r == 'err_image_type') {
					alr.show({
						class: 'alrDanger',
						content: 'Uploading images types are prohibited',
						delay: 2
					});
				} else if (r.err == 'no_acc') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do this',
						delay: 2
					});
					$('.imDialog > .imMes').last().remove();
				} else {
					if ($('.newEmail-info').length) {
						Page.get('/im/emails/' + ($('input[name="nmEmail"]').val() + ':|:' + $('input[name="nmSubject"]').val()).replace('.', '::'));
					} else {
						var minichat = $('#'+(support ? 'chat_guest' : 'chat_user')+(arr ? arr[1] : 0)+' .mini-chat-messages');
						
						if(minichat.length)
							minichat.append(Chat.compileMsg([r.message])).scrollTop(999999999);
						
						if (!email) {
							$('.imDialog').append(Im.compile(r.message)).scrollTop(999999999);
							$('#message').val('');
						}
						$('.imMessage .thumbnails').html('');
						$('.imMessage .files').html('');
						
						$('.filesUpload')[0].input.value = '';
						$('.filesUpload')[0].files = {};
						$('.filesUpload')[0].count = 0;
						
						$('.imageUpload')[0].input.value = '';
						$('.imageUpload')[0].files = {};
						$('.imageUpload')[0].count = 0;
						
						$('.imDialog').css('height', $('.im').height() - $('.imMessage').height() - 35);
					}
				}
			});
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'Message can not be empty',
				delay: 2
			})
		}
	},
	viewed: function(a){
		$.post('/im/viewed', {sid: a});
	},
	compile: function(d){
		console.log(d);
		var luid = $('[data-usr]').last().attr('data-usr'), first = (d.uid != luid);
		return '<div class="imMes'+(d.my ? ' my' : '')+(first ? ' first' : '')+'" data-msg="'+d.id+'" data-usr="'+d.uid+'">\
				'+(first ? '<a href="href=/users/view/'+d.uid+'" target="_blank">\
				'+(d.image ? '<img src="/uploads/images/users/'+d.uid+'/thumb_'+d.image+'" class="imImg">' : '<span class="fa fa-user imImg"></span>')+'\
			</a>' : '')+'\
			<div>\
				<div class="imMesTop">\
					'+(first ? '<a href="#">'+d.name+' '+d.lastname+'</a>' : '')+'\
				</div>\
				<div class="imMesText'+((d.my && (
					/\/im\/(?:support\/)?([0-9]+)/.test(location.pathname)
				)) ? ' new' : '')+'">\
					' + (d.my ? '<span class="fa fa-times imDel" onclick="Im.del(' + d.id + ')"></span>' : '') +
					'<span class="imDate">'+d.date+'</span>\
					'+d.message+'\
				</div>\
			</div>\
		</div>';
	},
	del: function(id) {
		support = location.pathname.indexOf('support') != -1 ? 'support/' : (location.pathname.indexOf('emails') != -1 ? 'emails/' : '');
		if (id) {
			$.post('/im/'+support+'del', {
					id: id,
					sid: $('.imDialog').attr('data-id')
				}, function(r) {
				if (r == 'OK') {
					if (support == 'emails/') {
						$('.imMes[data-msg="'+id+'"]').remove();
					} else {
						var date = $('.imMes[data-msg="'+id+'"] .imMesText > .imDate').text();
						$('.imMes[data-msg="' + id + '"] .imMesText').html($('<span/>', {
							class: 'fa fa-undo imDel',
							onclick: 'Im.undo('+id+');'
						})).append($('<span/>', {
							class: 'imDate',
							text: date
						})).append($('<span/>', {
							class: 'fa fa-trash imDeleted'
						})).append('Message deleted');
					}
				} else if (r == 'ERR') {
					alr.show({
						class: 'alrDanger',
						content: 'You are not allowed to do this',
						delay: 2
					});
				}
			})
		}
	},
	delTree: function(e) {
		var id = $(e).parent().attr('data-id');
		if (id) {
			$.post('/im/delTree', {
					id: id
				}, function(r) {
				if (r == 'OK') {
					$(e).parent().remove();
					$('.imDialog').empty();
				} else if (r == 'ERR') {
					alr.show({
						class: 'alrDanger',
						content: 'You are not allowed to do this',
						delay: 2
					});
				}
			})
		}
	},
	newEmail: function() {
		if (!$('.newEmail-info').length) {
			$.post('/im/config_emails', {}, function(r) {
				if (r) {
					$('.imChats').prepend($('<div/>', {
						class: 'newEmail-info',
						html: $('<div/>', {
							class: 'nmEmail',
							html: $('<label/>', {
								html: 'Email:'
							})
						}).append($('<input/>', {
							type: 'email',
							name: 'nmEmail'
						}))
					}).append($('<div/>', {
						class: 'nmSubject',
						html: $('<label/>', {
							html: 'Subject:'
						})
					}).append($('<input/>', {
						type: 'text',
						name: 'nmSubject'
					}))).append($('<div/>', {
						class: 'nmFrom',
						html: $('<label/>', {
							html: 'From:'
						})
					}).append($('<select/>', {
						name: 'nmFrom',
						html: r
					}))));
					
					$('select').select();
					$('.imUser.active').removeClass('active');
					$('.imDialog').removeAttr('data-id').html('');
				} else {
					alr.show({
						class: 'alrDanger',
						content: 'You have no default email',
						delay: 2
					});
				}
			});
		}
	}
};