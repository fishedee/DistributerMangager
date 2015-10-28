/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');

var App = React.createClass({

	getInitialState:function() {

		var address = '';
		if(typeof(sessionStorage.address) != 'undefined'){
			address = sessionStorage.address;
		}else{
			sessionStorage.address = '';
			address = sessionStorage.address;
		}


		var delivery = '';
		if(typeof(sessionStorage.delivery) != 'undefined'){
			delivery = sessionStorage.delivery;
		}else{
			sessionStorage.delivery = '尽快送达';
			delivery = sessionStorage.delivery;
		}


		var bigRemark = '';
		if(typeof(sessionStorage.bigRemark) != 'undefined'){
			bigRemark = sessionStorage.bigRemark;
		}else{
			sessionStorage.bigRemark = '';
			bigRemark = sessionStorage.bigRemark;
		}


	    return {
		  remarkDisplay:false,
      verifyDisplay:false,
      tentDisplay:false,
	      remarkName:'',
	      remarkValue:'',
	      typeName:'',
	      address:address,
	      delivery:delivery,
	      bigRemark:bigRemark,
        dishesData:[],
      checkVerify:true,
      verifyText:'发送验证码',
      seconds:60,
	    };
	},

  componentDidMount : function() {
      $.get('/boarddate/checkVerify', function(data) {

          //1的话不需要 0 需要
          if(data.code == 1){
            this.setStatu({
              checkVerify:false,
            });
          }


      }.bind(this));


    this.dishesUpdate();
  },

  setAddress:function(){

    this.setState({
      remarkName:'请填写地址',
      remarkDisplay:true,
      tentDisplay:true,
      remarkValue:this.state.address,
      typeName:'address',
    },function(){
    	console.log(this.state.address);
    });

  },

  setDelivery:function(){

    this.setState({
      remarkName:'送达时间',
      remarkDisplay:true,
      tentDisplay:true,
      remarkValue:this.state.delivery,
      typeName:'delivery',
    });

  },

  setRemark:function(){

    this.setState({
      remarkName:'我单备注',
      remarkDisplay:true,
      tentDisplay:true,
      remarkValue:this.state.bigRemark,
      typeName:'bigRemark',
    });

  },


  dishesUpdate:function(){
    var dishesData = [];

    var cart = JSON.parse(sessionStorage.cart);
    $.each(cart, function(index, val) {
       dishesData.push(    <div className="single">
        <div className="goods">{val.dishName}</div>
        <div className="num">{'x' + val.num}</div>
        <div className="money">{'￥' + (val.dishPrice * val.num).toFixed(2)  }</div>
    </div>);
    }.bind(this));

    this.setState({
      dishesData:dishesData,
    });
  },


  hiddenRemark:function(){
    this.setState({
      remarkDisplay:false,
      tentDisplay:false,
    });
  },

  confirmReamrk:function(){
    var typeName = this.refs.typeName.getDOMNode().value;
    var remarkMsg = this.refs.remarkMsg.getDOMNode().value;

    sessionStorage[typeName] = remarkMsg;

    this.setState({
      remarkDisplay:false,
      tentDisplay:false,
      address:sessionStorage.address,
	    delivery:sessionStorage.delivery,
	    bigRemark:sessionStorage.bigRemark,
    },function(){
    	this.refs.typeName.getDOMNode().value = '';
    	this.refs.remarkMsg.getDOMNode().value = '';
    	console.log(this.state);
    });

  },

	setRemarkValue:function(event){
 		this.setState({remarkValue: event.target.value});
	},

  // hideTent:function(){
  //   this.setState({
  //     tentDisplay:false,
  //     remarkDisplay:false,
  //     verifyDisplay:false,
  //   });
  // },


  navigateBack: function(){
      location.href = 'meal.html';
  },


  // submitData:function(){

  //     // if(this.state.checkVerify){
  //     //   this.setState({
  //     //     tentDisplay:true,
  //     //   });        
  //     // }


  //     var sendData = {
  //       address:this.state.address,
  //       delivery:this.state.delivery,
  //       bigRemark:this.state.bigRemark,
  //       car:JSON.parse(sessionStorage.cart),
  //       bigRemark:sessionStorage.bigRemark,
  //       code:1111,
  //     };

  //     console.log(sendData);

  //     // $.post('/dishbooking/booking', sendData , function(data) {
  //     //     console.log('retureData',data);
  //     // });
      

  // },


  submitData:function(){
//是否需要验证
      if(this.state.checkVerify){
        this.setState({
          tentDisplay:true,
          verifyDisplay:true,
        });        
      }else{
        this.sendData();
      }

  },

  sendData:function(){
        var sendData = {
        address:this.state.address,
        delivery:this.state.delivery,
        remark:this.state.bigRemark,
        cart:JSON.parse(sessionStorage.cart),
        name:this.refs.names.getDOMNode().value,
        phone:this.refs.phone.getDOMNode().value,
        code:this.refs.codes.getDOMNode().value,
        type:3,
        booking:2,
      };


      console.log(sendData);

      $.post('/dishorder/placeOrder', sendData , function(data) {
          if (data.code == 0) {
            location.href = 'mealOrderDetailed.html?orderNo='+data.data;
          }else{
            alert(data.msg);
          }
      });
  },

  sendVerifyCode:function(){
    var phone = this.refs.phone.getDOMNode().value;
    // console.log('phone',phone);
     var reg = /^1[3|4|5|8][0-9]\d{8}$/;

     if(this.state.verifyText != '发送验证码'){
      return;
     }

      //号码正确;
     if (reg.test(phone)) {
          

        $.get('/user/getPhoneCode',{phone:phone,booking:1}, function(data) {
          
            if(data.code != 0){
              alert(data.msg); 
              return;
            }else{
              //循环倒时
              console.log('进入了倒计时');
              window.timer = setInterval(this.reciprocal,1000);
            }

        }.bind(this));

     }else{
          alert("手机号码有误，请重新输入！");
     };
  },

  reciprocal:function (){

     this.setState({
      seconds: this.state.seconds - 1,
      verifyText: this.state.seconds + '秒后重获'
     });

    
      //倒计时完毕
      if(this.state.seconds < 0){
        // $('.hqyzm').show();
        // $('.reciprocal').hide();
          this.setState({
            seconds: 60,
            verifyText: '发送验证码'
          });
          clearInterval(window.timer);
      }
  },


  hideTent:function(){
    console.log('tent');
    this.setState({
      tentDisplay:false,
      dateSelectDisplay:false,
      verifyDisplay:false,
    });
  },


//验证码背景颜色
  yanzhengma:function () {
    if(this.state.verifyText == '发送验证码'){
      return 'yanzhengma';
    }else{
      return 'yanzhengma invalid';
    }
  },



    render:function() {

    var tentDisplay = {
      display:this.state.tentDisplay ? 'block' : 'none',
    };

    var remarkDisplay = {
      display:this.state.remarkDisplay ? 'block' : 'none',
    };

    var verifyDisplay ={
      display:this.state.verifyDisplay ? 'block' : 'none',
    };


	  var bigRemarkData = this.state.bigRemark == '' ? '点击添加备注' : this.state.bigRemark;

        return (

            <div>

    <div className="row">
        <div className="item">暂不支付
            <div className="tubiao"><img src="/data/upload/image/meal/GreenTrue.png" /></div>
        </div>
        <div className="item">餐到付款
            <div className="tubiao"><img src="/data/upload/image/meal/GreenTrue.png" /></div>
        </div>
        
    </div>
<div className="tiao"></div>
    <div className="row">
       
        <div className="item" onClick={this.setAddress}><div className="tou"><img src="/data/upload/image/meal/dingwei2.png" /></div>
          请填写地址
           <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png" /></div>
           <div className="text">{this.state.address}</div>
        </div>
             
        <div className="item"  onClick={this.setDelivery}>送达时间
           <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png" /></div>
            <div className="text">{this.state.delivery}</div>
        </div>
             
        <div className="item"  onClick={this.setRemark}>我单备注
           <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png" /></div>
            <div className="text">{bigRemarkData}</div>
        </div>
             
        <div className="item noSupport">发票信息
           <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png" /></div>
            <div className="text noSupport">餐厅不支持开发票</div>
        </div>             
        <div className="item noSupport">抵价券
           <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png" /></div>
            <div className="text noSupport">餐厅不支持抵价券</div>
        </div>
        
    </div>

<div className="row">

{this.state.dishesData}
    
</div>

      <footer></footer>
      <div id="footer">
          <div className="footer_left" onClick={this.navigateBack} ></div>
                  <div className="footer_right" onClick={this.submitData}>
                  提交订单
                   </div>
      </div>



<div className="tent" style={tentDisplay}></div>

	<div className="windons" style={remarkDisplay}>
	     <div className='xiaoBeiZhu' style={remarkDisplay}>
	         <div className='tiMu'>{this.state.remarkName}</div>
	         <div className='neiRong'><input type='text' ref='remarkMsg' onChange={this.setRemarkValue} value={this.state.remarkValue} placeholder='请输入备注信息' /> <input ref='typeName' type='hidden' value={this.state.typeName} /></div>
	          <div className='anNiu'>
	             <div className='fanhui' onClick={this.hiddenRemark}>返回</div>
	             <div className='queding' onClick={this.confirmReamrk}>确认</div>
	         </div>
		</div>	
	</div>

            <div className="verify" style={verifyDisplay}>
            <div className="tent" onClick={this.hideTent} style={tentDisplay}></div>
                <div className="content">
                    <div className="title">用户手机验证</div>

                    <div className="yanzhen">
                        <div className="item">
                            <div className="name">姓&nbsp;&nbsp;&nbsp;名</div>
                            <input type="text" ref='names' placeholder="请输入您的姓名" />
                        </div>
                        <div className="item">
                            <div className="name">手机号</div>
                            <input type="text" ref='phone' placeholder="请输入您的手机号码" />
                        </div>
                        <div className="item">
                            <div className="name">验证码</div>
                            <input type="text" ref='codes' className="verifyInput" placeholder="短信验证码" />
                            <div className={this.yanzhengma()} onClick={this.sendVerifyCode}>{this.state.verifyText}</div>
                        </div>
                    </div>
                    <div className="submitVerify" onClick={this.sendData}><div className="juzhong">提交验证</div></div>
                    <div className="verifyTips">验证成功后，将自动创建订餐账户并完成订单</div>

                </div>
            </div>



            </div>

        );
    }
});

var mainCom = React.render(
  <App />,
  document.body
  );

