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