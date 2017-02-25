jQuery(document).ready(function($) {

	function ajax_admin_search_update_search() {
		s = a.val().replace(' ', '+');
		var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < url.length; i++) {
			if (/(^s$)|(\bs=.*\b)|(\bs=)/g.test(url[i]) === true || /http.*/g.test(url[i]) === true) y = i;
		}
		if (typeof y === 'undefined') url.unshift('s='+s);
		else url[y] = 's='+s;
		url = url.join('&');
		url = window.location.pathname+'?'+url;
		c.addClass('loading');
		a.addClass('loading');

		$.get(url, {}, function(data) {
			var r = $('<div />').html(data);
			var table = r.find(z);
			var tablenav_top = r.find(tnt);
			var tablenav_bottom = r.find(tnb);
			$(z).html(table);
			$(tnt).html(tablenav_top);
			$(tnb).html(tablenav_bottom);
		},'html');

		$(document).ajaxStop(function() {
			c.removeClass('loading');
			a.removeClass('loading');
			if(s.length) {
				history.pushState({}, "after search", url);
			} else {
				history.pushState({}, "empty search", url);
			}
		});

	}

	$(function() {
		a = $('input[type="search"]');
		b = a.parent();
		b.css('position', 'relative');
		b.prepend('<div class="loading-gif"></div>');
		c = $('.loading-gif');
		t = a.closest('form').find('table');
		if(!t.length) t = a.closest('div').find('table');
		if(!t.length) return;
		z = '.'+t.attr('class').replace(/\s/g, '.');
		tn = '.top .displaying-num';
		bn = '.bottom .displaying-num';
		tpl = '.top span.pagination-links';
		bpl = '.bottom span.pagination-links';
		tnt = '.tablenav.top';
		tnb = '.tablenav.bottom';
		var timer;
		a.on('keyup', function(event) {
			if (timer) clearTimeout(timer);
			timer = setTimeout(ajax_admin_search_update_search, 300);
		});
	});
});