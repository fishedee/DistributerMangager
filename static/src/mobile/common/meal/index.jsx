var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var App = React.createClass({

  getInitialState: function() {

  var boardId = $.location.getQueryArgv('boardId');
  localStorage.boardId = boardId;


  
    return {
      data : '',

    };

  },

  componentDidMount : function() {


    $.get('/clientlogin/islogin', {}, function(data) {
      if (data.code != 0) {
        // alert(data.msg)
        location.href = $.url.buildQueryUrl('/clientlogin/wxlogin', {
          callback: location.href
        })
        // return;
      }else{

            this.setState({clientId : data.data});
           $.get('/room/getRoomInfo',{}, function(data) {
            if (data.code == 0) {
            this.setState({
              data: data.data
            });
            }else{
              alert(data.msg)
            }

          }.bind(this));
           
      }



    }.bind(this));





  },

eatIn:function(){
  sessionStorage.type = 'eatIn';
},

bookEat:function(){
  sessionStorage.type = 'bookEat';
},

takeOut:function(){
  sessionStorage.type = 'takeOut';
},

other:function(){
  sessionStorage.type = '';
},

  render:function(){

var headerStyle = {
  backgroundSize: 'contain',
   backgroundRepeat:'no-repeat',
   backgroundPosition:'center',
  backgroundImage: 'url(' +this.state.data.roomHeadBackground + ')',
};


    return (
        <div>
        <div className='header'>
            <div className='headerbg' style={headerStyle}>
                <div className='appName'>{this.state.data.roomName}</div>
                <div className='logo'>
                    <img src={this.state.data.roomHead} width='80' height='80' />
                </div>
            </div>
            <div className='headermenu'>
                <div className='lanmu'>
                    <div className='lm' onClick={this.other}><a href='mealMenu.html'><img src='/data/upload/image/meal/cd.png' /></a></div>
                    <div className='lm'><a href='mealOrder.html'><img src='/data/upload/image/meal/dd.png' /></a></div>
                    <div className='lm'><a href='#'><img src='/data/upload/image/meal/pl.png' /></a></div>
                </div>
            </div>
        </div>
        <div className='panel'>

            
            <div className='selection' onClick={this.eatIn}>
            <a href='mealMenu.html'>
                <div className='icon'>
                    <img src='/data/upload/image/meal/06.jpg' />
                </div>
                <p className='select_title'>点餐</p>
            </a>
            </div>
           

            <div className='selection' onClick={this.bookEat}>
            <a href='mealBook.html'>
                <div className='icon'>
                    <img src='/data/upload/image/meal/07.jpg' />
                </div>
                <p className='select_title'>预约</p>
            </a>
            </div>

            <div className='selection'>
            <a href='#'>
                <div className='icon'>
                    <img src='/data/upload/image/meal/08.jpg' />
                </div>
                <p className='select_title'>排队</p>
            </a>
            </div>

            <div className='selection' onClick={this.takeOut}>
            <a href='mealMenu.html'>
                <div className='icon'>
                    <img src='/data/upload/image/meal/09.jpg' />
                </div>
                <p className='select_title'>外卖</p>
            </a>
            </div>
          
        </div>
        <div className='pic'>
            <img src={this.state.data.roomIcon} />
        </div>
        <div className='info'>
            <div className='address'>
                <span className='location'></span>
                <span className='addressDetail'>{this.state.data.roomAddress}</span>
                <span className='right'></span>
            </div>
        </div>
        <div className='info'>
            <div className='time'>
                <span className='hitory'></span>
                <span className='workTime'>{this.state.data.roomWorkTime}</span>
            </div>
        </div>
        <div className='explain'>
            <div className='explainDetail'>
                <div className='xdtz'>
                    下单须知<br/>
                        {this.state.data.roomRemark}
                </div>
            </div>
        </div>
    </div>
    )
  }
});



var mainCom = React.render(
  <App />,
  document.body
  );
