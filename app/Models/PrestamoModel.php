<?php

namespace App\Models;

use CodeIgniter\Model;

class PrestamoModel extends Model
{
    protected $table      = 'prestamos';
    protected $primaryKey = 'id_prestamo';
    protected $returnType = 'array';

    protected $allowedFields = [
        'origen', 'destino', 'articulo', 'unidades',
        'fecha_prestamo', 'fecha_dev', 'id_user_p', 'id_user_d', 'notas',
    ];

    private function baseSelect()
    {
        return $this->db->table('prestamos p')
            ->select('p.*, o.hospital AS origen_nombre, d.hospital AS destino_nombre')
            ->join('hospitales o', 'o.id_hospital = p.origen',  'left')
            ->join('hospitales d', 'd.id_hospital = p.destino', 'left');
    }

    public function sinDevolver()
    {
        return $this->baseSelect()
            ->where('p.fecha_dev IS NULL', null, false)
            ->orderBy('p.fecha_prestamo', 'ASC')
            ->get()->getResultArray();
    }

    public function devueltos($limite = 50)
    {
        return $this->baseSelect()
            ->where('p.fecha_dev IS NOT NULL', null, false)
            ->orderBy('p.fecha_prestamo', 'DESC')
            ->limit($limite)
            ->get()->getResultArray();
    }
}