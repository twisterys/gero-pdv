function __datatable_ajax_callback(data){
    for (var i = 0, len = data.columns.length; i < len; i++) {
        if (! data.columns[i].search.value) delete data.columns[i].search;
        if (data.columns[i].searchable === true) delete data.columns[i].searchable;
        if (data.columns[i].orderable === true) delete data.columns[i].orderable;
        if (data.columns[i].data === data.columns[i].name) delete data.columns[i].name;
    }
    delete data.search.regex;

    return data;
}
function __preventMoreThanTwoDecimals(input) {
    if (input.val().includes('.') || input.val().includes(',')) {
        let separator = '.';
        if (input.val().includes(',')){
            separator = ',';
        }
        let arr = input.val().split(separator);
        if (arr[1].length > 2) {
            input.val(arr[0]+separator+arr[1].slice(0,2))
        }
    }
}
