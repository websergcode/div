<?php

namespace App\Models\UserApplicationForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @property string $message
 * @property string|null $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserApplicationForm extends Model
{
    protected $fillable = [
        'name',
        'email',
        'status',
        'message',
        'comment',
    ];

    public function scopeIndexFilter($query, array $data)
    {
        return $query->when(isset($data['status']), function (Builder $builder) use ($data) {
            return $builder->where('status', $data['status']);
        })
            ->when(isset($data['date_from'], $data['date_to']), function (Builder $builder) use ($data) {
                return $builder->whereBetween('created_at', [$data['date_from'], $data['date_to']]);
            })
            ->when(isset($data['date_from']) && !isset($data['date_to']), function (Builder $builder) use ($data) {
                return $builder->where('created_at', '>=', $data['date_from']);
            })
            ->when(!isset($data['date_from']) && isset($data['date_to']), function (Builder $builder) use ($data) {
                return $builder->where('created_at', '<=', $data['date_to']);
            });
    }
}
