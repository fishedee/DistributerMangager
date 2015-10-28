/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var App = React.createClass({

  getInitialState: function() {
      var dishId = $.location.getQueryArgv('dishId');
      var totalNumb = 0;
      var totalMon = 0;
      var singleNum = 0;
      
    if(typeof(sessionStorage.cart) != 'undefined'){
      var cart = JSON.parse(sessionStorage.cart);

      //判断是否有产品在购物车
      if(typeof(cart[dishId]) != 'undefined'){
        singleNum = cart[dishId]['num'];
      }

    //初始化购物车数量和总价
      $.each(cart, function(index, val) {

         totalNumb += val['num'];
         totalMon += val['num'] * val['dishPrice'];

      });

    }else{
      sessionStorage.cart = '{}';
    }
  
    return {
      data : '',
      singleNum : singleNum,
      dishId:dishId,
      cartNum:totalNumb,
      totalMoney:totalMon,
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
      this.setState({clientId : data.data});



  }.bind(this));


     $.get('/dish/getDish',{dishId:this.state.dishId,mobile:1}, function(data) {
        if (data.code == 0) {
          this.setState({
            data: data.data
          });

          // console.log(this.state.data);
        }else{
          alert(data.msg);
        }

      }.bind(this));



  },

jia:function(){
  var cart = JSON.parse(sessionStorage.cart);

    if(typeof(cart[this.state.dishId]) == 'undefined'){
      cart[this.state.data.dishId]={dishName:this.state.data.dishName,dishPrice:this.state.data.dishPrice,num:0,remark:''}
    }

  cart[this.state.data.dishId]['num'] = cart[this.state.data.dishId]['num'] + 1;
  
  this.setState({
    singleNum:cart[this.state.data.dishId]['num']
  });

  sessionStorage.cart = JSON.stringify(cart);
  this.totalNum();

},

jian:function(){
  var cart = JSON.parse(sessionStorage.cart);

    if(typeof(cart[this.state.dishId]) == 'undefined'){
      cart[this.state.data.dishId]={dishName:this.state.data.dishName,dishPrice:this.state.data.dishPrice,num:0,remark:''}
    }

    if(cart[this.state.data.dishId]['num'] > 0){
      cart[this.state.data.dishId]['num'] = cart[this.state.data.dishId]['num'] - 1;
    }

  this.setState({
    singleNum:cart[this.state.data.dishId]['num']
  });

  sessionStorage.cart = JSON.stringify(cart);
  this.totalNum();

},

star:function(num){
  var theNum = parseInt(num);
  var starNum = []
  //黄星数量
  for (var i = 1; theNum >= i; i++) {
    starNum.push(<img src="/data/upload/image/meal/star2.png" />);
  };

  //灰星数量
  for (var i = 1; (5 - theNum) >= i ; i++) {
    starNum.push(<img src="/data/upload/image/meal/star.png" />);
  };

  return starNum;
},

navigateBack: function(){
    // history.back();
    location.href = 'mealMenu.html';
},

totalNum:function(){
    var cart = JSON.parse(sessionStorage.cart);
    var totalNumb = 0;
    var totalMon =0;
    $.each(cart, function(index, val) {

       totalNumb += val['num'];
       totalMon += val['num'] * parseInt(val['dishPrice']);

    });


    this.setState({
      cartNum:totalNumb,
      totalMoney:totalMon
    });
},

 bgCart:function(){
  if(this.state.cartNum == 0){
   return {backgroundImage: 'url(/data/upload/image/meal/gouwulan.png)'};
  }else{
    return ;
  } 
 }, 

comment:function(){

  if(this.isMounted()){
    var commentData = [];

    

    $.each(this.state.data.comment, function(index, val) {

      commentData.push(
          <div className="Now">
             <div className="ZhongTouBu">
                  <div className="TouBu">
                  <img src={val.headImgUrl} className="touXiang" />
                  </div>
                  <div className="mingZi">
                     {val.nickName}
                  </div>
                  <div className="PingJia">
                      <span className="PingJiaXingXing">{this.star(val.degree)}</span><span className="ShiJian"> {val.modifyTime}</span>
                  </div>
              </div>
              <div className="NeiRong">
                  {val.content}
              </div>  
          </div>

        );
      }.bind(this));

    return commentData;
  }

},


  render:function(){

    var mySwipeStyle = {maxWidth:'500px',margin:'0 auto'};
    var cartNumb = this.state.cartNum > 0 ? this.state.cartNum :  '' ;

    return (
<div>

          <div id='mySwipe' style={mySwipeStyle} className='swipe'>
              <div className='swipe-wrap'>
                  <div><img src={this.state.data.icon} /></div>
              </div>
          </div>

      <div className="jiBenXinxi">
          <p className="mingcheng">{this.state.data.dishName}</p>

          <div className="qian">
              <span className="fuHao">￥</span>
              <span className="jiaQian">{parseInt(this.state.data.dishPrice).toFixed(2)}</span>
          </div>

          <div className="shuliang">
          <span className="jian" onClick={this.jian}>-</span>
          <span className="shumu">{this.state.singleNum}</span>
          <span className="jia" onClick={this.jia}>+</span>
          </div>

      </div>

      <div className="meiShiDianPing">
          <div className="now">
              <span className="biaoTi">美食点评</span>
              <a href={'mealAllEvaluate.html?dishId='+this.state.dishId} ><span className="quanBuPingJia">全部评价 &gt;&gt; </span></a>
          </div>
      </div>

      <div className="pingJiaNeiRong">
          <div className="ZhongTiPingJia">总体评价: <span className="PingJiaXingXing">{this.star(this.state.data.degree)}</span></div>
          
          {this.comment()}

      </div>

      <div dangerouslySetInnerHTML={{__html: this.state.data.detail}} className="jieXiao"></div>

<footer></footer>
          <div id="footer">
              <div className="footer_left" onClick={this.navigateBack} ></div>  

              <div className="footer_min">
                  <span name='sum'>{this.state.totalMoney.toFixed(2)}</span> 
                  <span id="qianfuhao">￥</span>
                  <span id="gouwulan" style={this.bgCart()}><p className="shuzi">{cartNumb}</p></span>
              </div>
        <a href='mealCart.html'> 
        <div className="footer_right">
            下一步
        </div>
        </a>
          </div>


      <footer></footer>


</div>
    )
  }
});



var mainCom = React.render(
  <App />,
  document.body
  );
