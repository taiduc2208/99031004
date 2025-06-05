<?php
// Kiểm tra method POST và file upload có tồn tại
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['giffile'])) {
        $file = $_FILES['giffile'];

        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo "Upload lỗi: " . $file['error'];
            exit;
        }

        // Kiểm tra loại file (MIME type)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'image/gif') {
            http_response_code(400);
            echo "Chỉ chấp nhận file GIF thôi nha!";
            exit;
        }

        // Nếu ok thì lưu file vào thư mục uploads (tạo folder uploads trước)
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $targetPath = $uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "Đã nhận file GIF: " . htmlspecialchars($file['name']);
        } else {
            http_response_code(500);
            echo "Lỗi khi lưu file!";
        }
    } else {
        http_response_code(400);
        echo "Không tìm thấy file upload giffile";
    }
} else {
    // Nếu không phải POST thì show form đơn giản
    echo <<<HTML
<form method="POST" enctype="multipart/form-data">
  Chọn file GIF: <input type="file" name="giffile" accept="image/gif" required />
  <button type="submit">Upload</button>
</form>
HTML;
}
