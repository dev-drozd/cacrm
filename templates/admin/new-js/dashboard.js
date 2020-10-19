/**
 * @Appointment Dashboard
 * @Author      Alexandr Drozd, Youlia Design
 * @Copyright   Copyright Your Company 2020
 * @Link        https://yoursite.com/
 * This code is copyrighted
*/

var Dashboard = {
	tasks: function() {
		$.post('/main/tasks', {}, function(r) {
			$('#tbl_tasks').html(r);
			$('#tbl_tasks').parent().next().remove();
		})
	},
	appointments: function() {
		$.post('/main/appointments', {}, function(r) {
			$('#tbl_appointments').html(r);
			$('#tbl_appointments').parent().next().remove();
		})
	},
	uncofirmed: function() {
		$.post('/main/uncofirmed', {}, function(r) {
			$('#conf_services').html(r.services);
			$('#conf_inventory').html(r.inventory);
			$('#conf_discounts').html(r.discounts);
			$('#conf_warranty').html(r.warranty);
			$('#conf_services').parent().next().remove();
			$('#conf_inventory').parent().next().remove();
			$('#conf_discounts').parent().next().remove();
			$('#conf_warranty').parent().next().remove();
			/* $('body').hntJS({
				left: $('.hntJS').offset().left
			}); */
		}, 'json')
	}, drops: function() {
		$.post('/main/drops', {}, function(r) {
			$('#drops').html(r);
			$('#drops').next().remove();
		})
	}, onsite: function() {
		$.post('/main/onsite', {}, function(r) {
			$('#onsite_list').html(r.list);
			$('#onsite_details_list').html(r.details_list);
			$('#onsite_invoices_list').html(r.invoices_list);
			$('#onsite_uncofirmed_list').html(r.uncofirmed_list);
			$('#onsite_list').parent().next().remove();
			$('#onsite_details_list').parent().next().remove();
			$('#onsite_invoices_list').parent().next().remove();
			$('#onsite_uncofirmed_list').parent().next().remove();
		}, 'json');
	}, issues: function() {
		$.post('/main/new_issues', {}, function(r) {
			$('#issues_list').html(r.issues);
			//$('#store_issues_list').html(r.store_issues);
			//$('#issues_list').parent().next().remove();
			//$('#store_issues_list').parent().next().remove();
		}, 'json');
	}, issue_transfer: function() {
		$.post('/main/issue_transfer', {}, function(r) {
			$('#issue_transfer_list').html(r);
			$('#issue_transfer_list').parent().next().remove();
		});
	}, my_issues: function() {
		$.post('/main/my_issues', {}, function(r) {
			$('#my_issues_list').html(r);
			$('#my_issues_list').parent().next().remove();
		});
	}, object_issues: function() {
		$.post('/main/store_issues', {}, function(r) {
			$('#object_issues_list').html(r);
			$('#object_issues_list').parent().next().remove();
		});
	}, timer: function() {
		$.post('/main/timer', {}, function(r) {
			$('#timers_list').html(r);
			$('#timers_list').parent().next().remove();
		});
	}, cash_stat: function() {
		$.post('/main/cash_stat', {}, function(r) {
			$('#cash_stat_list').html(r);
			$('#cash_stat_list').parent().next().remove();
		});
	}, feedbacks: function() {
		$.post('/main/feedbacks', {}, function(r) {
			$('#feedbacks_list').html(r);
			$('#feedbacks_list').parent().next().remove();
		});
	}, purchases: function() {
		$.post('/main/purchases', {}, function(r) {
			$('#tbl_purchases').html(r.purchases);
			$('#tbl_purchases').parent().next().remove();
		}, 'json');
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
				$('#drop_' + id).remove();
				/* if (a == 'confirm') {
					$(el).removeClass('fa-check').removeClass('gr').addClass('fa-times').addClass('rd').text(' Unconfirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'unconfirm\', this);');
					$('#drop_' + id).removeClass('unconfirmed').addClass('confirmed').appendTo($('#drops .tBody'));
				} else {
					$(el).removeClass('fa-times').removeClass('rd').addClass('fa-check').addClass('gr').text(' Confirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'confirm\', this);');
					$('#drop_' + id).removeClass('confirmed').addClass('unconfirmed').prependTo($('#drops .tBody'));
				} */
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
	},
	confirmDropObject: function(id, el) {
		var data = {
			id: id
		};
		$.post('/cash/confirm_drop_object', data, function(r) {
			if (r == 'OK') {
				alr.show({
					class: 'alrSuccess',
					content: 'Drop was successfully confirmed',
					delay: 2
				});
				$('#drop_object_'+id).html('Confirmed');
				/* if (a == 'confirm') {
					$(el).removeClass('fa-check').removeClass('gr').addClass('fa-times').addClass('rd').text(' Unconfirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'unconfirm\', this);');
					$('#drop_' + id).removeClass('unconfirmed').addClass('confirmed').appendTo($('#drops .tBody'));
				} else {
					$(el).removeClass('fa-times').removeClass('rd').addClass('fa-check').addClass('gr').text(' Confirm').attr('onclick', 'dashbord.confirm_drop(' + id + ', \'confirm\', this);');
					$('#drop_' + id).removeClass('confirmed').addClass('unconfirmed').prependTo($('#drops .tBody'));
				} */
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