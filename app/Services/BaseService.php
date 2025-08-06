<?php

namespace App\Services;

abstract class BaseService
{
    abstract protected function getModelClass(): string;

    public function find($id)
    {
        return $this->getModelClass()::find($id);
    }

    public function create(array $data)
    {
        return $this->getModelClass()::create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        $model = $this->find($id);
        return $model->delete();
    }
}
