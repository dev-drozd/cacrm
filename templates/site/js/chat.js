var Timeout = undefined, Chat = {
	init: function(){
		var c = $('#chat'), w = $(window);
		c.mousedown(function(e){
			if($(e.target).hasClass('chat-close')){
				if($('.chat-dialog > .cd-block').length > 2 && !localStorage.getItem('feedback')){
					Chat.feedback();
				} else {
					console.log('ok');
					c.hide().css({
						bottom: '',
						left: '',
						top: '',
						transition: ''
					}).find('.chat-title').css('cursor', '');
					w.unbind("mousemove");
					$('#chat-btn').fadeIn();
				}
			} else if($(e.target).hasClass('chat-title') && $(window).width() > 650){
				
				c.removeClass('hide').find('.chat-title').css('cursor', 'move');
				
				if(e.pageX && e.pageY){
					var o = c.position(),
						x = parseInt(o.left || 0)-e.pageX,
						y = parseInt(o.top || 0)-e.pageY;

					w.mousemove(function(e){
						c.css({
							bottom: 'inherit',
							left: x+e.pageX,
							top: y+e.pageY,
							transition: 'none'
						});
					});
					$('.back-chat').show();
				}
			}
			return;
		});
		w.mouseup(function(){
			$('.back-chat').hide();
			w.unbind("mousemove");
		});
		$.get('/chat', function(r){
			c.find('.chat-name').text(r.name);
			c.find('#cnt').html(r.cnt);
			$('.chat-btn').click();
			if(r.step !== 3){
				var a = new Audio();
				a.src = '/notify.mp3';
				a.volume = 0.3;
				a.play();
			}
		}, 'json');
	},
	mobScroll: function(){
		setTimeout(function(){
			$('#chat').scrollTop(999999);
			//$('body').scrollTop(999999);
		}, 300);
	},
	open: function(e){
		$(e).hide();
		$('#chat').show();
		if($('#chat .chat-dialog').length)
			$('#chat .chat-dialog').scrollTop(999999999);
	},
	feedback: function(){
		if($('.chat-dialog .feedback-chat').length) return;
		$('.chat-dialog').append('<div class="feedback-chat">\
			<p>Please rate our services.</p>\
			<div class="rating" onmouseover="Chat.starFeedback(this, event);" onmouseout="Chat.starFeedback(this, event);" onclick="Chat.starFeedback(this, event);">\
				<span class="fa fa-star rStart mini" ratting="1"></span>\
				<span class="fa fa-star rStart mini" ratting="2"></span>\
				<span class="fa fa-star rStart mini" ratting="3"></span>\
				<span class="fa fa-star rStart mini" ratting="4"></span>\
				<span class="fa fa-star rStart mini" ratting="5" onclick="window.open(\'https://search.google.com/local/writereview?placeid=\'+map_id)"></span>\
			</div>\
		</div>').scrollTop(999999999);
	},
	starFeedback: function(a, b, c, d){
		b.preventDefault();
		switch(b.type){
			
			case 'mouseover':
				var count = $(b.target).attr('ratting');
				for(var i = 0; i < count; i++){
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
				for(var i = 0; i < count; i++){
					$(a).find('span').eq(i).addClass('active').addClass('r_'+count);
				}
				if(count > 0){
					$.post('/chat/feedback', {
						ratting: count
					}, function(r){
						localStorage.setItem('feedback', 1);
					});
				}
			break;
		}
	},
	timeout: function(a){
		if(Timeout) return;
		Timeout = setTimeout(function(){
			var a = new Audio();
			a.src = '/notify.mp3';
			$('#chat .chat-dialog').append('<div class="cd-single">\
				<label>Your Company:</label>\
				<p>Thank you for your patience, a technician will respond in approximately 60 seconds.</p>\
			</div>').scrollTop(999999999);
			a.play();
		}, a || 10000);
	},
	auth: function(e){
		e.preventDefault();
		$.post('/chat/message', {
			name: e.target.name.value,
			email: e.target.email.value,
			phone: e.target.phone.value
		}, function(r){
			//gtag_report_conversion_chat();
			Chat.timeout(300);
			$('#chat #cnt').html(r.msg);
		}, 'json');
		return false;
	},
	textarea: function(e){
		if(((e.keyCode == 13) || (e.keyCode == 10)) && (e.ctrlKey == false)){
			e.preventDefault();
			$(e.target.form).submit();
		}
		if(((e.keyCode == 13) || (e.keyCode == 10)) && (e.ctrlKey == true)){
			e.preventDefault();
			var val = '\r\n';
			e.target.focus();
			if(document.selection){
				e.target.form.document.selection.createRange().text = e.target.form.document.selection.createRange().text+val;
			} else if(e.target.selectionStart != undefined){
				var element = e.target; 
				var str = element.value; 
				var start = element.selectionStart; 
				var length = element.selectionEnd - element.selectionStart; 
				element.value = str.substr(0, start) + str.substr(start, length) + val + str.substr(start + length);
			} else {
				e.target.value += val; 
			}
		}
		return false;
	},
	msg: function(e){
		e.preventDefault();
		var msg = e.target.message.value.trim(),
			m = $('#chat .chat-dialog .cd-single');
		e.target.message.value = '';
		e.target.message.focus();
		$('#chat').find('.chat-dialog').append('<div class="cd-block" data-id="">\
			<div class="cd-image">\
				<span class="fa fa-user-secret"></span>\
			</div>\
			<div class="cd-single">\
				<label>Me:</label>\
				<p>'+msg+'</p>\
			</div>\
		</div>').scrollTop(999999999);
		$.post('/chat/message', {
			msg: msg,
			im: m.length > 0 ? 1 : 0
		}, function(r){
			Chat.timeout();
			//$('#chat').find('.chat-dialog')
			//	.append(r.msg)
			//	.scrollTop(999999999);
		}, 'json');
	}
};