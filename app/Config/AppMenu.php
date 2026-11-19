<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Definición del menú lateral.
 *
 * Entrada simple:
 *   ['Etiqueta', '/ruta', 'PERMISO']
 *
 * Permiso vacío ('') → visible para todos los usuarios autenticados.
 *
 * Grupo (acordeón):
 *   [
 *     'Etiqueta del grupo',           // primer elemento: título del acordeón
 *     [                               // segundo elemento: array de entradas
 *       ['Subitem 1', '/ruta1', 'PERMISO_A'],
 *       ['Subitem 2', '/ruta2', 'PERMISO_B'],
 *     ]
 *   ]
 */
class AppMenu extends BaseConfig
{
    public array $menu = [

        ['Inicio',      '/',               ''],

        // Grupo: Grupo1
        [
            'Utilidades',
            [
                ['Préstamos',               '/prestamos',               'prestamo'],
                ['Incidencias pedidos',     '/incidenciasPedidos',      ''],
                ['Incidencias materiales',  '/incidenciasMateriales',   ''],
                ['Incidencias equipos',     '/incidenciasEquipos',       ''],
            ]
        ],
        // Grupo: Grupo2
        [
            'Tablas',
            [
                ['Hospitales (Présamos)',           '/hospitales',  'administrar'],
                ['Pruebas',                         '/pruebas',     'administrar'],
                ['Proveedores',                     '/proveedores', 'administrar'],
                ['Equipos',                         '/equipos',     'administrar'],
                ['Tipos de incidencia en equipos',  '/tie',         'administrar'],
            ]
        ],
        // Grupo: Administración
        [
            'Administración',
            [
                ['Usuarios',   'usuarios',    'administrar'],
                ['Grupos',     'grupos',      'administrar'],
                ['Permisos',   'permisos',    'administrar'],
            ]
        ],
    ];
}