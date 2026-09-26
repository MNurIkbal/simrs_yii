<?php

use yii\db\Migration;

/**
 * Class m210115_055208_migrate_20200115_infodistribusibarang_v
 */
class m210115_055208_migrate_20200115_infodistribusibarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infodistribusibarang_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infodistribusibarang_v\" AS  SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.ruangantujuan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanbarang_t.no_pemesanan,
    pesanbarang_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    mutasibarang_t.mutasibarang_id,
    pesanbarang_t.statuspesan,
    fgetnamalookup(pesanbarang_t.statuspesan::integer) AS status_pengiriman,
    pesanbarang_t.tgl_mintadikirim,
    pesanbarang_t.keterangan_pesan,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS status_penerimaan,
    terimamutasibarang_t.noterimamutasi,
    terimamutasibarang_t.tglterima,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
        CASE
            WHEN pesanbarang_t.statuspesan::text = '398'::text THEN 'Belum Dikirim'::text
            WHEN mutasibarang_t.status_mutasi = 401 THEN 'Sudah Dikirim'::text
            WHEN mutasibarang_t.status_mutasi = 400 THEN 'Diterima'::text
            ELSE '-'::text
        END AS status_distribusi,
    concat(mutasibarang_t.nomutasi_barang,
        CASE
            WHEN terimamutasibarang_t.noterimamutasi IS NULL THEN ''::text
            ELSE concat(',', terimamutasibarang_t.noterimamutasi)
        END) AS reference,
        CASE
            WHEN mutasibarang_t.pesanbarang_id IS NULL THEN pesanbarang_t.statuspesan::integer
            WHEN mutasibarang_t.pesanbarang_id IS NOT NULL THEN mutasibarang_t.status_mutasi
            WHEN terimamutasibarang_t.mutasibarang_id IS NOT NULL THEN mutasibarang_t.status_mutasi
            ELSE NULL::integer
        END AS status_id,
    NULL::text AS status_verifikasi,
    NULL::text AS status_verifikasi_nama,
    pemesan.nama_pegawai AS pemesan,
    pengirim.nama_pegawai AS pengirim,
    penerima.nama_pegawai AS penerima
   FROM pesanbarang_t
     JOIN ruangan_m ON pesanbarang_t.ruangantujuan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanbarang_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasibarang_t ON pesanbarang_t.pesanbarang_id = mutasibarang_t.pesanbarang_id
     LEFT JOIN terimamutasibarang_t ON mutasibarang_t.mutasibarang_id = terimamutasibarang_t.mutasibarang_id
     LEFT JOIN pegawai_m pemesan ON pesanbarang_t.pegawaipemesan_id = pemesan.pegawai_id
     LEFT JOIN pegawai_m pengirim ON mutasibarang_t.pegawaimengetahui_id = pengirim.pegawai_id
     LEFT JOIN pegawai_m penerima ON terimamutasibarang_t.pegawaipenerima_id = penerima.pegawai_id
  WHERE pesanbarang_t.is_active = true AND pesanbarang_t.is_deleted = false;");

         $this->execute('ALTER TABLE "public"."infodistribusibarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210115_055208_migrate_20200115_infodistribusibarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210115_055208_migrate_20200115_infodistribusibarang_v cannot be reverted.\n";

        return false;
    }
    */
}
