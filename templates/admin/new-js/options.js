/**
 * @Appointment Options
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/


var Options = {
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
                        onclick: 'Options.addSel(this); return false;',
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
            ondragover: 'Options.dragover(event)',
            ondragstart: 'Options.dragstart(event)',
            ondragend: 'Options.dragend(event)',
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
            ondragover: 'Options.dragover(event)',
            ondragstart: 'Options.dragstart(event)',
            ondragend: 'Options.dragend(event)',
            onmousedown: '$(this).parent().addClass(\'drag\');',
            onmouseup: '$(this).parent().removeClass(\'drag\');'
        })));
    },
    dragstart: function(e) {
        Options.clickEl = e.target;
        Options.dragEl = e.target.parentNode;
        Options.parentEl = Options.dragEl.parentNode;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('Text', Options.dragEl.textContent);
        return false;
    },
    dragend: function(e, cb) {
        e.preventDefault();
        Options.dragEl.classList.remove('drag');
        if (cb) cb.apply(e.target);
        return false;
    },
    dragover: function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var target = e.target;
        if (target && target !== Options.clickEl && target.className == Options.clickEl.className) {
            if (target.parentNode.nextSibling == Options.dragEl)
                Options.parentEl.insertBefore(Options.dragEl, target.parentNode);
            else
                Options.parentEl.insertBefore(Options.dragEl, target.parentNode.nextSibling);
        }
    }
};