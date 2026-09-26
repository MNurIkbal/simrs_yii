<?php

use yii\db\Migration;

/**
 * Class m190719_093637_orderpenunjang_v_old2
 */
class m190719_093637_orderpenunjang_v_old2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP VIEW IF exists public.orderpenunjang_v_old2;
        ');

          $this->execute('
         CREATE OR REPLACE VIEW public.orderpenunjang_v_old2 AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.instalasi_nama,
    pendaftaran_t.ruangan_nama,
    pendaftaran_t.no_rekam_medik,
    pendaftaran_t.nama_pasien,
    instalasi_m.instalasi_nama AS instalasi_penunjang,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    pendaftaran_t.tanggal_lahir,
    fgetnamalookup(pendaftaran_t.jeniskelamin::integer) AS jenis_kelamin,
    pendaftaran_t.carabayar_nama,
    pendaftaran_t.penjamin_nama,
    permintaankepenunjang_t.tglpermintaankepenunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    permintaankepenunjang_t.pemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    permintaankepenunjang_t.pemeriksaanrad_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    permintaankepenunjang_t.operasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.operasi_nama,
    permintaankepenunjang_t.tarif_pelayanan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_cytotindakan,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.instruksi_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
   FROM permintaankepenunjang_t
     JOIN pasienkirimkeunitlain_t ON permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT kunjunganrs.pendaftaran_id,
            kunjunganrs.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pasien_m.jeniskelamin,
            ruangan.ruangan_nama,
            instalasi.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama
           FROM pendaftaran_t kunjunganrs
             JOIN pasien_m ON kunjunganrs.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ruangan ON kunjunganrs.ruangan_id = ruangan.ruangan_id
             JOIN instalasi_m instalasi ON ruangan.instalasi_id = instalasi.instalasi_id
             JOIN carabayar_m ON kunjunganrs.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON kunjunganrs.penjamin_id = penjamin_m.penjamin_id) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     LEFT JOIN jenispemeriksaanlab_m ON jenispemeriksaanlab_m.jenispemeriksaanlab_id = pemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON jenispemeriksaanrad_m.jenispemeriksaanrad_id = pemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.instalasi_nama,
    pasienadmisi_t.ruangan_nama,
    pendaftaran_t.no_rekam_medik,
    pendaftaran_t.nama_pasien,
    instalasi_m.instalasi_nama AS instalasi_penunjang,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    pendaftaran_t.tanggal_lahir,
    fgetnamalookup(pendaftaran_t.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.carabayar_nama,
    pasienadmisi_t.penjamin_nama,
    permintaankepenunjang_t.tglpermintaankepenunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    permintaankepenunjang_t.pemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    permintaankepenunjang_t.pemeriksaanrad_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    permintaankepenunjang_t.operasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.operasi_nama,
    permintaankepenunjang_t.tarif_pelayanan,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tarif_cytotindakan,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.instruksi_id,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
   FROM permintaankepenunjang_t
     JOIN pasienkirimkeunitlain_t ON permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT pasienadmisi_t_1.pendaftaran_id,
            pasienadmisi_t_1.pasienadmisi_id,
            ruangan_m_1.ruangan_nama,
            instalasi_m_1.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama
           FROM pasienadmisi_t pasienadmisi_t_1
             JOIN ruangan_m ruangan_m_1 ON pasienadmisi_t_1.ruangan_id = ruangan_m_1.ruangan_id
             JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
             JOIN carabayar_m ON pasienadmisi_t_1.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t_1.penjamin_id = penjamin_m.penjamin_id) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT daftar_ri.pendaftaran_id,
            daftar_ri.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir,
            pasien_m.jeniskelamin
           FROM pendaftaran_t daftar_ri
             JOIN pasien_m ON daftar_ri.pasien_id = pasien_m.pasien_id) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pemeriksaanlab_m ON permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
     LEFT JOIN jenispemeriksaanlab_m ON jenispemeriksaanlab_m.jenispemeriksaanlab_id = pemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON jenispemeriksaanrad_m.jenispemeriksaanrad_id = pemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN operasi_m ON permintaankepenunjang_t.operasi_id = operasi_m.operasi_id
     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id;
        ');

           $this->execute('
         ALTER TABLE public.orderpenunjang_v_old2
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_093637_orderpenunjang_v_old2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_093637_orderpenunjang_v_old2 cannot be reverted.\n";

        return false;
    }
    */
}
