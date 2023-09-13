<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Work with Excel in Laravel</title>
</head>

<body>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="modal-title">Message:</span>
            <p class="modal-message">{{ $message }}</p>
            <button class="modal-btn" onclick="window.history.back()">OK</button>
        </div>
    </div>

    <script>
        // Lấy modal và nút đóng
        var modal = document.getElementById("myModal");
        var closeBtn = document.getElementsByClassName("close")[0];

        // Hiển thị modal
        modal.style.display = "block";

        // Đóng modal khi nhấp vào bất kỳ vị trí nào bên ngoài modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                window.history.back();
            }
        };
    </script>
</body>
</html>