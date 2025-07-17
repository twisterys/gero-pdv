<style>
    .datepicker {
        z-index: 1080 !important;
    }
</style>
<div class="modal-header">
    <h5 class="modal-title align-self-center" id="exercise-modal-title">Changer d'exercice</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('exercice.mettre_en_place')}}">
    @csrf
    @method('put')
    <div class="modal-body">
        <div class="switch">
            <label for="i_exercice">Exercice</label>
            <div class="input-group">
                <select name="i_exercice" class="form-select" id="i_exercice">
                    @foreach($exercices as $exercice)
                        <option @if(+$exercice->annee === +session()->get('exercice')) selected
                                @endif value="{{$exercice->annee}}">{{$exercice->annee}}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-soft-success switch-btn"><i class="fa fa-plus icon"></i><span
                        class="d-none icon spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        <div class="switch d-none">
            <label for="year-cal">Année</label>
            <div class="input-group">
                <input type="text" id="year-cal" class="form-control" name="i_annee">
                <button type="button" class="btn btn-soft-danger switch-btn "><i class="fa fa-times"></i></button>
                <button type="button" class="btn btn-soft-success" id="add-btn"><i class="fa fa-check"></i></button>
                <div class="invalid-feedback" id="__errors">

                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button  class="btn btn-primary">Enregistrer</button>
    </div>
</form>
<script>
    var __add_exercice_process = 0;
    $('.switch-btn').on('click', function () {
        $('.switch').toggleClass('d-none')
        $('#year-cal').removeClass('is-invalid').val('');
    })
    $('#add-btn').click(e=>{
        if(__add_exercice_process ===0){
            __add_exercice_process =1;
            let html = $('#add-btn').html();
            let input= $('#year-cal')
            $('#add-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            $.ajax({
                url:'{{route("exercice.sauvegarder")}}',
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':'{{@csrf_token()}}'
                },
                data:{
                  i_year:input.val()
                },
                success: response => {
                    input.removeClass('is-invalid');
                    input.val('');
                    $('#add-btn').html(html)
                    __add_exercice_process =0;
                    $('#i_exercice').append(`<option value="${response.annee}" >${response.annee}</option>`)
                    $('.switch').toggleClass('d-none');
                    toastr.success('Exercice ajouté')
                },
                error: re=> {
                    $('#add-btn').html(html)
                    __add_exercice_process =0;
                    if(typeof re.responseJSON.errors.i_year !== 'undefined'){
                        let errors = re.responseJSON.errors.i_year  ;
                        input.addClass('is-invalid');
                        $('#__errors').html('')
                        errors.forEach(error=>{
                          $('#__errors').append(error+'<br>')
                        })
                    }
                }
            })
        }
    })
    $('#year-cal').datepicker({
        changeMonth: false,
        autoclose: true,
        language:'fr',
        changeYear: true,
        showButtonPanel: true,
        viewMode: "years",
        minViewMode: "years",
        format: 'yyyy',
        endDate: "+1y",
    })
</script>
