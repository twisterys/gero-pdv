@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapports')
@push('styles')
    <style>
        /* Petit style pour l'icône d'aide et l'animation */
        .help-icon {
            border: none;
            background: transparent;
            cursor: pointer;
            color: #6c757d;
            font-size: 1.1rem;
            transition: transform 0.18s ease, color 0.18s ease;
        }
        .help-icon:hover {
            color: #0d6efd;
            transform: scale(1.08);
        }
        .help-icon:active {
            transform: scale(0.96);
        }

        /* Animation pulse when clicked (brief) */
        @keyframes help-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.12); }
            100% { transform: scale(1); }
        }
        .help-icon.pulse {
            animation: help-pulse 320ms ease-in-out;
        }

        /* Modal customisation pour un look moderne */
        .modal-header {
            border-bottom: none;
        }
        .modal-body p { white-space: pre-wrap; }
    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div class="card-title justify-content-between align-items-center">
                <h2><i class="fa fa-chart-pie me-2 text-success"></i> Rapports</h2>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            @if($achat_vente->count() > 0)
                <div class="card-title">
                    <h4>Achats et vente</h4>
                    <hr>
                </div>
            @endif
            <div class="row">
                @foreach($achat_vente as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center d-flex">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                    <button type="button" class="help-icon ms-2" aria-label="Voir les détails">
                                        <i class="fa fa-question-circle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                                {{-- Champ caché contenant les détails complets du rapport --}}
                                <div class="rapport-details d-none">{!! nl2br(e($rapport->details ?? 'Aucun détail disponible pour ce rapport.')) !!}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
           @if($stock->count() > 0)
                <div class="card-title mt-2">
                    <h4>Stock</h4>
                    <hr>
                </div>
           @endif
            <div class="row">
                @foreach($stock as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center d-flex">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                    <button type="button" class="help-icon ms-2" aria-label="Voir les détails">
                                        <i class="fa fa-question-circle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                                {{-- Champ caché contenant les détails complets du rapport --}}
                                <div class="rapport-details d-none">{!! nl2br(e($rapport->details ?? 'Aucun détail disponible pour ce rapport.')) !!}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
           @if($statistiques->count() > 0)
                <div class="card-title mt-2">
                    <h4>Statistiques</h4>
                    <hr>
                </div>
            @endif
            <div class="row">
                @foreach($statistiques as $rapport)
                    <div class="col-md-4 col-sm-6 col-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="card-title m-0 justify-content-between align-items-center d-flex">
                                    <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                    <button type="button" class="help-icon ms-2" aria-label="Voir les détails">
                                        <i class="fa fa-question-circle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body ">
                                <p class="m-0">
                                    {{$rapport->description}}
                                </p>
                                {{-- Champ caché contenant les détails complets du rapport --}}
                                <div class="rapport-details d-none">{!! nl2br(e($rapport->details ?? 'Aucun détail disponible pour ce rapport.')) !!}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

                @if($comptabilité->count() > 0)
                    <div class="card-title mt-2">
                        <h4>Comptabilité</h4>
                        <hr>
                    </div>
                @endif
                <div class="row">
                    @foreach($comptabilité as $rapport)
                        <div class="col-md-4 col-sm-6 col-12 mt-3">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <div class="card-title m-0 justify-content-between align-items-center d-flex">
                                        <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                        <button type="button" class="help-icon ms-2" aria-label="Voir les détails">
                                            <i class="fa fa-question-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <p class="m-0">
                                        {{$rapport->description}}
                                    </p>
                                    {{-- Champ caché contenant les détails complets du rapport --}}
                                    <div class="rapport-details d-none">{!! nl2br(e($rapport->details ?? 'Aucun détail disponible pour ce rapport.')) !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($pos->count() > 0)
                    <div class="card-title mt-2">
                        <h4>Point de vente</h4>
                        <hr>
                    </div>
                @endif
                <div class="row">
                    @foreach($pos as $rapport)
                        <div class="col-md-4 col-sm-6 col-12 mt-3">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <div class="card-title m-0 justify-content-between align-items-center d-flex">
                                        <h4 class="m-0"><a href="{{route('rapports.'.$rapport->route)}}">{{$rapport->nom}}</a></h4>
                                        <button type="button" class="help-icon ms-2" aria-label="Voir les détails">
                                            <i class="fa fa-question-circle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <p class="m-0">
                                        {{$rapport->description}}
                                    </p>
                                    {{-- Champ caché contenant les détails complets du rapport --}}
                                    <div class="rapport-details d-none">{!! nl2br(e($rapport->details ?? 'Aucun détail disponible pour ce rapport.')) !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
        <!-- Modal pour afficher les détails du rapport -->
        <div class="modal fade" id="rapportDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails du rapport</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <div id="rapport-details-content"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Délégué: ouverture du modal et injection du contenu
            document.querySelectorAll('.help-icon').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    // petite animation pulse
                    btn.classList.add('pulse');
                    setTimeout(function () { btn.classList.remove('pulse'); }, 350);

                    // trouve le contenu details dans la même carte
                    const card = btn.closest('.card');
                    const detailsEl = card ? card.querySelector('.rapport-details') : null;
                    const modalTitle = document.querySelector('#rapportDetailsModal .modal-title');
                    const modalBody = document.getElementById('rapport-details-content');

                    if (detailsEl) {
                        modalBody.innerHTML = detailsEl.innerHTML || '<p>Aucun détail disponible pour ce rapport.</p>';
                    } else {
                        modalBody.innerHTML = '<p>Aucun détail disponible pour ce rapport.</p>';
                    }

                    // Si le nom du rapport est présent, on le met en titre
                    const nameEl = card ? card.querySelector('h4 a') : null;
                    if (nameEl) {
                        modalTitle.textContent = 'Détails — ' + nameEl.textContent.trim();
                    } else {
                        modalTitle.textContent = 'Détails du rapport';
                    }

                    // ouvrir le modal (Bootstrap 5)
                    var modalEl = document.getElementById('rapportDetailsModal');
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            });

            // activer les tooltips bootstrap si présents
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    </script>
@endpush

