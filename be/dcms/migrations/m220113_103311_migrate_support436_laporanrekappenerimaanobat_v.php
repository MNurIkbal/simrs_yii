<?php

use yii\db\Migration;

/**
 * Class m220113_103311_migrate_support436_laporanrekappenerimaanobat_v
 */
class m220113_103311_migrate_support436_laporanrekappenerimaanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekappenerimaanobat_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekappenerimaanobat_v\" AS
            SELECT rekap.penerimaanobat_id,
            rekap.supplier_id,
            rekap.supplier_kode,
            rekap.supplier_nama,
            rekap.tgl_penerimaan,
            rekap.no_penerimaan,
            rekap.diterima_oleh,
            rekap.status_penerimaan,
            rekap.nomor_po,
            rekap.tgl_po,
            rekap.tgl_validasi_po,
            rekap.no_suratjalan,
            rekap.no_faktur,
            sum(rekap.total) AS total,
            rekap.payterm_id,
            rekap.payterm_nama
            FROM ( SELECT penerimaansupp_t.penerimaansupp_id AS penerimaanobat_id,
            penerimaansupp_t.supplier_id,
            supplier_m.supplier_kode,
            supplier_m.supplier_nama,
            penerimaansupp_t.tgl_penerimaan,
            penerimaansupp_t.no_penerimaan,
            pegawai_m.nama_pegawai AS diterima_oleh,
            CASE
            WHEN ((penerimaansupp_t.is_verifikasi)::integer = 1) THEN 'Sudah diverifikasi'::text
            WHEN ((penerimaansupp_t.is_verifikasi)::integer = 2) THEN 'Dibatalkan'::text
            ELSE 'Belum diverifikasi'::text
            END AS status_penerimaan,
            NULL::character varying AS nomor_po,
            NULL::timestamp without time zone AS tgl_po,
            penerimaansupp_t.tgl_verifikasi AS tgl_validasi_po,
            penerimaansupp_t.no_suratjalan,
            penerimaansupp_t.no_faktur,
            (((penerimaansuppdetail_t.harga_netto_satuan * (penerimaansuppdetail_t.qty_besar)::double precision) - (((penerimaansuppdetail_t.harga_netto_satuan * (penerimaansuppdetail_t.qty_besar)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) + (((penerimaansuppdetail_t.harga_netto_satuan * (penerimaansuppdetail_t.qty_besar)::double precision) - (((penerimaansuppdetail_t.harga_netto_satuan * (penerimaansuppdetail_t.qty_besar)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * ((pajak_m.pajak_persen)::double precision / (100)::double precision))) AS total,
            payterm_m.payterm_id,
            payterm_m.payterm_nama
            FROM ((((((penerimaansupp_t
            JOIN penerimaansuppdetail_t ON ((penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id)))
            JOIN pajak_m ON ((penerimaansupp_t.pajak_id = pajak_m.pajak_id)))
            JOIN supplier_m ON ((penerimaansupp_t.supplier_id = supplier_m.supplier_id)))
            LEFT JOIN loginpemakai_k ON ((penerimaansupp_t.created_by = loginpemakai_k.loginpemakai_id)))
            LEFT JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN payterm_m ON ((penerimaansupp_t.payterm_id = payterm_m.payterm_id)))
            WHERE ((penerimaansuppdetail_t.is_deleted = false) AND (penerimaansupp_t.is_deleted = false))
            UNION ALL
            SELECT penerimaanobat_t.penerimaanobat_id,
            penerimaanobat_t.supplier_id,
            supplier_m.supplier_kode,
            supplier_m.supplier_nama,
            penerimaanobat_t.tgl_penerimaan,
            penerimaanobat_t.no_penerimaan,
            pegawai_m.nama_pegawai AS diterima_oleh,
            CASE
            WHEN ((penerimaanobat_t.is_verifikasi)::integer = 1) THEN 'Sudah diverifikasi'::text
            WHEN ((penerimaanobat_t.is_verifikasi)::integer = 2) THEN 'Dibatalkan'::text
            ELSE 'Belum diverifikasi'::text
            END AS status_penerimaan,
            validasipoobat_t.no_poobat AS nomor_po,
            validasipoobat_t.created_date AS tgl_po,
            validasipoobat_t.tgl_validasi AS tgl_validasi_po,
            penerimaanobat_t.no_suratjalan,
            penerimaanobat_t.no_faktur,
            ((((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga) - penerimaanobatdetail_t.discount_rp) + ((((penerimaanobatdetail_t.qty_diterima)::double precision * penerimaanobatdetail_t.harga) - penerimaanobatdetail_t.discount_rp) * ((pajak_m.pajak_persen)::double precision / (100)::double precision))) AS total,
            payterm_m.payterm_id,
            payterm_m.payterm_nama
            FROM (((((((((penerimaanobat_t
            JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))
            LEFT JOIN validasipoobat_t ON ((penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id)))
            JOIN validasipoobatdetail_t ON ((penerimaanobatdetail_t.validasipoobatdetail_id = validasipoobatdetail_t.validasipoobatdetail_id)))
            LEFT JOIN purchasereqdetail_t ON ((validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id)))
            LEFT JOIN purchasereq_t ON ((purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id)))
            LEFT JOIN pegawai_m ON ((penerimaanobat_t.diterima_oleh = pegawai_m.pegawai_id)))
            LEFT JOIN pajak_m ON ((pajak_m.pajak_id = validasipoobat_t.pajak_id)))
            JOIN supplier_m ON ((supplier_m.supplier_id = validasipoobat_t.supplier_id)))
            LEFT JOIN payterm_m ON ((validasipoobat_t.payterm_id = payterm_m.payterm_id)))
            WHERE ((penerimaanobatdetail_t.is_deleted = false) AND (penerimaanobat_t.is_deleted = false))) rekap
            GROUP BY rekap.penerimaanobat_id, rekap.supplier_id, rekap.supplier_kode, rekap.supplier_nama, rekap.tgl_penerimaan, rekap.no_penerimaan, rekap.diterima_oleh, rekap.status_penerimaan, rekap.nomor_po, rekap.tgl_po, rekap.tgl_validasi_po, rekap.no_suratjalan, rekap.no_faktur, rekap.payterm_id, rekap.payterm_nama
            ;");
        $this->execute('
            ALTER TABLE public.laporanrekappenerimaanobat_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220113_103311_migrate_support436_laporanrekappenerimaanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220113_103311_migrate_support436_laporanrekappenerimaanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
