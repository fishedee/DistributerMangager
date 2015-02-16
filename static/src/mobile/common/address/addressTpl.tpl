<ul id="common_address">
	<li class="name">
		<span>姓名：</span>
		<div class="input"><input type="text" value="<%- name %>"></div>
	</li>
	<li class="province">
		<span>省份：</span>
		<div class="input"><%= provinceSelectListEl %></div>
	</li>
	<li class="city">
		<span>城市：</span>
		<div class="input"><%= citySelectListEl %></div>
	</li>
	<li class="address">
		<span>地址：</span>
		<div class="input"><input type="text" value="<%- address %>"></div>
	</li>
	<li class="phone">
		<span>手机号码：</span>
		<div class="input"><input type="text" value="<%- phone %>"></div>
	</li>
	<li class="payment">
		<span>支付方式：</span>
		<div class="input"><%= paymentSelectListEl %></div>
	</li>
</ul>