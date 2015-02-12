<div class="common_itembriefwithnum" id="<%- id %>">
	<%= itembrief %>
	<div class="quantity">
		<span class="tip1">请选择数量：</span>
		<span class="decrease" onclick="<%= $.func.invoke(decreaseClick) %>">-</span><input type="text" value="<%- quantity %>"/><span class="increase" onclick="<%- $.func.invoke(increaseClick) %>">+</span>
		<span class="tip2">件</span>
	</div>
</div>