<?php

use yii\db\Migration;

/**
 * Class m231117_025331_migrate_glbj346_view_informasiresep_v
 */
class m231117_025331_migrate_glbj346_view_informasiresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."informasiresep_v";
        ');

        $this->execute('
            CREATE VIEW "public"."informasiresep_v" AS  SELECT resep.jenis,
    resep.reseptur_id,
    resep.penjualanresep_id,
    resep.pendaftaran_id,
    resep.pasienadmisi_id,
    resep.resep_id, 
    resep.status_reseptur_id,
    resep.antrian_id,
    resep.no_antrian,
    resep.status_reseptur,
    resep.tglreseptur,
    resep.tglresep,
    resep.tgl_resep_dibuat,
    resep.no_reseptur,
    resep.no_resep,
    resep.nomor,
    resep.iter,
    resep.instalasi_reseptur_id,
    resep.instalasi_reseptur,
    resep.instalasi_resep_id,
    resep.instalasi_resep,
    resep.ruangan_id,
    resep.ruanganreseptur_id,
    resep.ruangan_tujuan,
    resep.ruangan_reseptur,
    resep.pegawai_id,
    resep.nama_pegawai,
    resep.pasien_id,
    resep.no_rekam_medik,
    resep.nama_pasien,
    resep.nama,
    resep.tanggal_lahir,
    resep.alamat_pasien,
    resep.no_pendaftaran,
    resep.nosep,
    resep.carabayar_id,
    resep.penjamin_id,
    resep.carabayar_nama,
    resep.penjamin_nama,
    resep.biayaadministrasi,
    resep.totalhargajual,
    resep.totaltagihan,
    resep.status_bayar,
    resep.status_racikan,
    resep.is_kronis,
    resep.kategori_resep,
    resep.kategori_resep_nama,
    resep.kategori_resep_kode,
    resep.additional_data,
    resep.catatan,
    resep.diagnosa_id,
    resep.diagnosa_nama,
    NULL::text AS diagnosa_text,
    resep.tanda_tangan
   FROM ( SELECT \'reseptur\'::text AS jenis,
            penjualanresep_t.penjualanresep_id,
            reseptur_t.pendaftaran_id,
            reseptur_t.pasienadmisi_id,
            reseptur_t.reseptur_id,
            reseptur_t.penjualanresep_id AS resep_id,
            reseptur_t.status_reseptur AS status_reseptur_id,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
                CASE
                    WHEN penjualanresep_t.penjualanresep_id IS NULL THEN \'Belum Proses\'::character varying
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE status_reseptur.lookup_name
                END AS status_reseptur,
            reseptur_t.tglreseptur,
            penjualanresep_t.tglresep,
            COALESCE(penjualanresep_t.tglresep, reseptur_t.tglreseptur) AS tgl_resep_dibuat,
            reseptur_t.noresep AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            reseptur_t.noresep AS nomor,
            ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
            instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
            ruangan_tujuan.instalasi_id AS instalasi_resep_id,
            instalasi_tujuan.instalasi_nama AS instalasi_resep,
            reseptur_t.ruangan_id,
            reseptur_t.ruanganreseptur_id,
            ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
            ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
            reseptur_t.pegawai_id,
            pegawai_m.nama_pegawai,
            reseptur_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            concat(namadepan.lookup_name, \' \', pasien_m.nama_pasien) AS nama,
            pasien_m.alamat_pasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.no_pendaftaran,
            NULL::character varying AS nosep,
            COALESCE(penjamin_admisi.carabayar_id, pendaftaran_t.carabayar_id) AS carabayar_id,
            COALESCE(pendaftaran_t.penjamin_admisi_id, pendaftaran_t.penjamin_id) AS penjamin_id,
            COALESCE(carabayar_admisi.carabayar_nama, carabayar_m.carabayar_nama) AS carabayar_nama,
            COALESCE(penjamin_admisi.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            penjualanresep_t.biayaadministrasi,
            penjualanresep_t.totalhargajual,
            totaltagihan.totaltagihan,
            penjualanresep_t.status_bayar,
                CASE
                    WHEN resepturracikan_t.reseptur_id IS NOT NULL AND resepturracikan_t.type::text = \'OR\'::text THEN \'Racikan\'::text
                    ELSE
                    CASE
                        WHEN resepturdetail_t.count_obat_racikan >= 1 THEN \'Racikan\'::text
                        ELSE \'Non Racikan\'::text
                    END
                END AS status_racikan,
                CASE
                    WHEN resepturdetail_t.count_is_kronis >= 1 THEN true
                    ELSE false
                END AS is_kronis,
            reseptur_t.kategori_resep,
            kategori_resep_lookup.lookup_name AS kategori_resep_nama,
            kategori_resep_lookup.lookup_kode AS kategori_resep_kode,
                CASE
                    WHEN reseptur_t.catatan IS NOT NULL THEN reseptur_t.catatan
                    ELSE penjualanresep_t.catatan
                END AS catatan,
            penjualanresep_t.additional_data,
            resepturdetail_t.iter,
            diagnosa_m.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            pegawai_m.tanda_tangan
           FROM reseptur_t
             LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
                    penjualanresep.tglresep,
                    penjualanresep.noresep,
                    penjualanresep.status_reseptur,
                    penjualanresep.status_bayar,
                    penjualanresep.catatan,
                    penjualanresep.biayaadministrasi,
                    penjualanresep.totalhargajual,
                    penjualanresep.pegawai_approve_id,
                    penjualanresep.nosep,
                    penjualanresep.additional_data
                   FROM penjualanresep_t penjualanresep
                  WHERE penjualanresep.is_deleted = false) penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT DISTINCT a.reseptur_id,
                    a.type
                   FROM resepturracikan_t a
                  WHERE a.is_deleted = false
                  ORDER BY a.reseptur_id) resepturracikan_t ON resepturracikan_t.reseptur_id = reseptur_t.reseptur_id
             LEFT JOIN ( SELECT reseptur_detail.reseptur_id,
                    count(reseptur_detail.racikan_id) FILTER (WHERE reseptur_detail.racikan_id = 1) AS count_obat_racikan,
                    count(reseptur_detail.is_kronis) FILTER (WHERE reseptur_detail.is_kronis = true) AS count_is_kronis,
                    reseptur_detail.iter
                   FROM resepturdetail_t reseptur_detail
                  WHERE reseptur_detail.is_deleted = false
                  GROUP BY reseptur_detail.reseptur_id, reseptur_detail.iter
                  ORDER BY reseptur_detail.reseptur_id) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
             JOIN ( SELECT pendaftaran.pendaftaran_id,
                    pendaftaran.pasienadmisi_id,
                    pendaftaran.kelaspelayanan_id,
                    pendaftaran.carabayar_id,
                    pendaftaran.penjamin_id,
                    pendaftaran.umur,
                    pendaftaran.no_pendaftaran,
                    pendaftaran.instalasi_id,
                    pasienadmisi_t.penjamin_id AS penjamin_admisi_id,
                    pasienadmisi_t.bpjs_id AS bpjsadmisi_id,
                    pendaftaran.bpjs_id,
                    pendaftaran.ruangan_id,
                    pasienadmisi_t.ruangan_id AS ruangan_pasien_admisi,
                    pasienadmisi_t.kamarruangan_id AS kamarruangan_pasien_admisi,
                    pasienadmisi_t.kamartempattidur_id AS kamartempattidur_pasien_admisi
                   FROM pendaftaran_t pendaftaran
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.pasienadmisi_id,
                            a.penjamin_id,
                            a.bpjs_id,
                            a.kamarruangan_id,
                            a.kamartempattidur_id,
                            a.ruangan_id
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT a.bpjs_id,
                            a.nosep
                           FROM bpjs_t a
                          WHERE a.is_deleted IS FALSE) bpjs_t ON COALESCE(pasienadmisi_t.bpjs_id, pendaftaran.bpjs_id) = bpjs_t.bpjs_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien
                   FROM pasien_m pasien) pasien_m ON reseptur_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT ruangan_1.ruangan_id,
                    ruangan_1.ruangan_nama,
                    ruangan_1.instalasi_id
                   FROM ruangan_m ruangan_1) ruangan_tujuan ON reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id
             JOIN ( SELECT ruangan_2.ruangan_id,
                    ruangan_2.ruangan_nama,
                    ruangan_2.instalasi_id
                   FROM ruangan_m ruangan_2) ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
             JOIN ( SELECT instalasi_1.instalasi_id,
                    instalasi_1.instalasi_nama
                   FROM instalasi_m instalasi_1) instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
             JOIN ( SELECT instalasi_2.instalasi_id,
                    instalasi_2.instalasi_nama
                   FROM instalasi_m instalasi_2) instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
             JOIN ( SELECT kelas.kelaspelayanan_id,
                    kelas.kelaspelayanan_nama
                   FROM kelaspelayanan_m kelas) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama
                   FROM penjamin_m penjamin) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama,
                    penjamin.carabayar_id
                   FROM penjamin_m penjamin) penjamin_admisi ON pendaftaran_t.penjamin_admisi_id = penjamin_admisi.penjamin_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_admisi ON penjamin_admisi.carabayar_id = carabayar_admisi.carabayar_id
             JOIN ( SELECT pegawai.pegawai_id,
                    pegawai.tgl_lahirpegawai,
                    pegawai.nama_pegawai,
                    pegawai.alamat_pegawai,
                    pegawai.tanda_tangan
                   FROM pegawai_m pegawai) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON reseptur_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT resepturdetail_t_1.reseptur_id,
                    sum(resepturdetail_t_1.hargajual_reseptur) AS totaltagihan
                   FROM resepturdetail_t resepturdetail_t_1
                  WHERE resepturdetail_t_1.is_deleted = false AND resepturdetail_t_1.is_active = true
                  GROUP BY resepturdetail_t_1.reseptur_id) totaltagihan ON reseptur_t.reseptur_id = totaltagihan.reseptur_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id
             LEFT JOIN ( SELECT ruangan_daftar_1.ruangan_id,
                    ruangan_daftar_1.ruangan_nama,
                    ruangan_daftar_1.instalasi_id
                   FROM ruangan_m ruangan_daftar_1) ruangan_daftar ON ruangan_daftar.ruangan_id = pendaftaran_t.ruangan_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) namadepan ON pasien_m.namadepan::integer = namadepan.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name,
                    lookup_m.lookup_kode
                   FROM lookup_m) kategori_resep_lookup ON reseptur_t.kategori_resep = kategori_resep_lookup.lookup_id
             LEFT JOIN ( SELECT diagnosa.diagnosa_id,
                    diagnosa.diagnosa_kode,
                    diagnosa.diagnosa_nama
                   FROM diagnosa_m diagnosa) diagnosa_m ON reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id
          WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true AND reseptur_t.penjualanresep_id IS NULL
        UNION ALL
         SELECT \'resep\'::text AS jenis,
            penjualanresep_t.penjualanresep_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            reseptur_t.reseptur_id,
            penjualanresep_t.penjualanresep_id AS resep_id,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
            antrian_t.antrian_id,
            antrian_t.no_antrian,
                CASE
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE status_reseptur.lookup_name
                END AS status_reseptur,
            reseptur_t.tglreseptur,
            penjualanresep_t.tglresep,
            penjualanresep_t.tglresep AS tgl_resep_dibuat,
            reseptur_t.noresep AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            penjualanresep_t.noresep AS nomor,
            COALESCE(rm.instalasi_id, ruangan_resep.instalasi_id) AS instalasi_reseptur_id,
                CASE
                    WHEN reseptur_t.reseptur_id IS NULL THEN instalasi_resep.instalasi_nama
                    WHEN reseptur_t.reseptur_id IS NOT NULL THEN im.instalasi_nama
                    ELSE NULL::character varying
                END AS instalasi_reseptur,
            ruangan_resep.instalasi_id AS instalasi_resep_id,
            instalasi_resep.instalasi_nama AS instalasi_resep,
            penjualanresep_t.ruangan_id,
            COALESCE(reseptur_t.ruanganreseptur_id, penjualanresep_t.ruangan_id) AS ruanganreseptur_id,
            ruangan_resep.ruangan_nama AS ruangan_tujuan,
                CASE
                    WHEN reseptur_t.reseptur_id IS NULL THEN ruangan_resep.ruangan_nama
                    WHEN reseptur_t.reseptur_id IS NOT NULL THEN rm.ruangan_nama
                    ELSE NULL::character varying
                END AS ruangan_reseptur,
            penjualanresep_t.pegawai_id,
            pegawai_m.nama_pegawai,
            penjualanresep_t.pasien_id,
            COALESCE(pasien_m.no_rekam_medik, pasien_kornis.no_rekam_medik, pasien_reseptur_kornis.no_rekam_medik) AS no_rekam_medik,
            COALESCE(pasien_m.nama_pasien, pasien_kornis.nama_pasien, pasien_reseptur_kornis.nama_pasien) AS nama_pasien,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = \'343\'::text THEN penjualanresep_t.nama_pembeli
                    WHEN penjualanresep_t.jenispenjualan::text = \'344\'::text THEN concat(namadepan.lookup_name, \' \', COALESCE(pasien_m.nama_pasien, pasien_kornis.nama_pasien, pasien_reseptur_kornis.nama_pasien))::character varying
                    WHEN penjualanresep_t.jenispenjualan::text = \'345\'::text THEN concat(gelar.lookup_name, \' \', karyawan.nama_pegawai)::character varying
                    ELSE NULL::character varying
                END AS nama,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = \'345\'::text THEN pegawai_m.alamat_pegawai
                    ELSE pasien_m.alamat_pasien
                END AS alamat_pasien,
            pasien_m.tanggal_lahir,
                CASE
                    WHEN penjualanresep_t.resep_kronis_asal_id IS NULL AND penjualanresep_t.reseptur_kronis_asal_id IS NULL THEN pendaftaran_t.no_pendaftaran
                    ELSE NULL::character varying
                END AS no_pendaftaran,
            COALESCE(penjualanresep_t.nosep, bpjs_t.nosep) AS nosep,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.penjamin_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            penjualanresep_t.biayaadministrasi,
            obatalkespasien_t.hargajual_oa AS totalhargajual,
            COALESCE(obatalkespasien_t.hargajual_oa, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totaltagihan,
            penjualanresep_t.status_bayar,
                CASE
                    WHEN resepturracikan_t.reseptur_id IS NOT NULL AND resepturracikan_t.type::text = \'OR\'::text THEN \'Racikan\'::text
                    ELSE
                    CASE
                        WHEN resepturdetail_t.count_obat_racikan >= 1 THEN \'Racikan\'::text
                        ELSE \'Non Racikan\'::text
                    END
                END AS status_racikan,
                CASE
                    WHEN obatalkespasien_t.count_is_kronis >= 1 THEN true
                    ELSE false
                END AS is_kronis,
            reseptur_t.kategori_resep,
            kategori_resep_lookup.lookup_name AS kategori_resep_nama,
            kategori_resep_lookup.lookup_kode AS kategori_resep_kode,
            penjualanresep_t.catatan,
            penjualanresep_t.additional_data,
            penjualanresep_t.iter,
            NULL::integer AS diagnosa_id,
            NULL::character varying AS diagnosa_nama,
            pegawai_m.tanda_tangan
           FROM ( SELECT a.penjualanresep_id,
                    a.status_reseptur,
                    a.tglresep,
                    a.noresep,
                    a.ruangan_id,
                    a.pegawai_id,
                    a.pasien_id,
                    a.resep_kronis_asal_id,
                    a.nosep,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.biayaadministrasi,
                    a.pendaftaran_id,
                    a.karyawan_id,
                    a.reseptur_id,
                    a.antrian_id,
                    a.reseptur_kronis_asal_id,
                    a.status_bayar,
                    a.jenispenjualan,
                    a.nama_pembeli,
                    a.additional_data,
                    a.catatan,
                    a.iter
                   FROM penjualanresep_t a) penjualanresep_t
             LEFT JOIN ( SELECT a.penjualanresep_id,
                    sum(a.hargajual_oa) AS hargajual_oa,
                    count(a.is_kronis) FILTER (WHERE a.is_kronis = true) AS count_is_kronis
                   FROM obatalkespasien_t a
                  WHERE a.penjualanresep_id IS NOT NULL AND a.is_deleted = false
                  GROUP BY a.penjualanresep_id
                  ORDER BY a.penjualanresep_id) obatalkespasien_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT b.pendaftaran_id,
                    a.resep_kronis_asal_id,
                    a.penjualanresep_id,
                    b.pasien_id,
                    c.no_pendaftaran
                   FROM penjualanresep_t a
                     LEFT JOIN penjualanresep_t b ON a.resep_kronis_asal_id = b.penjualanresep_id
                     LEFT JOIN ( SELECT x.pendaftaran_id,
                            x.no_pendaftaran
                           FROM pendaftaran_t x) c ON b.pendaftaran_id = c.pendaftaran_id
                  WHERE a.resep_kronis_asal_id IS NOT NULL) penjualanresep_kronis ON penjualanresep_kronis.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT c.pendaftaran_id,
                    a.reseptur_kronis_asal_id,
                    a.reseptur_id,
                    a.penjualanresep_id,
                    c.pasien_id,
                    a.antrian_id
                   FROM penjualanresep_t a
                     LEFT JOIN reseptur_t c ON a.reseptur_kronis_asal_id = c.reseptur_id
                  WHERE a.reseptur_kronis_asal_id IS NOT NULL) penjualanreseptur_kronis ON penjualanreseptur_kronis.penjualanresep_id = penjualanresep_t.penjualanresep_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    jenis_kelamin_1.lookup_name AS jenis_kelamin
                   FROM pasien_m pasien
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) jenis_kelamin_1 ON pasien.jeniskelamin::integer = jenis_kelamin_1.lookup_id) pasien_kornis ON penjualanresep_kronis.pasien_id = pasien_kornis.pasien_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien,
                    jenis_kelamin_1.lookup_name AS jenis_kelamin
                   FROM pasien_m pasien
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) jenis_kelamin_1 ON pasien.jeniskelamin::integer = jenis_kelamin_1.lookup_id) pasien_reseptur_kornis ON penjualanreseptur_kronis.pasien_id = pasien_reseptur_kornis.pasien_id
             LEFT JOIN ( SELECT pendaftaran.pendaftaran_id,
                    pendaftaran.pasien_id,
                    pendaftaran.pasienadmisi_id,
                    pendaftaran.instalasi_id,
                    pendaftaran.umur,
                    pendaftaran.no_pendaftaran,
                    pendaftaran.bpjs_id,
                    pendaftaran.ruangan_id
                   FROM pendaftaran_t pendaftaran) pendaftaran_t ON COALESCE(penjualanresep_t.pendaftaran_id, penjualanresep_kronis.pendaftaran_id, penjualanreseptur_kronis.pendaftaran_id) = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pendaftaran_id,
                    a.bpjs_id,
                    a.kamartempattidur_id,
                    a.kamarruangan_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pendaftaran_id = pasienadmisi_t.pendaftaran_id
             LEFT JOIN ( SELECT pasien.pasien_id,
                    pasien.nama_pasien,
                    pasien.no_rekam_medik,
                    pasien.tanggal_lahir,
                    pasien.jeniskelamin,
                    pasien.namadepan,
                    pasien.alamat_pasien
                   FROM pasien_m pasien) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT ruangan.ruangan_id,
                    ruangan.ruangan_nama,
                    ruangan.instalasi_id
                   FROM ruangan_m ruangan) ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
             JOIN ( SELECT instalasi.instalasi_id,
                    instalasi.instalasi_nama
                   FROM instalasi_m instalasi) instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
             LEFT JOIN ( SELECT carabayar.carabayar_id,
                    carabayar.carabayar_nama
                   FROM carabayar_m carabayar) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT penjamin.penjamin_id,
                    penjamin.penjamin_nama
                   FROM penjamin_m penjamin) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT peg_1.pegawai_id,
                    peg_1.nama_pegawai,
                    peg_1.gelardepan,
                    peg_1.alamat_pegawai,
                    peg_1.tanda_tangan
                   FROM pegawai_m peg_1) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT peg_2.pegawai_id,
                    peg_2.nama_pegawai,
                    peg_2.alamat_pegawai,
                    peg_2.tgl_lahirpegawai
                   FROM pegawai_m peg_2) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
             LEFT JOIN ( SELECT a.reseptur_id,
                    a.tglreseptur,
                    a.noresep,
                    a.ruanganreseptur_id,
                    a.catatan,
                    a.antrian_id,
                    a.kategori_resep
                   FROM reseptur_t a) reseptur_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
             LEFT JOIN ( SELECT reseptur_detail.reseptur_id,
                    count(reseptur_detail.racikan_id) FILTER (WHERE reseptur_detail.racikan_id = 1) AS count_obat_racikan
                   FROM resepturdetail_t reseptur_detail
                  WHERE reseptur_detail.is_deleted = false
                  GROUP BY reseptur_detail.reseptur_id
                  ORDER BY reseptur_detail.reseptur_id) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
             LEFT JOIN ( SELECT DISTINCT ON (a.reseptur_id) a.reseptur_id,
                    a.type
                   FROM resepturracikan_t a
                  WHERE a.is_deleted = false) resepturracikan_t ON resepturracikan_t.reseptur_id = reseptur_t.reseptur_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name,
                    lookup_m.lookup_kode
                   FROM lookup_m) kategori_resep_lookup ON reseptur_t.kategori_resep = kategori_resep_lookup.lookup_id
             LEFT JOIN ( SELECT antrian.antrian_id,
                    antrian.no_antrian
                   FROM antrian_t antrian
                  ORDER BY antrian.antrian_id) antrian_t ON COALESCE(penjualanresep_t.antrian_id, reseptur_t.antrian_id) = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) rm ON rm.ruangan_id = reseptur_t.ruanganreseptur_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) im ON im.instalasi_id = rm.instalasi_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) namadepan ON pasien_m.namadepan::integer = namadepan.lookup_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) gelar ON pegawai_m.gelardepan::integer = gelar.lookup_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep,
                    a.norujukan,
                    a.pendaftaran_id
                   FROM bpjs_t a) bpjs_t ON bpjs_t.bpjs_id = COALESCE(pasienadmisi_t.bpjs_id, pendaftaran_t.bpjs_id)) resep;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_025331_migrate_glbj346_view_informasiresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_025331_migrate_glbj346_view_informasiresep_v cannot be reverted.\n";

        return false;
    }
    */
}
