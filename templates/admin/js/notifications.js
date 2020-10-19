 /**
 * @appointment Notifications
 * @authors     Alexandr Drozd, Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
$.getJSON('/language/'+_user.lang+'/admin/javascript.json', function(r){
	window.lang = r;
});
 
var Notify = {
	minus:function(){
		setTimeout(function(){
			$('#notify').text(Number($('#notify').text())-1);
		}, 600);
	},
	tab_id: null,
	perm: function(){
		var t = new Date().getTime();
		this.tab_id = t;
		localStorage.setItem('tab_id', t);
		return Notification.permission !== "granted" ? Notification.requestPermission() : true;
	},
	get: function(){
		$.getJSON('/users/get_notify', function(r){
			if(r.count > 0){
				$('.notifications > .nfArea').html(r.html).toggle();
				$('.notifications > .nfCount').text(r.count);	
			}
		});
	},
	show: function(a, b, c, d, e){
		if(String(this.tab_id) !== localStorage.getItem('tab_id'))
			return false;
		var sound = document.getElementById('notify_sound');
		if(window.status == 'blur'){
			this.perm();
			var opt = {
				body: a
			};
			if(c.image)
				opt.icon = location.origin+'/uploads/images/users/'+c.uid+'/thumb_'+c.image;
			
			console.log('OK');
			
			var tab = new Notification(c.name + ' ' + c.lastname, opt);
			tab.onclick = function(){
				try {
					if(typeof b === 'function') 
						b();
					else 
						Page.get(b);
					window.focus();
					tab.close();
				} catch(e){
					console.log(e);
				}
			};
		} else {
			var alrt = $('<div/>', {
				class: 'notif',
				css: {
					opacity: 0,
					right: '-300px'
				},
				html: $('<div/>', {
					class: 'tNotif',
					html: c.name ? $('<div/>', {
						class: 'uNotif',
						html: (c.image ? $('<img/>', {
							class: 'imgNotif',
							src: '/uploads/images/users/' + c.uid + '/thumb_' + c.image
						}) : '')
					}).append(c.name + ' ' + c.lastname) : ''
				}).click(function(){
					if(typeof b === 'function') 
						b();
					else 
						Page.get(b);
					$(this.parentNode).remove();
				}).append(a)
			}).append($('<span>', {
				class: 'fa fa-times'
			}).click(function(){
				$(this.parentNode).remove();
			})).appendTo('body').animate({
				right: 20,
				opacity: 1
			}, 300);
			setTimeout(function(){
				alrt.animate({
					right: '-300',
					opacity: 0
				}, 300, function(){
					this.remove();
				});
			}, e || 6000);
		}
		sound.src = d || '/notify_lite.mp3';
		sound.play();
	},
	go: function(a, b){
		Notify.show(a, b);
	},
	block: false,
	blockBox: function(a){
		if(this.block){
			$('<div class="mdlWrap new_guest block" style="display: block; overflow: auto; padding-top: 70px;"></div>').appendTo('body');
/* 			mdl.open({
				title: 'You can not continue until you answer website visitor',
				class: 'new_guest',
				id: a,
				content: '<div class="uForm">\
					<div class="iGroup fw">\
						<label>Message</label>\
						<textarea name="message"></textarea>\
					</div>\
					<div class="sGroup">\
						<button type="button" class="btn btnSubmit" onclick="">Send</button>\
					</div>\
				</div>',
				cb: function(){
					$('#'+a+' button[type="button"]').click(function(){
						var text = $('#'+a+' textarea[name="message"]').val() || '';
						if(text.length > 0){
							Notify.block = false;
							mdl.close(a);
							$.post('/im/support/welcom', {
								id: a,
								msg: text,
							});
						} else {
							alr.show({
								 class: 'alrDanger',
								 content: 'You can\'t send empty message',
								 delay: 1
							});
						}
					});
				}
			}); */
		}
	},
	pending: function(t, p){
		var s = '';
		if(typeof p === 'object'){
			for(var k in p){
				s += '&'+k+'='+p[k];
			}
		}
		$.getJSON('/sub/'+t+'.sock?time='+$.now()+s, function(j){
			switch(j.type){
				
				case 'unblock_box':
					Notify.block = false;
					$('.block').remove();
					if(j.staff_id != _user.id){
						$('#chat_guest'+j.id).remove();
						alr.show({
							class: 'alrSuccess',
							content: j.name+' '+j.lastname+' began a dialogue with the visitor',
							delay: 3
						});	
					}
				break;
				
				case 'chat_guest_visit':
					Notify.block = true;
					if(window.status == 'blur'){
						Notify.show('There is new guest in the website', function(){
							Notify.blockBox(j.id);
						}, j);	
					}
					Notify.blockBox(j.id);
				break;
				
				case 'reload':
					if(confirm("There are new changes on website. Please, refresh page")){
						//location.href = '/?loggout=admin';
						location.reload(true);
					}
				break;
				
				case 'activity':
					$('#act > .tbl > .tHead').after(j.html);
				break;
				
				case 'new_visitor':
					$('#visitors_online > span').html(parseInt($('#visitors_online > span').html()) + 1);
				break;
				
				case 'purchase':
					var notf = $('.notifications > .nfCount');
					notf.text(Number(notf.text())+1);
					Notify.show(j.message, j.id, j);
				break;
				
				case 'viewed_messages':
					if(new RegExp(/im/+j.uid).test(location.pathname))
						$('.imMesText.new').removeClass('new');
				break;
				
				case 'emails':
					Notify.show(j.message, function(){
						Page.get('/im/emails');
					}, j);
				break;
				
				case 'chat_guest_msg':
					var arr = location.pathname.match(/\/im\/support\/?([0-9]+)?/), chat = $('#chat_guest'+j.chanel_id);
					if(j.block){
						Notify.block = true;
						Notify.blockBox(j.id);
					}
					j.message = j.msg;
					delete j.msg;
					if(arr || chat.length){
						if(arr){
							if($('.imDialog[data-id="'+j.chanel_id+'"]').length){
								$('.imDialog')
									.append(Im.compile(j))
									.scrollTop(999999999);
							} else {
								var imUser = $('.imUser[data-id="'+j.chanel_id+'"]').prependTo('.imDialogs'), imCount = imUser.find('.imCount');
								if(imCount.length)
									imCount.text(Number(imCount.text())+1);
								else {
									imUser.append($('<span/>', {
										class: 'imCount',
										text: '1'
									}));
								}
							}	
						}
						chat.find('.mini-chat-messages').append(Chat.compileMsg([j])).scrollTop(999999999);
					} else if(!j.staff_ids || $.inArray(String(_user.id), String(j.staff_ids).split(',')) != -1){
/* 						var mntf = $('#new_msg > a'), sp = mntf.find('.newMsg');
						if(sp.length){
							sp.text(Number(sp.text())+1);
						} else {
							mntf.append($('<span/>', {
								class: 'newMsg',
								text: 1
							}));
						} */
						Chat.flag = 1;
						Chat.open({
							sid: j.chanel_id,
							name: j.name+' '+j.lastname
						}, 1);
						Notify.show(j.message, function(){
							Chat.flag = 1;
							Chat.open({
								sid: j.chanel_id,
								name: j.name+' '+j.lastname
							}, 1);
						}, j, '/alarm-frenzy.mp3');
					}
				break;
				
				case 'bugs':
					if(j.uid != _user.id){
						if(String(Notify.tab_id) == localStorage.getItem('tab_id')){
							var mntf = $('#new_bug > a'), sp = mntf.find('.newMsg');
							if(sp.length){
								sp.text(Number(sp.text())+1);
							} else {
								mntf.append($('<span/>', {
									class: 'newMsg',
									text: 1
								}));
							}
							Notify.show(j.comment, function(){
								Page.get('/bugs/'+j.id);
							}, j);
						} else
							console.log('error', String(Notify.tab_id), localStorage.getItem('tab_id'));
					}
				break;
				
				case 'messages':
					var arr = location.pathname.match(/\/im\/?([0-9]+|all)?/), chat = $('#chat_user'+j.sid);
					if(j.uid != _user.id){
						console.log(j);
						if(arr || chat.length){
							if(arr){
								if($('.imDialog[data-id="'+j.sid+'"]').length){
									$('.imDialog')
										.append(Im.compile(j))
										.scrollTop(999999999);
										if(j.sid != 'all')
											Im.viewed(j.uid);
								} else if(String(Notify.tab_id) == localStorage.getItem('tab_id')){
									var imUser = $('.imUser[data-id="'+j.sid+'"]').prependTo('.imDialogs'), imCount = imUser.find('.imCount');
									if(imCount.length){
										imCount.text(Number(imCount.text())+1);
									}else {
										imUser.append($('<span/>', {
											class: 'imCount',
											text: '1'
										}));
									}
								}	
							}
							j.date = j.date.split(' ')[1];
							chat.find('.mini-chat-messages').append(Chat.compileMsg([j])).scrollTop(999999999);
						} else if(String(Notify.tab_id) == localStorage.getItem('tab_id')){
							var mntf = $('#new_msg > a'), sp = mntf.find('.newMsg');
							if(sp.length){
								sp.text(Number(sp.text())+1);
							} else {
								mntf.append($('<span/>', {
									class: 'newMsg',
									text: 1
								}));
							}
							Notify.show(j.message, function(){
								Chat.flag = 0;
								Chat.open({
									sid: j.sid,
									name: j.name+' '+j.lastname
								});
							}, j);
						}	 else
							console.log('error', String(Notify.tab_id), localStorage.getItem('tab_id'));
					}
				break;
				
				case 'del_message':
					$('.imMes[data-msg="' + j.id + '"] .imMesText').html($('<span/>', {
						class: 'imDate',
						text: $('.imMes[data-msg="' + j.id + '"] .imMesText > .imDate').text()
					})).append($('<span/>', {
						class: 'fa fa-trash imDeleted'
					})).append('Message deleted');
				break;
				
				case 'undo_message':
					var msg = $('.imMes[data-msg="'+j.id+'"] .imMesText'), date = msg.find('.imDate').text();
					msg.html('<span class="imDate">'+date+'</span>'+j.msg);
				break;
				
				case 'new_task':
					$('.cash').before('<div id="task_'+j.id+'" class="taskNotif"><div class="flRight"><button class="btn btnComplite" onclick="tasks.complite('+j.id+');">Complite</button></div><p>'+j.note+'</p></div>');
				break;
				
				case 'open_store':
					Notify.show(j.text, '/cash/' + j.id, j);
				break;
				
				case 'new_quote':
					var notf = $('#new_quote .newMsg');
					notf.text(Number(notf.text())+1);
					Notify.show(j.message, '/quote/view/'+j.id, j, false, 999999999);
				break;
				
			}
			Notify.pending(t, p);
		});
	}
};
Notify.perm();
window.status = 'focus';
$(window).bind('focus blur', function(a){
    window.status = a.type;
});