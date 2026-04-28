<?php

namespace App\Models;

use App\Notifications\ResetUsuarioPasswordNotification;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str as StringHelper;
use Illuminate\Support\Str;

class Usuario extends Model implements Authenticatable, CanResetPasswordContract
{
    use AuthenticatableTrait, CanResetPassword, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'apellidos',
        'nombre_usuario',
        'correo',
        'contrasena',
        'id_rol',
        'activo',
        'password_changed_at',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
            'activo' => 'boolean',
            'password_changed_at' => 'datetime',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function permisosModulo(): HasMany
    {
        return $this->hasMany(UsuarioModuloPermiso::class, 'id_usuario', 'id_usuario');
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(UsuarioApiToken::class, 'id_usuario', 'id_usuario');
    }

    public function canAccessModule(string $module, array $modulosAdicionales = []): bool
    {
        $module = Str::lower($module);
        $rolNombre = Str::lower($this->rol?->nombre_rol ?? '');

        if (! $this->activo) {
            return false;
        }

        if ($rolNombre === Rol::ADMINISTRACION) {
            return true;
        }

        if ($rolNombre === $module) {
            return true;
        }

        $extrasPersistidos = $this->permisosModulo()
            ->pluck('modulo')
            ->map(fn ($m) => Str::lower((string) $m))
            ->all();

        $normalizados = array_map(fn ($m) => Str::lower((string) $m), $modulosAdicionales);

        return in_array($module, $extrasPersistidos, true) || in_array($module, $normalizados, true);
    }

    public function passwordExpired(int $dias = 90): bool
    {
        if ($this->password_changed_at === null) {
            return true;
        }

        return $this->password_changed_at->addDays($dias)->isPast();
    }

    public function getAuthPassword(): string
    {
        return (string) $this->contrasena;
    }

    public function getEmailForPasswordReset(): string
    {
        return (string) $this->correo;
    }

    public function routeNotificationForMail($notification = null): ?string
    {
        return $this->correo;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetUsuarioPasswordNotification((string) $token));
    }

    public function issueApiToken(array $abilities = ['*'], int $ttlMinutes = 720): array
    {
        $plainTextToken = StringHelper::random(64);
        $tokenHash = hash('sha256', $plainTextToken);

        $token = $this->apiTokens()->create([
            'token_hash' => $tokenHash,
            'abilities' => array_values($abilities),
            'expires_at' => now()->addMinutes($ttlMinutes),
        ]);

        return [
            'plain_text_token' => $plainTextToken,
            'token' => $token,
        ];
    }

    public function revokeAllApiTokens(): void
    {
        $this->apiTokens()
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function checkPassword(string $plainTextPassword): bool
    {
        return Hash::check($plainTextPassword, $this->contrasena);
    }
}
