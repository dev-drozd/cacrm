 /**
 * @appointment Main functions
 * @authors     Alexandr Drozd, Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
var page = 0,
	page_stats = 0,
    query = '',
	model_id = '',
	type_id = '',
	brand_id = '',
	location_id = '',
	object_id = '';
	location_count = 0;

// Is valid form
var is_valid = {
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
}

// Is mobile
function is_mobile() {
    var u = (navigator.userAgent || navigator.vendor || window.opera);
    return /(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(u) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
        u.substr(0, 4)
    );
}

//Scroll width
function scrollWidth() {
    return window.innerWidth - document.documentElement.clientWidth;
}

// Onload page
window.onload = function() {
    window.setTimeout(function() {
            window.addEventListener("popstate", function(e) {
                e.preventDefault();
                if (e.state)
                    Page.get(e.state.link, true);
                else
                    Page.get(window.location.pathname, true);
            }, false);
        },
        1);
    Page.init();
};

$(window).resize(function() {
    menu();
    $('.lMenu').removeClass('show');
});

// Menu events
function menu() {
    $('.dd > a').attr('onclick', '$(this).next().slideToggle(\'fast\');return false;');
}

// Page
var Page = {
    init: function(href) {
        page = 0, query = '';
        menu();
        $('#page').checkbox();
        $('#page').select();
        $('#page').tabs();
        $('#page').radio();
        if ($(window).width() <= 992) $('.lMenu').removeClass('show');
        $('.dd > ul').slideUp('fast');
		$('.navMore').slideUp('fast');
    },
    get: function(href, back){
		$('body').addClass('pre');
        if (!back) {
            history.pushState({
                link: href
            }, null, href);
        }
        $.getJSON(href, function(r) {
            if (r == 'loggout') location.reload();
            document.title = r.title;
			if(r.new_msg > 0){
				var mntf = $('#new_msg > a'), sp = mntf.find('.newMsg');
				if(sp.length){
					sp.text(r.new_msg);
				} else {
					mntf.append($('<span/>', {
						class: 'newMsg',
						text: r.new_msg
					}));
				}
			} else {
				$('#new_msg > a > .newMsg').remove();
			}
            $('#cpu').text(r.cpu);
            $('#time_script').text(r.time_script);
            $('#compile_tpl').text(r.compile_tpl);
            $('#query_cache').html(r.query_cache);
            $('#query_db').html(r.query_db);
            $('#points_top').html(r.points);
            $('#page').html(r.content);
			$('body').removeClass('pre');
            $(window).scrollTop(0);
            Page.init(href);
        });
    }
};

var barTimer = undefined;

function checkBarcode(t){
	clearTimeout(barTimer);
	console.log(t);
	var e = t.match(/(US|IS|DE|OB|PU|IN)\s([0-9]+)[.*]?/i), l, i;
	if(e){
		barTimer = setTimeout(function(){
			console.log(e);
			i = parseFloat(e[2])
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
}

var searchTimer = undefined;

function mainSearch(a){
	if(a.length){
		clearTimeout(searchTimer);
		searchTimer = setTimeout(function(){
			$.post('/search', {
				q: a,
				type: $("[name='sGroup']").val()
			}, function(j){
				$('.searchResult').html(j.content).show();
			}, 'json');
		}, 200);
	} else
		$('.searchResult').hide();
}


// Search
var Search = function(a){
    query = a;
    $.post(location.pathname, {
        query: a,
		event: $('select[name="searchSel"]').val() || '',
		object: $('select[name="object"]').val() || Object.keys($('input[name="object"]').data() || {}).join(','),
		date_start: $('#calendar > input[name="date"]').val(),
		date_finish: $('#calendar > input[name="fDate"]').val(),
		status: $('select[name="status"]').val(),
		type: $('select[name="type"]').val(),
		instore: $('input[name="instore"]').val(),
		action: $('select[name="action"]').val(),
		staff: Object.keys($('input[name="staff"]').data() || {}).join(',')
    }, function(r) {
        page = 0;
        $('#res_count').text(r.res_count);
		if ($('#calendar > input[name="date"]').length) {
			$('.userList').removeClass($('#calendar > input[name="date"]').val() ? 'tbl' : '')
					.addClass($('#calendar > input[name="date"]').val() ? '' : 'tbl');
		}
		$('.userList').html(r.content);
        if (r.left_count) $('.btnLoad').removeClass('hdn');
        else $('.btnLoad').addClass('hdn')
		if ($('.filterCtnr').length) $('.filterCtnr').hide();
    }, 'json');
};

// Doload
var Doload = function(e) {
    page += 1;
    $.post(location.pathname, {
        query: query,
        page: page,
		event: $('select[name="searchSel"]').val() || '',
		object: $('select[name="object"]').val() || Object.keys($('input[name="object"]').data() || {}).join(','),
		date_start: $('#calendar > input[name="date"]').val(),
		date_finish: $('#calendar > input[name="fDate"]').val(),
		status: $('select[name="status"]').val(),
		type: $('select[name="type"]').val(),
		instore: $('input[name="instore"]').val(),
		action: $('select[name="action"]').val(),
		staff: Object.keys($('input[name="staff"]').data() || {}).join(',')
    }, function(r) {
        $('.userList').append(r.content);
        if (!r.left_count) $(e).addClass('hdn');
		$('#page').checkbox();
    }, 'json');
};

// Settings
var Settings = {
	openRefferal: function(id, name) {
		mdl.open({
            id: 'editGroup',
            width: 500,
            title: lang[153],
            content: '<div class="iGroup"><label>' + lang[154] + ':</label><input type="text" name="refName" value="' + (name || '') + '"></div>\
					<div class="sGroup"><button class="btn btnSubmit" type="submit" onclick="Settings.sendRefferal(' + id + ');"><span class="fa fa-save"></span> ' + (id ? lang[42] : lang[91]) + '</button></div>'
        });
	}, sendRefferal: function(id) {
		var data = {
            id: id
        }
        if (!$('input[name="refName"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
        } else {
            data.name = $('input[name="refName"]').val();
            $.post('/settings/send_refferal', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[155],
                        delay: 2
                    });
                    mdl.close('editGroup');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delRefferal: function(id) {
        $.post('/settings/del_refferal', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#refferal_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, privileges: function(name) {
        $('[name^="privileges"]').each(function(i, e) {
            $(e).attr('name', e.name.replace(/privileges\[(\w+)\]\[(\w+)\]/g, function(a, b) {
                return a.replace(b, name);
            }))
        });
        $.post('/settings/privileges', {
            group: name
        }, function(e) {
            $.each(e, function(a, b) {
                var el = $('[name="privileges[' + name + '][' + a + ']"]');
                el.find('option').prop('selected', false).trigger('change');
                if ($.isArray(b)) {
                    $.each(b, function(i, v) {
                        el.find('option[value="' + v + '"]').prop('selected', true).trigger('change');
                    });
                } else {
                    if (el.attr('type') == 'checkbox')
                        el.prop('checked', b == 1 ? true : false).val(b);
                    else
                        el.find('option[value="' + b + '"]').prop('selected', true).trigger('change');
                }
            });
        }, 'json');
    },
    save: function(f, event) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {},
            form = $(f).find('[name]'),
            err = null;
        for (var i = 0; i < form.length; i++) {
            data[form[i].name] = $(form[i]).val();
        }
        console.log(data);
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        $.post('/settings/save', data, function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[1],
                    delay: 2
                })
            } else if (r == 'RELOAD') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[1],
                    delay: 2
                });
                var time = setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    },
    tab: function(a, p) {
        location.hash = a + (p ? '#' + p : '');
        $('.uForm > .tab').hide();
        $('.uForm > .tab[id="' + a + '"]').show();
        $('#stitle').text(a);
        if (location.hash.match(/Groups/))
            $('.btnAddGr').show();
        else
            $('.btnAddGr').hide();
        if (a != 'general') $('#' + a).html('<div class="spnCtnr"><span class="fa fa-cog fa-spin fa-3x fa-fw"></span></div>').load(location.pathname + '/' + a.toLocaleLowerCase(), Page.init);
    },
    addGroup: function(id, name, pay, timer) {
        mdl.open({
            id: 'editGroup',
            width: 500,
            title: lang[144],
            content: '<div class="iGroup"><label>Group name:</label><input type="text" name="grName" value="' + (name || '') + '"></div>\
					<div class="iGroup"><label>Paying per hour:</label><input type="checkbox" name="pay" ' + (pay == 1 ? 'checked' : '') + '></div>\
					<div class="iGroup"><label>Timer:</label><input type="checkbox" name="timer" ' + (timer == 1 ? 'checked' : '') + '></div>\
					<div class="sGroup"><button class="btn btnSubmit" type="submit" onclick="Settings.sendGroup(' + id + ');"><span class="fa fa-save"></span> ' + (id ? lang[42] : lang[91]) + '</button></div>',
			cb: function() {
				$('#page').checkbox();
			}
        });
    },
    sendGroup: function(id) {
        var data = {
            id: id
        }
        if (!$('input[name="grName"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
        } else {
            data.name = $('input[name="grName"]').val();
            data.pay = $('input[name="pay"]').val();
            data.timer = $('input[name="timer"]').val();
            $.post('/settings/add_group', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[143],
                        delay: 2
                    });
                    mdl.close('editGroup');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delGroup: function(id) {
        $.post('/settings/del_group', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#group_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendForm: function(a, b, c) {
        b.preventDefault();
        loadBtn.start($(b.target).find('button[type="submit"]'));
        var data = {
                id: c
            },
            err = null;
        $.each($(a).find('[name]'), function() {
            var val = $(this).val();
            if (!val) {
                err = this.name + ' ' + lang[142];
                return false;
            }
            data[this.name] = val;
        });
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(b.target).find('button[type="submit"]'));
            return;
        }
        $.post('/settings/save_form', data, function(e) {
            if (e) Page.get('/settings/forms/edit/' + e);
            loadBtn.stop($(b.target).find('button[type="submit"]'));
        });
    },
    delForm: function(id) {
        $.post('/settings/del_form', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#form_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, sendPoints: function(el, event) {
		event.preventDefault();
		$.post('/settings/save_points', $(el).serializeArray(), function(r) {
			if (r == 'OK') {
				Page.get('/settings/points');
			} else {
				alr.show({
					class: 'alrShow',
					content: lang[13],
					delay: 2
				});
			}
		})
	}
};

// Webcam photo
var webcam = {
	width: 470,    
	height: 0,
  	streaming: false,
	video: null,
	canvas: null,
	photo: null,
	startbutton: null,
	startup: function() {
        webcam.video = document.getElementById('video');
        webcam.canvas = document.getElementById('canvas');
        webcam.photo = document.getElementById('photo');
        webcam.startbutton = document.getElementById('startbutton');

        navigator.getMedia = ( navigator.getUserMedia ||
                                navigator.webkitGetUserMedia ||
                                navigator.mozGetUserMedia ||
                                navigator.msGetUserMedia);

        navigator.getMedia({
                video: true,
                audio: false
            },
            function(stream) {
                if (navigator.mozGetUserMedia) {
                    webcam.video.mozSrcObject = stream;
                } else {
                    var vendorURL = window.URL || window.webkitURL;
                    webcam.video.src = vendorURL.createObjectURL(stream);
                }
                webcam.video.play();
            },
            function(err) {
                console.log("An error occured! " + err);
            }
        );

        webcam.video.addEventListener('canplay', function(ev){
            if (!webcam.streaming) {
                webcam.height = webcam.video.videoHeight / (webcam.video.videoWidth/webcam.width);
                
                if (isNaN(webcam.height)) {
                    webcam.height = webcam.width / (4/3);
                }
                
                webcam.video.setAttribute('width', webcam.width);
                webcam.video.setAttribute('height', webcam.height);
                webcam.canvas.setAttribute('width', webcam.width);
                webcam.canvas.setAttribute('height', webcam.height);
                webcam.streaming = true;
            }
        }, false);

        webcam.startbutton.addEventListener('click', function(ev){
            webcam.takepicture();
            ev.preventDefault();
        }, false);

        webcam.clearphoto();
    }, clearphoto: function () {
        var context = webcam.canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, webcam.canvas.width, webcam.canvas.height);

        var data = webcam.canvas.toDataURL('image/png');
        webcam.photo.setAttribute('src', data);
    }, takepicture: function () {
        var context = webcam.canvas.getContext('2d');
        if (webcam.width && webcam.height) {
            webcam.canvas.width = webcam.width;
            webcam.canvas.height = webcam.height;
            context.drawImage(webcam.video, 0, 0, webcam.width, webcam.height);

            var data = webcam.canvas.toDataURL('image/png');
            webcam.photo.setAttribute('class', '');
            webcam.photo.setAttribute('src', data);
        } else {
            webcam.clearphoto();
        }
    }
};

// Base64 to blob
function b64Blob(b64Data, contentType) {
	var byteCharacters = atob(b64Data);
	var byteNumbers = new Array(byteCharacters.length);
	for (var i = 0; i < byteCharacters.length; i++) {
		byteNumbers[i] = byteCharacters.charCodeAt(i);
	}
	var byteArray = new Uint8Array(byteNumbers);
	return new Blob([byteArray], {type: contentType});
}

// Users
var user = {
	createInvoice: function(id) {
		
	},
	playOnsite: function(id, a, os_id) {
		mdl.open({
				id: 'playService',
				title: 'Choose store',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
						class: 'iGroup',
						id: 'store',
						html: $('<label/>', {
							html: 'Store'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'store'
					})).append($('<ul/>', {
						class: 'hdn'
					}))
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit ac',
						type: 'button',
						onclick: 'user.updateOnsite(' + id + ', \'' + a + '\', ' + os_id + ');',
						html: 'Done'
					})
				}).append($('<button/>', {
						class: 'btn btnSubmit dc',
						type: 'button',
						onclick: 'mdl.close();',
						html: 'Cancel'
					}))),
				cb: function() {
					$.post('/users/objects', {}, function (r) {
						if (r) {
							var items = '', lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
								lId = v.id;
							});
							if (r.list.length > 1) {
								$('#store > ul').html(items).sForm({
									action: '/users/objects',
									data: {
										lId: lId,
										query: $('#store > .sfWrap input').val() || ''
									},
									all: false,
									select: $('input[name="store"]').data(),
									s: true
								}, $('input[name="store"]'));
							} else if (r.list.length == 1) {
								var cash_object = {};
								cash_object[r.list[0].id] = {
									name: r.list[0].name
								}
								$('input[name="store"]').data(cash_object);
								$('#store').append($('<div/>', {
									class: 'storeOne',
									html: r.list[0].name
								}));
							}
						}
					}, 'json');
				}
			});
	},
	updateOnsite: function(id, a, os_id) {
		var store = 0,
			err = null;
		if (a == 'play' && !Object.keys($('input[name="store"]').data()).join(',').length) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, select store',
				delay: 2
			});
		} else {
			if (a == 'play') store = Object.keys($('input[name="store"]').data()).join(',');
			$.post('/users/update_onsite', {
				id: id,
				action: a,
				onsite: os_id,
				store: store
			}, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'Status Updated',
						delay: 2
					});
					Page.get(location.href);
					mdl.close();
				} else if (r == 'no_store') {
					alr.show({
						class: 'alrDanger',
						content: 'Please, select store',
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	},
	addOnsite: function(id) {
		$.post('/inventory/AllOnsite', {}, function(r) {
			var items = '', lId = 0;
			if (r.list) {
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
					lId = v.id;
				});
			}
			mdl.open({
				id: 'addOnsite',
				title: 'On site service',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
						class: 'iGroup fw price',
						id: 'service',
						html: $('<label/>', {
							html: 'Service'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'service'
					})).append($('<ul/>', {
						class: 'hdn',
						html: items
					}))
				}).append($('<div/>', {
					class: 'iGroup fw dGroup ',
					html: $('<label/>', {
						html: 'Start date'
					})
				}).append($('<input/>', {
					type: 'text',
					class: 'cl',
					name: 'date_start',
					onclick: '$(this).next().show().parent().addClass(\'act\');'
				})).append($('<div/>', {
					id: 'sCalendar'
				}))).append($('<div/>', {
					class: 'iGroup fw dGroup ',
					html: $('<label/>', {
						html: 'End date'
					})
				}).append($('<input/>', {
					type: 'text',
					class: 'cl',				
					name: 'date_end',
					onclick: '$(this).next().show().parent().addClass(\'act\');'
				})).append($('<div/>', {
					id: 'eCalendar'
				}))).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'user.sendService(this, ' + id + ');',
						html: 'Done'
					})
				})),
				cb: function() {
					$('#sCalendar').calendar(function() {
						$('input[name="date_start"]').val($('#sCalendar > input[name="date"]').val());
						$('.dGroup > input + div').hide().parent().removeClass('act');
					});
					$('#eCalendar').calendar(function() {
						$('input[name="date_end"]').val($('#eCalendar > input[name="date"]').val());
						$('.dGroup > input + div').hide().parent().removeClass('act');
					});
					
					 $('.uForm').on('click', function(event) {
						if (!$(event.target).hasClass('cl')) {
							console.log($(event.target).hasClass('cl'));
							$('.calendar').hide().parents('.iGroup').removeClass('act');
						}
					});
					
					$('#service > ul').html(items).sForm({
						action: '/inventory/AllOnsite',
						data: {
							lId: lId,
							query: $('#service > .sfWrap input').val() || ''
						},
						all: false,
						select: $('input[name="service"]').data(),
						s: true
					}, $('input[name="service"]'));
				}
			});
		}, 'json');
	},
	sendService: function(el, id) {
		loadBtn.start($(el));
		var data = {
			id: id,
			date_start: $('input[name="date_start"]').val(),
			date_end: $('input[name="date_end"]').val()
		}, err = null;
		if (!Object.keys($('input[name="service"]').data()).join(',')) 
			err = 'Please, choose service';
		else 
			data.service = Object.keys($('input[name="service"]').data()).join(',');
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/users/send_service', data, function(r) {
				loadBtn.stop($(el));
				if(r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'The service was successfully added',
						delay: 2
					});
					Page.get(location.href);
					mdl.close();
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	},
	webPhoto: function() {
		mdl.open({
			id: 'webPhoto',
			title: 'Make webcam capture',
			content: $('<div/>', {
				html: $('<div/>', {
					class: 'camera',
					html: $('<video/>', {
						id: 'video'
					})
				}).append($('<div/>', {
					class: 'aCenter',
					html: $('<button/>', {
						id: 'startbutton',
						class: 'btn btnWeb',
						type: 'button',
						html: 'Make capture'
					})
				})).append($('<img/>', {
					id: 'photo',
					class: 'hdn'
				}))
			}).append($('<canvas/>', {
				id: 'canvas'
			})).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'user.accPhoto()',
					html: 'Use photo'
				})
			})),
			cb: webcam.startup
		});
	}, accPhoto: function() {
		$('.imgGroup > figure > img').attr('src', $('#photo').attr('src')).attr('new', 1);
		$('dropdown').val('');
		mdl.close();
	}, newUsr: function() {
		mdl.open({
			id: 'newUser',
			title: 'New User',
			content: '<form class="uForm" method="post" onsubmit="user.addShort(this, event);">\
				<input type="hidden" name="group_id[]" value="5">\
				<div class="iGroup usr">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name">\
				</div>\
				<div class="iGroup usr">\
					<label>' + lang[161] + '</label>\
					<input type="text" name="lastname">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[162] + '</label>\
					<input type="text" name="zipcode">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[163] + '</label>\
					<input type="email" name="email">\
				</div>\
				<div class="iGroup">\
					<label>' + lang[164] + '</label>\
					<input type="text" name="phone">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> ' + lang[28] + '</button>\
				</div>\
			</form>'
		});
	}, addShort: function(f, event) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'email') {
                if (!form[i].value.length || !is_valid.email(form[i].value)) {
                    err = form[i].value.length ? lang[2] : lang[3];
                    break;
                }
            } else if (form[i].name == 'phone') {
                if (!form[i].value.length || !is_valid.phone(form[i].value)) {
                    err = form[i].value.length ? lang[4] : lang[5];
                    break;
                }
            } else if ((form[i].name == 'name' || form[i].name == 'lastname') && !$(f).find('input[name="company"]').val()) {
                if (!is_valid.name(form[i].value) || !is_valid.name(form[i].value)) {
                    err = lang[6];
                    break;
                }
            } 
			data.append(form[i].name, form[i].value);
        }
		
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/users/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
				mdl.close();
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                
				var usr = {};
				usr[r] = {
				   name: $(f).find('input[name="name"]').val() + ' ' + $(f).find('input[name="lastname"]').val()
				}
				$('input[name="contact"]').data(usr);
				
				if($('#contact .sfWrap > div').length){
					$('#contact .sfWrap > div').find('i').triger('click');
				}
				$('#contact .sfWrap > div').prepend($('<span/>', {
				   class: 'nc',
				   id: r,
				   html: $(f).find('input[name="name"]').val() + ' ' + $(f).find('input[name="lastname"]').val()
				}).append($('<i/>')));
            } else if (r == 'err_email') {
				alr.show({
                    class: 'alrDanger',
                    content: lang[165],
                    delay: 2
                });
			} else if (r == 'Name_not_valid') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Name is incorrect',
                    delay: 2
                });
			} else if (r == 'Lastname_not_valid') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Lastname is incorrect',
                    delay: 2
                });
			} else if (r == 'err_login') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Login can not be empty',
                    delay: 2
                });
			} else if (r == 'err_image_type') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Image type is incorrect',
                    delay: 2
                });
			} else if (r == 'err_file_size') {
				alr.show({
                    class: 'alrDanger',
                    content: 'File is too large',
                    delay: 2
                });
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    }, comSelect: function(v) {
		if (v != 0) {
			console.log(v);
			$('.com').show()
			$('.usr').hide()
		} else {
			$('.com').hide()
			$('.usr').show()
		}
	}, add: function(f, event, id, steps) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'email') {
                if (!form[i].value.length || !is_valid.email(form[i].value)) {
                    err = form[i].value.length ? lang[2] : lang[3];
                    break;
                }
            } else if (form[i].name == 'phone') {
                if (!form[i].value.length) {
                    err = lang[4];
                    break;
                }
            } else if (form[i].name == 'name' && $(f).find('input[name="company"]').val() == 0) {
                if (!is_valid.name(form[i].value) || !form[i].value.length) {
                    err = lang[6];
                    break;
                }
            } else if (form[i].name == 'lastname' && $(f).find('input[name="company"]').val() == 0) {
                if (!is_valid.name(form[i].value) || !form[i].value.length) {
                    err = 'Lastname can not be empty';
                    break;
                }
            } else if (form[i].name == 'cname' && $('input[name="company"]').val() == 1) {
				if (!form[i].value.length) {
					err = 'company name can not be empty';
					break;
				} 
            } else if (form[i].name == 'password') {
                if (form[i].value.length > 0 && form[i].value != $('input[name="password2"]').val()) {
                    err = lang[7];
                    break;
                }
			} else if (form[i].name == 'referral') {
                if (form[i].value == 0) {
                    err = 'Please, choose referal';
                    break;
                }
            }
			if ($.inArray(form[i].name, ['country', 'state', 'city', 'contact']) >= 0) {
				data.append(form[i].name, Object.keys($('input[name="' + form[i].name + '"]').data()).join(','));
			} else
				data.append(form[i].name, form[i].value.trim());
        }
		
		if ($('.dragndrop').length){
			var files = $('.dragndrop')[0].files;
			if (!$.isEmptyObject(files)) {
				for (var n in files) {
					data.append('image', files[n]);
				}
			}
		}
		if ($('.imgGroup > figure > img').attr('new')) {
			var pUsr = $('.imgGroup > figure > img').attr('src');
			data.append('image', b64Blob(pUsr.split(',')[1], 'image/png'));
			data.append('blob', 1);
		}
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
		if ($('.dragndrop').length){
			var thumb = $('.dragndrop').prev();
			if (thumb[0].tagName != 'FIGURE' && id) data.append('del_image', true);
		}
		if (steps)
			data.append('steps', steps);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/users/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
				alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id)
                    Page.get(steps ? '/inventory/step/?user=' + r : '/users/view/' + r);
                else if(location.pathname.match(/\/users\/edit\/(.*)/i)){
					Page.get('/users/view/' + id);
				} else
                    Page.get('/inventory/step/?user=' + id);
				if ($('.proUser').attr('id') == 'u_' + r)
					$('li.user > a > u').html($('input[name="name"]').val().trim() + ' ' + $('input[name="lastname"]').val().trim());
			} else if (r == 'err_email') {
				alr.show({
					class: 'alrDanger',
					content: 'Email exists or incorrect',
					delay: 2
				});
			} else if (r == 'not_allowed') {
				alr.show({
					class: 'alrDanger',
					content: 'You are not allowed to do this',
					delay: 2
				});
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    },
    del: function(id) {
        $.post('/users/del', {
            id: id
        }, function(r) {
            if (r == 'OK'){
				if ($('#user_' + id).length) {
					$('#user_' + id).addClass('deleted')
						.find('a[href="javascript:user.del('+id+');"]')
						.attr('href', 'javascript:user.restore('+id+');')
						.html('<span class="fa fa-times"></span> Restore user');
				} else 
					Page.get(location.href);
			} else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, restore: function(id) {
        $.post('/users/restore', {
            id: id
        }, function(r) {
            if (r == 'OK'){
				if ($('#user_' + id).length) {
					$('#user_' + id).removeClass('deleted')
						.find('a[href="javascript:user.restore('+id+');"]')
						.attr('href', 'javascript:user.del('+id+');')
						.html('<span class="fa fa-times"></span>  Delete user');
				} else 
					Page.get(location.href);
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, sendDetails: function(e, ev) {
		ev.preventDefault();
		if (!$('textarea[name="emails"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: lang[141],
				delay: 2
			});
		} else {
			loadBtn.start($(ev.target).find('button[type="submit"]'));
			$.post('/users/sendDetails', {data: $('textarea[name="emails"]').val()}, function(r) {
				loadBtn.stop($(ev.target).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: lang[140],
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	}
}


// Objects
var objects = {
    add: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (form[i].name == 'email') {
                if (!form[i].value.length || !is_valid.email(form[i].value)) {
                    err = form[i].value.length ? lang[2] : lang[3];
                    break;
                }
            } else if (form[i].name == 'phone') {
                if (!form[i].value.length || !is_valid.phone(form[i].value)) {
                    err = form[i].value.length ? lang[4] : lang[5];
                    break;
                }
            } else if (form[i].name == 'name') {
                if (!form[i].value.length) {
                    err = lang[8];
                    break;
                }
            }
            data.append(form[i].name, form[i].value);
        }
        if (id) {
            data.append('managers', Object.keys($('input[name="managers"]').data()).join(','));
            data.append('staff', Object.keys($('input[name="staff"]').data()).join(','));
        }
        var files = $('.dragndrop')[0].files;
        if (!$.isEmptyObject(files)) {
            for (var n in files) {
                data.append('image', files[n]);
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        var thumb = $('.dragndrop').prev();
        if (thumb[0].tagName != 'FIGURE') data.append('del_image', true);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/objects/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id) Page.get('/objects/edit/' + r);
				else Page.get('/objects/');
            }
        });
    },
    sel: function(objId, g, t, d) {
        $.post('/users/all', {
            id: objId,
            gId: g,
            nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '" data-image="' + v.image + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_' + objId,
                    title: t,
                    content: '<ul class="hdn">' + items + '</ul>\
						<div class="sGroup"><button class="btn btnMdl" onclick="objects.get(\'' + d + '\', ' + objId + ');">' + lang[139] + '</button></div>',
                    cb: function() {
                        nIds = [];
                        $('#select_' + objId).find('ul').sForm({
                            action: '/users/all',
                            data: {
                                lId: lId,
                                id: objId,
                                gId: g,
                                query: $('#select_' + objId).find('input').val(),
                                nIds: Object.keys($('input[name="' + d + '"]').data()).join(',')
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="' + d + '"]').data()
                        }, $('input[name="' + d + '"]'));
                    }
                });
            }
        }, 'json');
    },
    get: function(el, id) {
        //$('#select_' + id).find('ul').sForm('get', $('input[name="' + el + '"]'));
        mdl.close();
        userAppend($('#managers'), $('input[name="managers"]').data());
        userAppend($('#staff'), $('input[name="staff"]').data());
    },
    del: function(id) {
        $.post('/objects/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#object_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    rent: function() {
        if ($('input[name="rent_cost"]').val().length)
            $('#rentTotal').text((parseFloat($('input[name="rent_cost"]').val()) + parseFloat($('input[name="rent_cost"]').val() * $('input[name="tax"]').val() / 100)).toFixed(2));
    },
    openLocation: function(id, n, o, c) {
		mdl.open({
			id: 'openLocation',
			title: (id ? lang[17] : lang[18]) + ' ' + lang[92],
			content: '<div class="iGroup">\
					<label>' + lang[20] + '</label>\
					<input type="text" name="name" value="' + (n || '') + '">\
				</div>\
				<div class="iGroup">\
					<label>Count</label>\
					<input type="text" name="count" value="' + (c || '') + '">\
				</div>\
				<div class="sGroup">\
					<button class="btn btnSubmit" onclick="objects.sendLocation(this, ' + (id || 0) + ', ' + (o || 0) + ')">' + (id ? lang[17] : lang[18]) + '</button>\
				</div>'
		});
    },
    sendLocation: function(f, id, o) {
        f = $(f).parents('.mdlBody');
        var data = {
                id: id
            },
            err = null;
        f.find('input').css('border-color', '#ddd');
        loadBtn.start(f.find('button[type="submit"]'));
        if (!f.find('input[name="name"]').val().length) {
            err = lang[25];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
            data.object = o;
            data.count = f.find('input[name="count"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop(s.find('button[type="submit"]'));
        } else {
            $.post('/objects/save_location', data, function(r) {
                loadBtn.stop(f.find('button[type="submit"]'));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
                    console.log('fd');
                    mdl.close('openLocation');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delLocation: function(id) {
        $.post('/objects/del_location', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#location_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	report: function(el, main) {
		loadBtn.start($(el));
		var data = {},
			err = null;
		if (!$('input[name="date"]').val() && !main) {
			err = 'Please, choose period';
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			if (!main) {
				data.objects = Object.keys($('input[name="object"]').data()).join(',');
				data.date_start = $('#calendar > input[name="date"]').val();
				data.date_finish = $('#calendar > input[name="fDate"]').val();
			}
			
			$.post('/objects/report_result', data, function(r) {
				loadBtn.stop($(el));
				if (r) {
					$('#report').html(r.report).append($('<div/>', {
						class: 'roTotal',
						html: 'Total: ' + r.total
					}));
				} else {
					$('#report').empty();
				}
				$('#page').select();
				$('#page').tabs();
			}, 'json');
		}
	}, 
	miniReport: function(el, v, id) {
		loadBtn.start($(el));
		var data = {
			object: id,
			date_start: $('#calendar > input[name="date"]').val(),
			date_finish: $('#calendar > input[name="fDate"]').val(),
			type: v,
			status: $('#eo_' + id + ' select[name="status"]').val()
		}
		
		$.post('/objects/mini_reports', data, function(r) {
			loadBtn.stop($(el));
			if (r) {
				if ($('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).length)
					$('#reps_' + id + ' .' + v + (v == 'status_issues' ? '_' + data.status : '')).replaceWith(r);
				else
					$('#reps_' + id).prepend(r);
			}
		});
	}
}

// Options
var options = {
    dragEl: {},
    parentEl: {},
    clickEl: {},
    add: function(e) {
        var select = new Object,
            opts = ['input', 'number', 'textarea', 'checkbox', 'select'];
        select = $('<select/>', {
            name: 'type'
        }).change(function() {
            if ($(this).val() == 'select') {
                $(this).parents('.optGroup').append($('<div/>', {
                    class: 'selArea',
                    html: $('<div/>', {
                        class: 'dClear'
                    })
                }).append($('<label/>', {
                    html: lang[137]
                })).append($('<input/>', {
                    type: 'checkbox',
                    name: 'sMul'
                })).append($('<div/>', {
                    class: 'dClear'
                })).append($('<div/>', {
                    class: 'selOpts',
                    html: $('<label/>', {
                        class: 'lTitle',
                        html: lang[136]
                    })
                })).append($('<div/>', {
                    class: 'aRight',
                    html: $('<button/>', {
                        class: 'btn atnAo',
                        onclick: 'options.addSel(this); return false;',
                        html: lang[135]
                    })
                }))).checkbox();
            } else {
                if ($(this).parents('.optGroup').find('.selArea')) {
                    $(this).parents('.optGroup').find('.selArea').remove()
                }
            }
        });
        $.each(opts, function(i, val) {
            select.append($('<option/>', {
                value: val,
                html: lang[val]
            }));
        });
        $(e).parent().before($('<div/>', {
            class: 'iGroup optGroup',
            html: $('<div/>', {
                class: 'sSide',
                html: $('<label/>', {
                    html: lang[134]
                })
            }).append($('<input/>', {
                name: 'oName'
            }))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: lang[127]
            })
        }).append(select)).append($('<div/>', {
            class: 'sSide rSide',
            html: $('<label/>', {
                html: lang[133]
            })
        }).append($('<input/>', {
            type: 'checkbox',
            name: 'req'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })).prepend($('<span/>', {
            class: 'fa fa-bars',
            draggable: true,
            ondragover: 'options.dragover(event)',
            ondragstart: 'options.dragstart(event)',
            ondragend: 'options.dragend(event)',
            onmousedown: '$(this).parent().addClass(\'drag\');',
            onmouseup: '$(this).parent().removeClass(\'drag\');'
        })));
        $('#page').select();
        $('#page').checkbox();
    },
    addSel: function(el) {
        $(el).parents('.selArea').find('.selOpts').append($('<div/>', {
            class: 'dClear',
            html: $('<div/>', {
                class: 'sSide w100',
                html: $('<input/>', {
                    name: 'opt',
                    placeholder: lang[132] + '...'
                })
            })
        }).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })).prepend($('<span/>', {
            class: 'fa fa-bars opt',
            draggable: true,
            ondragover: 'options.dragover(event)',
            ondragstart: 'options.dragstart(event)',
            ondragend: 'options.dragend(event)',
            onmousedown: '$(this).parent().addClass(\'drag\');',
            onmouseup: '$(this).parent().removeClass(\'drag\');'
        })));
    },
    dragstart: function(e) {
        options.clickEl = e.target;
        options.dragEl = e.target.parentNode;
        options.parentEl = options.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', options.dragEl.textContent);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        options.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== options.clickEl && target.className == options.clickEl.className) {
            if (target.parentNode.nextSibling == options.dragEl)
                options.parentEl.insertBefore(options.dragEl, target.parentNode);
            else
                options.parentEl.insertBefore(options.dragEl, target.parentNode.nextSibling);
        }
    }
}

// Inventory
var inventory = {
	delRequest: function(id) {
        $.post('/inventory/del_request', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#request_' + id).remove();
			else if (r == 'no_acc') {
                alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	requestConfirm: function(id) {
		$.post('/inventory/confirm_request', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Request successfully confirmed',
					delay: 2
				});
				$('#request_' + id).removeClass('not-confirmed').addClass('confirmed').find('.nIssue > a').remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	changeRequestType: function() {
		if ($('select[name="type_request"]').val() == 'service') 
			$('.typePrice').show();
		else
			$('.typePrice').hide().find('input').val(0);
	},
	addRequest: function(id, name, type, price) {
		mdl.open({
			id: 'addRequest',
			title: 'Add request',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Type'
					})
				}).append($('<select/>', {
					name: 'type_request',
					onchange: 'inventory.changeRequestType();',
					html: $('<option/>', {
						value: 'type',
						html: 'Type'
					})
				}).append($('<option/>', {
					value: 'brand',
					html: 'Brand'
				})).append($('<option/>', {
					value: 'service',
					html: 'Service'
				})))
			}).append($('<div/>', {
				class: 'iGroup',
				html: $('<label/>', {
					html: 'Name'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'name',
				value: name || ''
			}))).append($('<div/>', {
				class: 'iGroup typePrice',
				style: 'display: none;',
				html: $('<label/>', {
					html: 'Price'
				})
			}).append($('<input/>', {
				type: 'number',
				name: 'price',
				step: '0.1',
				min: '0',
				value: price || ''
			}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					html: 'Send',
					onclick: 'inventory.sendRequest(this' + (id ? ', ' + id : '') + ');'
				})
			})),
			cb: function() {
				$('#page').select();
				$('select[name="type_request"]').val(type || 'type').trigger('change');
			}
		});
	},
	sendRequest: function(el, id) {
		loadBtn.start($(el));
		var data = {
				id: id
			}, 
			err = null;
		
		$(el).parents('.uForm').find('input').css('border', '1px solid #ddd');
		if (!$('input[name="name"]').val()) {
			err = 'Please, enter name';
			$('input[name="name"]').css('border', '1px solid #f00');
		} else if ($('select[name="type_request"]').val() == 'service' && !$('input[name="price"]').val()) {
			err = 'Please, enter price';
			$('input[name="price"]').css('border', '1px solid #f00');
		} else {
			data.type = $('select[name="type_request"]').val();
			data.name = $('input[name="name"]').val();
			data.price = $('input[name="price"]').val();
		}
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			$.post('/inventory/send_request', data, function(r) {
				if (r == 'forbidden') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r == 'OK') {
					Page.get(location.href);
					mdl.close();
					alr.show({
						class: 'alrSuccess',
						content: 'Request was successfully ' + (id ? 'edited' : 'added'),
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(el));
			});
		}
	},
	addOnsite: function(el, event, id) {
		event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
		var data = {
				id: id
			}, 
			err = null,
			f = $(el);
			
		f.find('label').next().css('border', '1px solid #ddd');
		f.find('.iGroup').each(function(i, v) {
			if ($.inArray($(v).find('label').next().attr('name'), ['name', 'time', 'price']) >= 0 && !$(v).find('label').next().val()) {
				err = 'Please, enter ' + $(v).find('label').text();
				$(v).find('label').next().css('border', '1px solid #f00');
			} else
				data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
		});
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
			loadBtn.stop($(event.target).find('button[type="submit"]'));
		} else {
			$.post('/inventory/send_onsite', data, function(r) {
				if (r == 'forbidden') {
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to do that',
						delay: 2
					});
				} else if (r > 0) {
					Page.get('/inventory/onsite/edit/' + r);
					alr.show({
						class: 'alrSuccess',
						content: 'On site service was successfully ' + (id ? 'edited' : 'added'),
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
				loadBtn.stop($(event.target).find('button[type="submit"]'));
			});
		}
	},
	delOnsite: function(id) {
        $.post('/inventory/del_onsite', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#onsite_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	confirmOnsite: function(id, el) {
        $.post('/inventory/confirm_onsite', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Service successfully confirmed',
					delay: 2
				});
				$(el).parent().remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
    },
	confirmed: function(id, el) {
		$.post('/inventory/confirm', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Service successfully confirmed',
					delay: 2
				});
				if ($('#inventory_' + id).length)
					$('#inventory_' + id).removeClass('not-confirmed').find('.confBtn > a').remove();
				else 
					$(el).parent().remove();
			} else if (r == 'no_acc') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do that',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	addModel: function(id, n, p) {
        $.post('/inventory/allCategories', {
            id: id || '',
			type: 'inventory',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'add_model',
                    title: (id ? lang[17] : lang[18]) + ' Model',
                    content: '<form  method="post" onsubmit="inventory.sendModel(this, event, ' + (id || 0) + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup" id="brand">\
							<label>Brand</label>\
							<input type="hidden" name="brand">\
							<ul class="hdn">' + items + '</ul>\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        nIds = [];
                        $('input[name="brand"]').data(p);
                        $('#brand > ul').sForm({
                            action: '/inventory/allCategories',
                            data: {
                                lId: lId,
                                query: $('#brand > input').val(),
                                nIds: Object.keys(p || {}).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: p,
                            s: true
                        }, $('input[name="brand"]'));
                    }
                });
            }
        }, 'json');
    },
    sendModel: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null;
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        data.append('id', id);
        if (!form.find('input[name="name"]').val().length) {
            form.find('input[name="name"]').css('border', '1px solid #f00');
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        } else {
            data.append('name', form.find('input[name="name"]').val());
            data.append('brand', Object.keys(form.find('input[name="brand"]').data()).join(','));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/sendModel',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[8],
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close();
                }
            });
        }
    },
    delModel: function(id) {
        $.post('/inventory/delModel', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invModel_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
	newType: function() {
		mdl.open({
			id: 'newType',
			title: 'New Type',
			content: $('<div/>', {
				class: 'form',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'name'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'inventory.sendNewType(this);',
					html: 'Send'
				})
			}))
		})
	}, sendNewType: function(el) {
		var f = $(el).parents('.form');
		loadBtn.start($(el));
		if (!f.find('input[name="name"]').val()){
			alr.show({
				class: 'alrDanger',
				content: 'Name can not be empty',
				delay: 2
			});
		} else {
			$.post('/inventory/new_type', {name: f.find('input[name="name"]').val()}, function(r) {
				if (r > 0) {
				   $('select[name="type_id"]').append($('<option/>', {
					   value: r,
					   html: f.find('input[name="name"]').val()
				   }));
				   $('select[name="type_id"]').next().remove();
				   $('select[name="type_id"]').val(r);
				   $('#page').select();
				   mdl.close();
				}
			});
		}
	}, newBrand: function() {
		mdl.open({
			id: 'newBrand',
			title: 'New Brand',
			content: $('<div/>', {
				class: 'form',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Name'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'name'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					class: 'btn btnSubmit',
					type: 'button',
					onclick: 'inventory.sendNewBrand(this);',
					html: 'Send'
				})
			}))
		})
	}, sendNewBrand: function(el) {
		var f = $(el).parents('.form');
		loadBtn.start($(el));
		if (!f.find('input[name="name"]').val()){
			alr.show({
				class: 'alrDanger',
				content: 'Name can not be empty',
				delay: 2
			});
		} else {
			$.post('/inventory/new_brand', {name: f.find('input[name="name"]').val()}, function(r) {
				if (r > 0) {
				   var brand = {};
				   brand[r] = {
					   name: f.find('input[name="name"]').val()
				   }
				   $('input[name="category"]').data(brand);
				   $('#category .sfWrap > div').prepend($('<span/>', {
					   class: 'nc',
					   id: r,
					   html: f.find('input[name="name"]').val()
				   }).append($('<i/>')));
				   mdl.close();
				}
			});
		}
	}, dub: function(id) {
		$.post('/inventory/dub_service', {id: id}, function(r) {
			if (r > 0) {
				Page.get('/inventory/edit/service/' + r);
				alr.show({
					class: 'alrSuccess',
					content: 'Service was successfully dublicated',
					delay: 2
				})
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
    sendPriority: function(e) {
        var data = {};
        $('.sUser').each(function(i, v) {
            data[i] = {
                id: $(v).attr('id').replace('status_', ''),
                pr: $(v).find('input').val()
            }
        });
        if (data) {
            $.post('/inventory/stPriority', data, function(r) {
                //console.log(r);
            });
        }
    },
    getGroup: function(id, iId, f) {
		var items = '', lId = 0;
		$.post('/inventory/allModels', {
			type: Object.keys($('input[name="category"]').length ? $('input[name="category"]').data() : $('input[name="brand"]').data()).join(',')
		}, function (r) {
			if (r){
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				if (!f) {
					$('#model > input').removeData();
					$('#model > input').next().remove();
					$('#model').append($('<ul/>', {
						html: items
					}));
				}
				if (!brand_id && model_id) {
					$('#model > input').data(model_id);
					model_id = '';
				}
				$('#model > ul').sForm({
					action: '/inventory/allModels',
					data: {
						query: $('#model > .sfWrap input').val(),
						type: Object.keys($('input[name="category"]').length ? $('input[name="category"]').data() : $('input[name="brand"]').data()).join(',')
					},
					all: false,
					select: $('input[name="model"]').data(),
					s: true
				}, $('input[name="model"]'), null, {
					type: "category"
				})
			
				if (id) {
					$.post('/inventory', {
						type_id: id,
						inventory: iId
					}, function(r) {
						$('#forms').html(r);
						Page.init();
						if ($('.mdl').length) {
							p = ($(window).height() - $('.mdl').height()) / 2;
							$('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
						}
					}, 'json');
				}
			}
		}, 'json');
    },
    addGroup: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id,
                opts: {}
            },
            form = $(f),
            err = null;
        form.find('input').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data['name'] = form.find('input[name="name"]').val();
            /* data['type'] = form.find('select[name="type"]').val();
            data['brand'] = Object.keys(form.find('input[name="brand"]').data()).join(','); */
            form.find('.optGroup').each(function(i, val) {
                data.opts[i] = {
                    type: $(val).find('select[name="type"]').val(),
                    req: $(val).find('input[name="req"]').val()
                }
                if (!$(val).find('input[name="oName"]').val().length) {
                    err = lang[16];
                    $(val).find('input[name="oName"]').css('border-color', '#f00');
                } else
                    data.opts[i].name = $(val).find('input[name="oName"]').val();
                data.opts[i].id = $(val).find('input[name="oName"]').attr('data-id');
                if (data.opts[i].type == 'select') {
                    data.opts[i].mult = $(val).find('input[name="sMul"]').val();
                    data.opts[i].sOpts = {};
                    $(val).find('input[name="opt"]').each(function(sI, sVal) {
                        if (!$(sVal).val().length) {
                            err = lang[130];
                            $(sVal).css('border-color', '#f00');
                        } else
                            data.opts[i].sOpts[$(sVal).attr('data-id') || sI] = $(sVal).val();
                    });
                }
            });
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/send', data, function(res) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (res == 'ERROR') {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[131] + (res == 'SAVE' ? lang[24] : lang[23]),
                        delay: 2
                    });
                    Page.get('/inventory/types/edit/' + id);
                }
            });
        }
    },
    del: function(id) {
        $.post('/inventory/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
				if ($('#inventory_' + id).length)
					$('#inventory_' + id).remove();
				else 
					Page.get('/inventory');
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    delGroup: function (id) {
       $.post('/inventory/delGroup', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invGroup_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    sendCategory: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null;
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        data.append('id', id);
        if (!form.find('input[name="name"]').val().length) {
            form.find('input[name="name"]').css('border', '1px solid #f00');
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        } else {
            data.append('name', form.find('input[name="name"]').val());
            //data.append('type', form.find('select[name="type"]').val());
            //$('.mdl').find('ul').sForm('get', $('input[name="parent"]'));
            data.append('parent', Object.keys(form.find('input[name="parent"]').data()));
			//data.append('type', Object.keys(form.find('input[name="types"]').data()));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/sendCategory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[8],
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('select_par');
                }
            });
        }
    },
    delCategory: function(id) {
        $.post('/inventory/delCategory', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invCategory_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    selParent: function(t, d, id) {
        $.post('/inventory/allCategories', {
            nIds: Object.keys($('input[name="' + d + '"]').data()).join(','),
            id: id
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: t,
                    content: '<ul class="hdn">' + items + '</ul>\
						<div class="sGroup"><button class="btn btnMdl" onclick="inventory.getParent(\'' + d + '\');">' + lang[130] + '</button></div>',
                    cb: function() {
                        nIds = [];
                        $('#select_par').find('ul').sForm({
                            action: '/inventory/allCategories',
                            data: {
                                lId: lId,
                                query: $('#select_par').find('input').val(),
                                nIds: Object.keys($('input[name="' + d + '"]').data()).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="' + d + '"]').data(),
                            s: true
                        }, $('input[name="' + el + '"]'));
                    }
                });
            }
        }, 'json');
    },
    getParent: function(el) {
        //$('#select_par').find('ul').sForm('get', $('input[name="' + el + '"]'));
        mdl.close('select_par');
    },
    addCategory: function(id, n, p, t) {
        $.post('/inventory/allCategories', {
            id: id || '',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
				/* $.post('/inventory/allTypes', {
					id: id || '',
					nIds: Object.keys(p || {}).join(',')
				}, function(r) {
					if (r) {
						types = '';
						$.each(r.list, function(i, v) {
							types += '<li data-value="' + v.id + '">' + v.name + '</li>';
							lId = v.id;
						}); */
						mdl.open({
							id: 'select_par',
							title: (id ? lang[17] : lang[18]) + ' ' + lang[129],
							content: '<form  method="post" onsubmit="inventory.sendCategory(this, event, ' + (id || 0) + ');">\
								<div class="iGroup">\
									<label>' + lang[20] + '</label>\
									<input type="text" name="name" value="' + (n || '') + '">\
								</div>\
								<div class="iGroup" id="parent">\
									<input type="hidden" name="parent">\
									<div class="lbl">' + lang[128] + '</div>\
									<ul class="hdn">' + items + '</ul>\
								</div>\
								<div class="sGroup">\
									<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
								</div>\
							</form>',
							cb: function() {
								nIds = [];
								$('input[name="parent"]').data(p);
								$('#parent > ul').sForm({
									action: '/inventory/allCategories',
									data: {
										lId: lId,
										query: $('#parent > .sfWrap input').val() || '',
										nIds: Object.keys(p || {}).join(',') + ',' + id
									},
									all: (r.count <= 20) ? true : false,
									select: p,
									s: true
								}, $('input[name="parent"]'));
								
								/* $('input[name="types"]').data(t);
								$('#types > ul').sForm({
									action: '/inventory/allTypes',
									data: {
										lId: lId,
										query: $('#types > .sfWrap input').val() || '',
										nIds: Object.keys(p || {}).join(',') + ',' + id
									},
									all: (r.count <= 20) ? true : false,
									select: t,
									s: true
								}, $('input[name="types"]')); */
							}
						});
					/* }
				}, 'json'); */
		    }
        }, 'json');
    },
    openType: function(id, n, t, c, bId, bName) {
        $.post('/inventory/allCategories', {
            nIds: Object.keys(c || {}).join(','),
            type: t || ''
        }, function(r) {
            if (r) {
                var items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: (id ? lang[17] : lang[18]) + ' ' + lang[127],
                    content: '<form  method="post" onsubmit="inventory.sendType(this, event, ' + id + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        $('#page').select();
						if (bId) {
							var br = {};
							br[bId] = {
								name: bName
							}
							$('input[name="brand"]').data(br);
						}
						/* $.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data() || {}).join(',')}, function (r) {
							if (r){
								var items = '', lId = 0;
								$.each(r.list, function(i, v) {
									items += '<li data-value="' + v.id + '">' + v.name + '</li>';
									lId = v.id;
								});
								$('#brand > ul').html(items).sForm({
									action: '/inventory/allCategories',
									data: {
										lId: lId,
										nIds: Object.keys($('input[name="brand"]').data() || {}).join(','),
										query: $('#brand > .sfWrap input').val() || ''
									},
									all: (r.count <= 20) ? true : false,
									select: $('input[name="brand"]').data(),
									s: true
								}, $('input[name="brand"]'));
							}
						}, 'json'); */
                    }
                });
            }
        }, 'json');
    },
    sendType: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id,
                opts: {}
            },
            form = $(f),
            err = null;
        form.find('input').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data['name'] = form.find('input[name="name"]').val();
            data['type'] = form.find('select[name="type"]').val();
			//$('.catType ul').sForm('get', $('input[name="category"]'));
            //data['brand'] = Object.keys(form.find('input[name="brand"]').data()).join(',');
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/send', data, function(res) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (res == 'ERROR') {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[125] + (res == 'SAVE' ? lang[24] : lang[23]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('select_par');
                }
            });
        }
    },
    catChange: function(t, el) {
		if (t != 'service')
			$('#brand').show();
		else
			$('#brand').hide();
       /*  $.post('/inventory/allCategories', {
            type: t || ''
        }, function(r) {
            if (r) {
                var items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                el.find('input').val('').parent().find('.sfWrap').replaceWith($('<ul/>', {
                    class: 'hdn',
                    html: items
                }));
                el.find('ul').sForm({
                    action: '/inventory/allCategories',
                    data: {
                        type: t || '',
                        lId: lId,
                        query: $('#select_par').find('input').val()
                    },
                    all: (r.count <= 20) ? true : false,
                    s: true
                });
            }
        }, 'json'); */
    },
    addInv: function(f, event, id, n) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null,
			intype = location.pathname.indexOf('service') >= 0 ? 'service' : 'stock';
        data.append('id', id);
        
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if ((!form.find('input[name="category"]').length && !form.find('input[name="category"]').next().find('input').val()) && !form.find('input[name="name"]').length) {
            err = lang[124];
        } else if (!form.find('input[name="category"]').length && !form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else if (form.find('input[name="type_id"]').length && !Object.keys(form.find('input[name="type_id"]').data()).join(',')) {// && !form.find('input[name="type_id"]').next().find('input').val()
            err = 'Type can not be empty';
        } else if (form.find('input[name="category"]').length && !Object.keys(form.find('input[name="category"]').data()).join(',')) {// && !form.find('input[name="model"]').next().find('input').val()
            err = 'Brand can not be empty';
        } else if (form.find('input[name="model"]').length && !Object.keys(form.find('input[name="model"]').data()).join(',') && !form.find('input[name="model"]').next().find('input').val()) {
            err = 'Model can not be empty';
        } else if (!Object.keys(form.find('input[name="object"]').data()).join(',')) {
            err = 'Please, select store';
        } else if (form.find('input[name="location"]').length && !Object.keys(form.find('input[name="location"]').data()).join(',') && !form.find('input[name="location"]').next().find('input').val()) {
            err = 'Location can not be empty';
        } else if (form.find('select[name="sublocation"] > option[value="1"]').length && form.find('select[name="sublocation"]').val() == 0) {
            err = 'Sublocation can not be empty';
        } else {
            if ($(f).find('select[name="type_id"]').val() == 0) {
                err = lang[123];
            } else {
                /* $('form > div:not(.sTitle)').each(function(i, v) {
					if (!$(v).find('label').next().val() && $(v).find('label').next().attr('name')) 
						data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data() || {}).join(',');
					else if($(v).find('label').next().attr('name')) 
						data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
				}); */
                //data.customer = Object.keys(form.find('input[name="customer"]').data() || {}).join(',');
                if ($(f).find('input[name="name"]').val()) data.append('name', $(f).find('input[name="name"]').val());
                data.append('price', $(f).find('input[name="price"]').val());
                data.append('purchase-price', $(f).find('input[name="purchase-price"]').val());
                //if ($(f).find('select[name="type_id"]').val()) data.append('type', $(f).find('select[name="type_id"]').val());
                data.append('smodel', $(f).find('input[name="smodel"]').val() || '');
                data.append('os', $(f).find('select[name="os"]').val() || '');
                data.append('vendor', $(f).find('select[name="vendor"]').val() || '');
                data.append('owner_type', $(f).find('select[name="owner_type"]').val() || 'external');
                data.append('location_count', $(f).find('select[name="sublocation"]').val() || '');
                data.append('os_version', $(f).find('input[name="os_version"]').val() || '');
                data.append('serial', $(f).find('input[name="serial"]').val() || '');
                data.append('descr', $(f).find('textarea[name="descr"]').val() || '');
                data.append('purchace', $(f).find('input[name="purchace"]').val() || '');
                data.append('quantity', $(f).find('input[name="quantity"]').val() || '');
                data.append('main', $(f).find('input[name="main"]').val());
                data.append('charger', $(f).find('input[name="charger"]').val());
                data.append('opt_charger', $(f).find('input[name="opt_charger"]').val());
				
				data.append('category_new', $(f).find('input[name="category"]').next().find('input').val());
				data.append('category', Object.keys($(f).find('input[name="category"]').data() || {}).join(','));
				
				data.append('type_new', $(f).find('input[name="type_id"]').next().find('input').val());
				data.append('type', Object.keys($(f).find('input[name="type_id"]').data() || {}).join(','));
				
				data.append('model_new', $(f).find('input[name="model"]').next().find('input').val());
				data.append('model', Object.keys($(f).find('input[name="model"]').data() || {}).join(','));
				
				data.append('location_new', form.find('input[name="location"]').next().find('input').val());
				data.append('location', Object.keys(form.find('input[name="location"]').data() || {}).join(','));

                data.append('object', Object.keys(form.find('input[name="object"]').data() || {}).join(','));
                data.append('object_owner', Object.keys(form.find('input[name="object_owner"]').data() || {}).join(','));
                data.append('status', Object.keys(form.find('input[name="status"]').data() || {}).join(','));
                data.append('storeCat', Object.keys(form.find('input[name="storeCat"]').data() || {}).join(','));
                
                //data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                var options = {};
				if (n) {
					data.append('customer', form.find('input[name="customer"]').val());
					data.append('intype', 'stock');
				} else {
					data.append('customer', Object.keys(form.find('input[name="customer"]').data() || {}).join(','));
					if (location.pathname.indexOf('service') >= 0)
						data.append('intype', 'service');
					else
						data.append('intype', 'stock');
				}
				if (location.pathname.indexOf('service') >= 0) {
					i = 0;
					form.find('#steps > .iGroup').each(function(i, val) {
						if ($(val).find('input').val())
							options[i] = {
								id: $(val).find('input').attr('data-id') || i,
								name: $(val).find('input').val()
							}
						i ++;
					});
				} else {
					form.find('#forms > .iGroup').each(function(i, val) {
						if ($(val).find('label').next().val())
							options[$(val).find('label').next().attr('data-id')] = $(val).find('label').next().val();
					});
				}
                
                data.append('opts', JSON.stringify(options));
                if ($('.dragndrop').length) {
                    var files = $('.dragndrop')[0].files,
                        del = $('.dragndrop')[0].delete;
                    if (del) {
                        $.each(del, function() {
                            data.append('delete[]', this.split('thumb_')[1]);
                        });
                    }
                    if (!$.isEmptyObject(files)) {
                        for (var n in files) {
                            data.append('image[]', files[n]);
                        }
                    }
                }
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else if (r == 'cat_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Brand exists',
						delay: 2
					});
				} else if (r == 'tp_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Type exists',
						delay: 2
					});
				} else if (r == 'mdl_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Model exists',
						delay: 2
					});
				} else if (r == 'loc_exists') {
					alr.show({
						class: 'alrDanger',
						content: 'Locations exists. Check, please, maybe you can not use the location with current status',
						delay: 2
					});
				} else if (r == 'no_object') {
					alr.show({
						class: 'alrDanger',
						content: 'Please, select object',
						delay: 2
					});
				} else if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[122],
                        delay: 2
                    });
                    if (!id) {
					    if (location.pathname.indexOf('service') >= 0)
						    Page.get('/inventory/edit/service/' + r);
					    else {
							if ($_GET('backusr'))
								Page.get('/users/view/' + parseInt($_GET('backusr')));
							else
								Page.get(n ? '/issues/step/' + r : '/inventory/edit/' + r);
						}
				    } else if ($_GET('backusr'))
						Page.get('/users/view/' + parseInt($_GET('backusr')));
						
                }
            });
        }
    },
	changeStType: function(el) {
		console.log(el.value);
		if (el.value == 0) {
			console.log(1);
			$('#points').show();
			$('#addPoint').show();
			$('#minus').hide();
			$('input[name="forfeit"]').val(0);
		} else {
			$('#points').hide();
			$('#addPoint').hide();
			$('#minus').show();
		}
	},
    openStatus: function(id, data, name, forfeit, sms, smsForm) {
		$.post('/settings/allForms', {}, function(r) {
			forms = '';
			if (r) {
				$.each(r.list, function(i, v) {
					forms += '<option value="' + v.id + '" ' + (smsForm == v.id ? 'selected' : '') + '>' + v.name + '</option>';
				});
			}
			mdl.open({
				id: 'openStatus',
				title: (id ? lang[17] : lang[18]) + lang[121],
				cb: function() {
					if (smsForm) $('select[name="smForm"]').val(smsForm);
					$('#page').select();
					$('#page').checkbox();
				},
				content: $('<div/>', {
					html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: lang[20]
						})
					}).append($('<input/>', {
						type: 'text',
						name: 'name',
						value: name || ''
					}))
				}).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: lang[120]
					})
				}).append($('<input/>', {
					type: 'checkbox',
					onchange: 'inventory.changeStType(this);',
					name: 'fine',
					value: (forfeit ? 1 : 0)
				}))).append($('<div/>', {
					id: 'points',
					class: forfeit ? 'hdn' : ''
				})).append($('<div/>', {
					id: 'minus',
					class: forfeit ? '' : 'hdn',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<input/>', {
							type: 'text',
							name: 'forfeit',
							value: forfeit || '',
							placeholder: lang[120]
						})
					})
				})).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Send sms'
					})
				}).append($('<input/>', {
					type: 'checkbox',
					name: 'sms',
					onchange: 'inventory.changeStSms(this);',
					value: (sms ? 1 : 0)
				}))).append($('<div/>', {
					id: 'sms',
					class: sms ? '' : 'hdn',
					html: $('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Form'
						})
					}).append($('<select/>', {
							name: 'smsForm',
							html: forms
						}))
				})).append($('<div/>', {
					class: 'iGroup sGroup',
					id: 'addPoint',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						onclick: 'inventory.addPoint(this)',
						html: lang[119]
					})
				})).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit',
						onclick: 'inventory.sendStatus(this, ' + id + ')',
						html: id ? lang[113] : lang[91]
					})
				}))
			});
			
			if (data) {
				if (sms == 1) $('input[name="sms"]').trigger('click');
				if (forfeit) $('input[name="fine"]').trigger('click');
				$.each(data, function(i, v) {
					$('#points').html($('<div/>', {
						class: 'iGroup pGroup',
						html: $('<div/>', {
							class: 'sSide',
							html: $('<label/>', {
								html: lang[116]
							})
						}).append($('<input/>', {
							type: 'number',
							min: 0,
							name: 'time',
							value: i
						}))
					}).append($('<div/>', {
						class: 'sSide',
						html: $('<label/>', {
							html: lang[115]
						})
					}).append($('<input/>', {
						type: 'number',
						min: 0,
						name: 'points',
						value: v
					}))).append($('<span/>', {
						class: 'fa fa-times',
						onclick: '$(this).parent().remove();'
					})))
				})
				p = ($(window).height() - $('.mdl').height()) / 2;
				$('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
			}
		}, 'json')
    },
	changeStSms: function(e) {
		if ($(e).val() > 0) 
			$('#sms').removeClass('hdn');
		else 
			$('#sms').addClass('hdn');
	},
    sendStatus: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = lang[16];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
			data.forfeit = f.find('input[name="forfeit"]').val();
			data.sms = f.find('input[name="sms"]').val();
			data.smsForm = f.find('select[name="smsForm"]').val();
            data.pointGroup = {};
            f.find('.pGroup').each(function(i, v) {
                if (!$(v).find('input[name="time"]').val().length) {
                    err = lang[119];
                    $(v).find('input[name="time"]').css('border-color', '#f00');
                } else if (!$(v).find('input[name="points"]').val().length) {
                    err = lang[118];
                    $(v).find('input[name="points"]').css('border-color', '#f00');
                } else {
                    data.pointGroup[$(v).find('input[name="time"]').val()] = $(v).find('input[name="points"]').val();
                }
            });
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/sendStatus', data, function(r) {
                loadBtn.stop($(this));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
                    mdl.close('openStatus');
                    Page.get(location.href);
                }
            }, 'json');
        }
    },
    delStatus: function(id) {
        if (id) {
            $.post('/inventory/delStatus', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#status_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
    statusHis: function(id) {
        $.post('/inventory/status_history', {id: id}, function(r) {
			if (r) {
				var items = '';
				$.each(r.list, function(i, v) {
					items += '<div class="iGroup stGroup"><label>' + v.status + '</label><label>' + v.name + ' ' + v.lastname + '</label><label>' + v.date + '</label></div>';
				});
				mdl.open({
					id: 'statusHis',
					width: 800,
					title: lang[117],
					content: items
				})
			} 
		}, 'json'); 
    },
    addPoint: function(el) {
        e = $(el).parent();
        $('#points').append($('<div/>', {
            class: 'iGroup pGroup',
            html: $('<div/>', {
                class: 'sSide',
                html: $('<label/>', {
                    html: lang[116]
                })
            }).append($('<input/>', {
                type: 'number',
                min: 0,
                name: 'time'
            }))
        }).append($('<div/>', {
            class: 'sSide',
            html: $('<label/>', {
                html: lang[115]
            })
        }).append($('<input/>', {
            type: 'number',
            min: 0,
            name: 'points'
        }))).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
        p = ($(window).height() - $('.mdl').height()) / 2;
        $('.mdlWrap').css('padding-top', (p < 20 ? 20 : p) + 'px');
    },
    addLocation: function(el) {
        $(el).parent().before($('<div/>', {
            class: 'iGroup lGroup',
            html: $('<input/>', {
                type: 'text',
                name: 'location',
                placeholder: lang[92]
            })
        }).append($('<span/>', {
            class: 'fa fa-times',
            onclick: '$(this).parent().remove();'
        })));
    },
    sendLocation: function(f, event, id) {
        event.preventDefault();
        var data = {
                id: id
            },
            err = null;
        $(f).find('input').css('border-color', '#ddd');
        loadBtn.start($(event.target).find('button[type="submit"]'));
        data.loc = Object.keys($('input[name="location"]').data() || {}).join(',');
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/inventory/location', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[114],
                        delay: 2
                    });
					Page.get('/inventory/types/status');
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    openOS: function(id, name) {
        mdl.open({
            id: 'openOS',
            title: (id ? lang[17] : lang[18]) + ' OS',
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[20]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'name',
                    value: name || ''
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendOS(this, ' + id + ')',
                    html: id ? lang[113] : lang[91]
                })
            }))
        });
    },
    sendOS: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = 'Name can not be empty';
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/save_os', data, function(r) {
                loadBtn.stop($(this));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[112],
                        delay: 2
                    });
                    mdl.close('openOS');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delOS: function(id) {
        if (id) {
            $.post('/inventory/del_os', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#os_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
	openVendor: function(id, name) {
        mdl.open({
            id: 'openVendor',
            title: (id ? lang[17] : lang[18]) + ' Vendor',
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[20]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'name',
                    value: name || ''
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendVendor(this, ' + id + ')',
                    html: id ? lang[113] : lang[91]
                })
            }))
        });
    },
    sendVendor: function(el, id) {
        var f = $(el).parents('.mdlBody'),
            data = {},
            err = null;
        if (id) data.id = id;
        f.find('input').css('border-color', '#ddd');
        if (!f.find('input[name="name"]').val().length) {
            err = 'Name can not be empty';
            f.find('input[name="name"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
            $.post('/inventory/sendVendor', data, function(r) {
                loadBtn.stop($(this));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: 'Vendor was successfully saved',
                        delay: 2
                    });
                    mdl.close('openVendor');
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delVendor: function(id) {
        if (id) {
            $.post('/inventory/delVendor', {
                id: id
            }, function(r) {
                if (r == 'OK')
                    $('#vendor_' + id).remove();
                else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[9],
                        delay: 2
                    })
                }
            })
        } else
            $(el).parents('.sUser').remove();
    },
    toObject: function(id) {
        mdl.open({
            id: 'openMove',
            title: lang[111],
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[110]
                    })
                }).append($('<input/>', {
                    type: 'number',
                    min: '0',
                    step: '0.1',
                    name: 'purchase'
                }))
            }).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[109]
                })
            }).append($('<input/>', {
                type: 'number',
                name: 'sale',
                min: '0',
                step: '0.1'
            }))).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    onclick: 'inventory.sendToObject(this, ' + id + ')',
                    html: lang[91]
                })
            }))
        });
    },
	tradeConfirm: function(id, p) {
		mdl.open({
			id: 'confirmTradein',
			title: 'Tradein confirmation',
			content: $('<div/>', {
				class: 'uForm',
				html: $('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Price'
					})
				}).append($('<input/>', {
					type: 'number',
					step: '0.1',
					min: '0',
					name: 'price',
					value: p
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					onclick: 'inventory.sendTradeConf(this, ' + id + ')',
					html: 'Confirm'
				})
			}))
		})
	},
	sendTradeConf: function(el, id) {
		loadBtn.start($(el));
		var data = {
			id: id,
			price: 0
		}
		if (!$(el).parents('.uForm').find('input[name="price"]').val()) {
			alr.show({
				class: 'alrDanger',
				content: 'Please, enter price',
				delay: 2
			});
			loadBtn.stop($(el));
		} else {
			data.price = $(el).parents('.uForm').find('input[name="price"]').val();
			$.post('/inventory/confirm_tradein', data, function(r) {
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: 'You successfully confirmed tradein',
						delay: 2
					});
					Page.get(location.href);
					loadBtn.start($(el));
					mdl.close();
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
					loadBtn.stop($(el));
				}
			});
		}
	},
    sendToObject: function(el, id) {
        var form = $(el).parents('.form');
        if (!form.find('input[name="purchase"]').val().length) {
            alr.show({
                class: 'alrDanger',
                content: lang[108],
                delay: 2
            });
            form.find('input[name="purchase"]').css('border-color', '#f00');
        } else {

            if (!form.find('input[name="sale"]').val()) {
                mdl.open({
                    id: 'openSend',
                    title: 'Warning',
                    content: $('<div/>', {
                        class: 'form',
                        html: $('<div/>', {
                            class: 'warning',
                            html: lang[107]
                        })
                    }).append($('<div/>', {
                        class: 'sGroup',
                        html: $('<button/>', {
                            class: 'btn btnSubmit',
                            onclick: 'inventory.contToObject(' + id + ')',
                            html: lang[106]
                        })
                    }).append($('<button/>', {
                        class: 'btn btnSubmit red',
                        onclick: 'mdl.close("openSend")',
                        html: lang[105]
                    })))
                });
            } else {
                inventory.contToObject(id);
            }
        }
    },
    contToObject: function(id) {
        var data = {
            id: id,
            purchase: $('#openMove').find('input[name="purchase"]').val(),
            sale: $('#openMove').find('input[name="sale"]').val(),
			object: Object.keys($('input[name="object"]').data()).join(',')
        }
        $.post('/inventory/to_object', data, function(r) {
            if (r == 'OK') {
                /* 				alr.show({
                					class: 'alrSuccess',
                					content: 'Inventory was successfully added to object',
                					delay: 2
                				}); */
                // Create invoice
                var iData = {};
                iData.customer = Object.keys($('input[name="customer"]').data()).join(',');
                iData.object = Object.keys($('input[name="object"]').data()).join(',');
                iData.inventory = id;
                iData.subtotal = -parseFloat($('input[name="purchase"]').val());
                iData.tax = 0;
                iData.total = iData.subtotal;
                iData.paid = 0;
                iData.purchase = 1;

                $.post('/invoices/send', iData, function(r) {
                    if (r > 0) {
                        alr.show({
                            class: 'alrSuccess',
                            content: lang[58],
                            delay: 2
                        });
                        mdl.close('openMove');
                        Page.get('/invoices/view/' + r);
                    } else {
                        alr.show({
                            class: 'alrDanger',
                            content: lang[13],
                            delay: 2
                        });
                    }
                });

            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    },
    toStore: function(id) {
        $.post('/inventory/to_store', {
            id: id
        }, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[104],
                    delay: 2
                });
                Page.get(location.href);
                mdl.close("openMove");
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    }
}

// Orders
var purchases = {
	confirm: function(id) {
		$.post('/purchases/confirm_purchase', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Purchase was successfully confirmed',
					delay: 2
				});
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	changePrice: function() {
		price = $('.uForm').find('input[name="price"]').val().replace(/\D(\.?)\D/ig, '');
		$('input[name="total"]').val(price * $('input[name="quantity"]').val());
		$('input[name="proceeds"]').val(parseFloat($('input[name="sale"]').val()) - parseFloat($('input[name="total"]').val()));
	}, getLink: function(el) {
        $.post('/purchases/ebay', {
            url: el.value
        }, function(r) {
            switch (r) {
                case 'ERR':
                    alr.show({
                        class: 'alrDanger',
                        content:  lang[13],
                        delay: 2
                    });
                    break;

                case 'NOP':
                    alr.show({
                        class: 'alrDanger',
                        content: lang[103],
                        delay: 2
                    });
                    break;

                default:
                    $(r).find('#itemTitle').find('span').remove();
                    $(el).parents('.uForm').find('input[name="name"]').val($(r).find('#itemTitle').text().replace('Details about', '').trim());
                    $(el).parents('.uForm').find('input[name="price"]').val($(r).find('#prcIsum').text().trim());
                    $(el).parents('.uForm').find('input[name="quantity"]').val(1);
                    $(el).parents('.uForm').find('input[name="total"]').val($(r).find('#prcIsum').text().trim().replace(/\D(\.?)\D/ig, ''));
                    $(el).parents('.uForm').find('input[name="estimated"]').val($(r).find('#delSummary .vi-acc-del-range > b').text().trim() ? 'Between ' + $(r).find('#delSummary .vi-acc-del-range > b').text().trim() : '');
                    if ($(r).find('#icImg').attr('src').length) {
                        $(el).parents('.uForm').find('.imgGroup').removeClass('hdn').find('img').attr('src', $(r).find('#icImg').attr('src').split('?')[0]);
                    }
                    break;
            }
        }, 'html');
    },
    send: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).serializeArray(),
            err = null;
        data.append('id', id);
        for (var i = 0; i < form.length; i++) {
            if (!(form[i].value.length || $('input[name="' + form[i].name + '"]').data()) && $.inArray(form[i].name, ['comment', 'customer', 'tracking', 'estimated']) < 0 && form[i].name.length) {
                err = lang[101] + form[i].name + lang[102];
                $('input[name="' + form[i].name + '"]').css('border-color', '#f00');
                break;
            }
            if ($.inArray(form[i].name, ['customer', 'object']) >= 0)
                data.append(form[i].name, Object.keys($('input[name="' + form[i].name + '"]').data() || {}));
            else if (form[i].name == 'price')
				data.append(form[i].name, $('[name="price"]').val().replace(/(\D*)/i, ''));
            else
                data.append(form[i].name, form[i].value);
        }
        if ($('.imgGroup img').attr('src'))
            data.append('photo', $('.imgGroup img').attr('src'));
        else
            data.append('del_photo', true);
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        }
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            url: '/purchases/send',
            data: data
        }).done(function(r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id) Page.get('/purchases/edit/' + r);
            }
        });
    },
    del: function(id) {
        $.post('/purchases/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#purchase_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        });
    }
}

