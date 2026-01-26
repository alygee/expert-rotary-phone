// select v 3.0
(function ($) {
  $.fn.mySelect = function (options) {
    options = $.extend({}, options);
    var make = function (a, b) {
      var _ = $(this);
      var html_sel = _;
      html_sel = html_sel.html().replace(/option/g, 'li');
      _.before(
        '<div class="wrapper_ul_select wrapper_new_class' +
          a +
          '"><span></span></div><ul class="select_li">' +
          html_sel +
          '</ul>'
      );
      _.prev('.select_li').css('display', 'none');
      _.prev('.select_li')
        .children('li')
        .removeAttr('value')
        .removeAttr('selected');

      var sel_index = _[0].selectedIndex;
      var sel_text = _.prev('ul')
        .children('li:eq(' + sel_index + ')')
        .text();
      _.prev('.select_li').find('li').removeClass('active-li');
      _.prev('ul')
        .children('li:eq(' + sel_index + ')')
        .css('background', '#caccb6')
        .css('color', '#fff')
        .addClass('active-li');
      _.prev('ul').prev('.wrapper_ul_select').children('span').text(sel_text);
      _.prev('ul').children('li').css('cursor', 'default');

      var width_ul = _.prev('ul').prev('.wrapper_ul_select').outerWidth();

      _.prev('ul').css({
        width: width_ul + 'px',
        'list-style': 'none',
      });

      _.prev('ul')
        .prev('.wrapper_ul_select')
        .bind('click', function (event) {
          if ($(this).next().css('display') == 'none') {
            $(this).addClass('active-select');
            if ($('.wrapper_ul_select').length > 1) {
              $('.wrapper_ul_select')
                .next()
                .each(function () {
                  if ($(this).css('display', 'block')) {
                    $(this).css('display', 'none');
                  }
                });
            }
            $(this).next().css('display', 'block');
          } else {
            $('.wrapper_ul_select').removeClass('active-select');
            $(this).next().css('display', 'none');
          }

          $(document).bind('click.myEvent', function (e) {
            if (_.prev('.select_li').css('display') == 'block') {
              $('.select_li').css('display', 'none');
              $('.wrapper_ul_select').removeClass('active-select');
              $(document).unbind('click.myEvent');
            }
          });

          _.prev('ul')
            .children('li')
            .bind('click.myEventli', function () {
              var data_sel_text = $(this).text();
              // ***
              var val = $(this)
                .parent('ul')
                .next('select')
                .children('option:contains(' + data_sel_text + ')')
                .attr('value');
              $(this).parent('ul').next('select').val(val).trigger('change');

              var index_select_li = $(this).index();
              $(this).parent('ul').next('select')[0].selectedIndex =
                index_select_li;
              $(this)
                .parent('ul')
                .prev('.wrapper_ul_select')
                .children('span')
                .text(data_sel_text);

              _.prev('ul')
                .children('li')
                .each(function () {
                  if ($(this).attr('style')) {
                    $(this).removeAttr('style');
                  }
                });
              _.prev('.select_li').find('li').removeClass('active-li');
              $(this)
                .css('background', '#caccb6')
                .css('color', '#fff')
                .addClass('active-li');
              _.prev('ul').children('li').css('cursor', 'default');
              _.prev('ul').children('li').unbind('click.myEventli');
            });
          return false;
        });
    };
    return this.each(make);
  };
})(jQuery);
