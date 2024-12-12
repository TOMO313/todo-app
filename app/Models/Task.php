<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; //$table->softDeletes()の場合は記述

class Task extends Model
{
    use SoftDeletes; ////$table->softDeletes()の場合は記述
}
