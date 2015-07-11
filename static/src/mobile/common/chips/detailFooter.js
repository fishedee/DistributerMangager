/*
 *@require detailFooter.less
 */

var $ = require('../core/core.js');

function detailFooter(name) {
	var template = __inline('detailFooterTpl.tpl');
	var el = template(name);

	function set() {
		//提交
		$("#common_footerbutton").on('click', 'a', function () {
			$.ajax({
				url: '/chips/chipsStart',
				type: 'POST',
				dataType: 'JSON',
				data: {
					chips_id: chips_id
				},
				success: function (data) {
					if (data == 1) {
						window.location.href = 'confirm.html?chips_id=' + chips_id;
					} else {
						alert('活动不在可众筹阶段');
						return false;
					}
				},
				error: function (XMLResponse) {
					alert(XMLResponse.responseText);
				}
			})
		});
	}

	function set2(chips) {
		//提交
		$('#common_footerbutton').on('click', 'a', function () {
			window.location.href = 'address.html?chips_id=' + chips['chips_id'] + '&num=' + $('input[name=num]').val();
		});
	}
	return {
		el: el,
		set: set,
		set2: set2,
	}
}
return detailFooter;