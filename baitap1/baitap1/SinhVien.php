<?php

class SinhVien {
    private $maSV;
    private $name;
    private $age;
    private $email;
    private $lop;
    private $diemTB;
    private $trangThai;

    public function __construct($maSV, $name, $age, $email, $lop, $diemTB, $trangThai) {
        $this->maSV      = $maSV;
        $this->name      = $name;
        $this->age       = $age;
        $this->email     = $email;
        $this->lop       = $lop;
        $this->diemTB    = $diemTB;
        $this->trangThai = $trangThai;
    }

    public function getMaSV()     { return $this->maSV; }
    public function getName()     { return $this->name; }
    public function getAge()      { return $this->age; }
    public function getEmail()    { return $this->email; }
    public function getLop()      { return $this->lop; }
    public function getDiemTB()   { return $this->diemTB; }
    public function getTrangThai(){ return $this->trangThai; }

    // Tính học lực dựa vào điểm trung bình
    public function getHocLuc() {
        if ($this->diemTB >= 8.5) return "Giỏi";
        if ($this->diemTB >= 7.0) return "Khá";
        if ($this->diemTB >= 5.5) return "Trung bình";
        return "Yếu";
    }
}
