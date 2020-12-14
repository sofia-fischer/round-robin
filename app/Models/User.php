<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /*
    |-------------------------------------------------------------------------------------
    | Capabilities
    |-------------------------------------------------------------------------------------
    */

    public static function login($email, $password)
    {
        if (Auth::attempt(['email' => $email, 'password' => $password], true)) {
            return Auth::user();
        }
    }

    public static function anonymLogin($name)
    {
        $input = [
            'name'     => $name,
            'email'    => Str::uuid(),
            'password' => Hash::make(Str::uuid()),
            'agent'    => true,
        ];

        $user = User::create($input);
        Auth::login($user, true);

        return $user;
    }

    public static function registerNew($name, $email, $password)
    {
        $input = [
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ];

        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'min:7'],
        ])->validate();

        $user = User::create($input);
        Auth::login($user, true);

        return Auth::user();
    }
}
