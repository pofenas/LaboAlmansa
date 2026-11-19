<?php
namespace App\Controllers;

use App\Models\CrudModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CrudController
 *
 * Controlador genérico CRUD. Los controladores hijos deben definir:
 *
 *   $modelClass  FQCN del modelo  (ej. App\Models\GrupoModel)
 *   $pageTitle   Título de la página
 *   $allow       Permiso requerido (heredado de BaseController)
 */
abstract class CrudController extends BaseController
{
    /** FQCN del modelo a usar */
    protected string $modelClass = '';

    /** Instancia del modelo */
    protected CrudModel $model;
    /**
     * Campos que se tratarán como contraseña:
     * se envía md5() en lugar del valor en claro.
     * Si el campo está vacío en edición, se ignora (no se modifica).
     */
    protected array $passwordFields = [];

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
        ): void
        {
            parent::initController($request, $response, $logger);

            if ($this->modelClass === '')
            {
                throw new \RuntimeException('CrudController: $modelClass no definido en ' . static::class);
            }

            $this->model = new $this->modelClass();
        }

    // ----------------------------------------------------------------
    // index — lista principal
    // ----------------------------------------------------------------

    public function index(): void
        {
            $perPage  = (int) ($this->request->getGet('perPage')  ?? 25);
            $page     = (int) ($this->request->getGet('page')     ?? 1);
            $orderBy  = $this->request->getGet('orderBy')  ?? '';
            $orderDir = $this->request->getGet('orderDir') ?? 'ASC';

            // Recoger filtros: cualquier GET que no sea un parámetro reservado
            $reserved = ['perPage', 'page', 'orderBy', 'orderDir'];
            $filters  = [];

            foreach ($this->request->getGet() as $key => $val)
            {
                if (! in_array($key, $reserved, true) && $val !== '')
                {
                    $filters[$key] = $val;
                }
            }

            $perPage = in_array($perPage, [25, 50, 100], true) ? $perPage : 25;
            $page    = max(1, $page);

            $result  = $this->model->getList($filters, $orderBy, $orderDir, $perPage, $page);
            $total   = $result['total'];
            $pages   = (int) ceil($total / $perPage);

            // Metadatos de columnas
            $visibleColumns = array_values($this->model->getVisibleColumns());
            $foreignKeys    = $this->model->getForeignKeys();
            $primaryKey     = $this->model->getPrimaryKey();

            // Opciones FK para los selects de los modales
            $fkOptions = [];
            foreach ($foreignKeys as $col => $fk)
            {
                $fkOptions[$col] = $this->model->getForeignOptions($col);
            }

          $this->render('crud/index', [
                'rows'           => $result['data'],
                'total'          => $total,
                'page'           => $page,
                'pages'          => $pages,
                'perPage'        => $perPage,
                'orderBy'        => $orderBy,
                'orderDir'       => $orderDir,
                'filters'        => $filters,
                'visibleColumns' => $visibleColumns,
                'foreignKeys'    => $foreignKeys,
                'primaryKey'     => $primaryKey,
                'fkOptions'      => $fkOptions,
                'editColumns'    => array_values($this->model->getEditColumns()),
                'insertColumns'  => array_values($this->model->getInsertColumns()),
                'passwordFields' => $this->model->getPasswordFields(),
                'baseUrl'        => current_url(),
                'manyToMany'     => $this->model->getManyToMany(),
            ]);
        }

    public function save(): void
        {
            $pk   = $this->model->getPrimaryKey();
            $id   = $this->request->getPost($pk);
            $data = $this->request->getPost();

                // Convertimos los strings vacíos a null para que PostgreSQL los acepte en fechas
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = null;
                }
            }
            unset($data[$pk], $data['csrf_token_name']);

            // Convertir strings vacíos de FK a null
            foreach (array_keys($this->model->getForeignKeys()) as $field)
            {
                if (array_key_exists($field, $data) && $data[$field] === '')
                {
                    $data[$field] = null;
                }
            }
            // Tratar campos contraseña
            foreach ($this->model->getPasswordFields() as $field)
            {
                if (! array_key_exists($field, $data)) continue;

                if ($data[$field] === '' || $data[$field] === null)
                {
                    // En edición: campo vacío = no cambiar contraseña
                    // En inserción: campo vacío = error de validación (lo captura el modelo)
                    unset($data[$field]);
                }
                else
                {
                    $data[$field] = md5($data[$field]);
                }
            }
            if ($id)
            {
                // UPDATE
                if (! $this->model->update($id, $data))
                {
                    $this->jsonError($this->model->errors());
                    return;
                }
            }
            else
            {
                // INSERT
                if (! $this->model->insert($data))
                {
                    $this->jsonError($this->model->errors());
                    return;
                }
            }

            $this->jsonOk();
        }


    // ----------------------------------------------------------------
    // delete — borrado físico
    // ----------------------------------------------------------------

    public function delete(): void
        {
            $pk = $this->model->getPrimaryKey();
            $id = $this->request->getPost($pk);

            if (! $id)
            {
                $this->jsonError(['error' => 'ID no proporcionado']);
                return;
            }

            if (! $this->model->delete($id))
            {
                $this->jsonError(['error' => 'No se pudo eliminar el registro']);
                return;
            }

            $this->jsonOk();
        }

    // ----------------------------------------------------------------
    // Helpers JSON
    // ----------------------------------------------------------------

    protected function jsonOk(array $extra = []): void
        {
            $response = service('response');
            $response->setContentType('application/json');
            $response->setBody(json_encode(array_merge(['ok' => true], $extra)));
            $response->send();
            exit;
        }

    protected function jsonError(array $errors): void
        {
            $response = service('response');
            $response->setStatusCode(422);
            $response->setContentType('application/json');
            $response->setBody(json_encode(['ok' => false, 'errors' => $errors]));
            $response->send();
            exit;
        }
        /**
     * Devuelve los items asignados y los disponibles para un ID dado.
     * POST params: pivotTable, localId
     */
    public function manyToManyGet(): ResponseInterface
        {
            $pivotTable = $this->request->getPost('pivotTable');
            $localId    = $this->request->getPost('localId');

            if (! $pivotTable || ! $localId)
            {
                return $this->response->setJSON(['error' => 'Parámetros incompletos'])->setStatusCode(400);
            }

            try
            {
                $assigned  = $this->model->getManyToManyItems($pivotTable, $localId);
                $available = $this->model->getManyToManyAvailable($pivotTable, $localId);
                $config    = $this->model->getManyToMany();

                // Buscar el campo 'display' para esta pivotTable
                $display = 'id';
                foreach ($config as $mtm)
                {
                    if ($mtm['pivotTable'] === $pivotTable)
                    {
                        $display = $mtm['display'];
                        break;
                    }
                }

                return $this->response->setJSON([
                    'assigned'  => $assigned,
                    'available' => $available,
                    'display'   => $display,
                ]);
            }
            catch (\Throwable $e)
            {
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        }

    /**
     * Añade una fila en la tabla pivote.
     * POST params: pivotTable, localId, remoteId
     */
    public function manyToManyAdd(): ResponseInterface
        {
            $pivotTable = $this->request->getPost('pivotTable');
            $localId    = $this->request->getPost('localId');
            $remoteId   = $this->request->getPost('remoteId');

            if (! $pivotTable || ! $localId || ! $remoteId)
            {
                return $this->response->setJSON(['error' => 'Parámetros incompletos'])->setStatusCode(400);
            }

            try
            {
                $this->model->manyToManyAdd($pivotTable, $localId, $remoteId);
                return $this->response->setJSON(['ok' => true]);
            }
            catch (\Throwable $e)
            {
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        }

    /**
     * Elimina una fila de la tabla pivote por su PK.
     * POST params: pivotTable, pivotId
     */
    public function manyToManyRemove(): ResponseInterface
        {
            $pivotTable = $this->request->getPost('pivotTable');
            $pivotId    = $this->request->getPost('pivotId');

            if (! $pivotTable || ! $pivotId)
            {
                return $this->response->setJSON(['error' => 'Parámetros incompletos'])->setStatusCode(400);
            }

            try
            {
                $this->model->manyToManyRemove($pivotTable, $pivotId);
                return $this->response->setJSON(['ok' => true]);
            }
            catch (\Throwable $e)
            {
                return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
            }
        }
}
