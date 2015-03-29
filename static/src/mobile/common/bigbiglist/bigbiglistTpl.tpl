<ul class="common_bigbiglist">
	<% for( var i in list ){ %>
		<a href="<%- list[i].link %>">
			<li data="<%- i %>">
				<div class="img"><img src="<%- list[i].image %>"/></div>
				<h1 class="text"><%- list[i].text %></h1>
			</li>
		</a>
	<% } %>
</ul>