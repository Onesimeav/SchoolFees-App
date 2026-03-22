@php
    $initials = strtoupper(mb_substr($user->name ?? '?', 0, 1) . mb_substr($user->surname ?? '', 0, 1));
    $tuitionProgress = $totalInstallments > 0 ? round(($paidInstallments / $totalInstallments) * 100) : 0;
    $registrationStatus = $registration?->status;
    $statusLabel = match($registrationStatus) {
        'accepted' => 'Inscrit(e)',
        'pending'  => 'En attente',
        'refused'  => 'Refusé(e)',
        default    => null,
    };
    $statusClass = match($registrationStatus) {
        'accepted' => 'ds-badge-green',
        'pending'  => 'ds-badge-yellow',
        'refused'  => 'ds-badge-red',
        default    => 'ds-badge-gray',
    };
    $dotColor = match($registrationStatus) {
        'accepted' => '#16a34a',
        'pending'  => '#ca8a04',
        'refused'  => '#dc2626',
        default    => '#9ca3af',
    };
@endphp

<style>
/* ── Dashboard student card ─────────────────────────────── */
.ds-card { border-radius:.875rem; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06); margin-bottom:1.25rem; }

/* Header */
.ds-header { background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#3b82f6 100%); padding:1.5rem 1.75rem; display:flex; align-items:center; gap:1.25rem; }
.ds-avatar { flex-shrink:0; width:3.5rem; height:3.5rem; border-radius:50%; background:rgba(255,255,255,.2); border:2px solid rgba(255,255,255,.4); display:flex; align-items:center; justify-content:center; }
.ds-avatar span { font-size:1.25rem; font-weight:700; color:#fff; letter-spacing:.05em; }
.ds-greeting { flex:1; min-width:0; }
.ds-greeting-name { margin:0 0 .2rem; font-size:1.25rem; font-weight:700; color:#fff; }
.ds-greeting-sub  { margin:0; font-size:.875rem; color:rgba(255,255,255,.8); }
.ds-status-pill { flex-shrink:0; display:flex; align-items:center; gap:.4rem; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); border-radius:9999px; padding:.25rem .75rem; }
.ds-status-pill span:first-child { width:.5rem; height:.5rem; border-radius:50%; }
.ds-status-pill span:last-child  { font-size:.8125rem; font-weight:600; color:#fff; }

/* Body */
.ds-body { background:#fff; padding:1.25rem 1.75rem; display:flex; flex-wrap:wrap; gap:1.5rem; }
.dark .ds-body { background:#1e293b; }

.ds-section { flex:1; min-width:180px; }
.ds-section-title { margin:0 0 .625rem; font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; }
.dark .ds-section-title { color:#94a3b8; }

.ds-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.4rem; }
.ds-label { font-size:.8125rem; color:#374151; }
.dark .ds-label { color:#cbd5e1; }

.ds-value { font-size:.8125rem; font-weight:600; color:#111827; }
.dark .ds-value { color:#f1f5f9; }

.ds-sep { width:1px; background:#f3f4f6; align-self:stretch; }
.dark .ds-sep { background:#334155; }

/* Badges */
.ds-badge { font-size:.75rem; font-weight:600; padding:.15rem .6rem; border-radius:9999px; }
.ds-badge-blue   { background:#dbeafe; color:#1e40af; }
.ds-badge-green  { background:#dcfce7; color:#166534; }
.ds-badge-yellow { background:#fef9c3; color:#713f12; }
.ds-badge-red    { background:#fee2e2; color:#7f1d1d; }
.ds-badge-gray   { background:#f3f4f6; color:#374151; }
.dark .ds-badge-blue   { background:#1e3a8a; color:#93c5fd; }
.dark .ds-badge-green  { background:#14532d; color:#86efac; }
.dark .ds-badge-yellow { background:#713f12; color:#fde68a; }
.dark .ds-badge-red    { background:#7f1d1d; color:#fca5a5; }
.dark .ds-badge-gray   { background:#334155; color:#cbd5e1; }

/* Progress */
.ds-progress-label { margin:0 0 .375rem; font-size:.875rem; font-weight:600; color:#111827; }
.dark .ds-progress-label { color:#f1f5f9; }
.ds-progress-sub { font-weight:400; color:#6b7280; font-size:.8125rem; }
.dark .ds-progress-sub { color:#94a3b8; }
.ds-progress-track { height:.5rem; background:#e5e7eb; border-radius:9999px; overflow:hidden; margin-bottom:.5rem; }
.dark .ds-progress-track { background:#334155; }
.ds-progress-fill { height:100%; background:linear-gradient(90deg,#2563eb,#3b82f6); border-radius:9999px; }
.ds-amount-paid  { margin:0; font-size:.8125rem; }
.ds-amount-value { font-weight:600; color:#1d4ed8; }
.dark .ds-amount-value { color:#93c5fd; }
.ds-amount-total { color:#9ca3af; }

/* Dots */
.ds-dots { display:flex; flex-wrap:wrap; gap:.35rem; align-items:center; }
.ds-dot  { width:.875rem; height:.875rem; border-radius:50%; display:inline-block; }
.ds-dot-extra { font-size:.75rem; color:#9ca3af; }

/* Footer */
.ds-footer { background:#f9fafb; border-top:1px solid #f3f4f6; padding:.875rem 1.75rem; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; }
.dark .ds-footer { background:#0f172a; border-top-color:#1e293b; }
.ds-footer-text { margin:0; font-size:.8125rem; color:#6b7280; }
.dark .ds-footer-text { color:#94a3b8; }
.ds-footer-text strong { color:#374151; }
.dark .ds-footer-text strong { color:#e2e8f0; }
.ds-actions { display:flex; gap:.75rem; flex-wrap:wrap; }
.ds-action-primary { display:inline-flex; align-items:center; gap:.35rem; font-size:.8125rem; font-weight:600; color:#2563eb; text-decoration:none; }
.dark .ds-action-primary { color:#60a5fa; }
.ds-action-secondary { display:inline-flex; align-items:center; gap:.35rem; font-size:.8125rem; font-weight:600; color:#6b7280; text-decoration:none; }
.dark .ds-action-secondary { color:#94a3b8; }

/* Not-enrolled state */
.ds-not-enrolled { background:#fff; padding:1.5rem 1.75rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
.dark .ds-not-enrolled { background:#1e293b; }
.ds-ne-title { margin:0 0 .25rem; font-size:.9375rem; font-weight:600; color:#111827; }
.dark .ds-ne-title { color:#f1f5f9; }
.ds-ne-sub { margin:0; font-size:.875rem; color:#6b7280; }
.dark .ds-ne-sub { color:#94a3b8; }
.ds-ne-btn { display:inline-flex; align-items:center; gap:.4rem; background:#2563eb; color:#fff; font-size:.875rem; font-weight:600; padding:.5rem 1.1rem; border-radius:.5rem; text-decoration:none; }
.dark .ds-ne-btn { background:#1d4ed8; }
</style>

<div class="ds-card">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="ds-header">
        <div class="ds-avatar"><span>{{ $initials }}</span></div>

        <div class="ds-greeting">
            <p class="ds-greeting-name">Bonjour, {{ $user->name }} !</p>
            @if($registration && $academicYear)
                <p class="ds-greeting-sub">{{ $registration->grade->name }} · Année {{ $academicYear }}</p>
            @else
                <p class="ds-greeting-sub">Bienvenue sur votre espace scolaire</p>
            @endif
        </div>

        @if($statusLabel)
            <div class="ds-status-pill">
                <span style="background:{{ $dotColor }};"></span>
                <span>{{ $statusLabel }}</span>
            </div>
        @endif
    </div>

    @if($registration)
    {{-- ── Body ────────────────────────────────────────────────────────────── --}}
    <div class="ds-body">

        {{-- Section A — Inscription --}}
        <div class="ds-section">
            <p class="ds-section-title">Inscription</p>
            <div class="ds-row">
                <span class="ds-label">Classe :</span>
                <span class="ds-badge ds-badge-blue">{{ $registration->grade->name }}</span>
            </div>
            <div class="ds-row">
                <span class="ds-label">Statut :</span>
                <span class="ds-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div class="ds-row">
                <span class="ds-label">Année :</span>
                <span class="ds-value">{{ $academicYear }}</span>
            </div>
        </div>

        <div class="ds-sep"></div>

        {{-- Section B — Scolarité --}}
        @if($totalInstallments > 0)
        <div class="ds-section">
            <p class="ds-section-title">Scolarité</p>
            <p class="ds-progress-label">
                {{ $paidInstallments }} / {{ $totalInstallments }}
                <span class="ds-progress-sub"> versements payés</span>
            </p>
            <div class="ds-progress-track">
                <div class="ds-progress-fill" style="width:{{ $tuitionProgress }}%;"></div>
            </div>
            <p class="ds-amount-paid">
                <span class="ds-amount-value">{{ number_format($tuitionPaid, 0, ',', ' ') }} F CFA</span>
                <span class="ds-amount-total"> / {{ number_format($tuitionAmount, 0, ',', ' ') }} F CFA</span>
            </p>
        </div>

        <div class="ds-sep"></div>
        @endif

        {{-- Section C — Frais généraux --}}
        <div class="ds-section">
            <p class="ds-section-title">Frais généraux</p>
            @if($generalFeeTotal > 0)
                <p class="ds-progress-label">
                    {{ $generalFeePaid }} / {{ $generalFeeTotal }}
                    <span class="ds-progress-sub"> réglés</span>
                </p>
                @php $dotsToShow = min($generalFeeTotal, 10); $extra = $generalFeeTotal - $dotsToShow; @endphp
                <div class="ds-dots">
                    @for($i = 0; $i < $dotsToShow; $i++)
                        <span class="ds-dot" style="background:{{ $i < $generalFeePaid ? '#16a34a' : '#d1d5db' }};"
                              title="{{ $i < $generalFeePaid ? 'Payé' : 'Non payé' }}"></span>
                    @endfor
                    @if($extra > 0)
                        <span class="ds-dot-extra">+{{ $extra }}</span>
                    @endif
                </div>
            @else
                <p class="ds-label">Aucun frais général pour cette année.</p>
            @endif
        </div>

    </div>

    {{-- ── Footer ──────────────────────────────────────────────────────────── --}}
    <div class="ds-footer">
        <p class="ds-footer-text">
            @if($lastTransaction)
                Dernier paiement :
                <strong>{{ $lastTransaction->date->format('d/m/Y') }}</strong>
                —
                <strong>{{ number_format($lastTransaction->amount, 0, ',', ' ') }} F CFA</strong>
            @else
                Aucun paiement enregistré.
            @endif
        </p>
        <div class="ds-actions">
            @if($totalInstallments > $paidInstallments)
            <a href="{{ \App\Filament\Portal\Pages\TuitionPayment::getUrl() }}" class="ds-action-primary">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75" />
                </svg>
                Payer ma scolarité →
            </a>
            @endif
            <a href="{{ \App\Filament\Portal\Resources\Transactions\TransactionResource::getUrl('index') }}" class="ds-action-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                Voir mes transactions →
            </a>
        </div>
    </div>

    @else
    {{-- ── Not enrolled ────────────────────────────────────────────────────── --}}
    <div class="ds-not-enrolled">
        <div>
            <p class="ds-ne-title">Vous n'êtes pas encore inscrit(e)</p>
            <p class="ds-ne-sub">Commencez votre inscription pour accéder à toutes les fonctionnalités.</p>
        </div>
        <a href="{{ \App\Filament\Portal\Pages\ClassEnrollment::getUrl() }}" class="ds-ne-btn">
            <svg xmlns="http://www.w3.org/2000/svg" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Commencer mon inscription
        </a>
    </div>
    @endif

</div>