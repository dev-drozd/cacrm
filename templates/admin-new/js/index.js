// Include language file
$.getJSON('/language/'+_user.lang+'/admin/javascript.json', function(r){
	window.lang = r;
});

var loadBtn = {
    class: '',
    start: function(e) {
        loadBtn.class = e.attr('disabled', true).find('span').attr('class');
        e.find('span').removeClass(loadBtn.class).addClass('fa fa-refresh fa-spin');
    },
    stop: function(e) {
        e.attr('disabled', false).find('span').removeClass('fa fa-refresh fa-spin').addClass(loadBtn.class);
    }
};

function round100(n, s, p) {
	if (n % s) {
		if (p) return (n + s - n % s);
		else return (n - s - n % s);
	} else {
		return n;
	}
}