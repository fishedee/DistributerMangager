<div class="common_itembrief">
	<div class="img"><img src="<%- image %>"/></div>
	<div class="info">
		<h1><%- title %></h1>
		<% if( _.isUndefined(this.summary) == false ){ %>
			<p><%- summary %></p>
		<% } %>
		<div class="price">价格：<span class="current">￥<%- price %></span><% if( _.isUndefined(this.oldprice) == false ){ %><span class="old">￥<%- oldprice %></span><% } %></div>
		<% if( _.isUndefined(this.stock) == false ){ %>
			<div class="stock">库存：<span class="current"><%- stock %></span></div>
		<% } %>
	</div>
</div>