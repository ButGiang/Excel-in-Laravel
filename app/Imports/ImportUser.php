<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportUser implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'name' => $row['Name'],
            'email' => $row['Email'],
            'password' => bcrypt($row['password']),
        ]);
    }
}
