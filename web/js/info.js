;(function($) {
    $.info = {
        _render: null,
        _displayTimeout: 5000,
        _count: 0,

        init: function(options){
            var info = this;
            info._render = options.renderTo;
        },

        display: function(title, text){
            var info = this;
            info._count++;
            info._render.prepend('<div id="info' + info._count + '" class="infoItem"><div class="infoTitle">' + title + '</div><div class="infoText">' + text + '</div></div>');
            setTimeout(function(id, delay){
                $(id).fadeOut();
                setTimeout(function(){$(id).remove();}, delay);
            }, info._displayTimeout, "#info" + info._count, info._displayTimeout);
        }
    }
})(jQuery);