<div id="common_banner">
	<ul class='swipe images' id='common_banner_images'>
		<div class='swipe-wrap wrapper'>
			<% for( var i = 0 ; i != images.length ; ++i ){ %>
				<li data="<%- i %>">
					<a href="<%- images[i].link %>">
						<img src="<%- images[i].image %>"/>
					</a>
				</li>
			<% } %>
		</div>
	</ul>
	<ul class="points">
		<% for( var i = 0 ; i != images.length ; ++i ){ %>
			<li data="<%- i %>">
			</li>
		<% } %>
	</ul>
</div>