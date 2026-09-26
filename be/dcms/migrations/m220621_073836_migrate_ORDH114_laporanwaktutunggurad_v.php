<?php

use yii\db\Migration;

/**
 * Class m220621_073836_migrate_ORDH114_laporanwaktutunggurad_v
 */
class m220621_073836_migrate_ORDH114_laporanwaktutunggurad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanwaktutunggurad_v";');
        $this->execute("CREATE VIEW \"public\".\"laporanwaktutunggurad_v\" AS  SELECT 'NON-PAKET'::text AS tipe,
        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
        tindakanpelayanan_t.tindakanpelayanan_id,
        tindakanpelayanan_t.daftartindakan_id,
        pemeriksaanrad_m.pemeriksaanradiologi_id,
        pasienkirimkeunitlain_t.tgl_kirimpasien,
        pendaftaran_t.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasienmasukpenunjang_t.pegawai_id AS dokter_id,
        dokter.nama_pegawai AS dokter,
        pemeriksaanrad_m.jenispemeriksaanrad_id,
        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
        pemeriksaanrad_m.pemeriksaanrad_nama,
        pasienmasukpenunjang_t.tglmasukpenunjang,
        hasilpemeriksaanrad_t.tgl_ambilfoto,
        hasilpemeriksaanrad_t.tgl_hasilrad,
        daftartindakan_m.daftartindakan_nama,
        pasienkirimkeunitlain_t.tglpersetujuan
       FROM pasienmasukpenunjang_t
         LEFT JOIN ( SELECT a.tgl_kirimpasien,
                a.tglpersetujuan,
                a.pasienmasukpenunjang_id
               FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
         JOIN ( SELECT a.tindakanpelayanan_id,
                a.daftartindakan_id,
                a.pasienmasukpenunjang_id
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
         JOIN ( SELECT a.daftartindakan_nama,
                a.daftartindakan_id
               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN ( SELECT a.pemeriksaanradiologi_id,
                a.jenispemeriksaanrad_id,
                a.pemeriksaanrad_nama,
                a.daftartindakan_id,
                a.is_deleted
               FROM pemeriksaanrad_m a) pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
         JOIN ( SELECT a.jenispemeriksaanrad_nama,
                a.jenispemeriksaanrad_id
               FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
         JOIN ( SELECT a.no_pendaftaran,
                a.pendaftaran_id,
                a.pasien_id
               FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN ( SELECT a.no_rekam_medik,
                a.nama_pasien,
                a.pasien_id
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         LEFT JOIN ( SELECT a.nama_pegawai,
                a.pegawai_id
               FROM pegawai_m a) dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
         JOIN ( SELECT a.tgl_ambilfoto,
                a.tgl_hasilrad,
                a.pasienmasukpenunjang_id,
                a.pemeriksaanrad_id,
                a.tindakanpelayanan_id
               FROM hasilpemeriksaanrad_t a) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
      WHERE pemeriksaanrad_m.is_deleted = false
    UNION ALL
     SELECT 'PAKET'::text AS tipe,
        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
        tindakanpelayanan_t.tindakanpelayanan_id,
        tindakanpelayanan_t.daftartindakan_id,
        pemeriksaanrad_m.pemeriksaanradiologi_id,
        pasienkirimkeunitlain_t.tgl_kirimpasien,
        pendaftaran_t.no_pendaftaran,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        pasienmasukpenunjang_t.pegawai_id AS dokter_id,
        dokter.nama_pegawai AS dokter,
        pemeriksaanrad_m.jenispemeriksaanrad_id,
        jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
        pemeriksaanrad_m.pemeriksaanrad_nama,
        pasienmasukpenunjang_t.tglmasukpenunjang,
        hasilpemeriksaanrad_t.tgl_ambilfoto,
        hasilpemeriksaanrad_t.tgl_hasilrad,
        daftartindakan_m.daftartindakan_nama,
        pasienkirimkeunitlain_t.tglpersetujuan
       FROM pasienmasukpenunjang_t
         LEFT JOIN ( SELECT a.tgl_kirimpasien,
                a.tglpersetujuan,
                a.pasienmasukpenunjang_id
               FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
         JOIN ( SELECT a.tindakanpelayanan_id,
                a.daftartindakan_id,
                a.pasienmasukpenunjang_id,
                a.tipepaket_id
               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
         JOIN ( SELECT a.tipepaket_id,
                a.daftartindakan_id
               FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
         JOIN ( SELECT a.daftartindakan_nama,
                a.daftartindakan_id
               FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN ( SELECT a.pemeriksaanradiologi_id,
                a.jenispemeriksaanrad_id,
                a.pemeriksaanrad_nama,
                a.daftartindakan_id,
                a.is_deleted
               FROM pemeriksaanrad_m a) pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
         JOIN ( SELECT a.jenispemeriksaanrad_nama,
                a.jenispemeriksaanrad_id
               FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
         JOIN ( SELECT a.no_pendaftaran,
                a.pendaftaran_id,
                a.pasien_id
               FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         JOIN ( SELECT a.no_rekam_medik,
                a.nama_pasien,
                a.pasien_id
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN ( SELECT a.nama_pegawai,
                a.pegawai_id
               FROM pegawai_m a) dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
         JOIN ( SELECT a.tgl_ambilfoto,
                a.tgl_hasilrad,
                a.pasienmasukpenunjang_id,
                a.pemeriksaanrad_id,
                a.tindakanpelayanan_id
               FROM hasilpemeriksaanrad_t a) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
      WHERE pemeriksaanrad_m.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220621_073836_migrate_ORDH114_laporanwaktutunggurad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220621_073836_migrate_ORDH114_laporanwaktutunggurad_v cannot be reverted.\n";

        return false;
    }
    */
}