function userAppend(el, data) {
    el.empty();
    $.each(data, function(i, v) {
        el.append($('<li/>', {
            'class': 'hnt hntTop',
            'data-title': v.name,
            'html': $('<a/>', {
                href: '/users/views/' + i,
                html: $('<img/>', {
                    src: v.photo ? '/uploads/images/users/' + i + '/thumb_' + v.photo : '/templates/admin/img/userDef.jpg',
                    onclick: 'showPhoto(this.src);'
                }),
                onclick: 'return false;'
            })
        }));
    });
}

var data;

// Issues
var issues = {
	editDiscount: function(is, el, id, c) {
		$.post('/users/is_admin', {}, function(u) {
			adm = 0;
			if (u == 'OK') adm = 1;
			$.post('/invoices/allDiscounts', {}, function(r) {
				var items = '<option value="0">Not selected</option>';
				$.each(r.list, function(i, v) {
					items += '<option value="' + v.id + '"' + (v.id == id ? ' selected' : '') + '>' + v.name + '</option>';
				});
				mdl.open({
					id: 'discount_mdl',
					title: 'Select discount',
					content: $('<div/>', {
						class: 'uForm',
						html: $('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: 'Discount'
							})
						}).append($('<select/>', {
							name: 'discount_id',
							html: items
						}))
					}).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Reason'
						})
					}).append($('<textarea/>', {
						name: 'reason',
						html: $('#discount_reason').val()
					}))).append($('<div/>', {
						class: 'sGroup',
						html: adm && ! c ? $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit left',
							onclick: 'issues.confirmDiscount(' + is + ');',
							html: 'Confirm'
						}) : ''
					}).append($('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							onclick: 'issues.sendDiscount(' + is + ');',
							html: 'Save'
						}))),
					cb: function() {
						$('#page').select();
					}
				})
			}, 'json');
		});
	},
	confirmDiscount: function(id) {
		$.post('/issues/send_field', {
			id: id,
			field: 'discount_confirmed',
			value: 1,
			full: 1
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount successfully confirmed',
					delay: 2
				});
				mdl.close();
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	sendDiscount: function(is) {
		$.post('/issues/send_discount', {
			id: is,
			discount: $('select[name="discount_id"]').val(),
			reason: $('textarea[name="reason"]').val()
		}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount successfully updated',
					delay: 2
				});
				mdl.close();
				Page.get(location.href);
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	},
	newInv: function() {
		$.post('/inventory/allOS', {}, function(r) {
			if (r) {
				var os = '';
				$.each(r.list, function(i, v) {
					os += '<option value="' + v.id + '">' + v.name + '</option>';
				});
				/* $.post('/inventory/allCategories', {}, function(r) {
					var cats = '';
					$.each(r.list, function(i, v) {
						cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
					});
						$.post('/inventory/allTypes', {
							name: 1
						}, function(r) {
							var types = '';
							$.each(r.list, function(i, v) {
								types += '<li data-value="' + v.id + '">' + v.name + '</li>';
							}); */
						
						/* mdl.open({
							id: 'createDevice',
							title: 'Add device',
							content: $('<div/>', {
								class: 'form',
								html: $('<div/>', {
								class: 'iGroup',
									id: 'type_id',
									html: $('<label/>', {
										html: 'Type'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'type_id'
								})).append($('<ul/>', {
									class: 'hdn',
									html: ''
								}))
								}).append($('<div/>', {
									class: 'iGroup',
									id: 'brand',
									html: $('<label/>', {
										html: 'Brand'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'brand'
								})).append($('<ul/>', {
									class: 'hdn',
									html: ''
								}))).append($('<div/>', {
								class: 'iGroup',
									id: 'model',
									html: $('<label/>', {
										html: 'Model'
									})
								}).append($('<input/>', {
									type: 'hidden',
									name: 'model'
								})).append($('<ul/>', {
									class: 'hdn'
								}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Model Specification'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'smodel'
							}))).append($('<div/>', {
								class: 'iGroup',
								id: 'os',
								html: $('<label/>', {
									html: 'OS'
								})
							}).append($('<select/>', {
								name: 'os',
								html: os
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'OS version'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'os_ver'
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Serial Number'
								})
							}).append($('<input/>', {
								type: 'text',
								name: 'sn'
							}))).append($('<div/>', {
								class: 'iGroup',
								html: $('<label/>', {
									html: 'Price'
								})
							}).append($('<input/>', {
								type: 'number',
								min: '0',
								step: '0.1',
								name: 'price'
							}))).append($('<div/>', {
								class: 'sGroup',
								html: $('<button/>', {
									type: 'button',
									class: 'btn btnSubmit',
									onclick: 'issues.sendDev(this, event);',
									html: 'Send'
								})
							})),
							cb: function() {
								var lId = 0;
								$('#page').select();
								
								getTypes(1);
								getBrands(1);
								inventory.getGroup(0,0,1);
							}
						}); */
				/* }, 'json');
			}, 'json'); */
			}
		}, 'json');
		
	}, newServ: function() {
		/* $.post('/inventory/allCategories', {}, function(r) {
			var cats = '';
			$.each(r.list, function(i, v) {
				cats += '<li data-value="' + v.id + '">' + v.name + '</li>';
			});
				$.post('/inventory/allTypes', {
					name: 1
				}, function(r) {
					var types = '';
					$.each(r.list, function(i, v) {
						types += '<li data-value="' + v.id + '">' + v.name + '</li>';
					}); */
			
			/* 			mdl.open({
							id: 'createDevice',
							title: 'Add service',
							content: $('<div/>', {
								class: 'form',
								html: $('<div/>', {
									class: 'iGroup',
									html: $('<label/>', {
										html: 'Name'
									})
								}).append($('<input/>', {
									type: 'text',
									name: 'name'
							}))}).append($('<div/>', {
									class: 'iGroup',
									html: $('<label/>', {
										html: 'Price'
									})
								}).append($('<input/>', {
									type: 'number',
									min: '0',
									step: '0.1',
									name: 'price'
								}))).append($('<div/>', {
									class: 'sGroup',
									html: $('<button/>', {
										type: 'button',
										class: 'btn btnSubmit',
										onclick: 'issues.sendServ(this, event);',
										html: 'Send'
									})
							}))
						}); */
				/* }, 'json');
		}, 'json'); */
		
	}, sendDev: function(f, event) {
		event.preventDefault();
        loadBtn.start($(f).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).parents('.form'),
            err = null;
       
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if (!Object.keys(form.find('input[name="brand"]').data()).join(',') && !form.find('input[name="brand"]').next().find('input').val().length) {
            err = lang[124];
        } else if (!Object.keys(form.find('input[name="type_id"]').data()).join(',') && !form.find('input[name="type_id"]').next().find('input').val().length) {
            err = 'Select type';
        } else if (!Object.keys(form.find('input[name="model"]').data()).join(',') && !form.find('input[name="model"]').next().find('input').val().length) {
            err = 'Select model';
        } else if (!form.find('input[name="brand"]').length && !form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            if (form.find('select[name="type"]').val() == 0) {
                err = lang[123];
            } else {
                if (form.find('input[name="name"]').val()) data.append('name', form.find('input[name="name"]').val());
                data.append('price', form.find('input[name="price"]').val());
                
                data.append('smodel', form.find('input[name="smodel"]').val() || '');
                data.append('os', form.find('select[name="os"]').val() || '');
                data.append('os_version', form.find('input[name="os_ver"]').val() || '');
                data.append('serial', form.find('input[name="sn"]').val() || '');
                data.append('intype', 'stock');
				
				data.append('type', Object.keys(form.find('input[name="type_id"]').data() || {}).join(','));
				data.append('type_new', form.find('input[name="type_id"]').next().find('input').val());
				
				data.append('category', Object.keys(form.find('input[name="brand"]').data() || {}).join(','));
				data.append('category_new', form.find('input[name="brand"]').next().find('input').val());
				
                data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                data.append('model_new', form.find('input[name="model"]').next().find('input').val());
				
				data.append('exist_inventory', $('input[name="inventory_id"]').val());
				
				if ($('input[name="issue_id"]').length) data.append('issue_id', $('input[name="issue_id"]').val());
			}
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_short_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(f).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    
					if (r == 'no_obj') {
						alr.show({
							class: 'alrDanger',
							content: 'You issue device is not in store. Or you are not logged on store. Fix it and try again.',
							delay: 2
						});
					} else if (r == 'cat_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Brand exists',
							delay: 2
						});
					} else if (r == 'tp_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Type exists',
							delay: 2
						});
					} else if (r == 'mdl_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Model exists',
							delay: 2
						});
					} else if (r == 'loc_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Locations exists',
							delay: 2
						});
					} else {
						r = JSON.parse(r);
						if (r.id > 0) {
							var inv = {};
							inv[r.id] = {
							   name: r.name,
							   price: form.find('input[name="price"]').val()
							}
							$('input[name="inventory"]').data(inv);
							$('#inventory .sfWrap > div').prepend($('<span/>', {
							   class: 'nc',
							   id: r.id,
							   'data-price': form.find('input[name="price"]').val(),
							   html: r.name
							}).append($('<i/>')));
							mdl.close('createDevice');

							alr.show({
								class: 'alrSuccess',
								content: lang[122],
								delay: 2
							});
						}
					}
						
                }
            });
        }
	}, sendServ: function(f, event) {
		event.preventDefault();
        loadBtn.start($(f).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f).parents('.form'),
            err = null;
       
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        form.find('.sWrap').css('border', '1px solid #ddd');
        if (!form.find('input[name="name"]').val().length) {
            err = lang[16];
            form.find('input[name="name"]').css('border-color', '#f00');
        } else {
            if (form.find('select[name="type"]').val() == 0) {
                err = lang[123];
            } else {
                if (form.find('input[name="name"]').val()) data.append('name', form.find('input[name="name"]').val());
                data.append('price', form.find('input[name="price"]').val());
				data.append('intype', 'service');
				
				data.append('type', Object.keys(form.find('input[name="type_id"]').data() || {}).join(','));
				data.append('type_new', form.find('input[name="type_id"]').next().find('input').val());
				
				data.append('category', Object.keys(form.find('input[name="brand"]').data() || {}).join(','));
				data.append('category_new', form.find('input[name="brand"]').next().find('input').val());
				
                data.append('model', Object.keys(form.find('input[name="model"]').data() || {}).join(','));
                data.append('model_new', form.find('input[name="model"]').next().find('input').val());
			}
        }
		form.find('#forms > .iGroup').each(function(i, val) {
			if ($(val).find('label').next().val())
				options[$(val).find('label').next().attr('data-id')] = $(val).find('label').next().val();
		});
		data.append('opts', JSON.stringify(options));
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(f).find('button[type="submit"]'));
        } else {
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/inventory/send_inventory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(f).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[122],
                        delay: 2
                    });
					if (r == 'cat_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Brand exists',
							delay: 2
						});
					} else if (r == 'tp_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Type exists',
							delay: 2
						});
					} else if (r == 'mdl_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Model exists',
							delay: 2
						});
					} else if (r == 'loc_exists') {
						alr.show({
							class: 'alrDanger',
							content: 'Locations exists',
							delay: 2
						});
					} else if (r > 0) {
					   var inv = {};
					   inv[r] = {
						   name: form.find('input[name="name"]').val(),
						   price: form.find('input[name="price"]').val()
					   }
					   $('input[name="service"]').data(inv);
					   $('#service .sfWrap > div').prepend($('<span/>', {
						   class: 'nc',
						   id: r,
						   'data-price': form.find('input[name="price"]').val(),
						   html: form.find('input[name="name"]').val()
					   }).append($('<i/>')));
					   mdl.close('createDevice');
					}
						
                }
            });
        }
	}, updateStatus: function(id) {
		/* $.post('/inventory/get_sublocations', {
			location: $('.devLocation').attr('data-id'),
			inventory: $('input[name="inventory_id"]').val()
		}, function(r) {
			if (r > 0) location_count = r;
			else location_count = 0; */
			mdl.open({
				id: 'stUpdate',
				title: 'Update issue',
				content: $('<div/>', {
					class: 'uForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						id: 'status',
						html: $('<label/>', {
							html: 'Status'
						})
					}).append($('<input/>', {
						type: 'hidden',
						name: 'status'
					})).append($('<ul/>', {
						class: 'hdn'
					}))
				}).append($('<div/>', {
					class: 'iGroup fw',
					id: 'location',
					html: $('<label/>', {
						html: 'Location'
					})
				}).append($('<input/>', {
					type: 'hidden',
					name: 'location'
				})).append($('<ul/>', {
					class: 'hdn'
				}))).append($('<div/>', {
					class: 'iGroup fw',
					id: 'sublocation',
					html: $('<label/>', {
						html: 'Sublocation'
					})
				}).append($('<select/>', {
					name: 'sublocation'
				}))).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'Comment'
					})
				}).append($('<textarea/>', {
					name: 'descr',
					html: $('.descrIssue').text()
				}))).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Important note'
					})
				}).append($('<input/>', {
					type: 'checkbox',
					name: 'important',
					checked: $('.descrIssue').hasClass('important')
				}))).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						onclick: 'issues.sendUpdate(' + id + ', this)',
						html: 'Send'
					})
				}).append($('<button/>', {
					class: 'btn btnPickup',
					type: 'button',
					onclick: 'issues.pickUp(' + id + ', this)',
					html: 'Pick up'
				}))),
				cb: function() {
					$('#page').checkbox();
					$('#page').select();
					var st = {},
						lc = {};
					location_count = $('.devLocation').attr('location_count');
					if ($('.devStatus').attr('data-id') != 0) st[$('.devStatus').attr('data-id')] = {name: $('.devStatus').text().replace('Status: ', '')};
					if ($('.devLocation').text().replace('Location: ', '').length) {
						lc[$('.devLocation').attr('data-id')] = {
							name: $('.devLocation').text().replace('Location: ', ''),
							count: $('.devLocation').attr('data-count')
						};
					}
					$('input[name="status"]').data(st);
					if ($('.devLocation').text().replace('Location: ', '').length) {
						location_id = lc;
						object_id  = $('.devLocation').attr('data-object');
					}
					$.post('/inventory/allStatuses', {nIds: Object.keys($('input[name="status"]').data()).join(',')}, function (r) {
						if (r){
							var items = '',
							lId = 0;
							$.each(r.list, function(i, v) {
								items += '<li data-value="' + v.id + '">' + v.name + '</li>';
								lId = v.id;
							});
							$('#status > ul').html(items).sForm({
								action: '/inventory/allStatuses',
								data: {
									lId: lId,
									nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
									query: $('#status > .sfWrap input').val() || ''
								},
								all: (r.count <= 20) ? true : false,
								select: $('input[name="status"]').data(),
								s: true
							}, $('input[name="status"]'), getLocation);
						}
					}, 'json');
					//getLocation(1);
				}
			});
		//});
	}, sendUpdate: function(id, el) {
        loadBtn.start($(el).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(el).parents('.uForm'),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if ($(v).find('label').next().attr('name') == 'descr' && !$(v).find('label').next().val()) {
                err = lang[100];
			} else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().find('option[value="1"]').length && $(v).find('label').next().val() == '0') {
				err = 'Sublocation can not be empty';
            } else if ($(v).find('label').next().attr('name') == 'descr' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else if ($(v).find('label').next().attr('name') == 'sublocation' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else {
                data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data()).join(',');
            }
        });
		data['inventory_id'] = $('input[name="inventory_id"]').val();
		data['important'] = $('input[name="important"]').val();
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el).find('button[type="submit"]'));
        } else {
            $.post('/issues/send_update', data, function(r) {
                loadBtn.stop($(el).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get(location.href);
					mdl.close();
                } else {
					alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
				}
            });
        }
    }, pickUp: function(id, el) {
        loadBtn.start($(el).find('button[type="submit"]'));
        var data = {
                id: id,
				inventory_id: $('input[name="inventory_id"]').val()
            },
            form = $(el).parents('.uForm'),
            err = null;
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el).find('button[type="submit"]'));
        } else {
            $.post('/issues/pickup', data, function(r) {
                loadBtn.stop($(el).find('button[type="submit"]'));
                if (r == 'OK') {
					alr.show({
                        class: 'alrSuccess',
                        content: 'Device was successfully picked up',
                        delay: 2
                    });
                    Page.get('/issues/view/' + id);
					mdl.close();
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
	DoloadStats: function(e, id) {
		page_stats += 1;
		$.post('/issues/doload_stats', {
			page: page_stats,
			id: id
		}, function(r) {
			$('#ChangeLog .stats_tbl').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	},
    add: function(f, event, id, i) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                inventory_id: i,
				id: id || 0
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if ($(v).find('label').next().attr('name') == 'descr' && !$(v).find('label').next().val()) {
                err = lang[100];
            } else if ($(v).find('label').next().attr('name') == 'location' && !Object.keys($(v).find('label').next().data()).join(',').length) {
                err = 'Location can not be empty';
            } else if ($(v).find('label').next().attr('name') == 'descr' && $(v).find('label').next().val()) {
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            } else {
                data[$(v).find('label').next().attr('name')] = Object.keys($(v).find('label').next().data()).join(',');
            }
            data['inventory_id'] = $('input[name="inventory_id"]').val();
            data['location_count'] = $('select[name="sublocation"]').val();
        });
		if (form.find('select[name="sublocation"] > option[value="1"]').length && form.find('select[name="sublocation"]').val() == 0)
                err = 'Subocation can not be empty';
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/issues/send', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get('/issues/view/' + r);
                }
            });
        }
    },
    del: function(id) {
        $.post('/issues/del', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#issue_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    addIssue: function(id, name, device, descr, st, lc, iv, sr) {
        mdl.open({
            id: 'addIssue',
            title: lang[99],
            content: $('<form/>', {
                method: 'post',
                onsubmit: 'issues.add(this, event,' + id + ');',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[98]
                    })
                }).append($('<label/>', {
                    class: 'mdlInfo',
                    html: name
                }))
            }).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[97]
                })
            }).append($('<label/>', {
                class: 'mdlInfo',
                html: device
            }))).append($('<div/>', {
                class: 'iGroup',
                html: $('<label/>', {
                    html: lang[96]
                })
            }).append($('<textarea/>', {
                name: 'descr',
                html: id ? $('#issue_' + id + ' > .isDescr').text().trim() : ''
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'service',
                html: $('<label/>', {
                    html: lang[95]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'service'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'inventory',
                html: $('<label/>', {
                    html: lang[94]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'inventory'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'status',
                html: $('<label/>', {
                    html: lang[93]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'status'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'iGroup sfGroup',
                id: 'location',
                html: $('<label/>', {
                    html: lang[92]
                })
            }).append($('<input/>', {
                type: 'hidden',
                name: 'location'
            })).append($('<ul/>', {
                class: 'hdn'
            }))).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'submit',
                    html: lang[91]
                })
            }).append($('<button/>', {
                class: 'btn btnPurchase',
                html: lang[90],
                onclick: 'issues.makePur(); return false;'
            }))),
            cb: function() {
                if (id) {
                    /* $('input[name="status"]').data(st);
					$('input[name="location"]').data(lc);
					$('input[name="inventory"]').data(iv);
					$('input[name="service"]').data(sr); */
                    getLocation();
                }
                $.post('/inventory/allStatuses', {
                    nIds: Object.keys($('input[name="status"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#status > ul').html(items).sForm({
                            action: '/inventory/allStatuses',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="status"]').data() || {}).join(','),
                                query: $('#status > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="status"]').data(),
                            s: true
                        }, $('input[name="status"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    type: 'stock',
                    nIds: Object.keys($('input[name="inventory"]').data()).join(',') + (id ? ', ' + id : '')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#inventory > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                type: 'stock',
                                lId: lId,
                                nIds: Object.keys($('input[name="inventory"]').data() || {}).join(',') + (id ? ',' + id : ''),
                                query: $('#inventory > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="inventory"]').data()
                        }, $('input[name="inventory"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    type: 'service',
                    nIds: Object.keys($('input[name="service"]').data()).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#service > ul').html(items).sForm({
                            action: '/objects/all',
                            data: {
                                type: 'service',
                                lId: lId,
                                nIds: Object.keys($('input[name="service"]').data() || {}).join(','),
                                query: $('#service > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="service"]').data()
                        }, $('input[name="service"]'));
                    }
                }, 'json');
            }
        });
    },
    toggleList: function(el, ev) {
        if (!$(ev.target).parents('.td').hasClass('w100') && !$(ev.target).parents('.td').hasClass('w5'))
            $(el).toggleClass('open').next().slideToggle();
    },
    addPur: function(id, pIds, cid) {
        mdl.open({
            id: 'addPurchase',
            title: lang[89],
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup sfGroup fw price',
                    id: 'purchase',
                    html: $('<label/>', {
                        html: lang[66]
                    })
                }).append($('<input/>', {
                    type: 'hidden',
                    name: 'purchase'
                })).append($('<ul/>'))
            }).append($('<div/>', {
				class: 'iGroup fw',
				html: $('<label/>', {
					html: 'New purchase'
				})
			}).append($('<input/>', {
				type: 'text',
				name: 'new_purchase',
				onblur: 'issues.newPur(this)'
			}))).append($('<div/>')).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendPur(this, ' + id + ', \'' + pIds + '\', \'' + cid + '\');',
                    html: lang[42]
                })
            })),
            cb: function() {
                $.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchase"]').data()).join(',') + pIds
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="' + v.price + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#purchase > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                nIds: Object.keys($('input[name="purchase"]').data() || {}).join(',') + pIds,
                                query: $('#purchase > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="purchase"]').data()
                        }, $('input[name="purchase"]'));
                    }
                }, 'json');
            }
        });
    },
	newPur: function(el) {
		$(el).parent().next().empty();
		$.post('/purchases/ebay', {
            url: el.value
        }, function(r) {
            switch (r) {
                case 'ERR':
                    alr.show({
                        class: 'alrDanger',
                        content:  lang[13],
                        delay: 2
                    });
                    break;

                case 'NOP':
                    alr.show({
                        class: 'alrDanger',
                        content: lang[103],
                        delay: 2
                    });
                    break;

                default:
                    $(r).find('#itemTitle').find('span').remove();
					
					$(el).parent().next().append($('<div/>', {
						class: 'pnThumb',
						html: $(r).find('#icImg').attr('src').length ? $('<img/>', {
							src: $(r).find('#icImg').attr('src')
						}) : ''
					})).append($('<div/>', {
						class: 'pNewText',
						html: $('<div/>', {
							class: 'pnName',
							html: $(r).find('#itemTitle').text().replace('Details about', '').trim()
						})
					})).append($('<div/>', {
						class: 'pnPrice',
						html: $(r).find('#prcIsum').text().trim()
					})).append($('<input/>', {
						type: 'number',
						class: 'npPrice',
						name: 'price',
						step: '0.1',
						min: '0',
						placeholder: 'Custom price'
					}));
                    break;
            }
        }, 'html');
	},
    sendPur: function(f, id, cid) {
        form = $(f).parent().parent().parent();
       /*  var data = {
                id: id,
                field: 'purchase_ids',
                value: {},
                prices: {}
            }, */
		var data = new FormData(),
            err = null;
		data.append('id', id);
		data.append('field', 'purchase_ids');
        loadBtn.start($(f));
        if (!form.find('input[name="purchase"]').data() && !form.find('input[name="new_purchase"]').data())
            err = lang[88];
        else if (form.find('input[name="purchase"]').data()) {
            /* data.value = Object.keys(form.find('input[name="purchase"]').data()).join(',') ? Object.keys(form.find('input[name="purchase"]').data()).join(',') + ',' : '';
            $.each(form.find('input[name="purchase"]').data(), function(i, v) {
                data.prices[i] = v.price;
            });
			if (form.find('input[name="new_purchase"]').data()) {
				data.cid = cid;
				data.name = $('.pnName').text();
				data.photo = $('#addPurchase .pnThumb > img').attr('src');
				data.price = $('.pnPrice').text().replace(/(\D*)/i, '');
				data.cprice = $('input[name="price"]').val() || 0;
				data.link = $('input[name="new_purchase"]').val();
			} */
			data.append('value', Object.keys(form.find('input[name="purchase"]').data()).join(',') ? Object.keys(form.find('input[name="purchase"]').data()).join(',') + ',' : '');
			var prices = {};
			$.each(form.find('input[name="purchase"]').data(), function(i, v) {
                prices[i] = v.price;
            });
			data.append('prices', prices);
			if (form.find('input[name="new_purchase"]').data()) {
				data.append('cid', cid);
				data.append('name', $('.pnName').text());
				data.append('photo', $('#addPurchase .pnThumb > img').attr('src'));
				data.append('price', $('.pnPrice').text().replace(/(\D*)/i, ''));
				data.append('cprice', $('input[name="price"]').val() || 0);
				data.append('link', $('input[name="new_purchase"]').val());
			}
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            //$.post('/issues/send_field', data, function(r) {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/issues/send_field',
				data: data
			}).done(function(r) {
                loadBtn.stop($(event.target));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[87] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('addPurchase');
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    addInv: function(id, sIds, type, devId) {
        name = (type == 'stock' ? lang[85] : lang[86]);
        mdl.open({
            id: 'addService',
            title: lang[84] + (devId ? lang[85] : lang[86]),
            content: $('<div/>', {
                html: $('<div/>', {
                    class: 'iGroup sfGroup fw price',
                    id: name,
                    html: $('<label/>', {
                        html: devId ? lang[82] : lang[83]
                    })
                }).append($('<input/>', {
                    type: 'hidden',
                    name: name
                })).append($('<ul/>'))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendInv(this, ' + id + ', \'' + (devId ? 'stock' : 'service') + '\', \'' + sIds + '\');',
                    html: lang[42]
                })
            })),
            cb: function() {
                $.post('/inventory/all', {
                    type: type,
                    noCust: 1,
                    nIds: (type != 'service' ? (Object.keys($('input[name="' + name + '"]').data()).join(',') + sIds) : '') + (devId ? ',' + devId : '')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
                            lId = v.id;
                        });
                        $('#' + name + ' > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                type: type,
                                lId: lId,
                                nIds: (type != 'service' ? (Object.keys($('input[name="' + name + '"]').data()).join(',') + sIds) : '') + (devId ? ',' + devId : ''),
                                query: $('#' + name + ' > .sfWrap input').val() || ''
                            },
                            all: false,
                            select: (type != 'service' ? Object.keys($('input[name="' + name + '"]').data()).join(',') : '')
                        }, $('input[name="' + name + '"]'));
                    }
                }, 'json');
            }
        });
    },
    sendInv: function(f, id, type) {
        form = $(f).parent().parent().parent();
        var data = {
                id: id,
                field: (type == 'stock' ? 'inventory_ids' : 'service_ids'),
                value: {}
            },
            err = null,
            name = (type == 'stock' ? 'inventory' : 'service');
        loadBtn.start($(f));
        if (!form.find('input[name="' + name + '"]').data())
            err = lang[81];
        else { 
            data.value = Object.keys(form.find('input[name="' + name + '"]').data()).join(',') + ',';
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_field', data, function(r) {
                loadBtn.stop($(event.target));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[74] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('addService');
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    delInv: function(id, type, issue) {
        var data = {
            id: issue,
            field: (type == 'stock' ? 'inventory_ids' : 'service_ids'),
            value: id,
            del: true
        };
        $.post('/issues/send_field', data, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[74] + (id ? lang[23] : lang[24]),
                    delay: 2
                });
                if (type == 'service') {
                    $('#service_com_' + id).remove();
                    $('#sSteps_' + id).remove();
                    $('#miniService_' + id).remove();
                }
                $('#' + (type == 'stock' ? 'inventory_' : 'service_') + id).remove();
				total.fTotal();
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    },
    addStep: function(el, id){
        var data = {
                id: id,
                sId: $(el).parent().parent().attr('id').replace('sSteps_', ''),
                value: $(el).attr('id'),
                del: $(el).hasClass('done') ? 1 : 0
            },
            serv = $(el).parents('.servSteps').find('.servTitle');
        $.post('/issues/send_step', data, function(r) {
            if (r != 'OK') {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            } else {
                $(el).toggleClass('done');
                serv.find('.num').text($(el).hasClass('done') ? (parseInt(serv.find('.num').text()) + 1) : (parseInt(serv.find('.num').text()) - 1));
                serv.find('.number').text((parseInt(serv.find('.num').text()) / parseInt(serv.find('.full').text()) * 100).toFixed(0));
            }
        })
    },
    editDoit: function(id, el) {
        $('#doit').hide().after($('<input/>', {
            type: 'number',
			min: 0,
            name: 'doit',
            value: parseInt($('#doit').text())
        }));
        $('#doitBtn').removeClass('fa-pencil').addClass('fa-check').parent().attr('href', 'javascript:issues.sendDoit(' + id + ')');
    },
    editQuote: function(id, el) {
        $('#quote').hide().after($('<input/>', {
            type: 'number',
			min: 0,
            name: 'quote',
            value: parseInt($('#quote').text())
        }));
        $('#quoteBtn').removeClass('fa-pencil').addClass('fa-check').parent().attr('href', 'javascript:issues.sendQuote(' + id + ')');
    },
    sendDoit: function(id, el) {
        var data = {
                id: id,
                field: 'doit',
                value: 0,
                full: 1
            },
            err = null;
        if (!$('input[name="doit"]').val()) {
            err = lang[80];
            $('input[name="doit"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="doit"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_field', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $('#doit').text('$' + parseFloat(data.value).toFixed(2)).show().next().remove();
                    $('#doitBtn').removeClass('fa-check').addClass('fa-pencil').parent().attr('href', 'javascript:issues.editDoit(' + id + ')');
                }
            });
        }
    },
    sendQuote: function(id, el) {
        var data = {
                id: id,
                field: 'quote',
                value: 0,
                full: 1
            },
            err = null;
        if (!$('input[name="quote"]').val()) {
            err = lang[80];
            $('input[name="quote"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="quote"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_field', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $('#quote').text('$' + parseFloat(data.value).toFixed(2)).show().next().remove();
                    $('#quoteBtn').removeClass('fa-check').addClass('fa-pencil').parent().attr('href', 'javascript:issues.editQuote(' + id + ')');
                }
            });
        }
    },
    addComment: function(s, i) {
        mdl.open({
            id: 'addComment',
            title: lang[79],
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'iGroup fw',
                    html: $('<textarea/>', {
                        id: 'comment',
                        html: $('#service_com_' + s + ' > span').text()
                    })
                })
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    class: 'btn btnSubmit',
                    type: 'button',
                    onclick: 'issues.sendComment(\'' + s + '\', ' + i + ');',
                    html: lang[79]
                })
            }))
        })
    },
    sendComment: function(s, i) {
        var data = {
            id: i,
            sId: s,
            value: $('#comment').val(),
            del: 0
        };
        $.post('/issues/send_comment', data, function(r) {
            if (r != 'OK') {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            } else {
                Page.get(location.href);
				mdl.close();
            }
        })
    },
    addNote: function(id) {
        var data = {
                id: id
            },
            err = null;
        $('textarea[name="note"]').css('border-color', '#ddd');
        if (!$('textarea[name="note"]').val()) {
            $('textarea[name="note"]').css('border-color', '#f00');
            alr.show({
                class: 'alrDanger',
                content: lang[78],
                delay: 2
            });
        } else {
            data.note = $('textarea[name="note"]').val();
            $.post('/issues/send_note', data, function(r) {
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[77],
                        delay: 2
                    });
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    pPrice: function(el, id, iss) {
        console.log(parseFloat($(el).prev().text()));
        $(el).prev().hide().after($('<input/>', {
            type: 'number',
            name: 'pPrice',
            step: '0.1',
            min: '0',
            value: parseFloat($(el).prev().text())
        }));
        $(el).removeClass('fa-pencil').addClass('fa-check').attr('onclick', 'issues.sendPPrice(this, ' + id + ', ' + iss + ')');
    },
    sendPPrice: function(el, id, iss) {
        var data = {
                id: iss,
                value: 0,
                pur: id
            },
            err = null;
        if (!$('input[name="pPrice"]').val()) {
            err = lang[76];
            $('input[name="pPrice"]').css('border-color', '#f00');
        } else {
            data.value = $('input[name="pPrice"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target));
        } else {
            $.post('/issues/send_price', data, function(r) {
                loadBtn.stop($(event.target));
                if (!r) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[75],
                        delay: 2
                    });
                    $(el).prev().prev().text(data.value).show().next().remove();
                    $(el).removeClass('fa-check').addClass('fa-pencil').attr('onclick', 'issues.pPrice(this, ' + id + ', ' + iss + ')');
                }
            });
        }
    },
    delPur: function(id, issue) {
        var data = {
            id: issue,
            field: 'purchase_ids',
            value: id,
            del: true
        };
        $.post('/issues/send_field', data, function(r) {
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[74] + (id ? lang[23] : lang[24]),
                    delay: 2
                });
                $('#purchase_' + id).remove();
				total.fTotal();
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    }
}
var tax = 0;
// Invoices
var invoices = {
    openDiscount: function(id, n, p, c) {
		$.post('/users/is_admin', {}, function(r){
			mdl.open({
				id: 'openDiscount',
				title: (id ? lang[17] : lang[18]) + ' Discount',
				content: '<div class="iGroup">\
							<label>' + lang[73] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup">\
							<label>' + lang[72] + '</label>\
							<input type="number" name="percent" value="' + (p || 0) + '" step="0.1" min=""0>\
						</div>\
						<div class="sGroup">\
							' + (($('#discount_' + id).hasClass('not-confirmed') && r == 'OK') ? '<button class="btn btnSubmit left" onclick="invoices.confirmDiscount(this, ' + (id || 0) + ')">Confirm</button>' : '') + '\
							<button class="btn btnSubmit" onclick="invoices.sendDiscount(this, ' + (id || 0) + ')">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>',
			});
		});
    },
	confirmDiscount: function(el, id) {
		loadBtn.start($(el));
		$.post('/invoices/confirm_discount', {id: id}, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Discount was successfully confirmed', 
					delay: 2
				});
				$('#discount_' + id).removeClass('not-confirmed').addClass('confirmed');
				mdl.close();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13], 
					delay: 2
				});
			}
		});
	},
    sendDiscount: function(f, id) {
        f = $(f).parents('.mdlBody');
        var data = {
                id: id
            },
            err = null;
        f.find('input').css('border-color', '#ddd');
        loadBtn.start(f.find('button[type="submit"]'));
        if (!f.find('input[name="name"]').val()) {
            err = lang[25];
            f.find('input[name="name"]').css('border-color', '#f00');
        } else if (!f.find('input[name="percent"]').val()) {
            err = lang[25];
            f.find('input[name="percent"]').css('border-color', '#f00');
        } else {
            data.name = f.find('input[name="name"]').val();
            data.percent = f.find('input[name="percent"]').val();
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop(f.find('button[type="submit"]'));
        } else {
            $.post('/invoices/send_discount', data, function(r) {
                loadBtn.stop(f.find('button[type="submit"]'));
                if (r) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[71],
                        delay: 2
                    });
                    mdl.close('openDiscount');
                    $('.userList').prepend($('<div/>', {
                        class: 'sUser ' + r.confirmed,
                        id: 'discount_' + r.id,
                        html: $('<a/>', {
                            class: 'uInfo',
                            html: data.name
                        })
                    }).append($('<div/>', {
                        class: 'pInfo',
                        html: data.percent
                    })).append($('<div/>', {
                        class: 'uMore',
                        html: $('<span/>', {
                            class: 'fa fa-ellipsis-h',
                            onclick: '$(this).next().toggle(0)'
                        })
                    }).append($('<ul/>', {
                        html: $('<li>', {
                            html: $('<a/>', {
                                href: 'javascript:invoices.openDiscount(' + r.id + ', "' + data.name + '", ' + data.percent + ')',
                                html: $('<span/>', {
                                    class: 'fa fa-pencil'
                                })
                            }).append(lang[70])
                        })
                    }).append($('<li>', {
                        html: $('<a/>', {
                            href: 'javascript:invoices.delDiscount(' + r.id + ')',
                            html: $('<span/>', {
                                class: 'fa fa-times'
                            })
                        }).append(lang[69])
                    })))));
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            }, 'json');
        }
    },
    delDiscount: function(id) {
        $.post('/invoices/del_discount', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#discount_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    addInventory: function() {
        mdl.open({
            id: 'addInv',
            title: lang[18],
            content: $('<div/>', {
                class: 'form',
                html: $('<div/>', {
                    class: 'tabs',
                    html: $('<div/>', {
                        class: 'tab',
                        'data-title': lang[68],
                        id: 'inv',
                        html: $('<div/>', {
                            id: 'inventory',
                            class: 'iGroup price',
                            html: $('<ul/>')
                        })
                    })
                }).append($('<div/>', {
                    class: 'tab',
                    'data-title': lang[67],
                    id: 'serv',
                    html: $('<div/>', {
                        id: 'services',
                        class: 'iGroup price',
                        html: $('<ul/>')
                    })
                })).append($('<div/>', {
                    class: 'tab',
                    'data-title': 'Order parts',
                    id: 'purch',
                    html: $('<div/>', {
                        id: 'purchases',
                        class: 'iGroup',
                        html: $('<ul/>')
                    })
                }).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: 'New purchase'
					})
				}).append($('<input/>', {
					type: 'text',
					name: 'new_purchase',
					onblur: 'issues.newPur(this)'
				}))).append($('<div/>'))).append($('<div/>', {
                    class: 'tab',
                    'data-title': 'Trade in',
                    id: 'buy_inventory',
                    html: $('<div/>', {
                        id: 'tradein',
                        class: 'iGroup price',
                        html: $('<ul/>')
                    })
                }))
            }).append($('<div/>', {
                class: 'sGroup',
                html: $('<button/>', {
                    type: 'button',
                    class: 'btn btnSubmit',
                    onclick: 'invoices.chooseInv();',
                    html: lang[65]
                })
            })),
            cb: function() {
                $('#page').tabs();
                $.post('/inventory/all', {
                    noCust: 1,
                    type: 'stock',
                    nIds: Object.keys($('input[name="inventory"]').data() || {}).join(','),
                    oId: Object.keys($('input[name="object"]').data() || {}).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price).toFixed(2) + '" data-catname="' + v.catname + '">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#inventory > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                noCust: 1,
                                type: 'stock',
                                nIds: Object.keys($('input[name="inventory"]').data() || {}).join(','),
                                query: $('#inventory > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="inventory"]').data()
                        }, $('input[name="inventory"]'));
                    }
                }, 'json');

                $.post('/inventory/all', {
                    noCust: 1,
                    type: 'service',
                    nIds: Object.keys($('input[name="services"]').data() || {}).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price).toFixed(2) + '" data-catname="' + v.catname + '">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#services > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                noCust: 1,
                                type: 'service',
                                nIds: Object.keys($('input[name="services"]').data() || {}).join(','),
                                query: $('#services > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="services"]').data()
                        }, $('input[name="services"]'));
                    }
                }, 'json');

                $.post('/purchases/all', {
                    nIds: Object.keys($('input[name="purchases"]').data() || {}).join(',')
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price.replace('US', '').replace('$', '').trim()).toFixed(2) + '" data-catname="">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#purchases > ul').html(items).sForm({
                            action: '/purchases/all',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="purchases"]').data() || {}).join(','),
                                query: $('#purchases > .sfWrap input').val() || ''
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="purchases"]').data()
                        }, $('input[name="purchases"]'));
                    }
                }, 'json');
				
				$.post('/inventory/all', {
                    nIds: Object.keys($('input[name="purchases"]').data() || {}).join(','),
					user: Object.keys($('input[name="customer"]').data() || {}).join(','),
					by_user: 1
                }, function(r) {
                    if (r) {
                        var items = '',
                            lId = 0;
                        $.each(r.list, function(i, v) {
                            items += '<li data-value="' + v.id + '" data-price="$ ' + parseFloat(v.price.replace('US', '').replace('$', '').trim()).toFixed(2) + '" data-catname="">' + v.name + '</li>';
                            lId = v;
                        });
                        $('#tradein > ul').html(items).sForm({
                            action: '/inventory/all',
                            data: {
                                lId: lId,
                                nIds: Object.keys($('input[name="tradein"]').data() || {}).join(','),
                                query: $('#tradein > .sfWrap input').val() || '',
								user: Object.keys($('input[name="customer"]').data() || {}).join(','),
								by_user: 1
                            },
                            all: (r.count <= 20) ? true : false,
                            select: $('input[name="tradein"]').data()
                        }, $('input[name="tradein"]'));
                    }
                }, 'json');
            }
        });
    },
    chooseInv: function() {
        var err = null;
        if (!Object.keys($('input[name="inventory"]').data()).join(',') && !Object.keys($('input[name="tradein"]').data()).join(',') && !Object.keys($('input[name="services"]').data()).join(',') && !Object.keys($('input[name="invoices"]').data()).join(',') && !Object.keys($('input[name="purchases"]').data()).join(','))
            alr.show({
				class: 'alrDanger',
				content: 'You did not choose the service!',
				delay: 2
			});
        else {
            var inv = $('input[name="inventory"]').data(),
                serv = $('input[name="services"]').data();
            invc = $('input[name="invoices"]').data();
            purch = $('input[name="purchases"]').data();
            tradein = $('input[name="tradein"]').data();

            invoices.eachService(inv, 'stock');
            invoices.eachService(serv, 'service');
            invoices.eachService(purch, 'purchase');
            invoices.eachService(tradein, 'tradein');

            mdl.close('addInv');
            invoices.total();
        }
    },
    eachService: function(el, type) {
        $.each(el, function(i, v) {
            if (!$('.mInvoice #tr_' + type + '_' + i).length || type == 'service') {
                $('.tbl.payInfo.mInvoice').append($('<div/>', {
                    class: 'tr itm',
                    id: 'tr_' + type + '_' + i,
					'data-id': i,
                    html: $('<div/>', {
                        class: 'td',
                        html: $('<span/>', {
                            class: 'fa fa-times del',
                            onclick: 'invoices.delInvItem(this, "' + type + '", ' + i + ')'
                        })
                    }).append(' ' + ($(v).attr('catname') != 'null' ? ($(v).attr('catname') + ' ') : '') + $(v).attr('name'))
                }).append($('<div/>', {
                    class: 'td w10',
                    html: '1'
                })).append($('<div/>', {
                    class: 'td w100' + (type == 'tradein' ? ' nPay trIn' : ''),
                    html: (type == 'tradein' ? $('<input/>', {
						name: 'tradein_price',
						type: 'number',
						step: '0.1',
						min: '0',
						onchange: 'invoices.total();',
						value: parseFloat($(v).attr('price').replace('$ ', '')).toFixed(2)
					}) : '$' + parseFloat($(v).attr('price').replace('$ ', '')).toFixed(2))
                })).append($('<div/>', {
                    class: 'td w10',
                    html: (type == 'tradein' ? 'no' : 'yes')
                })))
            }
        });
    },
    delInvItem: function(el, type, id) {
        type = (type == 'stock') ? 'inventory' : type;
        $(el).parent().parent().remove();
        delete $('input[name="' + type + '"]').data()[id];
        invoices.total();
    },
    delInInvoice: function(el) {
        $(el).parents('.tbl').remove();
        invoices.total();
    },
    total: function(el) {
        var subtotal = 0,
			subtotal_end = 0,
			tradein = 0,
            pSub = 0,
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax,
            discount = 1;
        if ($('.tbl.payInfo.discount').length)
            discount = (100 + parseFloat($('.tbl.payInfo.discount .w100').text().replace('$', ''))) / 100;
        $('.payInfo:not(.discount)').each(function(ind, val) {
            $(val).find('div > .td.w100:not(".nPay")').each(function(i, v) {
                if ($(v).parent().hasClass('discountTr'))
                    pSub *= ((100 + parseFloat($(v).text().replace('$', ''))) / 100);
                else
                    pSub += parseFloat($(v).text().replace('$', ''));
            });
            subtotal += pSub;
            pSub = 0;
        });
		
		 $('.td.w100.trIn').each(function(i, v) {
			tradein += parseFloat($(v).find('input').length ? $(v).find('input').val() : $(v).text().replace('$', ''));
		});
			
        subtotal *= discount;
		subtotal_end = subtotal - tradein;
        $('#subtotal').text(subtotal_end.toFixed(2));
        $('#tax').text((subtotal * tax / 100).toFixed(2));
        $('#total').text((subtotal_end + parseFloat($('#tax').text().replace('$', ''))).toFixed(2));
        $('#due').text((parseFloat($('#total').text().replace('$', '')) - parseFloat($('#paid').text().replace('$', '')) || 0).toFixed(2));
    },
    create: function(el, id) {
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null,
            subtotal = 0,
			tradein_arr = {},
            tax = $('input[name="object"]').data()[Object.keys($('input[name="object"]').data())].tax;
        if (!Object.keys($('input[name="customer"]').data()).join(',').length) {
            err = lang[64];
        } else if (!Object.keys($('input[name="object"]').data()).join(',').length) {
            err = lang[63];
        } else if (!Object.keys($('input[name="inventory"]').data()).join(',').length && !Object.keys($('input[name="tradein"]').data()).join(',').length && !Object.keys($('input[name="services"]').data()).join(',').length && !Object.keys($('input[name="purchases"]').data()).join(',').length) {
            err = lang[62];
        } else {
            data.customer = Object.keys($('input[name="customer"]').data()).join(',');
            data.object = Object.keys($('input[name="object"]').data()).join(',');
            data.inventory = Object.keys($('input[name="inventory"]').data()).join(',');
            data.services = Object.keys($('input[name="services"]').data()).join(',');
            data.purchases = Object.keys($('input[name="purchases"]').data()).join(',');
            data.tradein = Object.keys($('input[name="tradein"]').data()).join(',');

            $('.payInfo.mInvoice > div > .td.w100').each(function(i, v) {
                subtotal += parseFloat($(v).text());
            });
			
			$('.td.w100.trIn').each(function(i, v) {
				tradein_arr[$(v).parent().attr('data-id')] = parseFloat($(v).find('input').length ? $(v).find('input').val() : Math.abs(parseFloat($(v).text().replace('$', '').trim())));
			});
			
            /* data.subtotal = subtotal;
            data.tax = (subtotal * tax / 100).toFixed(2);
            data.total = parseFloat(subtotal) + parseFloat((subtotal * tax / 100).toFixed(2)); */
			data.subtotal = $('#subtotal').text();
            data.tax = $('#tax').text();
            data.total = $('#total').text();
            data.paid = parseFloat($('#paid').text());
			data.trade_arr = tradein_arr;

            if (id) {
                data.invoices = '';
                $('.inTbl').each(function(i, v) {
                    data.invoices += $(v).attr('data-id') + ',';
                });
            }
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/send', data, function(r) {
                loadBtn.stop($(el));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
                        delay: 2
                    });
                    var edit = location.pathname.match(/(.*)\/invoices\/?(.*)?/i);
                    if (edit[2].indexOf('edit') < 0) Page.get('/invoices/view/' + r);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    partial: function(id, el, p) {
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null;
        $('input[name="partial"]').css('border-color', '#ddd');
        if ($('input[name="partial"]').val() > Math.abs(parseFloat($('#due').text()))) {
            err = lang[61];
            $('input[name="partial"]').css('border-color', '#f00');
        } else if (!$('input[name="partial"]').val()) {
            err = lang[60];
            $('input[name="partial"]').css('border-color', '#f00');
        } else {
            data.partial = $('input[name="partial"]').val();
            data.purchace = p;
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/partial', data, function(r) {
                loadBtn.stop($(el));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[58],
                        delay: 2
                    });
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    discount: function(id, el, cb) {
        if (cb) cb(null, id);
        loadBtn.start($(el));
        var data = {
            id: id,
            discount: $('select[name="discount"]').val()
        };
        $.post('/invoices/discount', data, function(r) {
            loadBtn.stop($(el));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[58],
                    delay: 2
                });
                Page.get(location.href);
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[13],
                    delay: 2
                });
            }
        });
    },
    merge: function(id, el, cb) {
        if (cb) cb(null, id);
        loadBtn.start($(el));
        var data = {
                id: id
            },
            err = null;
        if (!Object.keys($('input[name="invoices"]').data()).join(',')) {
            err = lang[59];
        } else {
            data.invoice = Object.keys($('input[name="invoices"]').data()).join(',');
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(el));
        } else {
            $.post('/invoices/add_invoice', data, function(r) {
                loadBtn.stop($(el));
                if (r == 'OK') {
                    alr.show({
                        class: 'alrSuccess',
                        content:lang[58],
                        delay: 2
                    });
                    Page.get(location.href);
                } else {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                }
            });
        }
    },
    del: function(id) {
        $.post('/invoices/del', {
            id: id
        }, function(r) {
            if (r == 'OK') {
                if ($('#invoice_' + id).length)
                    $('#invoice_' + id).remove();
                else
                    Page.get('/invoices/');
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    issCreate: function(id, c, o, d) {
		/* if (parseFloat($('#doit').text().replace('$', '')) == 0) {
			alr.show({
				class: 'alrDanger',
				content: 'You can not create invoice before enter doit price!',
				delay: 2
			})
		} else { */
			var data = {
					issue: id,
					discount: d
				},
				err = null,
				inventory = '',
				services = '',
				purchases = '';
			/* $('#tbl_inventory > .tr > .td:first').each(function(i, v) {
				inventory += $(v).text().trim().replace('#', '') + ',';
			});
			$('#tbl_services > .tr > .td:first').each(function(i, v) {
				services += $(v).text().trim().replace('#', '') + ',';
			});
			$('#tbl_purchases > .tr > .td:first').each(function(i, v) {
				purchases += $(v).text().trim().replace('#', '') + ',';
			}); */
			data.customer = c;
			data.object = o;
			/* data.inventory = inventory;
			data.services = services;
			data.purchases = purchases; */
			data.subtotal = parseFloat($('#totalPrice').text().replace('$', ''));
			data.tax = ((parseFloat($('#doit').text().replace('$', '')) > 0) ? parseFloat($('#doit').text().replace('$', '')) : (parseFloat($('#totalPrice').text().replace('$', '')))) * parseFloat($('#tax').text()) / 100;
			//data.doit = ((parseFloat($('#doit').text().replace('$', '')) > 0) ? parseFloat($('#doit').text().replace('$', '')) + data.tax : 0);
			//data.total = (parseFloat($('#doit').text().replace('$', '')) > 0) ? data.doit : data.subtotal + data.tax;
			data.total = data.subtotal + data.tax;
			data.paid = 0;

			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/invoices/send', data, function(r) {
					if (r > 0) {
						alr.show({
							class: 'alrSuccess',
							content: lang[58],
							delay: 2
						});
						Page.get('/invoices/view/' + r);
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				});
			}
		//}
    }, saveForm: function(el, f) {
		f.preventDefault();
		loadBtn.start($(el).find('button[type="submit"]'));
		var data = {};
		if (!$('textarea[name="content"]').val()) {
			loadBtn.stop($(el).find('button[type="submit"]'));
			alr.show({
				class: 'alrDanger',
				content: lang[57],
				delay: 2
			});
		} else {
			data.content = $('textarea[name="content"]').val();
			$.post('/invoices/save_form', data, function(r) {
				loadBtn.stop($(el).find('button[type="submit"]'));
				if (r == 'OK') {
					alr.show({
						class: 'alrSuccess',
						content: lang[56],
						delay: 2
					});
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	}, makePayment: function(el, id, p) {
		loadBtn.start($(el));
			var data = {};
			if (!$('input[name="cash"]').val()) {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Enter amount',
					delay: 2
				});
			} else if (($('select[name="method"]').val() != 'cash' || p) && Math.abs($('input[name="cash"]').val()) > Math.abs(parseFloat($('#due').text()))) {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Amount exceeds due',
					delay: 2
				});
			} else if (!$('input[name="check"]').val() && $('select[name="method"]').val() == 'check') {
				loadBtn.stop($(el));
				alr.show({
					class: 'alrDanger',
					content: 'Enter check number',
					delay: 2
				});
			} else {
				data.id = id;
				data.method = $('select[name="method"]').val();
				data.amount = parseFloat($('input[name="cash"]').val()) - ($('input[name="rest"]').length ? parseFloat($('input[name="rest"]').val()) : 0);
				data.check = $('input[name="check"]').val();
				$.post('/invoices/make_tran', data, function(r) {
					loadBtn.stop($(el));
					if (r == 'OK') {
						alr.show({
							class: 'alrSuccess',
							content: 'Transaction is done',
							delay: 2
						});
						Page.get(location.href);
						mdl.close();
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				});
			}
	}, makeTran: function(id, el, p) {
		if ($('input[name="cash"]').val() < parseFloat($('#due').text())) {
			mdl.open({
				id: 'appliedPartial',
				title: 'Partial payment',
				content: $('<div/>', {
					class: 'aCenter',
					html: 'Are you applied to make partial payment?'
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit ac',
						onclick: 'invoices.makePayment(this, ' + id + ', ' + p + ');',
						html: 'Yes'
					})
				}).append($('<button/>', {
					type: 'button',
					class: 'btn btnSubmit dc',
					onclick: 'mdl.close();',
					html: 'No'
				})))
			})
		} else {
			invoices.makePayment(el, id, p);
		}
	}
}

