<?php

use yii\db\Migration;

/**
 * Class m220704_125132_migrate_MHG1798_laporanpasienrujukanrad_v
 */
class m220704_125132_migrate_MHG1798_laporanpasienrujukanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpasienrujukanrad_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanpasienrujukanrad_v
        AS SELECT 'ORDER'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_persetujuan,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            jk.lookup_name AS jenis_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            1 AS jenis_rujukan_id,
            'Rujukan RS'::text AS jenis_rujukan,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            asal_ruangan.ruangan_id AS ruanganasal_id,
            asal_ruangan.ruangan_nama AS ruanganasal_nama,
            NULL::integer AS asalrujukan_id,
            NULL::character varying AS asalrujukan_nama,
            NULL::integer AS rujukandari_id,
            NULL::character varying AS rujukandari_nama,
            (('Rujukan dari instalasi '::text || instalasi_asal.instalasi_nama::text) || ' ruangan '::text) || asal_ruangan.ruangan_nama::text AS rujukan,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienmasukpenunjang_t.status_periksa,
            status.lookup_name AS status_periksa_nama
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.tgl_kirimpasien,
                    a.no_orderkeunitlain,
                    a.pasienkirimkeunitlain_id,
                    a.instalasi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.no_pendaftaran,
                    a.pendaftaran_id,
                    a.umur,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.daftartindakan_id,
                    a.is_deleted,
                    a.is_referred
                   FROM permintaankepenunjang_t a
                  WHERE a.is_deleted IS FALSE) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND COALESCE(permintaankepenunjang_t.is_referred, false) IS FALSE AND permintaankepenunjang_t.is_deleted IS FALSE
        UNION ALL
         SELECT 'RUJUKAN RS'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_persetujuan,
            pendaftaran_t.no_pendaftaran,
            COALESCE(pasienmasukpenunjang_t.no_masukpenunjang, rujukan_t.no_rujukan) AS no_rujukan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            jk.lookup_name AS jenis_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            2 AS jenis_rujukan_id,
            'Rujukan Masuk'::text AS jenis_rujukan,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            asal_ruangan.ruangan_id AS ruanganasal_id,
            asal_ruangan.ruangan_nama AS ruanganasal_nama,
            asalrujukan_m.asalrujukan_id,
            asalrujukan_m.asalrujukan_nama,
            rujukandari_m.rujukandari_id,
            rujukandari_m.nama_perujuk AS rujukandari_nama,
            (('Rujukan dari '::text || COALESCE(rujukandari_m.nama_perujuk, ''::character varying)::text) || ' '::text) || COALESCE(asalrujukan_m.asalrujukan_nama, ''::character varying)::text AS rujukan,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienmasukpenunjang_t.status_periksa,
            status.lookup_name AS status_periksa_nama
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.no_pendaftaran,
                    a.pendaftaran_id,
                    a.umur,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.rujukan_id,
                    a.instalasi_id,
                    a.tgl_pendaftaran
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.rujukan_id,
                    a.asalrujukan_id,
                    a.rujukandari_id,
                    a.no_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             JOIN ( SELECT a.perujuk_id AS rujukandari_id,
                    a.namaperujuk AS nama_perujuk
                   FROM perujuk_m a) rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.pasienmasukpenunjang_id
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted IS FALSE) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
             JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
          WHERE pendaftaran_t.instalasi_id = 5
        UNION ALL
         SELECT 'APS'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_persetujuan,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.no_masukpenunjang AS no_rujukan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            jk.lookup_name AS jenis_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            3 AS jenis_rujukan_id,
            'APS'::text AS jenis_rujukan,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            asal_ruangan.ruangan_id AS ruanganasal_id,
            asal_ruangan.ruangan_nama AS ruanganasal_nama,
            NULL::integer AS asalrujukan_id,
            NULL::character varying AS asalrujukan_nama,
            NULL::integer AS rujukandari_id,
            NULL::character varying AS rujukandari_nama,
            (('Rujukan dari instalasi '::text || instalasi_asal.instalasi_nama::text) || ' ruangan '::text) || asal_ruangan.ruangan_nama::text AS rujukan,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienmasukpenunjang_t.status_periksa,
            status.lookup_name AS status_periksa_nama
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.no_pendaftaran,
                    a.pendaftaran_id,
                    a.umur,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id,
                    a.rujukan_id,
                    a.instalasi_id,
                    a.tgl_pendaftaran,
                    a.is_aps
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.pasienmasukpenunjang_id
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted IS FALSE) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
             JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
          WHERE pendaftaran_t.instalasi_id = 5 AND pendaftaran_t.is_aps IS TRUE AND pendaftaran_t.rujukan_id IS NULL
        UNION ALL
         SELECT 'RUJUKAN KE LUAR'::text AS tipe_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_persetujuan,
            pendaftaran_t.no_pendaftaran,
            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            jk.lookup_name AS jenis_kelamin,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            4 AS jenis_rujukan_id,
            'Rujukan Keluar'::text AS jenis_rujukan,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            asal_ruangan.ruangan_id AS ruanganasal_id,
            asal_ruangan.ruangan_nama AS ruanganasal_nama,
            asalrujukan_m.asalrujukan_id,
            asalrujukan_m.asalrujukan_nama,
            rujukankeluar_m.rujukankeluar_id AS rujukandari_id,
            rujukankeluar_m.rumahsakit_rujukan AS rujukandari_nama,
            (('Rujukan Keluar '::text || COALESCE(asalrujukan_m.asalrujukan_nama, ''::character varying)::text) || ' '::text) || COALESCE(rujukankeluar_m.rumahsakit_rujukan, ''::character varying)::text AS rujukan,
            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
            dokter_perujuk.nama_pegawai AS dokter_perujuk,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienmasukpenunjang_t.status_periksa,
            status.lookup_name AS status_periksa_nama
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.tgl_kirimpasien,
                    a.no_orderkeunitlain,
                    a.pasienkirimkeunitlain_id,
                    a.instalasi_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.daftartindakan_id,
                    a.is_deleted,
                    a.is_referred,
                    a.permintaankepenunjang_id
                   FROM permintaankepenunjang_t a
                  WHERE a.is_deleted IS FALSE) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.permintaankepenunjang_id,
                    a.rujukankeluar_id
                   FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_t ON permintaankepenunjang_t.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
             LEFT JOIN ( SELECT a.rujukankeluar_id,
                    a.asalrujukan_id,
                    a.rumahsakit_rujukan
                   FROM rujukankeluar_m a) rujukankeluar_m ON pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukankeluar_m.asalrujukan_id = asalrujukan_m.asalrujukan_id
             JOIN ( SELECT a.no_pendaftaran,
                    a.pendaftaran_id,
                    a.umur,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_perujuk ON pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
             JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
             JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status ON pasienmasukpenunjang_t.status_periksa::integer = status.lookup_id
          WHERE pasienkirimkeunitlain_t.instalasi_id = 5 AND COALESCE(permintaankepenunjang_t.is_referred, false) IS TRUE AND permintaankepenunjang_t.is_deleted IS FALSE;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220704_125132_migrate_MHG1798_laporanpasienrujukanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220704_125132_migrate_MHG1798_laporanpasienrujukanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
