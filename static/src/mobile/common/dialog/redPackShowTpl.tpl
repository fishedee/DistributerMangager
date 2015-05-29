<div id="common_redPackShow">
	<div class="container">
		<img src="<%- image %>"/>
		<div class="shop"><%- shop %></div>
		<div class="num"><%- num %></div>
		<div class="get" onclick="<%- $.func.invoke(nextClick) %>"></div>
		<div class="rule" onclick="<%- $.func.invoke(ruleClick) %>"></div>
	</div>
</div>