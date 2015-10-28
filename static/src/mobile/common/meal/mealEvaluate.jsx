/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
// var wxSdk = require('/fishstrap/module/jweixin.js');

var App = React.createClass({

  getInitialState: function() {

  var dishId = $.location.getQueryArgv('dishId'); 
  var orderDetailId = $.location.getQueryArgv('orderDetailId');
  var orderNo = $.location.getQueryArgv('orderNo');  
    return {
      data : '',
      dishId:dishId,
      orderNo:orderNo,
      orderDetailId:orderDetailId,
      star:{dianpu:5,kouWei:5,taiDu:5},
      liming:false,
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

  $.get('/dish/commentGetDish', {dishId:this.state.dishId}, function(data) {

      if( data.code == 0 ){
        this.setState({
            data:data.data,
        });
      }else{
        alert(data.msg)
      }

  }.bind(this));


  },

clickStar:function(num,whatsProps){
  var StarData = this.state.star;
  StarData[whatsProps]= num;
  this.setState({
    star:StarData,
  });

},

updataStar:function(num,whatsProps){
  var theNum = parseInt(num);
  var starNum = []
  var HowSmallStar = 0;
  //黄星数量
  for (var i = 1; theNum >= i; i++) {
    HowSmallStar += 1 ;
    starNum.push(<Star clickStar={this.clickStar} key={HowSmallStar} HowSmallStar={HowSmallStar} whatsProps={whatsProps}/>);
    // starNum.push(<img src="/data/upload/image/meal/star2.png" />);
     
    console.log('y'+HowSmallStar);
  };

  //灰星数量
  for (var i = 1; (5 - theNum) >= i ; i++) {
    HowSmallStar += 1 ;
    starNum.push(<Star2 clickStar={this.clickStar} key={HowSmallStar} HowSmallStar={HowSmallStar} whatsProps={whatsProps}/>);
    // starNum.push(<img src="/data/upload/image/meal/star.png" />);
    
    console.log('h'+HowSmallStar);
  };

  return starNum;
},

  navigateBack: function(){
      location.href = 'meal.html';
  },

 liming :function(){ 
  if(this.state.liming){
      return <img src="/data/upload/image/meal/gouTrue.png" /> 
    }else{
     return <img src="/data/upload/image/meal/gouFalse.png" />
   } 
 },

 clickLiming:function(){
   if(this.state.liming){
      this.setState({
        liming:false,
      });
    }else{
      this.setState({
        liming:true,
      });
   } 

 },
 submit:function(){
    var dishId = this.state.dishId;
    var orderDetailId = this.state.orderDetailId;
    var orderNo = this.state.orderNo;
    var content = this.refs.content.getDOMNode().value;
    var degree = this.state.star;
    var anonymous = this.state.liming;

    var sendData = {
                    dishId:dishId,
                    orderDetailId:orderDetailId,
                    orderNo:orderNo,
                    content:content,
                    degree:degree,
                    anonymous:anonymous,
                  };

    $.post('/dishcomment/publish', sendData, function(data) {
      
      if(data.code == 0 ){
        alert('感谢你的评价');
        history.back();
      }else{
        alert(data.msg);
        history.back();
      }

    });


 },

  render:function(){
    

    return (

<div>


    <header>
        <div className="tuPian"><img src={this.state.data.thumb_icon} alt="" /></div>
        <div className="mingZi">{this.state.data.dishName}</div>
        <div className="NeiRong">
            <textarea ref='content' rows="3" placeholder="亲，不满意，还可以与卖家协商哦"></textarea>
        </div>
    </header>
    
 <pingJia>
     <div className="dianpu"><div className="text">给店铺评价</div><div className="star"><span className="PingJiaXingXing">{this.updataStar( this.state.star.dianpu,'dianpu' )}</span></div></div>
     <div className="kouWei"><div className="text">口味评价</div><div className="star"><span className="PingJiaXingXing">{this.updataStar( this.state.star.kouWei,'kouWei' )}</span></div></div>
     <div className="taiDu"><div className="text">服务态度</div><div className="star"><span className="PingJiaXingXing">{this.updataStar( this.state.star.taiDu,'taiDu' )}</span></div></div>
     <div className="liming" onClick={this.clickLiming}><div className="gou">{this.liming()}</div>匿名发布</div>
 </pingJia>   
<footer></footer>
    <div id="footer">
        <div className="footer_left" onClick={this.navigateBack}></div>
        <div className="footer_right" onClick={this.submit}>
            发表评价
        </div>
    </div>


</div>


    )
  }
});



var Star2 = React.createClass({
  clickTheStar:function(){
    this.props.clickStar(this.props.HowSmallStar,this.props.whatsProps);
  },
    render:function() {
        return (
            <img src="/data/upload/image/meal/star.png" key={this.props.HowSmallStar} onClick={this.clickTheStar}/>
        );
    }
});

var Star = React.createClass({
  clickTheStar:function(){
    this.props.clickStar(this.props.HowSmallStar,this.props.whatsProps);
  },
    render:function() {
        return (
            <img src="/data/upload/image/meal/star2.png" key={this.props.HowSmallStar} onClick={this.clickTheStar}/>
        );
    }
});


var mainCom = React.render(
  <App />,
  document.body
  );