// Bugs
var bugs = {
    add: function(id, imp) {
		var title = '', content = '', images = '';
		if (id) {
			title = $('#bug_' + id).find('.bugTitle > div:first-child > i').text();
			content = $('#bug_' + id).find('.bugContent > div:first-child').html();
			var images = '';
			$('#bug_' + id).find('.thumbnails > .thumb').each(function(i, v) {
				images += '<div class="thumb">' + $(v).html() + '<span class="fa fa-times" onclick="delete_image(this);"></span></div>';
			})
		}
        mdl.open({
            id: 'bugAdd',
            title: lang[55],
            content: $('<div/>', {
                class: 'bugForm',
                html: $('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[54]
                    })
                }).append($('<input/>', {
                    type: 'text',
                    name: 'title',
					value: title || ''
                }))
            }).append($('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[53]
                    })
                }).append($('<textarea/>', {
                    name: 'content',
					html: content || ''
                    }))).append($('<div/>', {
                        class: 'iGroup imgGroup',
                        html: $('<label/>', {
                            html: lang[52]
                        })
                    }).append($('<div/>', {
                        class: 'dragndrop',
                        html: $('<span/>', {
                            class: 'fa fa-download'
                        })
                        }).append('Click or drag and drop file here')).append($('<div/>', {
                            class: 'thumbnails',
							html: images || ''
                        }))).append($('<div/>', {
                    class: 'sGroup',
                    html: $('<button/>', {
                        class: 'btn btnSubmit', 
                        html: lang[18],
                        type: 'button',
                        onclick: 'bugs.send(this, ' + (id  || 0) + ', ' + (imp || 0) + ');'
                    })
                }))
        });
		$('.dragndrop').upload({
			count: 0,
			multiple: true,
			max: 5,
			check: function(e){
				var self = this;
				if(!e.error){
					var img = new Image();
					img.src = URL.createObjectURL(e.file);
					img.setAttribute('onclick', 'showPhoto(this.src);');
					$('#bugAdd .thumbnails').append($('<div>', {
						class: 'thumb',
						html: img
					}).append($('<span/>', {
						class: 'fa fa-times'
					}).click(function(){
						delete self.files[e.file.name];
						$(this).parent().remove();
					})));
				} else if(e.error == 'max'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[51],
					   delay: 2
					});
				} else if(e.error == 'type'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[50],
					   delay: 2
					});
				} else if(e.error == 'size'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[49],
					   delay: 2
					});
				}
			}
		});
    }, send: function(el, id, imp) {
        loadBtn.start($(el));
        var data = new FormData(), err = null;
		if (id) data.append('id', id);
         $(el).parents('.bugForm')
			  .find('label').next()
			  .css('border', '1px solid #ddd');
         $(el).parents('.bugForm')
			  .find('.iGroup:not(.imgGroup)')
			  .each(function(i, v) {
            if (!$(v).find('label').next().val() && $(v).find('label').text().trim() != 'Comment') {
                err = $(v).text() + ' can not be empty';
                $(v).find('label')
					.next()
					.css('border', '1px solid #f00');
            } else {
                if ($(v).find('label').next().attr('name') == $(v).attr('id'))
					data.append(Object.keys($(v).find('label').next().attr('name'), $(v).find('label').next().data()).join(','));
                else {
					data.append($(v).find('label').next().attr('name'), $(v).find('label').next().val());
					if ($(v).find('label').next().attr('name') == 'comment') data.append('dev', 1);
					if (imp == 1) data.append('status', 'improvement');
				}
            }
        })
		
		if ($('.dragndrop').length) {
			var files = $('.dragndrop')[0].files,
				del = $('.dragndrop')[0].delete;
			if (del) {
				$.each(del, function() {
					data.append('delete[]', this.split('thumb_')[1]);
				});
			}
			if (!$.isEmptyObject(files)) {
				for (var n in files) {
					data.append('image[]', files[n]);
				}
			}
		}

         if (err) {
            loadBtn.stop($(el));
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
        } else {
			$.ajax({
				type: 'POST',
				processData: false,
				contentType: false,
				url: '/bugs/send',
				data: data
			}).done(function(r) {
                loadBtn.stop($(el));
                if (r) {
					r = JSON.parse(r);
					var new_images = '';
					if (r.images) {
						$.each(r.images, function(i, v) {
							new_images = '<div class="thumb"><img src="/uploads/images/bugs/' + r.id + '/thumb_' + v + '" onclick="showPhoto(this.src);"></div>'
						})
					}
                    if (id) {
						$('#bug_' + r.id + ' .bugTitle > div:first-child > i').html($('input[name="title"]').val());
						$('#bug_' + r.id + ' .bugContent > div:first-child').html($('textarea[name="content"]').val());
						if ($('select[name="status"]').val()) $('#bug_' + r.id + ' .bugTitle > div:first-child > span').removeClass($('#bug_' + r.id + ' .bugTitle > div:first-child > span').attr('class')).addClass('st_' + $('select[name="status"]').val()).html($('select[name="status"]').val());
						if (del) {
							$.each(del, function() {
								$('#bug_' + r.id + ' .bugContent > .thumbnails').find('img[src*="' + this.split('thumb_')[1] + '"]').parent().remove();
								$('#bug_' + r.id + ' .bugComment > .thumbnails').find('img[src*="' + this.split('thumb_')[1] + '"]').parent().remove();
							});
						}
						if ($('textarea[name="comment"]').length) {
							if ($('#bug_' + id + ' .bugComment').length) {
								$('#bug_' + id + ' .bugComment').removeAttr('class').addClass('bugComment cm_' + $('select[name="status"]').val())
									.html(r.user + ': <span>' + $('textarea[name="comment"]').val()
									+ '</span><div class="thumbnails">' + new_images + '</div>');
							} else {
								$('#bug_' + id + ' .bugContent').append($('<div/>', {
									class: 'bugComment cm_' + $('select[name="status"]').val(),
									html: r.user + ': <span>' + $('textarea[name="comment"]').val()
									+ '</span><div class="thumbnails">' + new_images + '</div>'
								}))
							}
						} else {
							$('#bug_' + r.id + ' .bugContent > .thumbnails').append(new_images);
						}
					} else {
						$('.userList.bug').prepend($('<div/>', {
							class: 'sUser bug',
							id: 'bug_' + r.id,
							html: $('<div/>', {
								class: 'bugTitle',
								html: $('<div/>', {
									html: $('<i/>', {
										html: $('input[name="title"]').val()
									})
								}).prepend($('<b/>', {
									html: '#' + r.id + ' '
								})).append('<span/>', {
									class: 'fa fa-pencil bEdit',
									onclick: 'bugs.add({id});'
								}).append($('<span/>', {
									class: imp ? 'st_improvement' : 'st_opened',
									html: imp ? 'improvement' : 'opened'
								}))
							}).append($('<div/>', {
								class: 'date',
								html: r.user
							}).append($('<span/>', {
								html: '|'
							})).append(r.date))
						}).append($('<div/>', {
							class: 'bugContent',
							html: $('<div/>', {
								html: $('textarea[name="content"]').val()
							})
						}).append($('<div/>', {
							class: 'thumbnails',
							html: new_images
						}))));
					}
					mdl.close();
				} else {
                   alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    }); 
				}
			});
        }
    }, edit: function(id) {
		var images = '', comment = '';
		if (id) {
			$('#bug_' + id).find('.bugComment > .thumbnails > .thumb').each(function(i, v) {
				images += '<div class="thumb">' + $(v).html() + '<span class="fa fa-times" onclick="delete_image(this);"></span></div>';
			})
			comment = $('#bug_' + id + ' .bugComment > span').text();
		}
        mdl.open({
            id: 'editBug',
            title: lang[48],
            content: $('<div/>', {
                class: 'bugForm',
                html: $('<div/>', {
                    class: 'iGroup',
                    html: $('<label/>', {
                        html: lang[47]
                    })
                }).append($('<select/>', {
                    name: 'status',
                    html: $('<option/>', {
                        value: 'opened',
                        html: lang[46]
                    })
                }).append($('<option/>', {
                        value: 'processed',
                        html: lang[45]
                    })).append($('<option/>', {
                        value: 'closed',
                        html: lang[44]
                    })).append($('<option/>', {
                        value: 'rejected',
                        html: lang[43]
                    })).append($('<option/>', {
                        value: 'improvement',
                        html: 'Improvement'
                    })))
				}).append($('<div/>', {
                    class: 'iGroup fw',
                    html: $('<label/>', {
                        html: lang[27]
                    })
                }).append($('<textarea/>', {
                    name: 'comment',
					html: comment || ''
                }))).append($('<div/>', {
					class: 'iGroup imgGroup',
					html: $('<label/>', {
						html: lang[52]
					})
				}).append($('<div/>', {
					class: 'dragndrop',
					html: $('<span/>', {
						class: 'fa fa-download'
					})
				}).append('Click or drag and drop file here')).append($('<div/>', {
					class: 'thumbnails',
					html: images || ''
				}))).append($('<div/>', {
                    class: 'sGroup',
                    html: $('<button/>', {
                        class: 'btn btnSubmit', 
                        html: lang[42],
                        type: 'button',
                        onclick: 'bugs.send(this, ' + id + ');'
                    })
                })),
            cb: function() { 
				$('select[name="status"]').val($('#bug_' + id + ' .bugTitle > div:first-child > span:not(.fa)').attr('class').replace('st_', ''));
                $('#page').select();
            }
        });
		$('.dragndrop').upload({
			count: 0,
			multiple: true,
			max: 5,
			check: function(e){
				var self = this;
				if(!e.error){
					var img = new Image();
					img.src = URL.createObjectURL(e.file);
					img.setAttribute('onclick', 'showPhoto(this.src);');
					$('#editBug .thumbnails').append($('<div>', {
						class: 'thumb',
						html: img
					}).append($('<span/>', {
						class: 'fa fa-times'
					}).click(function(){
						delete self.files[e.file.name];
						$(this).parent().remove();
					})));
				} else if(e.error == 'max'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[51],
					   delay: 2
					});
				} else if(e.error == 'type'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[50],
					   delay: 2
					});
				} else if(e.error == 'size'){
					alr.show({
					   class: 'alrDanger',
					   content: lang[49],
					   delay: 2
					});
				}
			}
		});
    }
}

