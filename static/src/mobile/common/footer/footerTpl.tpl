<div id="common_footer_padding"></div>
<ul id="common_footer">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
	<% 	var length=  1/list.length*100; %>
		<a href="<%- list[i].link %>">
			<li class="<%- list[i].name %>" state="<%- list[i].state %>" style="width:<%= length %>%">
				<span class="icon"></span>
				<span class="text"><%- list[i].text %></span>
			</li>
		</a>
	<% } %>
</ul>