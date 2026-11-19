<?php

namespace App\Libraries;

use App\Models\UsuarioModel;
use App\Models\GrupoModel;
use CodeIgniter\Session\Session;

/**
 * Library Usuario
 *
 * Responsabilidades:
 *  - Leer idUser de sesión; si no existe, ejecutar login.
 *  - Cargar recursivamente todos los permisos del usuario
 *    a través de la jerarquía de grupos (con protección anti-ciclos).
 */
class Usuario
{
    protected Session $session;

    public int    $idUser  = 0;
    public string $alias   = '';
    public string $nombre  = '';
    public int    $idGrupo = 0;

    /**
     * Permisos efectivos del usuario.
     * Formato: ['PERMISO_A' => true, 'PERMISO_B' => true, ...]
     *
     * @var array<string, bool>
     */
    public array $permisos = [];

    public function __construct()
        {
            log_message('debug', '>>> Clase Usuario. Constructor. ' );
            $this->session = session();

            $idUser = (int) $this->session->get('idUser');

            if ($idUser > 0)
                $this->cargarDesdeDB($idUser);
            else
                $this->login();
            
        }

    // ----------------------------------------------------------------
    // Login
    // ----------------------------------------------------------------

    /**
     * Procesa el formulario de login o muestra la vista.
     * Si las credenciales son correctas guarda idUser en sesión.
     */
    /**
     * Muestra el login o procesa las credenciales.
     *
     * Recuerda el destino solicitado mediante GET para volver
     * a él después de una autenticación correcta.
     */
    protected function login(): void
        {
            helper('url');

            $request = service('request');
            $method  = strtolower($request->getMethod());

            /*
            * Recordar una página solicitada.
            *
            * No se guardan peticiones POST ni se utiliza
            * una URL de retorno enviada por el formulario.
            */
            if ($method === 'get')
            {
                $ruta = trim(uri_string(), '/');

                /*
                * Solo admitimos rutas internas sencillas.
                * La raíz no sobrescribe un destino pendiente.
                */
                if (
                    $ruta !== ''
                    && preg_match('~\A[a-zA-Z0-9_/-]+\z~', $ruta) === 1
                )
                {
                    $this->session->set('login_destino', [
                        'ruta'  => $ruta,
                        'query' => $request->getUri()->getQuery(),
                    ]);
                }
            }

            $error = null;

            if ($method === 'post')
            {
                $aliasPost    = $request->getPost('alias');
                $passwordPost = $request->getPost('password');

                /*
                * Solo interpretar como intento de login un POST
                * que contenga los dos campos de credenciales.
                */
                if (is_string($aliasPost) && is_string($passwordPost))
                {
                    $alias    = trim($aliasPost);
                    $password = trim($passwordPost);

                    $model    = new UsuarioModel();
                    $userData = $model->validarCredenciales(
                        $alias,
                        $password
                    );

                    if ($userData)
                    {
                        $destino = $this->session->get('login_destino');

                        // Renovar el identificador tras autenticar.
                        $this->session->regenerate(true);

                        $this->session->set(
                            'idUser',
                            (int) $userData['idUser']
                        );

                        // El destino se consume una única vez.
                        $this->session->remove('login_destino');

                        /*
                        * Sin destino pendiente, mantenemos
                        * la entrada habitual por la raíz.
                        */
                        $url = base_url();

                        if (
                            is_array($destino)
                            && isset($destino['ruta'])
                            && is_string($destino['ruta'])
                            && preg_match(
                                '~\A[a-zA-Z0-9_/-]+\z~',
                                $destino['ruta']
                            ) === 1
                        )
                        {
                            $url = site_url($destino['ruta']);

                            $query = $destino['query'] ?? '';

                            if (
                                is_string($query)
                                && $query !== ''
                                && preg_match(
                                    '/[\x00-\x1F\x7F]/',
                                    $query
                                ) === 0
                            )
                            {
                                $url .= '?' . $query;
                            }
                        }

                        /*
                        * Estamos dentro de una biblioteca ejecutada
                        * desde el constructor: enviar la respuesta
                        * y detener esta petición.
                        *
                        * La siguiente petición cargará el usuario
                        * y comprobará los permisos normalmente.
                        */
                        redirect()
                            ->to($url, 303)
                            ->send();

                        exit;
                    }

                    $error = 'Usuario o contraseña incorrectos.';
                }
            }

            echo view('auth/login', [
                'error' => $error,
            ]);

            exit;
        }

    // ----------------------------------------------------------------
    // Carga de datos desde BD
    // ----------------------------------------------------------------

    protected function cargarDesdeDB(int $idUser): void
        {
            $model    = new UsuarioModel();
            $userData = $model->find($idUser);

            if (! $userData)
            {
                // idUser en sesión ya no existe en BD → forzar nuevo login
                $this->session->remove('idUser');
                $this->login();
                return;
            }

            $this->idUser  = (int) $userData['idUser'];
            $this->alias   = $userData['alias'];
            $this->nombre  = $userData['nombre'];
            $this->idGrupo = (int) $userData['idGrupo'];

            $this->cargarPermisos($this->idGrupo);
        }

    // ----------------------------------------------------------------
    // Carga recursiva de permisos (con protección anti-ciclo)
    // ----------------------------------------------------------------

    /**
     * Recorre la jerarquía de grupos de forma recursiva y acumula
     * los permisos en $this->permisos.
     *
     * $visitados evita bucles infinitos ante ciclos en idPadre.
     *
     * @param int   $idGrupo
     * @param int[] $visitados  IDs de grupos ya procesados
     */
    protected function cargarPermisos(int $idGrupo, array $visitados = []): void
        {
            if ($idGrupo <= 0 || in_array($idGrupo, $visitados, true))
            {
                return;
            }

            $visitados[] = $idGrupo;

            $grupoModel = new GrupoModel();

            // Permisos directamente asignados a este grupo
            $permisosGrupo = $grupoModel->getPermisos($idGrupo);

            foreach ($permisosGrupo as $p)
            {
                $this->permisos[trim($p['permiso'])] = true;
            }

            // Subir al grupo padre si existe
            $grupo = $grupoModel->find($idGrupo);

            if ($grupo && ! empty($grupo['idPadre']))
            {
                $this->cargarPermisos((int) $grupo['idPadre'], $visitados);
            }
        }

    // ----------------------------------------------------------------
    // Helpers públicos
    // ----------------------------------------------------------------

    /**
     * Comprueba si el usuario tiene un permiso concreto.
     *
     * Uso:
     *   if (! $this->usuario->tiene('PACIENTES_VER')) { ... }
     */
    public function tiene(string $permiso): bool
        {
            return isset($this->permisos[$permiso]);
        }

    /** Cierra la sesión del usuario. */
    public function logout(): void
        {
            $this->session->remove('idUser');
            $this->session->destroy();
        }
}