// Cash
var cash = {
	amount_cash: 0,
	amount_credit: 0,
	amount_hands: 0,
	amount_system: 0,
	show: 1,
	openCash: function(t, a) {
		cash.amount_cash = 0;
		cash.amount_credit = 0;
		cash.amount_hands = 0;
		cash.amount_system = 0;
		$.post('/cash/open', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			action: a,
			type: 'cash'
		}, function(r) {
			if (r.amount == 'DONE') {
				alr.show({
					class: 'alrDanger',
					content: lang[148] + (a == 'open' ? lang[149] : lang[150]) + lang[147],
					delay: 2
				});
			} else if (r.amount == 'ERR') {
				alr.show({
					class: 'alrDanger',
					content: lang[39] + (a == 'open' ? lang[40] : lang[41]),
					delay: 2
				});
			} else {
				cash.amount_cash = r.amount;
				cash.amount_system = r.amount;
				cash.show = r.show;
				mdl.open({
				id: 'openCash',
				title: (a == 'open' ? lang[38] : lang[151]),
				content: $('<div/>', {
					class: 'mdlCash',
					html: $('<div/>', {
						class: 'sumCash',
						html: (r.show == 1 ? ('Enter Total Drawer Amount<br>$ ' + r.amount) : 'Enter Total Drawer Amount')
					})
				}).append($('<div/>', {
					class: 'iGroup',
					html: $('<label/>', {
						html: 'Amount'
					})
				}).append($('<input/>', {
					type: 'number',
					min: '0',
					step: '0.1',
					name: 'hands'
				}))).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn  btnSubmit ac',
						html: lang[30],
						onclick: a == 'close' ? 'cash.acceptClose(\'cash\', \'' + a + '\', 0, \'' + (r == 'NO' ? '0' : r) + '\');' : 'cash.accept(\'cash\', \'' + a + '\');'
					})
				}).append($('<button/>', {
					type: 'button',
					class: 'btn btnSubmit dc',
					html: lang[29],
					onclick: 'mdl.close();'//'cash.confirmDicline(\'cash\', \'' + a + '\', \'' + (r == 'NO' ? '0' : r) + '\');' //'cash.dicline(\'cash\', \'' + a + '\', \'' + (r == 'NO' ? '0' : r) + '\');'
				})))
				});
			}
		}, 'json');
	}, confirmDicline: function(t, a, d) {
		mdl.close('openCash');
		mdl.open({
			id: 'decOpenCash',
			title: (a == 'open' ? lang[38] : lang[151]),
			content: $('<div/>', {
				class: 'mdlCash',
				html: $('<div/>', {
					class: 'sumCash',
					html: 'Are you sure you want to ' + a + ' cash?'
				})
			}).append($('<div/>', {
				class: 'cashGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn  btnSubmit ac',
					html: lang[30],
					onclick: 'cash.dicline(\'cash\', \'' + a + '\', \'' + d + '\');'
				})
			}).append($('<button/>', {
					type: 'button',
					class: 'btn btnSubmit dc',
					html: 'Recalculate',
					onclick: 'mdl.close();'
				})))
		});
	}, accept: function(t, a, d, out) {
		if ((t == 'cash' ? cash.amount_cash : cash.amount_credit) != parseFloat($('input[name="hands"]').val()) && a == 'open') {
			cash.amount_hands = $('input[name="hands"]').val();
			cash.amount_cash = $('input[name="hands"]').val();
			cash.amount_credit = $('input[name="hands"]').val();
			mdl.close();
			cash.dicline(t, a, d);
		} else {
			var data = {}, err = null;
			if (a == 'close') {
				$('.mdl input').css('border', '1px solid #ddd');
				if (!$('input[name="out"]').val() && out == 'out') {
					err = 'Please, enter value';
					$('input[name="out"]').css('border', '1px solid #f00');
				} else if (!$('input[name="leave"]').val() && !out) {
					err = 'Please, enter value';
					$('input[name="leave"]').css('border', '1px solid #f00');
				} else {
					//data.amount = $('input[name="leave"]').val();
					data.system = cash.amount_system;
					if (out == 'out')
						data.out = $('input[name="out"]').val();
					else
						data.amount = $('input[name="leave"]').val();
				}
			} else {
				//data.amount = (t == 'cash' ? cash.amount_cash : cash.amount_credit);
				data.amount = $('input[name="hands"]').val();
				data.system = cash.amount_system;
			}
			if (err) {
				alr.show({
					class: 'alrDanger',
					content: err,
					delay: 2
				});
			} else {
				$.post('/cash/send', {
					type: t, 
					action: 'accept', 
					acType: a, 
					object: Object.keys($('input[name="object"]').data()).join(','),
					data: data,
					d: d || 0 //dicline if d
				}, function(r) {
					if (r) {
						mdl.close();
						if (a == 'open' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'close\')').html(lang[34]);
							var crd = setTimeout(function() {
								cash.openCredit($('input[name="credit"]').val());
								clearTimeout(crd);
							}, 2000);
						} else if (a == 'close' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'open\')').html(lang[33]);
							if (out == 'out') var crd = setTimeout(function() {
								cash.leave(t, a, r.id);
								clearTimeout(crd);
							}, 500);
						}
						
						if ($('#cashHistory .noContent')) $('.noContent').remove();
						if (!d) {
							$('#cashHistory .tBody').prepend($('<div/>', {
								class: 'tr' + (d ? ' dicline' : ''),
								id: 'stat_' + r.id,
								onclick: (d ? ' cash.adminAccept(' + r.id + ');' : ''),
								html: $('<div/>', {
									class: 'td wIds',
									html: $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name
								})
							}).append($('<div/>', {
								class: 'td',
								html: lang[r.status]
							})).append($('<div/>', {
								class: 'td',
								html: t
							})).append($('<div/>', {
								class: 'td',
								html: (cash.show == 1 ? '$ ' + cash.amount_system : ' --- ')
							})).append($('<div/>', {
								class: 'td',
								html: (cash.show == 1 ? '$ ' + (data.amount || 0) : ' --- ')
							})).append($('<div/>', {
								class: 'td',
								html: lang[a]
							})).append($('<div/>', {
								class: 'td',
								html: r.date
							})).append($('<div/>', {
								class: 'td',
								html: r.user
							})).append($('<div/>', {
								class: 'td',
								style: 'width: 50px',
								html: $('<a/>', {
									href: '/invoices/history/?type=' + t + '&date=' + r.ddate + '&object=' + Object.keys($('input[name="object"]').data()).join(',') + '&object_name=' + $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name,
									onclick: 'Page.get(this.href); return false;',
									html: 'Invoices'
								})
							})));
						}
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
				}, 'json');
			}
		}
	}, dicline: function(t, a, s) {
		mdl.close();
		/* mdl.open({
			id: 'mdlDicline',
			title: lang[36],
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[35]
					})
				}).append($('<input/>', {
					type: 'number',
					min: 0,
					step: 0.1,
					placeholder: 0,
					name: 'amount',
					value: cash.amount_hands
				}))
			}).append($('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[27]
					})
				}).append($('<textarea/>', {
					placeholder: lang[27],
					name: 'comment'
				}))).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: lang[28],
					onclick: 'cash.sendDicline(\'' + t + '\', \'' + a + '\', \'' + s + '\');'
				})
			}))
		}); */
		
		mdl.open({
			id: 'mdlDicline',
			title: lang[36],
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'amNot',
					html: $('<div/>', {
						html: 'Are you sure?'
					})
			})}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn  btnSubmit ac',
						html: lang[30],
						onclick: 'cash.sendDicline(\'' + t + '\', \'' + a + '\'' + (s ? ', ' + s : '') + ');'
					})
				}).append($('<button/>', {
						type: 'button',
						class: 'btn btnSubmit dc',
						html: 'Recalculate',
						onclick: 'mdl.close();'
					})))
		});
	}, sendDicline: function(t, a, s) {
		var data = {}, err = null;
		/* $('.mdl input, .mdl textarea').css('border', '1px solid #ddd');
		if (!$('input[name="amount"]').val()) {
			err = 'leave can not be empty';
			$('input[name="amount"]').css('border', '1px solid #f00');
		} else if (!$('textarea[name="comment"]').val()) {
			err = 'Comment can not be empty';
			$('textarea[name="comment"]').css('border', '1px solid #f00');
		} else { */
			data.amount = (t == 'cash' ? cash.amount_cash : cash.amount_credit);
			data.system = cash.amount_system;
			data.lack = cash.amount_system - (t == 'cash' ? cash.amount_cash : cash.amount_credit);
		//}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/cash/send', {
					type: t, 
					action: 'dicline', 
					acType: a, 
					object: Object.keys($('input[name="object"]').data()).join(','),
					data: data
				}, function(r) {
					if (r.id > 0) {
						mdl.close();
						if (a == 'close') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'open\')').html(lang[33]);
							cash.acceptClose(t, a, r.id, 0, 1);
						} else if (a == 'open' && t == 'cash') {
							$('.btnOCash').attr('onclick', 'cash.openCash(\'cash\', \'close\')').html(lang[34]);
							var crd = setTimeout(function() {
								cash.openCredit();
								clearTimeout(crd);
							}, 2000);
						}
						if ($('#cashHistory .noContent')) $('.noContent').remove();
						$('#cashHistory .tBody').prepend($('<div/>', {
							class: 'tr dicline',
							id: 'stat_' + r.id,
							onclick: 'cash.adminAccept(' + r.id + ');',
							html: $('<div/>', {
								class: 'td wIds',
								html: $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name
							})
						}).append($('<div/>', {
							class: 'td',
							html: lang[r.status]
						})).append($('<div/>', {
							class: 'td',
							html: t
						})).append($('<div/>', {
							class: 'td',
							html: (cash.show == 1 ? '$ ' + data.system : ' --- ')
						})).append($('<div/>', {
							class: 'td',
							html: (cash.show == 1 ? '$ ' + data.amount : ' --- ')
						})).append($('<div/>', {
							class: 'td',
							html: lang[a]
						})).append($('<div/>', {
							class: 'td',
							html: r.date
						})).append($('<div/>', {
							class: 'td',
							html: r.user
						})).append($('<div/>', {
							class: 'td',
							style: 'width: 50px',
							html: $('<a/>', {
								href: '/invoices/history/?type=' + t + '&date=' + r.ddate + '&object=' + Object.keys($('input[name="object"]').data()).join(',') + '&object_name=' + $('input[name="object"]').data()[Object.keys($('input[name="object"]').data()).join(',')].name,
								onclick: 'Page.get(this.href); return false;',
								html: 'Invoices'
							})
						})));
					} else {
						alr.show({
							class: 'alrDanger',
							content: lang[13],
							delay: 2
						});
					}
			}, 'json');
		}
	}, change: function(e) {
		$('input[name="out"]').val(parseFloat($('input[name="out"]').attr('data-value')) - $(e).val());
	}, acceptClose: function(t, a, d, s, ac) {
		cash.amount_cash = parseFloat($('input[name="hands"]').val());
		cash.amount_hands = parseFloat($('input[name="hands"]').val());
		if (parseFloat($('input[name="hands"]').val()) != cash.amount_cash && !ac) {
			cash.dicline(t, a, d);
		} else {
			mdl.close();
			mdl.open({
				id: 'mdlClose',
				title: 'Input cash',
				content: $('<div/>', {
					class: 'diclineForm',
					html: $('<div/>', {
						class: 'iGroup fw',
						html: $('<label/>', {
							html: 'Drop Amount'
						})
					}).append($('<input/>', {
						type: 'number',
						min: 0,
						step: 0.1,
						placeholder: 0,
						name: 'out'
					}))
				}).append($('<div/>', {
					class: 'sGroup',
					html: $('<button/>', {
						type: 'button',
						class: 'btn btnSubmit',
						html: lang[28],
						onclick: 'cash.accept(\'cash\', \'' + a + '\', ' + (
							d ? d : 0
						) + ', \'out\');'
					})
				}))
			});
		}
	}, leave: function(t, a, d) {
		console.log(d);
		mdl.open({
			id: 'mdlClose',
			title: 'Input cash',
			content: $('<div/>', {
				class: 'diclineForm',
				html: $('<div/>', {
					class: 'iGroup fw',
					html: $('<label/>', {
						html: lang[32]
					})
				}).append($('<input/>', {
					type: 'number',
					min: 0,
					step: 0.1,
					placeholder: 0,
					name: 'leave'
				}))
			}).append($('<div/>', {
				class: 'sGroup',
				html: $('<button/>', {
					type: 'button',
					class: 'btn btnSubmit',
					html: lang[28],
					onclick: 'cash.accept(\'cash\', \'' + a + '\', ' + (
						d ? d : 0
					) + ');'
				})
			}))
		});
	}, openCredit: function() {
		cash.amount_cash = 0;
		cash.amount_credit = 0;
		cash.amount_hands = 0;
		cash.amount_system = 0;
		$.post('/cash/open', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			action: 'open',
			type: 'credit'
		}, function(r) {
			if (r) {
				cash.amount_system = r.amount;
				mdl.open({
					id: 'openCredit',
					title: lang[31],
					content: $('<div/>', {
						class: 'mdlCredit',
						html: $('<div/>', {
							class: 'sumCash',
							html: (cash.show == 1 ? 'Credit Settlement<br>$ ' + r.amount : 'Credit Settlement')
						})
					}).append($('<div/>', {
						class: 'iGroup',
						html: $('<label/>', {
							html: 'Amount'
						})
					}).append($('<input/>', {
						type: 'number',
						min: '0',
						step: '0.1',
						name: 'hands'
					}))).append($('<div/>', {
						class: 'cashGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn  btnSubmit ac',
							html: lang[30],
							onclick: 'cash.accept(\'credit\', \'open\');'
						})
					}).append($('<button/>', {
							type: 'button',
							class: 'btn btnSubmit dc',
							html: lang[29],
							onclick: 'cash.dicline(\'credit\', \'open\', ' + (r == 'NO' ? '0' : r) + ');'
						})))
				});
			}
		}, 'json');
	}, adminAccept: function(id) {
		$.post('/cash/getComment', {
			id: id
		}, function(r) {
			if (r) {
				mdl.open({
					id: 'admAccept',
					title: 'Leave a comment',
					content: $('<div/>', {
						class: 'comForm',
						html: $('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: r.user
							})
						}).append($('<div/>', {
							class: 'userComment',
							html: r.content
						}))
					}).append($('<div/>', {
							class: 'iGroup fw',
							html: $('<label/>', {
								html: lang[27]
							})
						}).append($('<textarea/>', {
							name: 'comment'
						}))).append($('<div/>', {
						class: 'sGroup',
						html: $('<button/>', {
							type: 'button',
							class: 'btn btnSubmit',
							html: lang[28],
							onclick: 'cash.sendAdm(' + id + ');'
						})
					}))
				})
			}
		}, 'json');
	}, sendAdm: function(id) {
		var data = {
			id: id
		}, err = null;
		$('textarea[name="comment"]').css('border', '1px solid #ddd');
		if (!$('textarea[name="comment"]').val()) {
			err = lang[26];
			$('textarea[name="comment"]').css('border', '1px solid #f00');
		} else {
			data.comment = $('textarea[name="comment"]').val();
		}
		
		if (err) {
			alr.show({
				class: 'alrDanger',
				content: err,
				delay: 2
			});
		} else {
			$.post('/cash/sendCom', data, function(r) {
				if (r == 'OK') {
					$('#stat_' + id).removeClass('dicline').find('.td').first().prepend(' <span class="fa fa-check"></span>');
					mdl.close();
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[13],
						delay: 2
					});
				}
			});
		}
	}, selectObject: function() {
		page = 0;
		$.post('/cash', {
			id: Object.keys($('input[name="object"]').data()).join(','),
			page: page,
			query: '',
			type: $('select[name="type"]').val(),
			action: $('select[name="action"]').val(),
			status: $('select[name="status"]').val(),
			staff: Object.keys($('input[name="staff"]').data()).join(','),
			date_start: $('#fCalendar input[name="date"]').val(),
			date_end: $('#fCalendar input[name="fDate"]').val()
		}, function(r) {
			if (r) {
				cash.amount_cash = 0;
				cash.amount_credit = 0;
				cash.show = 1;
				$('#cashHistory').removeClass('hdn').find('.tBody').html(r.content);
				$('.tbl + button').addClass(r.more);
				if (page == 0) {
					$('.btnOCash').removeAttr('disabled').attr('onclick', 'cash.openCash(\'cash\', \'' + (r.status || 'open') + '\')').html((r.status || 'Open ') + ' cash');
				}
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		}, 'json');
	}, doload: function(e) {
		page += 1;
		$.post(location.pathname, {
			id: Object.keys($('input[name="object"]').data()).join(',') || 0,
			query: query,
			page: page
		}, function(r) {
			$('.tBody').append(r.content);
			if (!r.left_count) $(e).addClass('hdn');
		}, 'json');
	}
}

