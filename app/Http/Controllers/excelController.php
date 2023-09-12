<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
            return view('error', [
                'error' => 'File không tồn tại hoặc định dạng không phù hợp. Vui lòng thử lại.'
            ]);
        }
    }
    
    public function save(Request $request) {
        // Lấy dữ liệu từ request
        $cellData = $request->input('cell');
    
        $filePath = $request->session()->get('filePath');
        // Load file Excel gốc
        $spreadsheet = IOFactory::load($filePath);
        // Lấy sheet đầu tiên
        $worksheet = $spreadsheet->getActiveSheet();
    
        // Xác định vị trí ô bắt đầu lưu dữ liệu
        $startRow = 1;
        $startColumn = 1;
    
        // Lưu dữ liệu vào file Excel
        $rowIndex = 0;
        $maxColumnIndex = 0;
    
        $rows = $worksheet->toArray();
        foreach ($rows as $row) {
            $columnIndex = 0;
            foreach ($row as $cell) {
                $cellValue = $cellData[($rowIndex * $maxColumnIndex) + $columnIndex] ?? '';
                $currentRow = $startRow + $rowIndex;
                $currentColumn = Coordinate::stringFromColumnIndex($startColumn + $columnIndex);
                $worksheet->setCellValue($currentColumn . $currentRow, $cellValue);
                $columnIndex++;
            }
            $maxColumnIndex = max($maxColumnIndex, $columnIndex);
            $rowIndex++;
        }
    
        // Xóa các hàng còn lại nếu $cellData có ít hơn số hàng trong file
        $remainingRows = count($rows) - $rowIndex;
        if ($remainingRows > 0) {
            $worksheet->removeRow($rowIndex + 1, $remainingRows);
        }
    
        // Tạo một tên file mới cho file Excel đã chỉnh sửa
        $newFileName = 'newFile.xlsx'; // Tên file mới
    
        // Lưu file Excel sau khi đã cập nhật dữ liệu
        $savePath = storage_path('app/public/' . $newFileName); // Đường dẫn và tên file để lưu
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($savePath);
    
        // Trả về file Excel đã chỉnh sửa cho người dùng tải về
        return response()->download($savePath, $newFileName)->deleteFileAfterSend(true);
    }
}
