<?php

namespace App\Exports\User;

use App\Enums\Role\Role;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class UserExport
 */
class UserExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return User::query()->withCount('orders')->where('role', '!=', Role::ADMIN->value);
    }

    /**
     * @param User $row
     * @return array
     */
    public function map($row): array
    {
        return [
            'id'           => $row->id,
            'name'         => $row->name,
            'email'        => $row->email,
            'phone'        => $row->phone,
            'orders_count' => empty($row->orders_count) ? 0 : $row->orders_count,
            'created_at'   => $row->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            "Ім'я",
            'Email',
            'Телефон',
            'Кількість замовлень',
            'Дата реєстрації',
        ];
    }
}
