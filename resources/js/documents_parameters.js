import {default as local} from '../../public/libs/filepond/locale/fr-fr.js';
$(document).ready(function () {
    $('#template-input').select2({
        minimumResultsForSearch : -1
    })
    seed_inputs();
    $('#preview-btn').click(function (){
        window.open(__document_show_url+$('#template-input').val(), '_blank').focus();
    })
    $(document).on('change','#template-input',function(){
        $('#template-settings-content').html(__spinner_element_lg);
        let id = $(this).val();
        $.ajax({
            url:__template_card_url,
            data: {
                template:id
            },
            success: function (response) {
                $('#template-settings-content').html(response);
                seed_inputs()
            }
        })
    })
});

function seed_inputs() {
    const __filepond_options = {
        imagePreview: true,
        acceptedFileTypes: ['image/png', 'image/jpeg'],
        server: {
            load:__image_load,
        },
        allowImageValidateSize: true,
        storeAsFile:true,
        ...local,
        credits: false,
        allowMultiple: false,
        imageValidateSizeMinWidth: 100,
    }
    $('.filepond-input').each(function(){
        let input = $(this);
        let files = [];
        if(input.data('file')){
            files= [{
                source: input.data('file'),
                options: {
                    type: 'local'
                }
            }]
        }
        input.filepond({
            ...__filepond_options,
            imageValidateSizeMaxWidth: input.data('max-width'),
            imageValidateSizeMinHeight:  input.data('max-height'),
            imageValidateSizeMaxHeight:  input.data('max-height'),
            labelIdle: 'Faites glisser votre '+input.data('nom')+' '+input.data('max-width')+' x '+input.data('max-height')+' <span class="filepond--label-action">Parcourir</span>',
            name:input.attr('name'),
            files: files
        });
        $('#coleur-input').spectrum()
    })
    $(document).on('FilePond:removefile','.filepond-input',function (e){
        $(this).closest('form').append('<input type="hidden" name="'+$(e.target).attr('id')+'_delete" value="1" >');
    })
    $(document).on('FilePond:addfile','.filepond-input',function (e){
        console.log('input[name="'+$(e.target).attr('id')+'_delete"')
        $(this).closest('form').find('input[name="'+$(e.target).attr('id')+'_delete"').remove();
    })
}
