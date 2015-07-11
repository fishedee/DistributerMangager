var $ = require('../core/core.js');

function password() {
	args = {};
	args.template = __inline('passwordTpl.tpl');
	var el = args.template();

	function set() {
		
		//清空密码
		$('.close').click(function() {
			$('#chips_password').val(null);
		});
		
		$('html').not('.Bomb_box').click(function () {
			$('.security_Mailbox_bd').hide();
		});
		$('.security_Mailbox_bd').click(function (event) {
			event.stopPropagation();
		});
		$('a[name=detail]').click(function (event) {
			if ($('.security_Mailbox_bd').is(':visible')) {
				$('.security_Mailbox_bd').hide();
				return false;
			} else {
				event.stopPropagation();
			}

			var chips_id = $(this).attr('chips_id');
			$('input[name=chips_input_id]').val(chips_id);

			//判断密码
			$.ajax({
				url: '/chipspower/judge',
				type: 'POST',
				dataType: 'JSON',
				data: {
					chips_id: chips_id
				},
				success: function (data) {
					// console.info(data);
					if (data == 0) {
						$('.security_Mailbox_bd').show();
					} else {
						window.location.href = 'detail.html?chips_id=' + chips_id + '&password=' + data;
					}
				},
				error: function (XMLResponse) {
					alert(XMLResponse.responseText);
				}
			})
		});

		//点击提交密码
		$('.sub_a').click(function () {
			var password = $('#chips_password').val();
			var chips_id = $('input[name=chips_input_id]').val();
			$.ajax({
				url: '/chips/checkpassword',
				type: 'POST',
				dataType: 'JSON',
				data: {
					chips_id: chips_id,
					password: password
				},
				success: function (data) {
					if (data != 0) {
						window.location.href = 'detail.html?chips_id=' + chips_id + '&password=' + data;
					} else {
						alert('密码错误');
					}
				},
				error: function (XMLResponse) {
					alert(XMLResponse.responseText);
				}
			})
		});
	}
	return {
		el: el,
		set: set
	}
}
return password;