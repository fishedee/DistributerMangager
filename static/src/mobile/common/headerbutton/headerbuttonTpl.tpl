<ul class="common_headerbutton">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
	<% var width = (1/list.length)*100; %>
		<li style="width:<%- width %>%" data="<%- i %>" onclick="<%= $.func.invoke(list[i].click) %>">
			<p class="number"><%- list[i].number %></p>
			<p class="name"><%- list[i].name %></p>
		</li>
	<% } %>
</ul>