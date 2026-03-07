-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 18, 2025 at 10:24 PM
-- Server version: 9.4.0
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kepoin`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_usage_tracking`
--

CREATE TABLE `ai_usage_tracking` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `tier` enum('free','pro','enterprise') DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `request_count` int DEFAULT '1',
  `usage_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `ai_usage_tracking`
--

INSERT INTO `ai_usage_tracking` (`id`, `user_id`, `tier`, `provider`, `request_count`, `usage_date`, `created_at`) VALUES
(1, 94, 'free', 'gemini-flash', 1, '2025-10-08', '2025-10-08 01:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `setting_type` enum('text','textarea','file','number') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'text',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'SiS_BK', 'text', 'Nama Aplikasi', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(2, 'app_description', 'Sistem Informasi Administrasi BK', 'textarea', 'Deskripsi Aplikasi', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(3, 'app_logo', 'logo.png', 'file', 'Logo Aplikasi (untuk header)', '2025-09-29 01:54:59', '2025-09-29 02:32:46'),
(4, 'app_favicon', 'favicon.png', 'file', 'Favicon Aplikasi', '2025-09-29 01:54:59', '2025-12-18 15:18:53'),
(5, 'login_logo', 'login_logo.png', 'file', 'Logo untuk halaman login', '2025-09-29 01:54:59', '2025-09-29 02:32:46'),
(6, 'app_version', '2.5.0', 'text', 'Versi Aplikasi', '2025-09-29 01:54:59', '2025-10-07 12:54:23'),
(7, 'app_author', 'Azka Coding', 'text', 'Nama Institusi', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(8, 'app_email', 'cloudeduinterfi@gmail.com', 'text', 'Email Kontak', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(9, 'app_phone', '085785377790', 'text', 'Nomor Telepon', '2025-09-29 01:54:59', '2025-12-18 15:05:12'),
(10, 'app_address', '', 'textarea', 'Alamat Institusi', '2025-09-29 01:54:59', '2025-09-29 01:54:59'),
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
-- Table structure for table `audit_kasus`
--

CREATE TABLE `audit_kasus` (
  `audit_id` int NOT NULL,
  `kasus_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` enum('CREATE','READ','UPDATE','DELETE') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_kunjungan`
--

