function downapp(){
	return box.open({
		title: 'Please download app',
		content: '<div class="ca-apps"><a href="https://play.google.com/store/apps/details?id=com.caapp" target="_blank"><i class="fa fa-android"></i>Ca App</a><a href="#"><i class="fa fa-apple" style="color: #777"></i>Ca App</a></div>'
	});
}

$.fn.select2 = function(a){
	var _t = this,
		m = _t[0].hasAttribute('multiple'),
		j = _t.attr('data-json'),
		_r = _t.attr('data-res'),
		l = j.search(/[\[\]\}\{]/) != -1;
	_t.data('list', a.data || (l ? JSON.parse('['+j.replace(/\[(.*)\]/i, '$1')+']') : []));
	_t.data('value', []);

	var d = _t.data().list, s = d.length > 10;
		
	_t.removeAttr('data-json');
	
	this.addClass('select').html('<div class="wrap"><input type="search" placeholder="Waiting..."></div><ul></ul>');
	
	var ul = this.find('ul'), wrap = this.find('.wrap'), inp = wrap.find('input');

	function add(a){
		
		if(a.length > 10){
			inp.removeAttr('readonly').attr('placeholder', 'Search...');
		} else {
			inp.attr('readonly', true);
			inp.attr('placeholder', 'Not selected');
		}
		
		ul.empty();
		
		for(var k in a){
			$('<li>'+a[k].name+'</li>').data(a[k]).appendTo(ul);
		}
		
		ul.find('li').mousedown(function(){
			var _i = this, data = $(_i).data();
			if(m){
				_t.data().value.push(data.id);
				$('<p>'+data.name+'<i></i></p>').prependTo(wrap).click(function(){
					$(this).remove();
					$(_i).show();
				});
				$(_i).hide();
			} else {
				_t.data('value', data.id);
				inp.val(data.name);
				ul.hide();
				wrap.removeClass('open');	
			}
		});
	}
	
	if(!d.length && !l && j.length){
		$.getJSON(j, function(r){
			d = _r ? r[_r] : r;
			add(d);
		});
	} else
		add(d);
	
	wrap.mousedown(function(e){
		if(e.target.tagName == 'INPUT' && !e.target.hasAttribute('readonly')){
			ul.show();
			wrap.addClass('open');
		} else if(e.target.tagName != 'P' && e.target.tagName != 'I'){
			ul.toggle();
			wrap.toggleClass('open');
		}
	});
	
	$(window).mousedown(function(e){
		if(!$(e.target).parents('.select').length){
			ul.hide();
			wrap.removeClass('open');
		}
	});
};