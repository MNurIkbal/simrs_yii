<?php

use yii\db\Migration;

/**
 * Class m190820_041510_sync_tindakan_de
 */
class m190820_041510_sync_tindakan_de extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW public.sync_tindakan_de;');

        $this->execute("
            CREATE OR REPLACE VIEW public.sync_tindakan_de AS 
 SELECT 'TINDAKAN'::text AS jenis_transaksi,
    'NON_PAKET'::text AS jenis,
    tindakankomponen_t.tindakankomponen_id AS id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    NULL::text AS nomor,
    pasien_m.no_rekam_medik AS rekam_medik,
    pasien_m.nama_pasien AS nama,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal_transaksi,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    komponentarif_m.komponentarif_nama AS komponen_tarif,
    0 AS diskon,
    komponentarif_m.komponentarif_id,
    komponentarif_m.komponentarif_kode,
    tindakankomponen_t.tarif_tindakankomp AS harga,
    concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-', daftartindakan_m.daftartindakan_nama, '-', komponentarif_m.komponentarif_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BELUM_BAYAR'::text
            ELSE 'SUDAH_BAYAR'::text
        END AS is_sudahbayar,
    tindakankomponen_t.is_jurnal,
    'DITAGIHKAN'::text AS is_ditagihkan,
    tindakanpelayanan_t.pemakaianambulan_id
   FROM tindakankomponen_t
     JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanpelayanan_t ON tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE NOT (tindakankomponen_t.tindakankomponen_id IN ( SELECT COALESCE(syncakuntansi_r.tindakankomponen_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE))
UNION ALL
 SELECT 'TINDAKAN'::text AS jenis_transaksi,
    'PAKET'::text AS jenis,
    tindakankomponen_t.tindakankomponen_id AS id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    NULL::text AS nomor,
    pasien_m.no_rekam_medik AS rekam_medik,
    pasien_m.nama_pasien AS nama,
    tindakanpelayanan_t.tgl_tindakan::date AS tanggal_transaksi,
    tipepaket_m.tipepaket_nama AS tindakan,
    komponentarif_m.komponentarif_nama AS komponen_tarif,
    0 AS diskon,
    komponentarif_m.komponentarif_id,
    komponentarif_m.komponentarif_kode,
    tindakankomponen_t.tarif_tindakankomp AS harga,
    concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-', tipepaket_m.tipepaket_nama, '-', komponentarif_m.komponentarif_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN 'BELUM_BAYAR'::text
            ELSE 'SUDAH_BAYAR'::text
        END AS is_sudahbayar,
    tindakankomponen_t.is_jurnal,
    'DITAGIHKAN'::text AS is_ditagihkan,
    tindakanpelayanan_t.pemakaianambulan_id
   FROM tindakankomponen_t
     JOIN komponentarif_m ON tindakankomponen_t.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanpelayanan_t ON tindakankomponen_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE NOT (tindakankomponen_t.tindakankomponen_id IN ( SELECT COALESCE(syncakuntansi_r.tindakankomponen_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS FALSE));");

        $this->execute('ALTER TABLE public.sync_tindakan_de
  OWNER TO postgres;
');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_041510_sync_tindakan_de cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_041510_sync_tindakan_de cannot be reverted.\n";

        return false;
    }
    */
}
