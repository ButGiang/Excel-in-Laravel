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
                    <form action="{{ route('import-users') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="form-control">
                        <br>
                        <button class="im-export-btn green-btn">Import USER data</button>
                    </form>
                </div>
                <div class="horizontal-lines"></div>
                <div class="func-item">
                    <p>Export file here:</p>
                    <a class="im-export-btn" href="{{ route('export-users') }}">
						Export USER Data
					</a>
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

        <div class="func-group">
            <div class="func-title">
                <p>Create chart from Excel file</p>
            </div>
            <div class="func-item single-item">
                <p>Select file to create chart:</p>
                <form class="read-file-form" action="/chart" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" class="read-form-input" name="file" accept=".xlsx,.xls">
                    <button type="submit" class=" pink-btn">Create</button>
                </form>
                <div class="attention">
                    <b><u>Attention:</u></b>
                    <p class="attention-text">
                        Please reformat the excel file in the following format <br> for the most accurate results
                    </p>
                    <table class="example-format">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title 1</th>
                                <th>Title 2</th>
                                <th>Title 3</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>name A</td>
                                <td>content 1</td>
                                <td>content 2</td>
                                <td>content 3</td>
                            </tr>
                            <tr>
                                <td>name B</td>
                                <td>content 1</td>
                                <td>content 2</td>
                                <td>content 3</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection