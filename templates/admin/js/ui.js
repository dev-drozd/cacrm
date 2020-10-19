/**
 * @appointment UI Elements
 * @author      Victoria Shovkovych, Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
function addBarcode(t){
	clearTimeout(barTimer);
	var e = t.value.match(/(DE)\s([0-9]+)[.*]?/i), i, l;
	if(e){
		barTimer = setTimeout(function(){
			i = parseInt(e[2]);
			l = $('.searchItems > [data-id='+i+']');
			return l ? l.click() : false;
		}, 300);	
	}
	return barTimer;
}

var Phones = {
	st: 0,
	format: function(a){
		var t = a.target, v = t.value.replace(/\D/g, ''), l = v.length;
		if(t.value.length > 0)
			$('.phones').find('> .fa-times').removeClass('fa-times').addClass('fa-plus');
		else
			$('.phones').find('> .fa-plus').removeClass('fa-plus').addClass('fa-times');
		if(a.type == 'keyup'){
			if(a.keyCode == 8 && l > 0 && l < 7) v = v.slice(0, -1);
			else return false;
		}
		t.value = v.replace(/([0-9]{1,3})?([0-9]{1,3})?([0-9]{1,4})?.*/i, function(a,b,c,d){
			return (b ? ' ('+b+')' : '')+(c ? ' '+c : '')+(d ? '-'+d : '');
		});
	},
	add: function(a){
		var p = $(a).find('.phone:last'), inp = p.find('input[type=tel]');
		if(!inp.val().length){
			this.err(inp);
			return false;
		}
		var div = $('<div/>', {
			class: 'phone',
			'data-code': p.attr('data-code'),
			html: '<input type="radio" name="sms" value="'+$(a).find('> .phone').length+'">'
		});
		
		p.after(div);
		inp.clone().val('').prependTo(div).focus();
		$('.phones').find('> .fa-plus').removeClass('fa-plus').addClass('fa-times');
		$(a).radio();
		//this.st = 0;
	},
	blur: function(a){
		//console.log(document.event.target);
		if($(a).parents('.phones').find('.phone').length > 1 && !a.value.length){
			$(a).parent().remove();
			$('.phones').find('> .fa-times').removeClass('fa-times').addClass('fa-plus');
		}
	},
	err: function(a){
		//alert('ERR');
		a.css('background', '#ff696924').focus();
		setTimeout(function(){
			a.css('background', '');
		}, 300);
	}
};
 
