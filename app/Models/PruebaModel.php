<?php

namespace App\Models;

use CodeIgniter\Model;

class PruebaModel extends Model
{
    protected $table         = 'pruebas';
    protected $primaryKey    = 'id_prueba';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'prueba', 'valor_referencia', 'tubo', 'unidades',
        'caracteristicas', 'significado_clinico', 'condiciones_preanaliticas',
        'cuando_solicitar', 'quien_solicita', 'interferencias',
    ];
}