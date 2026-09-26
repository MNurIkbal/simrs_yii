<?php

use yii\db\Migration;

/**
 * Class m221210_231632_migrate_GB144_f_getpasienollama
 */
class m221210_231632_migrate_GB144_f_getpasienollama extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.f_getpasienollama(xtanggalawal date, xtanggalakhir date, xruangan_id integer, xjeniskelamin integer, xcarabayar_id integer, xjenisruangan integer)
        RETURNS TABLE(pasienlama integer)
        LANGUAGE plpgsql
        IMMUTABLE
       AS \$function\$
       BEGIN
       
           IF (xjenisruangan = 722 OR xjenisruangan = 724) --Poliklinik dan IGD UMUM
           THEN
               SELECT 
                   COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienlama
               FROM pendaftaranol_t
               JOIN (SELECT
                           a.pendaftaran_id,
                           a.pasien_id,
                           a.tgl_pendaftaran,
                           a.carabayar_id,
                           a.kunjungan
                       FROM pendaftaran_t a) pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               JOIN (SELECT
                           a.pasien_id,
                           a.jeniskelamin
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
               JOIN (SELECT
                           a.ruangan_id
                       FROM ruangan_m a) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
               LEFT JOIN (SELECT
                               a.pendaftaran_id
                           FROM pasienpulang_t a) pasienpulang_t ON pendaftaranol_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaranol_t.pendaftaran_id IS NOT NULL
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
               AND pasien_m.jeniskelamin::text = xjeniskelamin::text
               AND pendaftaran_t.kunjungan = '181';
           END IF;
       
           IF (xjenisruangan = 725) --Penunjang Medis
           THEN
               SELECT 
                   COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienlama
               FROM pendaftaranol_t
               JOIN (SELECT
                           a.pendaftaran_id,
                           a.pasien_id,
                           a.tgl_pendaftaran,
                           a.carabayar_id,
                           a.kunjungan
                       FROM pendaftaran_t a) pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               JOIN (SELECT
                           a.pasien_id,
                           a.jeniskelamin
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
               JOIN (SELECT
                           a.ruangan_id
                       FROM ruangan_m a) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT
                           a.pendaftaran_id,
                           a.pasienmasukpenunjang_id
                       FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaranol_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
               JOIN (SELECT
                           a.pasienmasukpenunjang_id,
                           a.tindakansudahbayar_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaranol_t.pendaftaran_id IS NOT NULL
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
               AND pasien_m.jeniskelamin::text = xjeniskelamin::text
               AND tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL
               AND pendaftaran_t.kunjungan = '181'
               AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
           END IF;
       
           IF (xjenisruangan = 723) --MCU
           THEN
               SELECT 
                   COUNT(pendaftaranol_t.pendaftaran_id) INTO pasienlama
               FROM pendaftaranol_t
               JOIN (SELECT
                           a.pendaftaran_id,
                           a.pasien_id,
                           a.tgl_pendaftaran,
                           a.carabayar_id,
                           a.kunjungan
                       FROM pendaftaran_t a) pendaftaran_t ON pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               JOIN (SELECT
                           a.pasien_id,
                           a.jeniskelamin
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
               JOIN (SELECT
                           a.ruangan_id
                       FROM ruangan_m a) ruangan_m ON pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaranol_t.pendaftaran_id IS NOT NULL
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
               AND pendaftaran_t.kunjungan = '181'
               AND pasien_m.jeniskelamin::text = xjeniskelamin::text;
           END IF;
       
       RETURN NEXT;
       
       END
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221210_231632_migrate_GB144_f_getpasienollama cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221210_231632_migrate_GB144_f_getpasienollama cannot be reverted.\n";

        return false;
    }
    */
}