CREATE TABLE `audit_kunjungan` (
  `audit_id` int NOT NULL,
  `kunjungan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` enum('CREATE','READ','UPDATE','DELETE') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_siswa`
--

CREATE TABLE `feedback_siswa` (
  `feedback_id` int NOT NULL,
  `jurnal_id` int NOT NULL,
  `kasus_id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `feedback_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback_siswa`
--

INSERT INTO `feedback_siswa` (`feedback_id`, `jurnal_id`, `kasus_id`, `siswa_id`, `feedback_text`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'masih tetap tidak bisa, apa akrena saysa lapar?', 1, '2025-12-18 18:22:34', '2025-12-18 19:03:49'),
(2, 2, 1, 5, 'oke terimakasih bapak', 1, '2025-12-18 18:59:30', '2025-12-18 19:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `guru_bk`
--

CREATE TABLE `guru_bk` (
  `guru_bk_id` int NOT NULL,
  `nama_guru_bk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip_guru_bk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_guru_bk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon_guru_bk` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_guru_bk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `jabatan_guru_bk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Guru BK',
  `status_guru_bk` enum('Aktif','Tidak Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `user_id` int DEFAULT NULL COMMENT 'Link ke tabel user untuk login',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guru_bk`
--

INSERT INTO `guru_bk` (`guru_bk_id`, `nama_guru_bk`, `nip_guru_bk`, `email_guru_bk`, `telepon_guru_bk`, `alamat_guru_bk`, `jabatan_guru_bk`, `status_guru_bk`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Azka BK ', '3242234', 'azkabk@sisbk.com', '0123456789', 'Jawa Timur Indonesia', 'Guru BK', 'Aktif', 14, '2025-12-18 17:13:13', '2025-12-18 17:13:13');

-- --------------------------------------------------------

--
-- Table structure for table `input_pelanggaran`
--

CREATE TABLE `input_pelanggaran` (
  `id` int NOT NULL,
  `waktu` datetime NOT NULL,
  `siswa` int NOT NULL,
  `kelas` int NOT NULL,
  `pelanggaran` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `input_pelanggaran`
--

INSERT INTO `input_pelanggaran` (`id`, `waktu`, `siswa`, `kelas`, `pelanggaran`) VALUES
(1, '2025-12-18 00:17:00', 1, 8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `input_prestasi`
--

CREATE TABLE `input_prestasi` (
  `id` int NOT NULL,
  `waktu` datetime NOT NULL,
  `siswa` int NOT NULL,
  `kelas` int NOT NULL,
  `prestasi` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `input_prestasi`
--

INSERT INTO `input_prestasi` (`id`, `waktu`, `siswa`, `kelas`, `prestasi`) VALUES
(1, '2025-12-18 00:15:00', 6, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_kasus`
--

CREATE TABLE `jurnal_kasus` (
  `jurnal_id` int NOT NULL,
  `kasus_id` int NOT NULL,
  `tanggal_konseling` date NOT NULL,
  `bentuk_layanan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uraian_sesi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `analisis_diagnosis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tindakan_intervensi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rencana_tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lampiran_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal_kasus`
--

INSERT INTO `jurnal_kasus` (`jurnal_id`, `kasus_id`, `tanggal_konseling`, `bentuk_layanan`, `uraian_sesi`, `analisis_diagnosis`, `tindakan_intervensi`, `rencana_tindak_lanjut`, `lampiran_file`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-19', 'Konseling Individu', 'silahkan berusaha dengan membaca buku atau melakukan hal yang lain', '', '', '', '', '2025-12-18 18:11:18', '2025-12-18 18:11:18'),
(2, 1, '2025-12-19', 'Konseling Individu', 'saya rasa anda harus selalu dalam keadaan kenyang ketika sedang belajar', '', '', 'Selanjutnya adalah home visit', '', '2025-12-18 18:58:47', '2025-12-18 18:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `jurusan_id` int NOT NULL,
  `jurusan_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`jurusan_id`, `jurusan_nama`) VALUES
(1, 'Umum'),
(2, 'Teknik Industri'),
(3, 'Manajemen Perkantoran');

-- --------------------------------------------------------

--
-- Table structure for table `kasus_siswa`
--

CREATE TABLE `kasus_siswa` (
  `kasus_id` int NOT NULL,
  `kasus_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `siswa_id` int NOT NULL,
  `tanggal_pelaporan` date NOT NULL,
  `sumber_kasus` enum('Wali Kelas','Guru Mapel','Orang Tua','Inisiatif Siswa','Teman','Temuan Guru BK') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kategori_masalah` enum('Pribadi','Sosial','Belajar','Karir') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `judul_kasus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi_awal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status_kasus` enum('Baru','Dalam Proses','Selesai/Tuntas','Dirujuk/Alih Tangan Kasus') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Baru',
  `guru_bk_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kasus_siswa`
--

INSERT INTO `kasus_siswa` (`kasus_id`, `kasus_kode`, `siswa_id`, `tanggal_pelaporan`, `sumber_kasus`, `kategori_masalah`, `judul_kasus`, `deskripsi_awal`, `status_kasus`, `guru_bk_id`, `created_at`, `updated_at`) VALUES
(1, 'KS2025120001', 5, '2025-12-19', 'Inisiatif Siswa', 'Pribadi', 'Kesulitan Belajar', 'Setiap saya mau belajar selalu tidak bisa fokus', 'Dalam Proses', 1, '2025-12-18 18:10:12', '2025-12-18 18:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` int NOT NULL,
  `kelas_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas_jurusan` int NOT NULL,
  `kelas_ta` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`kelas_id`, `kelas_nama`, `kelas_jurusan`, `kelas_ta`) VALUES
(1, 'VII - 1', 1, 2),
(2, 'VIII - 1', 1, 2),
(3, 'X - TekIn', 2, 2),
(4, 'XI - TekIn', 2, 2),
(5, 'XII - TekIn', 2, 2),
(6, 'X - MP', 3, 2),
(7, 'XI - MP', 3, 2),
(8, 'XII - MP', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `kelas_siswa`
--

CREATE TABLE `kelas_siswa` (
  `ks_id` int NOT NULL,
  `ks_siswa` int NOT NULL,
  `ks_kelas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas_siswa`
--

INSERT INTO `kelas_siswa` (`ks_id`, `ks_siswa`, `ks_kelas`) VALUES
(1, 1, 8),
(2, 2, 8),
(3, 3, 8),
(4, 4, 8),
(5, 5, 8),
(6, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kunjungan_rumah`
--

CREATE TABLE `kunjungan_rumah` (
  `kunjungan_id` int NOT NULL,
  `kunjungan_kode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `siswa_id` int NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `waktu_kunjungan` time NOT NULL,
  `alamat_kunjungan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `petugas_bk_id` int NOT NULL,
  `tujuan_kunjungan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pihak_ditemui` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hasil_observasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ringkasan_wawancara` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kesimpulan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rekomendasi_tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lampiran_foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_kunjungan`
--

CREATE TABLE `lampiran_kunjungan` (
  `lampiran_id` int NOT NULL,
  `kunjungan_id` int NOT NULL,
  `nama_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `path_file` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ukuran_file` int NOT NULL,
  `tipe_file` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `layanan_bk`
--

CREATE TABLE `layanan_bk` (
  `layanan_id` int NOT NULL,
  `tanggal_pelaksanaan` date NOT NULL,
  `jenis_layanan` enum('Layanan Klasikal','Bimbingan Kelompok','Konseling Kelompok','Konsultasi','Mediasi','Layanan Advokasi','Layanan Peminatan','Lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `topik_materi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bidang_layanan` enum('Pribadi','Sosial','Belajar','Karir') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sasaran_layanan` enum('Satu Kelas','Kelompok Siswa','Individu') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `jumlah_peserta` int NOT NULL DEFAULT '0',
  `uraian_kegiatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evaluasi_proses` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evaluasi_hasil` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dibuat_oleh` int DEFAULT NULL,
  `status_layanan` enum('Aktif','Selesai','Dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Aktif',
  `lampiran_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `layanan_bk_peserta`
--

CREATE TABLE `layanan_bk_peserta` (
  `peserta_id` int NOT NULL,
  `layanan_id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi_rtl`
--

CREATE TABLE `notifikasi_rtl` (
  `notif_id` int NOT NULL,
  `jurnal_id` int NOT NULL,
  `tanggal_reminder` date NOT NULL,
  `pesan_reminder` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_reminder` enum('Belum','Sudah','Dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi_rtl`
--

INSERT INTO `notifikasi_rtl` (`notif_id`, `jurnal_id`, `tanggal_reminder`, `pesan_reminder`, `status_reminder`, `created_at`) VALUES
(1, 2, '2025-12-26', 'RTL: Selanjutnya adalah home visit...', 'Belum', '2025-12-18 18:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `pelanggaran_id` int NOT NULL,
  `pelanggaran_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pelanggaran_point` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggaran`
--

INSERT INTO `pelanggaran` (`pelanggaran_id`, `pelanggaran_nama`, `pelanggaran_point`) VALUES
(1, 'Merokok', 15),
(2, 'Terlambat Datang Ke Sekolah', 2),
(3, 'Tidak memakai atribut sekolah (Topi)', 20),
(4, 'Tidak memakai atribut sekolah (dasi)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_raport`
--

CREATE TABLE `pengaturan_raport` (
  `id` int NOT NULL,
  `nama_madrasah` varchar(255) NOT NULL,
  `jenis_institusi` enum('Sekolah','Madrasah') NOT NULL DEFAULT 'Madrasah',
  `alamat_madrasah` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `nama_kepala` varchar(255) NOT NULL,
  `nip_kepala` varchar(50) NOT NULL,
  `nama_waka` varchar(255) NOT NULL,
  `nip_waka` varchar(50) NOT NULL,
  `nama_guru_bk` varchar(255) NOT NULL DEFAULT 'Guru BK',
  `judul_raport` varchar(255) NOT NULL,
  `sub_judul` varchar(255) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan_raport`
--

INSERT INTO `pengaturan_raport` (`id`, `nama_madrasah`, `jenis_institusi`, `alamat_madrasah`, `kota`, `nama_kepala`, `nip_kepala`, `nama_waka`, `nip_waka`, `nama_guru_bk`, `judul_raport`, `sub_judul`, `logo_url`, `created_at`, `updated_at`) VALUES
(1, 'Madrasah Aliyah YASMU', 'Madrasah', 'Jl. Kyai Sahlan I No. 24 Manyarejo', 'Gresik', 'Nur Ismawati, S.Pd.', '-', 'Nurul Faridah, S.Pd', '-', 'Evi Zafifatun Nisa\'', 'LAPORAN PRESTASI DAN PELANGGARAN SISWA', 'Sistem Informasi Point Siswa', 'gambar/sistem/logo.png', '2025-09-15 05:55:02', '2025-10-06 05:54:01');

-- --------------------------------------------------------

--
-- Table structure for table `prestasi`
--

CREATE TABLE `prestasi` (
  `prestasi_id` int NOT NULL,
  `prestasi_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prestasi_point` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prestasi`
--

INSERT INTO `prestasi` (`prestasi_id`, `prestasi_nama`, `prestasi_point`) VALUES
(1, 'Juara 1 Tingkat Kecamatan', 10),
(2, 'Juara Perlombaan Tingkat Kabupaten', 10),
(3, 'Juara Perlombaan Tingkat Nasional', 30),
(4, 'Partisipasi Mengikuti Upacara 17 Agustus', 3);

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int NOT NULL,
  `siswa_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `siswa_nis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `siswa_jurusan` int NOT NULL,
  `siswa_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `siswa_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `siswa_foto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `siswa_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `siswa_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `siswa_alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `siswa_nama`, `siswa_nis`, `siswa_jurusan`, `siswa_status`, `siswa_password`, `siswa_foto`, `siswa_telepon`, `siswa_email`, `siswa_alamat`) VALUES
(1, 'Siswa MP 1', '131221', 3, 'Aktif', '3afa0d81296a4f17d477ec823261b1ec', NULL, NULL, NULL, NULL),
(2, 'Siswa MP 2', '131222', 3, 'Aktif', '3afa0d81296a4f17d477ec823261b1ec', NULL, NULL, NULL, NULL),
(3, 'Siswa MP 3', '131223', 3, 'Aktif', '3afa0d81296a4f17d477ec823261b1ec', NULL, NULL, NULL, NULL),
(4, 'Siswa MP 4', '131224', 3, 'Aktif', '3afa0d81296a4f17d477ec823261b1ec', NULL, NULL, NULL, NULL),
(5, 'Siswa MP 5', '131225', 3, 'Aktif', '3afa0d81296a4f17d477ec823261b1ec', 'siswa_5_1766096664.png', NULL, NULL, NULL),
(6, 'Joko Tingkir', '32423433', 1, 'aktif', '1f5cc082506d0121b0fb53ae605ef9e9', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ta`
--

CREATE TABLE `ta` (
  `ta_id` int NOT NULL,
  `ta_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ta_status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta`
--

INSERT INTO `ta` (`ta_id`, `ta_nama`, `ta_status`) VALUES
(2, '2025/2026 ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `user_nama` varchar(100) NOT NULL,
  `user_username` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_foto` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `user_telepon` varchar(20) DEFAULT NULL,
  `user_alamat` text,
  `user_level` varchar(20) NOT NULL,
  `user_tier` enum('free','pro','enterprise') DEFAULT 'free'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_nama`, `user_username`, `user_password`, `user_foto`, `user_email`, `user_telepon`, `user_alamat`, `user_level`, `user_tier`) VALUES
(10, 'Admin Aplikasi', 'admin', '0192023a7bbd73250516f069df18b500', '922369224_azco logo.png', NULL, NULL, NULL, 'administrator', 'free'),
(14, 'Azka BK ', 'azka_bk_', '670bd7190f59d3fc1cd87afb8ce68142', 'admin_14_1766080147.png', 'azkabk@sisbk.com', '0123456789', 'Jawa Timur Indonesia', 'guru_bk', 'free');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_usage_tracking`
--
ALTER TABLE `ai_usage_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`usage_date`),
  ADD KEY `idx_tier_date` (`tier`,`usage_date`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `audit_kasus`
--
ALTER TABLE `audit_kasus`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `fk_audit_kasus` (`kasus_id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `audit_kunjungan`
--
ALTER TABLE `audit_kunjungan`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `fk_audit_kunjungan` (`kunjungan_id`),
  ADD KEY `fk_audit_user_kunjungan` (`user_id`);

--
-- Indexes for table `feedback_siswa`
--
ALTER TABLE `feedback_siswa`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_feedback_jurnal` (`jurnal_id`),
  ADD KEY `fk_feedback_kasus` (`kasus_id`),
  ADD KEY `fk_feedback_siswa` (`siswa_id`);

--
-- Indexes for table `guru_bk`
--
ALTER TABLE `guru_bk`
  ADD PRIMARY KEY (`guru_bk_id`),
  ADD UNIQUE KEY `nip_guru_bk` (`nip_guru_bk`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `input_pelanggaran`
--
ALTER TABLE `input_pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `input_prestasi`
--
ALTER TABLE `input_prestasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jurnal_kasus`
--
ALTER TABLE `jurnal_kasus`
  ADD PRIMARY KEY (`jurnal_id`),
  ADD KEY `fk_jurnal_kasus` (`kasus_id`),
  ADD KEY `idx_tanggal_konseling` (`tanggal_konseling`),
  ADD KEY `idx_kasus_id` (`kasus_id`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`jurusan_id`);

--
-- Indexes for table `kasus_siswa`
--
ALTER TABLE `kasus_siswa`
  ADD PRIMARY KEY (`kasus_id`),
  ADD UNIQUE KEY `kasus_kode` (`kasus_kode`),
  ADD KEY `fk_kasus_siswa` (`siswa_id`),
  ADD KEY `fk_kasus_guru_bk` (`guru_bk_id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`);

--
-- Indexes for table `kelas_siswa`
--
ALTER TABLE `kelas_siswa`
  ADD PRIMARY KEY (`ks_id`);

--
-- Indexes for table `kunjungan_rumah`
--
ALTER TABLE `kunjungan_rumah`
  ADD PRIMARY KEY (`kunjungan_id`),
  ADD UNIQUE KEY `kunjungan_kode` (`kunjungan_kode`),
  ADD KEY `fk_kunjungan_siswa` (`siswa_id`),
  ADD KEY `fk_kunjungan_petugas` (`petugas_bk_id`);

--
-- Indexes for table `lampiran_kunjungan`
--
ALTER TABLE `lampiran_kunjungan`
  ADD PRIMARY KEY (`lampiran_id`),
  ADD KEY `fk_lampiran_kunjungan` (`kunjungan_id`);

--
-- Indexes for table `layanan_bk`
--
ALTER TABLE `layanan_bk`
  ADD PRIMARY KEY (`layanan_id`),
  ADD KEY `idx_tanggal` (`tanggal_pelaksanaan`),
  ADD KEY `idx_jenis_layanan` (`jenis_layanan`),
  ADD KEY `idx_bidang_layanan` (`bidang_layanan`),
  ADD KEY `idx_kelas_id` (`kelas_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_dibuat_oleh` (`dibuat_oleh`),
  ADD KEY `idx_status_layanan` (`status_layanan`);

--
-- Indexes for table `layanan_bk_peserta`
--
ALTER TABLE `layanan_bk_peserta`
  ADD PRIMARY KEY (`peserta_id`),
  ADD UNIQUE KEY `unique_layanan_siswa` (`layanan_id`,`siswa_id`),
  ADD KEY `idx_layanan_id` (`layanan_id`),
  ADD KEY `idx_siswa_id` (`siswa_id`);

--
-- Indexes for table `notifikasi_rtl`
--
ALTER TABLE `notifikasi_rtl`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `fk_notif_jurnal` (`jurnal_id`);

--
-- Indexes for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`pelanggaran_id`);

--
-- Indexes for table `pengaturan_raport`
--
ALTER TABLE `pengaturan_raport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`prestasi_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswa_id`);

--
-- Indexes for table `ta`
--
ALTER TABLE `ta`
  ADD PRIMARY KEY (`ta_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_usage_tracking`
--
ALTER TABLE `ai_usage_tracking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `audit_kasus`
--
ALTER TABLE `audit_kasus`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_kunjungan`
--
ALTER TABLE `audit_kunjungan`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_siswa`
--
ALTER TABLE `feedback_siswa`
  MODIFY `feedback_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guru_bk`
--
ALTER TABLE `guru_bk`
  MODIFY `guru_bk_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `input_pelanggaran`
--
ALTER TABLE `input_pelanggaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `input_prestasi`
--
ALTER TABLE `input_prestasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jurnal_kasus`
--
ALTER TABLE `jurnal_kasus`
  MODIFY `jurnal_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `jurusan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kasus_siswa`
--
ALTER TABLE `kasus_siswa`
  MODIFY `kasus_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kelas_siswa`
--
ALTER TABLE `kelas_siswa`
  MODIFY `ks_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kunjungan_rumah`
--
ALTER TABLE `kunjungan_rumah`
  MODIFY `kunjungan_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lampiran_kunjungan`
--
ALTER TABLE `lampiran_kunjungan`
  MODIFY `lampiran_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `layanan_bk`
--
ALTER TABLE `layanan_bk`
  MODIFY `layanan_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `layanan_bk_peserta`
--
ALTER TABLE `layanan_bk_peserta`
  MODIFY `peserta_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi_rtl`
--
ALTER TABLE `notifikasi_rtl`
  MODIFY `notif_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `pelanggaran_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengaturan_raport`
--
ALTER TABLE `pengaturan_raport`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `prestasi_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ta`
--
ALTER TABLE `ta`
  MODIFY `ta_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_kunjungan`
--
ALTER TABLE `audit_kunjungan`
  ADD CONSTRAINT `fk_audit_kunjungan` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_rumah` (`kunjungan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_audit_user_kunjungan` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_siswa`
--
ALTER TABLE `feedback_siswa`
  ADD CONSTRAINT `fk_feedback_jurnal` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal_kasus` (`jurnal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_kasus` FOREIGN KEY (`kasus_id`) REFERENCES `kasus_siswa` (`kasus_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_feedback_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guru_bk`
--
ALTER TABLE `guru_bk`
  ADD CONSTRAINT `fk_guru_bk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `kunjungan_rumah`
--
ALTER TABLE `kunjungan_rumah`
  ADD CONSTRAINT `fk_kunjungan_petugas` FOREIGN KEY (`petugas_bk_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kunjungan_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE;

--
-- Constraints for table `lampiran_kunjungan`
--
ALTER TABLE `lampiran_kunjungan`
  ADD CONSTRAINT `fk_lampiran_kunjungan` FOREIGN KEY (`kunjungan_id`) REFERENCES `kunjungan_rumah` (`kunjungan_id`) ON DELETE CASCADE;

--
-- Constraints for table `layanan_bk`
--
ALTER TABLE `layanan_bk`
  ADD CONSTRAINT `fk_layanan_bk_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_layanan_bk_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `layanan_bk_peserta`
--
ALTER TABLE `layanan_bk_peserta`
  ADD CONSTRAINT `fk_layanan_peserta_layanan` FOREIGN KEY (`layanan_id`) REFERENCES `layanan_bk` (`layanan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_layanan_peserta_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
