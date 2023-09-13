<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportUser;
use App\Exports\ExportUser;

class UserController extends Controller
{                                                                                                          
	public function import(Request $request){
		if ($request->hasFile('file')) {
			Excel::import(new ImportUser, $request->file('file')->store('files'));
			return view('Notification', [
				'message' => 'Đã thêm dữ liệu vào database.'
			]);
		}
		else {
			return view('Notification', [
                'message' => 'File không tồn tại hoặc định dạng không phù hợp. Vui lòng thử lại.'
            ]);
		}
	}

	public function exportUsers(){
		return Excel::download(new ExportUser, 'usersList.xlsx');
	}
}

