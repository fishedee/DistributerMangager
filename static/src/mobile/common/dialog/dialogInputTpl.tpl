<div id="common_dialog_input">
	<div class="container">
		<h1>您的信息</h1>
		<div class="item">
			<label>名字：</label><input type="text" name="name" placeholder="请在这里输入名字" value="<%- name %>"/>
		</div>
		<div class="item">
			<label>手机：</label><input type="text" name="phone" placeholder="请在这里输入11位手机号码" value="<%- phone %>"/>
		</div>
		<div class="button">
			<button class="confirm" onclick="<%- $.func.invoke(confirmClick) %>">确认</button>
			<button class="cancel" onclick="<%- $.func.invoke(cancelClick) %>">取消</button>
		</div>
	</div>
</div>