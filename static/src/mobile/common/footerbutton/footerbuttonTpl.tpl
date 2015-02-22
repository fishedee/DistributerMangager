<div id="common_footerbutton_padding"></div>
<ul id="common_footerbutton">
	<% for( var i in list ){ %>
	<% var width = 1/list.length * 100 ; %>
		<li data="<%- i %>" class="<%- list[i].name %>" style="width:<%- width %>%;" onclick="<%= $.func.invoke(list[i].click) %>">
			<span><%- list[i].text %></span>
		</li>
	<% } %>
</ul>