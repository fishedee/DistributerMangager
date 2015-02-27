<div class="common_itemlistwithnum" id="<%- id %>">
	<h2><%- title %></h2>
	<ul class="items">
		<% for( var i in item ){ %>
			<li data="<%- i %>">
				<div class="info" onclick="<%= $.func.invoke(item[i].checkClick) %>">
					<div class="checked"></div>
					<%= item[i].itembriefEl %>
				</div>
				<div class="numinfo">
					<div class="del" onclick="<%= $.func.invoke(item[i].delClick) %>">删除</div>
					<%= item[i].numchooserEl %>
				</div>
			</li>
		<% } %>
	</ul>
</div>