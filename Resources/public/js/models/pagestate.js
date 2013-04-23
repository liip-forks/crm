var Oro = Oro || {};

Oro.PageState = Oro.PageState || {};

Oro.PageState.Model = Backbone.Model.extend({
    defaults: {
        restore   : false,
        pagestate : {
            pageId : '',
            data   : {}
        }
    },

    initialize: function () {
        var self = this;

        if ($('form[data-collect=true]').length == 0) {
            return;
        }

        $.get(
            Routing.generate('oro_api_get_pagestate_checkid') + '?pageId=' + this.filterUrl(),
            function (data) {
                self.set({
                    id        : data.id,
                    pagestate : data.pagestate
                });

                if ( parseInt(data.id) > 0  && self.get('restore')) {
                    self.restore();
                }

                setInterval(function() {
                    self.collect();
                }, 2000);

                self.on('change:pagestate', function(model) {
                    self.save(self.get('pagestate'));
                });
            }
        )
    },

    collect: function() {
        var self = this;
        var data = {};

        $('form[data-collect=true]').each(function(index, el){
            data[index] = $(el)
                .find('input, textarea, select')
                .not(':input[type=button], :input[type=submit], :input[type=reset], :input[type=password], :input[type=file]')
                .serializeArray();
        });

        this.set({
            pagestate: {
                pageId : self.filterUrl(),
                data   : JSON.stringify(data)
            }
        });
    },

    restore: function() {
        $.each(JSON.parse(this.get('pagestate').data), function(index, el) {
            form = $('form[data-collect=true]').eq(index);

            $.each(el, function(i, input){
                form.find('[name="'+ input.name+'"]').val(input.value);
            });
        });
    },

    filterUrl: function() {
        var self = this;
        var href = document.location.href;
        var base = href.substr(0, location.href.indexOf('?'));
        var params = href.replace(base + '?', '');

        params = params.split('&');
        if (params.length == 1 && params[0].indexOf('restore') !== -1) {
            self.set('restore', true);
        } else {
            base += '?';
            $(params).each(function(index, el) {
                if (el.indexOf('restore') == -1) {
                    base += el;
                } else {
                    self.set('restore', true);
                }
            })
        }

        return base64_encode(base);
    },

    url: function(method) {
        return this.id
            ? Routing.generate('oro_api_put_pagestate', { id: this.id })
            : Routing.generate('oro_api_post_pagestate');
    }
});

$(function() {
    Oro.pagestate = new Oro.PageState.Model();
})
