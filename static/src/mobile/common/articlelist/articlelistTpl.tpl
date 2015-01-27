<ul id="common_articlelist">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<li data="<%= i %>">
			<a href="<%= list[i].link %>">
				<h1><%= list[i].title %></h1>
				<img src="<%= list[i].image %>"/>
				<p><%= list[i].summary %></p>
				<span>¸ü¶à</span>
			</a>
		</li>
	<% } %>
</ul>