
/***
 * Dropdown navigation
 **/
(function($) {
    $.fn.dropNav = function(options) {
      options = $.extend({ delay: 500, speed: 250 }, options || {});

      this.each(function() {

        var root = this, zIndex = 1000;

        function getSubnav(ele) {
          if (ele.nodeName.toLowerCase() == 'li') {
            var subnav = $('> ul', ele);
            return subnav.length ? subnav[0] : null;
          } else {
            return ele;
          }
        }

        function getActuator(ele) {
          if (ele.nodeName.toLowerCase() == 'ul') {
            return $(ele).parents('li')[0];
          } else {
            return ele;
          }
        }

        function hide() {
          var subnav = getSubnav(this);
          if (!subnav) return;
          $.data(subnav, 'cancelHide', false);
          setTimeout(function() { if (!$.data(subnav, 'cancelHide')) { $(subnav).slideUp(options.speed); } }, options.delay);
        }

        function show() {
          var subnav = getSubnav(this);
          if (!subnav) return;
          $.data(subnav, 'cancelHide', true);
          $(subnav).css({zIndex: zIndex++}).slideDown(options.speed);
          if (this.nodeName.toLowerCase() == 'ul') {
            var li = getActuator(this);
            $(li).addClass('hover');
            $('> a', li).addClass('hover');
          }
        }

        $('ul, li', this).hover(show, hide);
        $('li', this).hover(
          function() { $(this).addClass('hover'); $('> a', this).addClass('hover'); },
          function() { $(this).removeClass('hover'); $('> a', this).removeClass('hover'); }
        );

        $('ul li', this).click(function() {
          hide.call(this.parentNode);
        });
      });

    };

})(jQuery);

$(function() {
    $('ul#domain-dropdown').dropNav({ delay: 0, speed: 0 });

    $('a.close', $('#help')).click(function(e) {
        $('#help').toggle();

        if (e.preventDefault) e.preventDefault();
        return false;
    });

    $('#help-menu-activate').click(function(e) {
        $('#help').toggle();

        if (e.preventDefault) e.preventDefault();
        return false;
    });

});

/***
 * Hash navigation
 * TODO Temporarily disabled; couple bugs and control panel is perhaps more usable without it.
 *
 * $(function() {
 *   var interval = 50,
 *       path     = '';
 *
 *
 *   // Code to handle changes in the hash
 *   var hashNavRequest = null;
 *   function hashChange(path) {
 *     if (hashNavRequest != null && hashNavRequest.abort) hashNavRequest.abort();
 *
 *     // Display loading thing
 *     $('#json-wrap').html('<div class="loading">LOADING</div>');
 *
 *
 *     var temp = decodeURI(window.location);
 *     temp = temp.substr(temp.indexOf(':') + 3);
 *     temp = temp.substr(temp.indexOf('/'));
 *     if (temp.indexOf('#') > 0) temp = temp.substr(0, temp.indexOf('#'));
 *
 *     temp += path;
 *
 *     hashNavRequest = $.ajax({
 *       url: temp,
 *       type: 'POST',
 *       timeout: 20000,
 *       data: { 'json': 1 },
 *       success: function(response, status, xhr) {
 *         var result = null;
 *         if ((result = xhr.getResponseHeader('X-JSON-Result')) != null) result = $.parseJSON(result);
 *
 *         if (result != null && typeof(result) == 'object' && typeof(result['status']) != 'undefined') {
 *           if (result['status'] == 'error') {
 *             alert('Show errors: ' + xhr.getResponseHeader('X-JSON-Result'));
 *           }
 *         }
 *
 *         $('#json-wrap').replaceWith(response);
 *       }
 *     });
 *   }
 *
 *   // Constant checks to see if the hash part changes
 *   path = getActualPath();
 *   setInterval(update, interval);
 *   update();
 *
 *   function getActualPath() {
 *     var url = decodeURI(window.location);
 *     var temp = url.substr(url.indexOf(':') + 3);
 *     temp = temp.substr(temp.indexOf('/'));
 *     return (temp.indexOf('#') == -1) ? '' : temp.substr(0, temp.indexOf('#'));
 *   }
 *
 *   function getHashPath() {
 *     if (typeof(window.location.hash) != 'undefined')
 *         return (window.location.hash == '') ? '' : window.location.hash.substr(1);
 *
 *     var url = decodeURI(window.location);
 *     var temp = url.substr(url.indexOf(':') + 3);
 *     temp = temp.substr(temp.indexOf('/'));
 *     return (temp.indexOf('#') == -1) ? '' : temp.substr(temp.indexOf('#') + 1);
 *   };
 *
 *   function update() {
 *     try {
 *       var current = getHashPath();
 *       if (current == path) return;
 *       path = current;
 *       hashChange(path);
 *     } catch (e) { }
 *   }
 * });
 */

/***
 * Help links
 **/

$(function() {
  $('a.help').live('click', function() {
    // We could open help screen, or we could just do nothing.
    return false;
  });
});


