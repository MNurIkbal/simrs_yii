<?php

use yii\db\Migration;

/**
 * Class m220404_031027_migrate_VCS108_laporanpemeriksaanrad_v
 */
class m220404_031027_migrate_VCS108_laporanpemeriksaanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpemeriksaanrad_v";');
        $this->execute("CREATE VIEW \"public\".\"laporanpemeriksaanrad_v\" AS  SELECT 'NON_PAKET'::text AS tipe,
        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
        pasienmasukpenunjang_t.tglmasukpenunjang,
        pasienmasukpenunjang_t.pendaftaran_id,
        pendaftaran_t.no_pendaftaran,
        pendaftaran_t.pasien_id,
        pasien_m.nama_pasien,
        pasien_m.no_rekam_medik,
        pasien_m.tanggal_lahir,
        pasienmasukpenunjang_t.pegawai_id,
        dokter.nama_pegawai AS dokter,
        pemeriksaanrad_m.kelompokpemeriksaanrad_id,
        kelompokpemeriksaanrad_m.nama_kelompok,
        pemeriksaanrad_m.jenispemeriksaanrad_id,
        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
        tindakanpelayanan_t.daftartindakan_id,
        daftartindakan_m.daftartindakan_nama,
        NULL::integer AS tipepaket_id,
        NULL::character varying AS tipepaket_nama,
        tindakanpelayanan_t.tarif_satuan,
        tindakanpelayanan_t.qty_tindakan,
        tindakanpelayanan_t.tarifcyto_tindakan,
        tindakanpelayanan_t.tarif_tindakan,
        pendaftaran_t.kelaspelayanan_id,
        kelaspelayanan.kelaspelayanan_nama
       FROM pasienmasukpenunjang_t
         JOIN ( SELECT a.pendaftaran_id,
                a.no_pendaftaran,
                a.pasien_id,
                a.kelaspelayanan_id
               FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN ( SELECT a.pasien_id,
                a.nama_pasien,
                a.no_rekam_medik,
                a.tanggal_lahir
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN ( SELECT a.pasienmasukpenunjang_id,
                a.daftartindakan_id,
                a.dokterpenanggungjawab_id,
                a.tarif_satuan,
                a.qty_tindakan,
                a.tarifcyto_tindakan,
                a.tarif_tindakan,
                a.is_deleted
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
         JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
         JOIN ( SELECT a.daftartindakan_id,
                a.daftartindakan_nama
               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN ( SELECT a.daftartindakan_id,
                a.kelompokpemeriksaanrad_id,
                a.jenispemeriksaanrad_id
               FROM pemeriksaanrad_m a) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
         JOIN ( SELECT a.kelompokpemeriksaanrad_id,
                a.nama_kelompok
               FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
         JOIN ( SELECT a.jenispemeriksaanrad_id,
                a.jenispemeriksaanrad_nama
               FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
         JOIN ( SELECT a.kelaspelayanan_id,
                a.kelaspelayanan_nama
               FROM kelaspelayanan_m a) kelaspelayanan ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan.kelaspelayanan_id
    UNION ALL
     SELECT 'PAKET'::text AS tipe,
        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
        pasienmasukpenunjang_t.tglmasukpenunjang,
        pasienmasukpenunjang_t.pendaftaran_id,
        pendaftaran_t.no_pendaftaran,
        pendaftaran_t.pasien_id,
        pasien_m.nama_pasien,
        pasien_m.no_rekam_medik,
        pasien_m.tanggal_lahir,
        pasienmasukpenunjang_t.pegawai_id,
        dokter.nama_pegawai AS dokter,
        pemeriksaanrad_m.kelompokpemeriksaanrad_id,
        kelompokpemeriksaanrad_m.nama_kelompok,
        pemeriksaanrad_m.jenispemeriksaanrad_id,
        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
        paketpelayanan_mp.daftartindakan_id,
        daftartindakan_m.daftartindakan_nama,
        tindakanpelayanan_t.tipepaket_id,
        tipepaket_m.tipepaket_nama,
        tindakanpelayanan_t.tarif_satuan,
        tindakanpelayanan_t.qty_tindakan,
        tindakanpelayanan_t.tarifcyto_tindakan,
        tindakanpelayanan_t.tarif_tindakan,
        pendaftaran_t.kelaspelayanan_id,
        kelaspelayanan.kelaspelayanan_nama
       FROM pasienmasukpenunjang_t
         JOIN ( SELECT a.pendaftaran_id,
                a.no_pendaftaran,
                a.pasien_id,
                a.kelaspelayanan_id
               FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN ( SELECT a.pasien_id,
                a.nama_pasien,
                a.no_rekam_medik,
                a.tanggal_lahir
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN ( SELECT a.pasienmasukpenunjang_id,
                a.daftartindakan_id,
                a.dokterpenanggungjawab_id,
                a.tipepaket_id,
                a.tarif_satuan,
                a.qty_tindakan,
                a.tarifcyto_tindakan,
                a.tarif_tindakan,
                a.is_deleted
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
         JOIN ( SELECT a.pegawai_id,
                a.nama_pegawai
               FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
         JOIN ( SELECT a.tipepaket_id,
                a.tipepaket_nama
               FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
         JOIN ( SELECT a.tipepaket_id,
                a.daftartindakan_id
               FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
         JOIN ( SELECT a.daftartindakan_id,
                a.daftartindakan_nama
               FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN ( SELECT a.daftartindakan_id,
                a.kelompokpemeriksaanrad_id,
                a.jenispemeriksaanrad_id
               FROM pemeriksaanrad_m a) pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
         JOIN ( SELECT a.kelompokpemeriksaanrad_id,
                a.nama_kelompok
               FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
         JOIN ( SELECT a.jenispemeriksaanrad_id,
                a.jenispemeriksaanrad_nama
               FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
         JOIN ( SELECT a.kelaspelayanan_id,
                a.kelaspelayanan_nama
               FROM kelaspelayanan_m a) kelaspelayanan ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan.kelaspelayanan_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220404_031027_migrate_VCS108_laporanpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220404_031027_migrate_VCS108_laporanpemeriksaanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
