<?php

use yii\db\Migration;

/**
 * Class m230627_103907_migrate_DSV259_laporanpenerimaanobatdetail_v
 */
class m230627_103907_migrate_DSV259_laporanpenerimaanobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpenerimaanobatdetail_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanpenerimaanobatdetail_v
        AS SELECT supplier_m.supplier_nama AS nama_supplier,
            payterm_m.payterm_nama AS payterm,
            penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
            validasipoobat_t.tgl_validasi + '14 days'::interval AS tanggal_jatuhtempo,
            concat(konfigfarmasi_k.po_expired, ' ', 'Day') AS po_expired,
            penerimaanobat_t.no_penerimaan,
                CASE
                    WHEN penerimaanobat_t.is_verifikasi = 1 THEN 'Sudah Diverifikasi'::text
                    WHEN penerimaanobat_t.is_verifikasi = 0 THEN 'Dibatalkan'::text
                    ELSE NULL::text
                END AS status_penerimaan,
            validasipoobat_t.no_poobat AS no_po_obat,
            obatalkes_m.obatalkes_kode,
            obatalkes_m.obatalkes_nama,
            jenisobatalkes_m.jenisobatalkes_nama AS jenis_obatalkes,
            penerimaanobatdetail_t.qty_diterima,
            satuanbesar.satuanunit_nama AS satuan_besar,
            penerimaanobatdetail_t.harga,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga AS subtotal,
            penerimaanobatdetail_t.discount,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga * penerimaanobatdetail_t.discount / 100::double precision AS discount_rp,
            pajak_m.pajak_persen,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga * pajak_m.pajak_persen::double precision / 100::double precision AS pajak_rp,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga - penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga * penerimaanobatdetail_t.discount / 100::double precision + penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga * pajak_m.pajak_persen::double precision / 100::double precision AS total,
            penerimaanobat_t.no_faktur,
            penerimaanobat_t.no_suratjalan AS no_surat_jalan,
            penerimaanobat_t.tgl_suratjalan AS tanggal_faktur
           FROM penerimaanobatdetail_t
             LEFT JOIN ( SELECT a.penerimaanobat_id,
                    a.validasipoobat_id,
                    a.no_penerimaan,
                    a.tgl_penerimaan,
                    a.is_verifikasi,
                    a.no_faktur,
                    a.no_suratjalan,
                    a.tgl_suratjalan
                   FROM penerimaanobat_t a
                  WHERE a.is_deleted = false) penerimaanobat_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
             LEFT JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama,
                    a.obatalkes_kode,
                    a.jenisobatalkes_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkes_m.obatalkes_id = penerimaanobatdetail_t.obatalkes_id
             LEFT JOIN ( SELECT a.validasipoobat_id,
                    a.no_poobat,
                    a.supplier_id,
                    a.payterm_id,
                    a.tgl_validasi,
                    a.status_penerimaan,
                    a.pajak_id
                   FROM validasipoobat_t a) validasipoobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
             LEFT JOIN ( SELECT a.supplier_id,
                    a.supplier_kode,
                    a.supplier_nama
                   FROM supplier_m a) supplier_m ON supplier_m.supplier_id = validasipoobat_t.supplier_id
             LEFT JOIN ( SELECT a.payterm_id,
                    a.payterm_nama
                   FROM payterm_m a) payterm_m ON payterm_m.payterm_id = validasipoobat_t.payterm_id
             LEFT JOIN ( SELECT a.konfigfarmasi_id,
                    a.po_expired
                   FROM konfigfarmasi_k a) konfigfarmasi_k ON konfigfarmasi_k.konfigfarmasi_id = 1
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) statuspenerimaan ON statuspenerimaan.lookup_id = validasipoobat_t.status_penerimaan
             LEFT JOIN ( SELECT a.jenisobatalkes_id,
                    a.jenisobatalkes_nama
                   FROM jenisobatalkes_m a) jenisobatalkes_m ON jenisobatalkes_m.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id
             LEFT JOIN ( SELECT a.satuanbesar_id,
                    a.satuankecil_id,
                    a.nilai_konversi,
                    a.obatalkes_id,
                    a.satuankonversi_id
                   FROM satuankonversi_m a
                  WHERE a.is_deleted = false) satuankonversi_m ON satuankonversi_m.satuankonversi_id = penerimaanobatdetail_t.s_konversiobt_id
             LEFT JOIN ( SELECT a.satuanunit_id,
                    a.satuanunit_nama
                   FROM satuanunit_m a) satuanbesar ON satuanbesar.satuanunit_id = satuankonversi_m.satuanbesar_id
             LEFT JOIN ( SELECT a.pajak_id,
                    a.pajak_persen
                   FROM pajak_m a) pajak_m ON pajak_m.pajak_id = validasipoobat_t.pajak_id
          WHERE penerimaanobatdetail_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230627_103907_migrate_DSV259_laporanpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230627_103907_migrate_DSV259_laporanpenerimaanobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
