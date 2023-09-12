@extends('layout')

@section('content')
    <div class="function-bar">
        <div class="func-group">
            <div class="func-title">
                <p>Import and Export Excel data to database</p>
            </div>

            <div class="func-list">
                <div class="func-item">
                    <p>Import file here:</p>
                    <button class="im-export-btn green-btn">Import USER file</button>
                </div>
                <div class="func-item">
                    <p>Export file here:</p>
                    <button class="im-export-btn">Export USER file</button>
                </div>
            </div>
        </div>

        <div class="func-group">
            <div class="func-title">
              <p>Read & Write Excel file</p>
            </div>
            <div class="func-item single-item">
                <p>Select file to read:</p>
                <form class="read-file-form" action="/readFile" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" class="read-form-input" name="file" accept=".xlsx,.xls">
                    <button type="submit">Read file</button>
                </form>
            </div>
        </div>
    </div>
@endsection