<div id="common_itemdialog">
	<div class="dialog">
		<div class="cacnelClick" onclick="<%- $.func.invoke(cancelClick) %>">×</div>
		<%= itembriefwithnum %>
		<div class="confirmClik <%- confirmName %>" onclick="<%- $.func.invoke(confirmClick) %>"><span><%- confirmText %></span></div>
	</div>
</div>