var Timeout = undefined, Chat = {
	init: function(){
		var c = $('#chat'), w = $(window);
		c.mousedown(function(e){
			if($(e.target).hasClass('fa-times')){
				if($('.dialog-message').length > 2 && !localStorage.getItem('feedback')){
					Chat.feedback();
				} else {
					c.addClass('hide').css({
						bottom: '',
						left: '',
						top: '',
					}).find('.chatTitle').css('cursor', '');
					w.unbind("mousemove");	
				}
			} else if($(e.target).hasClass('chatTitle')){
				
				c.removeClass('hide').find('.chatTitle');//.css('cursor', 'move');
				
				if($('#chat .dialog-chat').length)
					$('#chat .dialog').scrollTop(999999999);
				
				/* if(e.pageX && e.pageY){
					var o = c.position(),
						x = parseInt(o.left || 0)-e.pageX,
						y = parseInt(o.top || 0)-e.pageY;

					w.mousemove(function(e){
						c.css({
							bottom: 'inherit',
							left: x+e.pageX,
							top: y+e.pageY,
						});
					});	
				} */
			}
			return;
		});
		w.mouseup(function(){
			w.unbind("mousemove");
		});
		$.get('/chat', function(r){
			c.find('#cnt').html(r.cnt);
			c.find('.chatTitle').mousedown();
			if(r.step !== 3){
				var a = new Audio();
				a.src = 'https://yoursite.com/notify.mp3';
				a.volume = 0.3;
				a.play();
			}
		}, 'json');
	},
	feedback: function(){
		if($('#chat div.dialog .feedback-chat').length) return;
		$('#chat div.dialog').append('<div class="feedback-chat">\
			<p>Please rate our services.</p>\
			<div class="rating" onmouseover="Chat.starFeedback(this, event);" onmouseout="Chat.starFeedback(this, event);" onclick="Chat.starFeedback(this, event);">\
				<span class="fa fa-star rStart mini" ratting="1"></span>\
				<span class="fa fa-star rStart mini" ratting="2"></span>\
				<span class="fa fa-star rStart mini" ratting="3"></span>\
				<span class="fa fa-star rStart mini" ratting="4"></span>\
				<span class="fa fa-star rStart mini" ratting="5"></span>\
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
			a.src = 'https://yoursite.com/notify.mp3';
			$('#chat .dialog').append('<div class="dialog-message">\
				<div class="dialog-img">\
				</div>\
				<div class="dialog-combi">\
					<div class="dm-title">\
						<span class="name">Your Company</span>\
						<i class="name">just now</i>\
					</div>\
					<div class="dm-content">Thank you for your patience, a technician will respond in approximately 60 seconds.\
					</div>\
				</div>\
			</div>').scrollTop(999999999);
			a.play();
		}, a || 10000);
	},
	auth: function(){
		var email = $('#chat_email').val();
		if(is_valid.email(email)){
			$.post('/chat/message', {
				email: email
			}, function(r){
				Chat.timeout(300);
				$('#chat #cnt').html(r.msg);
			}, 'json');
		} else {
			alr.show({
				 class: 'alrDanger',
				 content: 'Invalid email address',
				 delay: 1
			});
		}
	},
	msg: function(a){
		var msg = ($('#chat-message').val() || '').trim(),
			m = $('#chat .dialog .dialog-message');
		if(msg.length > 0){
			$('#chat-message').val('');
			$.post('/chat/message', {
				msg: msg,
				im: (!m.hasClass('me') && m.length > 0) ? 1 : 0
			}, function(r){
				Chat.timeout();
				$('#chat .cnt').hide();
				$('#chat .dialog-chat').show()
					.find('.dialog')
					.append(r.msg)
					.scrollTop(999999999);
			}, 'json');
		} else {
			alr.show({
				 class: 'alrDanger',
				 content: 'You can\'t send empty message',
				 delay: 1
			});	
		}
	}
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

// Alerts	
var alr = {
    show: function(o) {
		if(o.form){
			$(o.form).css('background', 'rgba(255, 0, 0, 0.18)').mousedown(function(){
				$(this).css('background', '').unbind('mousedown');
			})
		}
		if (!$('.alr').length) {
			$('<div/>', {
				class: 'alr ' + (o.class || 'alrInfo'),
				style: o.color ? 'background: ' + o.color : '',
				html: o.content
			}).append($('<span/>', {
				class: 'alrClose fa fa-times',
				onclick: 'alr.close()'
			})).appendTo('body');
			if (o.delay) {
				setTimeout(function() {
					alr.close();
				}, o.delay*1000);
			}
		}
    },
    close: function() {
        $('.alr').fadeOut('fast', function() {
            $('.alr').remove();
        });
    }
};