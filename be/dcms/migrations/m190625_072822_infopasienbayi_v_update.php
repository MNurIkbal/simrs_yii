<?php

use yii\db\Migration;

/**
 * Class m190625_072822_infopasienbayi_v_update
 */
class m190625_072822_infopasienbayi_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
    DROP VIEW public.infopasienbayi_v;
        ');

        $this->execute("
    CREATE OR REPLACE VIEW public.infopasienbayi_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    bayi.no_rekam_medik AS rm_bayi,
    bayi.nama_pasien AS nama_bayi,
    bayi.tanggal_lahir,
    bayi.jeniskelamin AS jkbayi_id,
    fgetnamalookup(bayi.jeniskelamin::integer) AS jkbayi_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.umur,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    pasienadmisi_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    pendaftaran_t.pendaftaranibu_id,
    ibu.no_pendaftaran AS no_pendaftaranibu,
    ibu.no_rekam_medik AS rm_ibu,
    ibu.nama_pasien AS nama_ibu,
    ibu.no_identitas_pasien AS no_identitas,
    ibu.alamat_pasien AS alamat,
    ibu.pekerjaan_nama AS pekerjaan,
    ibu.golongan_darah,
    pendaftaran_t.is_skl,
        CASE
            WHEN pendaftaran_t.is_skl = false THEN 'Belum Buat'::text
            ELSE 'Sudah Buat'::text
        END AS status_skl
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.is_deleted = false
     JOIN pasien_m bayi ON pendaftaran_t.pasien_id = bayi.pasien_id AND bayi.is_deleted = false
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN pegawai_m dokter ON pasienadmisi_t.pegawai_id = dokter.pegawai_id
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_identitas_pasien,
            pasien_m.alamat_pasien,
            pekerjaan_m.pekerjaan_nama,
            fgetnamalookup(pasien_m.golongandarah::integer) AS golongan_darah
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasien_m ON pendaftaran_t_1.pasien_id = pasien_m.pasien_id
             LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id) ibu ON pendaftaran_t.pendaftaranibu_id = ibu.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pendaftaran_t.pendaftaranibu_id IS NOT NULL;
        ");

        $this->execute('
    ALTER TABLE public.infopasienbayi_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190625_072822_infopasienbayi_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_072822_infopasienbayi_v_update cannot be reverted.\n";

        return false;
    }
    */
}
