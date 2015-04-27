<ul class="common_luckylist" id="<%- id %>">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<a href="javascript:void(0);">
			<li data="<%- i %>">
				<div class="img"><img src="<%- list[i].image %>"/></div>
				<div class="info">
					<div class="title"><span class="tip">标题：</span><span class="text"><%- list[i].title %></span></div>
					<div class="name"><span class="tip">名字：</span><span class="text"><%- list[i].name %></span></div>
					<div class="phone"><span class="tip">手机：</span><span class="text"><%- list[i].phone %></span></div>
				</div>
			</li>
		</a>
	<% } %>
</ul>