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
                ['Mi cuenta',               '/mi-perfil',               'tel'],
                ['Préstamos',               '/prestamos',               'tel'],
                ['Incidencias pedidos',     '/incidenciasPedidos',      'tel'],
                ['Incidencias materiales',  '/incidenciasMateriales',   'tel'],
                ['Incidencias equipos',     '/incidenciasEquipos',      'tel'],
               
            ]
        ],
        // Grupo: Grupo2
        [
            'Configuración',
            [
                ['Hospitales',                      '/hospitales',  'configurar'],
                ['Pruebas',                         '/pruebas',     'configurar'],
                ['Proveedores',                     '/proveedores', 'configurar'],
                ['Equipos',                         '/equipos',     'configurar'],
                ['Tipos de incidencia en equipos',  '/tie',         'configurar'],
                ['Usuarios',                        'usuarios',     'configurar'],
                ['QR inc. equipos',                 '/fer-equipos', 'configurar'],
            ]
        ],
        // Grupo: Administración
        [
            'Administración',
            [
                
                ['Grupos',     'grupos',      'administrar'],
                ['Permisos',   'permisos',    'administrar'],
            ]
        ],
    ];
}