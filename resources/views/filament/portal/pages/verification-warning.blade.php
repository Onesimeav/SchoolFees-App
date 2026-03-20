<div style="background-color: #fefce8; border: 1px solid #fde047; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1rem;">
    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
        <div style="flex-shrink: 0; margin-top: 0.125rem;">
            <svg xmlns="http://www.w3.org/2000/svg" style="height: 1.5rem; width: 1.5rem; color: #ca8a04;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>
        <div style="flex: 1;">
            <p style="font-size: 0.9375rem; font-weight: 700; color: #713f12; margin: 0 0 0.25rem 0;">
                Adresse email non vérifiée
            </p>
            <p style="font-size: 0.875rem; color: #854d0e; margin: 0 0 0.75rem 0;">
                Votre adresse email n'est pas encore vérifiée. Certaines fonctionnalités sont limitées jusqu'à la vérification de votre compte.
            </p>
            <a
                href="{{ $verifyUrl }}"
                style="display: inline-flex; align-items: center; gap: 0.375rem; background-color: #ca8a04; color: #ffffff; font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s;"
                onmouseover="this.style.backgroundColor='#a16207'"
                onmouseout="this.style.backgroundColor='#ca8a04'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 1rem; width: 1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
                Vérifier mon email
            </a>
        </div>
    </div>
</div>