<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Area extends Model
{
    use SoftDeletes;

    //
    protected $fillable = [
        'name', 'region_id', 
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */

	public function region()
    {
        return $this->belongsTo('App\Region', 'region_id');
    }

    public function districts()
    {
        return $this->hasMany('App\District', 'area_id');
    }

    public function dmAreas()
    {
        return $this->hasMany('App\DmArea', 'area_id');
    }

    public function trainerAreas()
    {
        return $this->hasMany('App\TrainerArea', 'area_id');
    }

	/**
     * Filtering Berdasarakan Request User
     * @param $query
     * @param QueryFilters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
