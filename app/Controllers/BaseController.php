<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Usuario;
use Arifrh\Themes\Themes;

class BaseController extends Controller
{
   // protected IncomingRequest|CLIRequest $request;

    protected $helpers = ['url', 'form', 'session'];

    protected Usuario $usuario;

     /** @var \Arifrh\Themes\Themes */
    protected $theme = null; 

    /** @var string[]  Permisos que dan acceso (vacío = todos) */
    protected array $allow = [];

    /** @var string[]  Permisos que deniegan acceso (prioridad sobre allow) */
    protected array $deny  = [];

    /** @var string  Título de la página actual (para el <title> y el banner) */
    protected string $pageTitle = '';

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
        ): void
        {
           parent::initController($request, $response, $logger);

            $this->usuario = new Usuario();
            $this->verificarAcceso();

            $this->theme = Themes::init();
            $this->theme->addCSS('app');
            $this->theme->setVar('usuario',   $this->usuario);
            $this->theme->setVar('menuItems', $this->construirMenu());
            $this->theme->setVar('pageTitle', $this->pageTitle);
        }

    // ----------------------------------------------------------------
    // Render helper para los controladores hijos
    // ----------------------------------------------------------------

    /**
     * Renderiza una vista dentro del layout del tema.
     *
     * Uso en controladores hijos:
     *   return $this->render('pacientes/listado', ['datos' => $datos]);
     */
    protected function render(string $view, array $data = []): void
        {
            if (! empty($data))
            {
                foreach ($data as $key => $value)
                {
                    $this->theme->setVar($key, $value);
                }
            }
            Themes::render($view);
        }

    // ----------------------------------------------------------------
    // Construcción del menú filtrado por permisos
    // ----------------------------------------------------------------

    /**
     * Recorre $menu de AppMenu y filtra los elementos según los permisos
     * del usuario actual. Devuelve el array listo para la vista.
     *
     * Cada elemento del resultado tiene la forma:
     *   ['tipo' => 'item',  'label' => '...', 'url' => '...']
     *   ['tipo' => 'grupo', 'label' => '...', 'items' => [...]]
     */
    protected function construirMenu(): array
        {
            $config   = config('AppMenu');
            $resultado = [];

            foreach ($config->menu as $entrada)
            {
                // Grupo (acordeón): segundo elemento es un array de items
                if (isset($entrada[1]) && is_array($entrada[1]))
                {
                    $hijos = [];

                    foreach ($entrada[1] as $hijo)
                    {
                        if ($this->puedeVerItem($hijo))
                        {
                            $hijos[] = [
                                'label' => $hijo[0],
                                'url'   => $hijo[1],
                            ];
                        }
                    }

                    // Solo añadir el grupo si tiene al menos un hijo visible
                    if (! empty($hijos))
                    {
                        $resultado[] = [
                            'tipo'   => 'grupo',
                            'label'  => $entrada[0],
                            'items'  => $hijos,
                        ];
                    }
                }
                // Entrada simple
                else
                {
                    if ($this->puedeVerItem($entrada))
                    {
                        $resultado[] = [
                            'tipo'  => 'item',
                            'label' => $entrada[0],
                            'url'   => $entrada[1],
                        ];
                    }
                }
            }
              return $resultado;
        }

    /**
     * Determina si el usuario puede ver un elemento simple del menú.
     * Permiso vacío → visible para cualquier usuario autenticado.
     */
    protected function puedeVerItem(array $item): bool
    {
        $permiso = $item[2] ?? '';

        if ($permiso === '')
        {
            return true;
        }

        return $this->usuario->tiene($permiso);
    }

    // ----------------------------------------------------------------
    // Control de acceso (sin cambios respecto a la versión anterior)
    // ----------------------------------------------------------------

    protected function verificarAcceso(): void
        {
            $sinAllow = empty($this->allow);
            $sinDeny  = empty($this->deny);
            if ($sinAllow && $sinDeny)
            {
                return;
            }

            if (! $sinDeny)
            {
                foreach ($this->deny as $permiso)
                {
                    if ($this->usuario->tiene($permiso))
                    {
                        $this->denegarAcceso();
                        return;
                    }
                }
            }

            if ($sinAllow)
            {
                return;
            }

            foreach ($this->allow as $permiso)
            {
                if ($this->usuario->tiene($permiso))
                {
                    return;
                }
            }

            $this->denegarAcceso();
        }

    protected function denegarAcceso(): void
        {
            service('response')->setStatusCode(403);
            echo view('errors/acceso_denegado', ['usuario' => $this->usuario]);
            exit;
        }
}