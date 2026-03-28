<x-filament-panels::page>

<style>
/* ── Enrollment page ─────────────────────────────────────── */
.enroll-hero {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 50%, #3730a3 100%);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(29,78,216,.35);
}
.enroll-hero-title {
    font-size: 1.625rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 .375rem;
    letter-spacing: -.015em;
}
.enroll-hero-sub {
    font-size: .9375rem;
    color: #bfdbfe;
    margin: 0;
}
.enroll-hero-sub strong { color: #ffffff; }
.enroll-hero-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.enroll-hero-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(255,255,255,.15);
    border-radius: .625rem;
    flex-shrink: 0;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .375rem;
    border-radius: 9999px;
    padding: .375rem 1rem;
    font-size: .8125rem;
    font-weight: 600;
    white-space: nowrap;
}
.status-badge-pending  { background: rgba(251,191,36,.9); color: #713f12; }
.status-badge-accepted { background: rgba(52,211,153,.9); color: #065f46; }
.status-badge-refused  { background: rgba(248,113,113,.9); color: #7f1d1d; }

/* Status cards */
.status-card {
    border-radius: .875rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.status-card-pending  { background: #fffbeb; border: 1px solid #fde68a; }
.status-card-accepted { background: #ecfdf5; border: 1px solid #6ee7b7; }
.status-card-refused  { background: #fef2f2; border: 1px solid #fca5a5; }
.dark .status-card-pending  { background: rgba(120,53,15,.12); border-color: rgba(217,119,6,.3); }
.dark .status-card-accepted { background: rgba(6,78,59,.12);  border-color: rgba(52,211,153,.3); }
.dark .status-card-refused  { background: rgba(127,29,29,.12); border-color: rgba(248,113,113,.3); }

.status-card-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.icon-pending  { background: #fef3c7; }
.icon-accepted { background: #d1fae5; }
.icon-refused  { background: #fee2e2; }
.dark .icon-pending  { background: rgba(217,119,6,.2); }
.dark .icon-accepted { background: rgba(52,211,153,.15); }
.dark .icon-refused  { background: rgba(248,113,113,.15); }

.status-card-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 .25rem;
}
.title-pending  { color: #78350f; }
.title-accepted { color: #065f46; }
.title-refused  { color: #7f1d1d; }
.dark .title-pending  { color: #fcd34d; }
.dark .title-accepted { color: #6ee7b7; }
.dark .title-refused  { color: #fca5a5; }

.status-card-body { font-size: .875rem; margin: 0 0 .375rem; }
.body-pending  { color: #92400e; }
.body-accepted { color: #047857; }
.body-refused  { color: #b91c1c; }
.dark .body-pending  { color: #fde68a; }
.dark .body-accepted { color: #a7f3d0; }
.dark .body-refused  { color: #fecaca; }

.status-card-meta { font-size: .75rem; color: #9ca3af; margin: 0; }

/* Payment box */
.payment-box {
    background: rgba(255,255,255,.6);
    border: 1px solid #6ee7b7;
    border-radius: .625rem;
    padding: 1rem;
    margin-top: 1rem;
}
.dark .payment-box { background: rgba(6,78,59,.2); border-color: rgba(52,211,153,.25); }
.payment-box-label { font-size: .8125rem; font-weight: 600; margin: 0 0 .75rem; color: #065f46; }
.dark .payment-box-label { color: #6ee7b7; }
.payment-amount-small { font-size: .6875rem; color: #059669; margin: 0 0 .125rem; }
.dark .payment-amount-small { color: #34d399; }
.payment-amount-big { font-size: 1.625rem; font-weight: 700; color: #065f46; margin: 0; }
.dark .payment-amount-big { color: #ecfdf5; }
.payment-amount-unit { font-size: 1rem; font-weight: 400; }
.payment-box-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.payment-detail-row { display: flex; justify-content: space-between; font-size: .8125rem; padding: .25rem 0; border-bottom: 1px solid rgba(110,231,183,.3); }
.payment-detail-row:last-child { border-bottom: none; }
.payment-detail-label { color: #047857; }
.dark .payment-detail-label { color: #6ee7b7; }
.payment-detail-value { font-weight: 600; color: #065f46; }
.dark .payment-detail-value { color: #a7f3d0; }

.paid-badge {
    display: inline-flex;
    align-items: center;
    gap: .375rem;
    background: #059669;
    color: #ffffff;
    font-size: .875rem;
    font-weight: 600;
    padding: .5rem 1.25rem;
    border-radius: 9999px;
}

/* Phone input inside modal */
.modal-input-group { margin-bottom: 1rem; }
.modal-input-label { display: block; font-size: .8125rem; font-weight: 500; color: #374151; margin-bottom: .375rem; }
.dark .modal-input-label { color: #d1d5db; }
.modal-input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: .5rem;
    padding: .5rem .75rem;
    font-size: .875rem;
    color: #111827;
    background: #ffffff;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
}
.dark .modal-input { background: #374151; border-color: #4b5563; color: #f9fafb; }
.modal-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.modal-input-error { font-size: .75rem; color: #dc2626; margin: .25rem 0 0; }

/* Section heading */
.section-heading { font-size: 1.125rem; font-weight: 600; margin: 0 0 .25rem; }
.section-sub { font-size: .875rem; color: #6b7280; margin: 0 0 1.25rem; }
.dark .section-heading { color: #f3f4f6; }
.dark .section-sub { color: #9ca3af; }

/* Grades grid */
.grades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.25rem;
}

/* Grade card */
.grade-card {
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: .875rem;
    padding: 1.25rem;
    cursor: pointer;
    text-align: left;
    width: 100%;
    transition: border-color .15s, box-shadow .15s, transform .15s;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.dark .grade-card { background: #1f2937; border-color: #374151; }
.grade-card:hover {
    border-color: #60a5fa;
    box-shadow: 0 8px 20px -4px rgba(59,130,246,.2);
    transform: translateY(-2px);
}
.dark .grade-card:hover { border-color: #3b82f6; }
.grade-card:focus { outline: 2px solid #3b82f6; outline-offset: 2px; }

.grade-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .875rem;
}
.grade-card-icon {
    width: 2.25rem;
    height: 2.25rem;
    background: #dbeafe;
    border-radius: .5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .15s;
}
.dark .grade-card-icon { background: rgba(59,130,246,.2); }
.grade-card:hover .grade-card-icon { background: #bfdbfe; }
.dark .grade-card:hover .grade-card-icon { background: rgba(59,130,246,.3); }

.grade-fee-pill {
    display: inline-flex;
    align-items: center;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    font-size: .6875rem;
    font-weight: 600;
    padding: .25rem .625rem;
    border-radius: 9999px;
    white-space: nowrap;
}
.dark .grade-fee-pill { background: rgba(29,78,216,.15); border-color: rgba(96,165,250,.3); color: #93c5fd; }

.grade-card-name { font-size: 1rem; font-weight: 700; margin: 0 0 .375rem; color: #111827; }
.dark .grade-card-name { color: #f9fafb; }
.grade-card-desc { font-size: .8125rem; color: #6b7280; margin: 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.dark .grade-card-desc { color: #9ca3af; }

.grade-card-footer {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-top: 1rem;
    gap: .5rem;
}
.grade-fee-label { font-size: .6875rem; color: #9ca3af; margin: 0 0 .125rem; }
.grade-fee-amount { font-size: .9375rem; font-weight: 700; color: #111827; margin: 0; }
.dark .grade-fee-amount { color: #f9fafb; }
.grade-deadline {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .6875rem;
    font-weight: 500;
    color: #d97706;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 9999px;
    padding: .2rem .6rem;
    margin-top: .5rem;
}
.dark .grade-deadline { background: rgba(217,119,6,.15); border-color: rgba(217,119,6,.3); color: #fcd34d; }
.grade-deadline-urgent { color: #dc2626; background: #fef2f2; border-color: #fca5a5; }
.dark .grade-deadline-urgent { background: rgba(220,38,38,.15); border-color: rgba(248,113,113,.3); color: #fca5a5; }

.btn-register {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #2563eb;
    color: #ffffff;
    font-size: .75rem;
    font-weight: 600;
    padding: .4375rem .875rem;
    border-radius: .5rem;
    border: none;
    transition: background .15s;
    flex-shrink: 0;
}
.grade-card:hover .btn-register { background: #1d4ed8; }

/* Empty state */
.empty-state {
    border: 2px dashed #d1d5db;
    border-radius: .875rem;
    padding: 3rem;
    text-align: center;
}
.dark .empty-state { border-color: #374151; }
.empty-state p { font-size: .875rem; color: #6b7280; margin: .75rem 0 0; }
.dark .empty-state p { color: #9ca3af; }

/* Modal backdrop */
.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 40;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(4px);
}
/* Modal */
.modal-wrap {
    position: fixed;
    inset: 0;
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-card {
    background: #ffffff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.4);
    width: 100%;
    max-width: 26rem;
    animation: modal-in .15s ease-out;
}
.dark .modal-card { background: #1f2937; }

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f3f4f6;
    padding: 1rem 1.5rem;
}
.dark .modal-header { border-color: #374151; }
.modal-title { font-size: .9375rem; font-weight: 600; color: #111827; margin: 0; }
.dark .modal-title { color: #f9fafb; }
.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    padding: .25rem;
    border-radius: .375rem;
    display: flex;
    transition: color .15s, background .15s;
}
.modal-close:hover { background: #f3f4f6; color: #374151; }
.dark .modal-close:hover { background: #374151; color: #d1d5db; }

.modal-body { padding: 1.25rem 1.5rem; }
.modal-grade-box {
    display: flex;
    align-items: center;
    gap: .875rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: .75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.dark .modal-grade-box { background: rgba(29,78,216,.1); border-color: rgba(96,165,250,.2); }
.modal-grade-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: #dbeafe;
    border-radius: .5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.dark .modal-grade-icon { background: rgba(59,130,246,.2); }
.modal-grade-label { font-size: .6875rem; color: #3b82f6; margin: 0 0 .125rem; }
.dark .modal-grade-label { color: #93c5fd; }
.modal-grade-name { font-size: 1rem; font-weight: 700; color: #1e3a8a; margin: 0; }
.dark .modal-grade-name { color: #bfdbfe; }

.modal-fee-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f9fafb;
    border-radius: .5rem;
    padding: .75rem 1rem;
    margin-bottom: 1rem;
}
.dark .modal-fee-row { background: #374151; }
.modal-fee-label { font-size: .875rem; color: #4b5563; }
.dark .modal-fee-label { color: #d1d5db; }
.modal-fee-amount { font-size: .9375rem; font-weight: 700; color: #111827; }
.dark .modal-fee-amount { color: #f9fafb; }

.modal-notice { font-size: .8125rem; color: #6b7280; margin: 0; line-height: 1.55; }
.dark .modal-notice { color: #9ca3af; }

.info-bubble {
    display: flex; align-items: flex-start; gap: .5rem;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: .5rem;
    padding: .625rem .875rem; margin-bottom: 1rem;
    font-size: .8125rem; color: #1e40af; line-height: 1.4;
}
.dark .info-bubble { background: rgba(30,64,175,.15); border-color: rgba(96,165,250,.3); color: #93c5fd; }

.modal-footer {
    display: flex;
    gap: .75rem;
    border-top: 1px solid #f3f4f6;
    padding: 1rem 1.5rem;
}
.dark .modal-footer { border-color: #374151; }

.btn-cancel {
    flex: 1;
    background: #ffffff;
    border: 1px solid #d1d5db;
    color: #374151;
    font-size: .875rem;
    font-weight: 500;
    padding: .625rem 1rem;
    border-radius: .5rem;
    cursor: pointer;
    transition: background .15s;
}
.dark .btn-cancel { background: #374151; border-color: #4b5563; color: #d1d5db; }
.btn-cancel:hover { background: #f9fafb; }
.dark .btn-cancel:hover { background: #4b5563; }

.btn-confirm {
    flex: 1;
    background: #2563eb;
    color: #ffffff;
    font-size: .875rem;
    font-weight: 600;
    padding: .625rem 1rem;
    border-radius: .5rem;
    border: none;
    cursor: pointer;
    transition: background .15s;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}
.btn-confirm:hover:not(:disabled) { background: #1d4ed8; }
.btn-confirm:disabled { opacity: .5; cursor: not-allowed; }

@keyframes modal-in {
    from { opacity: 0; transform: scale(.95) translateY(-6px); }
    to   { opacity: 1; transform: scale(1)  translateY(0);     }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
</style>

{{-- ─── Hero ─────────────────────────────────────────────────── --}}
<div class="enroll-hero">
    <div class="enroll-hero-row">
        <div style="display:flex;align-items:center;gap:.875rem;">
            <div class="enroll-hero-icon">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.5rem;height:1.5rem;color:#ffffff;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>
            <div>
                <h1 class="enroll-hero-title">Inscription à une Classe</h1>
                <p class="enroll-hero-sub">
                    Bonjour, <strong>{{ auth()->user()->name }} {{ auth()->user()->surname }}</strong> !
                    Inscriptions ouvertes pour l'année scolaire <strong>{{ $nextAcademicYear }}</strong>.
                </p>
            </div>
        </div>

        @if ($currentRegistration)
            <span class="status-badge status-badge-{{ $currentRegistration->status }}">
                @if ($currentRegistration->status === 'pending')
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    En attente
                @elseif ($currentRegistration->status === 'accepted')
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Accepté
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Refusé
                @endif
            </span>
        @endif
    </div>
</div>

{{-- ─── Status Card ──────────────────────────────────────────── --}}
@if ($currentRegistration)

    @if ($currentRegistration->status === 'pending')
        <div class="status-card status-card-pending">
            <div class="status-card-icon icon-pending">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.5rem;height:1.5rem;color:#d97706;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div style="flex:1;">
                <p class="status-card-title title-pending">Inscription en attente de validation</p>
                <p class="status-card-body body-pending">
                    Votre demande d'inscription en <strong>{{ $currentRegistration->grade->name }}</strong>
                    a été soumise et est en cours d'examen par l'équipe administrative.
                </p>
                @if ($currentRegistration->transaction)
                    <div class="payment-box" style="border-color:rgba(251,191,36,.5);background:rgba(254,243,199,.5);">
                        <p class="payment-box-label" style="color:#92400e;">Paiement effectué</p>
                        <div class="payment-detail-row" style="border-color:rgba(251,191,36,.3);">
                            <span class="payment-detail-label" style="color:#92400e;">Montant payé</span>
                            <span class="payment-detail-value" style="color:#78350f;">{{ number_format($currentRegistration->transaction->amount, 0, ',', ' ') }} F CFA</span>
                        </div>
                        <div class="payment-detail-row" style="border-color:rgba(251,191,36,.3);">
                            <span class="payment-detail-label" style="color:#92400e;">Date</span>
                            <span class="payment-detail-value" style="color:#78350f;">{{ $currentRegistration->transaction->date->format('d/m/Y') }}</span>
                        </div>
                        <div class="payment-detail-row" style="border-color:rgba(251,191,36,.3);">
                            <span class="payment-detail-label" style="color:#92400e;">N° de téléphone</span>
                            <span class="payment-detail-value" style="color:#78350f;">{{ $currentRegistration->transaction->phone_number }}</span>
                        </div>
                    </div>
                @endif
                <p class="status-card-meta">Soumis le {{ $currentRegistration->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

    @elseif ($currentRegistration->status === 'accepted')
        <div class="status-card status-card-accepted" style="flex-wrap:wrap;">
            <div class="status-card-icon icon-accepted">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.5rem;height:1.5rem;color:#059669;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div style="flex:1;min-width:200px;">
                <p class="status-card-title title-accepted">Inscription acceptée !</p>
                <p class="status-card-body body-accepted">
                    Félicitations ! Votre inscription en <strong>{{ $currentRegistration->grade->name }}</strong> a été acceptée par l'administration.
                </p>
                @if ($currentRegistration->transaction)
                    <div class="payment-box">
                        <p class="payment-box-label">Paiement confirmé</p>
                        <div class="payment-box-row">
                            <div>
                                <p class="payment-amount-small">Montant réglé</p>
                                <p class="payment-amount-big">
                                    {{ number_format($currentRegistration->transaction->amount, 0, ',', ' ') }}
                                    <span class="payment-amount-unit">F CFA</span>
                                </p>
                                <p style="font-size:.75rem;margin:.25rem 0 0;color:#6b7280;">
                                    Payé le {{ $currentRegistration->transaction->date->format('d/m/Y') }}
                                    · {{ $currentRegistration->transaction->phone_number }}
                                </p>
                            </div>
                            <span class="paid-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                                Payé ✓
                            </span>
                        </div>
                    </div>
                @endif
                <a href="{{ $tuitionUrl }}" style="display:inline-flex;align-items:center;gap:.5rem;margin-top:1rem;padding:.6rem 1.25rem;background:#059669;color:#fff;border-radius:.5rem;font-size:.875rem;font-weight:600;text-decoration:none;transition:background .15s;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                    Voir mes frais de scolarité
                </a>
            </div>
        </div>

    @elseif ($currentRegistration->status === 'refused')
        <div class="status-card status-card-refused">
            <div class="status-card-icon icon-refused">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.5rem;height:1.5rem;color:#dc2626;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div>
                <p class="status-card-title title-refused">Inscription refusée</p>
                @if ($currentRegistration->notes)
                    <p class="status-card-body body-refused"><strong>Motif :</strong> {{ $currentRegistration->notes }}</p>
                @endif
                <p class="status-card-meta" style="color:#b91c1c;">Vous pouvez choisir une autre classe ci-dessous.</p>
            </div>
        </div>
    @endif

@endif

{{-- ─── Grade Selection ──────────────────────────────────────── --}}
@if (!$currentRegistration || $currentRegistration->status === 'refused')

    <p class="section-heading">Classes disponibles</p>
    <p class="section-sub">Sélectionnez la classe dans laquelle vous souhaitez vous inscrire.</p>

    @if ($grades->isEmpty())
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:3rem;height:3rem;color:#d1d5db;margin:0 auto;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
            <p>Aucune classe disponible pour l'année {{ $nextAcademicYear }}. Revenez plus tard.</p>
        </div>
    @else
        <div class="grades-grid">
            @foreach ($grades as $grade)
                @php $gradeFee = $grade->registrationFees->first(); @endphp
                <button
                    wire:click="openModal('{{ $grade->id }}')"
                    type="button"
                    class="grade-card"
                >
                    <div class="grade-card-top">
                        <div class="grade-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                        </div>
                        @if ($gradeFee)
                            <span class="grade-fee-pill">{{ number_format($gradeFee->total_amount, 0, ',', ' ') }} F</span>
                        @endif
                    </div>

                    <p class="grade-card-name">{{ $grade->name }}</p>
                    @if ($grade->description)
                        <p class="grade-card-desc">{{ $grade->description }}</p>
                    @endif

                    <div class="grade-card-footer">
                        @if ($gradeFee)
                            <div>
                                <p class="grade-fee-label">Frais d'inscription</p>
                                <p class="grade-fee-amount">{{ number_format($gradeFee->total_amount, 0, ',', ' ') }} F CFA</p>
                                <p style="font-size:.75rem;color:#9ca3af;margin:.25rem 0 0;">Non remboursable</p>
                                @if ($gradeFee->due_before)
                                    @php $daysLeft = now()->startOfDay()->diffInDays($gradeFee->due_before->startOfDay(), false); @endphp
                                    <span class="grade-deadline {{ $daysLeft <= 7 ? 'grade-deadline-urgent' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:.625rem;height:.625rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        @if ($daysLeft < 0)
                                            Délai expiré
                                        @elseif ($daysLeft === 0)
                                            Dernier jour !
                                        @else
                                            Limite : {{ $gradeFee->due_before->format('d/m/Y') }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        @else
                            <p class="grade-fee-label">Frais à définir</p>
                        @endif
                        <span class="btn-register">
                            S'inscrire
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:.875rem;height:.875rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    @endif

@endif

{{-- ─── Confirmation Modal ───────────────────────────────────── --}}
@if ($showModal && $selectedGradeId)

    {{-- Backdrop: only closes modal when NOT processing --}}
    @if (!$processingPayment)
        <div class="modal-backdrop" wire:click="closeModal"></div>
    @else
        <div class="modal-backdrop"></div>
    @endif

    <div class="modal-wrap">
        <div class="modal-card" role="dialog" aria-modal="true">

            <div class="modal-header">
                <p class="modal-title">Confirmer l'inscription</p>
                @if (!$processingPayment)
                    <button class="modal-close" wire:click="closeModal" type="button" aria-label="Fermer">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            <div class="modal-body">
                <div class="modal-grade-box">
                    <div class="modal-grade-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>
                    </div>
                    <div>
                        <p class="modal-grade-label">Classe sélectionnée</p>
                        <p class="modal-grade-name">{{ $selectedGradeName }}</p>
                    </div>
                </div>

                @if ($selectedFeeAmount)
                    <div class="modal-fee-row">
                        <span class="modal-fee-label">Frais d'inscription</span>
                        <span class="modal-fee-amount">{{ number_format($selectedFeeAmount, 0, ',', ' ') }} F CFA</span>
                    </div>
                    @if ($selectedFeeDueBefore)
                        <div class="modal-fee-row" style="margin-bottom:1rem;">
                            <span class="modal-fee-label">Date limite de paiement</span>
                            <span class="modal-fee-amount" style="color:#d97706;">{{ $selectedFeeDueBefore }}</span>
                        </div>
                    @endif
                @endif

                @if (!$processingPayment)
                    <div class="info-bubble">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                        </svg>
                        <span>
                            <strong>Mode test</strong> — L'intégration de paiement est à titre de démonstration uniquement.
                            Pour simuler un paiement réussi, utilisez le numéro de test KKiaPay : <strong>97000000</strong>.
                        </span>
                    </div>
                    <div class="modal-input-group">
                        <label for="phone-input" class="modal-input-label">
                            Numéro Mobile Money <span style="color:#dc2626;">*</span>
                        </label>
                        <input
                            id="phone-input"
                            type="tel"
                            wire:model="phoneNumber"
                            class="modal-input"
                            placeholder="ex : +229 97 00 00 00"
                            autocomplete="tel"
                        >
                        @error('phoneNumber')
                            <p class="modal-input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="modal-notice">
                        Le paiement des frais d'inscription sera effectué immédiatement.
                        Votre dossier sera ensuite examiné par l'administration.
                    </p>
                @else
                    <div style="display:flex;align-items:center;gap:.75rem;padding:.875rem 0;color:#374151;">
                        <svg style="width:1.25rem;height:1.25rem;color:#3b82f6;flex-shrink:0;animation:spin 1s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity:.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span style="font-size:.875rem;font-weight:500;">Vérification du paiement en cours…</span>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                @if (!$processingPayment)
                    <button wire:click="closeModal" type="button" class="btn-cancel">Annuler</button>
                    <button
                        wire:click="initiatePayment"
                        wire:loading.attr="disabled"
                        type="button"
                        class="btn-confirm"
                    >
                        <span wire:loading.remove wire:target="initiatePayment">Payer &amp; S'inscrire</span>
                        <span wire:loading wire:target="initiatePayment">Traitement…</span>
                    </button>
                @else
                    <button type="button" class="btn-cancel" disabled style="opacity:.4;cursor:not-allowed;">Annuler</button>
                    <button type="button" class="btn-confirm" disabled style="opacity:.4;cursor:not-allowed;">Traitement…</button>
                @endif
            </div>
        </div>
    </div>
@endif


<script src="https://cdn.kkiapay.me/k.js"></script>

@script
<script>
    $wire.on('open-kkiapay-widget', function(data) {
        openKkiapayWidget({
            amount:   data.amount,
            api_key:  '{{ config("kkiapay.public_key") }}',
            sandbox:  {{ config('kkiapay.sandbox', true) ? 'true' : 'false' }},
            phone:    data.phone,
            position: 'center',
        });

        addSuccessListener(function(response) {
            $wire.handleKkiapaySuccess(response.transactionId);
        });

        addFailedListener(function() {
            $wire.handleKkiapayFailure();
        });
    });
</script>
@endscript

</x-filament-panels::page>