<?php

namespace Jhonoryza\ModelHistory\Factory;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

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
        if ($user == null) {
            return $this;
        }

        $this->data['changed_by'] = is_object($user) ? $user->id : $user;
        $this->data['changed_by_model'] = is_object($user) ? get_class($user) : null;

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

    public function getLogs(int $modelId): Collection|SupportCollection
    {
        return $this->logModel::where('model_id', $modelId)
            ->latest()
            ->with('user:id,name')
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'message' => $log->message,
                'operation' => $log->operation,
                'changed_by' => $log->user ? ['id' => $log->user->id, 'name' => $log->user->name] : null,
                'created_at' => $log->created_at->toISOString(),
                'old_data' => $log->old_data,
                'new_data' => $log->new_data,
            ]);
    }
}