// Get Location
function getLocation(f) {
    $.post('/objects/get_locations', {
        sId: Object.keys($('input[name="status"]').data() || {}).join(',') || 11,
        nIds: Object.keys($('input[name="location"]').data()).join(','),
		oId: Object.keys($('input[name="object"]').data() || {}).join(',') || $('.devLocation').attr('data-object') || 0,
    }, function(r) {
        if (r) {
            var items = '',
                lId = 0;
            $.each(r.list, function(i, v) {
                items += '<li data-value="' + v.id + '" data-count="' + v.count + '">' + v.name + '</li>';
                lId = v;
            });
			
				if (!$('#location > ul').length) {
					$('#location > input').removeData();
					$('#location > .sfWrap').remove();
					$('#location').append($('<ul/>'));
				}
				if (location_id && !f) {
					$('#location > input').data(location_id);
					location_id = '';
					object_id = '';
				}

            $('#location > ul').html(items).sForm({
                action: '/objects/get_locations',
                data: {
                    lId: lId,
                    sId: Object.keys($('input[name="status"]').data() || {}).join(',') || 11,
                    oId: Object.keys($('input[name="object"]').data() || {}).join(',') || $('.devLocation').attr('data-object') || object_id || 0,
                    nIds: Object.keys($('input[name="location"]').data() || {}).join(','),
                    query: $('#location > .sfWrap input').val() || ''
                },
                all: (r.count <= 20) ? true : false,
                select: $('input[name="location"]').data(),
                s: true
            }, $('input[name="location"]'), function() {
					$('#sublocation').find('select').empty();
					getCount(f);
			}, {
				oId: 'object',
				sId: 'status'
			});
        }
    }, 'json');
}

