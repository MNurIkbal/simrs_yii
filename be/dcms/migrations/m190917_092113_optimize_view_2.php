<?php

use yii\db\Migration;

/**
 * Class m190917_092113_optimize_view_2
 */
class m190917_092113_optimize_view_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       /* -- View: public.carabayar_v */

        $this->execute('DROP VIEW if exists public.carabayar_v;');

        $this->execute("
    CREATE OR REPLACE VIEW public.carabayar_v AS 
 SELECT carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_m.carabayar_namalainnya,
    carabayar_m.metode_pembayaran,
    fgetnamalookup(carabayar_m.metode_pembayaran) AS metode_pembayaran_nama,
    carabayar_m.carabayar_loket,
    carabayar_m.carabayar_singkatan,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(carabayar_m.groupcarabayar_id) AS groupcarabayar_nama,
    carabayar_m.is_subsidiasuransi,
    carabayar_m.is_subsidipemerintah,
    carabayar_m.is_subsidirs,
    carabayar_m.is_penjamin,
    carabayar_m.kode_antrian,
    carabayar_m.is_active,
    carabayar_m.is_online
   FROM carabayar_m
  WHERE carabayar_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.carabayar_v
  OWNER TO postgres;');


 /* -- View: public.cetakjadwalpoli_v */

    $this->execute('DROP VIEW if exists public.cetakjadwalpoli_v;');


    $this->execute("
        CREATE OR REPLACE VIEW public.cetakjadwalpoli_v AS 
 SELECT jadwalbukapoli_m.ruangan_id,
    ruangan_m.ruangan_nama,
    jadwalbukapoli_m.hari,
    jadwalbukapoli_m.jam_mulai,
    jadwalbukapoli_m.jam_tutup,
    jadwalbukapoli_m.is_active,
    jadwalbukapoli_m.shift_id,
    shift_m.shift_nama,
    shift_m.shift_jamawal,
    shift_m.shift_jamakhir,
    fgetnamalookup(jadwalbukapoli_m.hari) AS hari_nama,
    jadwalbukapoli_m.maxantrian_poli,
    jadwalbukapoli_m.kuota_online
   FROM jadwalbukapoli_m
     LEFT JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
  WHERE jadwalbukapoli_m.is_deleted = false AND ruangan_m.instalasi_id = 1;");


    $this->execute('ALTER TABLE public.cetakjadwalpoli_v
                    OWNER TO postgres;');

 /* -- View: public.cetakpemesanankamar_v */

    $this->execute('DROP VIEW if exists public.cetakpemesanankamar_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.cetakpemesanankamar_v AS 
 SELECT bookingkamar_t.bookingkamar_id,
    bookingkamar_t.bookingkamar_no AS nomor_pemesanan,
    bookingkamar_t.tgl_transaksibooking AS tanggal_pemesanan,
    bookingkamar_t.tgl_bookingkamar AS tanggal_rawat_inap,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jenis_kasus_penyakit,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    ruangan_m.ruangan_nama AS ruangan,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.no_tempattidur AS bed,
    bookingkamar_t.keterangan_booking AS keterangan_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    bookingkamar_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    bookingkamar_t.kamartempattidur_id,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.no_mobile_pasien AS nomor_telepon,
    bookingkamar_t.nama_pemesan AS pemesan,
    pegawai_m.nama_pegawai AS petugas_pendaftaran,
    bookingkamar_t.additional_data
   FROM bookingkamar_t
     LEFT JOIN pasien_m ON bookingkamar_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN kamarruangan_m ON bookingkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON bookingkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pendaftaran_t ON bookingkamar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN kamartempattidur_m ON bookingkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN jeniskasuspenyakit_m ON bookingkamar_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON bookingkamar_t.created_by = pegawai_m.pegawai_id
  WHERE bookingkamar_t.is_active = true AND bookingkamar_t.is_deleted = false;");

    $this->execute('ALTER TABLE public.cetakpemesanankamar_v
  OWNER TO postgres;');


 /* -- View: public.cetakpemesanankamar_v_old */

    $this->execute('DROP VIEW if exists public.cetakpemesanankamar_v_old;');

    $this->execute('CREATE OR REPLACE VIEW public.cetakpemesanankamar_v_old AS 
 SELECT bookingkamar_t.bookingkamar_id,
    bookingkamar_t.bookingkamar_no AS "Nomor Pemesanan",
    bookingkamar_t.tgl_transaksibooking AS "Tanggal Pemesanan",
    bookingkamar_t.tgl_bookingkamar AS "Tanggal Rawat Inap",
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS "Jenis Kasus Penyakit",
    kelaspelayanan_m.kelaspelayanan_nama AS "Kelas Pelayanan",
    ruangan_m.ruangan_nama AS "Ruangan",
    kamarruangan_m.kamarruangan_nokamar AS "Kamar",
    kamartempattidur_m.no_tempattidur AS "Bed",
    bookingkamar_t.keterangan_booking AS "Keterangan Pendaftaran",
    pasien_m.no_rekam_medik AS "No. Rekam Medik",
    pasien_m.nama_pasien AS "Nama Pasien",
    pasien_m.tanggal_lahir AS "Tanggal Lahir",
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    bookingkamar_t.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    bookingkamar_t.kamartempattidur_id,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS "Jenis Kelamin",
    pasien_m.no_mobile_pasien AS "Nomor Telepon",
    bookingkamar_t.nama_pemesan AS "Pemesan",
    pegawai_m.nama_pegawai AS "Petugas Pendaftaran",
    bookingkamar_t.additional_data
   FROM bookingkamar_t
     LEFT JOIN pasien_m ON bookingkamar_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN kamarruangan_m ON bookingkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON bookingkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pendaftaran_t ON bookingkamar_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN kamartempattidur_m ON bookingkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN jeniskasuspenyakit_m ON bookingkamar_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON bookingkamar_t.created_by = pegawai_m.pegawai_id
  WHERE bookingkamar_t.is_active = true AND bookingkamar_t.is_deleted = false;');

    $this->execute('ALTER TABLE public.cetakpemesanankamar_v_old
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_092113_optimize_view_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_092113_optimize_view_2 cannot be reverted.\n";

        return false;
    }
    */
}
