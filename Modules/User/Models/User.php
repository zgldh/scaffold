<?php namespace Modules\User\Models;

use Illuminate\Support\Facades\Hash;
use Modules\Upload\Models\Upload;
use Modules\Upload\Traits\HasUploads;
use Modules\User\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\ActivityLog\Traits\LogsActivity;
use Modules\User\Traits\CanResetPassword;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use HasUploads;
    use LogsActivity;
    use SoftDeletes;
    use CanResetPassword;

    public $table = 'z_users';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const ACTIVE = 1;
    const INACTIVE = 0;

    public $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'mobile',
        'is_active',
        'last_login_at',
        'login_times',
    ];

    protected static $logAttributes = [
        'name',
        'email',
        'password',
        'gender',
        'mobile',
        'is_active',
        'last_login_at',
        'login_times',
    ];

    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'name'          => 'string',
        'email'         => 'string',
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
        'login_times'   => 'integer',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'     => 'required',
        'password' => 'required'
    ];

    protected $appends = [
        'avatar_url'
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? $this->avatar->url : null;
    }

    public function avatar()
    {
        return $this->morphOne(Upload::class, 'uploadable')->where('z_uploads.type', Upload::TYPE_AVATAR);
    }

    public function isAdmin()
    {
        return $this->hasRole(RoleRepository::ROLE_SUPER_ADMIN);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
