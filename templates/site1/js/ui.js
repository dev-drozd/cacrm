/**
 * @appointment UI Elements
 * @author      Victoria Shovkovych, Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

(function($){

	// Uploader
	$.fn.upload = function(p){
		
		var opts = typeof p == 'string' ? p : $.extend({
			autoUpload: false,
			count: 0,
			multiple: false,
			accept: 'image/*',
			size: 10,
			max: 1,
			check: function(){}
		}, p);

		$(this).each(function(i, e){
			var self = this;
			self.files = {};
			self.count = opts.count;
			self.delete = [];
			self.input = $('<input/>', {
				type: 'file',
				accept: opts.accept,
				multiple: opts.multiple
			}).change(function(e){
				self.check(e.target);
			});
			$(window).bind('dragenter dragover', function(e){
				e.preventDefault();
				
			}).bind('dragleave', function(e){
				
			}).bind('drop', function(e){
				e = e.originalEvent;
				e.preventDefault();
				e.stopPropagation();
				if(e.dataTransfer.files.length)
					self.check(e.dataTransfer);
			});
			$(self).click(function(){
				self.input.click();
			});
			self.check = function(e){
				var count = Object.keys(self.files).length, files = e.files;
				if((count+self.count+files.length) > opts.max){
					opts.check.apply(self, [{
						error: 'max',
						files: files
					}]);
					return;
				}
				$.each(files, function(i){
					if(!this.type.match(/image\/(jpeg|jpg|png|gif)/i)){
						opts.check.apply(self, [{
							error: 'type',
							file: this
						}]);
						return;
					}
					if(this.size > (1024*opts.size*1024)){
						opts.check.apply(self, [{
							error: 'size',
							file: this
						}]);
						return;
					}
					if(opts.check.apply(self, [{
						file: this
					}]) === false) return false;
					self.files[this.name] = this;
				});
				self.upload();
			};
			self.upload = function(){
				if(opts.auto){

				}
			};
		});
		
		return this;
	};
  
	// Checkbox
	$.fn.checkbox = function(n){
		$('input[type="checkbox"]').each(function(i, e){
			if(!$(e).next().hasClass('cbWrap')){
				$(e).hide().val($(e).is(':checked') ? 1 : 0).after($('<div/>', {
					class: 'cbWrap',
					html: $('<span/>'),
				}).click(function(ev){
					$(e).val($(e).is(':checked') ? 0 : 1).prop('checked', !$(e).prop('checked'));
					if(e.onchange)
						e.onchange();
				})
				);
			}
		});
	};
	
	// Radio
	$.fn.radio = function(n){
		$('input[type="radio"]').each(function(i, e){
			if(!$(e).parent().hasClass('rbWrap')){
				$(e).wrap($('<div/>', {
					class: 'rbWrap'
				})).parent().append($('<label/>', {
					class: 'rdLbl',
					html: $(e).attr('data-label')
				}).click(function(ev){
						$(this).parent().find('input').trigger('click');
						if(e.onchange)
							e.onchange();
					})
				);
			}
		});
	};
 
  // Select 
  $.fn.select = function(n){
    $('select').each(function(i, e){
		if (!$(e).next().hasClass('sWrap')) {
			if ($(e).attr('required') && !$(e).val().length) {
				$(e).find('option').first().attr('selected', true);
			}
			$(e).hide(0).after($('<div/>', {
				class: 'sWrap',
				html: $('<ul/>')
			  })
			);
			if (!$(e).attr('multiple')) 
			  $(e).next().prepend($('<div/>').click(function() {
				$(e).next().find('ul').slideToggle('fast');
			  }));
			var items = $(e).next().find('ul');
			$(e).find('option').each(function() {
			  items.append($('<li/>', {
				  'class': $(this).prop('selected') ? 'active' : '',
				  'data-value': $(this).val(),
				  'html': $(this).text()
				}).click(function() {
					if ($(e).attr('required') && $(e).val() == $(this).attr('data-value')) {
						alr.show({
							class: 'alrDanger',
							content: 'The field must have a value',
							delay: 1.5
						});
					} else {
						if (!$(e).attr('multiple')) {
							$(e).next().find('div').html($(this).html()).next().slideToggle('fast');
							$(e).next().find('li').removeClass('active');
						}
						$(this).toggleClass('active');
						$(e).find('option[value="' + $(this).attr('data-value') + '"]')
							.prop('selected', !$(e).find('option[value="' + $(this).attr('data-value') + '"]').prop('selected')).trigger('change');
					}
				})
			  );
			  if (!$(e).attr('multiple') && $(this).prop('selected')) $(e).next().find('div').html($(this).text());
			});
			$(e).change(function (event) {
				if (!$(e).attr('multiple'))
					$(e).next().find('div').html($(e).next().find('li[data-value="' + $(e).val() + '"]').text());
				else {
					$(e).find('option').each(function() {
					  $(this).prop('selected') ? items.find('li[data-value="' + $(this).val() + '"]').addClass('active') : items.find('li[data-value="' + $(this).val() + '"]').removeClass('active');
					});
				}
			});
		}
    });
  };
  
  // Search form
	$.fn.sForm = function(o, inp, cb) {
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
                    nIds: []
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
                })).find('li').click(function() {
                    click($(this));
                });
                if (e.parents('.mdl')) {
                    p = ($(window).height() - e.parents('.mdl').height()) / 2;
                    e.parents('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
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
                    opt.data.query = e.parent().parent().find('input').val();
                    post();
                }
            });

            function showList(el, i) {
                ce = el;
				$('.sfWrap').css('z-index', '1')
                $(el).parent().css('z-index', '99999');
				$(el).next().slideDown();
				$('html').on('click', function(event) {
					if (!$(event.target).hasClass('nc')) {
						$(el).parent().css('z-index', '1');
						$(el).next().slideUp();
						$(el).off('click').on('click', function() {
							showList(el);
						});
						$('html').off('click');
					}
				})
                $(el).off('click').on('click', function(e) {
                    if (!$(e.target).attr('id')) {
						$(el).parent().css('z-index', '1');
                        $(el).next().slideUp();
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
					$.post(opt.action, opt.data, function(res) {
						if (res) {
							$.each(res.list, function(i, li) {
								e.parent().find('ul').append($('<li/>', {
									'class': 'nc',
									'data-value': li.id,
									'data-price': '$' + li.price,
									'data-tax': li.tax,
									'data-catname': li.catname,
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
                var id = nel ? nel : el.attr('data-value'),
                    name = nel ? el.name : el.text(),
                    lastname = nel ? el.lastname : el.text().split(' ')[1],
                    price = nel ? el.price : el.attr('data-price');
                catname = nel ? el.catname : el.attr('data-catname');
                tax = nel ? el.tax : el.attr('data-tax');
                photo = nel ? el.photo : el.attr('data-image');
                if (opt.s) {
                    e.parent().parent().find('div > span').each(function(i, sp) {
                        clickLi($(sp), id);
                    });
                }
                e.parent().parent().find('input').before($('<span/>', {
                    id: id,
					'class': 'nc',
                    'data-image': photo,
                    'data-price': price,
                    'data-tax': tax,
                    'data-catname': catname,
                    html: name
                }).append($('<i/>')).click(function(ev) {
                    clickLi($(this), id);
                }));
                if (e.parent().parent().find('li[data-value="' + id + '"]')) e.parent().find('li[data-value="' + id + '"]').remove();
                if (opt.s) {
                    dSelect = {};
                    dSelect[id] = {
                        name: name,
                        lastname: lastname,
                        photo: photo,
                        price: price,
                        catname: catname,
                        tax: tax
                    };
                    inp.removeData().data(dSelect);
                    opt.nIds = [id];
                    e.parent().slideUp();
                    $(ce).off('click').on('click', function() {
                        showList(ce);
                    });
                } else {
                    dSelect = inp.data();
                    dSelect[id] = {
                        name: name,
                        lastname: lastname,
                        price: price,
                        catname: catname,
                        tax: tax,
                        photo: photo
                    };
                    inp.data(dSelect);
                    opt.nIds.push(id);
                }
                if (cb) cb();
            }

            function clickLi(el, id) {
                e.parent().find('ul').append($('<li/>', {
                    'data-value': el.attr('id'),
                    'data-price': el.attr('data-price'),
                    'data-catname': el.attr('data-catname'),
                    'data-image': el.attr('data-image'),
                    'data-tax': el.attr('data-tax'),
                    'html': el.html()
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
            }
        });
    };
  
  // Tab
  $.fn.tabs = function(n){
    $('.tabs').each(function(i, e){
        var e = $(this);
		if (!e.find('.tUl').length) {
			e.prepend($('<ul/>', {
				class: 'tUl'
			})).find('.tab:first').css('display', 'block').parent().find('.tab').each(function(i, v) {
				e.find('.tUl').append($('<li/>', {
					'data-value': $(this).attr('id'),
					'html': $(this).attr('data-title'),
					'class': !i ? 'active' : ''
				}).click(function() {
					el = $(this)
					el.parent().find('.active').removeClass('active');
					el.addClass('active');
					e.find('.tab').hide(0, function() {
						$('#' + el.attr('data-value')).show(0);
					});
				}));
			});
		}
    });
  };

  	// Slider
	jQuery.fn.rbtSlider = function(opt){
        return this.each(function() {
            var slider = $(this);
            if (opt.height) slider.css('height', opt.height);
            slider.find('.slItem').first().addClass('active');
            if (opt.dots) {
                var count = slider.find('.slItem').length;
                slider.append(
                    $('<div/>', {
                        class: 'slDots',
                        html: $('<div/>', {
                            class: 'slDotsSingle active'
                        })
                    })                                  
                );
                for (var i = 1; i < count; i++) {
                    slider.find('.slDotsSingle.active').clone().removeClass('active').appendTo($(this).find('.slDots'));    
                }
                slider.find('.slDotsSingle').on('click', function(){
                    curIndex = $(this).parents('.slDots').find('.active').removeClass('active').index() + 1;
                    index = $(this).addClass('active').index() + 1;
                    if (index != curIndex) {
                        if (index > curIndex) nav('next', index);
                        else nav('prev', index);
                    }
                });
            }
            if (opt.arrows) {
                slider.append(
                    $('<div/>', {
                        class: 'ctrlPrev',
                        html: '&lsaquo;'
                    }).on('click', function(){
                        nav('prev');
                    })
                ).append(
                    $('<div/>', {
                        class: 'ctrlNext',
                        html: '&rsaquo;'
                    }).on('click', function(){
                        nav('next');
                    })
                );
            }
            if (opt.auto) {
                var time = setInterval(function(){nav('next')}, opt.auto * 1000);
                slider.on('mouseover', function() {
                    clearInterval(time);
                }).on('mouseleave', function() {
                    time = setInterval(function(){nav('next')}, opt.auto * 1000);
                });
            }

            function nav(side, index) {
                if (index) {
                    nextItem = slider.find('.slItem').eq(index - 1);
                } else {
                    if (side == 'prev') {
                        if (slider.find('.slItem.active').prev().length) nextItem = slider.find('.slItem.active').prev();  
                        else nextItem = slider.find('.slItem').last();
                    } else {
                        if (slider.find('.slItem.active').next().length) nextItem = slider.find('.slItem.active').next();
                        else nextItem = slider.find('.slItem').first();
                    }    
                    slider.find('.slDots > .active').removeClass('active').parent().find('.slDotsSingle').eq(nextItem.index()).addClass('active');
                }
                nextItem.addClass(side + 'Item').delay(50).queue(function(){
                    slider.find('.slItems > .active').addClass(side).delay(700).queue(function(){
                        $(this).removeClass(side +' active').dequeue();
                    });

                    $(this).addClass(side).delay(700).queue(function(){
                        $(this).removeClass(side + ' ' + side + 'Item').addClass('active').clearQueue();
                    });

                    $(this).dequeue();
                });
            }
        });
    };

    //Carousel
    jQuery.fn.carousel = function(){
    	return this.each(function() {
			var crl = $(this), width = crl.find('.igItem').length * (crl.find('.igItem')[0].offsetWidth + 15) - 15;
			crl.find('.igGroupCtnr').css('width', width + 'px');
			$(window).resize(function(){
				crl.find('.igGroupCtnr').css('margin-left', 0);
			});
			if (width > crl.width()) {
				crl.parents('.itemGroup').find('.igArrows').find('span').on('mousedown touchstart', function () {
					if ($(this).hasClass('fa-chevron-left')) {
						crl.find('.igGroupCtnr').animate({ marginLeft: 0}, 700); 
					} else {
						crl.find('.igGroupCtnr').animate({ marginLeft:  - crl.find('.igGroupCtnr').width() + crl.width() }, 1000);
					}    
				}).on('mouseup touchend', function () {
					crl.find('.igGroupCtnr').stop();
					if (!parseFloat(crl.find('.igGroupCtnr').css('margin-left'))) {
						crl.parents('.itemGroup').find('.fa-chevron-left').addClass('disable').parent().find('.fa-chevron-right').removeClass('disable');
					} else if (parseFloat(crl.find('.igGroupCtnr').css('margin-left')) == (crl.width() - width)) {
						crl.parents('.itemGroup').find('.fa-chevron-right').addClass('disable').parent().find('.fa-chevron-left').removeClass('disable');
					} else {
						crl.parents('.itemGroup').find('.fa-chevron-left').removeClass('disable').parent().find('.fa-chevron-right').removeClass('disable');
					}
				}); 
			} else {
				crl.parents('.itemGroup').find('.igArrows').hide();
			}
		}); 
	};
	
	// Calendar
	$.fn.calendar = function (cb) {
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
})(jQuery);

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

// Modals
var mdl = {
    open: function(opt) {
        var o = {
            id: Math.floor((Math.random() * 10000000) + 1)
        }, ml = 0;
        $('body').css({
			'overflow': 'hidden',
			'padding-right': scrollWidth() + 'px'
		});
        $.extend(o, opt);
		ml = ($(window).width() / 2 - (o.width ? o.width : 500) / 2);
        $('<div/>', {
            class: 'mdlWrap ' + (o.class || ''),
            id : o.id,
			style: 'overflow-y: hidden;',
            onclick: 'mdl.close("' + o.id + '", event)',
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
            $('#' + o.id).css('padding-top', (p < 20 ? 20 : p) + 'px');
            $(this).find('.mdl').addClass('show').delay(300).queue(function(){
                $('#' + o.id).css('overflow', 'auto').find('.mdl').clearQueue();
				if (o.cb) o.cb(o.id);
            });
        });
    },
    close: function(i, e) {
        el = i ? $('#' + i) : $('.mdlWrap');
        if (!e || (e.target.className.indexOf('mdlWrap') >= 0 || e.target.className.indexOf('mdlClose') >= 0)) {
            el.css('overflow', 'hidden').find('.mdl').removeClass('show').delay(350).queue(function(){
                el.remove();
                $('body').css({
					'overflow-y': 'scroll',
					'padding-right': 0
				}).clearQueue();
            })
        }
    }
};

// Show photos
var showPhoto = {
	curr: {},
	show: function(p, m) {
		$('body').append($('<div/>', {
			class: 'photoWrap',
			onclick: m ? '' : '$(this).remove();',
			html: $('<span/>', {
				class: 'fa fa-times',
				onclick: '$(this).parent().remove();'
			})
		}).append($('<img/>', {
			src: p.replace('thumb_', '').replace('preview_', ''),
			onclick: 'showPhoto.next("' + m + '", 1);'
		})));
		if (m) {
			if (!$('#' + m).parent().next().find('.active').length) $('#' + m).parent().next().find('.igItem').first().addClass('active');
			$('#' + m).parent().next().find('.active').addClass('show');
			$('.photoWrap').append($('<span/>', {
				class: 'fa fa-chevron-left spArrow',
				onclick: 'showPhoto.next("' + m + '");'
			})).append($('<span/>', {
				class: 'fa fa-chevron-right spArrow',
				onclick: 'showPhoto.next("' + m + '", 1);'
			})).append($('<span/>', {
				class: 'fa fa-spinner fa-spin'
			}));
		}
	}, next: function(m, s) {
		$(".photoWrap > img").css('opacity', '0');
		$(".fa-spinner").css("display","block");
		curr = $('#' + m).parent().next().find('.show').removeClass('show');
		if (s) {
			if (curr.next().length) curr.next().addClass('show');
			else curr.parents('.igGroupCtnr').find('.igItem').first().addClass('show');
		} else {
			if (curr.prev().length) curr.prev().addClass('show');
			else curr.parents('.igGroupCtnr').find('.igItem').last().addClass('show');
		}
		document.querySelector('.photoWrap > img').onload  = function(){
			$(".photoWrap > img").css('opacity', '1')
			$(".fa-spinner").css("display","none");
	    };
		$('.photoWrap > img').attr('src', $('#' + m).parent().next().find('.show > img').attr('src').replace('thumb_', '').replace('preview_', ''))
	}, change: function(e) {
		$(e).parents('.igGroupCtnr').find('.active').removeClass('active');
		$(e).parent().addClass('active').parents('.itemPhotosSet').prev().find('img').attr('src', $(e).attr('src'));
	}
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

//Scroll width
function scrollWidth() {
    return window.innerWidth - document.documentElement.clientWidth;
}