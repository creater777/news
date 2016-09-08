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

        init: function(options){
            var info = this;
            info._render = options.renderTo;
            info._url = options.url;
            info._intervalId = setInterval(function(){
                info.checkNew(info._lastDate);
            }, info._delay * 20);
        },

        display: function(title, text){
            var info = this;
            info._count++;
            info._lastIndex++;
            if (info._count >= info._maxCount){
                var index = info._lastIndex - info._maxCount;
                setTimeout(function(_index, _lastIndex){
                    $('#info' + _index).remove();
                    info._render.prepend('<div id="info' + _lastIndex + '" class="infoItem"><div class="infoTitle">' + title + '</div><div class="infoText">' + text + '</div></div>');
                }, info._delay, index, info._lastIndex);
                $('#info' + index).fadeOut(info._delay);
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
            $.post(info._url+'&lasttime=' + lastTime + '&limit=' + info._maxCount, null, null, 'json')
            .done(function(data){
                if (!data.length){
                    return;
                }
                $.each(data, function(key, val){
                    info.display(new Date(val.date * 1000).toLocaleDateString("ru"), val.subj);
                    if (info.getLastDate() < val.updateat){
                        info.setLastDate(val.updateat);
                    }
                });
                    
            })
            .error(function(data){
                console.log(data);
            });
        }
    }
})(jQuery);

$(document).ready(function(){
    var options = {
        renderTo: $('#notifications'),
        url: 'http://news/web/index.php?r=site%2Flatestnews'
    };
    $.info.init(options);
});