function setupLiveHandlers()
{
    var formAction = null;

    // live calls
    $('#json-wrap form').live('submit', function(event) {
        if ($(this).hasClass('skip-json-submit')) return;

        try {
            var form = $(this);
            var id = $(this).attr('id');
            var action = $(this).attr('action');
            var method = $(this).attr('method').toUpperCase();
            var vars = $(this).serialize();

            vars += "&json=1";

            // Append formAction to front (might be overridden)
            if (formAction != null) vars = "action="+formAction+"&"+vars;

            $(':input', form).attr('disabled', true);
            $.ajax({
                url: action,
                type: method,
                timeout: 20000,
                data: vars,
                success: function(response, status, xhr) {
                    try
                    {
                        var result = null;
                        if ((result = xhr.getResponseHeader('X-JSON-Result')) != null) result = $.parseJSON(result);

                        if (result != null && typeof(result) == 'object' && typeof(result['status']) != 'undefined') {
                            if (result['status'] == 'error') {
                                if (typeof(result['errors']) != 'undefined') {

                                  var errors = [];
                                  for (err in result['errors'])
                                  {
                                    var $el = $(':input[name='+err+']');
                                    if ($el.length) {
                                      $el.animate({'borderTopColor': '#ff0000', 'borderRightColor': '#ff0000', 'borderBottomColor': '#ff0000', 'borderLeftColor': '#ff0000'});
                                      $el.attr('title', result['errors'][err]);
                                    }else{
                                      errors.push(result['errors'][err]);
                                    }
                                  }
                                  if (errors.length) {
                                    var $errors = $('<ol>');
                                    for (var k = 0; k < errors.length; k++) {
                                      $('<li>').text(errors[k]).appendTo($errors);
                                    }
                                    // Not working, no idea why
                                    $('<div class="errors"></div>').append($errors).prependTo($('form#'+ id)).slideUp(0).slideDown();
                                  }
                                }

                            }
                        }

                        $('#json-wrap').replaceWith(response);
                        // $(':input', form).attr('disabled', false);
                    } catch (e) { alert(e); }
                }
            });

        } catch (e) { alert(e); }

        if (event.preventDefault) event.preventDefault();
        return false;
    });

    /*
     jQuery serialize won't include the submit button
    */
    $('#json-wrap form input[type="submit"].button-action').live('click', function(event) {
        if ($(this).hasClass('button-action-add')) formAction = 'add';
        else if ($(this).hasClass('button-action-remove')) formAction = 'remove';
        else if ($(this).hasClass('button-action-edit')) formAction = 'edit';
        else if ($(this).hasClass('button-action-export')) formAction = 'export';
        else formAction = null;
    });

    $('#json-wrap form input[type="checkbox"].select-all').live('click', function(event) {
        try {
            var checked = $(this).is(':checked');
            $(this).parent().parent().parent().find('input[type="checkbox"]').each(function(el) {
                if (!$(this).hasClass('select-all')) $(this).attr('checked', checked);
            });
        } catch (e) { alert(e); }
    });

    /***
     * hashNav temporarily disabled
     *
     * $('#json-wrap a[href^="/sites/"]').live('click', function(event) {
     *     try {
     *         var parts = $(this).attr('href').substr(1).split('/'), n = [];
     *         for (var i = 2; i < parts.length; i++) n.push(parts[i]);
     *         window.location.href = '/sites/' + parts[1] + '/#' + n.join('/');
     *     } catch (e) { alert(e); }

     *     if (event.preventDefault) event.preventDefault();
     *     return false;
     * });

    *$('#json-wrap a[rel="external"]').live('click', function(event) {
    *    try {
    *        window.open(this.href);
    *    } catch (e) { alert(e); }

    *    if (event.preventDefault) event.preventDefault();
    *    return false;
    *});
     **/
}

(function($) {
    var preload = [];
    $.preloadImages = function(path) {
        var len = arguments.length, img;
        for (var i = 1; i < len; i++) {
            try {
                img = new Image;
                img.src = path + arguments[i];
                preload.push(img);
            } catch (e) { }
        }
    }
})(jQuery);

var iface = (function() {
  var self = this;
  self.ready = false;

  return this;
})();

$(document).ready(function() {


    /***
     * hashNav temporarily disabled
     *
     * $('a[href^="/sites/"]', $('#domain-dropdown')).click(function(event) {
     *     try {
     *         var parts = $(this).attr('href').substr(1).split('/'), n = [];
     *         for (var i = 2; i < parts.length; i++) n.push(parts[i]);
     *         window.location.href = '/sites/' + parts[1] + '/#' + n.join('/');
     *     } catch (e) { alert(e); }
     *     if (event.preventDefault) event.preventDefault();
     *     // return false;
     * });

     * $('a[href^="http://"]', $('#domain-dropdown')).click(function(event) {
     *     try {
     *         window.open(this.href);
     *     } catch (e) { alert(e); }
     *     if (event.preventDefault) event.preventDefault();
     *     return false;
     * });
     */

    setupLiveHandlers();

});
