@extends('layouts.main')
@section('document-title','Références')
@push('styles')
    <style>
        .autocomplete-popup {
            background-color: white;
            position: absolute;
            z-index: 1;
            border-radius: var(--bs-border-radius);
            border: var(--bs-border-style) 1px #e8e8e8;
            box-shadow: var(--bs-box-shadow);
            left: .6rem;
            right: .6rem;
            top: calc(100% + .2rem);
            display: none;
            padding: .5rem 0;
            overflow: hidden;
        }
        .autocomplete-item {
            padding: .5rem 1rem;
            cursor: pointer;
        }
        .autocomplete-item:hover{
            background-color: var(--bs-primary);
            color: white;
        }
    </style>
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex switch-filter justify-content-between align-items-center">
                       <div class="d-flex">
                           <a href="{{route('parametres.liste')}}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                           <h5 class="m-0 align-items-start">Références</h5>
                       </div>
                        <button class="btn btn-soft-warning " data-bs-target="#reference-global-modal" data-bs-toggle="modal" id="paiement-btn">
                            <i class="fa fa-pen"></i>
                            Modifier les références en masse
                        </button>

                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Référence</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($o_reference as $key=> $reference)
                            <tr>
                                <th scope="row">{{ $reference->nom }}</th>
                                <td>{{ $generatedReferences[$key] }}</td>
                                <td>
                                    <a data-url="{{route('references.modifier',$reference->id)}}" data-target="edit-ref-modal"
                                       class="__datatable-edit-modal btn btn-sm btn-soft-warning">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal fade" id="edit-ref-modal" tabindex="-1" aria-labelledby="edit-ref-modal-title" aria-hidden="true"
                     style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        </div>
                        <!-- /.modal-content -->
                    </div>

                    <!-- /.modal-dialog -->
                </div>
                <div class="modal fade" id="reference-global-modal" tabindex="-1" aria-labelledby="reference-global-modal-title"
                     aria-hidden="true"
                     style="display: none;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
                                    Modifier les références en masse </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form method="POST" action="{{route('references.modifier.global')}}" class="needs-validation" novalidate autocomplete="off">
                                @csrf
                                @method('POST')

                                <div class="modal-body">
                                    <h6>Mots clés:</h6>
                                    <ul>
                                        <li>[a]: Année à 2 chiffres</li>
                                        <li>[A]: Année complète</li>
                                        <li>[m]: Mois à 2 chiffres</li>
                                        <li>[j]: Jour à 2 chiffres</li>
                                        <li>[n]: Numérotation</li>
                                    </ul>
                                    <p>Cette modification va s'appliquer sur toutes les references du type choisi</p>

                                    <div class="row">
                                        <div class="col-12 mb-3 position-relative">
                                            <label class="form-label required" for="type">Type</label>
                                            <select name="type" class="form-select"
                                                    id="type">
                                                <option  value="0">Achat</option>
                                                <option  value="1">Vente</option>
                                                <option  value="2">Les deux</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mb-3 position-relative">
                                            <label class="form-label required" for="format">Format</label>
                                            <div class="input-group">
                                                <span class="input-group-text">TYPE</span>
                                                <input type="text" class="form-control format-autocomplete {{$errors->has('format')?'is-invalid':null}}" id="format" placeholder="Entrer votre format" name="format" >
                                            </div>
                                            <div class="autocomplete-popup"></div>
                                            @if($errors->has('format'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('format') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="longueur_compteur">Longueur du compteur</label>
                                            <select required class="select2 form-control mb-3 custom-select {{$errors->has('longueur_compteur')? 'is-invalid':null}} " id="longueur_compteur" name="longueur_compteur">
                                                <option value="">Sélectionner une option</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>

                                            @if($errors->has('longueur_compteur'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('longueur_compteur') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>



                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                                    <button class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const formats = ['[a]', '[A]', '[m]', '[j]','[n]'];
        const labels = {
            '[a]':'Année à 2 chiffres',
            '[A]':'Année complète',
            '[m]':'Mois à 2 chiffres',
            '[j]':'Jour à 2 chiffres',
            '[n]':'Numérotation',
        }
        $(document).on('input', '.format-autocomplete', function () {
            let input = $(this);
            let value = input.val()
            let opening_index = value.lastIndexOf('[');
            let closing_index = value.lastIndexOf(']');
            $('.autocomplete-popup').html('')
            if (opening_index > closing_index && opening_index > -1) {
                let searchable = value.substring(opening_index)
                let filtered = formats.filter(e => e.includes(searchable))
                if (filtered.length > 0) {
                    filtered.forEach(e => {
                        let option = $(`<div class="autocomplete-item" data-key="${e}">${e} - ${labels[e]}</div>`);
                        $('.autocomplete-popup').append(option).show();
                    })
                } else {
                }
            }else{
                $('.autocomplete-popup').hide()
            }
        })
        $(document).on('click','.autocomplete-item',function () {
            let input = $('.format-autocomplete');
            let value = input.val();
            let opening_index = value.lastIndexOf('[');
            input.val(value.substring(0,opening_index)+$(this).data('key')+value.substring(opening_index+$(this).data('key').length));
            input.focus()
            $('.autocomplete-popup').html('').hide()
        })
    </script>
@endpush

