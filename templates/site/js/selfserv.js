$.fn.inptel = function() {
    $(this).find('input[type="tel"]').bind('input keyup', function(a) {
        window.setTimeout(function() {
            var t = a.target,
                v = t.value.replace(/\D/g, ''),
                l = v.length;
            if (a.type == 'keyup') {
                if (a.keyCode == 8 && l > 1 && l < 7) v = v.slice(0, -1);
                else return false;
            }
            t.value = v.replace(/([0-9]{1})?([0-9]{1,3})?([0-9]{1,3})?([0-9]{1,4})?.*/i, function(a, b, c, d, e, f) {
                return (b ? '+' + b : (t.value[0] == '+' ? '+' : '')) + (c ? ' (' + c + ')' : '') + (d ? ' ' + d : '') + (e ? '-' + e : '');
            });
        }, 0);
    });
};
$.fn.ajaxSubmit = function(a){
	$(this).submit(function(e){
		e.preventDefault();
		var form = new FormData(this), uploads = $(this).find('.dragndrop'), jlist = $(this).find('.json-list'), err = false;
		if(uploads.length){
			uploads.each(function(){
				if(this.getAttribute('required') !== null && $.isEmptyObject(this.files)){
					err = true;
					$(this).css('background', 'rgba(245, 97, 97, 0.24)').click(function(){
						$(this).css('background', '').unbind('click');
					});
					return false;
				}
				if(this.files){
					for(var k in this.files){
						form.append(this.name, this.files[k]);
					}
				} 
			});	
		}
		if(jlist.length){
			jlist.each(function(){
				var sval = $(this).data().value,
					rinp = this.hasAttribute('input'),
					sinp = $(this).find('input').val(),
					sname = this.getAttribute('name');
				if(this.hasAttribute('required') && (((sval == 0 || !sval.length) && !rinp) || (rinp && !sinp.length && (sval == 0 || !sval.length)))){
					err = true;
					$(this).css('border', '1px solid #d80808').click(function(){
						$(this).css('border', '').unbind('click');
					});
					this.scrollIntoView(true);
					window.scrollTo(0, window.pageYOffset-100);
					return false;
				}
				if(sval > 0 || sval.length)
					form.append(sname, sval);
				else if(rinp && sinp.length){
					form.append(sname+'-new', sinp);
				}
			});
		}
		if(err) return false;
		if(typeof a == 'object' && typeof a.check == 'function'){
			if(a.check.apply(this) === false)
				return false;
		}
		if(typeof a == 'object'){
			if(typeof a.data == 'function')
				a.data = a.data();
			if(typeof a.data == 'object'){
				for(var n in a.data){
					form.append(n, a.data[n]);
				}	
			}
		}
		$.ajax({
			type: this.method || 'post',
			processData: false,
			contentType: false,
			url: this.action,
			data: form
		}).done(typeof a == 'function' ? a : a.callback);
	});
};
$.fn.json_list = function(a){
	if(!a) a = {};
	this.each(function(){
		if(a == 'disabled'){
			this.enabled();
			return false;
		}
		var _t = $(this),
			_m = _t[0].hasAttribute('multiple'),
			_d = _t.attr('disabled'),
			_c = _t.attr('onchange'),
			_j = _t.attr('json'),
			_r = _t.attr('res'),
			_k = _t.attr('key'),
			_s = _t.attr('search'),
			_l = _j.search(/[\[\]\}\{]/) != -1;
		_t.data('list', a.data || (_l ? JSON.parse('['+_j.replace(/\[(.*)\]/i, '$1')+']') : []));
		_t.data('value', []);

		var d = _t.data().list, s = d.length > 10;
			
		if(!_d){
			_t.removeAttr('json');
			_t.removeAttr('key');
			_t.removeAttr('search');
			_t.removeAttr('multiple');
		}
		
		_t.addClass('json-list').html('<div class="wrap" tabindex="-1"><input type="search" placeholder="Waiting..." '+(_d ? 'disabled' : '')+'></div><ul></ul>');
		
		var ul = _t.find('ul'), wrap = _t.find('.wrap'), inp = wrap.find('input');
		
		function change(){
			var _i = this, data = $(_i).data();
			if(_m){
				_t.data().value.push(data[_k || 'id']);
				$('<p>'+data.name+'<i></i></p>').prependTo(wrap).click(function(){
					$(this).remove();
					_t.data().value = _t.data().value.filter(function(e){
					   return e != data[_k || 'id']; 
					});
					$(_i).show();
				});
				$(_i).hide();
			} else {
				_t.data('value', data[_k || 'id']);
				inp.val(data.name);
				ul.hide();
				_t.removeClass('open');	
			}
			if(_c) window[_c].apply(_i, [data[_k || 'id']]);
		}
		
		function input(a){
			x = _s ? _s.split('ajax') : [];
			s = x[1] || x[0];
			if(s >= 0 && a.length >= s){
				var q = false;
				inp.removeAttr('readonly').attr('placeholder', 'Search...').bind('input', function(e){
					var val = this.value;
					if(!_m)
						_t.data().value = [];
					if(x[1]){
						if(!q){
							q = true;
							$.getJSON(_j+(_j.indexOf('?') >= 0 ? '&' : '?')+'q='+val, function(r){
								d = _r ? r[_r] : r.responce;
								add(d);
								q = false;
							});	
						}
					} else {
						ul.find('li').each(function(){
							var li = $(this);
							if(String(li.data().name).toLowerCase().indexOf(val.toLowerCase()) != -1){
								li.show();
							} else {
								li.hide();
							}
						});	
					}
					ul.show();
					_t.addClass('open');
				});
			} else {
				inp.attr('readonly', true);
				inp.attr('placeholder', 'Not selected');
			}
		}

		function add(a){
			
			ul.empty();
			
			for(var k in a){
				$('<li role="option">\
					'+(a[k].image ? '<img src="'+a[k].image+'" align="left">' : '')+''+a[k].name+''+(
						a[k].descr ? '<p>'+a[k].descr+'</p>' : ''
					)+'\
				</li>').data(a[k]).mouseover(function(){
					ul.find('li').removeClass('active');
					$(this).addClass('active');
				}).appendTo(ul);
			}
			
			ul.find('li').mousedown(change);
		}
		
		if(!d.length && !_l && _j.length){
			$.getJSON(_j, function(r){
				d = _r ? r[_r] : r.responce;
				input(d);
				add(d);
			});
		} else {
			input(d);
			add(d);
		}
		
		wrap.mousedown(function(e){
			if(_t.attr('disabled'))
				return false;
			if(e.target.tagName == 'INPUT' && !e.target.hasAttribute('readonly')){
				ul.show();
				_t.addClass('open');
			} else if(e.target.tagName != 'P' && e.target.tagName != 'I'){
				if(!_m && !inp.val().length){
					_t.data().value = [];
				}
				ul.toggle();
				_t.toggleClass('open');
			}
		}).keydown(function(e){
			var li = ul.find('li.active');
			switch(e.keyCode){
				case 38:
				case 40:
					ul.show();
					_t.addClass('open');
					ul.find('li').removeClass('active');
					if(e.keyCode == 38){
						if(li.length && li.index())
							li = li.prev();
						li[0].scrollIntoView(false);
					} else if(e.keyCode == 40){
						if(li.length && li.next().length)
							li = li.next();
						else if(!li.length)
							li = ul.find('li:first');
						li[0].scrollIntoView(false);
					}
					li.addClass('active');
				break;
				
				case 13:
					change.apply(li[0]);
				break;
			}
		});
		$(window).mousedown(function(e){
			if(!$(e.target).parents('.json-list').length){
				ul.hide();
				if(!_m && !inp.val().length){
					_t.data().value = [];
				}
				_t.removeClass('open');
			}
		});
		_t[0].load = function(a){
			$(this).data().value = [];
			if(typeof a == 'string'){
				_j = a;
				$.getJSON(a, function(r){
					d = _r ? r[_r] : r.responce;
					input(d);
					add(d);
				});
			} else {
				input(a);
				add(a);
			}
			inp.val('');
			this.enabled();
		};
		_t[0].enabled = function(){
			$(this).removeAttr('disabled').find('input').removeAttr('disabled');
		};
		_t[0].disabled = function(){
			$(this).attr('disabled', true).find('input').attr('disabled', true);
		};
	});
};