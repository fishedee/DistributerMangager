<ul class="common_deallist" id="<%- id %>">
	<% for( var i = 0 ; i != list.length ; ++i ){ %>
		<a href="<%- list[i].link %>">
			<li data="<%- i %>">
				<div class="img"><img src="<%- list[i].image %>"/></div>
				<div class="info">
					<div class="id"><span class="tip">订单号：</span><span class="text"><%- list[i].id %></span></div>
					<div class="state"><span class="tip">订单状态：</span><span class="text"><%- list[i].state %></span></div>
					<div class="price"><span class="tip">订单金额：</span><span class="text">￥<%- (list[i].price /100 ).toFixed(2)%></span></div>
				</div>
			</li>
		</a>
	<% } %>
</ul>