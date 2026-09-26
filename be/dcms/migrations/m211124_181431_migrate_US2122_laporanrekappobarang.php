<?php

use yii\db\Migration;

/**
 * Class m211124_181431_migrate_US2122_laporanrekappobarang
 */
class m211124_181431_migrate_US2122_laporanrekappobarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekappobarang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekappobarang_v\" AS
            SELECT rekap.supplier_id,
            rekap.supplier_kode,
            rekap.supplier_nama,
            rekap.no_po,
            rekap.tgl_po,
            rekap.tgl_validasi,
            rekap.status_id,
            rekap.status_po,
            rekap.tgl_batal_po,
            rekap.alasan_batal_po,
            rekap.total_harga
            FROM ( SELECT supplier_m.supplier_id,
            supplier_m.supplier_kode,
            supplier_m.supplier_nama,
            validasipobarang_t.no_pobarang AS no_po,
            validasipobarang_t.created_date AS tgl_po,
            validasipobarang_t.tgl_validasi,
            validasipobarang_t.status_penerimaan AS status_id,
            fgetnamalookup(validasipobarang_t.status_penerimaan) AS status_po,
            CASE
            WHEN (validasipobarang_t.status_penerimaan = 575) THEN validasipobarang_t.last_modified_date
            ELSE NULL::timestamp without time zone
            END AS tgl_batal_po,
            btrim(validasipobarang_t.catatan) AS alasan_batal_po,
            validasipobarang_t.total AS total_harga
            FROM ((validasipobarang_t
            JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
            JOIN supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
            WHERE ((validasipobarang_t.is_deleted = false) AND (validasipobarangdetail_t.is_deleted = false))) rekap
            GROUP BY rekap.supplier_id, rekap.supplier_kode, rekap.supplier_nama, rekap.no_po, rekap.tgl_po, rekap.tgl_validasi, rekap.status_id, rekap.status_po, rekap.tgl_batal_po, rekap.alasan_batal_po, rekap.total_harga
            ;");
        $this->execute('
            ALTER TABLE public.laporanrekappobarang_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211124_181431_migrate_US2122_laporanrekappobarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211124_181431_migrate_US2122_laporanrekappobarang cannot be reverted.\n";

        return false;
    }
    */
}
