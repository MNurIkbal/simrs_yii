<?php

use yii\db\Migration;

/**
 * Class m200131_065713_perubahan_view_2020131
 */
class m200131_065713_perubahan_view_2020131 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruang_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id
  WHERE ruang_penunjang.instalasi_id = 5 AND pendaftaran_t.is_aps = true AND pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pasienmasukpenunjang_t.no_antrian IS NOT NULL;
");

        $this->execute('ALTER TABLE public.infopasienrad_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasienmcudetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienmcudetail_v AS 
 SELECT 'RAD'::text AS penunjang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    radiologi.tindakanpelayanan_id,
        CASE
            WHEN radiologi.tipepaket_id IS NULL THEN radiologi.daftartindakan_id
            ELSE radiologi.tipepaket_id
        END AS tindakan_paket_id,
    radiologi.tipepaket_nama,
    radiologi.detail_2id,
    radiologi.detail_2,
    radiologi.detail_3id,
        CASE
            WHEN radiologi.tipepaket_id IS NULL THEN radiologi.daftartindakan_nama
            ELSE radiologi.detail_3
        END AS detail_3,
    radiologi.p_rad AS pemeriksaan,
    radiologi.j_rad AS jenis
   FROM pendaftaran_t
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT rad.tindakanpelayanan_id,
            rad.pendaftaran_id,
            rad.tipepaket_id,
            rad.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_rad,
            detail.j_rad
           FROM tindakanpelayanan_t rad
             LEFT JOIN tipepaket_m ON rad.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN daftartindakan_m ON rad.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_rad,
                    paket_detail.j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                           FROM tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                             JOIN daftartindakan_m daftartindakan_m_1 ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id
                             LEFT JOIN pemeriksaanrad_m ON daftartindakan_m_1.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
                  WHERE tipepaket_m_1.is_deleted = false
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
                     LEFT JOIN pemeriksaanrad_m ON tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                  WHERE tipepaket_m_1.is_deleted = false) detail ON rad.tipepaket_id = detail.detail_1id) radiologi ON pendaftaran_t.pendaftaran_id = radiologi.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 21
UNION ALL
 SELECT 'LAB'::text AS penunjang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    laboratorium.tindakanpelayanan_id,
        CASE
            WHEN laboratorium.tipepaket_id IS NULL THEN laboratorium.daftartindakan_id
            ELSE laboratorium.tipepaket_id
        END AS tindakan_paket_id,
    laboratorium.tipepaket_nama,
    laboratorium.detail_2id,
    laboratorium.detail_2,
    laboratorium.detail_3id,
        CASE
            WHEN laboratorium.tipepaket_id IS NULL THEN laboratorium.daftartindakan_nama
            ELSE laboratorium.detail_3
        END AS detail_3,
    laboratorium.p_lab AS pemeriksaan,
    laboratorium.j_lab AS jenis
   FROM pendaftaran_t
     JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 4
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT lab.tindakanpelayanan_id,
            lab.pendaftaran_id,
            lab.tipepaket_id,
            lab.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_lab,
            detail.j_lab
           FROM tindakanpelayanan_t lab
             JOIN tipepaket_m ON lab.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN daftartindakan_m ON lab.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_lab,
                    paket_detail.j_lab
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 4
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                           FROM tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                             JOIN daftartindakan_m daftartindakan_m_1 ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id
                             LEFT JOIN pemeriksaanlab_m ON daftartindakan_m_1.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                             LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
                  WHERE tipepaket_m_1.is_deleted = false
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                   FROM tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
                     JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
                     LEFT JOIN pemeriksaanlab_m ON tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                  WHERE tipepaket_m_1.is_deleted = false) detail ON lab.tipepaket_id = detail.detail_1id) laboratorium ON pendaftaran_t.pendaftaran_id = laboratorium.pendaftaran_id
  WHERE pendaftaran_t.instalasi_id = 21;
");

        $this->execute('ALTER TABLE public.infopasienmcudetail_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporanpasiensudahbayar_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanpasiensudahbayar_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pembayaranpelayanan_t.tgl_pembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    fgetnamalookup(pembayaranpelayanan_t.statusbayar::integer) AS status_bayar,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    tandabuktibayar_t.closingkasir_id,
    pembayaranpelayanan_t.total_biayapelayanan::integer AS total_tagihan,
    pembayaranpelayanan_t.penggunaan_uangmuka::integer AS total_uang_muka,
    pembayaranpelayanan_t.total_subsidiasuransi::integer AS subsidi_asuransi,
    pembayaranpelayanan_t.total_bayartindakan::integer AS total_sudah_dibayarkan,
    pembayaranpelayanan_t.total_sisatagihan::integer AS total_sisa_tagihan,
    pembayaranpelayanan_t.biaya_administrasi::integer AS biaya_administrasi,
    pembayaranpelayanan_t.pembulatan::integer AS pembulatan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    peg_rd_rj.nama_pegawai AS pegawai_rd_rj,
    kelaspelayanan_m.kelaspelayanan_nama
   FROM pembayaranpelayanan_t
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tandabuktibayar_t.pembayaranpelayanan_id
     LEFT JOIN pegawai_m peg_rd_rj ON pendaftaran_t.pegawai_id = peg_rd_rj.pegawai_id
     LEFT JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE pembayaranpelayanan_t.is_active = true AND pembayaranpelayanan_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.laporanpasiensudahbayar_v
  OWNER TO postgres;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200131_065713_perubahan_view_2020131 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200131_065713_perubahan_view_2020131 cannot be reverted.\n";

        return false;
    }
    */
}
