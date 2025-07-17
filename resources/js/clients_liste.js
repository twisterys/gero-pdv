$('.filter-btn').click(e => {
    $('.switch-filter').toggleClass('d-none')
})
$('#forme-juridique-select').select2({
    width: '100%',
    allowClear:!1,
    minimumResultsForSearch: -1,

})
