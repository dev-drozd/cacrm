/**
    Jquery Plugin: Fast-Editor
    Authors: Alexandr Drozd
	Copyright: Fast Engine 2020
	Email:  dev@fast-engine.org, 
    Year: 2020
*/
(function($) {

    $.fn.fEditor = function(p) {

        /*
         * Options
         */
        var opts = typeof p == 'object' ? p : $.extend({
            plugins: '*',
            spellcheck: true,
        }, p);

        /*
         * Compiling editor
         */

        this.each(function() {
            var _t = this;
            _t.className = 'eArea';
            _t.style.display = 'none';
			$(_t).bind('paste blur', function() {
				_t.parentNode.querySelector('div.eArea').innerHTML = _t.value;
			})
            var area = $('<div/>', {
                    class: 'eArea',
                    contenteditable: true,
                    spellcheck: opts.spellcheck,
                    html: _t.value
                }).bind('mousedown', function(e) {
                    switch (e.target.tagName) {
                        case 'IMG':
                            e.target.ondblclick = $.fEditor.editImage;
                            break;
                        case 'A':
                            $.fEditor.editLink(e);
                            break;
                    }
                }).bind('paste blur', function() {
                    _t.value = this.innerHTML;
                }),
                panel = $('<div/>', {
                    class: 'eTools'
                }),
                parent = $('<div/>', {
                    class: 'editor',
                    html: panel
                }).append(area);
            $.fEditor.area = area;
            $.fEditor.textarea = _t;
            $.each($.fEditor.methods, function(method, fn) {
                var plugins = opts.plugins;
                if (plugins && plugins !== '*') {
                    var arr = plugins.replace(/\s+/g, '').split(',');
                    return arr.indexOf(method) >= 0 ? fn.apply($.fEditor, [panel, area]) : null;
                } else
                    return fn.apply($.fEditor, [panel, area]);
            });
            $(_t).after(parent).appendTo(parent);

            var help = $('<div style="text-align: right;">\
                <a href="/" onclikc="return false;">Help</a>\
            </div>')
            .insertAfter($(_t).parents('.editor'))
            .children('a')
            .bind('click', function(e) {
                e.preventDefault();
                $.fEditor.showHelp();
            })
        });
    };

    /*
     * Object editor
     */
    $.fEditor = new function(_w, _d) {

        /*
         * Localization
         */
        this.lang = {
            resize: 'Full Screen',
            undo: 'Back',
            redo: 'Forward',
            bold: 'Bold',
            italic: 'italic',
            strike: 'strikeout',
            underline: 'underlined',
            left: 'Align Left',
            center: 'Centered',
            right: 'Align Right',
            list: 'List',
            numlist: 'Numbered list',
            quote: 'Tsytata',
            hr: 'Horizontal Line',
            format: 'Format',
            code: 'Show code',
            color: 'Text Color',
            background: 'The background color of the text',
            text: 'Text',
            size: 'font size',
            header: 'Title'
        };

        this.showHelp = function() {
            mdl.open({
                id: 'fEditor',
                width: 500,
                title: 'Help',
                content: '<table class="responsive">' +
                    '<tr><td class="td"><b>{store}</b> - Will replace by store name</td></tr>' +
                    '<tr><td class="td"><b>{quote=Your text}</b> - Will show a special button which will bring pop-up windows for Quote request. Your text can be replaced by any text</td></tr>' +
                    '<tr><td class="td"><b>{call_req=Call me}</b> - Will show a special button which will bring pop-up for call back request, where \'call me\' can be replaced with any text</td></tr>' +
                '</table>'
            });
        };

        /*
         * Html formating
         */
		this.form_html = function(a){
			a = a.trim();
			var r = '', intl = 0, ts = a.split(/</);
			function intd(lv) {
				var r = '', i = lv * 4;
				if(lv < 0) return;
				while (i--) r += ' ';
				return r;
			}
			for(var i = 0, l = ts.length; i < l; i++){
				var p = ts[i].split(/>/);
				if(p.length === 2){
					if(ts[i][0] === '/') intl--;
					r += intd(intl);
					if(ts[i][0] !== '/') intl++;
					if(i > 0) r += '<';
					r += p[0].trim() + ">\n";
					if(p[1].trim() !== '') r += intd(intl)+p[1].trim().replace(/\s+/g, ' ')+"\n";
					if(p[0].match(/^(img|hr|br)/)) intl--;
				} else
					r += intd(intl) + p[0] + "\n";
			}
			return r;
		}

        /*
         * Image Editing
         */
        this.editImage = function(e) {
            var el = e.target,
                title = el.alt,
                width = el.width,
                height = el.height,
				align = el.align,
				margin = el.hspace || el.vspace || 0,
				link = '';
				
			//if(el.parentNode.tagName == 'A')
			//	link = el.parentNode.href;
			
            mdl.open({
                id: 'fEditor',
                width: 700,
                title: 'Edit Image',
                cb: function() {
                    $('#page').radio()
                },
                content: $('<div/>', {
                    class: 'imgEdit',
                    html: $('<div/>', {
                        class: 'iZone',
                        html: $('<img/>', {
                            src: e.target.src
                        })
                    })
                }).append($('<div/>', {
                    class: 'eZone',
                    html: $('<div/>', {
                        class: 'iGroup ie',
                        html: $('<label/>', {
                            html: 'Title'
                        })
                    }).append($('<input/>', {
                        type: 'text',
                        name: 'title',
                        value: title
                    }))
                }).append($('<div/>', {
                    class: 'iGroup ie',
                    html: $('<label/>', {
                        html: 'Margin'
                    })
                }).append($('<input/>', {
                    type: 'number',
                    name: 'margin',
                    value: margin
                }))).append($('<div/>', {
                    class: 'iGroup ie',
                    html: $('<label/>', {
                        html: 'Size'
                    })
                }).append($('<div/>', {
                    class: 'rGroup',
                    html: $('<input/>', {
                        type: 'number',
                        name: 'width',
                        class: 'iSize',
                        value: width
                    })
                }).append($('<span/>', {
                    class: 'fa fa-times sz'
                })).append($('<input/>', {
                    type: 'number',
                    name: 'height',
                    class: 'iSize',
                    value: height
                })).append('px'))).append($('<div/>', {
                    class: 'iGroup ie',
                    html: $('<label/>', {
                        html: 'Align'
                    })
                }).append($('<div>', {
                    class: 'iALign',
                    html: $('<input/>', {
                        type: 'radio',
                        name: 'iAlign',
                        'data-label': 'left',
                        value: 'left',
						checked: align == 'left' ? true : false
                    })
                }).append($('<input/>', {
                    type: 'radio',
                    name: 'iAlign',
                    'data-label': 'center',
                    value: 'center',
					checked: align == 'center' ? true : false
                })).append($('<input/>', {
                    type: 'radio',
                    name: 'iAlign',
                    'data-label': 'right',
                    value: 'right',
					checked: align == 'right' ? true : false,
                }))))).append($('<div/>', {
                    class: 'ieBottom',
                    html: $('<button/>', {
                        class: 'btn btnSave',
                        html: 'Save'
                    }).click(function() {
						var title = $('#fEditor input[name="title"]').val(),
							margin = $('#fEditor input[name="margin"]').val(),
							width = $('#fEditor input[name="width"]').val(),
							height = $('#fEditor input[name="height"]').val(),
							align = $('#fEditor input[name="iAlign"]:checked').val();
						
						el.alt = title;
						el.width = width;
						el.height = height;
						el.hspace = margin;
						el.vspace = margin;
						el.align = align || '';
						
/*  						if(link){
							if(el.parentNode.tagName == 'A')
								el.parentNode.href = link;
							else {
								var hlink = $('<div/>').html($('<a/>', {
									href: link,
									target: '_blank'
								}).html(el)).html();
								$(el).replaceWith(hlink);
							}
						} else if(el.parentNode.tagName == 'A'){
							$(el.parentNode).replaceWith($.parseHTML(el)); */
						
						$.fEditor.textarea.value = $.fEditor.area.html();
						 
						mdl.close();
					})
                }))
            });
        };

        /*
         * Link Editing
         */
        this.editLink = function(e, b) {
            var _t = this;
            if (b) {
                var _s = _t.getSelection(),
                    _r = _t.getRange(_t.area, e),
                    _l = '';
            } else {
                var el = $(e.target),
                    _s = el.html(),
                    _l = el.attr('href');
            }
            mdl.open({
                id: 'feEditor',
                title: 'Adding link',
                content: '<div class="iGroup">\
					<label>Text:</label>\
					<input type="text" name="text" value="' + _s + '">\
				</div>\
				<div class="iGroup">\
					<label>Link:</label>\
					<input type="text" name="link" value="' + _l + '">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit">Insert</button>\
				</div>'
            });
            $('#feEditor button').click(function() {
                var text = $('#feEditor input[name="text"]').val(),
                    link = $('#feEditor input[name="link"]').val();
                if (b) {
                    _s.removeAllRanges();
                    if (_r) _s.addRange(_r);
                    _t.command(_t.area, 'insertHTML', false, '<a href="' + link + '" target="_blank">' + text + '</a>', e);
                } else
                    el.html(text).attr('href', link);
                mdl.close();
            });
        };

        /*
         * The selected area
         */
        this.getSelection = function() {
            return _w.getSelection ? _w.getSelection() : '';
        };

        /*
         * Executing commands
         */
        this.command = function(a, b, c, d, e) {
            e.preventDefault();
            var s = this.getSelection(a),
                rc = false;
            if (s && s.rangeCount) {
                var r = s.getRangeAt(0);
                rc = r.commonAncestorContainer || (
                    r.parentElement ? r.parentElement() : r.item(0)
                );
            }
            while (rc && rc != a[0]) rc = rc.parentNode;
            if (!rc) this.setFocus(a[0], false, true);
            _d.execCommand(b, c, d);
        };

        /*
         * Get range
         */
        this.getRange = function(a, e) {
            e.preventDefault();
            var s = this.getSelection(a);
            return s.rangeCount ? s.getRangeAt(0) : null;
        };

        /*
         * Setting the focus to the editor
         */
        this.setFocus = function(e) {
            if (typeof _w.getSelection != 'undefined' && typeof _d.createRange != 'undefined') {
                var s = _w.getSelection(),
                    r = _d.createRange();
                r.selectNodeContents(e);
                r.collapse(false);
                var s = _w.getSelection();
                s.removeAllRanges();
                s.addRange(r);
            } else if (typeof _d.body.createTextRange != 'undefined') {
                var r = _d.body.createTextRange();
                r.moveToElementText(e);
                r.collapse(false);
                r.select();
            }
        };

        /*
         * Compile button
         */
        this.createButton = function(e, p) {
            function item(el, obj) {
                $.each(obj, function(title, o) {
                    $.each(o, function(a, b) {
                        var button = $('<button/>', {
                            class: 'eBtn ' + a,
							type: 'button'
                        });
                        if (typeof b === 'function') {
                            button.mousedown(b)
                        } else {
                            var button = $('<div/>', {
                                class: 'eBox',
                                html: button
                            }).append($('<div/>', {
                                class: 'eDrop' + (b[2] ? ' ' + b[2] : ''),
                                html: b[0]
                            }).mousedown(b[1]));
                        }
                        $(el).append(button);
                    });
                });
            }
            if (p.length) {
                var group = $('<div/>', {
                    class: 'eGrp'
                }).appendTo(e);
                $.each(p, function() {
                    item(group, this);
                });
            } else
                item(e, p);
        };

        /*
         * Methods
         */
        this.methods = {

            // Full screen mode
            resize: function(a, form) {
                var lng = this.lang.resize;
                this.createButton(a, {
                    lng: {
                        'fa fa-expand rsz': function(e) {
                            if ($(a).parent().hasClass('fullScreen')) {
                                $('body').css('overflow', 'auto')
                                $(a).find('.rsz')
                                    .addClass('fa-expand')
                                    .removeClass('fa-compress')
                                    .parents('.editor')
                                    .removeClass('fullScreen');
                            } else {
                                $(a).find('.rsz')
                                    .removeClass('fa-expand')
                                    .addClass('fa-compress')
                                    .parents('.editor')
                                    .addClass('fullScreen');
                                $('body').css('overflow', 'hidden')
                            }
                        }
                    }
                });
            },

            // Font Style
            style: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    bold = l.bold,
                    italic = l.italic,
                    strike = l.strike,
                    underline = l.underline;
                this.createButton(a, [{
                    bold: {
                        'fa fa-bold': function(e) {
                            _t.command(b, 'bold', false, null, e);
                        }
                    }
                }, {
                    italic: {
                        'fa fa-italic': function(e) {
                            _t.command(b, 'italic', false, null, e);
                        }
                    }
                }, {
                    strike: {
                        'fa fa-strikethrough': function(e) {
                            _t.command(b, 'strikeThrough', false, null, e);
                        }
                    }
                }, {
                    underline: {
                        'fa fa-underline': function(e) {
                            _t.command(b, 'underline', false, null, e);
                        }
                    }
                }]);
            },

            // Alignment
            aligment: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    left = l.left,
                    center = l.center,
                    right = l.right;
                this.createButton(a, [{
                    left: {
                        'fa fa-align-left': function(e) {
                            _t.command(b, 'justifyLeft', false, null, e);
                        }
                    }
                }, {
                    center: {
                        'fa fa-align-center': function(e) {
                            _t.command(b, 'justifyCenter', false, null, e);
                        }
                    }
                }, {
                    right: {
                        'fa fa-align-right': function(e) {
                            _t.command(b, 'justifyRight', false, null, e);
                        }
                    }
                }, {
                    full: {
                        'fa fa-align-justify': function(e) {
                            _t.command(b, 'justifyFull', false, null, e);
                        }
                    }
                }]);
            },

            // Indent
            indent: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    indent = l.indent,
                    outdent = l.outdent;
                this.createButton(a, [{
                    indent: {
                        'fa fa-indent': function(e) {
                            _t.command(b, 'indent', false, null, e);
                        }
                    }
                }, {
                    outdent: {
                        'fa fa-outdent': function(e) {
                            _t.command(b, 'outdent', false, null, e);
                        }
                    }
                }]);
            },

            // Lists
            list: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    list = l.list,
                    numlist = l.numlist;
                this.createButton(a, [{
                    list: {
                        'fa fa-list-ul': function(e) {
                            _t.command(b, 'insertUnorderedList', false, null, e);
                        }
                    }
                }, {
                    numlist: {
                        'fa fa-list-ol': function(e) {
                            _t.command(b, 'insertOrderedList', false, null, e);
                        }
                    }
                }]);
            },

            // Inserts
            insert: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    quote = l.quote,
                    hr = l.hr,
                    th = '',
                    tw = 10,
                    tc = 0;
                for (var i = 1; i <= 100; i++) {
                    if (tc < 10) tc++;
                    else tc = 1;
                    var h = 10 * tc;
                    th += '<span data-row="' + i + '" style="width:' + tw + '%;height:' + h + '%;z-index:' + (100 - i) + '" title="' + (h / 10) + ',' + (tw / 10) + '"></span>';
                    if (h == 100) tw = tw + 10;
                }
                this.createButton(a, [{
                    quote: {
                        'fa fa-quote-left': function(e) {
                            var sel = _t.getSelection(b);
                            if (sel) {
                                _t.command(b, 'insertHTML', false, '<blockquote>' + sel + '</blockquote>&nbsp;', e);
                            }
                        }
                    }
                }, {
                    hr: {
                        'fa fa-minus': function(e) {
                            _t.command(b, 'InsertHorizontalRule', true, '', e);
                        }
                    }
                }, {
                    link: {
                        'fa fa-link': function(e) {
                            _t.editLink(e, true);
                        }
                    }
                }, {
                    table: {
                        'fa fa-table': [th, function(e) {
                            var num = e.target.getAttribute('title'),
                                rows = '',
                                _s = _t.getSelection(),
                                _r = _t.getRange(b, e);
                            if (num) {
                                var len = num.split(',');
                                for (var i = 0; i < len[0]; i++) {
                                    rows += '<tr>';
                                    for (var j = 0; j < len[1]; j++) {
                                        rows += '<td>&#65279;</td>';
                                    }
                                    rows += '</tr>';
                                }
                                _s.removeAllRanges();
                                if (_r) _s.addRange(_r);
                                _t.command(b, 'insertHTML', false, '<table cellspacing="5" cellpadding="0">' + rows + '</table>&nbsp;', e);
                            }
                        }, 'table']
                    }
                }, {
                    image: {
                        'fa fa-photo': function(e) {
                            var _s = _t.getSelection(),
                                _r = _t.getRange(b, e),
                                admin = location.pathname.split('/'),
                                preloader = '<div class="loader">\
									<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>\
									<span class="">Image is loading. Please, wait...</span>\
								</div>';
                            mdl.open({
                                id: 'fEditor',
                                title: 'Insert Image',
                                content: $('<div/>', {
                                    class: 'dragndrop',
                                    html: $('<span/>', {
                                        class: 'fa fa-download'
                                    })
                                }).append('Click or drag and drop file here').upload({
                                    check: function(r) {
                                        if (!r.error) {
                                            $('#fEditor .mdlBody').html(preloader);
                                            var form = new FormData();
                                            form.append('image', r.file, r.file.name);
                                            $.ajax({
                                                type: 'POST',
                                                processData: false,
                                                contentType: false,
                                                url: '/editor',
                                                data: form
                                            }).done(function(r) {
                                                var j = JSON.parse(r);
                                                if (!j.error) {
                                                    _s.removeAllRanges();
                                                    if (_r) _s.addRange(_r);
                                                    _t.command(b, 'insertHTML', false, '<img src="' + j.image + '">&nbsp;', e);
                                                    mdl.close();
                                                }
                                            });
                                        } else if (e.error == 'type') {
                                            alr.show({
                                                class: 'alrDanger',
                                                content: 'You can load only jpeg, jpg, png, gif images',
                                                delay: 2
                                            });
                                        } else if (e.error == 'size') {
                                            alr.show({
                                                class: 'alrDanger',
                                                content: 'Upload size for images is more than allowable',
                                                delay: 2
                                            });
                                        }
                                    }
                                })
                            });
/*                             $('#fEditor .mdlBody').append(preloader);
                            $.getJSON('/editor/list', function(list) {
                                if (list.length) {
                                    $('#fEditor .loader').replaceWith($('<div/>', {
                                        class: 'thumbnails'
                                    }));
                                    $.each(list, function() {
                                        $('#fEditor .thumbnails').append($('<div/>', {
                                            class: 'thumb',
                                            html: $('<img/>', {
                                                src: this
                                            }).click(function(e) {
                                                _s.removeAllRanges();
                                                if (_r) _s.addRange(_r);
                                                _t.command(b, 'insertHTML', false, '<img src="' + e.target.src.replace(location.origin, '').replace('thumb_', '') + '">&nbsp;', e);
                                                mdl.close();
                                            })
                                        }).append($('<span/>', {
                                            class: 'fa fa-times'
                                        }).click(function(e) {
                                            var p = $(e.target.parentNode),
                                                src = p.find('img[src]').attr('src');
                                            p.remove();
                                            $.post('/editor/del_image', {
                                                image: src
                                            })
                                        })));
                                    });
                                } else
                                    $('#fEditor .loader').remove();
                            }); */
                        }
                    }
                }, {
                    youtube: {
                        'fa fa-youtube': function(e) {
                            var _s = _t.getSelection(),
                                _r = _t.getRange(b, e);
                            mdl.open({
                                id: 'feEditor',
                                title: 'Youtube link',
                                content: '<div class="iGroup">\
									<label>URL:</label>\
									<input type="text" name="url">\
									<a href="//youtube.com/upload" target="_blank">Go to upload</a>\
								</div>\
								<div class="sGroup">\
									<button class="btn btnSubmit">Insert</button>\
								</div>'
                            });
                            $('#feEditor button').click(function() {
                                var url = $('#feEditor input[name="url"]').val(),
                                    match = url.match(/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/);
                                if (match && match[1].length === 11) {
                                    _s.removeAllRanges();
                                    if (_r) _s.addRange(_r);
                                    _t.command(b, 'insertHTML', false, '<iframe src="//www.youtube.com/embed/' + match[1] + '" frameborder="0" width="640" height="360"></iframe>&nbsp;', e);
                                    mdl.close();
                                } else
                                    alert('Ссылка не верная');
                            });
                        }
                    }
                }]);
            },

            // Formatting
            code: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    format = l.format,
                    code = l.code;
                this.createButton(a, [{
                    format: {
                        'fa fa-eraser': function(e) {
                            var sel = _t.getSelection(b);
                            if (sel) {
                                _t.command(b, 'removeFormat', false, null, e);
                            }
                        }
                    }
                }, {
                    code: {
                        'fa fa-code': function(e) {
                            if (!b.code) {
								var tr = b.hide().next().show();
								tr.val(_t.form_html(tr.val()).trim());
                                //b.text(b.html());
                                b.code = true;
								$(a).parent().find('.eTools button:not(.fa-code, .rsz)').attr('disabled', 'disabled');
                            } else {
								b.show().next().hide();
                                //b.html(b.text());
                                b.code = false;
								$(a).parent().find('.eTools button:not(.fa-code, .rsz)').removeAttr('disabled');
                            }
                        }
                    }
                }]);
            },

            // Text and Fonts
            font: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    color = l.color,
                    background = l.background,
                    family = l.family,
                    size = l.size,
                    header = l.header,
                    colors = [
                        '#fff', '#000', '#eee', '#f00', '#00f', '#F2FB5B', '#EB981B', '#EB567F',
                        '#C625DA', '#6DBDF5', '#5BD4B8', '#44C06A', '#6FE760', '#C2C2C2', '#464646'
                    ],
                    families = ['cursive', 'fantasy', 'monospace', 'sans-serif', 'serif'],
                    families = ['cursive', 'fantasy', 'monospace', 'sans-serif', 'serif'],
                    ch = '',
                    fh = '<ul>',
                    fzh = '<ul>',
                    hh = '<ul>';
                $.each(colors, function() {
                    ch += '<span data-color="' + this + '" style="background: ' + this + '"></span>';
                });
                $.each(families, function() {
                    fh += '<li data-family="' + this + '" class="' + this + '">' + this + '</li>';
                });
                for (var i = 1; i <= 7; i++) {
                    fzh += '<li data-size="' + i + '"><font size="' + i + '" data-size="' + i + '">' + l.text + '</font></li>';
                }
                for (var i = 1; i <= 6; i++) {
                    hh += '<li data-header="' + i + '" class="h' + i + '">' + header + ' ' + i + '</li>';
                }
                this.createButton(a, [{
                    color: {
                        'fa fa-adjust': [ch, function(e) {
                            var hex = e.target.getAttribute('data-color');
                            if (hex) _t.command(b, 'foreColor', true, hex, e);
                        }, 'color']
                    }
                }, {
                    background: {
                        'fa fa-th': [ch, function(e) {
                            var hex = e.target.getAttribute('data-color');
                            if (hex) _t.command(b, 'backColor', true, hex, e);
                        }, 'color']
                    }
                }, {
                    family: {
                        'fa fa-font': [fh + '</ul>', function(e) {
                            var fn = e.target.getAttribute('data-family');
                            if (fn) _t.command(b, 'fontName', true, fn, e);
                        }, 'family']
                    }
                }, {
                    size: {
                        'fa fa-text-height': [fzh + '</ul>', function(e) {
                            var fz = e.target.getAttribute('data-size');
                            if (fz) _t.command(b, 'fontSize', true, fz, e);
                        }, 'size']
                    }
                }, {
                    header: {
                        'fa fa-header': [hh + '</ul>', function(e) {
                            var h = e.target.getAttribute('data-header');
                            if (h) _t.command(b, 'formatBlock', true, 'H' + h, e);
                        }, 'header']
                    }
                }]);
            },

            // Navigation
            history: function(a, b) {
                var _t = this,
                    l = _t.lang,
                    undo = l.undo,
                    redo = l.redo;
                this.createButton(a, [{
                    undo: {
                        'fa fa-undo': function(e) {
                            _t.command(b, 'undo', false, null, e);
                        }
                    }
                }, {
                    redo: {
                        'fa fa-repeat': function(e) {
                            _t.command(b, 'redo', false, null, e);
                        }
                    }
                }]);
            }
        };

    }(window, document);

})(jQuery);