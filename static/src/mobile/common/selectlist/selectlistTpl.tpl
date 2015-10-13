<div class="common_selectlist" id="<%- id %>" >
	<select onchange="<%= $.func.invoke(change) %>">
		<% for( var i = 0 ; i != list.length ; ++i ){ %>
			<% if( list[i].value == value ){ %>
				<option value ="<%- list[i].value %>" selected="selected"><%- list[i].name %></option>
			<% }else{ %>
				<option value ="<%- list[i].value %>"><%- list[i].name %></option>
			<% } %>
		<% } %>
	</select>
	<span class="downicon"></span>
</div>