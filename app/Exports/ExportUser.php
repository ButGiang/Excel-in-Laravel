<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportUser implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    // chuyển hàng đầu tiên của file export thành kiểu chữ đậm
    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    // tạo heading cho file export
    public function headings(): array {
        return [
          'ID',
          'Name',
          'Email',
          'Created_at',
          'Updated_at'
        ];
    }

    // truy xuất những thông tin sau từ database
    public function collection() {
        return User::select('id', 'name', 'email', 'created_at', 'updated_at')->get();
    }
}
