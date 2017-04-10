(function ($) {
    'use strict'

    function Frujax(element, options) {
        this.$element = $(element)
        this.options = options || {}
        this.init()
    }

    Frujax.prototype.options = function (options) {
        $.extend(true, this.options, options)
    }

    Frujax.prototype.init = function () {
    }

    Frujax.prototype.destroy = function () {
    }

    Frujax.prototype.attachEvents = function () {
    }

    $.fn.frujax = function () {
        var method, args = []

        method = arguments[0]
        args = Array.prototype.slice.call(arguments, 1)

        return this.each(function () {
            var $this = $(this),
                frujaxObj = $this.data('frujaxObj')

            if (!frujaxObj) {
                var options = $.extend(true, {}, $.fn.frujax.defaults, $this.data('frujax'))

                if (typeof method === 'object') {
                    $.extend(true, options, method)
                    method = null
                }

                $this.data('frujaxObj', (frujaxObj = new Frujax(this, options)))
            }

            if (typeof method === 'string' && typeof frujaxObj[method] !== 'undefined') {
                frujaxObj[method].apply(frujaxObj, args)
            } else if (method) {
                throw new Error('Wrong Frujax method call.')
            }

            if (method === 'destroy') {
                $this.data('frujaxObj', null)
            }
        })
    }

    $.fn.frujax.defaults = {
        classes: {
            ajaxPending: 'frujax-ajax-pending',
            ajaxSuccess: 'frujax-ajax-success',
            ajaxError: 'frujax-ajax-error'
        },
        onBeforeSend: function () {
        },
        onAlways: function () {
        },
        onDone: function () {
        },
        onFail: function () {
        }
    }

    $(document)
        .ready(function () {
            $('[data-frujax]').frujax()
        })
})(window.jQuery)
