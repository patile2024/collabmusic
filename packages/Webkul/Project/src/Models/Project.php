<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Project\Contracts\Project as ProjectContract;

class Project extends Model implements ProjectContract
{
    protected $fillable = [
        'name',
        'start_date',
        'kpi',
    ];
}
