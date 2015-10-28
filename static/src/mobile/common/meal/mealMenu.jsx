/*
* @require ./footer.css
*/
var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
require('/fishstrap/module/es5-shim.js');

var FirstMenuListData = React.createClass({

  clickMenu:function(){
      this.props.setMenuNum(this.props.dishTypeId); 
  },

  render:function(){

    return (
        <li className={this.props.className} key={this.props.key} onClick={this.clickMenu} >{this.props.children}</li>
      )

  }
});


var SecondMenuListData = React.createClass({
  clickMenu:function(){
      this.props.setMenuNum(this.props.dishTypeId); 
  },

  active :function(){ 

      if(this.props.MenuNum == this.props.dishTypeId){

        return 'small active';
        
      }else{
        return this.props.className;
      }


  },

  render:function(){

    return (
        <li className={this.active()} key={this.props.key} onClick={this.clickMenu}>{this.props.title}</li>
      )

  }
});


var Menu = React.createClass({

  getInitialState: function() {

    return {
      data : '',
      menuListData : '',
      MenuNum : '',
      FirstMenuSet: true,
      productList:'',
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

       $.get('/dish/getMenu',{}, function(data) {
          if (data.code == 0) {
          this.setState({
            data: data.data
          });
         
          this.menuList();

          }else{
            alert(data.msg);
          }

        }.bind(this));
    }.bind(this));

  }, 
  setMenuNum:function(dishTypeId){

    // console.log('1:'+this.state.MenuNum);

    this.setState({MenuNum:dishTypeId},function(){
      this.menuList();
    });

    // this.forceUpdate(); 
    // this.menuList();

    // console.log('2:'+this.state.MenuNum);

  },


  menuList:function(){
    var menuData =this.state.data;
    var menuListData = [];
    
    $.each(menuData,function (index, val) {
       console.log(val);


       //只有一级菜单
       if(typeof(val.firstData) != 'undefined' ){

          //如果被选中
          var theSelect = 'first';
          if(this.state.MenuNum == val.dishTypeId){
            theSelect = 'first active';
          }

          menuListData.push( <FirstMenuListData className={theSelect} setMenuNum={this.setMenuNum} key={val.dishTypeId} dishTypeId={val.dishTypeId}> {val.title} </FirstMenuListData>);

       }else{

          //有两级菜单
          menuListData.push( <li className={'big'}  >{menuData[index].title}</li>);
          
          var secondMenu = menuData[index].data;

          $.each(secondMenu,function(index, domEle){
            if(this.state.FirstMenuSet){
              
              this.setState({
                MenuNum:secondMenu[index].dishTypeId,
                FirstMenuSet:false,
              });
            }
            // console.log('3:'+this.state.MenuNum);
            //遍历二级菜单
            menuListData.push( <SecondMenuListData 
              key={secondMenu[index].dishTypeId} 
              dishTypeId={secondMenu[index].dishTypeId} 
              MenuNum={this.state.MenuNum} 
              setMenuNum={this.setMenuNum} 
              className={'small'} 
              title={secondMenu[index].title} 
               />);
          }.bind(this));

       }


    }.bind(this));

    this.setState({menuListData:menuListData});

    // console.log(this.state.MenuNum);

    //遍历该菜单下的产品
    var productData =[];
    $.each(this.state.data, function(index, val1) {
          if(typeof(val1.firstData) != 'undefined' ){

              //如果是一级菜单
            if(val1.dishTypeId == this.state.MenuNum){
              $.each(val1.firstData, function(index, dish) {
                 productData.push(<ProductSingle 
                  key={dish.dishId}  
                  dishId={dish.dishId} 
                  dishPrice={dish.dishPrice} 
                  dishName={dish.dishName} 
                  thumb_icon={dish.thumb_icon}
                  menuList={this.menuList}
                  totalNum={this.props.totalNum}
                  />);
              }.bind(this));
            }

          }else{
            //二级菜单
               $.each(val1.data, function(index, val2) {
                if(val2.dishTypeId == this.state.MenuNum){
                  $.each(val2.menu, function(index, dish) {
                     productData.push(<ProductSingle 
                      key={dish.dishId}  
                      dishId={dish.dishId} 
                      dishPrice={dish.dishPrice} 
                      dishName={dish.dishName} 
                      thumb_icon={dish.thumb_icon}
                      menuList={this.menuList}
                      totalNum={this.props.totalNum}
                      />);
                  }.bind(this));
                 return;
                };
               }.bind(this));
          }



    }.bind(this));
    // console.log(productData);
    this.setState({
      productList:productData
    });
    
  },


  render:function(){
    return (

      <div id="main">
        <div id="type">
            <ul>
            {this.state.menuListData}

            </ul>
        </div>
        <div id="detail">
            <ul> 
                {this.state.productList}
            </ul>

          
        </div>
        <div className="clearfix"></div>
    </div>

    )
  }

});

