<?php

use yii\db\Migration;

/**
 * Class m230818_093058_migrate_RPP545_view_laporandatajurnal_new_v
 */
class m230818_093058_migrate_RPP545_view_laporandatajurnal_new_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS laporandatajurnal_new_v;
        ');

        $this->execute('
            CREATE VIEW "public"."laporandatajurnal_new_v" AS  SELECT data.tipe AS "Jenis",
    data.tgl_keluar::date AS "Tanggal Billing",
    data.tgl_omset::date AS "Tanggal Omset",
    data.no_rekam_medik AS "No RM",
    data.penjamin_nama AS "Penjamin",
    data.nama_pasien AS "Nama Pasien",
    data.tgl_masuk::date AS "Tanggal Masuk",
    data.tgl_keluar::date AS "Tanggal Keluar",
    data.ruangan_nama AS "Ruangan",
    data.nama_tindakan_obat AS "Nama Tindakan",
    data.ruangan_tindakan_obat AS "Ruangan Tindakan",
    data.tarif_total AS "Tarif"
   FROM ( SELECT 
                CASE
                    WHEN pembayaran_t.pasienadmisi_id IS NULL THEN \'RJRD\'::text
                    ELSE \'RI\'::text
                END AS tipe,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL AND pulang_ri.tglpasienpulang::time without time zone <= \'07:00:00\'::time without time zone THEN (pulang_ri.tglpasienpulang::date - 1)::timestamp without time zone
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.tgl_pendaftaran::time without time zone <= \'07:00:00\'::time without time zone THEN (pendaftaran_t.tgl_pendaftaran::date - 1)::timestamp without time zone
                    ELSE COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_pendaftaran)
                END AS tgl_omset,
            pasien_m.no_rekam_medik,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin_nama,
            pasien_m.nama_pasien,
            COALESCE(pasienadmisi_t.tgl_admisi, pendaftaran_t.tgl_pendaftaran) AS tgl_masuk,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.tgl_pendaftaran
                    ELSE NULL::timestamp without time zone
                END AS tgl_keluar,
            COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan_nama,
            tagihan.nama_tindakan_obat,
            tagihan.ruangan_tindakan_obat,
            tagihan.tarif_total
           FROM pembayaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.penjamin_id
                   FROM pendaftaran_t a
                  WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 628])) AND a.pasienbatalperiksa_id IS NULL) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.tgl_admisi,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.penjamin_id
                   FROM pasienadmisi_t a
                  WHERE a.status_ranap <> 453 AND a.pasienbatalperiksa_id IS NULL) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
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
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_rjrd ON pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a
                  WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a
                  WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pembayaran_id,
                        CASE
                            WHEN daftartindakan_m.kelompoktindakan_id = ANY (ARRAY[6, 18, 28, 37]) THEN concat(COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama), \' - \', pegawai_m.nama_pegawai)::character varying
                            WHEN daftartindakan_m.is_akomodasi = true THEN concat(COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama), \' - \', ruangan_m.ruangan_nama)::character varying
                            ELSE COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama)
                        END AS nama_tindakan_obat,
                    ruangan_m.ruangan_nama AS ruangan_tindakan_obat,
                        CASE
                            WHEN a.parent_id IS NOT NULL AND a.tarif_tindakan = 0::double precision THEN a.tarif_satuan
                            WHEN a.tipepaket_id IS NOT NULL THEN 0::double precision
                            ELSE a.tarif_tindakan
                        END AS tarif_total
                   FROM tindakanpelayanan_t a
                     LEFT JOIN ( SELECT b.daftartindakan_id,
                            b.kelompoktindakan_id,
                            b.daftartindakan_nama,
                            b.is_akomodasi
                           FROM daftartindakan_m b) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT c.ruangan_id,
                            c.ruangan_nama
                           FROM ruangan_m c) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT d.tipepaket_id,
                            d.tipepaket_nama
                           FROM tipepaket_m d) tipepaket_m ON a.tipepaket_id = tipepaket_m.tipepaket_id
                     LEFT JOIN ( SELECT e.pegawai_id,
                            e.nama_pegawai
                           FROM pegawai_m e) pegawai_m ON a.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                  WHERE a.is_deleted = false
                UNION ALL
                 SELECT a.pendaftaran_id,
                    a.pembayaran_id,
                    concat(ruangan_m.ruangan_nama, \' - \', obatalkes_m.obatalkes_nama) AS nama_tindakan_obat,
                    ruangan_m.ruangan_nama AS ruangan_tindakan_obat,
                    a.hargajual_oa AS tarif_total
                   FROM obatalkespasien_t a
                     JOIN ( SELECT b.obatalkes_id,
                            b.obatalkes_nama
                           FROM obatalkes_m b) obatalkes_m ON a.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN ( SELECT c.ruangan_id,
                            c.ruangan_nama
                           FROM ruangan_m c) ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id
                  WHERE a.is_deleted = false) tagihan ON pembayaran_t.pembayaran_id = tagihan.pembayaran_id
          WHERE pembayaran_t.is_deleted = false
        UNION ALL
         SELECT \'RESEP BEBAS\'::text AS tipe,
                CASE
                    WHEN penjualanresep_t.tglpenjualan::time without time zone <= \'07:00:00\'::time without time zone THEN penjualanresep_t.tglpenjualan::date - 1
                    ELSE penjualanresep_t.tglpenjualan::date
                END AS tgl_omset,
            NULL::character varying AS no_rekam_medik,
            penjamin_m.penjamin_nama,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            penjualanresep_t.tglpenjualan AS tgl_masuk,
            penjualanresep_t.tglpenjualan AS tgl_keluar,
            ruangan_m.ruangan_nama,
            concat(ruangan_m.ruangan_nama, \' - \', obatalkes_m.obatalkes_nama) AS nama_tindakan_obat,
            ruangan_m.ruangan_nama AS ruangan_tindakan_obat,
            obatalkespasien_t.hargajual_oa AS tarif_total
           FROM pembayaran_t
             JOIN ( SELECT a.penjualanresep_id,
                    a.pembayaran_id,
                    a.obatalkes_id,
                    a.ruangan_id,
                    a.hargajual_oa
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false) obatalkespasien_t ON pembayaran_t.pembayaran_id = obatalkespasien_t.pembayaran_id
             JOIN ( SELECT a.penjualanresep_id,
                    a.tglpenjualan,
                    a.penjamin_id,
                    a.nama_pembeli
                   FROM penjualanresep_t a
                  WHERE a.jenispenjualan::integer = 343) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
        UNION ALL
         SELECT
                CASE
                    WHEN pembayaran_t.pasienadmisi_id IS NULL THEN \'RJRD\'::text
                    ELSE \'RI\'::text
                END AS tipe,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL AND pulang_ri.tglpasienpulang::time without time zone <= \'07:00:00\'::time without time zone THEN (pulang_ri.tglpasienpulang::date - 1)::timestamp without time zone
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.tgl_pendaftaran::time without time zone <= \'07:00:00\'::time without time zone THEN (pendaftaran_t.tgl_pendaftaran::date - 1)::timestamp without time zone
                    ELSE COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_pendaftaran)
                END AS tgl_omset,
            pasien_m.no_rekam_medik,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin_nama,
            pasien_m.nama_pasien,
            COALESCE(pasienadmisi_t.tgl_admisi, pendaftaran_t.tgl_pendaftaran) AS tgl_masuk,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.tgl_pendaftaran
                    ELSE NULL::timestamp without time zone
                END AS tgl_keluar,
            COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan_nama,
            \'JASA PELAYANAN\'::character varying AS nama_tindakan_obat,
            NULL::character varying AS ruangan_tindakan_obat,
            pembayaran_t.total_administrasi AS tarif_total
           FROM pembayaran_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.pasien_id,
                    a.tgl_pendaftaran,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.penjamin_id
                   FROM pendaftaran_t a
                  WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 628])) AND a.pasienbatalperiksa_id IS NULL) pendaftaran_t ON pembayaran_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.tgl_admisi,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.penjamin_id
                   FROM pasienadmisi_t a
                  WHERE a.status_ranap <> 453 AND a.pasienbatalperiksa_id IS NULL) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
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
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a
                  WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_rjrd ON pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
          WHERE pembayaran_t.is_deleted = false) data
  ORDER BY data.tgl_keluar, data.nama_tindakan_obat;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230818_093058_migrate_RPP545_view_laporandatajurnal_new_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230818_093058_migrate_RPP545_view_laporandatajurnal_new_v cannot be reverted.\n";

        return false;
    }
    */
}
