<div id="product">
<ul>
<% for( var i = 0 ; i != list.length ; ++i ){ %>

	<% var start_status = '';if (list[i].start == 1) {start_status = '众筹中';} else if (list[i].start == 3) {start_status = '待开始';} else if (list[i].start == 0) {start_status = '众筹结束';} %>

	<a href='javascript:;' name='detail' chips_id='<%- list[i].chips_id %>'>
		<li>
			<div class='chips'>
				<div class='chips_start' start='<%- list[i].start %>'><%- start_status %></div>
				<div class='icon'>
					<img src='<%- list[i].icon %>' alt='' />
				</div>
				<div class='product_info'>
					<p class='p1'><%- list[i].product_title %></p>

						
													<% if(list[i].isDetail){ %>
									<p class='p2'>
										原 始 价 格：<span class='span1'><%- list[i].oldprice %></span>
									</p>
									<p class='p2'>
										已 筹 数 量：<span class='span2'><%- list[i].num %></span>
									</p>
									<p class='p2'>
										现 在 价 格：<span class='span2'><%- list[i].newprice %></span>
									</p>
									<p class='p2'>
										参 与 人 数：<span class='span2'><%- list[i].person %>人</span>
									</p>
									<% } else { %>
										<p class='p2'>
											已 筹 数 量：<span class='span1'><%- list[i].num %></span>
										</p>
										<p class='p2'>
											剩 余 时 间：<span class='span2'><%- list[i].distime %>天</span>
										</p>


										<% } %>	
						
						
				</div
			</div>
		</li>
	</a>
<% } %>
</ul>
</div>




