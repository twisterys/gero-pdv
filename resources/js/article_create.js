// form init
$("#i_image").dropify({
    messages: {
        default: "Glissez-déposez un fichier ici ou cliquez",
        replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
        remove: "Supprimer",
        error: "Désolé, le fichier trop volumineux",
    },
});
$("#cat-select").select2({
    placeholder: "...",
    ajax: {
        url: famille_select_ajax_link,
        dataType: "json",
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
    minimumInputLength: 1,
});
$("#unity-select").select2({
    placeholder: "...",
    minimumResultsForSearch: -1,
});
$("#marque").select2({
    allowClear : true,
    placeholder: "...",
    minimumResultsForSearch: -1,
});
$("#tax-select").select2({
    placeholder: "...",
    minimumResultsForSearch: -1,
});
// validation




$('#unite-modal').on('show.bs.modal',function (){
    $(this).find('form').trigger('reset')
})

$(document).on('submit','#unite-form',function (e){
    console.log('unite');
    e.preventDefault();
    let data = $(this).serialize();
    $.ajax({
        url : $(this).attr('action'),
        method:'post',
        data: data,
        success: function (response){
            toastr.success('Unité ajoutée !')
            $("#unity-select")
                .append(
                    `<option selected value="${response.id}">${response.nom}</option>`
                )
                .trigger("change");
            $('#unite-modal').modal('hide');
        },
        error: function (){
            toastr.error('Erreur')
        }
    })
})
$('#family-modal').on('show.bs.modal',function () {
    $(this).find('form').trigger('reset');
});
$('#family-modal').on('shown.bs.modal',function (){
    $(this).find('#couleur-input').spectrum();
})

$(document).on('submit','#family-form',function (e){
    console.log('fimily');

    e.preventDefault();
    let data = $(this).serialize();
    $.ajax({
        url : $(this).attr('action'),
        method:'post',
        data: data,
        success: function (response){
            toastr.success('Famille ajoutée !')
            $("#cat-select")
                .append(
                    `<option selected value="${response.id}">${response.nom}</option>`
                )
                .trigger("change");
            $('#family-modal').modal('hide');
        },
        error: function (){
            toastr.error('Erreur')
        }
    })
})

$('#marque-modal').on('show.bs.modal',function () {
    $(this).find('form').trigger('reset');
});

$(document).on('submit','#marque-form',function (e){
    console.log('marque');

    e.preventDefault();
    let data = $(this).serialize();
    $.ajax({
        url : $(this).attr('action'),
        method:'post',
        data: data,
        success: function (response){
            toastr.success('Marque ajoutée !')
            $("#marque")
                .append(
                    `<option selected value="${response.id}">${response.nom}</option>`
                )
                .trigger("change");
            $('#marque-modal').modal('hide');
        },
        error: function (){
            toastr.error('Erreur')
        }
    })
})
