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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'status', 'confirmation_code', 'confirmed'];

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

    protected $appends = ['idle'];

    public function visits() {
          return $this->hasMany('App\Models\Hopper\Visit', 'design_username', 'name');
      }

    public function visitCount(){
      return $this->hasOne('App\Models\Hopper\Visit', 'design_username', 'name')
            ->selectRaw('design_username, count(*) as aggregate')
            ->groupBy('design_username');
    }

    public function getVisitCountAttribute()
    {
      // if relation is not loaded already, let's do it first
      if ( ! array_key_exists('visitCount', $this->relations)){
        $this->load('visitCount');
      }
      $related = $this->getRelation('visitCount');
      // then return the count directly
      return ($related) ? (int) $related->aggregate : 0;
    }

    public function assignments() {
          return $this->hasMany('App\Models\Hopper\Visit', 'assignment_user_id', 'id');
      }

    public function hasAssignments(){
      return $this->assignments()->exists();
    }


    public function getIdleAttribute()
    {
      return ! $this->assignments()->exists();
    }

    public function scopeGraphicOperatorStatus(){

      $query = $this->selectRaw('users.id, users.name, users.email, users.state, roles.name as role, (select count(*) from visits where visits.design_username = users.name) as visits')
            ->leftJoin('assigned_roles', function($join) {
                          $join->on('users.id', '=', 'assigned_roles.user_id');
                      })
                      ->leftJoin('roles', function ($join) {
                          $join->on('assigned_roles.role_id', '=', 'roles.id');
                      })
                      ->where('roles.name', 'Graphic Operator');

      return $query;
    }

    public function scopeGraphicOperators($query){
      return $query->leftJoin('assigned_roles', function($join) {
                          $join->on('users.id', '=', 'assigned_roles.user_id');
                      })
                      ->leftJoin('roles', function ($join) {
                          $join->on('assigned_roles.role_id', '=', 'roles.id');
                      })
                      ->where('roles.name', 'Graphic Operator');
    }


    public function scopeIdleGraphicOperators($query){

      return $query->select('users.*')
            ->leftJoin('assigned_roles', function($join) {
                          $join->on('users.id', '=', 'assigned_roles.user_id');
                      })
                      ->leftJoin('roles', function ($join) {
                          $join->on('assigned_roles.role_id', '=', 'roles.id');
                      })
                      ->where('roles.name', 'Graphic Operator')
            ->where('users.state', 'idle')
            ->get();
    }

    public function scopeIdleOperators($query){

      return $query
            ->leftJoin('assigned_roles', function($join) {
                          $join->on('users.id', '=', 'assigned_roles.user_id');
                      })
                      ->leftJoin('roles', function ($join) {
                          $join->on('assigned_roles.role_id', '=', 'roles.id');
                      })
                      ->where('roles.name', 'Graphic Operator')
            ->where('users.state', 'idle');
    }

    public function scopeUnassignedOperators($query){

      return $query
            ->leftJoin('assigned_roles', function($join) {
                          $join->on('users.id', '=', 'assigned_roles.user_id');
                      })
                      ->leftJoin('roles', function ($join) {
                          $join->on('assigned_roles.role_id', '=', 'roles.id');
                      })
                      ->where('roles.name', 'Graphic Operator')
            ->where('users.state', 'idle');
    }
}
