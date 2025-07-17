@extends('layouts.main')
@section('document-title','Paramètres SMTP')
@push('styles')
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('smtpSettings.mettre_a_jour') }}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('parametres.liste') }}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Configuration SMTP</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <div class="row col-12 mx-0">
                        <div class="col-6">
                            <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                <tr>
                                    <th>Option</th>
                                    <th>Valeur</th>
                                </tr>
                                <!-- Host Input -->
                                <tr>
                                    <td>Hôte SMTP</td>
                                    <td>
                                        <input type="text" name="host" class="form-control @error('host') is-invalid @enderror"
                                               value="{{ old('host', $smtp_settings->host ?? '') }}" required>
                                        @error('host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- Port Input -->
                                <tr>
                                    <td>Port SMTP</td>
                                    <td>
                                        <input type="text" name="port" class="form-control @error('port') is-invalid @enderror"
                                               value="{{ old('port', $smtp_settings->port ?? '') }}" required>
                                        @error('port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- Username Input -->
                                <tr>
                                    <td>Nom d'utilisateur SMTP</td>
                                    <td>
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                               value="{{ old('username', $smtp_settings->username ?? '') }}" required>
                                        @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- Password Input -->
                                <tr>
                                    <td>Mot de passe SMTP</td>
                                    <td>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                               value="{{ old('password', $smtp_settings->password ?? '') }}" required>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- Encryption Select -->

                            </table>

                        </div> <div class="col-6">
                            <table class="table table-bordered table-striped mt-3 rounded overflow-hidden">
                                <tr>
                                    <td>Chiffrement SMTP</td>
                                    <td>
                                        <select name="encryption" class="form-select @error('encryption') is-invalid @enderror">
                                            <option value="" @selected(old('encryption', $smtp_settings->encryption ?? '') == '')>Sélectionner le chiffrement</option>
                                            <option value="tls" @selected(old('encryption', $smtp_settings->encryption ?? '') == 'tls')>TLS</option>
                                            <option value="ssl" @selected(old('encryption', $smtp_settings->encryption ?? '') == 'ssl')>SSL</option>
                                        </select>
                                        @error('encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- From Address Input -->
                                <tr>
                                    <td>Adresse de l'expéditeur</td>
                                    <td>
                                        <input type="email" name="from_address" class="form-control @error('from_address') is-invalid @enderror"
                                               value="{{ old('from_address', $smtp_settings->from_address ?? '') }}" required>
                                        @error('from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <!-- From Name Input -->
                                <tr>
                                    <td>Nom de l'expéditeur</td>
                                    <td>
                                        <input type="text" name="from_name" class="form-control @error('from_name') is-invalid @enderror"
                                               value="{{ old('from_name', $smtp_settings->from_name ?? '') }}" required>
                                        @error('from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                            </table>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush

