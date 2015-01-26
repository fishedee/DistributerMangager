<div id="common_header">
	<a href="<%= backLink %>">
		<button class="backicon"></button>
	</a>
	<% for( var i in button ){ %>
	<a href="<%= button[i].link %>">
		<button class="<%= button[i].name %>icon"></button>
	</a>
	<% } %>
	<p class="title"><%= title %></p>
</div>
<div id="common_header_padding"></div>