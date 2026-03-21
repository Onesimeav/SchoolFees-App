<div style="background-color: #fff7ed; border: 1px solid #fdba74; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
        <div style="flex-shrink: 0; margin-top: 0.125rem;">
            <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.5rem; width: 1.5rem; color: #ea580c;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </div>
        <div style="flex: 1;">
            <p style="font-size: 0.9375rem; font-weight: 700; color: #7c2d12; margin: 0 0 0.25rem 0;">
                {{ $unpaidCount }} frais {{ $unpaidCount > 1 ? 'généraux' : 'général' }} en attente
            </p>
            <p style="font-size: 0.875rem; color: #9a3412; margin: 0 0 0.75rem 0;">
                Vous avez {{ $unpaidCount }} frais {{ $unpaidCount > 1 ? 'généraux' : 'général' }} à régler pour cette année scolaire.
            </p>
            <a
                href="{{ $generalFeesUrl }}"
                style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #ea580c; color: #ffffff; font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s;"
                onmouseover="this.style.backgroundColor='#c2410c'"
                onmouseout="this.style.backgroundColor='#ea580c'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 21Z" />
                </svg>
                Voir les frais généraux
            </a>
        </div>
    </div>
</div>