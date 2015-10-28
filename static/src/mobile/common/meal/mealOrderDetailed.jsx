/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var App = React.createClass({

  getInitialState: function() {

var orderNo = $.location.getQueryArgv('orderNo');
  
    return {
      data : {},
      orderNo:orderNo,
      itemListData:null,
    };

  },


  componentDidMount : function() {


  // $.get('/clientlogin/islogin', {}, function(data) {
  //     if (data.code != 0) {
  //       location.href = $.url.buildQueryUrl('/clientlogin/wxlogin', {
  //         callback: location.href
  //       });
  //       return;
  //     }
  //     this.setState({clientId : data.data});



  // }.bind(this));


     $.get('/dishorder/myOrderDetail',{orderNo:this.state.orderNo}, function(data) {
        if (data.code == 0) {
          this.setState({
            data: data.data,
            orderNo:data.data.orderNo,
            createTime:data.data.createTime,
            price:data.data.price,
            type:data.data.type,
            orderDetail:data.data.orderDetail,
            roomInfo:data.data.roomInfo,
          },function(){
            this.updataItem();
          });

          console.log(this.state.data);
        }else{
          alert(data.msg);
        }

      }.bind(this));



  },

navigateBack: function(){
    location.href = 'mealOrder.html';
},


typeName:function(){
//订单类型  type:1点餐 2:堂食 3:外卖
    switch(this.state.type){
      case '1':
        return '点餐';
        break;
      case '2':
        return '预约';
        break;
      case '3':
        return '外卖';
        break;
    }
},

stateName:function(){
      switch(this.state.data.state){
      case '0':
        return '未受理';
        break;
      case '1':
        return '已受理';
        break;
      case '2':
        return '已关闭';
        break;
      case '3':
        return '已取消';
        break;
      case '4':
        return '已结账';
        break;
    }
},


updataItem:function(){

    var orderDetail = this.state.orderDetail;
    // console.log(orderDetail);
    var itemData = [];

    $.each(orderDetail, function(index, val) {
       itemData.push(
        <Item {...val} />
        // <div>
        //     <div className='item'>
        //         <div className='mingCheng'>{val.dishName}</div>
        //         <div className='beizhu'>{'备注：'+val.remark}</div>
        //         <div className='qian'>￥{val.dishPrice}</div>
        //         <div className='shuLiang'>X{val.num}</div>
        //         <div className='tubiao'><img src='/data/upload/image/meal/pingjia.png' /><div className='wenzi'>评价</div></div>
        //     </div>
        //     <hr/>
        // </div>
        );
    });

    this.setState({
      itemListData:itemData,
    });

},

cancelOrder:function(){
  $.get('/dishorder/cancel',{orderNo:this.state.orderNo}, function(data) {
   
      if (data.code == 0) {
          // location.href = 'mealOrderDetailed.html?orderNo='+this.state.orderNo;
          alert('已经成功取消订单');
          location.reload(true);
          console.log(data);
        }else{
          alert(data.msg);
        }


  }.bind(this));
},

stateColor:function(){
  if(this.state.data.state <= 1 ){
      return {
        color: '#FF7542',
        border: '1px solid #FF7542',
          };
  }else{
          return {
        color: '#ADADAD',
        border: '1px solid #ADADAD',
          };
  }
},

  render:function(){

    var cancelOrderDisplay = {
      display: this.state.data.state == 0 ? 'block' : 'none',
    };

    var dishesDisplay ={
      display: this.state.type != 2 ? 'block' : 'none',
    }

    return (
<div>

<a href='./meal.html'><div className='tips'>你可以用微信扫描餐桌二维码进行自助下单哦~ &#62;</div></a>
      <div className='dingDangXinXi'></div>
       <div className='xinXi'>
<a href='./meal.html'><div className='item'>
               <span className='tubiao'><img src='/data/upload/image/meal/wu.png' /></span>
                <span className='tiMu'>{this.state.data.roomName}</span>
                <span className='youtubiao'><img src='/data/upload/image/meal/youjiantou.png' /></span>
            </div></a>
        <hr/>
        <div className='item'>
           <span className='tubiao'><img src='/data/upload/image/meal/dingwei.png' /></span>
            <span className='tiMu'>{this.state.data.roomAddress}</span>
            <a href={'tel:'+this.state.data.roomPhone}><span className='phone'><img src='/data/upload/image/meal/phone.png' /></span></a>
        </div>
    </div>

    <div className='dingDangXinXi'>订单信息</div>
    <div className='xinXi'>
        <div className='item'>
            <span className='tiMu'>订单编号</span>
            <span className='neiRong'>{this.state.orderNo}</span>
        </div>
        <hr/>
        <div className='item'>
            <span className='tiMu'>订单内容</span>
            <span className='neiRong'>{this.typeName()}</span>
        </div>
        <hr/>
        <div className='item'>
            <span className='tiMu'>保存时间</span>
            <span className='neiRong'>{this.state.createTime}</span>
        </div>
        <hr/>
        <div className='item'>
            <span className='tiMu'>订单金额</span>
            <span className='neiRong'>￥{this.state.price}</span>
        </div>
        <div className='zhuangTai' style={this.stateColor()}>{this.stateName()}</div>
    </div>
    
    <div className='dingDangXinXi' style={dishesDisplay} >所选菜品</div>
    
    <div className='caiPing' style={dishesDisplay} >

{this.state.itemListData}

    </div>
    <div className='scdd' style={cancelOrderDisplay}  onClick={this.cancelOrder}>取消订单</div>
    <footer></footer>
        <div id='footer'>
        <div className='footer_left' onClick={this.navigateBack} ></div>

    </div>


<footer></footer>

</div>
    )
  }
});


var Item = React.createClass({

//comment = 1 可以评论
  comment:function(){
    if(this.props.comment == '1'){
      return '/data/upload/image/meal/pingjia.png';
    }else{
      return '/data/upload/image/meal/pingjia2.png';
    }
  },
  clickPingJia:function(){
    if(this.props.comment =='1'){
      // console.log('./mealEvaluate.html?dishId='+this.props.dishId);
      location.href = './mealEvaluate.html?dishId='+this.props.dishId+'&orderDetailId='+this.props.orderDetailId+'&orderNo='+this.props.orderNo;
    }else{
      alert('已经评论了。')
    }
  },

    render:function() {
        return (
      <div>
        <div className='item'>
            <div className='mingCheng'>{this.props.dishName}</div>
            <div className='beizhu'>{'备注：'+this.props.remark}</div>
            <div className='qian'>{'￥'+this.props.dishPrice}</div>
            <div className='shuLiang'>{'X'+this.props.num}</div>
            <div className='tubiao'><img src={this.comment()} onClick={this.clickPingJia} /><div className='wenzi'>评价</div></div>
        </div>
        <hr/>
    </div>
        );
    }
});



var mainCom = React.render(
  <App />,
  document.body
  );

