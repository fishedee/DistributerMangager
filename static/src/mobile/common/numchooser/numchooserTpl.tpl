<div class="common_numchooser" id="<%- id %>">
	<span class="tip1"><%- tip1 %></span><span class="decrease" onclick="<%= $.func.invoke(decreaseClick) %>">-</span><input type="text" value="<%- quantity %>"/><span class="increase" onclick="<%= $.func.invoke(increaseClick) %>">+</span><span class="tip2"><%- tip2 %></span>
</div>