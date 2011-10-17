$(function() {
  $('#select-domain-js').disableSelection();
  $('li, li a', $('#select-domain-js')).disableSelection();

  var currentPage = function() {
    var url = window.location.href.split('/');
    while (url[0] != 'site' && url.length) url.shift();
    if (url.length > 1) {
      url.shift(); url.shift();
      return '/' + url.join('/');
    }else return '';
  };

  var dropdown = $('#site-choice');

  $('#site-choice').hover(function() {
      dropdown.addClass('expanded');
  }, function() {
      dropdown.removeClass('expanded');
  });

  $('#site-choice li').hover(function() {
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });

  $('#site-choice li a').click(function(e) {
    window.location.href = $(this).attr('href')+currentPage();
    e.preventDefault();
    return false;
  });


});
