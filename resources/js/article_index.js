$('.filter-btn').click(e => {
    $('.switch-filter').toggleClass('d-none')
})
$('#cat-select').select2({
    width: '100%',
    placeholder: {
        id:'',
        text:'Tous'
    },
    allowClear:!0,
    ajax: {
        url: famille_select_ajax_link,
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                term: params.term,
            };
        },
        processResults: function (data) {
            return {
                results: data,
            };
        },
        cache: false,
    },
})
$('#status-select').select2({
    width: '100%',
})
$('#created_by-select').select2({
    width: '100%',
})
$('#datepicker2').daterangepicker({
    ranges: __datepicker_dates,
    locale: {
        format: "DD/MM/YYYY",
        separator: " - ",
        applyLabel: "Appliquer",
        cancelLabel: "Annuler",
        fromLabel: "De",
        toLabel: "à",
        customRangeLabel: "Plage personnalisée",
        weekLabel: "S",
        daysOfWeek: [
            "Di",
            "Lu",
            "Ma",
            "Me",
            "Je",
            "Ve",
            "Sa"
        ],
        monthNames: [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Août",
            "Septembre",
            "Octobre",
            "Novembre",
            "Décembre"
        ],
        firstDay: 1
    },
    startDate: __datepicker_start_date,
    endDate: __datepicker_end_date,
    minDate: __datepicker_min_date,
    maxDate: __datepicker_max_date
})
