<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportUser implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array {
        return [
          'ID',
          'Name',
          'Email',
          'Created_at',
          'Updated_at'
        ];
    }

    public function collection() {
        $users = User::select('id', 'name', 'email', 'created_at', 'updated_at')->get();
        $formattedUsers = $users->map(function ($user) {
            $user->created_at = Carbon::parse($user->created_at)->format('Y-m-d H:i:s');
            $user->updated_at = Carbon::parse($user->updated_at)->format('Y-m-d H:i:s');
            return $user;
        });

        return $formattedUsers;
    }
}
