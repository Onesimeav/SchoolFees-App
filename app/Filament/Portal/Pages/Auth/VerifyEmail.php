<?php

namespace App\Filament\Portal\Pages\Auth;

use App\Mail\EmailVerificationMail;
use App\Models\VerificationCode;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Concerns\HasMaxWidth;
use Filament\Pages\Concerns\HasTopbar;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

class VerifyEmail extends Page
{
    use HasMaxWidth;
    use HasTopbar;

    protected static ?string $slug = 'auth/verify-email';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament-panels::pages.simple';

    protected static string $layout = 'filament-panels::components.layout.simple';

    public function hasLogo(): bool
    {
        return true;
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar'       => $this->hasTopbar(),
            'maxContentWidth' => $maxContentWidth = $this->getMaxWidth() ?? $this->getMaxContentWidth(),
            'maxWidth'        => $maxContentWidth,
        ];
    }

    public ?array $data = [];

    public int $resendCooldown = 0;

    public function mount(): void
    {
        if (! Filament::auth()->check()) {
            $this->redirect(Filament::getLoginUrl());
            return;
        }

        $user = Filament::auth()->user();

        if ($user->verified) {
            $this->redirect(Filament::getUrl());
            return;
        }

        $existing = VerificationCode::where('email', $user->email)
            ->where('type', 'email_verification')
            ->where('expires_at', '>', now())
            ->first();

        if (! $existing) {
            $this->sendOtp($user->email, $user->name);
        }

        $this->form->fill();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Code de vérification')
                ->placeholder('000000')
                ->required()
                ->maxLength(6)
                ->autofocus(),
        ]);
    }

    public function verify(): void
    {
        $this->form->validate();
        $data = $this->form->getState();
        $user = Filament::auth()->user();

        $record = VerificationCode::where('email', $user->email)
            ->where('code', $data['code'])
            ->where('type', 'email_verification')
            ->first();

        if (! $record || $record->isExpired()) {
            $this->addError('data.code', 'Le code est invalide ou expiré.');
            return;
        }

        $record->delete();
        $user->update(['verified' => true, 'email_verified_at' => now()]);

        $this->redirect(Filament::getUrl());
    }

    public function resend(): void
    {
        $user = Filament::auth()->user();
        $this->sendOtp($user->email, $user->name);
        $this->resendCooldown = 15;
    }

    protected function sendOtp(string $email, string $name): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        VerificationCode::where('email', $email)->where('type', 'email_verification')->delete();
        VerificationCode::create([
            'email'      => $email,
            'code'       => $otp,
            'type'       => 'email_verification',
            'expires_at' => now()->addMinutes(15),
        ]);
        Mail::to($email)->queue(new EmailVerificationMail($otp, $name));
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('verify')
                ->footer([
                    Actions::make([
                        Action::make('verify')
                            ->label('Vérifier')
                            ->submit('verify'),
                        Action::make('resend')
                            ->label('Renvoyer le code')
                            ->action('resend')
                            ->color('gray')
                            ->link()
                            ->extraAttributes([
                                'x-data' => '{ countdown: 0, timer: null }',
                                'x-init' => '
                                    $watch(() => $wire.resendCooldown, v => {
                                        if (v <= 0) return;
                                        countdown = v;
                                        if (timer) clearInterval(timer);
                                        timer = setInterval(() => {
                                            if (countdown > 0) {
                                                countdown--;
                                            } else {
                                                clearInterval(timer);
                                                timer = null;
                                            }
                                        }, 1000);
                                    })
                                ',
                                'x-bind:disabled' => 'countdown > 0',
                                'x-text' => "countdown > 0 ? 'Renvoyer (' + countdown + 's)' : 'Renvoyer le code'",
                            ]),
                    ])->fullWidth(),
                ]),
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Vérification email';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Vérifiez votre adresse email';
    }
}