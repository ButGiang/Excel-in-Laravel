<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class excelController extends Controller
{
    // hàm giúp đọc file excel từ đường dẫn và trả về sheet đang được dùng
    public function loadWorksheet($filePath) {
        $spreadsheet = IOFactory::load($filePath);
        return $spreadsheet->getActiveSheet();
    }

    // hàm đọc dữ liệu từ file excel và lưu vào các biến
    public function store(Request $request) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // Lưu file Excel vào thư mục tạm
            $tempFilePath = $file->store('temp', 'public');
            $filePath = storage_path('app/public/' . $tempFilePath);
            $request->session()->put('filePath', $filePath);

            // lấy active sheet
            $worksheet = $this->loadWorksheet($filePath);
    
            // đọc dữ liệu từ cột đầu tiên đến cột cuối cùng có dữ liệu
            $columnNames = [];
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $columnNames[] = $worksheet->getCell(Coordinate::stringFromColumnIndex($col) . '1')->getValue();
            }
    
            // lấy số hàng cuối cùng có dữ liệu
            $rowCount = $worksheet->getHighestDataRow();
    
            // gửi dữ liệu đến render.blade.php
            return view('render', [
                'worksheet' => $worksheet,
                'columnNames' => $columnNames,
                'rowCount' => $rowCount
            ]);
        } 
        else {
            // nếu input k có file hoặc file k phải đuôi xlsx, xls thì báo lỗi
            return view('Notification', [
                'message' => 'File không tồn tại hoặc định dạng không phù hợp! 
                Chỉ nhận file có định dạng <b>.xlsx</b> và <b>.xls</b>. <br>
                Xin vui lòng thử lại.'
            ]);
        }
    }
    
    // hàm lưu file excel mới về máy
    public function save(Request $request) {
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

    // hàm tạo biểu đồ với dữ liệu đọc từ file excel
    public function chart(Request $request) {
        if($file = $request->file('file')) {
            // Lưu file Excel vào thư mục tạm
            $tempFilePath = $file->store('temp', 'public');
            $filePath = storage_path('app/public/' . $tempFilePath);

            $request->session()->put('filePath', $filePath);
            $worksheet = $this->loadWorksheet($filePath);
            $data = $worksheet->toArray();

            // Xử lý dữ liệu và chuẩn bị dữ liệu cho biểu đồ
            $columns = [];
            $labels = $data[0]; // hàng đầu tiên chứa tiêu đề, lưu trữ nó vào biến $labels
            $labels = array_slice($labels, 1); // bỏ qua ô đầu tiên của hàng tiêu đề

            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];
                $column = [];

                $column['label'] = $row[0]; // cột đầu tiên trong mỗi hàng chứa tên thành phần
                $values = [];
                for ($j = 1; $j < count($row); $j++) {
                    $values[] = $row[$j]; // các cột tiếp theo chứa giá trị
                }
                $column['values'] = $values;

                $columns[] = $column;
            }

            // Truyền dữ liệu cho blade view hiển thị biểu đồ
            return view('chart')->with([
                'labels' => $labels,
                'columns' => json_encode($columns),
            ]);
        }
        else {
            // nếu input k có file hoặc file k phải đuôi xlsx, xls thì báo lỗi
            return view('Notification', [
                'message' => 'File không tồn tại hoặc định dạng không phù hợp! 
                Chỉ nhận file có định dạng <b>.xlsx</b> và <b>.xls</b>. <br>
                Xin vui lòng thử lại.'
            ]);
        }
    }
}
