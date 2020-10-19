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
		$('input[name="total"]').val((price * $('input[name="quantity"]').val()).toFixed(2));
		$('input[name="proceeds"]').val((parseFloat($('input[name="sale"]').val()) - parseFloat($('input[name="price"]').val())).toFixed(2));
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
					var price = $(r).find('#mm-saleDscPrc').length ? $(r).find('#mm-saleDscPrc').text().trim() : $(r).find('#prcIsum').text().trim();
                    $(r).find('#itemTitle').find('span').remove();
                    $(el).parents('.uForm').find('input[name="name"]').val($(r).find('#itemTitle').text().replace('Details about', '').trim());
                    $(el).parents('.uForm').find('input[name="price"]').val(price.replace(',', '').replace('$', '').replace('US', '').trim());
                    $(el).parents('.uForm').find('input[name="quantity"]').val(1);
                    $(el).parents('.uForm').find('input[name="total"]').val($(r).find('#prcIsum').text().trim().replace(/\D(\.?)\D/ig, ''));
                    $(el).parents('.uForm').find('input[name="estimated"]').val($(r).find('#delSummary .vi-acc-del-range > b').text().trim() ? 'Between ' + $(r).find('#delSummary .vi-acc-del-range > b').text().trim() : '');
                    $(el).parents('.uForm').find('input[name="tracking"]').val($(r).find('#descItemNumber').length ? $(r).find('#descItemNumber').text().trim() : '');
                    if ($(r).find('#icImg').attr('src')) {
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
            if (!(form[i].value.length || $('input[name="' + form[i].name + '"]').data()) && $.inArray(form[i].name, ['comment', 'customer', 'tracking', 'ship-tracking', 'estimated']) < 0 && form[i].name.length) {
                err = lang[101] + form[i].name + lang[102];
                $('input[name="' + form[i].name + '"]').css('border-color', '#f00');
                break;
            } else if ($('input[name="' + form[i].name + '"]').val() <= 0 && form[i].name == 'sale') {
				err = 'Please, enter sale price';
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
            if (r == 'min_price') {
				alr.show({
                    class: 'alrDanger',
                    content: 'Min sale price can not be less than $' + parseFloat(min_price($('[name="price"]').val().replace(/(\D*)/i, ''))).toFixed(2),
                    delay: 2
                });
			} else if (r > 0) {
                alr.show({
                    class: 'alrSuccess',
                    content: lang[8],
                    delay: 2
                });
                if (!id) Page.get('/purchases/edit/' + r);
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