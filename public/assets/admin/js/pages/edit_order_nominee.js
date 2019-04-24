function orderPoint() {
    const cost = nominee.pivot.cost;
    const orderDuration = $('#edit-duration-nominee').val();
    return (cost / 2) * Math.floor(orderDuration * 60 / 15);
}

function allowance() {
    const orderDate = currentOrderStartDate;
    const duration = $('#edit-duration-nominee').val();
    const orderStartDate = moment(orderDate);
    const orderEndDate = moment(orderDate).clone().add(duration, 'hours');
    const orderStartTime = moment().set({
        hour: orderStartDate.get('hour'),
        minute: orderStartDate.get('minute'),
        second: 0
    });
    const orderEndTime = moment().set({hour: orderEndDate.get('hour'), minute: orderEndDate.get('minute'), second: 0});

    const conditionStartTime = moment().set({hour: 0, minute: 1, second: 0});
    const conditionEndTime = moment().set({hour: 4, minute: 0, second: 0});

    let bool = false;
    if (orderStartTime.isBetween(conditionStartTime, conditionEndTime) || orderEndTime.isBetween(conditionStartTime, conditionEndTime) || orderEndTime.isSame(conditionEndTime)) {
        bool = true;
    }

    if (orderStartDate.days() != orderEndDate.days() && orderEndDate.hours() != 0) {
        bool = true;
    }
    return bool ? 4000 : 0;
}

function updateTempPoint() {
    console.log(orderPoint());
    console.log(allowance());
    const tempPoint = orderPoint() + allowance();
    console.log(tempPoint);
    $('#temp-point').text((tempPoint + '').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + 'P');
}

function renderDay() {
    const currentYear = $('#edit-year').val();
    const currentMonth = $('#edit-month').val();
    let currentDay = $('#edit-day').val();
    let currentHour = $('#edit-hour').val();
    let currentMinute = $('#edit-minute').val();
    const selectedMonth = moment(currentYear + '/' + currentMonth);
    const selectedMonthTotalDay = selectedMonth.daysInMonth();

    $('#edit-day').empty();
    if (currentDay > selectedMonthTotalDay) {
        currentDay = '01';
    }

    for (let i= 1; i <= selectedMonthTotalDay; i++) {
        $('#edit-day').append($('<option>', {
            value: (i < 10) ? '0' + i : i,
            text: (i < 10) ? '0' + i : i,
            selected: (i == currentDay) ? true : false
        }));
    }

    currentOrderStartDate = currentYear + '/' + currentMonth + '/' + currentDay + ' ' + currentHour + ':' + currentMinute;

    $('#order-start-date').val(currentOrderStartDate);
    updateTempPoint();
}

$('#edit-month').on('change', function () {
    renderDay();
});

$('#edit-year').on('change', function(){
    renderDay();
});

$('#edit-day').on('change', function(){
    renderDay();
});

$('#edit-hour').on('change', function(){
    renderDay();
});

$('#edit-minute').on('change', function(){
    renderDay();
});

$('#edit-duration-nominee').on('change', function() {
    updateTempPoint();
});
