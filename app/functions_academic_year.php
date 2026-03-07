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
    
    // JANGAN otomatis membuat tahun ajaran baru jika tidak ada
    // Biarkan user yang membuat tahun ajaran secara manual melalui form
    // return createAcademicYear($koneksi, $academic_year_name);
    
    return false;
}

/**
 * Membuat tahun ajaran baru jika belum ada
 * @param mysqli $koneksi - Koneksi database
 * @param string $academic_year_name - Nama tahun ajaran (contoh: 2025/2026)
 * @return array|false - Data tahun ajaran yang dibuat atau false jika gagal
 */
function createAcademicYear($koneksi, $academic_year_name) {
    
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
    // Cek apakah tabel ta memiliki data
    $check_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ta");
    $check_result = mysqli_fetch_assoc($check_query);
    
    // Jika tabel kosong, tidak perlu update apa-apa
    if ($check_result['total'] == 0) {
        return true;
    }
    
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
    
    // JANGAN otomatis membuat tahun ajaran baru jika tidak ada
    // Biarkan user yang membuat tahun ajaran secara manual melalui form
    
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

/**
 * Mendapatkan query untuk siswa aktif berdasarkan tahun ajaran
 * @param mysqli $koneksi - Koneksi database
 * @param int $ta_id - ID tahun ajaran (opsional, default: tahun ajaran aktif)
 * @return string - Query SQL untuk mendapatkan siswa aktif
 */
function getActiveStudentsQuery($koneksi, $ta_id = null) {
    if ($ta_id === null) {
        // Gunakan tahun ajaran aktif
        $ta_id = getActiveAcademicYearId($koneksi);
        if (!$ta_id) return "SELECT * FROM siswa WHERE 1=0"; // Return empty query if no active year
        
        return "SELECT DISTINCT s.*, j.jurusan_nama, k.kelas_nama 
                FROM siswa s 
                JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                JOIN ta t ON k.kelas_ta = t.ta_id 
                WHERE t.ta_id = '$ta_id' AND s.siswa_status = 'aktif' 
                ORDER BY s.siswa_nama";
    } else {
        // Gunakan tahun ajaran yang ditentukan
        return "SELECT DISTINCT s.*, j.jurusan_nama, k.kelas_nama 
                FROM siswa s 
                JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                WHERE k.kelas_ta = '$ta_id' AND s.siswa_status = 'aktif' 
                ORDER BY s.siswa_nama";
    }
}

/**
 * Mendapatkan query untuk menghitung siswa aktif berdasarkan tahun ajaran
 * @param mysqli $koneksi - Koneksi database
 * @param int $ta_id - ID tahun ajaran (opsional, default: tahun ajaran aktif)
 * @return string - Query SQL untuk menghitung siswa aktif
 */
function getActiveStudentsCountQuery($koneksi, $ta_id = null) {
    if ($ta_id === null) {
        // Gunakan tahun ajaran aktif
        $ta_id = getActiveAcademicYearId($koneksi);
        if (!$ta_id) return "SELECT 0 as total";
        
        return "SELECT COUNT(DISTINCT s.siswa_id) as total 
                FROM siswa s 
                JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                JOIN ta t ON k.kelas_ta = t.ta_id 
                WHERE t.ta_id = '$ta_id' AND s.siswa_status = 'aktif'";
    } else {
        // Gunakan tahun ajaran yang ditentukan
        return "SELECT COUNT(DISTINCT s.siswa_id) as total 
                FROM siswa s 
                JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                WHERE k.kelas_ta = '$ta_id' AND s.siswa_status = 'aktif'";
    }
}
?>
