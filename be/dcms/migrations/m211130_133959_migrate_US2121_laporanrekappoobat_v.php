<?php

use yii\db\Migration;

/**
 * Class m211130_133959_migrate_US2121_laporanrekappoobat_v
 */
class m211130_133959_migrate_US2121_laporanrekappoobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekappoobat_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekappoobat_v\" AS
            SELECT 'a'::text AS ket,
            supplier_m.supplier_id,
            supplier_m.supplier_kode,
            supplier_m.supplier_nama,
            btrim((validasipoobat_t.no_poobat)::text) AS no_po,
            validasipoobat_t.created_date AS tgl_po,
            validasipoobat_t.tgl_validasi,
            validasipoobat_t.status_penerimaan AS status_id,
            btrim((fgetnamalookup(validasipoobat_t.status_penerimaan))::text) AS status_po,
            CASE
            WHEN (validasipoobat_t.status_penerimaan = 575) THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
            END AS tgl_batal_po,
            btrim(validasipoobat_t.catatan) AS alasan_batal_po,
            (((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) + ((((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) * (validasipoobat_t.ppn_persen)::double precision) / (100)::double precision)) AS total_harga
            FROM ((((purchasereq_t
            JOIN ( SELECT a.purchasereq_id,
            a.purchasereqdetail_id
            FROM purchasereqdetail_t a
            WHERE (a.is_deleted = false)) purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
            LEFT JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.harga,
            a.qty_input,
            a.discount_rp
            FROM validasipoobatdetail_t a
            WHERE (a.is_deleted = false)) validasipoobatdetail_t ON ((purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id)))
            LEFT JOIN ( SELECT a.validasipoobat_id,
            a.supplier_id,
            a.no_poobat,
            a.created_date,
            a.tgl_validasi,
            a.status_penerimaan,
            a.last_modified_date,
            a.catatan,
            a.ppn_persen,
            a.is_manual
            FROM validasipoobat_t a
            WHERE (a.is_deleted = false)) validasipoobat_t ON ((validasipoobatdetail_t.validasipoobat_id = validasipoobat_t.validasipoobat_id)))
            LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
            FROM supplier_m a
            WHERE (a.is_deleted = false)) supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
            WHERE ((purchasereq_t.is_deleted = false) AND (validasipoobat_t.is_manual = false))
            UNION ALL
            SELECT 'c'::text AS ket,
            supplier_m.supplier_id,
            supplier_m.supplier_kode,
            supplier_m.supplier_nama,
            validasipoobat_t.no_poobat AS no_po,
            validasipoobat_t.created_date AS tgl_po,
            validasipoobat_t.tgl_validasi,
            validasipoobat_t.status_penerimaan AS status_id,
            btrim((fgetnamalookup(validasipoobat_t.status_penerimaan))::text) AS status_po,
            CASE
            WHEN (validasipoobat_t.status_penerimaan = 575) THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
            END AS tgl_batal_po,
            btrim(validasipoobat_t.catatan) AS alasan_batal_po,
            validasipoobat_t.total AS total_harga
            FROM (((((((validasipoobat_t
            JOIN validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
            LEFT JOIN penerimaanobat_t ON ((validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id)))
            LEFT JOIN purchasereqdetail_t ON ((validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id)))
            LEFT JOIN purchasereq_t ON ((purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id)))
            LEFT JOIN pegawai_m ON ((validasipoobat_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pajak_m ON ((pajak_m.pajak_id = validasipoobat_t.pajak_id)))
            JOIN supplier_m ON ((supplier_m.supplier_id = validasipoobat_t.supplier_id)))
            WHERE ((validasipoobat_t.is_deleted = false) AND (validasipoobatdetail_t.is_deleted = false) AND (validasipoobat_t.is_manual = true))
            GROUP BY supplier_m.supplier_id, supplier_m.supplier_kode, supplier_m.supplier_nama, validasipoobat_t.no_poobat, validasipoobat_t.created_date, validasipoobat_t.tgl_validasi, validasipoobat_t.status_penerimaan, validasipoobat_t.last_modified_date, validasipoobat_t.catatan, validasipoobat_t.total
            ;");
        $this->execute('
            ALTER TABLE public.laporanrekappoobat_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_133959_migrate_US2121_laporanrekappoobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_133959_migrate_US2121_laporanrekappoobat_v cannot be reverted.\n";

        return false;
    }
    */
}
