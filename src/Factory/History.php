<?php

namespace Jhonoryza\ModelHistory\Factory;

use Illuminate\Database\Eloquent\Model;

class History
{
    protected string $logModel;

    protected array $data = [];

    public const INSERT = 'INSERT';

    public const UPDATE = 'UPDATE';

    public const DELETE = 'DELETE';

    public const RESTORE = 'RESTORE';

    public const FORCE_DELETE = 'FORCE_DELETE';

    public static function make(string $logModel): self
    {
        $instance = new self;
        $instance->logModel = $logModel;

        return $instance;
    }

    public function changeBy($user): self
    {
        $this->data['changed_by'] = is_object($user) ? $user->id : $user;

        return $this;
    }

    public function old($data): self
    {
        $this->data['old_data'] = $data;

        return $this;
    }

    public function new($data): self
    {
        $this->data['new_data'] = $data;

        return $this;
    }

    public function operation(string $op): self
    {
        $this->data['operation'] = $op;

        return $this;
    }

    public function model(Model $model): self
    {
        $this->data['model_id'] = $model->id;

        return $this;
    }

    public function log(string $message): Model
    {
        $this->data['message'] = $message;
        $logModel = $this->logModel;

        return $logModel::create($this->data);
    }
}
