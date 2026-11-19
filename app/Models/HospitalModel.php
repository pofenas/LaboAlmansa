<?php

namespace App\Models;

use CodeIgniter\Model;

class HospitalModel extends Model
{
    protected $table      = 'hospitales';
    protected $primaryKey = 'id_hospital';
    protected $returnType = 'array';

    const ALMANSA_ID = 1;

    public function listado()
    {
        return $this->orderBy('hospital', 'ASC')->findAll();
    }
}