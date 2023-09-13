@extends('layout')

@section('content')
    <canvas id="myChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Lấy dữ liệu từ blade view
        var labels = {!! json_encode($labels) !!};
        var columns = {!! $columns !!};

        // Tạo biểu đồ
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: columns.map((column, index) => ({
                    label: column.label,
                    data: column.values,
                    backgroundColor: `rgba(75, 192, 192, ${0.2 + (index * 0.1)})`,
                    borderColor: `rgba(75, 192, 192, ${0.5 + (index * 0.1)})`,
                    borderWidth: 1
                }))
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection