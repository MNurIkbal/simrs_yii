<?php

use yii\db\Migration;

/**
 * Class m190327_080817_seeder_jabatan
 */
class m190327_080817_seeder_jabatan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
           truncate table jabatan_m restart identity;
        ");

        $this->execute("
            INSERT INTO \"public\".\"jabatan_m\"(\"jabatan_id\", \"kelompokjabatan_id\", \"indexing_id\", \"jabatan_nama\", \"jabatan_singkatan\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES 
            (1, 166, 1, 'Direktur RS', 'DIR', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (2, 166, 1, 'Wadir Pelayanan', 'WADIR', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (3, 166, 1, 'Ka. Rawat Inap/IGD', 'Kepala Ruangan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (4, 166, 1, 'Wadir Keuangan', 'Wadir Keuangan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (5, 166, 1, 'Wadir Umum dan SDM', 'Wadir Umum dan SDM', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (6, 166, 1, 'Ka. Farmalkes & Penunjang Medik', 'Ka. Farmalkes & Penunjang Medik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (7, 166, 1, 'Ka. Peng. Biaya Layanan &Support', 'Ka. Peng. Biaya Layanan &Support', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (8, 166, 1, 'Ka. SDM', 'Ka. SDM', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (9, 166, 1, 'Ka. Umum', 'Ka. Umum', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (10, 166, 1, 'BKIA', 'BKIA', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (11, 166, 1, 'Poli Umum', 'Poli Umum', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (12, 166, 1, 'Poli Spesialis', 'Poli Spesialis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (13, 166, 1, 'ICD/Pelaporan', 'ICD/Pelaporan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (14, 166, 1, 'TPP', 'TPP', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (15, 166, 1, 'Pengendalian RM', 'Pengendalian RM', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (16, 166, 1, 'Rawat Inap', 'Rawat Inap', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (17, 166, 1, 'Rawat Bersalin', 'Rawat Bersalin', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (18, 166, 1, 'OK dan CSSD', 'OK dan CSSD', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (19, 166, 1, 'Penata IGD', 'Penata IGD', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (20, 166, 1, 'Apotek', 'Apotek', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (21, 166, 1, 'Gudang Obat', 'Gudang Obat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (22, 166, 1, 'Ahli Gizi dan Juru Masak', 'Ahli Gizi dan Juru Masak', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (23, 166, 1, 'Laboratorium', 'Laboratorium', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (24, 166, 1, 'Radiologi', 'Radiologi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (25, 166, 1, 'Fisioterapi', 'Fisioterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (26, 166, 1, 'Kontroler', 'Kontroler', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (27, 166, 1, 'Aset dan Kontroler', 'Aset dan Kontroler', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (28, 166, 1, 'Utilisasi dan Verifikasi', 'Utilisasi dan Verifikasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (29, 166, 1, 'Prefen, Promotif, Komunikasi', 'Prefen, Promotif, Komunikasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (30, 166, 1, 'Kepesertaan dan Rujukan', 'Kepesertaan dan Rujukan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (31, 166, 1, 'Kasir dan Treasuri', 'Kasir dan Treasuri', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (32, 166, 1, 'Treasuri dan Tax', 'Treasuri dan Tax', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (33, 166, 1, 'Pengupahan dan Diklat', 'Pengupahan dan Diklat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (34, 166, 1, 'HIK dan Legal', 'HIK dan Legal', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (35, 166, 1, 'K3RS', 'K3RS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (36, 166, 1, 'Teknologi dan Informasi', 'Teknologi dan Informasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (37, 166, 1, 'Elektromedik', 'Elektromedik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (38, 166, 1, 'Tek. Mekanik dan Driver', 'Tek. Mekanik dan Driver', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (39, 166, 1, 'IPSRS dan IPAL', 'IPSRS dan IPAL', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (40, 166, 1, 'Loundry dan CS', 'Loundry dan CS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (41, 166, 1, 'Pemulasaran Jenazah', 'Pemulasaran Jenazah', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (42, 166, 1, 'Logistik', 'Logistik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (43, 166, 1, 'Pws Rawat Inap', 'Pws Rawat Inap', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (44, 166, 1, 'Pws IGD', 'Pws IGD', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (45, 166, 1, 'Pws Farmasi dan G. Obat', 'Pws Farmasi dan G. Obat', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (46, 166, 1, 'Pws. Laboratorium & Radiologi', 'Pws. Laboratorium & Radiologi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (47, 166, 1, 'Pws. Fisioterapi', 'Pws. Fisioterapi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (48, 166, 1, 'Pws. Kontroler', 'Pws. Kontroler', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (49, 166, 1, 'Pws. Treasuri dan TAX', 'Pws. Treasuri dan TAX', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (50, 166, 1, 'Pws. Teknologi Informasi', 'Pws. Teknologi Informasi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (51, 166, 1, 'Pws. Logistik', 'Pws. Logistik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (52, 166, 1, 'Pws. MCU', 'Pws. MCU', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (53, 166, 1, 'Pws. Gimul & Dental Lab', 'Pws. Gimul & Dental Lab', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (54, 166, 1, 'Pws. Adm Medis', 'Pws. Adm Medis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (55, 166, 1, 'Pws. Rawat Jalan', 'Pws. Rawat Jalan', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (56, 166, 1, 'Penata MCU', 'Penata MCU', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (57, 166, 1, 'Penata  Dental Lab', 'Penata  Dental Lab', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (58, 166, 1, 'Penata Gigi & Mulut', 'Penata Gigi & Mulut', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (59, 166, 1, 'Smf Gigi', 'Smf Gigi', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (60, 166, 1, 'Adm. RTK', 'Adm. RTK', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (61, 166, 1, 'sekretaris', 'sekretaris', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (62, 166, 1, 'Smf Spesialis', 'Smf Spesialis', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (63, 166, 1, 'Smf Umum', 'Smf Umum', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (64, 166, 1, 'Dokter Gigi Poliklinik', 'Dokter Gigi Poliklinik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (65, 166, 1, 'Penata Poliklinik', 'Penata Poliklinik', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_080817_seeder_jabatan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_080817_seeder_jabatan cannot be reverted.\n";

        return false;
    }
    */
}
