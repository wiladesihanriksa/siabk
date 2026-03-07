-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Mar 2026 pada 13.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siabk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ai_usage_tracking`
--

CREATE TABLE `ai_usage_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tier` enum('free','pro','enterprise') DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `request_count` int(11) DEFAULT 1,
  `usage_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ai_usage_tracking`
--

INSERT INTO `ai_usage_tracking` (`id`, `user_id`, `tier`, `provider`, `request_count`, `usage_date`, `created_at`) VALUES
(1, 94, 'free', 'gemini-flash', 1, '2025-10-08', '2025-10-08 01:37:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','file','number') DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `app_settings`
--

INSERT INTO `app_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'SIABK', 'text', 'Nama Aplikasi', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(2, 'app_description', 'Sistem Informasi Administrasi BK', 'textarea', 'Deskripsi Aplikasi', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(3, 'app_logo', 'logo.png', 'file', 'Logo Aplikasi (untuk header)', '2025-09-29 01:54:59', '2025-09-29 02:32:46'),
(4, 'app_favicon', 'favicon.png', 'file', 'Favicon Aplikasi', '2025-09-29 01:54:59', '2025-12-18 15:18:53'),
(5, 'login_logo', 'login_logo.png', 'file', 'Logo untuk halaman login', '2025-09-29 01:54:59', '2025-09-29 02:32:46'),
(6, 'app_version', '1.0.0', 'text', 'Versi Aplikasi', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(7, 'app_author', 'SMK LPS 2 Ciamis', 'text', 'Nama Institusi', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(8, 'app_email', 'wilade.sihan23@guru.smk.belajar.id', 'text', 'Email Kontak', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(9, 'app_phone', '08872455527', 'text', 'Nomor Telepon', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(10, 'app_address', 'Jalan R.E. Martadinata No. 23 Maleber Ciamis', 'textarea', 'Alamat Institusi', '2025-09-29 01:54:59', '2026-02-15 02:47:13'),
(11, 'primary_color', '#38571a', 'text', 'Warna Primary (Header, Button)', '2025-09-29 01:56:58', '2025-10-06 03:18:22'),
(12, 'secondary_color', '#005200', 'text', 'Warna Secondary (Accent)', '2025-09-29 01:56:58', '2025-10-06 03:18:22'),
(13, 'success_color', '#00a65a', 'text', 'Warna Success (Hijau)', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(14, 'warning_color', '#f39c12', 'text', 'Warna Warning (Kuning)', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(15, 'danger_color', '#dd4b39', 'text', 'Warna Danger (Merah)', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(16, 'info_color', '#3c8dbc', 'text', 'Warna Info (Biru)', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(17, 'sidebar_color', '#222d32', 'text', 'Warna Sidebar', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(18, 'sidebar_hover', '#1e282c', 'text', 'Warna Sidebar Hover', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(19, 'text_color', '#333333', 'text', 'Warna Text Utama', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(20, 'background_color', '#f4f4f4', 'text', 'Warna Background', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(21, 'header_skin', 'skin-blue', 'text', 'Skin Header (skin-blue, skin-green, skin-purple, skin-red, skin-yellow, skin-black)', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(22, 'sidebar_skin', 'skin-blue', 'text', 'Skin Sidebar', '2025-09-29 01:56:58', '2025-09-29 01:56:58'),
(23, 'chatbot_ai_provider', 'gemini-flash', 'text', 'AI Provider yang digunakan (gemini, openai, huggingface, ollama)', '2025-10-07 06:40:36', '2025-10-08 02:41:42'),
(24, 'chatbot_api_key', 'AIzaSyCJQIBmYFWXHlZ1n_5aCDIQCL92_3Sk1VI', 'text', 'API Key untuk cloud AI provider', '2025-10-07 06:40:36', '2025-10-08 02:41:42'),
(25, 'chatbot_cloud_enabled', '1', 'text', 'Status aktifasi cloud AI (1=aktif, 0=nonaktif)', '2025-10-07 06:40:36', '2025-10-08 00:40:16'),
(26, 'chatbot_fallback_enabled', '1', 'text', 'Status fallback ke rule-based (1=aktif, 0=nonaktif)', '2025-10-07 06:40:36', '2025-10-08 00:40:16'),
(27, 'chatbot_name', 'Azka AI  Assistant', 'text', 'Nama chatbot', '2025-10-07 06:41:00', '2025-12-18 15:27:00'),
(28, 'chatbot_description', 'Assalamualaikum Wr Wb\r\nPerkenalkan saya Azka AI asisten \r\nSaya siap membantu Anda baik tentang aplikasi ini ataupun konseling secara pribadi .', 'text', 'Deskripsi chatbot', '2025-10-07 06:41:00', '2025-12-18 15:27:00'),
(29, 'chatbot_status', 'Online', 'text', 'Status chatbot', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(30, 'chatbot_avatar', 'fas fa-robot', 'text', 'Icon avatar', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(31, 'chatbot_theme', 'modern', 'text', 'Tema', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(32, 'chatbot_position', 'bottom-right', 'text', 'Posisi widget', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(33, 'chatbot_welcome_message', 'Assalamualaikum Wr Wb\r\nPerkenalkan saya Azka AI asisten \r\nSaya siap membantu Anda baik tentang aplikasi ini ataupun konseling secara pribadi .', 'text', 'Welcome message', '2025-10-07 06:41:00', '2025-12-18 15:27:00'),
(34, 'chatbot_show_notification', '1', 'text', 'Tampilkan badge notifikasi', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(35, 'chatbot_auto_open', '0', 'text', 'Auto-open untuk user baru', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(36, 'chatbot_quick_actions', '[\"Apa itu SISBK?\",\"Cara login ke SISBK\",\"Fitur dashboard SISBK\",\"Manajemen kasus siswa\",\"Laporan dan dokumentasi\",\"Troubleshooting teknis\"]', 'text', 'Quick actions', '2025-10-07 06:41:00', '2025-10-07 06:41:00'),
(37, 'chatbot_faq', '[{\"question\":\"Apa itu SISBK?\",\"answer\":\"SISBK adalah sistem informasi poin dan manajemen bimbingan konseling\"},{\"question\":\"Bagaimana cara login ke SISBK?\",\"answer\":\"Gunakan username & password yang diberikan oleh admin\"},{\"question\":\"Apa saja fitur utama SISBK?\",\"answer\":\"Manajemen Poin, Layanan BK, Kasus Siswa, kunjungan rumah dan dilaporkan secara realtime\"}]', 'text', 'FAQ', '2025-10-07 06:41:00', '2025-10-09 09:06:26'),
(38, 'ai_tier_system_enabled', '1', 'text', 'Status sistem tier AI (1=aktif, 0=nonaktif)', '2025-10-07 06:41:21', '2025-10-08 00:39:50'),
(39, 'ai_free_tier_provider', 'gemini-flash', 'text', 'AI provider untuk user free tier', '2025-10-07 06:41:21', '2025-10-08 00:39:50'),
(40, 'ai_pro_tier_provider', 'gemini-pro', 'text', 'AI provider untuk user pro tier', '2025-10-07 06:41:21', '2025-10-08 00:39:50'),
(41, 'ai_enterprise_tier_provider', 'openai-gpt4', 'text', 'AI provider untuk user enterprise tier', '2025-10-07 06:41:21', '2025-10-08 00:39:50'),
(42, 'ai_free_tier_limits', '{\"daily_requests\": 50, \"monthly_requests\": 1000}', 'text', 'Limit untuk free tier', '2025-10-07 06:41:21', '2025-10-08 00:39:50'),
(43, 'ai_pro_tier_limits', '{\"daily_requests\": 500, \"monthly_requests\": 10000}', 'text', 'Limit untuk pro tier', '2025-10-07 06:41:22', '2025-10-08 00:39:51'),
(44, 'ai_enterprise_tier_limits', '{\"daily_requests\": 5000, \"monthly_requests\": 100000}', 'text', 'Limit untuk enterprise tier', '2025-10-07 06:41:22', '2025-10-08 00:39:51'),
(45, 'ai_fallback_enabled', '1', 'text', 'Fallback ke tier lebih rendah jika limit habis', '2025-10-07 06:41:22', '2025-10-07 06:41:22'),
(58, 'chatbot_ollama_enabled', '0', 'text', 'Status aktifasi Ollama AI (1=aktif, 0=nonaktif)', '2025-10-08 00:40:16', '2025-10-08 00:40:16'),
(59, 'chatbot_ollama_url', 'http://localhost:11434', 'text', 'URL server Ollama', '2025-10-08 00:40:16', '2025-10-08 00:40:16'),
(60, 'chatbot_ollama_model', 'llama2:7b', 'text', 'Model AI Ollama yang digunakan', '2025-10-08 00:40:16', '2025-10-08 00:40:16'),
(61, 'chatbot_enabled', '1', 'text', 'Status aktifasi chatbot (1=aktif, 0=nonaktif)', '2025-10-08 00:40:16', '2025-10-08 00:40:16'),
(84, 'chatbot_app_features', 'Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, Mobile Friendly', 'text', 'Fitur aplikasi untuk chatbot', '2025-10-08 01:25:55', '2025-10-08 01:25:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_kasus`
--

CREATE TABLE `audit_kasus` (
  `audit_id` int(11) NOT NULL,
  `kasus_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('CREATE','READ','UPDATE','DELETE') NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_kunjungan`
--

CREATE TABLE `audit_kunjungan` (
  `audit_id` int(11) NOT NULL,
  `kunjungan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('CREATE','READ','UPDATE','DELETE') NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback_siswa`
--

CREATE TABLE `feedback_siswa` (
  `feedback_id` int(11) NOT NULL,
  `jurnal_id` int(11) NOT NULL,
  `kasus_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `feedback_siswa`
--

INSERT INTO `feedback_siswa` (`feedback_id`, `jurnal_id`, `kasus_id`, `siswa_id`, `feedback_text`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'masih tetap tidak bisa, apa akrena saysa lapar?', 1, '2025-12-18 18:22:34', '2025-12-18 19:03:49'),
(2, 2, 1, 5, 'oke terimakasih bapak', 1, '2025-12-18 18:59:30', '2025-12-18 19:03:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru_bk`
--

CREATE TABLE `guru_bk` (
  `guru_bk_id` int(11) NOT NULL,
  `nama_guru_bk` varchar(255) NOT NULL,
  `nip_guru_bk` varchar(50) DEFAULT NULL,
  `email_guru_bk` varchar(100) DEFAULT NULL,
  `telepon_guru_bk` varchar(20) DEFAULT NULL,
  `alamat_guru_bk` text DEFAULT NULL,
  `jabatan_guru_bk` varchar(100) DEFAULT 'Guru BK',
  `status_guru_bk` enum('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  `user_id` int(11) DEFAULT NULL COMMENT 'Link ke tabel user untuk login',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `guru_bk`
--

INSERT INTO `guru_bk` (`guru_bk_id`, `nama_guru_bk`, `nip_guru_bk`, `email_guru_bk`, `telepon_guru_bk`, `alamat_guru_bk`, `jabatan_guru_bk`, `status_guru_bk`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Wilade Sihan Riksa, S.T.', '-', 'wilade.sihan23@guru.smk.belajar.id', '0887245527', 'Dusun Sukamulya, RT/RW 016/007, Desa Kertabumi, Kec. Cijeungjing, Kab. Ciamis, Jawa Barat', 'Guru BK', 'Aktif', 14, '2025-12-18 17:13:13', '2026-02-15 06:47:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `input_pelanggaran`
--

CREATE TABLE `input_pelanggaran` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `siswa` int(11) NOT NULL,
  `kelas` int(11) NOT NULL,
  `pelanggaran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `input_prestasi`
--

CREATE TABLE `input_prestasi` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `siswa` int(11) NOT NULL,
  `kelas` int(11) NOT NULL,
  `prestasi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_kasus`
--

CREATE TABLE `jurnal_kasus` (
  `jurnal_id` int(11) NOT NULL,
  `kasus_id` int(11) NOT NULL,
  `tanggal_konseling` date NOT NULL,
  `bentuk_layanan` varchar(255) DEFAULT NULL,
  `uraian_sesi` text NOT NULL,
  `analisis_diagnosis` text DEFAULT NULL,
  `tindakan_intervensi` text DEFAULT NULL,
  `rencana_tindak_lanjut` text DEFAULT NULL,
  `lampiran_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `jurusan_id` int(11) NOT NULL,
  `jurusan_nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`jurusan_id`, `jurusan_nama`) VALUES
(1, 'Teknik Pemesinan'),
(2, 'Teknik Kendaraan Ringan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasus_siswa`
--

CREATE TABLE `kasus_siswa` (
  `kasus_id` int(11) NOT NULL,
  `kasus_kode` varchar(20) DEFAULT NULL,
  `siswa_id` int(11) NOT NULL,
  `tanggal_pelaporan` date NOT NULL,
  `sumber_kasus` enum('Wali Kelas','Guru Mapel','Orang Tua','Inisiatif Siswa','Teman','Temuan Guru BK') NOT NULL,
  `kategori_masalah` enum('Pribadi','Sosial','Belajar','Karir') NOT NULL,
  `judul_kasus` varchar(255) NOT NULL,
  `deskripsi_awal` text DEFAULT NULL,
  `status_kasus` enum('Baru','Dalam Proses','Selesai/Tuntas','Dirujuk/Alih Tangan Kasus') NOT NULL DEFAULT 'Baru',
  `guru_bk_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` int(11) NOT NULL,
  `kelas_nama` varchar(255) NOT NULL,
  `kelas_jurusan` int(11) NOT NULL,
  `kelas_ta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas_siswa`
--

CREATE TABLE `kelas_siswa` (
  `ks_id` int(11) NOT NULL,
  `ks_siswa` int(11) NOT NULL,
  `ks_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunjungan_rumah`
--

CREATE TABLE `kunjungan_rumah` (
  `kunjungan_id` int(11) NOT NULL,
  `kunjungan_kode` varchar(20) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `waktu_kunjungan` time NOT NULL,
  `alamat_kunjungan` text NOT NULL,
  `petugas_bk_id` int(11) NOT NULL,
  `tujuan_kunjungan` text NOT NULL,
  `pihak_ditemui` text NOT NULL,
  `hasil_observasi` text NOT NULL,
  `ringkasan_wawancara` text NOT NULL,
  `kesimpulan` text NOT NULL,
  `rekomendasi_tindak_lanjut` text NOT NULL,
  `lampiran_foto` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lampiran_kunjungan`
--

CREATE TABLE `lampiran_kunjungan` (
  `lampiran_id` int(11) NOT NULL,
  `kunjungan_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `ukuran_file` int(11) NOT NULL,
  `tipe_file` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `layanan_bk`
--

CREATE TABLE `layanan_bk` (
  `layanan_id` int(11) NOT NULL,
  `tanggal_pelaksanaan` date NOT NULL,
  `jenis_layanan` enum('Layanan Klasikal','Bimbingan Kelompok','Konseling Kelompok','Konsultasi','Mediasi','Layanan Advokasi','Layanan Peminatan','Lainnya') NOT NULL,
  `topik_materi` varchar(255) NOT NULL,
  `bidang_layanan` enum('Pribadi','Sosial','Belajar','Karir') NOT NULL,
  `sasaran_layanan` enum('Satu Kelas','Kelompok Siswa','Individu') NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `jumlah_peserta` int(11) NOT NULL DEFAULT 0,
  `uraian_kegiatan` text DEFAULT NULL,
  `evaluasi_proses` text DEFAULT NULL,
  `evaluasi_hasil` text DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `status_layanan` enum('Aktif','Selesai','Dibatalkan') DEFAULT 'Aktif',
  `lampiran_foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `layanan_bk_peserta`
--

CREATE TABLE `layanan_bk_peserta` (
  `peserta_id` int(11) NOT NULL,
  `layanan_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi_rtl`
--

CREATE TABLE `notifikasi_rtl` (
  `notif_id` int(11) NOT NULL,
  `jurnal_id` int(11) NOT NULL,
  `tanggal_reminder` date NOT NULL,
  `pesan_reminder` text NOT NULL,
  `status_reminder` enum('Belum','Sudah','Dibatalkan') NOT NULL DEFAULT 'Belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `pelanggaran_id` int(11) NOT NULL,
  `pelanggaran_nama` varchar(255) NOT NULL,
  `pelanggaran_point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggaran`
--

INSERT INTO `pelanggaran` (`pelanggaran_id`, `pelanggaran_nama`, `pelanggaran_point`) VALUES
(1, 'Merokok', 15),
(2, 'Terlambat Datang Ke Sekolah', 2),
(4, 'Tidak memakai atribut sekolah (dasi)', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `prestasi`
--

CREATE TABLE `prestasi` (
  `prestasi_id` int(11) NOT NULL,
  `prestasi_nama` varchar(255) NOT NULL,
  `prestasi_point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prestasi`
--

INSERT INTO `prestasi` (`prestasi_id`, `prestasi_nama`, `prestasi_point`) VALUES
(1, 'Juara 1 Tingkat Kecamatan', 10),
(2, 'Juara Perlombaan Tingkat Kabupaten', 10),
(4, 'Partisipasi Mengikuti Upacara 17 Agustus', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int(11) NOT NULL,
  `siswa_nama` varchar(255) NOT NULL,
  `siswa_nis` varchar(255) NOT NULL,
  `siswa_jurusan` int(11) NOT NULL,
  `siswa_status` varchar(255) NOT NULL,
  `siswa_password` varchar(255) NOT NULL,
  `siswa_foto` varchar(100) DEFAULT NULL,
  `siswa_telepon` varchar(20) DEFAULT NULL,
  `siswa_email` varchar(100) DEFAULT NULL,
  `siswa_alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ta`
--

CREATE TABLE `ta` (
  `ta_id` int(11) NOT NULL,
  `ta_nama` varchar(255) NOT NULL,
  `ta_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ta`
--

INSERT INTO `ta` (`ta_id`, `ta_nama`, `ta_status`) VALUES
(4, '2025/2026', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_nama` varchar(100) NOT NULL,
  `user_username` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_foto` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_telepon` varchar(20) DEFAULT NULL,
  `user_alamat` text DEFAULT NULL,
  `user_level` varchar(20) NOT NULL,
  `user_tier` enum('free','pro','enterprise') DEFAULT 'free'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`user_id`, `user_nama`, `user_username`, `user_password`, `user_foto`, `user_email`, `user_telepon`, `user_alamat`, `user_level`, `user_tier`) VALUES
(10, 'Wilade Sihan RIksa', 'admin', 'edf16e91044a1c1e44a0d38b83b5a34f', 'admin_10_1771127169.jpeg', 'wilade95@gmail.com', '08872455527', '', 'administrator', 'free'),
(14, 'Wilade Sihan Riksa, S.T.', 'wilade', 'edf16e91044a1c1e44a0d38b83b5a34f', 'admin_14_1771126967.jpeg', 'wilade.sihan23@guru.smk.belajar.id', '0887245527', 'Dusun Sukamulya, RT/RW 016/007, Desa Kertabumi, Kec. Cijeungjing, Kab. Ciamis, Jawa Barat', 'guru_bk', 'free');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ai_usage_tracking`
--
ALTER TABLE `ai_usage_tracking`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`jurusan_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`);

--
-- Indeks untuk tabel `ta`
--
ALTER TABLE `ta`
  ADD PRIMARY KEY (`ta_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ai_usage_tracking`
--
ALTER TABLE `ai_usage_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `jurusan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `ta`
--
ALTER TABLE `ta`
  MODIFY `ta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
