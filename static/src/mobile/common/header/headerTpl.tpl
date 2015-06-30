<div id="common_header">
	<a data-href="<%= backLink %>" onclick="history.back();">
		<button class="backicon"></button>
	</a>
	<% for( var i in button ){ %>
	<a href="<%= button[i].link %>">
		<button class="<%= button[i].name %>icon"></button>
	</a>
	<% } %>
</div>
<div id="common_header_padding"></div>
