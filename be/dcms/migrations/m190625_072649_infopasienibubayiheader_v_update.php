<?php

use yii\db\Migration;

/**
 * Class m190625_072649_infopasienibubayiheader_v_update
 */
class m190625_072649_infopasienibubayiheader_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
    DROP VIEW public.infopasienibubayiheader_v;
        ');

        $this->execute("
    CREATE OR REPLACE VIEW public.infopasienibubayiheader_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.jenisidentitas,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas_nama,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin AS nama_panggilan,
    pasien_m.nama_ibu,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin::integer AS jenis_kelaminid,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.golongandarah AS golongandarah_id,
    fgetnamalookup(pasien_m.golongandarah) AS golongandarah_nama,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.nama_ayah,
    pasien_m.propinsi_id,
    pasien_m.propinsi_nama,
    pasien_m.kabupaten_id,
    pasien_m.kabupaten_nama,
    pasien_m.kecamatan_id,
    pasien_m.kecamatan_nama,
    pasien_m.kelurahan_id,
    pasien_m.kelurahan_nama,
    pasien_m.warga_negara,
    pasien_m.agama,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warganegara_nama,
    kelahiran.jumlah_bayi
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT pasien_m_1.pasien_id,
            pasien_m_1.no_rekam_medik,
            pasien_m_1.jenisidentitas,
            pasien_m_1.no_identitas_pasien,
            pasien_m_1.nama_pasien,
            pasien_m_1.nama_bin,
            pasien_m_1.nama_ibu,
            pasien_m_1.tempat_lahir,
            pasien_m_1.tanggal_lahir,
            pasien_m_1.jeniskelamin,
            pasien_m_1.golongandarah::integer AS golongandarah,
            pasien_m_1.alamat_pasien,
            pasien_m_1.rt,
            pasien_m_1.rw,
            pasien_m_1.nama_ayah,
            pasien_m_1.propinsi_id,
            propinsi_m.propinsi_nama,
            pasien_m_1.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            pasien_m_1.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pasien_m_1.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pasien_m_1.warga_negara,
            pasien_m_1.agama
           FROM pasien_m pasien_m_1
             LEFT JOIN propinsi_m ON pasien_m_1.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN kabupaten_m ON pasien_m_1.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN kecamatan_m ON pasien_m_1.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN kelurahan_m ON pasien_m_1.kelurahan_id = kelurahan_m.kelurahan_id) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN instalasi_m i_pendaftaran ON r_pendaftaran.instalasi_id = i_pendaftaran.instalasi_id
     LEFT JOIN instalasi_m i_admisi ON r_admisi.instalasi_id = i_admisi.instalasi_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pasien_id,
            max(pendaftaran_t_1.tgl_pendaftaran) AS tgl_pendaftaran
           FROM pendaftaran_t pendaftaran_t_1
          GROUP BY pendaftaran_t_1.pasien_id) pendaftaran_terakhir ON pendaftaran_t.tgl_pendaftaran = pendaftaran_terakhir.tgl_pendaftaran AND pendaftaran_t.pasien_id = pendaftaran_terakhir.pasien_id
     JOIN ( SELECT max(kelahiranbayi_t.kelahiranbayi_id) AS kelahiranbayi_id,
            count(kelahiranbayi_t.kelahiranbayi_id) AS jumlah_bayi,
            pendaftaran_t_1.pendaftaran_id
           FROM pendaftaran_t pendaftaran_t_1
             JOIN kelahiranbayi_t ON pendaftaran_t_1.pendaftaran_id = kelahiranbayi_t.pendaftaran_id AND kelahiranbayi_t.is_deleted = false AND kelahiranbayi_t.pendaftaranbaru_id IS NULL
          GROUP BY pendaftaran_t_1.pendaftaran_id) kelahiran ON pendaftaran_t.pendaftaran_id = kelahiran.pendaftaran_id
  WHERE pendaftaran_t.pasienpulang_id IS NULL AND pasienadmisi_t.pasienpulang_id IS NULL
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.jenisidentitas, (fgetnamalookup(pasien_m.jenisidentitas::integer)), pasien_m.no_identitas_pasien, pasien_m.nama_pasien, pasien_m.nama_bin, pasien_m.nama_ibu, pasien_m.tempat_lahir, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.golongandarah, (fgetnamalookup(pasien_m.golongandarah)), pasien_m.alamat_pasien, pasien_m.rt, pasien_m.rw, pasien_m.nama_ayah, pasien_m.propinsi_id, pasien_m.propinsi_nama, pasien_m.kabupaten_id, pasien_m.kabupaten_nama, pasien_m.kecamatan_id, pasien_m.kecamatan_nama, pasien_m.kelurahan_id, pasien_m.kelurahan_nama, pasien_m.warga_negara, pasien_m.agama, (fgetnamalookup(pasien_m.warga_negara::integer)), kelahiran.jumlah_bayi;

        ");

        $this->execute('
    ALTER TABLE public.infopasienibubayiheader_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190625_072649_infopasienibubayiheader_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_072649_infopasienibubayiheader_v_update cannot be reverted.\n";

        return false;
    }
    */
}
