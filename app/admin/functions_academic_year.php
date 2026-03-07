<?php
/**
 * Fungsi untuk menentukan tahun ajaran aktif berdasarkan tanggal
 * Standar pendidikan Indonesia: Juli - Juni
 */

/**
 * Mendapatkan tahun ajaran aktif berdasarkan tanggal saat ini
 * @param mysqli $koneksi - Koneksi database
 * @return array|false - Data tahun ajaran aktif atau false jika tidak ditemukan
 */
function getActiveAcademicYear($koneksi) {
    $current_date = date('Y-m-d');
    $current_year = date('Y');
    $current_month = date('n'); // 1-12
    
    // Tentukan tahun ajaran berdasarkan bulan
    if ($current_month >= 7) {
        // Juli - Desember: tahun ajaran dimulai tahun ini
        $academic_year_start = $current_year;
        $academic_year_end = $current_year + 1;
    } else {
        // Januari - Juni: tahun ajaran dimulai tahun lalu
        $academic_year_start = $current_year - 1;
        $academic_year_end = $current_year;
    }
    
    $academic_year_name = $academic_year_start . '/' . $academic_year_end;
    
    // Cari tahun ajaran di database
    $query = "SELECT * FROM ta WHERE ta_nama = '$academic_year_name'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    // Jika tidak ditemukan, buat tahun ajaran baru
    return createAcademicYear($koneksi, $academic_year_name);
}

/**
 * Membuat tahun ajaran baru jika belum ada
 * @param mysqli $koneksi - Koneksi database
 * @param string $academic_year_name - Nama tahun ajaran (contoh: 2025/2026)
 * @return array|false - Data tahun ajaran yang dibuat atau false jika gagal
 */
function createAcademicYear($koneksi, $academic_year_name) {
    // Nonaktifkan semua tahun ajaran sebelumnya
    mysqli_query($koneksi, "UPDATE ta SET ta_status = 0");
    
    // Buat tahun ajaran baru
    $query = "INSERT INTO ta (ta_nama, ta_status) VALUES ('$academic_year_name', 1)";
    if (mysqli_query($koneksi, $query)) {
        $ta_id = mysqli_insert_id($koneksi);
        return array(
            'ta_id' => $ta_id,
            'ta_nama' => $academic_year_name,
            'ta_status' => 1
        );
    }
    
    return false;
}

/**
 * Memperbarui status tahun ajaran berdasarkan tanggal saat ini
 * @param mysqli $koneksi - Koneksi database
 * @return bool - True jika berhasil, false jika gagal
 */
function updateAcademicYearStatus($koneksi) {
    $current_date = date('Y-m-d');
    $current_year = date('Y');
    $current_month = date('n');
    
    // Tentukan tahun ajaran aktif
    if ($current_month >= 7) {
        $academic_year_start = $current_year;
        $academic_year_end = $current_year + 1;
    } else {
        $academic_year_start = $current_year - 1;
        $academic_year_end = $current_year;
    }
    
    $academic_year_name = $academic_year_start . '/' . $academic_year_end;
    
    // Nonaktifkan semua tahun ajaran
    mysqli_query($koneksi, "UPDATE ta SET ta_status = 0");
    
    // Aktifkan tahun ajaran yang sesuai
    $query = "UPDATE ta SET ta_status = 1 WHERE ta_nama = '$academic_year_name'";
    $result = mysqli_query($koneksi, $query);
    
    // Jika tahun ajaran tidak ada, buat yang baru
    if (mysqli_affected_rows($koneksi) == 0) {
        createAcademicYear($koneksi, $academic_year_name);
    }
    
    return $result;
}

/**
 * Mendapatkan daftar tahun ajaran dengan status yang benar
 * @param mysqli $koneksi - Koneksi database
 * @return array - Array tahun ajaran dengan status yang sudah diperbarui
 */
function getAcademicYearsWithCorrectStatus($koneksi) {
    // Update status terlebih dahulu
    updateAcademicYearStatus($koneksi);
    
    // Ambil semua tahun ajaran
    $query = "SELECT * FROM ta ORDER BY ta_id DESC";
    $result = mysqli_query($koneksi, $query);
    
    $academic_years = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $academic_years[] = $row;
    }
    
    return $academic_years;
}

/**
 * Mendapatkan ID tahun ajaran aktif
 * @param mysqli $koneksi - Koneksi database
 * @return int|false - ID tahun ajaran aktif atau false jika tidak ditemukan
 */
function getActiveAcademicYearId($koneksi) {
    $active_year = getActiveAcademicYear($koneksi);
    return $active_year ? $active_year['ta_id'] : false;
}

/**
 * Mendapatkan nama tahun ajaran aktif
 * @param mysqli $koneksi - Koneksi database
 * @return string|false - Nama tahun ajaran aktif atau false jika tidak ditemukan
 */
function getActiveAcademicYearName($koneksi) {
    $active_year = getActiveAcademicYear($koneksi);
    return $active_year ? $active_year['ta_nama'] : false;
}
?>
