<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AbstractController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\NotFoundErrorException;

abstract class ApiController extends AbstractController
{
    protected $guard = 'api';

    protected $prefix = 'api.v1';

    protected $dataSelect = ['*'];

    public function jsonRender($data = [])
    {
        $this->compacts['message'] = [
            'code' => 200,
            'status' => true,
        ];

        $compacts = array_merge($data, $this->compacts);

        return response()->json($compacts);
    }

    public function getData(array $params, $relations = [])
    {
        try {
            $items = $this->repository->getData($params, $relations, $this->dataSelect);
        } catch (\Exception $e) {
            throw new NotFoundErrorException($e->getMessage(), $e->getCode());
        }

        return $items;
    }

    public function show($id)
    {
        try {
            $item = $this->repository->findOrFail($id);
            $this->before(__FUNCTION__, $item);
            $this->compacts['item'] = $item;
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new NotFoundErrorException($e->getMessage(), $e->getCode());
        }
    }

    public function storeData(array $data, callable $callback = null)
    {
        try {
            $entity = $this->repository->store($data);

            if (is_callable($callback)) {
                call_user_func_array($callback, [$entity]);
            }
        } catch (\Exception $e) {
            throw new NotFoundErrorException($e->getMessage(), $e->getCode());
        }

        return $this->jsonRender();
    }

    public function updateData(array $data, $id, callable $callback = null)
    {
        try {
            $entity = $this->repository->findOrFail($id);
            $this->before('update', $entity);

            if (is_callable($callback)) {
                call_user_func_array($callback, [$entity]);
            }

            $this->repository->update($entity, $data);

        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new NotFoundErrorException($e->getMessage(), $e->getCode());
        }

        return $this->jsonRender();
    }

    public function deleteData($id, callable $callback = null)
    {
        try {
            $entity = $this->repository->findOrFail($id);
            $this->before('delete', $entity);

            if (is_callable($callback)) {
                call_user_func_array($callback, [$entity]);
            }

            $this->repository->delete($entity);

        } catch (ModelNotFoundException $e) {
            throw new NotFoundException();
        } catch (\Exception $e) {
            throw new NotFoundErrorException($e->getMessage(), $e->getCode());
        }

        return $this->jsonRender();
    }
}
