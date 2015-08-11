var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var WinList = require('./WinList.jsx');

module.exports = React.createClass({

  getInitialState: function() {
    return {
      list:'',
      winList: '',
    };

  },

   componentDidMount : function() {
 
    $.post('/luckydraw/winningList', {
      luckyDrawId:this.props.luckyDrawId
    }, function(data) {
      if (data.code != 0) {
        dialog.message(data.msg);
        return;
      }

      this.setState({winList: data.data});
      this.listData();


      
    }.bind(this));
   },

   listData:function(){

    var winList = this.state.winList;

    if(!Array.isArray(winList)) throw new Error ('中奖名单winsList必须是数组');

    var list = winList.map(function(listArray){
      return <li key={_.uniqueId()} >{listArray.nickName}<span>[{listArray.title}]</span></li>
    });

     this.setState({list: list});

    return list;

   },

  render:function(){


    return (

              <div className='txtMarquee-left'>
          <div className='hd'>
            <p>中奖名单</p>
          </div>
          <div className='bd'>
            <ul className='infoList'>
          {this.state.list}
             </ul>
          </div>

        </div>

    )
  },

});
