<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Akun pengguna yang memiliki role petugas. Akun dinkes/admin tetap memakai User.
 */
class Petugas extends User
{
    public function scopePetugas(Builder $query): Builder
    {
        return $query->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->where('name', 'petugas'));
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return parent::resolveRouteBindingQuery($query, $value, $field)->petugas();
    }
}