<?php

use yii\db\Migration;

/**
 * Class m211029_020400_migrate_laporanpenerimaanobatalkes_v
 */
class m211029_020400_migrate_laporanpenerimaanobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpenerimaanobatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenerimaanobatalkes_v\" AS  SELECT supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    manufaktur_m.nama AS nama_manufaktur,
    terima.tgl_penerimaan,
    terima.no_penerimaan,
    terima.nama_pegawai AS diterima_oleh,
        CASE
            WHEN terima.is_verifikasi::integer = 1 THEN 'Sudah diverifikasi'::text
            WHEN terima.is_verifikasi::integer = 2 THEN 'Dibatalkan'::text
            ELSE 'Belum diverifikasi'::text
        END AS status_penerimaan,
    terima.tgl_po,
    terima.tgl_validasi_po,
    terima.nomor_po,
    obatalkes_m.obatalkes_kode AS kode_item,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    terima.qty_po,
    besar.satuanunit_nama AS satuan_po,
    terima.qty_diterima,
    besar.satuanunit_nama AS satuan_terima,
    terima.po_balance,
        CASE
            WHEN terima.po_balance IS NULL THEN NULL::character varying
            ELSE besar.satuanunit_nama
        END AS satuan_balance,
    satuankonversi_m.nilai_konversi,
    kecil.satuanunit_nama AS satuan_kecil,
    terima.harga,
    terima.discount,
    terima.ppn_persen,
    terima.sub_total,
    terima.total,
    terima.catatan_po,
    terima.tgl_pr,
    terima.no_pr,
    terima.no_batch,
    terima.tgl_kadaluarsa,
    terima.no_suratjalan,
    terima.no_faktur,
    terima.qty_diterima::double precision * satuankonversi_m.nilai_konversi AS qty_konversi,
    supplier_m.supplier_id
   FROM ( SELECT penerimaanobat_t.penerimaanobat_id,
            penerimaanobat_t.tgl_penerimaan,
            penerimaanobat_t.no_penerimaan,
            validasipoobat_t.no_poobat AS nomor_po,
            penerimaanobat_t.supplier_id,
            penerimaanobat_t.no_suratjalan,
            penerimaanobat_t.tgl_suratjalan,
            penerimaanobat_t.no_faktur,
            penerimaanobat_t.diterima_oleh,
            penerimaanobat_t.upload_berkas,
            penerimaanobat_t.catatan_berkas,
            penerimaanobat_t.catatan,
            penerimaanobat_t.peg_mengetahui,
            penerimaanobat_t.peg_menyetujui,
            penerimaanobatdetail_t.penerimaanobatdetail_id,
            penerimaanobatdetail_t.obatalkes_id,
            penerimaanobatdetail_t.qty_po,
            penerimaanobatdetail_t.qty_diterima,
            COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
            penerimaanobatdetail_t.tgl_kadaluarsa,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.keterangan,
            penerimaanobat_t.is_verifikasi,
            penerimaanobatdetail_t.validasipoobatdetail_id,
            penerimaanobatdetail_t.harga,
            penerimaanobatdetail_t.discount,
            penerimaanobatdetail_t.discount_rp,
            penerimaanobatdetail_t.jumlah,
            validasipoobat_t.pajak_id,
            penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp AS sub_total,
            validasipoobat_t.total_discount,
            validasipoobat_t.ppn_persen,
            validasipoobat_t.ppn_nilai,
            validasipoobat_t.created_date AS tgl_po,
            validasipoobat_t.tgl_validasi AS tgl_validasi_po,
            penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga - penerimaanobatdetail_t.discount_rp + (penerimaanobatdetail_t.qty_diterima::double precision * penerimaanobatdetail_t.harga - penerimaanobatdetail_t.discount_rp) * (pajak_m.pajak_persen::double precision / 100::double precision) AS total,
            purchasereq_t.no_pr,
            purchasereq_t.tgl_pr,
            validasipoobat_t.catatan AS catatan_po,
            pegawai_m.nama_pegawai
           FROM penerimaanobat_t
             JOIN penerimaanobatdetail_t ON penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id
             LEFT JOIN validasipoobat_t ON penerimaanobat_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
             JOIN validasipoobatdetail_t ON penerimaanobatdetail_t.validasipoobatdetail_id = validasipoobatdetail_t.validasipoobatdetail_id
             LEFT JOIN purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
             LEFT JOIN purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
             LEFT JOIN pegawai_m ON penerimaanobat_t.diterima_oleh = pegawai_m.pegawai_id
             LEFT JOIN pajak_m ON pajak_m.pajak_id = validasipoobat_t.pajak_id
          WHERE penerimaanobatdetail_t.is_deleted = false AND penerimaanobat_t.is_deleted = false
        UNION ALL
         SELECT penerimaansupp_t.penerimaansupp_id,
            penerimaansupp_t.tgl_penerimaan,
            penerimaansupp_t.no_penerimaan,
            NULL::character varying AS nomor_po,
            penerimaansupp_t.supplier_id,
            penerimaansupp_t.no_suratjalan,
            penerimaansupp_t.tgl_penerimaan,
            penerimaansupp_t.no_faktur,
            penerimaansupp_t.created_by,
            NULL::text AS upload_berkas,
            NULL::text AS catatan_berkas,
            NULL::text AS catatan,
            penerimaansupp_t.peg_mengetahui,
            penerimaansupp_t.peg_menyetujui,
            penerimaansuppdetail_t.penerimaansuppdetail_id,
            penerimaansuppdetail_t.obatalkes_id,
            0 AS qty_po,
            penerimaansuppdetail_t.qty_besar,
            NULL::integer AS po_balance,
            penerimaansuppdetail_t.tgl_kadaluarsa,
            penerimaansuppdetail_t.no_batch,
            penerimaansuppdetail_t.satuankonversi_id,
            penerimaansuppdetail_t.keterangan,
            NULL::smallint AS is_verifikasi,
            NULL::integer AS validasipoobatdetail_id,
            penerimaansuppdetail_t.harga_netto_satuan,
            penerimaansuppdetail_t.diskon,
            penerimaansuppdetail_t.diskon AS discount_rp,
            0 AS jumlah,
            penerimaansupp_t.pajak_id,
            penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision AS sub_total,
            0 AS total_discount,
            pajak_m.pajak_persen AS ppn_persen,
            pajak_m.pajak_persen AS ppn_nilai,
            NULL::timestamp without time zone AS tgl_po,
            penerimaansupp_t.tgl_verifikasi AS tgl_validasi_po,
            penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision + (penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS total,
            NULL::character varying AS no_pr,
            NULL::date AS tgl_pr,
            NULL::text AS catatan_po,
            pegawai_m.nama_pegawai
           FROM penerimaansupp_t
             JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN loginpemakai_k ON penerimaansupp_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
          WHERE penerimaansuppdetail_t.is_deleted = false AND penerimaansupp_t.is_deleted = false) terima
     JOIN supplier_m ON terima.supplier_id = supplier_m.supplier_id
     JOIN obatalkes_m ON terima.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuankonversi_m ON terima.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN pegawai_m peg_mengetahui ON terima.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON terima.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN ( SELECT returpenerimaanobatdetail_t.penerimaanobatdetail_id,
            sum(returpenerimaanobatdetail_t.qty_retur) AS on_retur
           FROM returpenerimaanobatdetail_t returpenerimaanobatdetail_t
          GROUP BY returpenerimaanobatdetail_t.penerimaanobatdetail_id) returdetailjumlah ON terima.penerimaanobatdetail_id = returdetailjumlah.penerimaanobatdetail_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211029_020400_migrate_laporanpenerimaanobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211029_020400_migrate_laporanpenerimaanobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
