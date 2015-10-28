/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var wxSdk = require('/fishstrap/module/jweixin.js');

var App = React.createClass({

  getInitialState: function() {
      var totalNum = 0;
      var totalMoney = 0;
      var bigRemark = '';
      
    if(typeof(sessionStorage.cart) != 'undefined'){
      var cart = JSON.parse(sessionStorage.cart);

      //初始化购物车数量和总价
      $.each(cart, function(index, val) {

         totalNum += val['num'];
         totalMoney += val['num'] * parseInt(val['dishPrice']);

      });

    }else{
      sessionStorage.cart = '{}';
    }

    if(typeof(sessionStorage.bigRemark) != 'undefined'){
      var bigRemark = sessionStorage.bigRemark;
    }else{
      sessionStorage.bigRemark = '';
    }
  
    return {

      totalNum:totalNum,
      totalMoney:totalMoney,
      itemList:null,
      remarkDisplay:false,
      remarkName:null,
      steps:'扫描餐桌',
      bigRemark:bigRemark,
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

    /*判断是否二维码进来*/
     $.get('/board/scanTime',{}, function(data) {
      //0扫描进来，

        if (data.code != 0  && sessionStorage.type == 'eatIn' ) {
            //不是扫描进来，并且是堂食入口。就要扫描了！

          this.setState({
            steps:'扫描餐桌',
          });

        }else{
          

          this.setState({
            steps:'下一步',
          });

        }

      }.bind(this));

    this.itemList();

  },

  navigateBack: function(){
      location.href = 'meal.html';
  },



  itemList:function(){

    var cartData=[];
    var totalNumb = 0;
    var totalMon =0;

    var cart = JSON.parse(sessionStorage.cart);

    $.each(cart, function(index, val) {

      
      if(val['num'] > 0){
        cartData.push( <List dishId={index} 
          dishName={val.dishName} 
          dishPrice={val.dishPrice} 
          num={val.num} 
          remark={val.remark} 
          itemList={this.itemList}
          setSingleRemark={this.setSingleRemark}
          />);
      }
    
    }.bind(this));

    $.each(cart, function(index, val) {

       totalNumb += val['num'];
       totalMon += val['num'] * val['dishPrice'];

    });

     this.setState({
      itemList:cartData,
      totalNum:totalNumb,
      totalMoney:totalMon,
     });


  },

  setSingleRemark:function(dishId,dishName){
    console.log(dishId);console.log(dishName);
    this.setState({
      remarkName:dishName,
      remarkDisplay:true,
      dishId:dishId,

    });
    
  },
  hiddenRemark:function(){
    this.setState({
      remarkDisplay:false,
    });
  },
  confirmReamrk:function(){
    var dishId = this.refs.dishId.getDOMNode().value;
    var remarkMsg = this.refs.remarkMsg.getDOMNode().value;

    var cart = JSON.parse(sessionStorage.cart);
    cart[dishId]['remark'] = remarkMsg ;
    sessionStorage.cart = JSON.stringify(cart);

    this.itemList();

    this.hiddenRemark();

    this.refs.remarkMsg.getDOMNode().value = '';
  },

  submit:function(){

    sessionStorage.bigRemark = this.refs.bigRemark.getDOMNode().value;

    if(this.state.steps == '扫描餐桌'){
      location.href = 'mealConfirm.html';
      // this._scan();

    };

    if (this.state.steps == '下一步') {

      //如果没有的
      if( typeof(sessionStorage.type) == 'undefined' ){
        location.href = 'mealSelect.html';
      }

      
      if(sessionStorage.type == 'eatIn'){

        //堂食
        this._submitOrder();

      }else if(sessionStorage.type == 'bookEat'){

        //预订
        location.href = 'mealBook.html';

      }else if(sessionStorage.type == 'takeOut'){

        //外卖
        location.href = 'mealTakeOut.html';

      }else{

        //其他
        location.href = 'mealSelect.html';
      }

    };

  },

  setBigRemark:function(e){
    sessionStorage.bigRemark = e.target.value;
    this.setState({
      bigRemark:e.target.value,
    });
  },

  _scan:function(){

    (function(){
        wxSdk.scanQRCode({
          needResult: 1,
          desc: 'scanQRCode desc',
          success: function (res) {

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
                  
                  var postData = {cart:cart,remark:bigRemark,boardId:boardId,pay:0};

                    $.post('/dishorder/placeOrder',postData, function(data) {
                      if (data.code == 0) {
                       sessionStorage.cart = '{}';
                       location.href = 'mealOrderDetailed.html?orderNo='+data.data;

                      }else{
                        // alert(data.msg);
                      }
                    });
                //和_submitOrder方法一样。end.

              } else{
                console.log(data);
                alert(data.msg);
              };
            });

          }
        });

    })();

  },

  _submitOrder:function(){
      var cart = JSON.parse(sessionStorage.cart);
      var bigRemark = this.refs.bigRemark.getDOMNode().value;
      console.log(cart);
      console.log(bigRemark);

      //判断是否有boardId
      var boardId = 'null';
      if(localStorage.boardId){
        boardId = localStorage.boardId;
      }else{
        boardId = 'null';
      }

      var postData = {cart:cart,remark:bigRemark,boardId:boardId,pay:0};

        $.post('/dishorder/placeOrder',postData, function(data) {
          if (data.code == 0) {
           sessionStorage.cart = '{}';
           location.href = 'mealOrderDetailed.html?orderNo='+data.data;

          }else{
            alert(data.msg);
          }
        }.bind(this));
  },

  render:function(){

    var remarkDisplay = {
      display:this.state.remarkDisplay ? 'block' : 'none',
    };

    return (

<div>

  <div className='item'>
        <div className='zhifu'>
            <div className='zhangBuZhiFu'>
                <div className='text'>暂不支付</div>
                <div className='gou'><img src='/data/upload/image/meal/gouTrue.png' /></div>
            </div>
        </div>
    </div>
    <div className='itemList'>

       {this.state.itemList}
        
        <div className='jieZhang'>共{this.state.totalNum}菜，总价￥{this.state.totalMoney.toFixed(2)}</div>
        <input className='yaoQiu' ref='bigRemark' value={this.state.bigRemark} onChange={this.setBigRemark} type='text'  placeholder='请输入特殊要求' />
    </div>
    
    <footer></footer>
    <div id='footer'>
        <div className='footer_left' onClick={this.navigateBack} ></div>

        <div className='footer_right' ref='stepsDOM' onClick={this.submit}>
            {this.state.steps}
        </div>
    </div>
    
    <footer></footer>
    
    <div className='mu' style={remarkDisplay}></div>
    
     <div className='xiaoBeiZhu' style={remarkDisplay}>
         <div className='tiMu'>{this.state.remarkName}</div>
         <div className='neiRong'><input type='text' ref='remarkMsg' placeholder='请输入备注信息' /> <input ref='dishId' type='hidden' value={this.state.dishId} /></div>
          <div className='anNiu'>
             <div className='fanhui' onClick={this.hiddenRemark}>返回</div>
             <div className='queding' onClick={this.confirmReamrk}>确认</div>
         </div>
     </div>

</div>


    )
  }
});

