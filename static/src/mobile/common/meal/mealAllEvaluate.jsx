/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
// var wxSdk = require('/fishstrap/module/jweixin.js');

var App = React.createClass({

  getInitialState: function() {
  var dishId = $.location.getQueryArgv('dishId');
    return {
      data : '',
      listData:'',
      dishId:dishId,
    };

  },

  componentDidMount : function() {



  $.get('/dishcomment/search', {dishId:this.state.dishId}, function(data) {
      if( data.code == 0 ){
        this.setState({
            data:data.data,
        },function(){
          this.listUpdata();
        });
      }else{
        alert(data.msg)
      }

      }.bind(this));


  },

listUpdata:function(){
  var listData = [];
  $.each(this.state.data.data, function(index, val) {
     listData.push(
            <div className='Now'>
      <div className='ZhongTouBu'>
            <div className='TouBu'>
            <img src={val.headImgUrl} className='touXiang' />
            </div>
            <div className='mingZi'>
                {val.nickName}
            </div>
            <div className='PingJia'>
                <span className='PingJiaXingXing'>{this.star(val.degree)}</span><span className='ShiJian'>{val.createTime}</span>
            </div>
        </div>
        <div className='NeiRong'>
            {val.content}
        </div>  
    </div>
      );
  }.bind(this));
  this.setState({
    listData:listData
  });

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
      location.href = 'meal.html';
  },


  render:function(){

    return (

<div>

<div className='pingJiaNeiRong'>
    <div className='ZhongTiPingJia'>总体评价: <span className='PingJiaXingXing'>{this.star(this.state.data.degree)}</span></div>
    
  
{this.state.listData}
        
    
</div>


    <div id='footer'>
        <div className='footer_left' onClick={this.navigateBack} ></div>

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
