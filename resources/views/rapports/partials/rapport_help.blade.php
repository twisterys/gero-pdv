@push('styles')
<style>
    .floating-help-btn {
        position: fixed;
        right: 20px;
        bottom: 70px;
        z-index: 1040; /* au-dessus du contenu, sous le modal backdrop (1050) */
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: none;
        color: #6c757d;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: transform 0.18s ease, color 0.18s ease, background 0.18s ease;
    }
    .floating-help-btn:hover { color: #0d6efd; background: #ffffff; transform: translateY(-1px); }
    .floating-help-btn:active { transform: translateY(0); }

    @keyframes floating-help-pulse { 0%{transform:scale(1)} 50%{transform:scale(1.08)} 100%{transform:scale(1)} }
    .floating-help-btn.pulse { animation: floating-help-pulse 320ms ease-in-out; }

    #rapportDetailsHelperModal .modal-header { border-bottom: none; }
    #rapportDetailsHelperModal .modal-body p { white-space: pre-wrap; }
</style>
@endpush

@php
    $__details = $details ?? ($rapport_details->details ?? null);
    $__title = $title ?? ($rapport_details->nom ?? null);
@endphp

<button type="button" class="floating-help-btn" aria-label="Détails du rapport" title="Détails du rapport">
    <i class="fa fa-question-circle fa-2x" aria-hidden="true"></i>
</button>

<div class="modal fade" id="rapportDetailsHelperModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $__title ? ('Détails du rapport — ' . $__title) : 'Détails du rapport' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div id="rapport-details-helper-content">
                    {!! nl2br(e($__details ?? 'Aucun détail disponible pour ce rapport.')) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.querySelector('.floating-help-btn');
        if (!btn) return;
        btn.addEventListener('click', function () {
            btn.classList.add('pulse');
            setTimeout(function(){ btn.classList.remove('pulse'); }, 350);
            var modalEl = document.getElementById('rapportDetailsHelperModal');
            if (typeof bootstrap !== 'undefined' && modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                // Fallback basique
                modalEl && modalEl.classList.add('show');
            }
        });
    });
</script>
@endpush

