<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excelController extends Controller
{
    public function loadWorksheet($filePath) {
        $spreadsheet = IOFactory::load($filePath);
        return $spreadsheet->getActiveSheet();
    }

    public function store(Request $request) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // Lưu file Excel vào thư mục tạm
            $tempFilePath = $file->store('temp', 'public');
            $filePath = storage_path('app/public/' . $tempFilePath);
    
            $request->session()->put('filePath', $filePath);
            
            $worksheet = $this->loadWorksheet($filePath);
    
            $columnNames = [];
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $columnNames[] = $worksheet->getCell(Coordinate::stringFromColumnIndex($col) . '1')->getValue();
            }
    
            $rowCount = $worksheet->getHighestDataRow();
    
            return view('render', [
                'worksheet' => $worksheet,
                'columnNames' => $columnNames,
                'rowCount' => $rowCount
            ]);
        } else {
            return view('Notification', [
                'error' => 'File không tồn tại hoặc định dạng không phù hợp. Vui lòng thử lại.'
            ]);
        }
    }
    
    public function save(Request $request)
    {
        $data = $request->input('cell');
        $columnCount = $request->input('columnCount');
        $rowCount = $request->input('rowCount');
    
        // Tạo một đối tượng Spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Ghi dữ liệu vào từng ô trong bảng
        $cellIndex = 0;
        for ($i = 0; $i < $rowCount; $i++) {
            for ($j = 0; $j < $columnCount; $j++) {
                $cellValue = $data[$cellIndex];
                $columnLetter = Coordinate::stringFromColumnIndex($j + 1);
                $cellCoordinate = $columnLetter . ($i + 1);
                $sheet->setCellValue($cellCoordinate, $cellValue);
                $cellIndex++;
            }
        }
    
        // Tạo một đối tượng Writer để ghi tệp Excel
        $writer = new Xlsx($spreadsheet);
    
        // Đặt header để trình duyệt nhận diện tệp Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="newFile.xlsx"');
        header('Cache-Control: max-age=0');
    
        // Ghi tệp Excel vào output
        $writer->save('php://output');
    }
}