var ProductSingle = React.createClass({
  getInitialState: function() {

    if(typeof(sessionStorage.cart) == 'undefined'){
      sessionStorage.cart = '{}';
    }

    return {
      cartNum:'0',
    };

  },


select:function(){
    // console.log(sessionStorage.cart);
    var cart = JSON.parse(sessionStorage.cart);
    // console.log(cart);
    cart[this.props.dishId]={dishName:this.props.dishName,dishPrice:this.props.dishPrice,num:1,remark:''}
    // console.log(cart);
    sessionStorage.cart = JSON.stringify(cart);
    // console.log(sessionStorage.cart);
    
    this.props.menuList();
    this.props.totalNum();
},

showNumDispaly:function(){
  var cartData = JSON.parse(sessionStorage.cart);
  if( (typeof(cartData[this.props.dishId]) != 'undefined')  && (cartData[this.props.dishId]['num'] > 0)){
    return false;
  }else{
    return true;
  }
},

jia:function(){
  var cart = JSON.parse(sessionStorage.cart);
  cart[this.props.dishId]['num'] = cart[this.props.dishId]['num'] + 1;
  sessionStorage.cart = JSON.stringify(cart);
  this.props.menuList();
  this.props.totalNum();
},

jian:function(){
  var cart = JSON.parse(sessionStorage.cart);
  cart[this.props.dishId]['num'] = cart[this.props.dishId]['num'] - 1;
  sessionStorage.cart = JSON.stringify(cart);
  this.props.menuList();
  this.props.totalNum();
},

shuliang:function(){
   var cartData = JSON.parse(sessionStorage.cart);
  if(!this.showNumDispaly()){
   return <div className='shuliang'>
   <span className='jian' onClick={this.jian}>-</span>
   <span className='shumu'>{cartData[this.props.dishId]['num']}</span>
   <span className='jia'  onClick={this.jia}>+</span></div> 
  }else{
    return;
  }
},

  render:function(){
    var NumDispaly = {
      display: this.showNumDispaly() ? 'block' : 'none'
    };

    return (
      
        <li key={this.props.key}>
            <div className="select"  style={NumDispaly} onClick={this.select}></div>
            <div className="select2"  style={NumDispaly} onClick={this.select}></div>
        <a href={'mealProduct.html?dishId='+this.props.dishId}>
            <div className='menu_info'>
                <p className="menu_title">{this.props.dishName}</p>
                <p className="menu_price">￥<span name='price'>{this.props.dishPrice}</span>/份</p>
            </div>
            <div className="menu_img" style={NumDispaly}>
                <img src={this.props.thumb_icon} alt="" />
            </div>
        </a>
            {this.shuliang()}

        </li>
      
      )
  }
});





var App = React.createClass({
  getInitialState: function() {
      var totalNumb = 0;
      var totalMon =0;

      //初始化购物车数量和总价 
    if(typeof(sessionStorage.cart) != 'undefined'){
      var cart = JSON.parse(sessionStorage.cart);
      $.each(cart, function(index, val) {

         totalNumb += val['num'];
         totalMon += val['num'] * val['dishPrice'];

      });
    }else{
      sessionStorage.cart = '{}';
    }
  
    return {
      cartNum:totalNumb,
      totalMoney:totalMon
    };

  },

 bgCart:function(){
  if(this.state.cartNum == 0){
   return {backgroundImage: 'url(/data/upload/image/meal/gouwulan.png)'};
  }else{
    return ;
  } 
 }, 

totalNum:function(){
    var cart = JSON.parse(sessionStorage.cart);
    var totalNumb = 0;
    var totalMon =0;
    $.each(cart, function(index, val) {
      console.log(val['dishPrice']);
       totalNumb += val['num'];
       totalMon += val['num'] * val['dishPrice'];
       console.log(totalMon);
    });


    this.setState({
      cartNum:totalNumb,
      totalMoney:totalMon
    });
},
navigateBack: function(){
    location.href = 'meal.html';
},

  render:function(){

    var cartNumb = this.state.cartNum > 0 ? this.state.cartNum :  '' ;
    
    return (
    <div>

    <Menu totalNum={this.totalNum} />

    <div id="footer">
        <div className="footer_left" onClick={this.navigateBack} ></div>

        <div className="footer_min">
            <span name='sum'>{this.state.totalMoney.toFixed(2)}</span> 
            <span id="qianfuhao">￥</span>
            <span id="gouwulan" style={this.bgCart()} ><p className="shuzi">{cartNumb}</p></span>
        </div>
        <a href='mealCart.html'> 
        <div className="footer_right">
            下一步
        </div>
        </a>
    </div>

    </div>
    )
  }
});


var mainCom = React.render(
  <App />,
  document.body
  );
