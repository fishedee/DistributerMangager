<ul class="common_buttonlist">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<a href="<%- list[i].link %>">
			<li data="<%- i %>"><%- list[i].name %></li>
		</a>
	<% } %>
</ul>