<ul class="common_metro">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<a href="<%= list[i].link %>" >
			<li data="<%= i %>" style="width:<%= list[i].size %>;">
				<div class="container" style="background:<%= list[i].color %>;">
					<span class="icon"><img src="<%= list[i].icon %>"/></span>
					<span class="text"><%= list[i].title %></span>
				</div>
			</li>
		</a>
	<% } %>
</ul>