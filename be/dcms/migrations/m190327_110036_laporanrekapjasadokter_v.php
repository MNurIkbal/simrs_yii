<?php

use yii\db\Migration;

/**
 * Class m190327_110036_laporanrekapjasadokter_v
 */
class m190327_110036_laporanrekapjasadokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW laporanrekapjasadokter_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW laporanrekapjasadokter_v AS 
             SELECT gabung.pendaftaran_id,
                gabung.pasienadmisi_id,
                gabung.no_pendaftaran,
                gabung.tindakanpelayanan_id,
                gabung.tgl_tindakan,
                gabung.dokterpenanggungjawab_id,
                gabung.nama_pegawai,
                gabung.pasien_id,
                gabung.no_rekam_medik,
                gabung.nama_pasien,
                gabung.daftartindakan_id,
                gabung.daftartindakan_nama,
                gabung.tarif_tindakankomp,
                gabung.komponentarif_nama
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasienadmisi_id,
                        pendaftaran_t.no_pendaftaran,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pegawai_m.nama_pegawai,
                        pendaftaran_t.pasien_id,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        tindakanpelayanan_t.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        tindakankomponen_t.tarif_tindakankomp,
                        komponentarif_m.komponentarif_nama
                       FROM pendaftaran_t
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id AND (tindakankomponen_t.komponentarif_id = ANY (ARRAY[5, 10, 11, 12, 31]))
                         JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                         LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                         JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.pasienadmisi_id,
                        pendaftaran_t.no_pendaftaran,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        tindakanpelayanan_t.tgl_tindakan,
                        tindakanpelayanan_t.dokterpenanggungjawab_id,
                        pegawai_m.nama_pegawai,
                        pendaftaran_t.pasien_id,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        tindakanpelayanan_t.tipepaket_id,
                        tipepaket_m.tipepaket_nama,
                        tindakankomponen_t.tarif_tindakankomp,
                        komponentarif_m.komponentarif_nama
                       FROM pendaftaran_t
                         JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                         JOIN tindakankomponen_t ON tindakanpelayanan_t.tindakanpelayanan_id = tindakankomponen_t.tindakanpelayanan_id AND (tindakankomponen_t.komponentarif_id = ANY (ARRAY[5, 10, 11, 12, 31]))
                         JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
                         LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                         JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id) gabung;
        ');

        $this->execute('
            ALTER TABLE laporanrekapjasadokter_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_110036_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_110036_laporanrekapjasadokter_v cannot be reverted.\n";

        return false;
    }
    */
}
