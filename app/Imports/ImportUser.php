<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportUser implements ToModel, WithHeadingRow
{
    // đọc những dữ liệu từ những cột có tiêu đề tương ứng
    public function model(array $col)
    {
        return new User([
            'name' => $col['Name'],
            'email' => $col['Email'],
            'password' => bcrypt($col['password']),
        ]);
    }
}
