<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['user_id', 'total', 'profit'];
}


