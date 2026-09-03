<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::writeAuditLog($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $dataLama = [];
            $dataBaru = [];

            foreach ($model->getChanges() as $key => $newValue) {
                if ($key === 'updated_at') {
                    continue;
                }

                $dataLama[$key] = $model->getOriginal($key);
                $dataBaru[$key] = $newValue;
            }

            if (empty($dataBaru)) {
                return;
            }

            static::writeAuditLog($model, 'updated', $dataLama, $dataBaru);
        });

        static::deleted(function ($model) {
            static::writeAuditLog($model, 'deleted', $model->getAttributes(), null);
        });
    }

    protected static function writeAuditLog($model, string $action, ?array $dataLama, ?array $dataBaru): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'action' => $action,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'created_at' => now(),
        ]);
    }
}