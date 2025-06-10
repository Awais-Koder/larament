<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;


// use App\Notifications\CustomVerifyEmail;


class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password',
        'company_name',
        'company_reg_no',
        'company_address',
        'company_email',
        'company_phone',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }
    // public function canAccessFilament(): bool
    // {
    //     return $this->hasRole(['super_admin', 'storage_manager']);
    // }

    public function storageCompany()
    {
        // return $this->hasOne(StorageCompany::class);
        return $this->belongsTo(StorageCompany::class);
    }
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
    // public function storageFacilities()
    // {
    //     return $this->hasMany(\App\Models\StorageFacility::class);
    // }
    // public function sendEmailVerificationNotification()
    // {
        // $this->notify(new class($this) extends VerifyEmail {
        //     protected $user;

        //     public function __construct($user)
        //     {
        //         $this->user = $user;
        //     }

        //     // protected function verificationUrl($notifiable)
        //     // {
        //     //     return URL::temporarySignedRoute(
        //     //         'filament.admin.auth.email-verification.verify',
        //     //         Carbon::now()->addMinutes(60),
        //     //         [
        //     //             'id' => $notifiable->getKey(),
        //     //             'hash' => sha1($notifiable->getEmailForVerification()),
        //     //         ]
        //     //     );
        //     // }

        //     public function toMail($notifiable)
        //     {
        //         return (new MailMessage)
        //             ->subject('Verify Email Address')
        //             ->line('Click the button below to verify your email address.')
        //             ->action('Verify Email', $this->verificationUrl($notifiable));
        //     }
        // });
        
    // }
    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new CustomVerifyEmail());
    // }
}