(function($) {
	// ColorPicker
	$.fn.colorPicker = function(o) {
        var values = ['#fffec9', '#f7f6b9', '#ffe7e7', '#f9cdcd', '#e4ffd9', '#d4efca', '#ffe1ce', '#ffd3b6', '#e8dbf1', '#dbcbe6', '#e9f5f9', '#d6edf5', '#d7e6f5', '#c3daf1', '#d7f5ea', '#c2efde'];
        $('.colorPicker').each(function(i, v) {
            $(v).attr('type', 'hidden').after($('<div/>', {
                class: 'color-picker'
            }));

            values.forEach(function(item, j) {
                $(v).next().append($('<span/>', {
                    'data-value': item,
                    'style': 'background: ' + item
                }).click(function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        $(v).val('');
                    } else {
                        $(this).parent().find('.active').removeClass('active');
                        $(this).addClass('active');
                        $(v).val($(this).attr('data-value'));
                    }
                }));
            });

            if ($(v).val())
                $(v).next().find('[data-value="' + $(v).val() + '"]').addClass('active');
        });
    };
	
	// Emoji
	$.fn.emoji = function(){
/* 		return this.each(function(){
			this.innerHTML = this.innerHTML.replace(new RegExp([
			  '\ud83c[\udf00-\udfff]',
			  '\ud83d[\udc00-\ude4f]',
			  '\ud83d[\ude80-\udeff]'
			].join('|'), 'g'), function(){
				console.log(arguments);
				return '<span class="emoji" data-emoji="$&"></span>';
			});
		}); */
	};
	
	// Transfer form
	$.fn.transfer = function(o) {
		//return false;
		var e = $(this), 
			opt = {
				action: '',
				data: {}
			};
		
		opt = $.extend(o);
		
		$(this).each(function(i, e) {
			$(e).html($('<input/>', {
				type: 'hidden',
				name: 'trIds'
			})).append($('<div/>', {
				class: 'trW50 stFrom',
				html: $('<input/>', {
					type: 'text',
					name: 'trSearch',
					oninput: 'addBarcode(this);',
					placeholder: 'Start type...'
				}).keyup(function() {
					var e = this.value.match(/(DE)\s([0-9]+)[.*]?/i);
					/* try {
						if(e[2])
							return;
					} catch(e){
						return;
					} */
					opt.data.query = e ? e[2] : $(this).val();
                    opt.data.nIds = $('input[name="trIds"]').val();
					post();
				})
			}).append($('<div/>', {
				class: 'searchItems'
			}))).append($('<div/>', {
				class: 'trW50 stTo'
			}))
		});
		
		post();
		
		function post() {
			$('.searchItems').html('');
			if (opt.action) {
				$.post(opt.action, opt.data, function(r) {
					if (r.list) {
						$.each(r.list, function(ind, val) {
							$('.searchItems').append($('<div/>', {
								class: 'trItem',
								'data-id': val.id,
								'data-quantity': val.quantity,
								html: val.name + ' [Q-ty: ' + val.quantity + ']'
							}).click(function() {
								if (event.target.tagName != 'INPUT')
									click(this, event);
							}).append($('<span/>', {
								class: 'trPrice',
								html: '$ ' + val.price
							})));
						});
					}
				}, 'json');
			}
		}
		
		function click(el) {
			$(el).prepend($('<input/>', {
				name: 'quantity',
				min: 1,
				max: $(el).attr('data-quantity'),
				value: $(el).attr('data-quantity'),
				type: 'number'
			}).change(function() {
				change(this, event);
			}));
			$(el).unbind('click').click(function() {
				console.log(event.target.tagName);
				if (event.target.tagName != 'INPUT')
					clickTo(this, event);
			}).appendTo(e.find('.stTo'));
			if ($('input[name="trIds"]').val().indexOf($(el).attr('data-id')) < 0)
				$('input[name="trIds"]').val($('input[name="trIds"]').val() + ($('input[name="trIds"]').val() ? ',' : '') + $(el).attr('data-id') + ':' + $(el).attr('data-quantity'));
		}
		
		function clickTo(el) {
			$(el).unbind('click').click(function() {
				if (event.target.tagName != 'INPUT')
					click(this, event);
			}).appendTo(e.find('.searchItems'));
			var value = $(el).attr('data-id') + ':' + $(el).find('input').val() + ($('input[name="trIds"]').val().indexOf($(el).attr('data-id') + ':' + $(el).find('input').val() + ',') >= 0 ? ',' : '');
			$('input[name="trIds"]').val($('input[name="trIds"]').val().replace(value, ''));
			$(el).find('input').remove();
		}
		
		function change(el) {
			var value = $(el).val();
			if (value > parseInt($(el).attr('max')))
				value = $(el).attr('max');
			
			if ($(el).val() < parseInt($(el).attr('min')))
				value = $(el).attr('min');
			
			$(el).val(value);
			var re = new RegExp($(el).parent().attr('data-id') + ':([0-9]+),?', 'g'),
				rec = new RegExp($(el).parent().attr('data-id') + ':([0-9]+),', 'g');
			$('input[name="trIds"]').val($('input[name="trIds"]').val().replace(re, $(el).parent().attr('data-id') + ':' + value + ($('input[name="trIds"]').val().match(rec) ? ',' : '')));
		}
	};

    // Uploader
    $.fn.upload = function(p) {

        var opts = typeof p == 'string' ? p : $.extend({
            autoUpload: false,
            count: 0,
            multiple: false,
            accept: 'image',
			types: ['jpeg', 'jpg', 'png', 'gif'],
            size: 10,
            max: 1,
            check: function() {}
        }, p);

        $(this).each(function(i, e) {
            var self = this;
            self.files = {};
            self.count = opts.count;
            self.delete = [];
            self.input = $('<input/>', {
                type: 'file',
                accept: opts.accept,
                multiple: opts.multiple
            }).change(function(e) {
                self.check(e.target);
            });
            $(window).bind('dragenter dragover', function(e) {
                e.preventDefault();

            }).bind('dragleave', function(e) {

            }).bind('drop', function(e) {
                e = e.originalEvent;
                e.preventDefault();
                e.stopPropagation();
                if (e.dataTransfer.files.length)
                    self.check(e.dataTransfer);
            });
            $(self).click(function() {
                self.input.click();
            });
            self.check = function(e) {
                var count = Object.keys(self.files).length,
                    files = e.files;
                if ((count + self.count + files.length) > opts.max) {
                    opts.check.apply(self, [{
                        error: 'max',
                        files: files
                    }]);
                    return;
                }
                $.each(files, function(i) {
					var t = this.name.split('.');
                    if ($.inArray(this.type.split('/')[1], opts.types) == -1 && $.inArray(t[t.length - 1], opts.types) == -1) {
                        opts.check.apply(self, [{
                            error: 'type',
                            file: this
                        }]);
                        return;
                    }
                    if (this.size > (1024 * opts.size * 1024)) {
                        opts.check.apply(self, [{
                            error: 'size',
                            file: this
                        }]);
                        return;
                    }
                    if (opts.check.apply(self, [{
                            file: this,
                        }]) === false) return false;
                    self.files[this.name] = this;
                });
				self.input.val('');
                self.upload();
            };
            self.upload = function() {
                if (opts.auto) {

                }
            };
        });

        return this;
    };

    // Checkbox
    $.fn.checkbox = function(n) {
		//return false;
        $('input[type="checkbox"]').each(function(i, e) {
            if (!$(e).next().hasClass('cbWrap')) {
                $(e).hide().val($(e).is(':checked') ? 1 : 0).after($('<div/>', {
                    class: 'cbWrap',
                    html: $('<span/>'),
                }).click(function(ev) {
                    $(e).val($(e).is(':checked') ? 0 : 1).prop('checked', !$(e).prop('checked'));
                    if (e.onchange)
                        e.onchange();
                }));
            }
        });
    };

    // Radio
    $.fn.radio = function(n) {
		//return false;
        $('input[type="radio"]:not([hidden])').each(function(i, e) {
            if (!$(e).parent().hasClass('rbWrap')) {
                $(e).wrap($('<div/>', {
                    class: 'rbWrap'
                })).parent().append($('<label/>', {
                    class: 'rdLbl',
                    html: $(e).attr('data-label')
                }).click(function(ev) {
                    $(this).parent().find('input').trigger('click');
                    if (e.onchange)
                        e.onchange();
                }));
            }
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
							form.append(this.getAttribute('name') || 'file', this.files[k], this.name);
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
				_q = _t.attr('required'),
				_c = _t.attr('onchange'),
				_j = _t.attr('json'),
				_r = _t.attr('res'),
				_k = _t.attr('key'),
				_s = _t.attr('search'),
				_l = _j.search(/[\[\]\}\{]/) != -1;
			_t.data('list', a.data || (_l ? JSON.parse('['+_j.replace(/\[(.*)\]/i, '$1')+']') : []));
			if(!_t.data('value')) _t.data('value', []);

			var d = _t.data().list, s = d.length > 10;
				
			if(!_d){
				_t.removeAttr('json');
				_t.removeAttr('key');
				_t.removeAttr('search');
				_t.removeAttr('multiple');
			}
			
			_t.addClass('json-list').html('<div class="wrap" tabindex="-1"><input type="search" placeholder="Waiting..."'+(_d ? ' disabled' : '')+''+(_q ? ' required' : '')+'></div><ul></ul>');
			
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
								_t.addClass('pending');
								$.getJSON(_j+(_j.indexOf('?') >= 0 ? '&' : '?')+'q='+val, function(r){
									d = _r ? r[_r] : r.responce;
									add(d);
									q = false;
									_t.removeClass('pending');
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

			function add(a,b){
				
				ul.empty();
				
				for(var k in a){
					if(b && a[k].id == b) inp.val(a[k].name);
					var l = $('<li role="option">\
						'+(a[k].image ? '<img src="'+a[k].image+'" align="left">' : '')+''+a[k].name+(a[k].price ? '<span>$'+a[k].price+'</span>' : '')+(
							a[k].descr ? '<p>'+a[k].descr+'</p>' : ''
						)+'\
					</li>').data(a[k]).mouseover(function(){
						ul.find('li').removeClass('active');
						$(this).addClass('active');
					}).appendTo(ul);
					if(a[k].selected > 0){
						change.apply(l[0]);
						//console.log(l[0]);
					}
				}
				
				ul.find('li').mousedown(change);
			}
			
			if(!d.length && !_l && _j.length){
				var _v = _t.data().value;
				if(_v && typeof _v == 'string') _j = _j+'?s='+_v;
				_t.addClass('pending');
				$.getJSON(_j, function(r){
					d = _r ? r[_r] : r.responce;
					input(d);
					add(d,_v);
					_t.removeClass('pending');
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
					_t.addClass('pending');
					$.getJSON(a, function(r){
						d = _r ? r[_r] : r.responce;
						input(d);
						add(d);
						_t.removeClass('pending');
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

    // Select 
    $.fn.select = function(n) {
        $('select:not([hidden])').each(function(i, e) {
            if (!$(e).next().hasClass('sWrap')) {
                if ($(e).attr('required') && !$(e).val().length) {
                    $(e).find('option').first().attr('selected', true);
                }
                $(e).hide(0).after($('<div/>', {
                    class: 'sWrap cl',
					          tabindex: '-1',
                    html: $('<ul/>', {
						        class: 'cl'
					      })
                }).blur(function() {
                  if (!$(e).attr('multiple'))
                    $(e).next().children('ul').hide().parent().parent().removeClass('act').parents('.iGroup').removeClass('act');
                }));
              if (!$(e).attr('multiple'))
                $(e).next().prepend($('<div/>', {
                class: 'cl'
              }).click(function() {
                    $(e).next().children('ul').toggle().parent().parent().addClass('act').parents('.iGroup').addClass('act');
              }));
                    
              var items = $(e).next().find('ul');
              if ($(e).find('optgroup').length) {
                $(e).find('optgroup').each(function (i, v) {
                  items.append($('<ul/>', {
                    class: 'sel',
                    html: $('<li/>', {
                      class: 'optLbl',
                      html: $(v).attr('label')
                    })
                  }))
                   $(v).find('option').each(function () {
                      optionLi($(this), items.find('.sel'), e);
                  });
                   items.find('.sel').removeClass('sel');
                });
              } else {
                $(e).find('option').each(function () {
                  optionLi($(this), items, e);
                });
              }  
                    
              $(e).change(function(event) {
                  if (!$(e).attr('multiple'))
                      $(e).next().find('div').html($(e).next().find('li[data-value="' + $(e).val() + '"]').text());
                  else {
                      $(e).find('option').each(function() {
                          $(this).prop('selected') ? items.find('li[data-value="' + $(this).val() + '"]').addClass('active') : items.find('li[data-value="' + $(this).val() + '"]').removeClass('active');
                      });
                  }
              });
          }
          
          function optionLi(el, s, e) {
            s.append($('<li/>', {
                'class': el.prop('selected') ? 'active cl' : 'cl',
                'data-value': el.val(),
                'html': el.text()
            }).click(function() {
                if ($(e).attr('required') && $(e).val() == $(this).attr('data-value')) {
                    alr.show({
                        class: 'alrDanger',
                        content: 'The field must have a value',
                        delay: 1.5
                    });
                } else {
                    if (!$(e).attr('multiple')) {
                        $(e).next().find('div').html($(this).html()).next().toggle().parent().parent().removeClass('act');
                        $(e).next().find('li').removeClass('active');
                    }
                    $(this).toggleClass('active');
                    $(e).find('option[value="' + $(this).attr('data-value') + '"]')
                        .prop('selected', !$(e).find('option[value="' + $(this).attr('data-value') + '"]').prop('selected')).trigger('change');
                }
            }));
            if (!$(e).attr('multiple') && el.prop('selected')) $(e).next().find('div').html(el.text());
        }
      });
      
      
    };

    // Search form
    $.fn.sForm = function(o, inp, cb, d) {
		//return false;
        return this.each(function() {
			$('html').off('click');
            var e = $(this),
                opt = {
                    s: false,
                    all: false,
                    data: {
                        lId: 0
                    },
                    select: {},
                    nIds: [],
					link: false
                },
                pending = false,
                ce = undefined;
            if (typeof o == 'object' || !o) {
                $(this).removeClass('hdn').wrap($('<span/>'));
                var dSelect = {};
                $.extend(true, opt, o);
				$(this).find('li').addClass('nc');
                $(this).addClass('nc').parent().wrap($('<div/>', {
                    class: 'sfWrap nc',
					style: 'z-index: 1'
                })).parent().prepend($('<div/>', {
					class: 'nc',
                    html: $('<input/>', {
                        type: 'text',
						class: 'nc',
                        placeholder: 'Search...'
                    })
                }).click(function(e) {
                    showList(this);
                }).keyup(function() {
					showList(this);
				})).find('li').click(function() {
                    click($(this));
                });
                if (e.parents('.mdl')) {
                    p = ($(window).height() - e.parents('.mdl').height()) / 2;
                    e.parents('.mdlWrap').css('padding-top', (p < 70 ? 70 : p) + 'px');
                }
                init();
            } else if (typeof o != 'object' && o == 'get') {
                inp.removeData().data(opt.select);
            }
            e.parent().on('scroll', function() {
                if (($(this).scrollTop() >= (e.find('ul').height() - e.height() - 20)) && !opt.all) {
                    post();
                }
            }).parents('.sfWrap').find('input').keyup(function() {
                if (opt.all) {
                    e.parent().find('li').show();
                    $.each(e.find('li'), function() {
                        if (e.parent().parent().find('input').val() && $(this).text().toLowerCase().indexOf(e.parent().parent().find('input').val().toLowerCase()) < 0)
                            $(this).hide();
                    });
                } else {
                    e.parent().find('ul').empty();
					var query = e.parent().parent().find('input').val(),
						line = query.match(/DE\s([0-9]+)[.*]?/i);
					if (query.indexOf('DE') >= 0 && line && line.length == 2)
						query = parseInt(line[1]);
                    opt.data.query = query;
                    post();
                }
            });

            function showList(el, i) {
                ce = el;
				$('.sfWrap').css('z-index', '1')
                $(el).parent().css('z-index', '99999');
				$(el).next().show();
				$('html').on('click', function(event) {
					if (!$(event.target).hasClass('nc')) {
						$(el).parent().css('z-index', '1');
						$(el).next().hide();
						$(el).off('click').on('click', function() {
							showList(el);
						});
						$('html').off('click');
					}
				})
                $(el).off('click').on('click', function(e) {
                    if (!$(e.target).attr('id')) {
						$(el).parent().css('z-index', '1');
                        $(el).next().hide();
                        $(el).off('click').on('click', function() {
                            showList(el);
							$('html').off('click');
                        });
                    }
                })
            }

            function init() {
                if (opt.select) {
                    $.each(opt.select, function(k, li) {
                        click(li, k);
                    })
                }
            }

            function post() {
                if (!pending) {
                    pending = true;
                    opt.data.lId = e.find('li:last').attr('data-value');
					var data = opt.data;
					if (d) {
						$.each(d, function(i, v) {
							data[i] = Object.keys($('input[name="' + v + '"]').data() || []).join(',');
						});
					}
					$.post(opt.action, data, function(res) {
						if (res) {
							$.each(res.list, function(i, li) {
								e.parent().find('ul').append($('<li/>', {
									'class': 'nc',
									'data-value': li.id,
									'data-price': (li.currency ? currency_val[li.currency].symbol : '') + li.price,
									'data-cost-price': (li.currency ? currency_val[li.currency].symbol : '') + li.cost_price,
									'data-tax': li.tax,
									'data-object': li.object,
									'data-objectid': li.object_id,
									'data-count': li.count,
									'data-catname': li.catname,
									'data-req': li.req,
									'data-purchase': li.purchase,
									'data-quantity': li.quantity,
									'data-currency': li.currency || '',
									'html': li.name // + (li.lastname ? ' ' + li.lastname : '')
								}).click(function() {
									click($(this));
								}));
							});
							pending = false;
							//if (res.count <= 5) opt.all = true;
						}
					}, 'json');
                }
            }

            function click(el, nel) {
				e.parent().parent().find('input').val('');
				console.log(el, nel);
                var id = nel ? nel : el.attr('data-value'),
                    name = nel ? el.name : el.text(),
                    lastname = nel ? el.lastname : el.text().split(' ')[1],
                    price = nel ? el.price : el.attr('data-price'),
					cost_price = nel ? el.cost_price : el.attr('data-cost-price');
                catname = nel ? el.catname : el.attr('data-catname');
                tax = nel ? el.tax : el.attr('data-tax');
                count = nel ? el.count : el.attr('data-count');
                object = nel ? el.object : el.attr('data-object');
                objectid = nel ? el.objectid : el.attr('data-objectid');
                photo = nel ? el.photo : el.attr('data-image');
                req = nel ? el.req : el.attr('data-req');
                purchase = nel ? el.purchase : el.attr('data-purchase');
                quantity = nel ? el.quantity : el.attr('data-quantity');
                currency = nel ? el.currency : el.attr('data-currency');
                if (opt.s) {
                    e.parent().parent().find('div > span > i').each(function(i, sp) {
                        clickLi($(sp).parent(), id, 1);
                    });
                }
                e.parent().parent().find('input').before($('<span/>', {
                    id: id,
					'class': 'nc',
                    'data-image': photo,
                    'data-price': price,
                    'data-cost-price': cost_price,
                    'data-tax': tax,
                    'data-object': object,
                    'data-count': count,
                    'data-objectid': objectid,
                    'data-catname': catname,
                    'data-req': req,
                    'data-purchase': purchase,
                    'data-currency': currency,
                    'data-quantity': quantity,
                    html: (opt.link && parseInt(id) > 0) ? '<a href="/' + opt.link + '/' + parseInt(id) + '" target="_blank">' + name + '</a>' : name
                }).append($('<i/>').click(function(ev) {
                    clickLi($(this).parent(), id);
                })));
                if (e.parent().parent().find('li[data-value="' + id + '"]')) e.parent().find('li[data-value="' + id + '"]').remove();
                if (opt.s) {
                    dSelect = {};
                    dSelect[id] = {
                        name: name,
                        lastname: lastname,
                        photo: photo,
                        price: price,
						cost_price: cost_price,
                        count: count,
                        catname: catname,
                        tax: tax,
                        object: object,
                        objectid: objectid,
                        purchase: purchase,
                        quantity: quantity,
                        currency: currency,
                        req: req
                    };
                    inp.removeData().data(dSelect);
                    opt.nIds = [id];
                } else {
                    dSelect = inp.data();
                    dSelect[id] = {
                        name: name,
                        lastname: lastname,
                        price: price,
                        cost_price: cost_price,
                        catname: catname,
                        tax: tax,
                        count: count,
                        photo: photo,
                        object: object,
                        objectid: objectid,
                        purchase: purchase,
                        quantity: quantity,
                        currency: currency,
                        req: req
                    };
                    inp.data(dSelect);
                    opt.nIds.push(id);
                }
				e.parent().hide();
				$(ce).off('click').on('click', function() {
					showList(ce);
				});
                if (cb) cb();
            }

            function clickLi(el, id, s) {
                e.parent().find('ul').append($('<li/>', {
                    'data-value': el.attr('id'),
                    'data-price': el.attr('data-price'),
                    'data-cost-price': el.attr('data-cost-price'),
                    'data-catname': el.attr('data-catname'),
                    'data-image': el.attr('data-image'),
                    'data-tax': el.attr('data-tax'),
                    'data-object': el.attr('data-object'),
                    'data-count': el.attr('data-count'),
                    'data-objectid': el.attr('data-objectid'),
                    'data-req': el.attr('data-req'),
                    'data-purchase': el.attr('data-purchase'),
                    'data-quantity': el.attr('data-quantity'),
                    'data-currency': el.attr('data-currency'),
                    'html': (opt.link && parseInt(el.attr('id')) > 0) ? el.find('a').html() : el.html()
                }).click(function() {
                    click($(this));
                }));
                el.remove();
                dSelect = inp.data();
                delete dSelect[id];
                inp.data(dSelect);
                if (opt.s) inp.removeData()
                delete opt.select[id];
                opt.nIds.splice(opt.nIds.indexOf(id), 1);
				if (!s) console.log('close');
				if (e.parent().parent().find('span') && cb && !s) cb();
            }
        });
    };

    // Tab
    $.fn.tabs = function(n) {
		//return false;
        $('.tabs').each(function(i, e) {
            var e = $(this);
            if (!e.find('.tUl').length) {
                e.prepend($('<ul/>', {
                    class: 'tUl'
                })).children('.tab:first').css('display', 'block').parent().children('.tab').each(function(i, v) {
                    e.find('.tUl').append($('<li/>', {
                        'data-value': $(this).attr('id'),
                        'html': $(this).attr('data-title'),
                        'class': !i ? 'active' : ''
                    }).append($(this).attr('data-counter') ? $('<span/>', {
						class: 'tCounter',
						html: $(this).attr('data-counter')
					}).click(function() {
						$(this).parent().click();
					}) : '').click(function() {
                        click(this, e);
                    }));
                });
				
				//width(e);
				
				$( window ).on('resize', function() {
					//width(e);
				});
            }
        });
		
		function click(el, e) {
			$(el).parents('.tabs').find('.active').removeClass('active');
			$(el).addClass('active');
			e.children('.tab').hide(0, function() {
				$('#' + $(el).attr('data-value')).show(0);
			});
			if ($(e).find('.tabsAdd').length)
				$(e).find('.tabsAdd > ul').slideUp();
		}
		
		function width(e) {
			var w = 0,
				tw = $(e).find('.tUl').width() - 110,
				nt = '',
				f = 0;
			e.find('.tUl > li').each(function(i, v) {
				w += $(v).width() + 40;
				if (w > tw || f) {
					if (!f) {
						$(e).find('.tUl').append($('<li/>', {
							class: 'tabsAdd',
							html: $('<span/>', {
								class: 'fa fa-ellipsis-v'
							}).click(function() {
								$(this).parent().find('ul').toggle();
							}),
						}).append($('<ul/>', {
						})));
					}
					f = 1;
					$(e).find('.tabsAdd > ul').append($('<li/>', {
						'data-value': $(v).attr('data-value'),
						'html': $(v).html()
					}).click(function() {
						click(this, e);
					}))
					$(v).remove();
				}
			});
		}
    };
	
	// Calendar
	$.fn.calendar = function (cb) {
		//return false;
        var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            week = ['Sun', 'Mon', 'Tue', 'Wen', 'Thu', 'Fri', 'Sat'],
            today = new Date();
        return this.each(function () {
            var e = this, c = 0;
            $(e).addClass('calendar cl').append($('<input/>', {
                type: 'hidden',
                name: 'date'
            }));
            if ($(e).attr('data-multiple')) {
                $(e).append($('<input/>', {
                    type: 'hidden',
                    name: 'fDate'
                }));
            }

            // Header / Content
            $(e).append($('<div/>', {
                class: 'calHeader cl',
                html: $('<span/>', {
                    class: 'calArrow cl',
                    html: '&lsaquo;'
                }).click(function () { 
                    selMonth('prev');
                })
            }).append($('<span/>', {
                class: 'calCur cl',
                html: $('<span/>', {
                    class: 'calMonth cl',
                    html: $('<i/>', {
						class: 'cl',
                        html: month[today.getMonth()]
                    }),
                    onclick: '$(this).toggleClass(\'active\');'
                }).append($('<span/>'))
            }).append($('<span/>', {
                class: 'calYear cl',
                html: today.getFullYear()
            }).on('click', function () { 
                yearClick(this);
            }))).append($('<span/>', {
                class: 'calArrow cl',
                html: '&rsaquo;'
            }).click(function () { 
                selMonth('next');
            }))).append($('<div/>', {
                class: 'calContent cl',
                html: $('<div/>', {
                    class: 'calWeek cl'
                })
            }).append($('<div/>', {
                class: 'calDays cl'
            })));
            
            // Months
            $.each(month, function (j, m) {
                $(e).find('.calMonth > span').append($('<span/>', {
                    'data-value': j,
					class: 'cl',
                    html: m
                }).click(function () { 
                    days($(e).find('.calYear').text(), parseInt($(this).attr('data-value')) + 1);
                }))
            });
            $(e).find('.calMonth').find('span[data-value="' + today.getMonth() + '"]').addClass('active');

            // Day of week
            $.each(week, function (j, d) {
                $(e).find('.calWeek').append($('<span/>', {
					class: 'cl',
                    value: j,
                    html: d
                }))
            });
			
			if ($(e).attr('data-year') && $(e).attr('data-month'))
				days($(e).attr('data-year'), $(e).attr('data-month'), $(e).attr('data-day'));
			else
				days(today.getFullYear(), today.getMonth() + 1);
           
            // Days
            function days(y, m, dd) {
                $(e).find('.calDays').html($('<span/>', {
                    style: 'width: ' + (new Date(month[m - 1] + ' 1, ' + y).getDay() * 14.286) + '%'
                }));
                for (var d = 1; d <= new Date(y, m, 0).getDate(); d++) {
                    $(e).find('.calDays').append($('<span/>', {
                        class: 'calDay cl' + (dd == d ? ' active' : ''),
						month: m > 10 ? '0' + m : m,
                        html: d
                    }).on('click', function () {
                        if (c == 1) {
							var val = $(e).find('input[name="date"]').val().split('-');
							if (parseInt($('.calYear').text() + '' + (
									$(this).attr('month') < 10 ? '0' + $(this).attr('month') : $(this).attr('month')
								) + '' + ($(this).text().length < 2 ? '0' + $(this).text() : $(this).text())) >= 
							parseInt(val[0] + val[1] + val[2])) {
								$(this).addClass('active');
								var month = parseInt($(e).find('.calMonth > span').find('.active').attr('data-value')) + 1,
									day = parseInt($(this).text());
								$(e).find('input[name="fDate"]').val(
									parseInt($(e).find('.calYear').text()) + '-' + (month < 10 ? '0' + month : month)
									+ '-' + (day < 10 ? '0' + day : day)
								);
								c = 0;
								if (cb) cb();
							}
                        } else {
							$(e).find('.calDays > .active').removeClass('active');
                            $(this).addClass('active');
							var month = parseInt($(e).find('.calMonth > span').find('.active').attr('data-value')) + 1,
								day = parseInt($(this).text());
                            $(e).find('input[name="date"]').val(
                                parseInt($(e).find('.calYear').text()) + '-' + (m < 10 ? '0' + m : m)
                                + '-' + (day < 10 ? '0' + day : day)
                            );
                            if ($(e).attr('data-multiple'))                                
								c = 1;
                            else 
								if (cb) cb();
                        }
                            
                    }));
                }
                $(e).find('.calMonth > i').html(month[m - 1]);
                $(e).find('.calYear').html(y);
                $(e).find('.calMonth > span > .active').removeClass('active').parent().find('span[data-value="' + (m - 1) + '"]').addClass('active');
            }

            // Month nav 
            function selMonth(s) {
                m = parseInt($(e).find('.calMonth > span').find('.active').attr('data-value')) + (s == 'next' ? 2 : 0);
                y = parseInt($(e).find('.calYear').text());
                if (m > 12) {
                    m = 1;
                    y++;
                } else if (m < 1) {
                    m = 12;
                    y--;
                }
                days(y, m);
            }

            // Year nav
            function yearClick(el) {
                $(el).off('click');
                var y = $(el).text();
                $(el).html($('<input/>', {
                    type: 'number',
					class: 'cl',
                    name: 'year',
                    step: 1,
                    value: y
                }).blur(function() {
					 y = $(e).find('.calYear > input').val();
					days(y, parseInt($(e).find('.calMonth .active').attr('data-value')) + 1);
					yearBind(el, y)
				}));
                $(document).on('keydown', function (event) {
                    if (event.keyCode == 13) {
                        y = $(e).find('.calYear > input').val();
                        days(y, parseInt($(e).find('.calMonth .active').attr('data-value')) + 1);
                        yearBind(el, y)
                    } else if (event.keyCode == 27) {
                        yearBind(el)
                    }
                    return event;
                });
            }  
            
            // Year bind
            function yearBind(el, y) {
                $(e).find('.calYear').html(y);
                $(document).off('keydown');
                $(el).on('click', function () { 
                    yearClick(el);
                });
            }
        });
    };
	
	$.fn.hntJS = function(o) {
		var opt = {};
		$.extend(opt, o);
        $('.hntJS').hover(function() {
            if ($('.hntJS-hint').length && !$(this).hasClass('hovered'))
                $('.hntJS-hint').remove();

            $('.hntJS').removeClass('hovered');
            $(this).addClass('hovered');

            $('body').append($('<div/>', {
                class: 'hntJS-hint',
                html: $(this).attr('data-title'),
                style: 'top: ' + $(this).offset().top + 'px; left: ' + (opt.left || ($(this).offset().left + $(this).width()/2)) + 'px; '
            }));
        }).mouseout(function() {
            $('.hntJS-hint').remove();
            $(this).removeClass('hovered');
        });

        $(window).scroll(function() {
            $('.hntJS').removeClass('hovered');
            $('hntJS-hint').remove();
        });
    };
})(jQuery);

// Alerts	
var alr = {
    show: function(o) {
        if (o.form) {
            $(o.form).css('background', 'rgba(255, 0, 0, 0.18)').mousedown(function() {
                $(this).css('background', '').unbind('mousedown');
            })
        }
        if (!$('.alr').length) {
			let _a = new Audio();
			_a.src = '/templates/admin/sound/'+(
				o.class == 'alrSuccess' ? 'successfully' : 'error'
			)+'.mp3';
            $('<div/>', {
                class: 'alr ' + (o.class || 'alrInfo'),
                style: o.color ? 'background: ' + o.color : '',
                html: o.content
            }).append($('<span/>', {
                class: 'alrClose fa fa-times',
                onclick: 'alr.close()'
            })).appendTo('body');
			_a.play();
            if (o.delay) {
                setTimeout(function() {
                    alr.close();
                }, o.delay * 1000);
            }
        }
    },
    close: function() {
        $('.alr').fadeOut('fast', function() {
            $('.alr').remove();
        });
    }
};

// Modals
var mdl = {
    open: function(opt) {
        var o = {
                id: Math.floor((Math.random() * 10000000) + 1)
            },
            ml = 0;
        $('body').css({
            'overflow': 'hidden',
            'padding-right': scrollWidth() + 'px'
        });
        $.extend(o, opt);
        ml = ($(window).width() / 2 - (o.width ? o.width : 500) / 2);
        $('<div/>', {
            class: 'mdlWrap ' + (o.class || ''),
            id: o.id,
            style: 'overflow-y: hidden;',
           // onclick: 'mdl.close("' + o.id + '", event)',
            html: $('<div/>', {
                class: 'mdl',
                style: (o.width ? 'width: ' + o.width + 'px; ' : '') + 'margin-left: ' + (ml < 10 ? '10' : ml) + 'px',
                html: o.title ? $('<div/>', {
                    class: 'mdlTitle',
                    html: $('<span/>', {
                        class: 'mdlClose fa fa-times',
                        onclick: 'mdl.close("' + o.id + '", event);'
                    })
                }).prepend(o.title) : ''
            }).append($('<div/>', {
                class: 'mdlBody',
                html: o.content
            })).append(o.footer ? $('<div/>', {
                class: 'mdlFooter',
                html: o.footer
            }) : '')
        }).appendTo('body');
        $('#' + o.id).show(0, function() {
            p = ($(window).height() - $('#' + o.id + ' > .mdl').height()) / 2;
            $('#' + o.id).css('padding-top', (p < 70 ? 70 : p) + 'px');
            $(this).find('.mdl').addClass('show').queue(function() {
                $('#' + o.id).css('overflow', 'auto').find('.mdl').clearQueue();
                if (o.cb) o.cb(o.id);
            });
        });
    },
    close: function(i, e) {
        el = i ? $('#' + i) : $('.mdlWrap');
        if (!e || (e.target.className.indexOf('mdlWrap') >= 0 || e.target.className.indexOf('mdlClose') >= 0)) {
            el.css('overflow', 'hidden').find('.mdl').removeClass('show').queue(function() {
                el.remove();
                $('body').css({
                    'overflow-y': 'scroll',
                    'padding-right': 0
                }).clearQueue();
            })
        }
    }
};

var box = {
	open: function(a){
		var t = a.id || (new Date()).getTime(),
			l = ($(window).width() / 2 - (a.width ? a.width : 500) / 2);
			$('<div class="mdlWrap" id="'+(a.id || t)+'" style="overflow-y: hidden;"><form class="mdl" style="'+(a.width ? 'width:'+a.width+'px;' : '')+'margin-left:'+(l < 10 ? '10' : l)+'"><div class="mdlTitle">'+(a.title || '')+'<span class="mdlClose fa fa-times" onclick="box.close(\''+t+'\');"></span></div><div class="mdlBody">'+(a.content || '')+'</div><div class="mdlFooter">'+(a.submit ? '<button type="submit" name="button" class="btn btnSubmit">'+a.submit[0]+'</button>' : '')+'</div></form></div>').appendTo('body').show(0, function(){
            p = ($(window).height()-$('#'+t+' > .mdl').height())/2;
            $('#'+t).css('padding-top', (p < 70 ? 70 : p) + 'px');
            $(this).find('.mdl').addClass('show').delay(300).queue(function() {
                $('#'+t).css('overflow', 'auto').find('.mdl').clearQueue();
                if (a.cb) a.cb(a.id);
            });
        }).find('form').submit(function(e){
			e.preventDefault();
			 if (a.submit && a.submit[1]) a.submit[1].apply(this, [t]);
			return false;
		});
	},
	close: function(a){
		$('#'+a).css('overflow', 'hidden').find('.mdl').removeClass('show').delay(350).queue(function(){
			$(this.parentNode).remove();
			$('body').css({
				'overflow-y': 'scroll',
				'padding-right': 0
			});
		});
	}
};

// Show photos
function showPhoto(p) {
    $('body').append($('<div/>', {
        class: 'photoWrap',
        onclick: '$(this).remove();',
        html: $('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })
    }).append($('<img/>', {
        src: p.replace('thumb_', '')
    })));
}

// Load button
var loadBtn = {
    class: '',
    start: function(e) {
        loadBtn.class = e.attr('disabled', true).find('span').attr('class');
        e.find('span').removeClass(loadBtn.class).addClass('fa fa-refresh fa-spin');
    },
    stop: function(e) {
        e.attr('disabled', false).find('span').removeClass('fa fa-refresh fa-spin').addClass(loadBtn.class);
    }
}

// Drag & drop
var dragdrop = {
    dragEl: {},
    mouse: {},
    parentEl: {},
    dragstart: function(e) {
        dragdrop.mouse = e;
        dragdrop.dragEl = e.target;
        dragdrop.parentEl = dragdrop.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', dragdrop.dragEl.textContent);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        dragdrop.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== dragdrop.dragEl && $(target).hasClass('dragel') && !$(target).hasClass('child')) {
        	if (e.pageX - dragdrop.mouse.pageX > 20) {
            if (!$(target).find('.child').find('#'+dragdrop.dragEl.id).length) {
          	 	$(target).find('.child').first().append($(dragdrop.dragEl));
              //unset($('#'+dragdrop.dragEl.id));
             }
          } else {
             if (!$(target).index()) {
                  $(target).before($(dragdrop.dragEl))   
             } else {
                 $(target).after($(dragdrop.dragEl))
             }
           }
        }
    }
}