<div id="common_banner">
	<ul class="images">
		<% for( var i = 0 ; i != images.length ; ++i ){ %>
			<li data="<%- i %>">
				<a href="<%- images[i].link %>">
					<img src="<%- images[i].image %>"/>
				</a>
			</li>
		<% } %>
	</ul>
	<ul class="points">
		<% for( var i = 0 ; i != images.length ; ++i ){ %>
			<li data="<%- i %>">
			</li>
		<% } %>
	</ul>
</div>