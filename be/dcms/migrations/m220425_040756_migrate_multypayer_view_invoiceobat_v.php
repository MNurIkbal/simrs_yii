<?php

use yii\db\Migration;

/**
 * Class m220425_040756_migrate_multypayer_view_invoiceobat_v
 */
class m220425_040756_migrate_multypayer_view_invoiceobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoiceobat_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoiceobat_v" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
                pembayaranpelayanan_t.no_pembayaran,
                pembayaranpelayanan_t.tgl_pembayaran,
                penjualanresep_t.noresep AS no_resep,
                pendaftaran_t.no_pendaftaran,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
                        ELSE penjualanresep_t.nama_pembeli
                    END AS nama_pasien,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
                        ELSE NULL::character varying
                    END AS no_rekam_medik,
                pendaftaran_t.umur,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
                        ELSE NULL::date
                    END AS tgl_lahir,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
                        ELSE NULL::character varying
                    END AS jenis_kelamin,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
                        ELSE ruangan_2.ruangan_nama
                    END AS ruangan_nama,
                dok_pendaftaran.nama_pegawai AS dok_pendaftaran, 
                dok_admisi.nama_pegawai AS dok_admisi,
                dok_resep.nama_pegawai AS dok_resep,
                pembayaranpelayanan_t.total_biayaoa AS total_tagihan_obat,
                    CASE
                        WHEN (pembayaran_t.total_discountpembayaran <> (0)::double precision) THEN \'discount RS\'::character varying
                        ELSE NULL::character varying
                    END AS komponen,
                pembayaran_t.total_discountpembayaran AS total_diskon,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjualanresep_t.created_date AS tgl_pendaftaran
               FROM (((((((((((((((pembayaranpelayanan_t
                 JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
                 JOIN obatsudahbayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id)))
                 JOIN ( SELECT obatalkespasien_t_1.obatsudahbayar_id,
                        obatalkespasien_t_1.penjualanresep_id,
                        obatalkespasien_t_1.obatalkespasien_id
                       FROM obatalkespasien_t obatalkespasien_t_1
                      WHERE (obatalkespasien_t_1.is_deleted = false)
                      GROUP BY obatalkespasien_t_1.obatsudahbayar_id, obatalkespasien_t_1.penjualanresep_id, obatalkespasien_t_1.obatalkespasien_id) obatalkespasien_t ON (((obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id) AND (obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id))))
                 JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                 LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m resep_karyawan ON ((penjualanresep_t.karyawan_id = resep_karyawan.pegawai_id)))
                 LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
                 LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
                 LEFT JOIN pegawai_m dok_resep ON ((penjualanresep_t.pegawai_id = dok_resep.pegawai_id)))
                 LEFT JOIN ruangan_m ruangan_1 ON ((pendaftaran_t.ruangan_id = ruangan_1.ruangan_id)))
                 LEFT JOIN ruangan_m ruangan_2 ON ((pasienadmisi_t.ruangan_id = ruangan_2.ruangan_id)))
                 LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN kelaspelayanan_m ON ((COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
              GROUP BY pembayaranpelayanan_t.pembayaran_id, pembayaranpelayanan_t.no_pembayaran, pembayaranpelayanan_t.tgl_pembayaran, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, penjualanresep_t.created_date,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
                        ELSE penjualanresep_t.nama_pembeli
                    END,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
                        ELSE NULL::character varying
                    END, pendaftaran_t.umur,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
                        ELSE NULL::date
                    END,
                    CASE
                        WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
                        WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
                        ELSE NULL::character varying
                    END,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
                        ELSE ruangan_2.ruangan_nama
                    END, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, dok_resep.nama_pegawai, pembayaranpelayanan_t.total_biayaoa, pembayaran_t.total_discountpembayaran;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220425_040756_migrate_multypayer_view_invoiceobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_040756_migrate_multypayer_view_invoiceobat_v cannot be reverted.\n";

        return false;
    }
    */
}
