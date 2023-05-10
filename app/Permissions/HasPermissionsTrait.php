<?php

namespace App\Permissions;

use Illuminate\Contracts\Auth\Access\Gate;

trait HasPermissionsTrait
{
  public function hasPermissionTo($permission)
  {
    return (bool) $this->hasPermission($permission);
  }

  public function hasRole(...$roles)
  {
    foreach ($roles as $role) {
      if ($this->user_type == $role) {
        return true;
      }
    }
    return false;
  }

  protected function hasPermission($permission)
  { 
    // if ($this->user_type == 'admin') {
    //   return true;
    // }
    $permissions = collect($this->user_permissions)->toArray();
    // dd($permissions);
    // dd($permissions);
    // dd($permissions->where('name', $permission)->count());
    // return (bool) $permissions->get($permission);
    // return (bool) $permissions->where('name', $permission)->count();
    // print_r($permission);
    return (bool) in_array($permission,$permissions);
  }

  public function can($permission, $arguments = [])
  {
    return (bool) $this->hasPermission($permission, $arguments);
  }

  public function canAny($_permissions, $arguments = [])
  {
    if ($this->user_type == 'admin') {
      return true;
    }
    $permissions = collect(array_keys((array) $this->user_permissions));
    $permissions = $permissions->map(function ($permission){
      return ['name' => $permission];
    });
    return (bool) $permissions->whereIn('name', $_permissions)->count();
  }

  public function cant($permission, $arguments = [])
  {
    if ($this->user_type == 'admin') {
      return false;
    }
    return !$this->hasPermission($permission, $arguments);
  }

  public function cannot($permission, $arguments = [])
  {
    return $this->cant($permission, $arguments);
  }
}
