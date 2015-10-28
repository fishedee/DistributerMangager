/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
// var _ = require('fishstrap/module/underscore.js');
var App = React.createClass({

  getInitialState: function() {

  // var boardId = $.location.getQueryArgv('boardId');
  // localStorage.boardId = boardId;
  
    return {
      getOrderTime : '',
      DateData : [],
      selectDay:'',
      selectTime:'',
      TimeData:'',
      confirmDay:'',
      confirmTime:'',
      dateSelectDisplay:false,
      tentDisplay:false,
      verifyDisplay:false,
      fontGetAllType:'',
      confirmType:'',
      typeData:'',
      checkVerify:true,
      verifyText:'发送验证码',
      seconds:60,
    };

  },

  componentDidMount : function() {


    // $.get('/clientlogin/islogin', {}, function(data) {
    //   if (data.code != 0) {
    //     
    //     location.href = $.url.buildQueryUrl('/clientlogin/wxlogin', {
    //       callback: location.href
    //     })
    //     // return;
    //   }else{

            //this.setState({clientId : data.data});
           $.get('/boarddate/getOrderTime',{}, function(data) {

            
            if( typeof(data.data[0].time[0]) ==  "undefined" ){
              var selectTime = '';
              var confirmTime = '';
            }else{
              var selectTime = data.data[0].time[0].dateId;
              var confirmTime = data.data[0].time[0].time;
            }

            if (data.code == 0) {
            this.setState({
              getOrderTime: data.data,
              selectDay:data.data[0].date,
              confirmDay:data.data[0].date,
              selectTime:selectTime,
              confirmTime:confirmTime,
            },function(){
              this.updateDateList();
              this.updateTimeList();
            });
           // console.log(data);
            }else{
              alert(data.msg)
            }

          }.bind(this));
           


           $.get('/boardtype/fontGetAllType', function(data) {
              if(data.code == 0){
                // console.log(data);


                this.setState({
                  fontGetAllType:data.data.reverse(),
                  confirmType:data.data[0].boardTypeId,
                },function(){
                  this.updateTypeList();
                });
              }else{
              alert(data.msg)
               } 
           }.bind(this));

          $.get('/boarddate/checkVerify', function(data) {

              //1的话不需要 0 需要
              if(data.code == 1){
                this.setStatu({
                  checkVerify:false,
                });
              }


          }.bind(this));

    //   }



    // }.bind(this));





  },

theSelect:function(tpye,data,dayData,timeString){
  if(tpye == 'day'){
    // console.log('day');
    this.setState({
      selectDay:data,
    },function(){
      this.updateDateList();
      this.updateTimeList();
    });
  }else{
    // console.log('time');
    // console.log(data);
    this.setState({
      selectTime:data,
      confirmDay:dayData,
      confirmTime:timeString,
      dateSelectDisplay:false,
      tentDisplay:false,
    },function(){
      this.updateDateList();
      this.updateTimeList();
    });
  }


},

updateDateList:function(){

  var DateData = [];

  $.each(this.state.getOrderTime, function(index, val) {
     // console.log(val.date);
     var weekString = '';
     switch(val.week){
       case 1:
         weekString = '周一';
         break;
       case 2:
         weekString = '周二';
         break;
       case 3:
         weekString = '周三';
         break;
       case 4:
         weekString = '周四';
         break;
       case 5:
         weekString = '周五';
         break;
       case 6:
         weekString = '周六';
         break;
       case 7:
         weekString = '周日';
         break;
     }

     //选择
     var className = 'data-item';
     if(val.date == this.state.selectDay){
       className += ' active'
     }

     DateData.push(<DateItem className={className} day={val.date} week={weekString} theSelect={this.theSelect} />);

  }.bind(this));


  this.setState({
    DateData:DateData,
  });
},

  updateTimeList:function(){

    var TimeData = [];

    $.each(this.state.getOrderTime, function(index, val) {

      var days = val.date;

      if(val.date == this.state.selectDay){

        $.each(val.time, function(index, val) {

          //选择

          var className = 'time-item';
          if(days == this.state.confirmDay  && val.dateId == this.state.selectTime ){
             className += ' active'
          }

          TimeData.push(<TimeItem className={className} times={val.time} dateId={val.dateId} days={days} theSelect={this.theSelect} />);
           

        }.bind(this));

      }
     

    }.bind(this));

      this.setState({
        TimeData:TimeData,
      });

  },

  updateTypeList:function(){
    var typeData = [];

    
    var newData = this.state.fontGetAllType;
    

    $.each(newData, function(index, val) {


      //选择
      var isTrue = '/data/upload/image/meal/select2.png';
      if(val.boardTypeId == this.state.confirmType){
        isTrue = '/data/upload/image/meal/select1.png';
      }

       typeData.push(<TypeItem setType={this.setType} nums={val.boardTypeId} texts={val.typeName} isTrue={isTrue} />);
       // console.log(typeData);
    }.bind(this));

    // console.log('type:');
    // console.log(typeData);

    this.setState({
      typeData:typeData,
    });
  },

  setType:function(nums){
      this.setState({
        confirmType:nums,
      },function(){
        this.updateTypeList();
      });
  },


  showSelectDate:function(){
    this.setState({
      dateSelectDisplay:true,
      tentDisplay:true,
    });
  },

  navigateBack: function(){
      location.href = 'meal.html';
  },

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
        confirmDay:this.state.confirmDay,
        confirmTime:this.state.selectTime,
        peopleNum:this.refs.peopleNum.getDOMNode().value,
        confirmType:this.state.confirmType,
        remark:this.refs.special.getDOMNode().value,
        name:this.refs.names.getDOMNode().value,
        phone:this.refs.phone.getDOMNode().value,
        code:this.refs.codes.getDOMNode().value,
        type:2,
        booking:1,
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

  render:function(){

    var dateSelectDisplay = {
      display: this.state.dateSelectDisplay ? 'block' : 'none',
    };

    var tentDisplay = {
      display: this.state.tentDisplay ? 'block' : 'none',
    };

    var verifyDisplay ={
      display:this.state.verifyDisplay ? '' : 'none',
    };

    return (
       <div>


    <div className="row">
        <div className="item" onClick={this.showSelectDate}>预订时间
            <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png"/></div>
            <div className="time">{this.state.confirmDay + ' ' + this.state.confirmTime}</div>
        </div>
        <div className="item">预订人数
            <div className="tubiao"><img src="/data/upload/image/meal/youjiantou.png"/></div>
              <div className="neirong">
                <select ref='peopleNum'>
                    <option value="1">餐桌1人就餐</option>
                    <option value="2">餐桌2人就餐</option>
                    <option value="3">餐桌3人就餐</option>
                    <option value="4">餐桌4人就餐</option>
                    <option value="5">餐桌5人就餐</option>
                    <option value="6">餐桌6人就餐</option>
                    <option value="7">餐桌7人就餐</option>
                    <option value="8">餐桌8人就餐</option>
                    <option value="9">餐桌9人就餐</option>
                    <option value="10">餐桌10人就餐</option>
                    <option value="11">餐桌11人就餐</option>
                    <option value="12">餐桌12人就餐</option>
                    <option value="13">餐桌13人就餐</option>
                    <option value="14">餐桌14人就餐</option>
                    <option value="15">餐桌15人就餐</option>
                    <option value="16">餐桌16人就餐</option>
                    <option value="17">餐桌17人就餐</option>
                    <option value="18">餐桌18人就餐</option>
                    <option value="19">餐桌19人就餐</option>
                    <option value="20">餐桌20人就餐</option>
                    <option value="21">餐桌21人就餐</option>
                    <option value="22">餐桌22人就餐</option>
                    <option value="23">餐桌23人就餐</option>
                    <option value="24">餐桌24人就餐</option>
                    <option value="25">餐桌25人就餐</option>
                    <option value="26">餐桌26人就餐</option>
                    <option value="27">餐桌27人就餐</option>
                    <option value="28">餐桌28人就餐</option>
                    <option value="29">餐桌29人就餐</option>
                    <option value="30">餐桌30人就餐</option>
                    <option value="0">更多人</option>

                </select>
                </div>
        </div>
        <div className="item">
            类&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;型
            <div className="big">
                
      {this.state.typeData}
                

                
            </div>

        </div>

    </div>
   
    <div className="row">
        <div className="item">预订订金
            <div className="tubiao"><img src="/data/upload/image/meal/GreenTrue.png"/></div>
        </div>
    </div>

    <div className="row">
        <div className="item">暂不支付
            <div className="tubiao"><img src="/data/upload/image/meal/GreenTrue.png"/></div>
        </div>
    </div>

    <div className="row">
        <div className="item">
            <input ref='special' placeholder="请输入特殊要求" type="text" />
        </div>
    </div>

<div className="tent" onClick={this.hideTent} style={tentDisplay}></div>
    
    <div className="dateSelect" style={dateSelectDisplay} >
        <div className="dataText"><p>选择预订日期</p></div>
        <div className="date">
          

{this.state.DateData}
            
        </div>
        
        <div className="time">

{this.state.TimeData}

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
<footer></footer>
            <div id="footer">
                <div className="footer_left" onClick={this.navigateBack} ></div>
                        <div className="footer_right" onClick={this.submitData}>
                        提交订单
                         </div>
            </div>

       </div>


    )
  }
});



var DateItem = React.createClass({
  theClick:function(){
    this.props.theSelect('day',this.props.day);
  },

    render:function() {
        return (
          <div className={this.props.className} onClick={this.theClick}>
                  <div className="day">{this.props.day}</div>
                  <div className="week">{this.props.week}</div>
          </div>
        );
    }
});



var TimeItem = React.createClass({
  theClick:function(){
    this.props.theSelect('time',this.props.dateId,this.props.days,this.props.times);
  },
    render:function() {
        return (
            <div className={this.props.className} key={this.props.dateId} onClick={this.theClick}>{this.props.times}</div>
        );
    }
});




var TypeItem = React.createClass({

  setType:function(){
    this.props.setType(this.props.nums);
  },

    render:function() {
        return (
            <div className="text" onClick={this.setType} key={this.props.nums}><div className="select"><img src={this.props.isTrue} /></div>&nbsp;{this.props.texts}&nbsp;</div>
        );
    }

});




var mainCom = React.render(
  <App />,
  document.body
  );

