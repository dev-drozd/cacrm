var Chat = {
	flag: 0,
	position: {},
	titleEvent: function(a) {
		console.log('1');
		w = $(window);
		$(a).css('cursor', 'move');

		
		if(event.pageX && event.pageY){
			var o = $(a).parent().position(),
				x = parseInt(o.left || 0)-event.pageX,
				y = parseInt(o.top || 0)-event.pageY;
			
			Chat.position = {
				left: x+event.pageX,
				top: y+event.pageY
			};
			
			w.mousemove(function(e){
				$(a).parent().css({
					bottom: 'inherit',
					left: x+e.pageX,
					top: y+e.pageY,
				});
			});	
			
			w.mouseup(function(){
				w.unbind("mousemove");
			});
		}
	},
	toogle: function(a){
		if (Chat.position.left == $(a).offset().left || Chat.position.top == $(a).offset().top)
			$(a).toggleClass('open');
	},
	search: function(a){
		$.post('/chat/'+(this.flag ? 'support' : 'im'), {
			q: a || '',
			p: 0
		}, function(r){
			console.log(r);
			$('.left-chat-list > ul').html(Chat.compileIm(r[0], Chat.flag));
		}, 'json');
	},
/* 	tab: function(a,b){
		$('.mini-chat-list').html('<div class="mini-chat-messages">\
			<div class="imAnim">\
				<span class="fa fa-pulse fa-spinner"></span>\
			</div>\
		</div>');
		if(b) $(a).addClass('support');
		else $(a).removeClass('support');
		this.flag = b;
		this.search();
	}, */
	compileIm: function(a,b){
		var tpl = '';
		if(a.length){
			for(var i = 0; i < a.length; i++){
				tpl += '<li class="mini-chat-person" data-uid="'+a[i].id+'" onclick="Chat.open(this'+(b ? ', 1' : '')+');">\
					'+(a[i].image ? '<img src="/uploads/images/'+(b ? 'guests' : 'users')+'/'+a[i].id+'/thumb_'+a[i].image+'" class="imImg">' :
					'<span class="fa fa-user-secret miniRound"></span>')+'\
					 '+a[i].name+' '+a[i].lastname+'\
				</li>';
			}
		}
		return tpl;
	},
	draggable: function(a){
		var w = $(window);
		a.mousedown(function(e){
			if($(e.target).hasClass('fa-times')){
				$('.block').remove();
				a.remove();
				w.unbind("mousemove");
			} else if($(e.target).hasClass('fa-window-maximize')){
				Page.get('/im/'+(
					Chat.flag ? 'support/' : ''
				)+a.attr('data-sid'));
			} else if($(e.target).hasClass('mini-chat-title')){
				if(e.pageX && e.pageY){
					var o = a.position(),
						x = parseInt(o.left || 0)-e.pageX,
						y = parseInt(o.top || 0)-e.pageY;

					w.mousemove(function(e){
						a.css({
							bottom: 'inherit',
							left: x+e.pageX,
							top: y+e.pageY,
						}).removeClass('tmp');
					});	
				}
			}
			return;
		});
		w.mouseup(function(){
			w.unbind("mousemove");
		});
		return a;
	},
	open: function(a,b){
		var tmp = $('.mini-chat.tmp'), 
			name = a.name || $(a).text(), 
			uid = a.sid || $(a).attr('data-uid'),
			id = (b ? 'chat_guest' : 'chat_user')+uid;
		if(tmp.length)
			tmp.remove();
		this.draggable($('<div class="mini-chat tmp" id="'+id+'" data-sid="'+uid+'">\
			<div class="mini-chat-title">\
				<span class="fa fa-times flRight"></span>\
				<span class="fa fa-window-maximize flRight"></span>\
				'+name+'\
			</div>\
			<div class="mini-chat-messages">\
				<div class="imAnim"><span class="fa fa-pulse fa-spinner"></span></div>\
			</div>\
			<div class="mini-chat-form">\
				<textarea name="message" placeholder="Enter message" data-sid="'+uid+'" onkeydown="if(event.keyCode == 13) {Chat.send(this); return false;}"></textarea>\
			</div>\
		</div>')).appendTo('body');
		$.post('/chat/history'+((this.flag || b) ? '_support' : ''), {
			sid: uid
		}, function(r){
			$('#'+id+' .mini-chat-messages').html(Chat.compileMsg(r[0])).scrollTop(999999999);
		}, 'json');
	},
	compileMsg: function(a){
		var tpl = '';
		if(a.length){
			for(var i = 0; i < a.length; i++){
				tpl += '<div class="mini-chat-message'+(
					(_user.id == a[i].from_uid || a[i].my) ? ' me' : ''
				)+'" data-id="'+(a[i].id || 0)+'">\
					<div class="mc-title">\
						<span class="name">'+a[i].name+' '+a[i].lastname+'</span>\
						<i class="name">'+a[i].date+'</i>\
					</div>\
					<div class="mc-content">\
						'+a[i].message+'\
					</div>\
				</div>';
			}
		}
		return tpl;
	},
	send: function(e){
		var a = $(e),
			id = a.attr('data-sid'),
			msg = a.val().trim(),
			tpl = $(this.compileMsg([{
				my: true,
				name: _user.name,
				lastname: _user.lastname,
				message: $('<div/>', {
					text: msg
				}).html(),
				date: 'Just now'
			}])),
			sp = /chat_guest([0-9]+)/i.test(e.parentNode.parentNode.id),
			tid = (sp ? 'chat_guest' : 'chat_user')+id,
			data = new FormData();
		data.append('id', id);
		data.append('message', msg);
		$('#'+tid+' .mini-chat-messages').append(tpl).scrollTop(999999999);
		a.val('');
		if(msg.length){
			$('.block').remove();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				processData: false,
				contentType: false,
				url: '/im/'+(sp ? 'support/' : '')+'send',
				data: data
			}).done(function(r){
				if($('.imDialog').length)
					$('.imDialog').append(Im.compile(r.message)).scrollTop(999999999);
				tpl.attr('data-id', r.message.id).find('.mc-content').html(r.message.message);
			});
		} else {
			alr.show({
				class: 'alrDanger',
				content: 'Message can not be empty',
				delay: 2
			})
		}
	}
};