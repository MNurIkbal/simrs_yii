<?php

use yii\db\Migration;

/**
 * Class m201008_083406_migrate_20201008_laporanmutasiobat
 */
class m201008_083406_migrate_20201008_laporanmutasiobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanmutasiobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanmutasiobat_v\" AS  SELECT COALESCE((pesanobat.tglpemesanan)::character varying, '-'::character varying) AS tgl_pemesanan,
    mutasiobat.tglmutasioa AS tgl_pengiriman,
    terimaobat.tglterima AS tgl_penerimaan,
    COALESCE(pesanobat.nopemesanan, '-'::character varying) AS no_pemesanan,
    mutasiobat.nomutasioa AS no_pengiriman,
    terimaobat.noterimamutasi AS no_penerimaan,
    COALESCE(peg_pemesan.nama_pegawai, '-'::character varying) AS pegawai_pemesan,
    peg_mutasi.nama_pegawai AS pegawai_pengirim,
    terimaobat.nama_pegawai AS pegawai_penerima,
    ruang_mutasi.ruangan_nama AS ruangan_pengirim,
    terimaobat.ruangan_nama AS ruangan_penerima,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    pesanobat.jumlah_input AS qty_input_pesan,
    pesanobat.jumlah_pesan AS qty_pesan,
    mutasiobat_detail.jumlah_input AS qty_input_kirim,
    mutasiobat_detail.jumlah_mutasi AS qty_kirim,
    (mutasiobat_detail.jumlah_mutasi / s_konversi.nilai_konversi) AS qty_input_terima,
    terimaobat.qty_diterima AS qty_terima,
    obatalkes_m.harganetto AS harga_satuan,
    (obatalkes_m.harganetto * s_konversi.nilai_konversi) AS harga_netto_konversi,
    ((obatalkes_m.harganetto * s_konversi.nilai_konversi) * mutasiobat_detail.jumlah_mutasi) AS harga_total,
        CASE
            WHEN (pesanobat.jumlah_pesan IS NOT NULL) THEN satuan_input.satuanunit_nama
            ELSE NULL::character varying
        END AS uom_input,
    satuan_konversi.satuanunit_nama AS uom_konversi,
    pesanobat.keterangan_pesan AS catatan_pemesan,
    mutasiobat.keteranganmutasi AS catatan_pengirim,
    terimaobat.keterangan_terima AS catatan_penerima,
    ruang_mutasi.ruangan_id AS ruanganpengirim_id,
    terimaobat.ruanganpenerima_id,
    satuan_input.satuanunit_nama AS uom_input_terima
   FROM ((((((((((mutasiobatruangan_t mutasiobat
     JOIN mutasiobatdetail_t mutasiobat_detail ON ((mutasiobat.mutasiobatruangan_id = mutasiobat_detail.mutasiobatruangan_id)))
     LEFT JOIN ( SELECT pesanobatalkes_t.pesanobatalkes_id,
            pesanobatalkes_t.tglpemesanan,
            pesanobatalkes_t.nopemesanan,
            pesanobatalkes_t.keterangan_pesan,
            pesanobatalkes_t.pegawaipemesan_id,
            pesanobatdetail_t.jumlah_input,
            pesanobatdetail_t.jumlah_pesan,
            pesanobatdetail_t.obatalkes_id
           FROM (pesanobatdetail_t
             JOIN pesanobatalkes_t ON ((pesanobatdetail_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
          WHERE ((pesanobatalkes_t.is_deleted = false) AND (pesanobatdetail_t.is_deleted = false))
          GROUP BY pesanobatalkes_t.pesanobatalkes_id, pesanobatalkes_t.tglpemesanan, pesanobatalkes_t.nopemesanan, pesanobatalkes_t.keterangan_pesan, pesanobatalkes_t.pegawaipemesan_id, pesanobatdetail_t.jumlah_input, pesanobatdetail_t.jumlah_pesan, pesanobatdetail_t.obatalkes_id) pesanobat ON (((mutasiobat.pesanobatalkes_id = pesanobat.pesanobatalkes_id) AND (mutasiobat_detail.obatalkes_id = pesanobat.obatalkes_id))))
     LEFT JOIN pegawai_m peg_pemesan ON ((pesanobat.pegawaipemesan_id = peg_pemesan.pegawai_id)))
     LEFT JOIN pegawai_m peg_mutasi ON ((mutasiobat.pegawaimenyetujui_id = peg_mutasi.pegawai_id)))
     JOIN obatalkes_m ON ((mutasiobat_detail.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_input ON ((mutasiobat_detail.satuanbesar_id = satuan_input.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_konversi ON ((mutasiobat_detail.satuankecil_id = satuan_konversi.satuanunit_id)))
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuanbesar_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
          WHERE (satuankonversi_m.is_deleted = false)) s_konversi ON (((mutasiobat_detail.obatalkes_id = s_konversi.obatalkes_id) AND (mutasiobat_detail.satuanbesar_id = s_konversi.satuanbesar_id) AND (mutasiobat_detail.satuankecil_id = s_konversi.satuankecil_id))))
     LEFT JOIN ruangan_m ruang_mutasi ON ((mutasiobat.ruanganasal_id = ruang_mutasi.ruangan_id)))
     LEFT JOIN ( SELECT terimamutasiobat_t.mutasiobatruangan_id,
            terimamutasiobat_t.tglterima,
            terimamutasiobat_t.noterimamutasi,
            terimamutasiobat_t.ruanganpenerima_id,
            peg_terima.nama_pegawai,
            ruang_terima.ruangan_nama,
            terimamutasiobat_t.keterangan_terima,
            terimamutasiobatdetail_t.obatalkes_id,
            sum(terimamutasiobatdetail_t.jmlterima) AS qty_diterima
           FROM (((terimamutasiobat_t
             JOIN terimamutasiobatdetail_t ON ((terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id)))
             LEFT JOIN pegawai_m peg_terima ON ((terimamutasiobat_t.pegawaipenerima_id = peg_terima.pegawai_id)))
             LEFT JOIN ruangan_m ruang_terima ON ((terimamutasiobat_t.ruanganpenerima_id = ruang_terima.ruangan_id)))
          WHERE ((terimamutasiobat_t.is_deleted = false) AND (terimamutasiobatdetail_t.is_deleted = false))
          GROUP BY terimamutasiobat_t.mutasiobatruangan_id, terimamutasiobat_t.tglterima, terimamutasiobat_t.noterimamutasi, terimamutasiobat_t.ruanganpenerima_id, peg_terima.nama_pegawai, ruang_terima.ruangan_nama, terimamutasiobat_t.keterangan_terima, terimamutasiobatdetail_t.obatalkes_id) terimaobat ON (((mutasiobat.mutasiobatruangan_id = terimaobat.mutasiobatruangan_id) AND (mutasiobat_detail.obatalkes_id = terimaobat.obatalkes_id))))
  WHERE (mutasiobat.is_deleted = false);");

        $this->execute('ALTER TABLE "public"."laporanmutasiobat_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201008_083406_migrate_20201008_laporanmutasiobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201008_083406_migrate_20201008_laporanmutasiobat cannot be reverted.\n";

        return false;
    }
    */
}
