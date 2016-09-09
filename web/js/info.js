;if (typeof jQuery === 'undefined') {
  throw new Error('Notifications require jQuery')
};

(function($) {
    'use strict';
    $.info = {
        _render: null,
        _maxCount: 5,
        _count: 0,
        _lastIndex: 0,
        _url: null,
        _delay: 1000,
        _lastDate: ~~(new Date().getTime() / 1000),
        _intervalId: null,
        onData: null,

        init: function(options){
            var info = this;
            info._render = options.renderTo;
            info._url = options.url;
            info.onData = options.onData;
            info._intervalId = setInterval(function(){
                info.checkNew(info._lastDate);
            }, info._delay * 10);
        },
        
        removeItem: function($item){
            var info = this;
            setTimeout(function(item){
                item.remove();
            }, info._delay, $item);
            $item.fadeOut(info._delay);        
        },

        display: function(title, text, id){
            var info = this;
            info._count++;
            info._lastIndex++;
            var view = $('div[name=' + id + ']');
            if (view){
                info.removeItem(view);
            }
            info._render.prepend('<div name="' + id + '" id="info' + info._lastIndex + '" class="infoItem"><div class="infoTitle">' + title + '</div><div class="infoText">' + text + '</div></div>');
            if (info._count >= info._maxCount){
                var index = info._lastIndex - info._maxCount;
                info.removeItem($('#info' + index));
                info._count--;
            }
        },
        
        setLastDate: function(date){
            this._lastDate = date;
        },
        
        getLastDate: function(date){
            return this._lastDate;
        },
        
        checkNew: function(lastTime){
            var info = this;
            $.post(info._url+'&lasttime=' + lastTime + '&limit=' + info._maxCount, function(data){
                if (!data.length){
                    return;
                }
                if (data === 'stop'){
                    clearInterval(info._intervalId);
                    return;
                }
                $.each(data, function(key, val){
                    info.display(new Date(val.date * 1000).toLocaleDateString("ru"), val.subj, val.id);
                    if (info.getLastDate() < val.updateat){
                        info.setLastDate(val.updateat);
                    }
                });
                info.onData();
            }, 'json')
            .error(function(data){
                console.log(data.response);
            });
        }
    }
})(jQuery);

$(document).ready(function(){
    var options = {
        renderTo: $('#notifications'),
        url: 'http://news/web/index.php?r=site%2Flatestnews',
        onData: function(){
            $.pjax.reload({container:"#news"});
        }
    };
    $.info.init(options);
});