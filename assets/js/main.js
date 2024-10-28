jQuery(document).ready(function () {
(function($){
  var attendance_check = $('.p-attendance-check');
  if ( attendance_check.length > 0 ) {
    var attendance_history = $('.p-attendance-check').data('history');
    var current = {
      'year' : moment().year(),
      'month' : moment().month() + 1,
      'day' : moment().date()
    };
    if ( pac_ajax_obj.test_mode ) {
      current.year -= pac_ajax_obj.year_minus;
      current.month -= pac_ajax_obj.month_minus;
      current.day -= pac_ajax_obj.day_minus;
    }
    var theyear = current.year;
    var themonth = current.month;
    var stamp_btn = $('.p-attendance-check__stamp_btn');
    stamp_btn.on('click', function(){
      var self = $(this);
      if ( !self.hasClass('loading') ) {
        self.addClass('loading');
        var data = {
          action : 'attendance_check',
          check_today : true,
        };
        $.post(pac_ajax_obj.ajax_url, data,
          function (response) {
            if ( response.code == 200 ) {
              self.hide();
              self.siblings('img')[0].src = pac_ajax_obj.seal_src;
              
              // 완전 처음 들어온경우
              if ( !attendance_history ) {
                attendance_history = [];
                attendance_history[current.year] = [];
                attendance_history[current.year][current.month] = [];
              }
              // 처음 들어온건 아니지만, year이 없는 경우
              if ( attendance_history[current.year] === undefined ) {
                attendance_history[current.year] = [];
                attendance_history[current.year][current.month] = [];
              }
              // year은 있는데 month가 없는경우
              if ( attendance_history[current.year][current.month] === undefined ) {
                attendance_history[current.year][current.month] = [];
              }
              
              // day는 당연히 없겠지! 이제 넣자!
              attendance_history[current.year][current.month][current.day] = true;
              self.remove();
            } else {
              self.removeClass('loading');
            }
            alert( response.message );
          },
          'json'
        );
      }
    });

    render( theyear, themonth );

    function render(year, month) {
      $(".p-attendance-check .p-attendance-check__days > li").remove(); // remove only in days
      var days_in_month = get_days_in_month(year, month);
      var first_day_in_month = get_first_day_in_month(year, month);
      var months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
      var no_seal = $('.p-attendance-check__clone > .p-attendance-check__days__date--no_seal');
      var yes_seal = $('.p-attendance-check__clone > .p-attendance-check__days__date--yes_seal');
      var p_calendar_days = $('.p-attendance-check__days');
      var empty_rear_count = (7 * 5) - ( first_day_in_month + days_in_month );
      if ( empty_rear_count < 0 ) {
        empty_rear_count = first_day_in_month;
      }

      $('.p-attendance-check__header__monthname').text(year+"."+months[month-1]);
      for ( var i = 0; i < first_day_in_month; i++ ) {
        p_calendar_days.append($('<li class="empty">&nbsp;</li>'));
      }
      for ( var i = 1; i <= days_in_month; i++ ) {
        var seal = no_seal.clone();
        if ( is_checked(i, month, year) ) {
          seal = yes_seal.clone();
        }
        seal.find('.p-attendance-check__days__date__number').text(i);

        if ( is_today(i, month, year) ) {
          seal.addClass('p-attendance-check__days__date--today');
          // check if attendance already
          if ( ! seal.hasClass('p-attendance-check__days__date--yes_seal') ) {
            var new_stamp_btn = stamp_btn.clone(true);
            seal.append(new_stamp_btn);
          }
        }

        p_calendar_days.append( seal );
      }
      for ( var i = 0; i < empty_rear_count; i++ ) {
        p_calendar_days.append($('<li class="empty">&nbsp;</li>'));
      }
    }

    function get_first_day_in_month(year, month) {
      if ( month < 10 ) {
        month = "0" + month;
      }
      var format = year + "-" + month + "-01 00:00:00";
      return moment(format).weekday();
    }
    function get_days_in_month(year, month) {
      if ( month < 10 ) {
        month = "0" + month;
      }
      return moment((year + "-" + month), "YYYY-MM").daysInMonth();
    }
    function is_today(day, month, year) {
      if ( (day == current.day) && (month == current.month) && (year == current.year) ) {
        return true;
      }
      return false;
    }
    function is_checked(day, month, year) {
      if ( attendance_history[year] !== undefined && attendance_history[year][month] !== undefined && attendance_history[year][month][day] !== undefined ) {
        return true;
      }
      return false;
    }

    $('.p-attendance-check__header__pointer--minusmonth .fas').click(function(){
      themonth -= 1;
      if ( themonth == 0 ) {
        theyear -= 1;
        themonth = 12;
      }
      render(theyear, themonth);
    });

    $('.p-attendance-check__header__pointer--addmonth .fas').click(function(){
      themonth += 1;
      if ( themonth == 12 ) {
        theyear += 1;
        themonth = 1;
      }
      render(theyear, themonth);
    });
  }
})(jQuery);
});