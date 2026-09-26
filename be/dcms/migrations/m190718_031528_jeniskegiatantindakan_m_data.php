<?php

use yii\db\Migration;

/**
 * Class m190718_031528_jeniskegiatantindakan_m_data
 */
class m190718_031528_jeniskegiatantindakan_m_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          TRUNCATE TABLE jeniskegiatantindakan_m RESTART IDENTITY;
        ');

          $this->execute("
          INSERT INTO \"public\".\"jeniskegiatantindakan_m\"(\"jeniskegiatantindakan_id\", \"jeniskegiatantindakan_kode\", \"jeniskegiatantindakan_nama\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\", \"jeniskegiatan_namalainnya\", \"jeniskegiatan_keterangan\") VALUES 
(1, 'JK001', 'Bedah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Bedah', NULL),
(2, 'JK002', 'Tumpatan Gigi Sulung', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Tumpatan Gigi Sulung', NULL),
(3, 'JK003', 'Pengobatan Pulpa', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pengobatan Pulpa', NULL),
(4, 'JK004', 'Pencabutan Gigi Tetap', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pencabutan Gigi Tetap', NULL),
(5, 'JK005', 'Pencabutan Gigi Sulung', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pencabutan Gigi Sulung', NULL),
(6, 'JK006', 'Pengobatan Periodontal', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pengobatan Periodontal', NULL),
(7, 'JK007', 'Pengobatan Abses', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pengobatan Abses', NULL),
(8, 'JK008', 'Pembersihan Karang Gigi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pembersihan Karang Gigi', NULL),
(9, 'JK009', 'Prothese Lengkap', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Prothese Lengkap', NULL),
(10, 'JK010', 'Prothese Sebagian', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Prothese Sebagian', NULL),
(11, 'JK011', 'Prothese Cekat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Prothese Cekat', NULL),
(12, 'JK012', 'Orthodonti', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Orthodonti', NULL),
(13, 'JK013', 'Jacket/Bridge', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Jacket/Bridge', NULL),
(14, 'JK014', 'Bedah Mulut', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Bedah Mulut', NULL),
(15, 'JK015', 'Persalinan Normal', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Persalinan Normal', NULL),
(16, 'JK016', 'Perd sbl Persalinan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Perd sbl Persalinan', NULL),
(17, 'JK017', 'Perd sdh Persalinan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Perd sdh Persalinan', NULL),
(18, 'JK018', 'Pre Eclampsi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pre Eclampsi', NULL),
(19, 'JK019', 'Eclampsi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Eclampsi', NULL),
(20, 'JK020', 'Infeksi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Infeksi', NULL),
(21, 'JK021', 'Lain - Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain - Lain', NULL),
(22, 'JK022', 'Sectio caesaria', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Sectio caesaria', NULL),
(23, 'JK023', 'Abortus', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Abortus', NULL),
(24, 'JK024', 'Imunisasi - TT1', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Imunisasi - TT1', NULL),
(25, 'JK025', 'Imunisasi - TT2', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Imunisasi - TT2', NULL),
(26, 'JK026', 'Foto tanpa bahan kontras', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Foto tanpa bahan kontras', NULL),
(27, 'JK027', 'Foto dengan bahan kontras', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Foto dengan bahan kontras', NULL),
(28, 'JK028', 'Foto dengan rol film', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Foto dengan rol film', NULL),
(29, 'JK029', 'Flouroskopi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Flouroskopi', NULL),
(30, 'JK030', 'Foto Gigi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Foto Gigi', NULL),
(31, 'JK031', 'C.T. Scan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'C.T. Scan', NULL),
(32, 'JK032', 'Lymphografi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lymphografi', NULL),
(33, 'JK033', 'Angiograpi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Angiograpi', NULL),
(34, 'JK034', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(35, 'JK035', 'Jumlah Kegiatan Radiotherapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Jumlah Kegiatan Radiotherapi', NULL),
(36, 'JK036', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(37, 'JK037', 'Jumlah Kegiatan Diagnostik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Jumlah Kegiatan Diagnostik', NULL),
(38, 'JK038', 'Jumlah Kegiatan Therapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Jumlah Kegiatan Therapi', NULL),
(39, 'JK039', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(40, 'JK040', 'USG', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'USG', NULL),
(41, 'JK041', 'MRI', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'MRI', NULL),
(42, 'JK042', 'Lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-lain', NULL),
(43, 'JK043', 'Medis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Medis', NULL),
(44, 'JK044', 'Gait Analyzer', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Gait Analyzer', NULL),
(45, 'JK045', 'E M G', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'E M G', NULL),
(46, 'JK046', 'Uro Dinamic', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Uro Dinamic', NULL),
(47, 'JK047', 'Side Back', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Side Back', NULL),
(48, 'JK048', 'E N Tree', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'E N Tree', NULL),
(49, 'JK049', 'Spyrometer', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Spyrometer', NULL),
(50, 'JK050', 'Static Bicycle', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Static Bicycle', NULL),
(51, 'JK051', 'Tread Mill', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Tread Mill', NULL),
(52, 'JK052', 'Body Platysmograf', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Body Platysmograf', NULL),
(53, 'JK053', 'lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'lain-lain', NULL),
(54, 'JK054', 'Fisioterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Fisioterapi', NULL),
(55, 'JK055', 'Latihan Fisik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Latihan Fisik', NULL),
(56, 'JK056', 'Aktinoterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Aktinoterapi', NULL),
(57, 'JK057', 'Elektroterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Elektroterapi', NULL),
(58, 'JK058', 'Hidroterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Hidroterapi', NULL),
(59, 'JK059', 'Traksi Lumbal & Cervical', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Traksi Lumbal & Cervical', NULL),
(60, 'JK060', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(61, 'JK061', 'Okupasiterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Okupasiterapi', NULL),
(62, 'JK062', 'Snoosien Room', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Snoosien Room', NULL),
(63, 'JK063', 'Sensori Integrasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Sensori Integrasi', NULL),
(64, 'JK064', 'Latihan aktivitas kehidupan sehari-hari', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Latihan aktivitas kehidupan sehari-hari', NULL),
(65, 'JK065', 'Proper Body Mekanik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Proper Body Mekanik', NULL),
(66, 'JK066', 'Pembuatan Alat Lontar & Adaptasi Alat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pembuatan Alat Lontar & Adaptasi Alat', NULL),
(67, 'JK067', 'Analisa Persiapan Kerja', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Analisa Persiapan Kerja', NULL),
(68, 'JK068', 'Latihan Relaksasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Latihan Relaksasi', NULL),
(69, 'JK069', 'Analisa & Intervensi, Persepsi, Kognitif, Psikomot', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Analisa & Intervensi, Persepsi, Kognitif, Psikomot', NULL),
(70, 'JK070', 'Lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-lain', NULL),
(71, 'JK071', 'Terapi Wicara', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Terapi Wicara', NULL),
(72, 'JK072', 'Fungsi Bicara', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Fungsi Bicara', NULL),
(73, 'JK073', 'Fungsi Bahasa / Laku', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Fungsi Bahasa / Laku', NULL),
(74, 'JK074', 'Fungsi Menelan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Fungsi Menelan', NULL),
(75, 'JK075', 'Lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-lain', NULL),
(76, 'JK076', 'Psikologi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Psikologi', NULL),
(77, 'JK077', 'Psikolog Anak', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Psikolog Anak', NULL),
(78, 'JK078', 'Psikolog Dewasa', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Psikolog Dewasa', NULL),
(79, 'JK079', 'Lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-lain', NULL),
(80, 'JK080', 'Sosial Medis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Sosial Medis', NULL),
(81, 'JK081', 'Evaluasi Lingkungan Rumah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Evaluasi Lingkungan Rumah', NULL),
(82, 'JK082', 'Evaluasi Ekonomi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Evaluasi Ekonomi', NULL),
(83, 'JK083', 'Evaluasi Pekerjaan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Evaluasi Pekerjaan', NULL),
(84, 'JK084', 'Lain-lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-lain', NULL),
(85, 'JK085', 'Ortotik Prostetik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Ortotik Prostetik', NULL),
(86, 'JK086', 'Pembuatan Alat Bantu', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pembuatan Alat Bantu', NULL),
(87, 'JK087', 'Pembuatan Alat Anggota Tiruan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pembuatan Alat Anggota Tiruan', NULL),
(88, 'JK088', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(89, 'JK089', 'Kunjungan Rumah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Kunjungan Rumah', NULL),
(90, 'JK090', 'Elektro Encephalografi (EEG)', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Elektro Encephalografi (EEG)', NULL),
(91, 'JK091', 'Elektro Kardiographi (EKG)', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Elektro Kardiographi (EKG)', NULL),
(92, 'JK092', 'Elektro Myographi (EMG)', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Elektro Myographi (EMG)', NULL),
(93, 'JK093', 'Echo Cardiographi (ECG)', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Echo Cardiographi (ECG)', NULL),
(94, 'JK094', 'Endoskopi (semua bentuk)', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Endoskopi (semua bentuk)', NULL),
(95, 'JK095', 'Hemodialisa', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Hemodialisa', NULL),
(96, 'JK096', 'Densometri Tulang', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Densometri Tulang', NULL),
(97, 'JK097', 'Koreksi Fraktur/Dislokasi non Bedah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Koreksi Fraktur/Dislokasi non Bedah', NULL),
(98, 'JK098', 'Pungsi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Pungsi', NULL),
(99, 'JK099', 'Spirometri', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Spirometri', NULL),
(100, 'JK100', 'Tes Kulit/Alergi/Histamin', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Tes Kulit/Alergi/Histamin', NULL),
(101, 'JK101', 'Topometri', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Topometri', NULL),
(102, 'JK102', 'Tredmill/ Exercise Test', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Tredmill/ Exercise Test', NULL),
(103, 'JK103', 'Akupuntur', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Akupuntur', NULL),
(104, 'JK104', 'Hiperbarik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Hiperbarik', NULL),
(105, 'JK105', 'Herbal / jamu', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Herbal / jamu', NULL),
(106, 'JK106', 'Lain-Lain', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Lain-Lain', NULL),
(107, 'JK107', 'Psikotes', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Psikotes', NULL),
(108, 'JK108', 'Konsultasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Konsultasi', NULL),
(109, 'JK109', 'Terapi Medikamentosa', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Terapi Medikamentosa', NULL),
(110, 'JK110', 'Elektro Medik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Elektro Medik', NULL),
(111, 'JK111', 'Psikoterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Psikoterapi', NULL),
(112, 'JK112', 'Play Therapy', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Play Therapy', NULL),
(113, 'JK113', 'Rehabilitasi Medik Psikiatrik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL, 'Rehabilitasi Medik Psikiatrik', NULL);
");

    $this->execute("SELECT setval('public.jeniskegiatantindakan_m_jeniskegiatantindakan_id_seq', (SELECT COALESCE(MAX(jeniskegiatantindakan_id) ,1)+1 FROM jeniskegiatantindakan_m), false);");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_031528_jeniskegiatantindakan_m_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_031528_jeniskegiatantindakan_m_data cannot be reverted.\n";

        return false;
    }
    */
}
