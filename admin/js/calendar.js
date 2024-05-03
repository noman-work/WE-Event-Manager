jQuery(document).ready(function ($) {
    // Function to load calendar content via AJAX
    function loadCalendar(month, year) {
        $.ajax({
            url: calendar_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'load_event_calendar',
                month: month,
                year: year
            },
            success: function (response) {
                $('#event-calendar').html(response);
                // Update current_month and current_year in calendar_ajax_object after successful load
                calendar_ajax_object.current_month = month;
                calendar_ajax_object.current_year = year;
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }

    // Load initial calendar
    loadCalendar(calendar_ajax_object.current_month, calendar_ajax_object.current_year);

    // Event listener for previous month button
    $(document).on('click', '#prev-month', function () {
        var currentMonth = parseInt(calendar_ajax_object.current_month);
        var currentYear = parseInt(calendar_ajax_object.current_year);
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        loadCalendar(currentMonth, currentYear);
    });

    // Event listener for next month button
    $(document).on('click', '#next-month', function () {
        var currentMonth = parseInt(calendar_ajax_object.current_month);
        var currentYear = parseInt(calendar_ajax_object.current_year);
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        loadCalendar(currentMonth, currentYear);
    });
});