var List = React.createClass({
    jia:function(){
      var cart = JSON.parse(sessionStorage.cart);
      cart[this.props.dishId]['num'] = cart[this.props.dishId]['num'] + 1;
      sessionStorage.cart = JSON.stringify(cart);
      this.props.itemList();
    },

    jian:function(){
      var cart = JSON.parse(sessionStorage.cart);
      cart[this.props.dishId]['num'] = cart[this.props.dishId]['num'] - 1;
      sessionStorage.cart = JSON.stringify(cart);
      this.props.itemList();
    },

    singleRemark:function(){
      this.props.setSingleRemark(this.props.dishId,this.props.dishName);
    },
    render:function(){

    return (

        <div className='list' key={this.props.dishId}>
            <div className='neiRong'>
                <div className='BZ'>
                    <div className='beiZhu'>备注</div>
                    <div className='BZ2' onClick={this.singleRemark}></div>

                </div>
                <div className='Xinxi'>
                    <div className='mingCheng'>{this.props.dishName}</div>
                    <div className='jiaQian'>￥{this.props.dishPrice}</div>
                    <div className='beiZhu'>{this.props.remark}</div>
                </div>
                <div className='shuLiang'>
                    <div className='jian' onClick={this.jian}>-</div>
                    <div className='sl'>{this.props.num}</div>
                    <div className='jia' onClick={this.jia}>+</div>
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
