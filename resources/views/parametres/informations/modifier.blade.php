@extends('layouts.main')
@section('document-title','Informations Entreprise')
@push('styles')
@endpush
@section('page')
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <form method="POST" enctype="multipart/form-data"
                    action="{{ route('informations.mettre_a_jour') }}" class="needs-validation"
                    novalidate autocomplete="off">
                  <!-- #####--Card Title--##### -->
                  <div class="card-title">
                      <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                          <div class="d-flex align-items-center">
                              <a href="{{route('parametres.liste')}}">
                                  <i class="fa fa-arrow-left text-success me-2"></i>
                              </a>
                              <h5 class="m-0">Informations entreprise</h5>
                          </div>
                          <div class="pull-right">
                              <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                          </div>
                      </div>
                      <hr class="border">
                  </div>
                  @csrf
                  @method('PUT')

                  <div class="row px-3 align-items-start ">
                      <div class="row col-md-12 ">


                          <div class="col-12 col-lg-6 mb-3">
                              <label for="formJuridique"
                                     class="form-label required">Forme juridique</label>
                              <select class="form-control @error('forme_juridique') is-invalid @enderror" id="formJuridique" name="forme_juridique">
                                  @foreach ($form_juridique_types as  $form)
                                      <option value="{{ $form->id }}" {{ !is_null($o_client) && old('forme_juridique', $o_client->forme_juridique) == $form->id ? 'selected' : '' }}>
                                          {{ $form->nom }}
                                      </option>
                                  @endforeach
                              </select>

                              @error('forme_juridique')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror

                          </div>
                          <div class="col-12 col-lg-6 mb-3">
                              <label for="raison_social" class="form-label required" id="dynamic_label">
                                  Raison sociale
                              </label>
                              <input type="text" class="form-control @error('raison_social') is-invalid @enderror"
                                     value="{{ !is_null($o_client) ? old('raison_social', $o_client->raison_social) : old('raison_social') }}"
                                     id="raison_social" name="raison_social" required>

                              @error('raison_social')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>
                          <div class="col-12 col-lg-6 mb-3">
                              <label for="ice" class="form-label">
                                  ICE
                              </label>
                              <input type="text" class="form-control @error('ice') is-invalid @enderror"
                                     value="{{ !is_null($o_client) ? old('ice', $o_client->ice) : old('ice') }}"
                                     id="ice" name="ice">
                              @error('ice')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>
                          <div class="col-12 col-lg-4 mb-3">
                              <label for="RC" class="form-label">
                                  RC
                              </label>
                              <input type="text" class="form-control @error('RC') is-invalid @enderror"
                                     value="{{ !is_null($o_client) ? old('RC', $o_client->RC) : old('RC') }}"
                                     id="RC" name="RC">
                              @error('RC')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>

                          <div class="col-12 col-lg-2 mb-3">
                              <label for="ville" class="form-label">
                                  Ville
                              </label>
                              <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                     value="{{ !is_null($o_client) ? old('ville', $o_client->ville) : old('ville') }}"
                                     id="ville" name="ville">
                              @error('ville')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>
                          <div class="col-12 col-lg-6 mb-3">
                              <label for="IF" class="form-label">
                                  IF
                              </label>
                              <input type="text" class="form-control @error('IF') is-invalid @enderror"
                                     value="{{ !is_null($o_client) ? old('IF', $o_client->IF) : old('IF') }}"
                                     id="IF" name="IF">
                              @error('IF')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>

                          <div class="col-12 col-lg-6 mb-3">
                              <label for="email" class="form-label required">
                                  Email
                              </label>
                              <input class="form-control @error('email') is-invalid @enderror" type="text"
                                     value="{{ !is_null($o_client) ? old('email', $o_client->email) : old('email') }}"
                                     name="email" id="example-email-input1">
                              @error('email')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>

                          <div class="col-12 col-lg-6 mb-3">
                              <label for="telephone" class="form-label">
                                  Téléphone
                              </label>
                              <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                     id="telephone"
                                     value="{{ !is_null($o_client) ? old('telephone', $o_client->telephone) : old('telephone') }}" name="telephone">
                              @error('telephone')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>

                          {{-- </div> --}}
                          <div class="col-6 col-lg-6 mb-3">
                              <label for="adresse" class="form-label ">
                                  Adresse
                              </label>
                              <textarea class="form-control @error('adresse') is-invalid @enderror"
                                        style="resize: vertical" placeholder="Ajouter adresse ici ....." id="adresse"
                                        name="adresse" cols="30"
                                        rows="1">{{ !is_null($o_client) ? old('adresse', $o_client->adresse) : old('adresse') }}</textarea>
                              @error('adresse')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>

                          <div class="col-6 col-lg-6 mb-3">
                              <label for="note" class="form-label ">
                                  Note
                              </label>
                              <textarea class="form-control @error('note') is-invalid @enderror"
                                        style="resize: vertical" placeholder="Ajouter note ici ....." id="note"
                                        name="note" cols="30"
                                        rows="1">{{ !is_null($o_client) ? old('note', $o_client->note) : old('note') }}</textarea>
                              @error('note')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                              @enderror
                          </div>


                      </div>
                  </div>


              </form>
          </div>
      </div>
  </div>
@endsection
@push('scripts')
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
@endpush

