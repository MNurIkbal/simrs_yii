<?php

use yii\db\Migration;

/**
 * Class m220324_155417_migrate_skema_fisio_lookuptransaksi_m
 */
class m220324_155417_migrate_skema_fisio_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'ruang_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('ruang_fisio', 935, 'Ruang Fisioterapi', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'kategori_tindakan_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('kategori_tindakan_fisio', 86, 'Kategori Fisio', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'kelompok_tindakan_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('kelompok_tindakan_fisio', 56, 'Kelompok Fisio', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'status_batal_program_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('status_batal_program_fisio', 1172, 'Status ID Batal Program Fisioterapi', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'status_close_program_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('status_close_program_fisio', 1170, 'Status ID Close Program Fisioterapi', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'status_open_program_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('status_open_program_fisio', 1171, 'Status ID Open Program Fisioterapi', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'kelompok_pemeriksaan_fisio_default';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('kelompok_pemeriksaan_fisio_default', 1, 'Default Pemeriksaan Fisio', NULL, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'ruang_fisio';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('ruang_fisio', 935, 'Ruang Fisioterapi', NULL, NULL, NULL);
            ");


        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'tipe_instalasi';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('tipe_instalasi', 1, 'Memisahkan Instalasi RJ', 'RJ', NULL, NULL);
            ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('tipe_instalasi', 3, 'Memisahkan Instalasi RI', 'RI', NULL, NULL);
            ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('tipe_instalasi', 7, 'Memisahkan Instalasi FISIO', 'FIS', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'status_periksa_pulang';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('status_periksa_pulang', 4, 'Status Periksa Pulang', NULL, NULL, NULL);
            ");

        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "expired_time_program_fisio" int4,
          ADD COLUMN IF NOT EXISTS "is_expired_time_program_fisio" bool DEFAULT false;
          ');

        $this->execute('
            COMMENT ON COLUMN "public"."konfigsystem_k"."expired_time_program_fisio" IS \'Satuan Hari\';
            ');

        $this->execute('
            COMMENT ON COLUMN "public"."konfigsystem_k"."is_expired_time_program_fisio" IS \'untuk menentukan penggunaan expired beda rs\';
            ');

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'konfig_fisio_maks_frekuensi_non_paket';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfig_fisio_maks_frekuensi_non_paket', 0, 'Maks Non Paket Frekuensi', 1, NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'countdown_expire_program_fisioterapi';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('countdown_expire_program_fisioterapi', 1, 'konfigsystem_id on konfigsystem_k', NULL, NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_155417_migrate_skema_fisio_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_155417_migrate_skema_fisio_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
