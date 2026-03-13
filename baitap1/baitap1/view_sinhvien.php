<?php
require_once 'SinhVien.php';

// =============================================
// BƯỚC 1: Tạo mảng sinh viên mẫu ban đầu
// =============================================
// Dùng session để lưu danh sách sinh viên
// (vì nếu không dùng session thì khi F5 lại, dữ liệu sẽ mất)
session_start();

if (!isset($_SESSION['dsSinhVien'])) {
    // Lần đầu vào trang → tạo dữ liệu mẫu
    $_SESSION['dsSinhVien'] = [
        ["SV001", "Nguyễn Văn An",    20, "an.nguyen@example.com",   "CNTT K65",     8.5, "Đang học"],
        ["SV002", "Trần Thị Bình",    19, "binh.tran@example.com",   "Kinh tế K66",  7.2, "Đang học"],
        ["SV003", "Lê Văn Cường",     21, "cuong.le@example.com",    "Cơ khí K64",   9.1, "Tốt nghiệp"],
        ["SV004", "Phạm Thị Dung",    20, "dung.pham@example.com",   "CNTT K65",     6.8, "Đang học"],
        ["SV005", "Đoàn Văn Em",      22, "em.doan@example.com",     "Xây dựng K63", 5.5, "Bảo lưu"],
    ];
}

// =============================================
// BƯỚC 2: Xử lý khi người dùng bấm nút "Thêm"
// =============================================
$thongBao = "";  // Biến để hiện thông báo lỗi hoặc thành công

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Lấy dữ liệu từ form, trim() để xóa khoảng trắng thừa
    $maSV      = trim($_POST['maSV']);
    $name      = trim($_POST['name']);
    $age       = trim($_POST['age']);
    $email     = trim($_POST['email']);
    $lop       = trim($_POST['lop']);
    $diemTB    = trim($_POST['diemTB']);
    $trangThai = $_POST['trangThai'];

    // --- Kiểm tra dữ liệu đơn giản ---
    if ($maSV == "" || $name == "" || $age == "" || $email == "" || $lop == "" || $diemTB == "") {
        $thongBao = "error:Vui lòng điền đầy đủ thông tin!";

    } elseif (!is_numeric($age) || $age < 16 || $age > 60) {
        $thongBao = "error:Tuổi phải là số và nằm trong khoảng 16 - 60!";

    } elseif (!is_numeric($diemTB) || $diemTB < 0 || $diemTB > 10) {
        $thongBao = "error:Điểm TB phải là số từ 0 đến 10!";

    } else {
        // Kiểm tra mã SV có bị trùng không
        $trung = false;
        foreach ($_SESSION['dsSinhVien'] as $sv) {
            if ($sv[0] == $maSV) {
                $trung = true;
                break;
            }
        }

        if ($trung) {
            $thongBao = "error:Mã sinh viên '$maSV' đã tồn tại!";
        } else {
            // Tất cả hợp lệ → thêm vào mảng session
            $_SESSION['dsSinhVien'][] = [$maSV, $name, (int)$age, $email, $lop, (float)$diemTB, $trangThai];
            $thongBao = "success:Thêm sinh viên '$name' thành công!";
        }
    }
}

