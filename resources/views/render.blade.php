@extends('layout')

@section('content')
    <style>
        .excel-cell:focus {
            box-shadow: 0 0 0 1px #00CC00;
            outline: 2px solid #00CC00;
            outline-offset: -2px;
        }
    </style>

    @php
        $columnCount = 6; // Giá trị mặc định cho số lượng cột
        $rowCount = 8; // Giá trị mặc định cho số lượng hàng
    @endphp

    <div class="tool-bar">
        <button onclick="window.history.back()" class="cancel-btn button">Back</button>
        <button onclick="addColumn()" class="button">Add Column</button>
        <button onclick="addRow()" class="button">Add Row</button>
    </div>

    <form action="/saveFile" method="post" enctype="multipart/form-data">
        @csrf
        <table style="border-collapse: collapse;">
            <tr>
                <th> </th>
                @php $columnIndex = 1; @endphp
                @for ($i = 0; $i < $columnCount; $i++)
                    <th>
                        {{ \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex) }}
                    </th>
                    @php $columnIndex++; @endphp
                @endfor
            </tr> 
            
            @php $rowIndex = 1; @endphp
            @for ($i = 0; $i < $rowCount; $i++)
                <tr>
                    <td>{{ $rowIndex }}</td>
                    @php $columnIndex = 1; @endphp
                    @for ($j = 0; $j < $columnCount; $j++)
                        <td>
                            @php
                                $cellValue = ''; 
                                $cell = $worksheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex);
                                if ($cell) {
                                    $cellValue = $cell->getValue();
                                }
                            @endphp
                            <input type="text" class="excel-cell" name="cell[]" value="{{ $cellValue }}">
                        </td>
                        @php $columnIndex++; @endphp
                    @endfor
                    @php $rowIndex++; @endphp
                </tr>
            @endfor
        </table>

        <input type="hidden" id="columnCount" name="columnCount" value="{{ $columnCount }}">
        <input type="hidden" id="rowCount" name="rowCount" value="{{ $rowCount }}">

        <button type="submit" class="save-btn">Save</button>
    </form>

    <script>
        function addColumn() {
            var columnCountInput = document.getElementById("columnCount");
            var columnCount = parseInt(columnCountInput.value);
            columnCount++;
            columnCountInput.value = columnCount;

            // Lưu trữ giá trị của các ô input hiện có
            var cellValues = [];
            var inputElements = document.getElementsByClassName("excel-cell");
            for (var i = 0; i < inputElements.length; i++) {
                var columnIndex = i % (columnCount - 1);
                cellValues.push(inputElements[i].value);
            }

            // Cập nhật số lượng cột mới
            recreateTable();

            // Khôi phục giá trị của các ô input
            var newInputElements = document.getElementsByClassName("excel-cell");
            for (var i = 0; i < newInputElements.length; i++) {
                var columnIndex = i % columnCount;
                var cellIndex = Math.floor(i / columnCount);
                if (columnIndex !== columnCount - 1) {
                    var oldValueIndex = cellIndex * (columnCount - 1) + columnIndex;
                    if (oldValueIndex < cellValues.length) {
                        newInputElements[i].value = cellValues[oldValueIndex];
                    } else {
                        newInputElements[i].value = '';
                    }
                } else {
                    newInputElements[i].value = '';
                }
            }
        }

        function addRow() {
            var rowCountInput = document.getElementById("rowCount");
            var rowCount = parseInt(rowCountInput.value);
            rowCount++;
            rowCountInput.value = rowCount;

            // Lưu trữ giá trị của các ô input hiện có
            var cellValues = [];
            var inputElements = document.getElementsByClassName("excel-cell");
            for (var i = 0; i < inputElements.length; i++) {
                cellValues.push(inputElements[i].value);
            }

            // Cập nhật số lượng hàng mới
            recreateTable();

            // Khôi phục giá trị của các ô input
            var newInputElements = document.getElementsByClassName("excel-cell");
            for(var i = 0; i < newInputElements.length; i++) {
                if(i < cellValues.length) {
                    newInputElements[i].value = cellValues[i];
                }
                else {
                    newInputElements[i].value = '';
                }
            }
        }

        function recreateTable() {
            var columnCount = parseInt(document.getElementById("columnCount").value);
            var rowCount = parseInt(document.getElementById("rowCount").value);

            var table = document.createElement("table");
            table.style.borderCollapse = "collapse";

            var headerRow = document.createElement("tr");
            var emptyHeaderCell = document.createElement("th");
            headerRow.appendChild(emptyHeaderCell); // Thêm cột thead trống

            var columnIndex = 1;
            for (var i = 0; i < columnCount; i++) {
                var headerCell = document.createElement("th");
                var columnHeader = document.createTextNode(
                    String.fromCharCode(64 + columnIndex)
                );
                headerCell.appendChild(columnHeader);
                headerRow.appendChild(headerCell);
                columnIndex++;
            }
            table.appendChild(headerRow);

            var rowIndex = 1;
            for (var i = 0; i < rowCount; i++) {
                var row = document.createElement("tr");
                var rowNumberCell = document.createElement("td");
                var rowNumber = document.createTextNode(rowIndex);
                rowNumberCell.appendChild(rowNumber);
                row.appendChild(rowNumberCell);

                var columnIndex = 2;
                for (var j = 0; j < columnCount; j++) {
                    var cell = document.createElement("td");
                    var input = document.createElement("input");
                    input.type = "text";
                    input.className = "excel-cell";
                    input.name = "cell[]";
                    cell.appendChild(input);
                    row.appendChild(cell);
                    columnIndex++;
                }

                table.appendChild(row);
                rowIndex++;
            }

            var oldTable = document.querySelector("table");
            oldTable.parentNode.replaceChild(table, oldTable);
        }
    </script>

@endsection