// Get Count
function getCount(f) {
	var options = '<option value="0">Not selected</option>';
	var c = Object.keys($('input[name="location"]').data() || {}).join(',') ? $('input[name="location"]').data()[Object.keys($('input[name="location"]').data() || {}).join(',')]['count'] : 0;
    if (c > 0) {
		for(var i = 1; i <= c; i++) {
			options += '<option value="' + i + '"' + (!f && location_count == i ? ' selected' : '') + '>' + i + '</option>';
		}
		if (!f) location_count = 0;
		$('#sublocation').show().find('select').html(options);
		if ($('#sublocation .sWrap').length) {
			$('#sublocation .sWrap').remove();
			$('#page').select();
		}
	} else {
		$('#sublocation').hide().find('select').val(0).trigger('change');
	}
}

// Categories
var categories = {
    dragEl: {},
    mouse: {},
    parentEl: {},
    clickEl: {},
    dragstart: function(e) {
        options.mouse = e;
        options.clickEl = e.target;
        options.dragEl = e.target.parentNode;
        options.parentEl = options.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', options.dragEl.textContent);
        console.log(options.mouse);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        options.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== options.clickEl && target.className == options.clickEl.className) {
            if (target.parentNode.nextSibling == options.dragEl)
                options.parentEl.insertBefore(options.dragEl, target.parentNode);
            else {
                options.parentEl.insertBefore(options.dragEl, target.parentNode.nextSibling);
            }
        }
    }
}

