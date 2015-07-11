var $ = require('../core/core.js');

function book(data) {
	var template = __inline('bookTpl.tpl');
	var el = template();

	function set() {
		// + 
		$('.add').click(function () {
			var num = $('input[name=num]').val();
			var new_num = parseInt(num) + 1;
			$('input[name=num]').val(new_num);
		});
		// - 
		$('.dis').click(function () {
			var num = $('input[name=num]').val();
			var new_num = parseInt(num) - 1;
			if (new_num <= 0) {
				$('input[name=num]').val(num);
			} else {
				$('input[name=num]').val(new_num);
			}
		});

		//更改数量
		$('input[name=num]').change(function () {
			var new_num = $(this).val();
			var old_num = $('input[name=oldnum]').val();
			if (!isNaN(new_num)) {
				$(this).val(new_num);
				$('input[name=oldnum]').val(new_num);
			} else {
				$(this).val(old_num);
			}
		});
	}

	function set2() {
		// + 
		$('.num').on('click', '.add', function () {
			var num = $('input[name=num]').val();
			var new_num = parseInt(num) + 1;
			$('input[name=num]').val(new_num);
			$('input[name=oldnum]').val(new_num);
			var free = $('input[name=num]').val() * chips['newprice'] * chips['percent'] * 0.01;
			$('#order_firstpay_num').text(free.toFixed(2));
		});
		// - 
		$('.num').on('click', '.dis', function () {
			var num = $('input[name=num]').val();
			var new_num = parseInt(num) - 1;
			if (new_num <= 0) {
				$('input[name=num]').val(num);
				$('input[name=oldnum]').val(num);
			} else {
				$('input[name=num]').val(new_num);
				$('input[name=oldnum]').val(new_num);
			}
			var free = $('input[name=num]').val() * chips['newprice'] * chips['percent'] * 0.01;
			$('#order_firstpay_num').text(free.toFixed(2));
		});
		//更改数量
		$('.num').on('change', 'input[name=num]', function () {
			var new_num = $(this).val();
			var old_num = $('input[name=oldnum]').val();
			if (!isNaN(new_num)) {
				if (new_num == '') {
					new_num = 1;
				}
				$(this).val(new_num);
				$('input[name=oldnum]').val(new_num);
				var free = $('input[name=num]').val() * chips['newprice'] * chips['percent'] * 0.01;
				$('#order_firstpay_num').text(free.toFixed(2));
			} else {
				$(this).val(old_num);
			}
		});
	}
	return {
		el: el,
		set: set,
		set2: set2
	}
}
return book;