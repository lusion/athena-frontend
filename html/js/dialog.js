(function($) {
  $.fn.serializeJSON = function() {
    var json = { };
    jQuery.map($(this).serializeArray(), function(n, i) { json[n['name']] = n['value']; });
    return json;
  };
})(jQuery);


var dialog = (function($) {

  var generatePassword = function() {
    var randomString = function(length, letters) {
      var s = "";
      for (var k = 0; k < length; k++) {
        s += letters[parseInt(Math.random()*letters.length)];
      }
      return s;
    };
    var l = "abcdefghjkmnpqrstwxyz"; var c = "ABCDEFGHJKLMNPQRTXYZ"; var ds = "2346789";
    do {
      value = randomString(1,l) + randomString(4,l+l+l+c+ds) + randomString(1, l+c);
    } while (!((/[0-9]/.test(value) && /[A-Z]/.test(value) && /[^a-zA-Z]/.test(value)) || /[^a-zA-Z0-9]{2}/.test(value)));
    return value;
  };

  var overlay = (function() {
    var self = this,
        $self,
        visible = false;

    $(function() { $self = $('#lightbox-overlay'); });

    self.show = function(fadeTime) {
      if (!$self) return;

      if (show === false) return self.hide();

      $self.css({'width': $('body').width(), 'height': Math.max($('body').height(), $(window).height())});

      if (!visible) {
        $self.fadeIn(fadeTime || 0);
        visible = true;
      }
    };

    self.hide = function(fadeTime) {
      if (!visible || !$self) return;

      $self.fadeOut(fadeTime || 0);
      visible = false;
    }

    return this;
  })();

  // Currently open dialogs
  var dialogs = {};

  // Show/hide the overlay if there any dialogs open (and none are sticky)
  var checkOverlay = function() {
    var hasDialog = false;
    var hasSticky = false;

    $.each(dialogs, function(k, d) {
      if (d) {
        hasDialog = true;
        hasSticky = hasSticky || d.stuck;
      }
    });

    if (hasDialog && hasSticky) overlay.hide(300);
    else if (hasDialog) {
      overlay.show();
    }else{
      overlay.hide();
    }
  };

  // Dialog class
  var dialog = function(id) {
    var self = this;

    var dom = $('<div class="dialog" id="dialog-'+id+'">').appendTo($('#dialogs'));

    dom.data('dialog-instance', this);

    $.tmpl($('#template-'+id), {}).appendTo(dom);

    $('#dialogs').append(dom);

    dom.draggable({'handle': '.header'});
    /* dom.resizable(); */
    $('.header', dom).disableSelection();

    self.stuck = false;

    /* Form submission */
    var busy = null;

    $('form', dom).submit(function(e) {
      $(':input[type=text]', form).attr('title', '').animate({'borderTopColor': '#cccccc', 'borderRightColor': '#cccccc', 'borderBottomColor': '#cccccc', 'borderLeftColor': '#cccccc'});
      $('div.errors ol', form).slideUp();
      try {
        var form = this,
            action = $(this).attr('action'),
            method = $(this).attr('method').toUpperCase(),
            vars = $(this).serializeJSON();

        vars = $.extend(vars, { 'json': 1 });

        $.ajax({
			url: action,
			type: method,
			timeout: 20000,
            data: vars,
			success: function(response, status, xhr) {
              var result = null;
              if ((result = xhr.getResponseHeader('X-JSON-Result')) != null) result = $.parseJSON(result);

              if (result != null && typeof(result) == 'object' && typeof(result['status']) != 'undefined') {
                if (result['status'] == 'error') {

                  var errors = [];
                  if (typeof(result['errors']) != 'undefined') {
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
                  }else if (result['error-message']) {
                    errors.push(result['error-message']);
                  }
                  if (!errors.length) {
                    errors.push('An unknown error occurred.');
                  }

                  var $errors = $('<ol>');
                  for (var k = 0; k < errors.length; k++) {
                    $('<li>').text(errors[k]).appendTo($errors);
                  }
                  $('<div class="errors"></div>').empty().append($errors).prependTo($('div.content', form)).slideUp(0).slideDown();
                } else {
                  $('div.errors', $('div.content', form)).remove();
                  self.close();
                }
              }

              $(':input', form).attr('disabled', false);
              $('#json-wrap').empty().append(response);
			}
        });

        if (!busy) {
          //$('.header', dom).prepend($('<img src="/img/busy.gif"/>'));
        }

        // self.close();
      } catch (e) {

      }

      if (e.preventDefault) e.preventDefault();
      return false;
    });

    $('input.button-action-cancel', dom).click(function(e) {
        var d = $(this).parent().closest('div.dialog').data('dialog-instance');
        d.close();
    });

    self.close = function() {
      dom.remove();
      dialogs[id] = undefined;
      checkOverlay();
    };

    self.sticky = function() {
      self.stuck = !self.stuck;
      dom.find('a.sticky').toggleClass('active', self.stuck);
      checkOverlay();
    };

    self.reset = function() {
      // By default reset to 250px which is just below page header (or middle of screen)
      var x = $(window).width()/2 - dom.width()/2;
      var y = Math.min(250, $(window).height()/2 - dom.height()/2);
      dom.css({'position': 'fixed', 'left': x, 'top': y});
      $('.content :input', dom)
        .val('')
        .first().focus();

      $('.automatic', dom).each(function() {
        var $automatic = $(this),
            $preview = $(this).find('.preview'),
            $form = $(this).find('.form');

        var randomValue = function() {
          var value;

          switch ($automatic.data('mode')) {
          case 'password':
            value = generatePassword();
            break;
          }

          $preview.find('span.value').text(value);
          $form.find(':input').val(value);
        };

        $preview.find('a').click(function() {
          if ($(this).attr('href') == '#generate') {
            randomValue();
          }else{
            $preview.hide();
            $form.show();
            $form.find(':input').val('').focus();
          }
          return false;
        });

        randomValue();
        $form.hide();
      });
    };

    // Once we're loaded, reset
    self.reset();
  };

  // Open a new dialog or reset the existing one
  dialog.open = function(id) {
    if (dialogs[id]) {
      dialogs[id].reset();
    }else{
      dialogs[id] = new dialog(id);
    }
    checkOverlay();
    return dialogs[id];
  };

  dialog.sticky = function(el)
  {
    try {
      var d = $(el).closest('div.dialog').data('dialog-instance');
      d.sticky();
    } catch (e) {

    }
  };

  // Closes a dialog based on dom element
  dialog.close = function(el) {
    try {
      var d = $(el).closest('div.dialog').data('dialog-instance');
      d.close();
    } catch (e) {

    }
  };



  return dialog;
})(jQuery);