// Store
var store = {
    id: 0,
    sendPost: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {
                id: id
            },
            form = $(f),
            err = null;
        form.find('.iGroup').each(function(i, v) {
            if (!$(v).find('label').next().val() && $.inArray($(v).find('label').next().attr('name'), ['name', 'descr']) > 0) {
                err = 'The field can not be empty';
                $(v).find('label').next().css('border-color', '#f00');
            } else if ($(v).find('label').next().attr('name')) {
                $(v).find('label').next().css('border-color', '#ddd');
                data[$(v).find('label').next().attr('name')] = $(v).find('label').next().val();
            }
        });
        if (!$('textarea[name="content"]').val())
            err = lang[25];
        else {
            data['content'] = $('textarea[name="content"]').val()
        }
        if (err) {
            alr.show({
                class: 'alrDanger',
                content: err,
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
        } else {
            $.post('/store/send_post', data, function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r.id <= 0) {
                    alr.show({
                        class: 'alrDanger',
                        content: lang[13],
                        delay: 2
                    });
                } else {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[22] + (id ? lang[23] : lang[24]),
                        delay: 2
                    });
                    if (r.delivery) {
                        store.id = r.id;
                        store.mail(r.delivery, 0, true);
                    } else
                        Page.get('/store/blog/edit/' + r.id);
                }
            }, 'json');
        }
    },
    delBlog: function(id) {
        $.post('/store/del_post', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#post_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    },
    mail: function(delivery, lId, first) {
        if (first) {
            $('#all').text(delivery.count);
            $('#sended').text(0);
        }
        $.post('/store/delivery', {
            lid: lid
        }, function(r) {
            $('#sended').text(parseInt($('#sended').text()) ++);
            if (r.delivery)
                store.mail(r.delivery, r.lId, false);
            else
                Page.get('/store/blog/edit/' + store.id);
        })
    }, addCategory: function(id, n, p) {
        $.post('/store/allCategories', {
            id: id || '',
            nIds: Object.keys(p || {}).join(',')
        }, function(r) {
            if (r) {
                items = '';
                $.each(r.list, function(i, v) {
                    items += '<li data-value="' + v.id + '">' + v.name + '</li>';
                    lId = v.id;
                });
                mdl.open({
                    id: 'select_par',
                    title: (id ? lang[17] : lang[18]) + lang[19],
                    content: '<form  method="post" onsubmit="store.sendCategory(this, event, ' + (id || 0) + ');">\
						<div class="iGroup">\
							<label>' + lang[20] + '</label>\
							<input type="text" name="name" value="' + (n || '') + '">\
						</div>\
						<div class="iGroup">\
							<input type="hidden" name="parent">\
							<div class="lbl">' + lang[21] + '</div>\
							<ul class="hdn">' + items + '</ul>\
						</div>\
						<div class="sGroup">\
							<button class="btn btnSubmit">' + (id ? lang[17] : lang[18]) + '</button>\
						</div>\
					</form>',
                    cb: function() {
                        nIds = [];
                        $('input[name="parent"]').data(p);
                        $('#select_par').find('ul').sForm({
                            action: '/store/allCategories',
                            data: {
                                lId: lId,
                                query: $('#select_par').find('input').val(),
                                nIds: Object.keys(p || {}).join(',') + ',' + id
                            },
                            all: (r.count <= 20) ? true : false,
                            select: p,
                            s: true
                        }, $('input[name="parent"]'));
                    }
                });
            }
        }, 'json');
    }, sendCategory: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = new FormData(),
            form = $(f),
            err = null;
        form.find('.iGroup > input').css('border', '1px solid #ddd');
        data.append('id', id);
        if (!form.find('input[name="name"]').val().length) {
            form.find('input[name="name"]').css('border', '1px solid #f00');
            alr.show({
                class: 'alrDanger',
                content: lang[16],
                delay: 2
            });
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            return;
        } else {
            data.append('name', form.find('input[name="name"]').val());
            //data.append('type', form.find('select[name="type"]').val());
            //$('.mdl').find('ul').sForm('get', $('input[name="parent"]'));
            data.append('parent', Object.keys(form.find('input[name="parent"]').data()));
            $.ajax({
                type: 'POST',
                processData: false,
                contentType: false,
                url: '/store/sendCategory',
                data: data
            }).done(function(r) {
                loadBtn.stop($(event.target).find('button[type="submit"]'));
                if (r > 0) {
                    alr.show({
                        class: 'alrSuccess',
                        content: lang[8],
                        delay: 2
                    });
                    Page.get(location.href);
                    mdl.close('select_par');
                }
            });
        }
    },
    delCategory: function(id) {
        $.post('/store/delCategory', {
            id: id
        }, function(r) {
            if (r == 'OK')
                $('#invCategory_' + id).remove();
            else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                })
            }
        })
    }, sendIntroducing: function(f, event, id) {
        event.preventDefault();
        loadBtn.start($(event.target).find('button[type="submit"]'));
        var data = {},
            form = $(f);
        
        data.title = form.find('input[name="title"]').val();
        data.content = form.find('textarea[name="content"]').val();
        
        $.post('/store/save_introducing', data, function (r) {
            loadBtn.stop($(event.target).find('button[type="submit"]'));
            if (r == 'OK') {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[15],
                    delay: 2
                });
            } else {
                alr.show({
                    class: 'alrDanger',
                    content: lang[9],
                    delay: 2
                });
            }
        });  
    }
}

