<ul class="common_biglist">
	<% for( var i in list ){ %>
		<a href="<%- list[i].link %>">
			<li data="<%- i %>">
				<%= list[i].itembrief %>
			</li>
		</a>
	<% } %>
</ul>