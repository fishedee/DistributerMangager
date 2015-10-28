/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var wxSdk = require('/fishstrap/module/jweixin.js');

var App = React.createClass({


  componentDidMount : function() {

    $('.kanDingDang').css('top',document.body.clientWidth * 0.378 +'px');
    $('.kanDingDang').css('height',document.body.clientWidth * 0.11 +'px');
    $('.erWeiMa').css('top',document.body.clientWidth * 0.522 +'px');
    $('.erWeiMa').css('height',document.body.clientWidth * 0.11 +'px');

  // $.get('/clientlogin/islogin', {}, function(data) {
  //     if (data.code != 0) {
  //       location.href = $.url.buildQueryUrl('/clientlogin/wxlogin', {
  //         callback: location.href
  //       });
  //       return;
  //     }
  //     this.setState({clientId : data.data});
  //     }.bind(this));

      $.get('/redpack/getJsConfig',{url:location.href},function(data){
        if( data.code != 0 ){
          alert(data.msg);
          return;

        }
        
        var jsConfig = data.data;
        console.log(jsConfig);
        wxSdk.config($.extend(
          jsConfig,
          {
            debug:false,

            jsApiList:[
              'scanQRCode'
            ]
          }
        ));
        wxSdk.error(function(){
          alert('微信接口配置失败，请检查appId与appKey是否设置正确');
        });
      }.bind(this));


  },


  scan:function(){

    $.get('/dishorder/scan', function(data) {

      console.log('NoTeturnImageTextMsg',data);
     
    });

  
    (function(){
        wxSdk.scanQRCode({
          needResult: 1,
          desc: 'scanQRCode desc',
          success: function (res) {
            //看是否扫描过，顺便拿

            $.get('/board/scanConfirm',{url:res.resultStr}, function(data) {
              if (data.code == 0) {

                localStorage.boardId = data.data;

                //和_submitOrder方法一样。start
                  var cart = JSON.parse(sessionStorage.cart);
                  var bigRemark = sessionStorage.bigRemark;
                  console.log(cart);
                  console.log(bigRemark);

                  //判断是否有boardId
                  var boardId = 'null';
                  if(localStorage.boardId){
                    boardId = localStorage.boardId;
                  }else{
                    boardId = 'null';
                  }
                  
                  var postData = {cart:cart,remark:sessionStorage.bigRemark,boardId:boardId,pay:0};

                    $.post('/dishorder/placeOrder',postData, function(data) {
                      if (data.code == 0) {
                       sessionStorage.cart = '{}';
                       location.href = 'mealOrderDetailed.html?orderNo='+data.data;

                      }else{
                        alert(data.msg);
                      }
                    });
                //和_submitOrder方法一样。end.

              } else{
                alert(data.msg);
              };
            });

          }

        });

    }

    )();
  


  },

  navigateBack: function(){
      location.href = 'meal.html';
  },


  render:function(){

    return (

<div>
    <div className="tu"><img src="/data/upload/image/meal/saomatijiao.png" />
    </div>
    <a href="./mealOrder.html"><div className="kanDingDang"></div></a>
    <div className="erWeiMa" onClick={this.scan}></div>
    <div id="footer">
        <div className="footer_left" onClick={this.navigateBack}></div>
    </div>

</div>


    )
  }
});

var mainCom = React.render(
  <App />,
  document.body
  );
