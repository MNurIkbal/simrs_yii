<?php

use yii\db\Migration;

/**
 * Class m190918_070446_optimize_view_5
 */
class m190918_070446_optimize_view_5 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*infopasienrad_v*/
        $this->execute('DROP VIEW if exists public.infopasienrad_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienrad_v AS 
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienmasukpenunjang_t
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    rujukan_t.no_rujukan,
    rujukan_t.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    rujukan_t.rujukandari_id AS ruanganasal_id,
    perujuk_m.namaperujuk AS ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
  WHERE pendaftaran_t.instalasi_id = 5 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.pasien_id,
    NULL::integer AS pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
  WHERE pendaftaran_t.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL;
");

        $this->execute('ALTER TABLE public.infoorderanrad_v
            OWNER TO postgres;');

/*infoorderanbedah_v*/

     $this->execute('DROP VIEW if exists public.infoorderanbedah_v;');

      $this->execute("
        CREATE OR REPLACE VIEW public.infoorderanbedah_v AS 
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12;");

       $this->execute('ALTER TABLE public.infoorderanbedah_v
  OWNER TO postgres;');

/*infoorderanlab_v*/

    $this->execute('DROP VIEW if exists public.infoorderanlab_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.infoorderanlab_v AS 
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4;");

    $this->execute('ALTER TABLE public.infoorderanlab_v
  OWNER TO postgres;');

/*infoorderanrad_v*/

    $this->execute('DROP VIEW if exists public.infoorderanrad_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.infoorderanrad_v AS 
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5;");

    $this->execute('ALTER TABLE public.infoorderanrad_v
  OWNER TO postgres;');

/*inpostoperasi_v*/

     $this->execute('DROP VIEW if exists public.inpostoperasi_v;');

      $this->execute("
        CREATE OR REPLACE VIEW public.inpostoperasi_v AS 
 SELECT inpostoperasi_t.inpostoperasi_id,
    inpostoperasi_t.pasienmasukpenunjang_id,
    inpostoperasi_t.is_surgicalsavety,
    inpostoperasi_t.dokterbedah_id,
    dr_bedah.nama_pegawai AS dok_bedah,
    inpostoperasi_t.dokteranastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    inpostoperasi_t.masuk_kamar,
    inpostoperasi_t.mulai_anastesi,
    inpostoperasi_t.selesai_anastesi,
    inpostoperasi_t.mulai_operasi,
    inpostoperasi_t.selesai_operasi,
    inpostoperasi_t.set_instrumen,
    instrumen.obatalkes_nama AS instrumen_nama,
    inpostoperasi_t.penunjang_khusus_id,
    pen_khusus.obatalkes_nama AS penunjang_khusus,
    inpostoperasi_t.perlengkapan_pribadi,
    inpostoperasi_t.is_diathermy,
    inpostoperasi_t.kondisi_kulit_sebelum,
    fgetnamalookup(inpostoperasi_t.kondisi_kulit_sebelum) AS kulit_sebelum,
    inpostoperasi_t.kondisi_kulit_setelah,
    fgetnamalookup(inpostoperasi_t.kondisi_kulit_setelah) AS kulit_setelah,
    inpostoperasi_t.posisi_operasi,
    fgetnamalookup(inpostoperasi_t.posisi_operasi) AS posisi_op,
    inpostoperasi_t.kateter_urin,
    inpostoperasi_t.pencucian_operasi,
    fgetnamalookup(inpostoperasi_t.pencucian_operasi) AS cuci_operasi,
    inpostoperasi_t.posisi_elektroda,
    fgetnamalookup(inpostoperasi_t.posisi_elektroda) AS pos_elektroda,
    inpostoperasi_t.fiksasi_balon,
    inpostoperasi_t.pemakaian_implan,
    inpostoperasi_t.lokasi_drainvacum,
    inpostoperasi_t.lokasi_drainpenrose,
    inpostoperasi_t.lokasi_drainselang,
    inpostoperasi_t.is_jaringantubuh,
    inpostoperasi_t.jenis_jaringan,
    inpostoperasi_t.is_diserahkan,
    inpostoperasi_t.penerima,
    inpostoperasi_t.pegawai_pemberi_id,
    pegawai_pemberi.nama_pegawai AS pegawai_pemberi,
    inpostoperasi_t.is_recovery,
    inpostoperasi_t.jam_masuk_rec,
    inpostoperasi_t.jam_keluar_rec,
    inpostoperasi_t.kembali_ruangan_id,
    kembali_ruang.ruangan_nama AS ruang_kembali,
    inpostoperasi_t.kesadaran_umum,
    fgetnamalookup(inpostoperasi_t.kesadaran_umum) AS kes_umum,
    inpostoperasi_t.kesadaran_umum_lain,
    inpostoperasi_t.tingkat_kesadaran,
    fgetnamalookup(inpostoperasi_t.tingkat_kesadaran) AS tingkat_kes,
    inpostoperasi_t.tingkat_kesadaran_lain,
    inpostoperasi_t.jalan_napas,
    fgetnamalookup(inpostoperasi_t.jalan_napas) AS jln_napas,
    inpostoperasi_t.jalan_napas_lain,
    inpostoperasi_t.terapi_oksigen,
    fgetnamalookup(inpostoperasi_t.terapi_oksigen) AS terapi_oks,
    inpostoperasi_t.terapi_oksigen_lain,
    inpostoperasi_t.l_mnt,
    inpostoperasi_t.kulit_datang,
    fgetnamalookup(inpostoperasi_t.kulit_datang) AS kulit_dtg,
    inpostoperasi_t.kulit_datang_lain,
    inpostoperasi_t.kulit_keluar,
    fgetnamalookup(inpostoperasi_t.kulit_keluar) AS kulit_klr,
    inpostoperasi_t.kulit_keluar_lain,
    inpostoperasi_t.sirkulasi_badan,
    fgetnamalookup(inpostoperasi_t.sirkulasi_badan) AS sirkulasi_bdn,
    inpostoperasi_t.sirkulasi_badan_lain,
    inpostoperasi_t.area_luka,
    inpostoperasi_t.is_skrining_nyeri,
    inpostoperasi_t.ket_skrining,
    inpostoperasi_t.skala_nyeri,
    inpostoperasi_t.lokasi,
    inpostoperasi_t.metode_nyeri,
    fgetnamalookup(inpostoperasi_t.metode_nyeri) AS metod_nyeri,
    inpostoperasi_t.resiko_jatuh,
    inpostoperasi_t.barang_pasien,
    inpostoperasi_t.is_pasanginfus,
    inpostoperasi_t.keterangan AS keterangan_post,
    inpostoperasi_t.pemberitahu_perawat,
    inpostoperasi_t.perawat_datang
   FROM inpostoperasi_t
     LEFT JOIN pegawai_m dr_bedah ON inpostoperasi_t.dokterbedah_id = dr_bedah.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON inpostoperasi_t.dokteranastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN obatalkes_m instrumen ON inpostoperasi_t.set_instrumen::integer = instrumen.obatalkes_id
     LEFT JOIN obatalkes_m pen_khusus ON inpostoperasi_t.penunjang_khusus_id = pen_khusus.obatalkes_id
     LEFT JOIN pegawai_m pegawai_pemberi ON inpostoperasi_t.pegawai_pemberi_id = pegawai_pemberi.pegawai_id
     LEFT JOIN ruangan_m kembali_ruang ON inpostoperasi_t.kembali_ruangan_id = kembali_ruang.ruangan_id;
");

       $this->execute('ALTER TABLE public.inpostoperasi_v
  OWNER TO postgres;');
    
/*infojamkunjunganpoli_v*/

$this->execute('DROP VIEW if exists public.infojamkunjunganpoli_v;');

  $this->execute("
    CREATE OR REPLACE VIEW public.infojamkunjunganpoli_v AS 
 SELECT jadwalbukapoli_id,
    instalasi_id,
    instalasi_nama,
    ruangan_id,
    ruangan_nama,
    hari_id,
    hari,
    waktu,
    jam_mulai,
    jam_tutup,
    shift_id,
    shift_nama,
    kuota_offline,
    y.kuota_tersedia_offline,
    kuota_online,
    x.kuota_tersedia_online,
    x.is_active
   FROM ( SELECT jadwalbukapoli_m.jadwalbukapoli_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jadwalbukapoli_m.ruangan_id,
            ruangan_m.ruangan_nama,
            jadwalbukapoli_m.waktu_pelayanan AS waktu,
            jadwalbukapoli_m.jam_mulai,
            jadwalbukapoli_m.jam_tutup,
            jadwalbukapoli_m.hari AS hari_id,
            fgetnamalookup(jadwalbukapoli_m.hari) AS hari,
            jadwalbukapoli_m.is_active,
            COALESCE(shift_m.shift_id, 0) AS shift_id,
            COALESCE(shift_m.shift_nama, ''::character varying) AS shift_nama,
            jadwalbukapoli_m.maxantrian_poli AS kuota_offline,
            jadwalbukapoli_m.kuota_online,
            kuotadokter_r.kuota_tersedia AS kuota_tersedia_online
           FROM jadwalbukapoli_m
             JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
             JOIN kuotadokter_r ON jadwalbukapoli_m.jadwalbukapoli_id = kuotadokter_r.jadwalbukapoli_id AND kuotadokter_r.is_online IS TRUE
          WHERE jadwalbukapoli_m.is_deleted IS FALSE) x
     FULL JOIN ( SELECT jadwalbukapoli_m.jadwalbukapoli_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jadwalbukapoli_m.ruangan_id,
            ruangan_m.ruangan_nama,
            jadwalbukapoli_m.waktu_pelayanan AS waktu,
            jadwalbukapoli_m.jam_mulai,
            jadwalbukapoli_m.jam_tutup,
            jadwalbukapoli_m.hari AS hari_id,
            fgetnamalookup(jadwalbukapoli_m.hari) AS hari,
            jadwalbukapoli_m.is_active,
            COALESCE(shift_m.shift_id, 0) AS shift_id,
            COALESCE(shift_m.shift_nama, ''::character varying) AS shift_nama,
            jadwalbukapoli_m.maxantrian_poli AS kuota_offline,
            jadwalbukapoli_m.kuota_online,
            kuotadokter_r.kuota_tersedia AS kuota_tersedia_offline
           FROM jadwalbukapoli_m
             JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
             JOIN kuotadokter_r ON jadwalbukapoli_m.jadwalbukapoli_id = kuotadokter_r.jadwalbukapoli_id AND kuotadokter_r.is_online IS FALSE
          WHERE jadwalbukapoli_m.is_deleted IS FALSE) y USING (jadwalbukapoli_id, instalasi_id, instalasi_nama, ruangan_id, ruangan_nama, hari_id, hari, waktu, jam_mulai, jam_tutup, shift_id, shift_nama, kuota_offline, kuota_online);
");

  $this->execute('ALTER TABLE public.infojamkunjunganpoli_v
  OWNER TO postgres;');


/*infopasiennonbpjs_v*/

  $this->execute('DROP VIEW if exists public.infopasiennonbpjs_v;');

  $this->execute("
    CREATE OR REPLACE VIEW public.infopasiennonbpjs_v AS 
 SELECT rincian.tipe,
    rincian.pasien_id,
    rincian.no_rekam_medik,
    rincian.nama_pasien,
    rincian.pendaftaran_id,
    rincian.tgl_pendaftaran,
    rincian.tglpasienpulang,
    rincian.no_pendaftaran,
    rincian.ruangan_id,
    rincian.ruangan_nama,
    rincian.instalasi_id,
    rincian.instalasi_nama,
    pembayaranpelayanan_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pembayaranpelayanan_t.penjamin_id,
    penjamin_m.penjamin_nama,
    rincian.kelaspelayanan_id,
    rincian.kelaspelayanan_nama,
    rincian.jeniskasuspenyakit_id,
    rincian.jeniskasuspenyakit_nama,
    rincian.pegawai_id,
    rincian.dokter,
    rincian.status_bayar,
    rincian_header_tagihan_pasien.total_tagihan,
    rincianpasien_detail.total_sdh_bayar,
    sum(cektagihanpejamin.total) AS total_sisa_tagihan,
    sum(cektagihanpejamin.total) AS total_asuransi,
    rincian.is_skd,
        CASE
            WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
            WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
            ELSE NULL::text
        END AS status_skd,
    rincian.pasienadmisi_id,
    rincian.status_verifikasi,
    rincian.lookup_name AS status_verif
   FROM ( SELECT 'RD'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name
           FROM pasien_m
             JOIN pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
             JOIN carabayar_m carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6]))
        UNION ALL
         SELECT 'RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pasienadmisi_t.is_skd,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS lookup_name
           FROM pasien_m
             JOIN pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN carabayar_m carabayar_m_1 ON pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6]))
        UNION ALL
         SELECT 'RD-RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name
           FROM pasien_m
             JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    pendaftaran_t_1.tgl_pendaftaran,
                    pendaftaran_t_1.no_pendaftaran,
                        CASE
                            WHEN pasienadmisi_t.carabayar_id IS NULL THEN pendaftaran_t_1.carabayar_id
                            ELSE pasienadmisi_t.carabayar_id
                        END AS carabayar_id,
                        CASE
                            WHEN pasienadmisi_t.penjamin_id IS NULL THEN pendaftaran_t_1.penjamin_id
                            ELSE pasienadmisi_t.penjamin_id
                        END AS penjamin_id,
                        CASE
                            WHEN pasienadmisi_t.kelaspelayanan_id IS NULL THEN pendaftaran_t_1.kelaspelayanan_id
                            ELSE pasienadmisi_t.kelaspelayanan_id
                        END AS kelaspelayanan_id,
                    pendaftaran_t_1.jeniskasuspenyakit_id,
                        CASE
                            WHEN pasienadmisi_t.pegawai_id IS NULL THEN pendaftaran_t_1.pegawai_id
                            ELSE pasienadmisi_t.pegawai_id
                        END AS pegawai_id,
                        CASE
                            WHEN pasienadmisi_t.ruangan_id IS NULL THEN pendaftaran_t_1.ruangan_id
                            ELSE pasienadmisi_t.ruangan_id
                        END AS ruangan_id,
                        CASE
                            WHEN pasienadmisi_t.ruangan_id IS NULL THEN pendaftaran_t_1.instalasi_id
                            ELSE 3
                        END AS instalasi_id,
                        CASE
                            WHEN pasienadmisi_t.pasienpulang_id IS NULL THEN pendaftaran_t_1.pasienpulang_id
                            ELSE pasienadmisi_t.pasienpulang_id
                        END AS pasienpulang_id,
                        CASE
                            WHEN pasienadmisi_t.is_skd IS NULL THEN pendaftaran_t_1.is_skd
                            ELSE pasienadmisi_t.is_skd
                        END AS is_skd,
                        CASE
                            WHEN pasienadmisi_t.status_verifikasi IS NULL THEN pendaftaran_t_1.status_verifikasi
                            ELSE pasienadmisi_t.status_verifikasi
                        END AS status_verifikasi,
                    pendaftaran_t_1.status_bayar,
                    pendaftaran_t_1.pasien_id
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id
                     JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                          WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                          GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE pendaftaran_t_1.instalasi_id = 2) pendaftaran_t ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) rincian
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            tagihan.pasienadmisi_id,
            pasien_m.no_rekam_medik,
            sum(tagihan.tagihan_tindakan_obat) AS total_tagihan,
            sum(tagihan.tagihan_sudah_bayar) AS total_sdh_bayar,
            sum(tagihan.tagihan_tindakan_obat) - sum(tagihan.tagihan_sudah_bayar) - sum(tagihan.tagihan_asuransi) AS total_sisa_tagihan,
            sum(tagihan.tagihan_uang_muka) AS total_uang_muka,
            sum(tagihan.tagihan_biaya_admin) AS total_administrasi,
            sum(tagihan.tagihan_pembulatan) AS total_pembulatan,
            sum(tagihan.tagihan_asuransi) AS total_asuransi,
            sum(tagihan.pembayaran_pasien) AS total_pembayaran_pasien
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    0 AS tagihan_tindakan_obat,
                    0 AS tagihan_sudah_bayar,
                    sum(bayaruangmuka_t.jumlah_uangmuka) AS tagihan_uang_muka,
                    0 AS tagihan_biaya_admin,
                    0 AS tagihan_pembulatan,
                    0 AS tagihan_asuransi,
                    0 AS pembayaran_pasien,
                    pendaftaran_t.pasien_id
                   FROM pendaftaran_t
                     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
                  GROUP BY pendaftaran_t.pendaftaran_id, bayaruangmuka_t.jumlah_uangmuka, pendaftaran_t.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    sum(pembayaranpelayanan_t_1.total_biayapelayanan) AS tagihan_tindakan_obat,
                    sum(pembayaranpelayanan_t_1.total_terbayar) AS tagihan_sudah_bayar,
                    0 AS tagihan_uang_muka,
                    sum(pembayaranpelayanan_t_1.biaya_administrasi) AS tagihan_biaya_admin,
                    sum(pembayaranpelayanan_t_1.pembulatan) AS tagihan_pembulatan,
                    sum(pembayaranpelayanan_t_1.total_subsidiasuransi) AS tagihan_asuransi,
                    sum(pembayaranpelayanan_t_1.total_bayartindakan) AS pembayaran_pasien,
                    pendaftaran_t.pasien_id
                   FROM pendaftaran_t
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  GROUP BY pendaftaran_t.pendaftaran_id, pembayaranpelayanan_t_1.biaya_administrasi, pembayaranpelayanan_t_1.pembulatan, pendaftaran_t.pasienadmisi_id, pembayaranpelayanan_t_1.total_subsidiasuransi) tagihan
             LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
          GROUP BY tagihan.pendaftaran_id, tagihan.pasienadmisi_id, pasien_m.no_rekam_medik) rincianpasien_detail ON rincian.pendaftaran_id = rincianpasien_detail.pendaftaran_id
     LEFT JOIN pembayaranpelayanan_t ON rincian.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     LEFT JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT hitung.pendaftaran_id,
                CASE
                    WHEN hitung.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
                    ELSE NULL::integer
                END AS pasienadmisi_id,
            hitung.instalasi_id,
            hitung.instalasi_nama,
            sum(hitung.tarif) AS total,
            hitung.penjamin_id,
            penjamin_m_1.penjamin_nama
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    tindakanpelayanan_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    tindakanpelayanan_t.penjamin_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                   FROM tindakanpelayanan_t
                     LEFT JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
                  WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
                UNION ALL
                 SELECT tindakanpelayanan_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    tindakanpelayanan_t.penjamin_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                   FROM tindakanpelayanan_t
                     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    obatalkespasien_t.penjamin_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sum
                   FROM obatalkespasien_t
                     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE obatalkespasien_t.resepturdetail_id IS NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    obatalkespasien_t.penjamin_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sum
                   FROM obatalkespasien_t
                     LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                     LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                     LEFT JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id) hitung
             JOIN pendaftaran_t ON hitung.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN penjamin_m penjamin_m_1 ON hitung.penjamin_id = penjamin_m_1.penjamin_id
          GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t.pasienadmisi_id, pendaftaran_t.instalasi_id, hitung.penjamin_id, penjamin_m_1.penjamin_nama) cektagihanpejamin ON rincian.pendaftaran_id = cektagihanpejamin.pendaftaran_id AND pembayaranpelayanan_t.penjamin_id = cektagihanpejamin.penjamin_id
     LEFT JOIN rincian_header_tagihan_pasien ON rincian.pendaftaran_id = rincian_header_tagihan_pasien.pendaftaran_id
  WHERE pembayaranpelayanan_t.carabayar_id <> ALL (ARRAY[5, 6])
  GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, pembayaranpelayanan_t.carabayar_id, carabayar_m.carabayar_nama, pembayaranpelayanan_t.penjamin_id, penjamin_m.penjamin_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian_header_tagihan_pasien.total_tagihan, rincianpasien_detail.total_sdh_bayar, rincian.is_skd, (
        CASE
            WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
            WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
            ELSE NULL::text
        END), rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name;");

  $this->execute('ALTER TABLE public.infopasiennonbpjs_v
  OWNER TO postgres;');


  /*infotagihanpasienpulang_v*/

  $this->execute('DROP VIEW if exists public.infotagihanpasienpulang_v;');

  $this->execute("
    CREATE OR REPLACE VIEW public.infotagihanpasienpulang_v AS 
 SELECT gabung.pendaftaran_id,
    gabung.pasienpulang_id,
    gabung.pasienpulangri_id,
    gabung.tglpasienpulang,
    gabung.no_pendaftaran,
    gabung.instalasi_id,
    gabung.instalasi_nama,
    gabung.ruanganakhir_id AS ruangan_id,
    gabung.ruangan_nama,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.carabayar_id,
    gabung.carabayar_nama,
    gabung.penjamin_id,
    gabung.penjamin_nama,
    gabung.jeniskasuspenyakit_nama,
    gabung.status_bayar,
    gabung.kelaspelayanan_nama,
    gabung.nama_pegawai AS dokter,
    COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) AS total_tindakan,
    COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision) AS total_obat,
    (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer AS total_tagihan,
    gabung.pegawai_id,
    gabung.photopasien,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.jeniskelamin,
    gabung.jenis_kelamin,
    gabung.tgl_pendaftaran
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            jk.lookup_name AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
           FROM pendaftaran_t
             LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.instalasi_id = 1 OR pendaftaran_t.instalasi_id = 2 AND pasienpulang_t.carakeluar_id <> 5
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, jk.lookup_name, pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            status_bayar.lookup_name AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
            NULL::double precision AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN lookup_m status_bayar ON pendaftaran_t.status_bayar = status_bayar.lookup_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, status_bayar.lookup_name, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            NULL::integer AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.instalasi_id = 1 OR pendaftaran_t.instalasi_id = 2 AND pasienpulang_t.carakeluar_id <> 5
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai,
            NULL::double precision AS total_tindakan,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id
           FROM pendaftaran_t
             LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
          WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id) gabung
  WHERE gabung.sudah_bayar IS NULL
  GROUP BY gabung.kelaspelayanan_nama, gabung.nama_pegawai, gabung.status_bayar, gabung.pendaftaran_id, gabung.pasienpulang_id, gabung.tglpasienpulang, gabung.no_pendaftaran, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruanganakhir_id, gabung.ruangan_nama, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_nama, gabung.pegawai_id, gabung.pasienpulangri_id, gabung.photopasien, gabung.tanggal_lahir, gabung.umur, gabung.jeniskelamin, gabung.jenis_kelamin, gabung.tgl_pendaftaran;
");

  $this->execute('ALTER TABLE public.infotagihanpasienpulang_v
  OWNER TO postgres;');

/* infopasienmeninggal_v */
    
      $this->execute('DROP VIEW if exists public.infopasienmeninggal_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienmeninggal_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    peg_ruangan.nama_pegawai AS pegawai_ruangan,
    jabatan_m.jabatan_nama,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    ins_asal.instalasi_nama AS instalasi_asal,
    pendaftaran_t.penanggungjawab_id,
    persetujuanjenazah_t.nama_pj AS penanggungjawab_nama,
    persetujuanjenazah_t.umur AS umur_pj,
    fgetnamalookup(persetujuanjenazah_t.jeniskelamin_id) AS jenis_kelamin_pj,
    persetujuanjenazah_t.alamat,
    persetujuanjenazah_t.no_kontak,
    fgetnamalookup(persetujuanjenazah_t.hubungan_keluarga) AS hubungan_kel,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    peg_jenazah.nama_pegawai AS pegawai_jenazah,
    jab_jenazah.jabatan_nama AS jabatan_pegjenazah,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status_periksa_nama,
    diagnosa.diagnosa_utama ->> 'text'::text AS diagnosa_nama,
    ambiljenazah_t.tgl_pengambilan,
    persetujuanjenazah_t.kondisi,
    ambiljenazah_t.tgl_lahir AS tgl_lahir_pj,
    ambiljenazah_t.tempat_lahir AS tempat_lahir_pj
   FROM pendaftaran_t
     JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4 AND pasienpulang_t.pasienbatalpulang_id IS NULL
     JOIN loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m peg_ruangan ON loginpemakai_k.pegawai_id = peg_ruangan.pegawai_id
     JOIN jabatan_m ON peg_ruangan.jabatan_id = jabatan_m.jabatan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN instalasi_m ins_asal ON ruangan_m.instalasi_id = ins_asal.instalasi_id
     LEFT JOIN persetujuanjenazah_t ON pendaftaran_t.pendaftaran_id = persetujuanjenazah_t.pendaftaran_id AND persetujuanjenazah_t.is_deleted = false
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38 AND pasienmasukpenunjang_t.is_deleted = false
     JOIN pegawai_m peg_jenazah ON pasienmasukpenunjang_t.pegawai_id = peg_jenazah.pegawai_id
     JOIN jabatan_m jab_jenazah ON peg_jenazah.jabatan_id = jab_jenazah.jabatan_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ambiljenazah_t ON pendaftaran_t.pendaftaran_id = ambiljenazah_t.pendaftaran_id
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.pasienpulang_id,
    pasienpulang_t.tgl_meninggal,
    peg_ruangan.nama_pegawai AS pegawai_ruangan,
    jabatan_m.jabatan_nama,
    pendaftaran_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.alamat_pasien,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_nama,
    ins_asal.instalasi_nama AS instalasi_asal,
    pendaftaran_t.penanggungjawab_id,
    persetujuanjenazah_t.nama_pj AS penanggungjawab_nama,
    persetujuanjenazah_t.umur AS umur_pj,
    fgetnamalookup(persetujuanjenazah_t.jeniskelamin_id) AS jenis_kelamin_pj,
    persetujuanjenazah_t.alamat,
    persetujuanjenazah_t.no_kontak,
    fgetnamalookup(persetujuanjenazah_t.hubungan_keluarga) AS hubungan_kel,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    peg_jenazah.nama_pegawai AS pegawai_jenazah,
    jab_jenazah.jabatan_nama AS jabatan_pegjenazah,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status_periksa_nama,
    diagnosa.diagnosa_utama ->> 'text'::text AS diagnosa_nama,
    ambiljenazah_t.tgl_pengambilan,
    persetujuanjenazah_t.kondisi,
    ambiljenazah_t.tgl_lahir AS tgl_lahir_pj,
    ambiljenazah_t.tempat_lahir AS tempat_lahir_pj
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id = 4 AND pasienpulang_t.pasienbatalpulang_id IS NULL
     JOIN loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m peg_ruangan ON loginpemakai_k.pegawai_id = peg_ruangan.pegawai_id
     JOIN jabatan_m ON peg_ruangan.jabatan_id = jabatan_m.jabatan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
     JOIN instalasi_m ins_asal ON ruangan_m.instalasi_id = ins_asal.instalasi_id
     LEFT JOIN persetujuanjenazah_t ON pendaftaran_t.pendaftaran_id = persetujuanjenazah_t.pendaftaran_id AND persetujuanjenazah_t.is_deleted = false
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id AND pasienmasukpenunjang_t.ruangan_id = 38 AND pasienmasukpenunjang_t.is_deleted = false
     JOIN pegawai_m peg_jenazah ON pasienmasukpenunjang_t.pegawai_id = peg_jenazah.pegawai_id
     JOIN jabatan_m jab_jenazah ON peg_jenazah.jabatan_id = jab_jenazah.jabatan_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN pasienadmisi_t pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id) diagnosa ON pendaftaran_t.pasienadmisi_id = diagnosa.pasienadmisi_id
     LEFT JOIN ambiljenazah_t ON pendaftaran_t.pasienadmisi_id = ambiljenazah_t.pasienadmisi_id;
");

          $this->execute('ALTER TABLE public.infopasienmeninggal_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190918_070446_optimize_view_5 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_070446_optimize_view_5 cannot be reverted.\n";

        return false;
    }
    */
}
