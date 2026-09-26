<?php

use yii\db\Migration;

/**
 * Class m220121_061625_migrate_hotfix_pengajuanklaim
 */
class m220121_061625_migrate_hotfix_pengajuanklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pengajuanklaim_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pengajuanklaim_v\" AS
            SELECT
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN 'RJ'::text
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN 'RJ'::text
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN 'RD'::text
            ELSE 'OTHER'::text
            END AS tipe,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 21]))) THEN pendaftaran_t.tgl_pendaftaran
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pulang_rjrd.tglpasienpulang
            ELSE pulang_ri.tglpasienpulang
            END AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_rjrd.ruangan_nama
            ELSE ruangan_ri.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_rjrd.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_rjrd.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
            END AS penjamin_nama,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
            pembayaran_t.total_sisatagihan AS total_sisa_tagihan,
            (pembayaran_t.total_dijamin)::integer AS total_asuransi,
            pendaftaran_t.is_skd,
            CASE
            WHEN ((pendaftaran_t.is_skd IS FALSE) OR (pendaftaran_t.is_skd IS NULL)) THEN 'Belum Dibuat'::text
            WHEN ((pendaftaran_t.is_skd IS TRUE) OR (pendaftaran_t.is_skd IS NOT NULL)) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.bpjs_id
            ELSE pasienadmisi_t.bpjs_id
            END AS bpjs_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_rjrd.nosep
            ELSE bpjs_ri.nosep
            END AS nosep,
            (pembayaran_t.total_dijamin)::integer AS jumlah_inacbg,
            pendaftaran_t.status_verifikasi,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaimdetail_t.pengajuanklaim_id,
            CASE
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) THEN (('RJ'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN ((pendaftaran_t.pasienadmisi_id IS NULL) AND (pendaftaran_t.instalasi_id = 2)) THEN (('RD'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            WHEN (pendaftaran_t.pasienadmisi_id IS NOT NULL) THEN (('RI'::text || '-'::text) || pendaftaran_t.pasienadmisi_id)
            ELSE NULL::text
            END AS verif_klaim_id,
            pembayaran_t.pembayaranpelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_rjrd.instalasi_id
            ELSE instalasi_ri.instalasi_id
            END AS instalasi_id,
            pembayaran_t.total_discountpembayaran
            FROM ((((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN pasienpulang_t pulang_rjrd ON ((pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id)))
            LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
            LEFT JOIN ruangan_m ruangan_rjrd ON ((pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id)))
            LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_rjrd ON ((ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id)))
            LEFT JOIN instalasi_m instalasi_ri ON ((ruangan_ri.instalasi_id = instalasi_ri.instalasi_id)))
            LEFT JOIN penjamin_m penjamin_rjrd ON ((pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id)))
            LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
            LEFT JOIN carabayar_m carabayar_rjrd ON ((penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id)))
            LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.carabayar_id = carabayar_ri.carabayar_id)))
            LEFT JOIN bpjs_t bpjs_rjrd ON (((pendaftaran_t.bpjs_id = bpjs_rjrd.bpjs_id) AND (bpjs_rjrd.is_deleted = false))))
            LEFT JOIN bpjs_t bpjs_ri ON (((pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id) AND (bpjs_ri.is_deleted = false))))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_rjrd ON ((pendaftaran_t.pendaftaran_id = klaim_rjrd.pendaftaran_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.klaiminacbg_id,
            a.klaimgroup_id,
            klaimgroup_t.total
            FROM (klaiminacbg_t a
            JOIN klaimgroup_t ON (((a.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (a.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            WHERE (a.is_deleted = false)) klaim_ri ON ((pasienadmisi_t.pasienadmisi_id = klaim_ri.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            b.no_pembayaran,
            a.total_sisatagihan,
            b.pembayaranpelayanan_id,
            a.total_discountpembayaran
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t b ON ((a.pembayaran_id = b.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id)))
            LEFT JOIN pengajuanklaimdetail_t ON (((pembayaran_t.pembayaranpelayanan_id = pengajuanklaimdetail_t.pembayaranpelayanan_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            ;");
        $this->execute('
            ALTER TABLE public.pengajuanklaim_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.infopengajuanklaimdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuanklaimdetail_v\" AS
            SELECT rincian.pengajuanklaim_id,
            rincian.pengajuanklaimdetail_id,
            rincian.no_pengajuanklaim,
            rincian.pendaftaran_id,
            rincian.pasienadmisi_id,
            rincian.pasien_id,
            rincian.nama_pasien,
            rincian.no_rekam_medik,
            rincian.jumlah_bayar,
            rincian.jumlah_piutang,
            rincian.total_dibayar AS jumlah_telahbayar,
            rincian.jumlah_sisapiutang,
            rincian.no_pendaftaran,
            rincian.tgl_pendaftaran,
            rincian.tglpasienpulang,
            rincian.nosep,
            rincian.instalasi_id,
            rincian.instalasi_nama,
            rincian.ruangan_id,
            rincian.ruangan_nama,
            rincian.total_tagihan,
            rincian.total_tarifrs,
            rincian.no_pembayaran,
            rincian.total_dijamin,
            rincian.total_dibayar,
            rincian.no_pengajuanklaim AS no_invoice,
            rincian.pembayaran_id
            FROM ( SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM ((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id IS NULL))))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pendaftaran_t.bpjs_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            a.pembayaran_id
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))
            UNION ALL
            SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM (((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            ((b.total_tagihan + b.total_administrasi) + b.total_pembulatan) AS total_tagihan,
            b.total_dibayar,
            b.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            b.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            b.pembayaran_id
            FROM (pembayaran_t b
            JOIN pembayaranpelayanan_t ON ((b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (b.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))) rincian
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuanklaimdetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220121_061625_migrate_hotfix_pengajuanklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_061625_migrate_hotfix_pengajuanklaim cannot be reverted.\n";

        return false;
    }
    */
}
