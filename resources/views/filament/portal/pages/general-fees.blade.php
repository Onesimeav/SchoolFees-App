<x-filament-panels::page>
<style>
/* ── Base layout ── */
.gf-wrap { max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem; }

/* ── State cards ── */
.state-card { border-radius: .75rem; padding: 2rem; display: flex; align-items: flex-start; gap: 1rem; }
.state-card-icon { width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.state-card-title { font-size: 1rem; font-weight: 700; margin: 0 0 .25rem; }
.state-card-body  { font-size: .875rem; margin: 0; }

.state-info  { background: #eff6ff; border: 1px solid #bfdbfe; }
.state-info .state-card-icon  { background: #dbeafe; }
.state-info .state-card-title { color: #1e40af; }
.state-info .state-card-body  { color: #1d4ed8; }
.dark .state-info  { background: rgba(30,64,175,.1); border-color: rgba(96,165,250,.3); }
.dark .state-info .state-card-icon  { background: rgba(96,165,250,.15); }
.dark .state-info .state-card-title { color: #93c5fd; }
.dark .state-info .state-card-body  { color: #bfdbfe; }

/* ── Summary header ── */
.fee-header { background: #fff7ed; border: 1px solid #fdba74; border-radius: .75rem; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.dark .fee-header { background: rgba(124,45,18,.12); border-color: rgba(251,146,60,.3); }
.fee-header-left h2 { font-size: 1.1rem; font-weight: 700; color: #7c2d12; margin: 0 0 .25rem; }
.dark .fee-header-left h2 { color: #fdba74; }
.fee-header-left p  { font-size: .8rem; color: #9a3412; margin: 0; }
.dark .fee-header-left p  { color: #fed7aa; }
.year-badge { display: inline-block; background: #ffedd5; color: #7c2d12; border: 1px solid #fdba74; border-radius: 9999px; font-size: .75rem; font-weight: 700; padding: .2rem .75rem; }
.dark .year-badge { background: rgba(251,146,60,.15); color: #fdba74; border-color: rgba(251,146,60,.4); }

/* ── Fee cards ── */
.fees-list { display: flex; flex-direction: column; gap: 1rem; }
.fee-card { background: var(--fi-bg, #fff); border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.dark .fee-card { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.08); }
.fee-card.paid { opacity: .75; }
.fee-card-left { flex: 1; min-width: 0; }
.fee-card-title { font-size: .95rem; font-weight: 700; color: #111827; margin: 0 0 .25rem; }
.dark .fee-card-title { color: #f9fafb; }
.fee-card-desc { font-size: .8rem; color: #6b7280; margin: 0 0 .5rem; }
.dark .fee-card-desc { color: #9ca3af; }
.fee-card-meta { font-size: .75rem; color: #9ca3af; }
.fee-card-meta.overdue { color: #dc2626; font-weight: 600; }
.dark .fee-card-meta.overdue { color: #f87171; }
.fee-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: .5rem; }
.fee-amount { font-size: 1.1rem; font-weight: 800; color: #ea580c; }
.dark .fee-amount { color: #fdba74; }
.fee-fine { font-size: .75rem; color: #dc2626; }
.dark .fee-fine { color: #f87171; }
.fee-total { font-size: .8rem; color: #6b7280; }
.paid-badge { display: inline-flex; align-items: center; gap: .3rem; background: #d1fae5; color: #065f46; border-radius: 9999px; font-size: .7rem; font-weight: 700; padding: .25rem .7rem; white-space: nowrap; }
.dark .paid-badge { background: rgba(52,211,153,.15); color: #6ee7b7; }

/* ── Buttons ── */
.btn { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem 1.1rem; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: background .15s; }
.btn-pay { background: #ea580c; color: #fff; }
.btn-pay:hover { background: #c2410c; }
.btn:disabled { opacity: .5; cursor: not-allowed; }

/* ── Modal overlay ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.modal-box { background: #fff; border-radius: 1rem; padding: 2rem; max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.dark .modal-box { background: #1e2432; }
.modal-title { font-size: 1.1rem; font-weight: 700; margin: 0 0 1.25rem; }
.modal-summary { background: #f9fafb; border-radius: .5rem; padding: 1rem; margin-bottom: 1.25rem; font-size: .85rem; }
.dark .modal-summary { background: rgba(255,255,255,.05); }
.modal-summary-row { display: flex; justify-content: space-between; padding: .3rem 0; border-bottom: 1px solid #e5e7eb; }
.dark .modal-summary-row { border-color: rgba(255,255,255,.08); }
.modal-summary-row:last-child { border-bottom: none; font-weight: 700; }
.modal-total { font-size: 1rem; font-weight: 800; color: #ea580c; }
.dark .modal-total { color: #fdba74; }
.phone-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: .4rem; display: block; }
.dark .phone-label { color: #d1d5db; }
.phone-input { width: 100%; padding: .6rem .9rem; border: 1.5px solid #d1d5db; border-radius: .5rem; font-size: .875rem; background: #fff; color: #111827; box-sizing: border-box; margin-bottom: .5rem; }
.dark .phone-input { background: #374151; border-color: #4b5563; color: #f9fafb; }
.phone-input:focus { outline: none; border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.15); }
.info-bubble {
    display: flex; align-items: flex-start; gap: .5rem;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: .5rem;
    padding: .625rem .875rem; margin-bottom: 1rem;
    font-size: .8125rem; color: #1e40af; line-height: 1.4;
}
.dark .info-bubble { background: rgba(30,64,175,.15); border-color: rgba(96,165,250,.3); color: #93c5fd; }
.modal-actions { display: flex; gap: .75rem; margin-top: 1.25rem; }
.modal-actions .btn { flex: 1; justify-content: center; }
.btn-cancel { background: #f3f4f6; color: #374151; }
.dark .btn-cancel { background: rgba(255,255,255,.08); color: #d1d5db; }
.spinner { display: inline-block; width: 1rem; height: 1rem; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; }
.spinner-orange { display: inline-block; width: 2.25rem; height: 2.25rem; border: 3px solid rgba(234,88,12,.25); border-top-color: #ea580c; border-radius: 50%; animation: spin .7s linear infinite; }
.dark .spinner-orange { border-color: rgba(251,146,60,.2); border-top-color: #fdba74; }
@keyframes spin { to { transform: rotate(360deg); } }
.error-text { color: #dc2626; font-size: .75rem; margin-bottom: .5rem; }
.dark .error-text { color: #f87171; }
/* ── Processing lock ── */
.processing-state { text-align: center; padding: 1.5rem 0 .5rem; }
.processing-state p.ps-title { font-size: 1rem; font-weight: 700; color: #7c2d12; margin: .9rem 0 .4rem; }
.dark .processing-state p.ps-title { color: #fdba74; }
.processing-state p.ps-body { font-size: .82rem; color: #6b7280; line-height: 1.5; margin: 0; }
.dark .processing-state p.ps-body { color: #9ca3af; }
</style>

<div class="gf-wrap">

{{-- ── Not accepted state ── --}}
@if (! $acceptedRegistration)
    <div class="state-card state-info">
        <div class="state-card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
        </div>
        <div>
            <p class="state-card-title">Inscription requise</p>
            <p class="state-card-body">Votre inscription doit être acceptée par l'administration avant de pouvoir régler les frais généraux.</p>
            <a href="{{ \App\Filament\Portal\Pages\ClassEnrollment::getUrl() }}" style="display:inline-flex;align-items:center;gap:.4rem;margin-top:.75rem;font-size:.8rem;font-weight:600;color:#2563eb;text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:.9rem;height:.9rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Voir mon inscription
            </a>
        </div>
    </div>

@else
    {{-- Header --}}
    <div class="fee-header">
        <div class="fee-header-left">
            <h2>{{ $acceptedRegistration->grade->name }}</h2>
            <p>Frais généraux — {{ $registrationAcademicYear }}</p>
        </div>
        <span class="year-badge">{{ $registrationAcademicYear }}</span>
    </div>

    {{-- No fees configured state --}}
    @if ($fees->isEmpty())
        <div class="state-card state-info">
            <div class="state-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div>
                <p class="state-card-title">Aucun frais général</p>
                <p class="state-card-body">Aucun frais général n'a été défini pour <strong>{{ $acceptedRegistration->grade->name }}</strong> ({{ $registrationAcademicYear }}).</p>
            </div>
        </div>

    @else
        {{-- Fees list --}}
        <div class="fees-list">
            @foreach ($fees as $fee)
                @php
                    $isPaid    = in_array($fee->id, $paidFeeIds);
                    $isOverdue = ! $isPaid && $fee->due_before?->isPast();
                    $fine      = $isPaid ? 0 : $this->computeFine($fee);
                    $totalDue  = (int) $fee->total_amount + $fine;
                @endphp
                <div class="fee-card {{ $isPaid ? 'paid' : '' }}">
                    <div class="fee-card-left">
                        <p class="fee-card-title">{{ $fee->title }}</p>
                        @if ($fee->description)
                            <p class="fee-card-desc">{{ $fee->description }}</p>
                        @endif
                        @if ($fee->due_before)
                            <span class="fee-card-meta {{ $isOverdue ? 'overdue' : '' }}">
                                @if ($isPaid)
                                    Payé ✓
                                @elseif ($isOverdue)
                                    En retard · Éch. {{ $fee->due_before->format('d/m/Y') }}
                                @else
                                    Échéance {{ $fee->due_before->format('d/m/Y') }}
                                @endif
                            </span>
                        @endif
                    </div>
                    <div class="fee-card-right">
                        <span class="fee-amount">{{ number_format((int) $fee->total_amount, 0, ',', ' ') }} F CFA</span>
                        @if ($fine > 0)
                            <span class="fee-fine">+{{ number_format($fine, 0, ',', ' ') }} F amende</span>
                            <span class="fee-total">Total : {{ number_format($totalDue, 0, ',', ' ') }} F CFA</span>
                        @endif
                        @if ($isPaid)
                            <span class="paid-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:.75rem;height:.75rem;" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                                Payé
                            </span>
                        @else
                            <button class="btn btn-pay"
                                    wire:click="openPayModal('{{ $fee->id }}')"
                                    @if ($showModal) disabled @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 21Z"/></svg>
                                Payer
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- All paid state --}}
        @if ($fees->count() === count($paidFeeIds))
            <div class="state-card" style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:.75rem;">
                <div class="state-card-icon" style="background:#d1fae5;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:1.25rem;height:1.25rem;color:#059669;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="state-card-title" style="color:#065f46;">Tous les frais sont réglés</p>
                    <p class="state-card-body" style="color:#047857;">Vous avez réglé tous les frais généraux. Félicitations !</p>
                </div>
            </div>
        @endif
    @endif

    {{-- Payment modal --}}
    @if ($showModal && $selectedFee)
        @php
            $fine     = $this->computeFine($selectedFee);
            $totalDue = (int) $selectedFee->total_amount + $fine;
        @endphp
        <div class="modal-overlay">
            <div class="modal-box">

                @if ($processingPayment)
                    <div class="processing-state">
                        <div class="spinner-orange"></div>
                        <p class="ps-title">Paiement en cours…</p>
                        <p class="ps-body">
                            Complétez la transaction dans la fenêtre Mobile Money.<br>
                            <strong>Ne fermez pas cette page.</strong>
                        </p>
                    </div>

                @else
                    <p class="modal-title">Confirmer le paiement</p>

                    <div class="modal-summary">
                        <div class="modal-summary-row">
                            <span>{{ $selectedFee->title }}</span>
                            <span>{{ number_format((int) $selectedFee->total_amount, 0, ',', ' ') }} F CFA</span>
                        </div>
                        @if ($fine > 0)
                            <div class="modal-summary-row">
                                <span style="color:#dc2626;">Amende de retard</span>
                                <span style="color:#dc2626;">+{{ number_format($fine, 0, ',', ' ') }} F CFA</span>
                            </div>
                        @endif
                        <div class="modal-summary-row">
                            <span>Total</span>
                            <span class="modal-total">{{ number_format($totalDue, 0, ',', ' ') }} F CFA</span>
                        </div>
                    </div>

                    <div class="info-bubble">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                        </svg>
                        <span>
                            <strong>Mode test</strong> — L'intégration de paiement est à titre de démonstration uniquement.
                            Pour simuler un paiement réussi, utilisez le numéro de test KKiaPay : <strong>97000000</strong>.
                        </span>
                    </div>
                    <label class="phone-label" for="gf-phone">Numéro Mobile Money</label>
                    @error('phoneNumber')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <input id="gf-phone" type="tel" class="phone-input"
                           wire:model="phoneNumber"
                           placeholder="ex : 97000000">

                    <div class="modal-actions">
                        <button class="btn btn-cancel" wire:click="closeModal"
                                wire:loading.attr="disabled" wire:target="initiatePayment">
                            Annuler
                        </button>
                        <button class="btn btn-pay" wire:click="initiatePayment"
                                wire:loading.attr="disabled" wire:target="initiatePayment">
                            <span wire:loading.remove wire:target="initiatePayment">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 21Z"/></svg>
                                Payer via Mobile Money
                            </span>
                            <span wire:loading wire:target="initiatePayment">
                                <span class="spinner"></span> Ouverture…
                            </span>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif
@endif

</div>

{{-- KKiaPay widget CDN --}}
<script src="https://cdn.kkiapay.me/k.js"></script>

@script
<script>
    $wire.on('open-kkiapay-widget', function (data) {
        openKkiapayWidget({
            amount:   data.amount,
            api_key:  '{{ config("kkiapay.public_key") }}',
            sandbox:  {{ config('kkiapay.sandbox', true) ? 'true' : 'false' }},
            phone:    data.phone,
            position: 'center',
        });

        addSuccessListener(function (response) {
            $wire.handleKkiapaySuccess(response.transactionId);
        });

        addFailedListener(function () {
            $wire.handleKkiapayFailure();
        });
    });
</script>
@endscript

</x-filament-panels::page>