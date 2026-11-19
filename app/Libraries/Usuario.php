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
    protected function login(): void
        {
            $request = service('request');
                if (strtolower($request->getMethod()) === 'post')
                {
                    $alias    = trim($request->getPost('alias')    ?? '');
                    $password = trim($request->getPost('password') ?? '');

                    $model    = new UsuarioModel();
                    $userData = $model->validarCredenciales($alias, $password);
                    

                    if ($userData)
                    {
                        $this->session->set('idUser', $userData['idUser']);
                        $this->cargarDesdeDB((int) $userData['idUser']);
                        return;
                    }

                    $this->session->setFlashdata('login_error', 'Usuario o contraseña incorrectos.');
                }

            echo view('auth/login', ['error' => session('login_error')]);
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