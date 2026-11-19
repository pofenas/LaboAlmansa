<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class MiPerfil extends BaseController
{
    protected string $pageTitle = 'Mi perfil';

    public function index(): void
        {
        $this->mostrar($this->leerPerfil());
        }

    /**
     * Comprueba la disponibilidad del alias.
     *
     * GET: mi-perfil/alias?alias=...
     */
    public function comprobarAlias(): ResponseInterface
        {
        $alias = $this->request->getGet('alias');

        if (! is_string($alias))
        {
            return $this->respuestaAlias(
                false,
                'El nombre de usuario no es válido.'
            );
        }

        $alias = trim($alias);

        if ($alias === '')
        {
            return $this->respuestaAlias(
                false,
                'El nombre de usuario es obligatorio.'
            );
        }

        if (mb_strlen($alias) > 16)
        {
            return $this->respuestaAlias(
                false,
                'El nombre de usuario admite como máximo 16 caracteres.'
            );
        }

        if ($this->aliasOcupado($alias))
        {
            return $this->respuestaAlias(
                false,
                'Este nombre de usuario ya está en uso.'
            );
        }

        return $this->respuestaAlias(
            true,
            'Nombre de usuario disponible.'
        );
        }

    public function guardar(): ResponseInterface
        {
        $perfil = $this->leerPerfil();

        $campos = [
            'nombre',
            'alias',
            'password_actual',
            'password_nueva',
            'password_repetir',
        ];

        $datos = [];

        foreach ($campos as $campo)
        {
            $valor = $this->request->getPost($campo);

            $datos[$campo] = is_string($valor) ? $valor : '';
        }

        $datos['nombre'] = trim($datos['nombre']);
        $datos['alias']  = trim($datos['alias']);

        // Las contraseñas no se recortan ni se normalizan.
        $reglas = [
            'nombre' => [
                'label' => 'Nombre',
                'rules' => 'required|max_length[256]',
            ],
            'alias' => [
                'label' => 'Nombre de usuario',
                'rules' => 'required|max_length[16]',
            ],
            'password_actual' => [
                'label' => 'Contraseña actual',
                'rules' => 'required|max_length[1024]',
            ],
            'password_nueva' => [
                'label' => 'Nueva contraseña',
                'rules' => 'permit_empty|max_length[1024]',
            ],
            'password_repetir' => [
                'label' => 'Repetición de la nueva contraseña',
                'rules' => 'permit_empty|max_length[1024]',
            ],
        ];

        $errores = [];

        if (! $this->validateData($datos, $reglas))
        {
            $errores = $this->validator->getErrors();
        }

        if (! isset($errores['password_actual']))
        {
            $hashActual = md5($datos['password_actual']);

            if (! hash_equals((string) $perfil['password'], $hashActual))
            {
                $errores['password_actual'] =
                    'La contraseña actual no es correcta.';
            }
        }

        if (
            ! isset($errores['alias'])
            && $this->aliasOcupado($datos['alias'])
        )
        {
            $errores['alias'] =
                'Este nombre de usuario ya está en uso.';
        }

        $cambiarPassword = $datos['password_nueva'] !== ''
            || $datos['password_repetir'] !== '';

        if ($cambiarPassword)
        {
            if ($datos['password_nueva'] === '')
            {
                $errores['password_nueva'] =
                    'Introduce la nueva contraseña.';
            }

            if ($datos['password_repetir'] === '')
            {
                $errores['password_repetir'] =
                    'Repite la nueva contraseña.';
            }
            elseif ($datos['password_nueva'] !== $datos['password_repetir'])
            {
                $errores['password_repetir'] =
                    'Las nuevas contraseñas no coinciden.';
            }
        }

        if ($errores !== [])
        {
            return $this->mostrarErrores($perfil, $datos, $errores);
        }

        $actualizar = [
            'nombre' => $datos['nombre'],
            'alias'  => $datos['alias'],
        ];

        if ($cambiarPassword)
        {
            $actualizar['password'] = md5($datos['password_nueva']);
        }

        $modelo = new UsuarioModel();

        /*
         * Reglas específicas de esta operación.
         * No se actualizan grupo, teléfono, empresa ni otros campos.
         */
        $reglasModelo = [
            'nombre' => 'required|max_length[256]',
            'alias'  => 'required|max_length[16]',
        ];

        if ($cambiarPassword)
        {
            $reglasModelo['password'] = 'required|exact_length[32]';
        }

        $modelo->setValidationRules($reglasModelo);

        try
        {
            $guardado = $modelo->update(
                $this->usuario->idUser,
                $actualizar
            );
        }
        catch (\Throwable $e)
        {
            log_message(
                'error',
                'Error al actualizar el perfil del usuario {id}: {mensaje}',
                [
                    'id'      => $this->usuario->idUser,
                    'mensaje' => $e->getMessage(),
                ]
            );

            // Volver a comprobar por si otro usuario ocupó el alias.
            $errores = $this->aliasOcupado($datos['alias'])
                ? ['alias' => 'Este nombre de usuario ya está en uso.']
                : ['_global' => 'No se han podido guardar los cambios.'];

            return $this->mostrarErrores($perfil, $datos, $errores);
        }

        if (! $guardado)
        {
            $errores = $modelo->errors();

            if ($errores === [])
            {
                $errores['_global'] =
                    'No se han podido guardar los cambios.';
            }

            return $this->mostrarErrores($perfil, $datos, $errores);
        }

        if ($cambiarPassword)
        {
            session()->regenerate(true);
        }

        return redirect()
            ->to(site_url('mi-perfil'))
            ->with('perfil_exito', 'Los cambios se han guardado correctamente.');
        }

    private function leerPerfil(): array
        {
        $modelo = new UsuarioModel();

        $perfil = $modelo->find($this->usuario->idUser);

        if (! $perfil)
        {
            throw PageNotFoundException::forPageNotFound(
                'El usuario no existe.'
            );
        }

        return $perfil;
        }

    private function aliasOcupado(string $alias): bool
        {
        $modelo = new UsuarioModel();

        return $modelo
            ->where('alias', $alias)
            ->where('idUser !=', $this->usuario->idUser)
            ->countAllResults() > 0;
        }

    private function respuestaAlias(bool $disponible,string $mensaje): ResponseInterface
        {
        return $this->response
            ->setHeader('Cache-Control', 'no-store')
            ->setJSON([
                'disponible' => $disponible,
                'mensaje'    => $mensaje,
            ]);
     }

    private function mostrar(array $perfil, array $errores = [] ): void
        {
        // Nunca entregar la contraseña almacenada a la vista.
        $this->render('perfil/index', [
            'perfil' => [
                'nombre' => $perfil['nombre'],
                'alias'  => $perfil['alias'],
            ],
            'errores' => $errores,
        ]);
        }

    private function mostrarErrores(array $perfil, array $datos, array $errores ): ResponseInterface
        {
            $perfil['nombre'] = $datos['nombre'];
            $perfil['alias']  = $datos['alias'];

            $this->response->setStatusCode(422);

            $this->mostrar($perfil, $errores);

            return $this->response;
    }
}