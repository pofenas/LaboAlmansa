<?php

use CodeIgniter\Router\RouteCollection;


// Función helper para registrar todas las rutas de un CRUD
// Función helper para registrar todas las rutas de un CRUD
function crudRoutes(RouteCollection $routes, string $path, string $controller): void
{
    $routes->match(['get', 'post'], $path,              "$controller::index");
    $routes->match(['get', 'post'], "$path/save",       "$controller::save");
    $routes->match(['get', 'post'], "$path/delete",     "$controller::delete");
    $routes->match(['get', 'post'], "$path/mtm/get",    "$controller::manyToManyGet");
    $routes->match(['get', 'post'], "$path/mtm/add",    "$controller::manyToManyAdd");
    $routes->match(['get', 'post'], "$path/mtm/remove", "$controller::manyToManyRemove");
}
/**
 * @var RouteCollection $routes
 */


$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('LaboController');
$routes->setDefaultMethod('index');
$routes->get('/', 'GuestController::index');
$routes->get('/api/buscar', 'GuestController::buscar');
$routes->get('/prueba/(:num)', 'GuestController::prueba/$1');
$routes->get('/logout', static function() {
    session()->destroy();
    return redirect()->to('/');
});
$routes->post('grupos/mtm/get',    'Grupos::manyToManyGet');
$routes->post('grupos/mtm/add',    'Grupos::manyToManyAdd');
$routes->post('grupos/mtm/remove', 'Grupos::manyToManyRemove');
$routes->get('labo',            'LaboController::index');
$routes->get('Guest',            'GuestController::index');
crudRoutes($routes, 'hospitales', 'Hospitales');
crudRoutes($routes, 'pruebas', 'Pruebas');
$routes->get('prestamos',                'PrestamoController::index');
$routes->get('prestamos/hospitales',     'PrestamoController::hospitales');
$routes->post('prestamos/guardar',       'PrestamoController::guardar');
$routes->post('prestamos/eliminar/(:num)','PrestamoController::eliminar/$1');
$routes->post('prestamos/devolver/(:num)','PrestamoController::devolver/$1');
$routes->post('prestamos/anular-devolucion/(:num)', 'PrestamoController::anularDevolucion/$1');

crudRoutes($routes, 'proveedores', 'Proveedores');

crudRoutes($routes, 'incidencias_pedidos', 'IncidenciasPedidos');

crudRoutes($routes, 'incidencias_materiales', 'IncidenciasMateriales');

$routes->get('equipos', 'Equipos::index');
$routes->post('equipos/save', 'Equipos::save');
$routes->post('equipos/delete', 'Equipos::delete');

// Mantenimiento de tipos de incidencias.
$routes->get('tie', 'Tie::index');
$routes->post('tie/save', 'Tie::save');
$routes->post('tie/delete', 'Tie::delete');

// Equipos aplicables a cada tipo de incidencia.
$routes->post('tie/mtm/get', 'Tie::manyToManyGet');
$routes->post('tie/mtm/add', 'Tie::manyToManyAdd');
$routes->post('tie/mtm/remove', 'Tie::manyToManyRemove');
$routes->get(
    'incidenciasEquipos',
    'IncidenciasEquipos::index'
);

$routes->post(
    'incidenciasEquipos/save',
    'IncidenciasEquipos::save'
);

$routes->post(
    'incidenciasEquipos/delete',
    'IncidenciasEquipos::delete'
);