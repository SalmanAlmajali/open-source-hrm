<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\DeletesUploadedFile;
use App\Traits\FileUploadTrait;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use LaraZeus\Boredom\Concerns\HasBoringAvatar;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles,
        HasUuids,
        DeletesUploadedFile,
        FileUploadTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'profile_photo_path',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'assignee');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'hire_date' => 'date',
            'national_id' => 'integer',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return "https://api.dicebear.com/9.x/lorelei/svg?seed={$this->name}?beardProbability=50?earringsProbability=50?frecklesProbability=50?glasses=variant01,variant02,varian03,varian04,varian05?glassesProbability=80?hairAccessories=flowers?hairAccessoriesProbability=80?size=96
";
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected function uploadAttributes(): array
    {
        return [
            'profile_photo_path'
        ];
    }
}
