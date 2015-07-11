<div class="swiper-container">
	<div class="swiper-wrapper">	
<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<div class="swiper-slide">
			<img src="<%- list[i].img %>" alt="">
			<span class="banner_title"><%- list[i].title %></span>
		</div>
<% } %>
	</div>
</div>

