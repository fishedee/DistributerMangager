/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var App = React.createClass({

  getInitialState: function() {
      // var dishId = $.location.getQueryArgv('dishId');

  
    return {

      data:null,
      roomName:null,
      // listState 0 进行中，1 已结账
      listState:0,

    };

  },


  componentDidMount : function() {


  $.get('/clientlogin/islogin', {}, function(data) {
      if (data.code != 0) {
        location.href = $.url.buildQueryUrl('/clientlogin/wxlogin', {
          callback: location.href
        });
        return;
      }

    
     $.get('/dishorder/myOrder',{}, function(data) {
        if (data.code == 0) {
          this.setState({
            data: data.data,
            roomName:data.data.roomName,
          },function(){
             this.listData();
          });


          

        }else{
          alert(data.msg);
        }

      }.bind(this));




  }.bind(this));




  },

listData:function(){
  console.log(this.state.data);
 
 var list = [];
 var foreachState = null;
  $.each(this.state.data.orderInfo, function(index, val) {
    var listState = this.state.listState;
    var typeName = null;
    var stateName = null;

//订单类型  type:1点餐 2:堂食 3:外卖
    switch(val.type){
      case '1':
        typeName = '点餐';
        break;
      case '2':
        typeName = '预约';
        break;
      case '3':
        typeName = '外卖';
        break;
    }

    //订单状态  state 0：未受理 1：已受理 2：已关闭 3：已取消 4：已结账

    switch(val.state){
      case '0':
        stateName = '未受理';
        break;
      case '1':
        stateName = '已受理';
        break;
      case '2':
        stateName = '已关闭';
        break;
      case '3':
        stateName = '已取消';
        break;
      case '4':
        stateName = '已结账';
        break;
    }

    if( (listState == 0) && (val.state == 0 || val.state == 1 )){

     list.push(

        <a href={'mealOrderDetailed.html?orderNo='+val.orderNo}>
              <div className="item">
                      <div className="dingDang">
                          <span>{'订单号：'+val.orderNo}</span>
                          <span>{'保存时间:'+val.createTime}</span>
                      </div>
                      <hr/>
                      <div className="didian">{this.state.roomName}</div>
                      <div className="weizhi">{typeName}</div>
                      <hr/>
                      <div className="zongJia">总价：
                          <span className="qian">￥{val.price}</span>
                      </div>
                      <div className="zhuangTai">{stateName}</div>
                  </div>
              </a>
        );

    }

   if( (listState == 1) && (val.state == 2 || val.state == 3 || val.state == 4 )){
           list.push(
        <a href={'mealOrderDetailed.html?orderNo='+val.orderNo}>
              <div className="item">
                      <div className="dingDang">
                          <span>{'订单号：'+val.orderNo}</span>
                          <span>{'保存时间:'+val.createTime}</span>
                      </div>
                      <hr/>
                      <div className="didian">{this.state.roomName}</div>
                      <div className="weizhi">{typeName}</div>
                      <hr/>
                      <div className="zongJia">总价：
                          <span className="qian">￥{val.price}</span>
                      </div>
                      <div className="zhuangTai2">{stateName}</div>
                  </div>
              </a>
        );

    }


  }.bind(this));

  this.setState({
    listData:list,
  });
},

ongoing:function(){
  this.setState({
    listState:0,
  },function(){
    this.listData();
  });
  
},

endState:function(){
  this.setState({
    listState:1,
  },function(){
    this.listData();
  });
},
navigateBack: function(){
    location.href = 'meal.html';
},

  render:function(){

    // var ongoingStyle = {
    //     borderBottom:'3px solid #2EBB6D',
    // } ;

    var ongoingStyle = {
        borderBottom:this.state.listState == 0 ? '3px solid #2EBB6D' : '',
    } ;

    var endStateStyle = {
        borderBottom:this.state.listState == 1 ? '3px solid #2EBB6D' : '',
    } ;

    return (
<div>
            <div className="qingHuang">
                <div className="jingXingZhong" style={ongoingStyle} onClick={this.ongoing}>进行中</div>
                <div className="yiJieDan"  style={endStateStyle}  onClick={this.endState}>已结单</div>
            </div>

            {this.state.listData}

            <footer></footer>
            <div id="footer">
                <div className="footer_left" onClick={this.navigateBack} ></div>
            </div>
            
</div>
    )
  }
});



var mainCom = React.render(
  <App />,
  document.body
  );