// =============================================
// BƯỚC 3: Tạo mảng object SinhVien để hiển thị
// =============================================
$dsSinhVien = [];
foreach ($_SESSION['dsSinhVien'] as $sv) {
    $dsSinhVien[] = new SinhVien($sv[0], $sv[1], $sv[2], $sv[3], $sv[4], $sv[5], $sv[6]);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sinh Viên</title>
    <!-- Bootstrap 5 để có giao diện đẹp nhanh -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .status-danghoc   { color: green;  font-weight: bold; }
        .status-totnghiep { color: blue;   font-weight: bold; }
        .status-baoluu    { color: orange; font-weight: bold; }
    </style>
</head>
<body>
<div class="container my-4">

    <h2 class="text-center mb-4">Quản lý Sinh Viên</h2>

    <!-- ==========================================
         PHẦN 1: FORM THÊM SINH VIÊN MỚI
    ========================================== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thêm Sinh Viên Mới</h5>
        </div>
        <div class="card-body">

            <!-- Hiển thị thông báo nếu có -->
            <?php if ($thongBao != ""): ?>
                <?php
                    // Tách loại thông báo và nội dung
                    $parts = explode(":", $thongBao, 2);
                    $loai  = $parts[0];  // "error" hoặc "success"
                    $noi   = $parts[1];  // nội dung thông báo
                ?>
                <div class="alert alert-<?= $loai == 'error' ? 'danger' : 'success' ?>">
                    <?= $noi ?>
                </div>
            <?php endif; ?>

            <!-- Form thêm sinh viên -->
            <!-- method="POST" nghĩa là dữ liệu gửi lên server qua POST -->
            <form method="POST">
                <div class="row g-3">

                    <!-- Mã SV -->
                    <div class="col-md-4">
                        <label class="form-label">Mã SV <span class="text-danger">*</span></label>
                        <input type="text"
                               name="maSV"
                               class="form-control"
                               placeholder="VD: SV006"
                               value="<?= isset($_POST['maSV']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['maSV']) : '' ?>">
                        <!-- value="..." giúp giữ lại dữ liệu người dùng đã nhập khi có lỗi -->
                    </div>

                    <!-- Họ và Tên -->
                    <div class="col-md-4">
                        <label class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="VD: Nguyễn Văn A"
                               value="<?= isset($_POST['name']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['name']) : '' ?>">
                    </div>

                    <!-- Tuổi -->
                    <div class="col-md-4">
                        <label class="form-label">Tuổi <span class="text-danger">*</span></label>
                        <input type="number"
                               name="age"
                               class="form-control"
                               placeholder="VD: 20"
                               min="16" max="60"
                               value="<?= isset($_POST['age']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['age']) : '' ?>">
                    </div>

                    <!-- Email -->
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="VD: a.nguyen@example.com"
                               value="<?= isset($_POST['email']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>

                    <!-- Lớp -->
                    <div class="col-md-4">
                        <label class="form-label">Lớp <span class="text-danger">*</span></label>
                        <input type="text"
                               name="lop"
                               class="form-control"
                               placeholder="VD: CNTT K65"
                               value="<?= isset($_POST['lop']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['lop']) : '' ?>">
                    </div>

                    <!-- Điểm TB -->
                    <div class="col-md-2">
                        <label class="form-label">Điểm TB <span class="text-danger">*</span></label>
                        <input type="number"
                               name="diemTB"
                               class="form-control"
                               placeholder="0 - 10"
                               min="0" max="10" step="0.1"
                               value="<?= isset($_POST['diemTB']) && str_starts_with($thongBao,'error') ? htmlspecialchars($_POST['diemTB']) : '' ?>">
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <!-- select = dropdown chọn một trong nhiều lựa chọn -->
                        <select name="trangThai" class="form-select">
                            <option value="Đang học">Đang học</option>
                            <option value="Tốt nghiệp">Tốt nghiệp</option>
                            <option value="Bảo lưu">Bảo lưu</option>
                        </select>
                    </div>

                </div>

                <!-- Nút thêm -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">➕ Thêm Sinh Viên</button>
                    <a href="view_sinhvien.php" class="btn btn-secondary ms-2">🔄 Reset</a>
                    <!-- Reset = trỏ về trang này để xóa form -->
                </div>
            </form>

        </div>
    </div>
    <!-- KẾT THÚC FORM -->


    <!-- ==========================================
         PHẦN 2: BẢNG DANH SÁCH SINH VIÊN
    ========================================== -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách Sinh Viên</h5>
            <span class="badge bg-light text-dark">Tổng: <?= count($dsSinhVien) ?> sinh viên</span>
        </div>
        <div class="card-body p-0">

            <?php if (empty($dsSinhVien)): ?>
                <div class="alert alert-info text-center m-3">
                    Hiện chưa có sinh viên nào. Hãy thêm sinh viên ở form phía trên!
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Mã SV</th>
                                <th>Họ và Tên</th>
                                <th>Tuổi</th>
                                <th>Email</th>
                                <th>Lớp</th>
                                <th>Điểm TB</th>
                                <th>Học lực</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dsSinhVien as $index => $sv): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($sv->getMaSV()) ?></td>
                                    <td><?= htmlspecialchars($sv->getName()) ?></td>
                                    <td><?= $sv->getAge() ?></td>
                                    <td><?= htmlspecialchars($sv->getEmail()) ?></td>
                                    <td><?= htmlspecialchars($sv->getLop()) ?></td>
                                    <td><?= number_format($sv->getDiemTB(), 1) ?></td>
                                    <td><?= $sv->getHocLuc() ?></td>
                                    <td class="<?php
                                        if ($sv->getTrangThai() == 'Đang học')   echo 'status-danghoc';
                                        elseif ($sv->getTrangThai() == 'Tốt nghiệp') echo 'status-totnghiep';
                                        else echo 'status-baoluu';
                                    ?>">
                                        <?= htmlspecialchars($sv->getTrangThai()) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <!-- KẾT THÚC BẢNG -->

</div>
</body>
</html>
