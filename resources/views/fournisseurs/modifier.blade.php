@extends('layouts.main')
@section('document-title', 'Fournisseurs')
@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')
    <form action="{{ route('fournisseurs.mettre_a_jour', $O_fournisseur->id) }}" method="POST" class="needs-validation" novalidate>
        <input type="hidden" id="contacts_principal" name="contacts_principal">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <h5 class="m-0"><i class="mdi mdi-contacts  me-2 text-success"></i>
                                    Modifier un fournisseur</h5>
                                <div class="page-title-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>

                        @csrf
                        @method('PUT')
                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-6 ">

                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">
                                        Informations juridiques</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="reference " class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference"
                                           value="{{ old('reference', $O_fournisseur->reference) }}" name="reference" maxlength="20"
                                           required @if(!$modifier_reference) readonly @endif>
                                    @error('reference')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="formJuridique"
                                           class="form-label required">Forme juridique</label>
                                    <select class="form-control @error('forme_juridique') is-invalid @enderror" id="formJuridique" name="forme_juridique">

                                        @foreach ($form_juridique_types as $form_juridique)
                                            <option value="{{ $form_juridique->id }}"
                                                    id="{{ $form_juridique->nom_sur_facture }}"
                                                {{old('forme_juridique', $O_fournisseur->forme_juridique) == $form_juridique->id ? 'selected' : '' }}>
                                                {{ $form_juridique->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('forme_juridique')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="nom" class="form-label required" id="dynamic_label">
                                        Dénomination
                                    </label>
                                    <input type="text" class="form-control @error('nom')is-invalid @enderror" value="{{ old('name', $O_fournisseur->nom) }}"
                                           id="nom" name="nom" required>
                                    @error('nom')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="ice" class="form-label">
                                        ICE
                                    </label>
                                    <input type="text" class="form-control @error('ice') is-invalid @enderror" value="{{ old('ice', $O_fournisseur->ice) }}"
                                           id="ice" name="ice">
                                    @error('ice')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" value="{{ old('email', $O_fournisseur->email) }}"
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
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone"
                                           value="{{ old('telephone', $O_fournisseur->telephone) }}" name="telephone">
                                    @error('telephone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                {{-- </div> --}}
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="rib" class="form-label">
                                        RIB
                                    </label>
                                    <input type="text"
                                           class="form-control @error('rib') is-invalid @enderror"
                                           id="rib" value="{{old('rib',$O_fournisseur->rib)}}"
                                           name="rib">
                                    @error('rib')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 row p-0 m-0">
                                    <div class="col-6 col-lg-6 mb-3">
                                        <label for="note" class="form-label ">
                                            Note
                                        </label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" style="resize: vertical" placeholder="Ajouter note ici ....." id="note"
                                                  name="note" cols="30" rows="5">{{ old('note', $O_fournisseur->note) }}</textarea>
                                        @error('note')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-6 col-lg-6 mb-3">
                                        <label for="note" class="form-label">
                                            Adress
                                        </label>
                                        <textarea class="form-control @error('adresse') is-invalid @enderror" style="resize: vertical" placeholder="Ajouter adress ici ....." id="adresse"
                                                  name="adresse" cols="30" rows="5">{{ old('adresse', $O_fournisseur->adresse) }}</textarea>
                                        @error('adresse')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 row mx-0 a col-12 align-items-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Gestion</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label " for="limite_de_credit-input">Limite de crédit
                                    </label>

                                    <div class="input-group">

                                        <input type="number" step="0.01"
                                               class="form-control @error('limite_de_credit') is-invalid @enderror"
                                               id="limite_de_credit" name="limite_de_credit"
                                               value="{{ old('limite_de_credit', $O_fournisseur->limite_de_credit) }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">MAD</span>
                                        </div>
                                    </div>
                                    @error('limite_de_credit')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #####--Contacts--##### -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- #####--Card Title--##### -->
                            <div class="card-title">
                                <div class="d-flex switch-filter justify-content-between align-items-center">
                                    <h5 class="m-0"><i class="mdi mdi-contacts me-2 text-success"></i>
                                        Contacts
                                    </h5>
                                    <div class="pull-right">
                                        <button id="addContact" type="button" class="btn  btn-soft-success"><i
                                                class="fa fa-plus me-2 "></i>
                                            Ajouter a
                                            contact</button>
                                    </div>
                                </div>
                                <hr class="border">
                            </div>
                            <div class="table-responsive">
                                <table id="table-contacts" data-repeater-list="contacts"
                                       class="table table-striped table-borderless table-vertical-center">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>E-mail</th>
                                        <th>Téléphone</th>
                                        <th>Contact principal</th>
                                        <th></th>
                                    </tr>
                                    <tbody>
                                    @foreach ($O_fournisseur->contacts as $contact)
                                        <tr>
                                            <input type="hidden" name="contacts_id[]" value="{{ $contact->id }}">
                                            <td><input type="text" class="form-control" name="contacts_nom[]"
                                                       value="{{ $contact->nom }}"></td>
                                            <td><input type="text" class="form-control" name="contacts_prenom[]"
                                                       value="{{ $contact->prenom }}"></td>
                                            <td><input type="text" class="form-control" name="contacts_email[]"
                                                       value="{{ $contact->email }}"></td>
                                            <td><input type="text" class="form-control"
                                                       name="contacts_telephone[]" value="{{ $contact->telephone }}">
                                            </td>
                                            <td class="centered-cell"><input type="radio"
                                                                             class="radio form-check-input"
                                                                             onchange="handleRadioChange(this)"
                                                                             name="contacts_principal_" value="1"
                                                    {{ $contact->is_principal ? 'checked' : '' }}>
                                                <span></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="deleteContactRow(this)"><i class="fa fa-trash-alt" ></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>


@endsection
@push('scripts')
    <script>
        $('#rib').inputmask("999 999 9999999999999999 99")

        var table = document.getElementById("table-contacts").getElementsByTagName('tbody')[0];
        let rowIndexCounter = table.rows.length + 1;
        document.addEventListener('DOMContentLoaded', function() {
            $('#formJuridique').select2({
                width: '100%',
                placeholder: 'Selectioner un type'
            })
            $('#contacts_principal').val(getSelectedRow());
            $('#addContact').click(function() {
                addContactRow();
            });
            // Show the default input field based on the initial selected value
        });

        function addContactRow(e) {

            var table = document.getElementById("table-contacts").getElementsByTagName('tbody')[0];
            var newRow = table.insertRow(table.rows.length);

            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
            cell5.classList.add("centered-cell")
            // console.log('table.rows.length is ',table.rows.length)
            var rowIndex = rowIndexCounter++;
            cell1.innerHTML = '<input type="text" class="form-control required" name="contacts_nom[]" required>';
            cell2.innerHTML = '<input type="text" class="form-control required" name="contacts_prenom[]" required>';
            cell3.innerHTML = '<input type="text" class="form-control" name="contacts_email[]">';
            cell4.innerHTML = '<input type="text" class="form-control" name="contacts_telephone[]">';
            cell5.innerHTML =
                '<input type="radio" class="radio form-check-input" name="contacts_principal_" onchange="handleRadioChange(this) value=' +
                rowIndex +
                '><span></span><input type="hidden" class="form-control" name="contacts_nouvelle[]" value = ' + rowIndex +
                ' >';

            cell6.innerHTML =
                '<button type="button" class="btn btn-sm btn-soft-danger" onclick="deleteContactRow(this)"><i class="fa fa-trash-alt" ></i></button>'
        }

        function deleteContactRow(btn) {
            var row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
            updateSelelcted();
        }

        function getSelectedRow() {
            var selectedRow = $('#table-contacts tbody tr').index($('#table-contacts tbody tr:has(.radio:checked)'));
            return selectedRow;
        }

        function updateSelelcted() {
            $('#contacts_principal').val(getSelectedRow());
        }

        function handleRadioChange(radioButton) {
            // Get the selected value
            updateSelelcted();
        }
    </script>
@endpush
