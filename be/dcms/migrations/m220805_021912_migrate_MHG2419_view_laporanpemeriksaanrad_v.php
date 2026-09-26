<?php

use yii\db\Migration;

/**
 * Class m220805_021912_migrate_MHG2419_view_laporanpemeriksaanrad_v
 */
class m220805_021912_migrate_MHG2419_view_laporanpemeriksaanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanpemeriksaanrad_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanpemeriksaanrad_v" AS  SELECT \'NON_PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                pasien_m.tanggal_lahir, 
                pasienmasukpenunjang_t.pegawai_id,
                dokter.nama_pegawai AS dokter,
                pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                kelompokpemeriksaanrad_m.nama_kelompok,
                pemeriksaanrad_m.jenispemeriksaanrad_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.qty_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(kelaspelayanan_admisi.kelaspelayanan_nama, kelaspelayanan_pendaftaran.kelaspelayanan_nama) AS kelaspelayanan_nama,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                pemeriksaanrad_m.is_contrast,
                    CASE
                        WHEN pemeriksaanrad_m.is_contrast IS TRUE THEN \'Contrast\'::text
                        ELSE \'Non Contrast\'::text
                    END AS status_contrast,
                hasilpemeriksaanrad_t.tgl_periksa,
                pasienmasukpenunjang_t.catatan AS catatan_dokter,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                hasilpemeriksaanrad_t.tgl_verifikasi
               FROM pasienmasukpenunjang_t
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id,
                        a.kelaspelayanan_id,
                        COALESCE(pasienadmisi_t_1.pegawai_id, a.pegawai_id) AS pegawai_id
                       FROM pendaftaran_t a
                         LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                                a_1.pegawai_id
                               FROM pasienadmisi_t a_1) pasienadmisi_t_1 ON a.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.pasienmasukpenunjang_id,
                        a.daftartindakan_id,
                        a.dokterpenanggungjawab_id,
                        a.tarif_satuan,
                        a.qty_tindakan,
                        a.tarifcyto_tindakan,
                        a.tarif_tindakan,
                        a.is_deleted
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                 JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.kelompokpemeriksaanrad_id,
                        a.jenispemeriksaanrad_id,
                        a.is_contrast
                       FROM pemeriksaanrad_m a) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                 JOIN ( SELECT a.pasienmasukpenunjang_id,
                        max(a.created_date) AS tgl_periksa,
                        a.tindakanpelayanan_id,
                        max(a.tgl_verifikasi) AS tgl_verifikasi
                       FROM hasilpemeriksaanrad_t a
                      WHERE a.is_deleted IS FALSE AND a.tgl_verifikasi IS NOT NULL
                      GROUP BY a.pasienmasukpenunjang_id, a.tindakanpelayanan_id) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.catatan_dokterpengirim,
                        a.pegawai_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.kelompokpemeriksaanrad_id,
                        a.nama_kelompok
                       FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                 JOIN ( SELECT a.jenispemeriksaanrad_id,
                        a.jenispemeriksaanrad_nama
                       FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                 JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.kelaspelayanan_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_admisi ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_admisi.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
            UNION ALL
             SELECT \'PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.nama_pasien,
                pasien_m.no_rekam_medik,
                pasien_m.tanggal_lahir,
                pasienmasukpenunjang_t.pegawai_id,
                dokter.nama_pegawai AS dokter,
                pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                kelompokpemeriksaanrad_m.nama_kelompok,
                pemeriksaanrad_m.jenispemeriksaanrad_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.qty_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(kelaspelayanan_admisi.kelaspelayanan_nama, kelaspelayanan_pendaftaran.kelaspelayanan_nama) AS kelaspelayanan_nama,
                dokter_perujuk.pegawai_id AS dokter_perujuk_id,
                dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
                pemeriksaanrad_m.is_contrast,
                    CASE
                        WHEN pemeriksaanrad_m.is_contrast IS TRUE THEN \'Contrast\'::text
                        ELSE \'Non Contrast\'::text
                    END AS status_contrast,
                hasilpemeriksaanrad_t.tgl_periksa,
                pasienmasukpenunjang_t.catatan AS catatan_dokter,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                hasilpemeriksaanrad_t.tgl_verifikasi
               FROM pasienmasukpenunjang_t
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id,
                        a.kelaspelayanan_id,
                        COALESCE(pasienadmisi_t_1.pegawai_id, a.pegawai_id) AS pegawai_id
                       FROM pendaftaran_t a
                         LEFT JOIN ( SELECT a_1.pasienadmisi_id,
                                a_1.pegawai_id
                               FROM pasienadmisi_t a_1) pasienadmisi_t_1 ON a.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.pasienmasukpenunjang_id,
                        a.daftartindakan_id,
                        a.dokterpenanggungjawab_id,
                        a.tipepaket_id,
                        a.tarif_satuan,
                        a.qty_tindakan,
                        a.tarifcyto_tindakan,
                        a.tarif_tindakan,
                        a.is_deleted
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                 JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                 JOIN ( SELECT a.tipepaket_id,
                        a.tipepaket_nama
                       FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                 JOIN ( SELECT a.tipepaket_id,
                        a.daftartindakan_id
                       FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.kelompokpemeriksaanrad_id,
                        a.jenispemeriksaanrad_id,
                        a.is_contrast
                       FROM pemeriksaanrad_m a) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                 JOIN ( SELECT a.pasienmasukpenunjang_id,
                        max(a.created_date) AS tgl_periksa,
                        a.tindakanpelayanan_id,
                        max(a.tgl_verifikasi) AS tgl_verifikasi
                       FROM hasilpemeriksaanrad_t a
                      WHERE a.is_deleted IS FALSE AND a.tgl_verifikasi IS NOT NULL
                      GROUP BY a.pasienmasukpenunjang_id, a.tindakanpelayanan_id) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
                 JOIN ( SELECT a.kelompokpemeriksaanrad_id,
                        a.nama_kelompok
                       FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                 JOIN ( SELECT a.jenispemeriksaanrad_id,
                        a.jenispemeriksaanrad_nama
                       FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.catatan_dokterpengirim,
                        a.pegawai_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_pendaftaran ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_pendaftaran.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.kelaspelayanan_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_admisi ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_admisi.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienkirimkeunitlain_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220805_021912_migrate_MHG2419_view_laporanpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220805_021912_migrate_MHG2419_view_laporanpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
