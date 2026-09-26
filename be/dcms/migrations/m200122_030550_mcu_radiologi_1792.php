<?php

use yii\db\Migration;

/**
 * Class m200122_030550_mcu_radiologi_1792
 */
class m200122_030550_mcu_radiologi_1792 extends Migration
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
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
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

        $this->execute('DROP VIEW if exists public.infopasienraddetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienraddetail_v AS 
 SELECT 'NON_PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    ''::character varying AS tipepaket_nama,
    NULL::text AS detail_2,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
  WHERE daftartindakan_m.kelompoktindakan_id = 10
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::text AS detail_2,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
  WHERE daftartindakan_m.kelompoktindakan_id = 10
UNION ALL
 SELECT 'PAKET_MCU'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tgl_tindakan,
    detail.j_rad AS jenispemeriksaanrad_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    detail.detail_2,
    detail.detail_3id AS daftartindakan_id,
    detail.detail_3 AS daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.cyto_tindakan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.qty_tindakan,
    pasienmasukpenunjang_t.status_periksa,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasien_id
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            paketpelayanan_mp.paketdetail_id AS detail_2id,
            paket_detail.tipepaket_nama AS detail_2,
            paket_detail.daftartindakan_id AS detail_3id,
            paket_detail.daftartindakan_nama AS detail_3,
            paket_detail.p_rad,
            paket_detail.j_rad
           FROM tipepaket_m tipepaket_m_1
             JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    daftartindakan_m.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM tipepaket_m a
                     JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                     JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     LEFT JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
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
          WHERE tipepaket_m_1.is_deleted = false) detail ON tindakanpelayanan_t.tipepaket_id = detail.detail_1id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 5;");

        $this->execute('ALTER TABLE public.infopasienraddetail_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.hasilpemeriksaanrad_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.hasilpemeriksaanrad_v AS 
 SELECT 'NON_PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tipepaket_id,
    ''::character varying AS tipepaket_nama,
    NULL::text AS detail_2,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    kelompokpemeriksaanrad_m.nama_kelompok,
    tindakanpelayanan_t.cyto_tindakan,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.no_hasilrad,
    hasilpemeriksaanrad_t.tgl_ambilfoto,
    hasilpemeriksaanrad_t.tgl_uploadhasil,
    hasilpemeriksaanrad_t.tgl_hasilrad,
    hasilpemeriksaanrad_t.kesan,
    hasilpemeriksaanrad_t.kesimpulan,
    hasilpemeriksaanrad_t.penanggungjawab_id,
    penanggungjawab.nama_pegawai AS penanggung_jawab,
    hasilpemeriksaanrad_t.hasil_expertise,
    hasilpemeriksaanrad_t.expertise_id,
    hasilpemeriksaanrad_t.is_hasilkritis,
    pasienmasukpenunjang_t.status_periksa,
    hasilpemeriksaanrad_t.tgl_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
     LEFT JOIN pegawai_m penanggungjawab ON hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id
     LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
  WHERE daftartindakan_m.kelompoktindakan_id = 10 AND pemeriksaanrad_m.is_deleted = false
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::text AS detail_2,
    paketpelayanan_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    kelompokpemeriksaanrad_m.nama_kelompok,
    tindakanpelayanan_t.cyto_tindakan,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.no_hasilrad,
    hasilpemeriksaanrad_t.tgl_ambilfoto,
    hasilpemeriksaanrad_t.tgl_uploadhasil,
    hasilpemeriksaanrad_t.tgl_hasilrad,
    hasilpemeriksaanrad_t.kesan,
    hasilpemeriksaanrad_t.kesimpulan,
    hasilpemeriksaanrad_t.penanggungjawab_id,
    penanggungjawab.nama_pegawai AS penanggung_jawab,
    hasilpemeriksaanrad_t.hasil_expertise,
    hasilpemeriksaanrad_t.expertise_id,
    hasilpemeriksaanrad_t.is_hasilkritis,
    pasienmasukpenunjang_t.status_periksa,
    hasilpemeriksaanrad_t.tgl_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN permintaankepenunjang_t ON tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id
     LEFT JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id
     LEFT JOIN pegawai_m penanggungjawab ON hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id
     LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
  WHERE daftartindakan_m.kelompoktindakan_id = 10 AND pemeriksaanrad_m.is_deleted = false
UNION ALL
 SELECT 'PAKET_MCU'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    detail.detail_2,
    detail.detail_3id AS daftartindakan_id,
    detail.detail_3 AS daftartindakan_nama,
    detail.p_rad_id AS pemeriksaanradiologi_id,
    detail.p_rad AS pemeriksaanrad_nama,
    detail.k_rad AS nama_kelompok,
    tindakanpelayanan_t.cyto_tindakan,
    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
    hasilpemeriksaanrad_t.no_hasilrad,
    hasilpemeriksaanrad_t.tgl_ambilfoto,
    hasilpemeriksaanrad_t.tgl_uploadhasil,
    hasilpemeriksaanrad_t.tgl_hasilrad,
    hasilpemeriksaanrad_t.kesan,
    hasilpemeriksaanrad_t.kesimpulan,
    hasilpemeriksaanrad_t.penanggungjawab_id,
    penanggungjawab.nama_pegawai AS penanggung_jawab,
    hasilpemeriksaanrad_t.hasil_expertise,
    hasilpemeriksaanrad_t.expertise_id,
    hasilpemeriksaanrad_t.is_hasilkritis,
    pasienmasukpenunjang_t.status_periksa,
    hasilpemeriksaanrad_t.tgl_verifikasi
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            paketpelayanan_mp.paketdetail_id AS detail_2id,
            paket_detail.tipepaket_nama AS detail_2,
            paket_detail.daftartindakan_id AS detail_3id,
            paket_detail.daftartindakan_nama AS detail_3,
            paket_detail.p_rad_id,
            paket_detail.p_rad,
            paket_detail.j_rad,
            paket_detail.k_rad
           FROM tipepaket_m tipepaket_m_1
             JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
             JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    daftartindakan_m.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama,
                    pemeriksaanrad_m.pemeriksaanradiologi_id AS p_rad_id,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad,
                    kelompokpemeriksaanrad_m.nama_kelompok AS k_rad
                   FROM tipepaket_m a
                     JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                     JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     LEFT JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                     LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
          WHERE tipepaket_m_1.is_deleted = false
        UNION ALL
         SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            NULL::integer AS detail_2id,
            NULL::character varying AS detail_2,
            paketpelayanan_mp.daftartindakan_id AS detail_3id,
            tindakan_detail.daftartindakan_nama AS detail_3,
            pemeriksaanrad_m.pemeriksaanradiologi_id AS p_rad_id,
            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad,
            kelompokpemeriksaanrad_m.nama_kelompok AS k_rad
           FROM tipepaket_m tipepaket_m_1
             JOIN paketpelayanan_mp ON tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
             JOIN ruangan_m ruangan_m_1 ON paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id AND ruangan_m_1.instalasi_id = 5
             JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
             LEFT JOIN pemeriksaanrad_m ON tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
             LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
             LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
          WHERE tipepaket_m_1.is_deleted = false) detail ON tindakanpelayanan_t.tipepaket_id = detail.detail_1id
     LEFT JOIN hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id AND detail.p_rad_id = hasilpemeriksaanrad_t.pemeriksaanrad_id
     LEFT JOIN pegawai_m penanggungjawab ON hasilpemeriksaanrad_t.penanggungjawab_id = penanggungjawab.pegawai_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5;
");

        $this->execute('ALTER TABLE public.hasilpemeriksaanrad_v
  OWNER TO postgres;');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_030550_mcu_radiologi_1792 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_030550_mcu_radiologi_1792 cannot be reverted.\n";

        return false;
    }
    */
}
