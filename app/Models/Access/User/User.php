<?php

namespace App\Models\Access\User;

use App\Models\Access\User\Traits\UserAccess;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Access\User\Traits\Attribute\UserAttribute;
use App\Models\Access\User\Traits\Relationship\UserRelationship;

/**
 * Class User
 * @package App\Models\Access\User
 */
class User extends Authenticatable
{

    use SoftDeletes, UserAccess, UserAttribute, UserRelationship;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
	
	
	protected $casts = [
        'history' => 'array',
    ];
	
	
	
	public function scopeGetGraphicOperatorStatus(){
		
		$query = $this->select('users.id', 'users.name', 'users.state')
                    ->leftJoin('assigned_roles', function($join) {
                        $join->on('users.id', '=', 'assigned_roles.user_id');
                    })
                    ->leftJoin('roles', function ($join) {
                        $join->on('assigned_roles.role_id', '=', 'roles.id');
                    })
                    ->where('roles.name', 'Graphic Operator')
                    ->get();
		
		
		return $query;
		
	}
	
}
