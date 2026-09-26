<?php

use yii\db\Migration;

/**
 * Class m230818_093043_migrate_RPP545_view_laporanjasamedis_new_v
 */
class m230818_093043_migrate_RPP545_view_laporanjasamedis_new_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS laporanjasamedis_new_v;
        ');

        $this->execute('
            CREATE VIEW "public"."laporanjasamedis_new_v" AS  SELECT data.tipe AS "Jenis",
    data.no_rekam_medik AS "No RM",
    data.tgl_pulang::date AS "Tanggal Billing",
    data.tgl_masuk::date AS "Tanggal Masuk",
    data.tgl_pulang::date AS "Tanggal Pulang",
    data.penjamin_nama AS "Penjamin",
    data.nama_pasien AS "Nama Pasien",
    data.hak_kelas AS "Hak Kelas",
    data.nama_tindakan AS "Nama Tindakan",
    data.dokter AS "Dokter", 
    data.qty AS "Qty",
    data.tarif_satuan AS "Tarif Satuan",
    COALESCE(data.diskon, 0::double precision) AS "Diskon",
    data.tarif_total AS "Total"
   FROM ( SELECT
                CASE
                    WHEN pembayaran_t.pasienadmisi_id IS NULL THEN \'RJRD\'::text
                    ELSE \'RI\'::text
                END AS tipe,
            pasien_m.no_rekam_medik,
            COALESCE(pasienadmisi_t.tgl_admisi, pendaftaran_t.tgl_pendaftaran) AS tgl_masuk,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.tgl_pendaftaran
                    ELSE NULL::timestamp without time zone
                END AS tgl_pulang,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin_nama,
            pasien_m.nama_pasien,
            COALESCE(COALESCE(bpjs_ri.klsrawat::character varying, bpjs_rjrd.klsrawat::character varying), COALESCE(kelas_ri.kelaspelayanan_nama, kelas_rjrd.kelaspelayanan_nama)) AS hak_kelas,
            tagihan.nama_tindakan,
            tagihan.dokter,
            tagihan.qty,
            tagihan.tarif_satuan,
            tagihan.diskon,
            tagihan.tarif_total
           FROM pembayaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    a.kelaspelayanan_id,
                    a.penjamin_id,
                    a.bpjs_id,
                    a.pasienpulang_id
                   FROM pendaftaran_t a
                  WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 628])) AND a.pasienbatalperiksa_id IS NULL) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.tgl_admisi,
                    a.kelaspelayanan_id,
                    a.penjamin_id,
                    a.bpjs_id,
                    a.pasienpulang_id
                   FROM pasienadmisi_t a
                  WHERE a.status_ranap <> 453 AND a.pasienbatalperiksa_id IS NULL) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_rjrd ON pendaftaran_t.kelaspelayanan_id = kelas_rjrd.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ri ON pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_rjrd ON pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat
                   FROM bpjs_t a) bpjs_rjrd ON pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.klsrawat
                   FROM bpjs_t a) bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a
                  WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a
                  WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama) AS nama_tindakan,
                    pegawai_m.nama_pegawai AS dokter,
                    a.qty_tindakan AS qty,
                    a.tarif_satuan,
                    a.tarif_diskon AS diskon,
                    a.tarif_tindakan AS tarif_total
                   FROM tindakanpelayanan_t a
                     LEFT JOIN ( SELECT b.daftartindakan_id,
                            b.daftartindakan_nama
                           FROM daftartindakan_m b) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     LEFT JOIN ( SELECT c.tipepaket_id,
                            c.tipepaket_nama
                           FROM tipepaket_m c) tipepaket_m ON a.tipepaket_id = tipepaket_m.tipepaket_id
                     LEFT JOIN ( SELECT d.pegawai_id,
                            d.nama_pegawai
                           FROM pegawai_m d) pegawai_m ON a.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                  WHERE a.is_deleted = false) tagihan ON pembayaran_t.pembayaran_id = tagihan.pembayaran_id
          WHERE pembayaran_t.is_deleted = false) data
  ORDER BY data.tgl_pulang, data.nama_tindakan;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230818_093043_migrate_RPP545_view_laporanjasamedis_new_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230818_093043_migrate_RPP545_view_laporanjasamedis_new_v cannot be reverted.\n";

        return false;
    }
    */
}
