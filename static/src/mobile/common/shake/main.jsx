var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var WinList = require('./WinList.jsx');

var App = React.createClass({


  getInitialState: function() {
    var luckyDrawId = $.location.getQueryArgv('luckyDrawId');

    return {
      y : '',
      x : '',
      z : '',
      last_y : '',
      test : true,
      luckyDrawId: luckyDrawId,
      luckyDraw: '',
      winList: '',
      clientId: '',
      rotateFinishTip: '',
      yaoYiYaoPrizeText:'',
      tentDisplay: false,
      yaoYiYaoDisplay: false,
      yaoYiYaoTextDisplay: false,
      yaoYiYaoPrizeDisplay: false,
      loadingDisplay: false,
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


   $.get('/luckydraw/getClientResult', {
      luckyDrawId: this.state.luckyDrawId
    }, function(data) {
      if (data.code != 0) {
        dialog.message(data.msg);
        return;
      }

      this.setState({
        luckyDraw: data.data
      });


      // $('.summary').text(luckyDraw.summary);
      // $('.time').text(luckyDraw.beginTime + ' ~ ' + luckyDraw.endTime);

    }.bind(this));

   this.init();


  },

   init:function() {　　
    if (window.DeviceMotionEvent) {　　　　 // 移动浏览器支持运动传感事件　　　
      window.addEventListener('devicemotion', this.deviceMotionHandler, false);　　
    } 
    else {
      alert('本设备不支持摇一摇（devicemotion）事件');
    }
  },



  deviceMotionHandler:function(eventData) {

  　　 // 获取含重力的加速度
    　　
    this.setState({
    x:eventData.accelerationIncludingGravity.x,
    y:eventData.accelerationIncludingGravity.y,
    z:eventData.accelerationIncludingGravity.z,
    });　　　　　　　　　　　　 // TODO:在此处可以实现摇一摇之后所要进行的数据逻辑操作

    //修复iphone的bug
    if (this.state.test == true) {

      this.setState({
      last_y : eventData.accelerationIncludingGravity.y,
      test : false,
      });
    }

    if ((this.state.y != this.state.last_y && this.state.y > 18 ) || this.state.x > 18 || this.state.z > 18) {

      window.removeEventListener('devicemotion', this.deviceMotionHandler, false);
      this.yaoYiYao();

    }


  },

  ShowYaoyiyao: function() {
    this.setState({
      tentDisplay: true,
      yaoYiYaoDisplay: true,
      yaoYiYaoTextDisplay: true,
    })
  },

  yaoYiYao: function() {
    this.ShowYaoyiyao();
    this.refs.music1.getDOMNode().play();

    //摇一摇图片分裂效果
    var shang = $('.yaoYiYao .image .shang').css('top');
    var xia = $('.yaoYiYao .image .xia').css('top');
    $('.yaoYiYao .image .shang').css('top', '80px');
    $('.yaoYiYao .image .xia').css('top', '348px');
    setTimeout(function() {
      $('.yaoYiYao .image .shang').css('top', shang);
      $('.yaoYiYao .image .xia').css('top', xia);

    }, 500);

    setTimeout(function() {

      this.setState({
          yaoYiYaoTextDisplay: false,
        });

      this.setState({
        loadingDisplay: true,
      });

      $.post('/luckydraw/shakeDraw', {
        luckyDrawId: this.state.luckyDrawId,
      }, function(data) {

        this.refs.music2.getDOMNode().play();

        if (data.code != 0) {
          var errorTitle = data.msg;
        } else {
          var luckyDraw=this.state.luckyDraw;
          for (var i = 0; i != luckyDraw.commodity.length; ++i)
            if (luckyDraw.commodity[i].luckyDrawCommodityId == data.data) {
              this.setState({rotateFinishTip : luckyDraw.commodity[i].title}) ;
              break;
            }
        }

        this.setState({
          yaoYiYaoDisplay: false,
          loadingDisplay: false,
        });

        if (typeof errorTitle == 'string') {
          this.setState({yaoYiYaoPrizeText:errorTitle});
          // $('.yaoYiYaoPrizeText').text(errorTitle);
        } else {
          this.setState({yaoYiYaoPrizeText:'恭喜你，你获得的是：' + this.state.rotateFinishTip});
          // $('.yaoYiYaoPrizeText').text('恭喜你，你获得的是：' + rotateFinishTip);
        }

        this.setState({
          yaoYiYaoPrizeDisplay: true,
        });

        $('.txtMarquee-left').show(); //显示中奖名单
      }.bind(this));
    }.bind(this), 1000);


  },


  render:function(){
    var tentStyle = {
      display:this.state.tentDisplay ? 'block' : 'none',
    };

    var yaoYiYaoStyle = {
      display:this.state.yaoYiYaoDisplay ? 'block' : 'none',
    };

    var yaoYiYaoTextStyle = {
      display:this.state.yaoYiYaoTextDisplay ? 'block' : 'none',
    };

    var loadingStyle = {
      display:this.state.loadingDisplay ? 'block' : 'none',
    };

    var yaoYiYaoPrizeStyle = {
      display:this.state.yaoYiYaoPrizeDisplay ? 'block' : 'none',
    }

    return (
    <div>

      <div className='head'><img src= {__inline('./head.png')} />
        </div>
        <div onClick={this.ShowYaoyiyao} className='click'><img src= {__inline('./click.png')} />
        </div>

        <div className='banner'>
          <div className='bannerHead'><img src= {__inline('./banner1.png')} />
          </div>
          <div className='bannerContent'>
            <div className='bennerText'>
              <p>活动规则：</p>
              <p className='summary'>{this.state.luckyDraw.summary}</p>
              <p>活动时间：</p>
              <p className='time'>{this.state.luckyDraw.beginTime} ~ {this.state.luckyDraw.endTime}</p>
            </div>
          </div>
          <div className='bannerFooter'><img src= {__inline('./banner2.png')} />
          </div>

        </div>

        <div style={tentStyle} className='tent'></div>

        <div style={yaoYiYaoStyle} className='yaoYiYao'>
          <div className='image'><img className='shang' src= {__inline('./shang.png')} /><img className='xia' src= {__inline('./xia.png')} />
          </div>
        </div>

        <div style={yaoYiYaoTextStyle} className='yaoYiYaoText'>
          <p>大力摇呀！</p>
          <p>大奖正朝你的怀抱全力冲刺呢！</p>
        </div>
        <div style={yaoYiYaoPrizeStyle} className='yaoYiYaoPrize'>
          <a href='luckylist.html'>
            <p className='yaoYiYaoPrizeText'>{this.state.yaoYiYaoPrizeText}</p>
            <p>点击打开</p>
          </a>
        </div>
        <div style={loadingStyle} className='loading'><img src= {__inline('./loading.gif')} />
        </div>

    <WinList luckyDrawId={this.state.luckyDrawId} />


        <audio ref='music1' src='common/shake/1.mp3'></audio>
        <audio ref='music2' src='common/shake/2.mp3'></audio>


    </div>
    )
  }
});



var mainCom = React.render(
  <App />,
  document.body
  );