// Income
function income() {
    $('#income').html('<b>' + (parseFloat($('input[name="price"]').val()) - parseFloat($('input[name="purchase-price"]').val())) + '$</b>');
}

// Select all
function selectAll(name, val) {
    $('select[name="' + name + '"] > option').each(function(i, v) {
        $(v).prop('selected', val > 0 ? val : 0).trigger('change');
    })
}

// More text
function hideArea(el) {
    $(el).text($(el).text() == 'More' ? 'Less' : 'More').next().next().slideToggle('fast');
}

// Total
var total = {
    single: function(pr, t) {
		$(t).text(0);
        $(pr).each(function(i, v) {
            $(t).text((parseFloat($(t).text().replace('$', '')) + parseFloat($(v).text().replace('$', ''))).toFixed(2));
        })
        $(t).prepend('$');
    },
    fTotal: function() {
        total.single('.servPrice', '#serv_total');
        total.single('.miniServPrice', '#miniServ_total');
        total.single('.invPrice', '#inv_total');
        total.single('.purPrice', '#pur_total');
        total.single('#serv_total, #inv_total, #pur_total', '#totalPrice');
		
		$('#doit').text('$' + (parseFloat($('#totalPrice').text().replace('$', '')) * ((100 - parseFloat($('#discount').text())) / 100)).toFixed(2));
    }
}

// Printing
function to_print(a, b) {
    var modal = window.open(a, '_blank');
    modal.onload = function() {
        this.document.title = b;
        //this.print();
    }
}

// Timer
var timer = {
	time: undefined,
	tick: undefined,
	getCreate: function(a, b, c, d){
		$(a).replaceWith('<input type="time" value="'+b+'" onblur="timer.changeSeconds(this, '+c+', \''+d+'\')">');
	},
	winStart: function() {
		//timer.time = new Date(parseInt($('.timerClock').attr('value')) * 1000);
		timer.time = parseInt($('.timerClock').attr('value'));
		timer.timeStart($('.timerClock').next().find('.fa').hasClass('fa-pause'), true);	
	},
	timeStart: function(p, f) {
		var _t = timer;
		_t.tick = setTimeout(function() {
			$('.timerClock').attr('value', parseInt($('.timerClock').attr('value')) + 1);
			//_t.time.setSeconds(_t.time.getSeconds() + (p ? 1 : 0));
			_t.time += (p ? 1 : 0);
			/* h = _t.time.getUTCHours();
			m = _t.time.getUTCMinutes();
			s = _t.time.getUTCSeconds(); */
			s = _t.time % 60;
			m = (_t.time % 3600 - s) / 60;
			h = Math.abs((_t.time % 86400 - s - m * 60) / 3600);
			s = Math.abs(s);
			m = Math.abs(m);
			$('.timerClock').text(((_t.time < 0) ? '-' : '') + h + ':' + (
				m < 10 ? '0' + m : m
			) + ':' + (
				s < 10 ? '0' + s : s)
			)
			if (p) _t.timeStart(true);
		}, f ? 0 : 1000);
	}, confirm:function(e) {
		var text = '';
		switch (e) {
			case 'start':
				text = lang[156];
				side = 1;
			break;
			
			case 'pause':
				text = lang[157];
				side = 0;
			break;
			
			case 'stop':
				side = 0;
				text = lang[158];
			break;
		}
		$.post('/timer/getTime', {side: side}, function(r) {
			mdl.open({
				id: 'confirmation',
				title: 'Please, confirm',
				content: $('<div/>', {
					class: 'aCenter',
					html: $('<div/>', {
						html: text + '<br>' + r
					})
				}).append($('<div/>', {
					class: 'cashGroup',
					html: $('<button/>', {
						class: 'btn btnSubmit ac',
						onclick: 'timer.accept(\'' + e + '\');',
						html: lang[159]
					})
				}).append($('<button/>', {
						class: 'btn btnSubmit dc',
						onclick: 'mdl.close();',
						html: lang[160]
					})))
			});
		});
	}, accept: function(e) {
		var btn = $('.timerClock').next().find('.fa');
		$.post('/timer', {
			a: e, 
			id: $('.timerClock').attr('data-id')}, function(r) {
			if(r == 'IP'){
				alr.show({
					class: 'alrDanger',
					content: 'You can not login from this IP',
					delay: 2
				});
			} else if (r > 0) {
				switch(e.trim()) {
					case 'start':
						btn.removeClass('fa-play').addClass('fa-pause').attr('onclick', 'timer.confirm(\'pause\')');
						$('.timerClock').attr('data-id', r);
						timer.timeStart(true);
					break;
					
					case 'pause': 
					case 'stop': 
						btn.removeClass('fa-pause').addClass('fa-play').attr('onclick', 'timer.confirm(\'start\')');
						timer.timeStop(e);
					break;
				}
				mdl.close();
			} else if (r) {
				alr.show({
					class: 'alrDanger',
					content: r,
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		});
	}, timeStop: function(e) {
		if ($('.timerClock').attr('data-id') > 0) {
			clearTimeout(timer.tick);
			if (e == 'stop') $('.timerClock').attr('data-id', '');
		} else 
			alr.show({
				class: 'alrDanger',
				content: lang[14],
				delay: 2
			});
	}, changeSeconds: function(a, b, c){
		if(a.value){
			$(a).replaceWith('<span onclick="timer.getCreate(this, \''+a.value+'\', '+b+', \''+c+'\')">'+a.value+'</span>');
			$.post('/timer/create_time', {
				time: a.value,
				id: b,
				type: c
			}, function(j){
				if(j == 'OK'){
					$.getJSON(location.href, function(r) {
						$('#page').html(r.content);
					});
				}
			});
		}
	}, week: function() {
		if ($('#week').length)
			mdl.close('week');
		else {
			$.post('/timer/week', {}, function(r) {
				if (r) {
					mdl.open({
						id: 'week',
						width: '700px',
						class: 'weekInfo',
						title: 'Your punch card for this week',
						content: r
					});
				}
			});
		}
	}
}

// Round(plots)
function round100(n, s, p) {
	if (n % s) {
		if (p) return (n + s - n % s);
		else return (n - s - n % s);
	} else {
		return n;
	}
}

// Analytics
var analytics = {
	sales: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_stat"]').val(),
				object: Object.keys($('input[name="object_stat"]').data()).join(','),
				cash: $('input[name="pay_cash"]').val(),
				credit: $('input[name="pay_credit"]').val()
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/sales', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#stat').empty();
				makeStat.init({
					id: 'stat',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last-1) || 23, r.step, r.labels, (date ? (date[0] == date[1] ? lang[145] : lang[146]) : lang[145]) + ': '], 
					y: [minY, maxY, stepY, true, 'Sales: $'],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	}, points: function(e) {
		var f = $(e).parents('.filterCtnr'),
			data = {
				date: $('input[name="date_points"]').val(),
				object: Object.keys($('input[name="object_points"]').data()).join(','),
				user: Object.keys($('input[name="user_points"]').data()).join(',')
			};
		loadBtn.start($(e));
		date = data.date.split(' / ');
		
		$.post('/analytics/points', data, function(r) {
			loadBtn.stop($(e));
			if (r) {
				var inv = r.data,
					minY = Math.min.apply(null, inv.map(function(obj) { return obj[1]; })),
					maxY = Math.max.apply(null, inv.map(function(obj) { return obj[1]; })),
					stepY = ((maxY - minY) ? round100(parseInt((maxY - minY) / 10), 100, true) : minY) || 0;
				minY = round100(minY, stepY);
				maxY = round100(maxY, stepY, true);
				
				if (Math.abs(minY) == 'Infinity') minY = 0;
				if (Math.abs(maxY) == 'Infinity') maxY = 0;
				
				$('#eff').empty();

				makeStat.init({
					id: 'eff',
					s: 60, 
					w: 1140,
					h: 450, 
					x: [0, (r.last-1) || 23, r.step, r.labels, ((data.date && date[0] == date[1]) ? lang[145] : lang[146]) + ': '], 
					y: [minY, maxY, stepY, true, lang[115] + ': '],
					data: [
						{
							'points': inv,
							'color': '#36b1e6'
						}
					]
				}); 
				f.hide();
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
			
		}, 'json');
	}
}

function hideCalendar(e) {
	if ($(e.target).hasClass('cl')) $(e.target).parents('.filterCtnr').find('.calendar').hide();
}
$('body').on('click', function(e) {
	if (!$(e.target).hasClass('cl')) $('.calendar').hide();
});


function $_GET(param) {
	var result =  window.location.search.match(
		new RegExp("(\\?|&)" + param + "(\\[\\])?=([^&]*)")
	);
	return result ? result[3] : false;
}

// Unconfirmed
var dashbord = {
	uncofirmed: function() {
		$.post('/main/uncofirmed', {}, function(r) {
			$('#conf_services').html(r.services);
			$('#conf_inventory').html(r.inventory);
			$('#conf_discounts').html(r.discounts);
		}, 'json')
	}, drops: function() {
		$.post('/main/drops', {}, function(r) {
			$('#drops').html(r);
		})
	}, confirm_drop: function(id, a, el) {
		var data = {
			id: id,
			action: a
		};
		$.post('/cash/confirm_drop', data, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Drop was successfully ' + lang[a],
					delay: 2
				});
				if (a == 'confirm') {
					$(el).removeClass('fa-check').removeClass('gr').addClass('fa-times').addClass('rd').text(' Unconfirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'unconfirm\', this);');
					$('#drop_' + id).removeClass('unconfirmed').addClass('confirmed').appendTo($('#drops .tBody'));
				} else {
					$(el).removeClass('fa-times').removeClass('rd').addClass('fa-check').addClass('gr').text(' Confirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'confirm\', this);');
					$('#drop_' + id).removeClass('confirmed').addClass('unconfirmed').prependTo($('#drops .tBody'));
				}
			} else if (r == 'ERR') {
				alr.show({
					class: 'alrDanger',
					content: 'You have no access to do this',
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[13],
					delay: 2
				});
			}
		})
	}
}
var Languages = {
	save: function(form, event){
		var form = $(form).serializeArray();
		event.preventDefault();
		$.post('/settings/saveLang', form, function(r){
			if (r == 'OK'){
				alr.show({
					class: 'alrSuccess',
					content: lang[1],
					delay: 2
				});
			} else {
				alr.show({
					class: 'alrDanger',
					content: lang[1],
					delay: 2
				});
			}
		});
	},
	get: function(a){
		$.post('/settings/getLang', {
			lang: a
		}, function(a){
			$('select[name="module"]').html(a).next().remove().parent().select();
		});
	},
	getModule: function(a){
		var exp = a.split('_');
		$.post('/settings/getLangModule', {
			lang: $('select[name="lang"]').val(),
			type: exp[0],
			module: exp[1]
		}, function(r){
			$('#phrases').html(r);
		});
	}
}