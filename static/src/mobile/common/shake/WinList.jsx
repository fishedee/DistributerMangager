var React = require('/fishstrap/react/react-debug.js');
var $ = require('/mobile/common/core/core.js');
var IScroll = require('/fishstrap/module/iscroll.js');

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
        console.log(data.msg);
        return;
      }

      this.setState({winList: data.data});
      this.listData();
      window.myscroll = new IScroll(React.findDOMNode(this.refs.winContent));
    }.bind(this));

   },

   listData:function(){

    var winList = this.state.winList;

    if(!Array.isArray(winList)) throw new Error ('中奖名单winsList必须是数组');

    var list = winList.map(function(listArray){
      return <li key={_.uniqueId()} >{listArray.nickName}一一[{listArray.title}]</li>
    });

     this.setState({list: list});

   },

   winningResults:function(){
    if(this.props.errorNum == 0){
      return <div><p>您已获得:</p><p className="prizeText">{this.props.rotateFinishTip}</p></div>
    }else{
      return <div><p>您已参与本次抽奖活动</p><p>只限一次哦!</p></div>
    }
   },

  render:function(){

    var ListStyle = {
      zIndex:this.props.WinListDisplay ? '5' : '-5',
    }

    return (

  <div style={ListStyle} className='txtMarquee-left'>

    <div className='bd'>
    <div className='clock' onClick={this.props.WinListClock}><p>╳</p></div>
    <div className='row'>
      <div ref='winContent' id='wrapper'>
          <ul id='scroll'>

          {this.state.list}

          <li>………</li>
          </ul>
        </div>
    </div>
      <div className='prize'>

        {this.winningResults()}

        <a href='luckylist.html'><div className='zzzx'>查看奖品</div></a>
      </div>
  </div>  
  </div>

    )
  },

});
