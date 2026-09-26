<?php

use yii\db\Migration;

/**
 * Class m190703_044511_kelahiranbayi_t_update
 */
class m190703_044511_kelahiranbayi_t_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
   DROP VIEW IF exists public.infopasienbayi_v;
        ');

        $this->execute('
   DROP VIEW IF exists public.kelahiranbayi_v;
        ');

         $this->execute('
   DROP VIEW IF exists public.infopasienibubayi_v;
        ');

          $this->execute('
   DROP VIEW IF exists public.infopasienibubayiheader_v;
        ');



        $this->execute('
   DROP TABLE IF exists public.kelahiranbayi_t;
        ');

        $this->execute('
   DROP SEQUENCE IF exists public.kelahiranbayi_t_kelahiranbayi_id_seq;
        ');

        $this->execute('
   CREATE SEQUENCE public.kelahiranbayi_t_kelahiranbayi_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
        ');

        $this->execute('
   ALTER TABLE IF exists public.kelahiranbayi_t_kelahiranbayi_id
  OWNER TO postgres;
        ');

        $this->execute('
   CREATE TABLE public.kelahiranbayi_t
(
  kelahiranbayi_id integer NOT NULL DEFAULT nextval(\'kelahiranbayi_t_kelahiranbayi_id_seq\'::regclass),
  pendaftaran_id integer NOT NULL, -- pendaftaran_id ibu bayi
  pasienadmisi_id integer, 
  pendaftaranbaru_id integer, 
  bayi_urut smallint,
  berat_badan real,
  tinggi_badan real,
  jenis_kelamin smallint, 
  penilaian smallint, 
  kondisi_bayi smallint, 
  normal_tindakan text, 
  asfiksia smallint,
  asfiksia_tindakan text, 
  keterangan_kondisi text,
  is_asi boolean,
  keterangan_asi character varying(255),
  masalah_lain text,
  hasil text,
  additional_data text,
  created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
  created_by integer,
  modified_count integer,
  last_modified_date timestamp(6) without time zone,
  last_modified_by integer,
  is_deleted boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  deleted_date timestamp(6) without time zone,
  deleted_by integer,
  keterangan_cacat text,
  keterangan_hipotermi text,
  CONSTRAINT kelahiranbayi_t_pkey PRIMARY KEY (kelahiranbayi_id)
)
WITH (
  OIDS=FALSE
);
        ');

     $this->execute('
   ALTER TABLE IF exists public.kelahiranbayi_t
  OWNER TO postgres;
        ');

     $this->execute('
   CREATE OR REPLACE VIEW public.infopasienbayi_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    bayi.no_rekam_medik AS rm_bayi,
    bayi.nama_pasien AS nama_bayi,
    bayi.tanggal_lahir,
    bayi.jeniskelamin AS jkbayi_id,
    fgetnamalookup(bayi.jeniskelamin::integer) AS jkbayi_nama,
    bayi.anakke,
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
    bayi.nama_ayah,
    pendaftaran_t.is_skl,
        CASE
            WHEN pendaftaran_t.is_skl = false THEN \'Belum Buat\'::text
            ELSE \'Sudah Buat\'::text
        END AS status_skl,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan
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
     LEFT JOIN kelahiranbayi_t ON pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id
  WHERE pendaftaran_t.pendaftaranibu_id IS NOT NULL;
        ');

 $this->execute('
   ALTER TABLE public.infopasienbayi_v
  OWNER TO postgres;
        ');


  $this->execute('
  CREATE OR REPLACE VIEW public.kelahiranbayi_v AS 
 SELECT kelahiranbayi_t.kelahiranbayi_id,
    kelahiranbayi_t.pendaftaran_id,
    kelahiranbayi_t.pasienadmisi_id,
    kelahiranbayi_t.pendaftaranbaru_id,
    pendaftaran_t.no_pendaftaran,
        CASE
            WHEN kelahiranbayi_t.pendaftaranbaru_id IS NULL THEN \'BELUM TERDAFTAR\'::text
            ELSE \'SUDAH TERDAFTAR\'::text
        END AS status_pendaftaran,
    kelahiranbayi_t.bayi_urut,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan,
    fgetnamalookup(kelahiranbayi_t.jenis_kelamin::integer) AS jenis_kelamin,
    fgetnamalookupkeperawatan(kelahiranbayi_t.penilaian::integer) AS penilaian,
    fgetnamalookupkeperawatan(kelahiranbayi_t.kondisi_bayi::integer) AS kondisi_bayi,
    fgetnamalookupkeperawatan(kelahiranbayi_t.asfiksia::integer) AS normal_tindakan,
    kelahiranbayi_t.is_asi,
    kelahiranbayi_t.keterangan_asi,
    kelahiranbayi_t.masalah_lain,
    kelahiranbayi_t.hasil
   FROM kelahiranbayi_t
     LEFT JOIN pendaftaran_t ON kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id
  WHERE kelahiranbayi_t.is_deleted = false;
        ');

   $this->execute('
   ALTER TABLE public.kelahiranbayi_v
  OWNER TO postgres;
        ');

   $this->execute('
   CREATE OR REPLACE VIEW public.infopasienibubayi_v AS 
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
    pasien_m.no_telepon_pasien,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warganegara_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_id
            ELSE r_admisi.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN r_pendaftaran.ruangan_nama
            ELSE r_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN i_pendaftaran.instalasi_nama
            ELSE i_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
    pendaftaran_terakhir.tgl_pendaftaran,
    kelahiranbayi_t.bayi_urut,
    kelahiranbayi_t.berat_badan,
    kelahiranbayi_t.tinggi_badan,
    kelahiranbayi_t.jenis_kelamin AS jeniskelamin_id_bayi,
    fgetnamalookup(kelahiranbayi_t.jenis_kelamin::integer) AS jeniskelamin_bayi,
    fgetnamalookupkeperawatan(kelahiranbayi_t.kondisi_bayi::integer) AS kondisi_bayi,
    kelahiranbayi_t.pendaftaranbaru_id,
    pendaftaran_bayi.no_pendaftaran AS no_pendaftaranbayi,
    kelahiranbayi_t.kelahiranbayi_id
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
            pasien_m_1.agama,
            pasien_m_1.no_telepon_pasien
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
     JOIN kelahiranbayi_t ON kelahiranbayi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND kelahiranbayi_t.is_deleted = false
     LEFT JOIN pendaftaran_t pendaftaran_bayi ON kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_bayi.pendaftaran_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pasienpulang_t.carakeluar_id = 5 OR pendaftaran_t.pasienpulang_id IS NULL AND pasienadmisi_t.pasienpulang_id IS NULL AND kelahiranbayi_t.pendaftaranbaru_id IS NULL;
        ');

   $this->execute('
   ALTER TABLE public.infopasienibubayi_v
  OWNER TO postgres;
        ');

   $this->execute('
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
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pasienpulang_t.carakeluar_id = 5 OR pendaftaran_t.pasienpulang_id IS NULL AND pasienadmisi_t.pasienpulang_id IS NULL
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.jenisidentitas, (fgetnamalookup(pasien_m.jenisidentitas::integer)), pasien_m.no_identitas_pasien, pasien_m.nama_pasien, pasien_m.nama_bin, pasien_m.nama_ibu, pasien_m.tempat_lahir, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pasien_m.golongandarah, (fgetnamalookup(pasien_m.golongandarah)), pasien_m.alamat_pasien, pasien_m.rt, pasien_m.rw, pasien_m.nama_ayah, pasien_m.propinsi_id, pasien_m.propinsi_nama, pasien_m.kabupaten_id, pasien_m.kabupaten_nama, pasien_m.kecamatan_id, pasien_m.kecamatan_nama, pasien_m.kelurahan_id, pasien_m.kelurahan_nama, pasien_m.warga_negara, pasien_m.agama, (fgetnamalookup(pasien_m.warga_negara::integer)), kelahiran.jumlah_bayi;
        ');

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
        echo "m190703_044511_kelahiranbayi_t_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_044511_kelahiranbayi_t_update cannot be reverted.\n";

        return false;
    }
    */
}
