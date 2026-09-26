<?php

use yii\db\Migration;

/**
 * Class m200421_030404_migrate_20200420
 */
class m200421_030404_migrate_20200420 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
                            ADD COLUMN "dash_kamarheader" text COLLATE "pg_catalog"."default",
                            ADD COLUMN "dash_kamardetail" text COLLATE "pg_catalog"."default",
                            ADD COLUMN "dash_kamarfooter" text COLLATE "pg_catalog"."default",
                            ADD COLUMN "dash_logo" text COLLATE "pg_catalog"."default";');

        $this->execute('DROP VIEW if exists "public"."infodatapendaftaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infodatapendaftaran_v\" AS  SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN (data_info.tglpasienpulang IS NULL) THEN data_info.tglpasienpulang_ri
            ELSE data_info.tglpasienpulang
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idpendaftaran IS NOT NULL)) THEN data_info.no_bpjspendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NOT NULL)) THEN data_info.no_bpjsadmisi
            WHEN ((data_info.pasienadmisi_id IS NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransipendaftaran
            WHEN ((data_info.pasienadmisi_id IS NOT NULL) AND (data_info.bpjs_idadmisi IS NULL)) THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN (data_info.pasienadmisi_id IS NULL) THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran,
    (data_info.tagihan_belumbayar)::integer AS tagihan_belumbayar,
    (data_info.sisa_penunjang)::integer AS sisa_penunjang,
    (data_info.sisa_karcis)::integer AS sisa_karcis,
    (data_info.sisa_obat)::integer AS sisa_obat,
    data_info.status_periksa
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            ((COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, (0)::double precision)) - COALESCE(pengembalianuangmuka_t.total_pengembalian, (0)::double precision)) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN (penjualan_resep.biaya_adm IS NULL) THEN (0)::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran,
            COALESCE(belum_bayar.total_tagihan, (0)::double precision) AS tagihan_belumbayar,
            COALESCE(sisa_penunjang.total_tagihan, (0)::double precision) AS sisa_penunjang,
            COALESCE(sisa_karcis.total_tagihan, (0)::double precision) AS sisa_karcis,
            COALESCE(sisa_obat.total_tagihan, (0)::double precision) AS sisa_obat,
            pendaftaran_t.status_periksa
           FROM (((((((((((((((((((((((((((((((pendaftaran_t
             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
             JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
             JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN ruangan_m ruang_ri ON ((pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id)))
             LEFT JOIN instalasi_m ins_ri ON ((ruang_ri.instalasi_id = ins_ri.instalasi_id)))
             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN carabayar_m carabayar_ri ON ((pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id)))
             LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id)))
             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
             LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE (bayaruangmuka_t_1.is_deleted = false)
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
             LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE (pengembalianuangmuka_t_1.is_deleted = false)
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id)))
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE (pemakaianuangmuka_t_1.is_deleted = false)
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON ((pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
             LEFT JOIN pegawai_m dok_rj_rd ON ((pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id)))
             LEFT JOIN pegawai_m dok_ri ON ((pasienadmisi_t.pegawai_id = dok_ri.pegawai_id)))
             LEFT JOIN bpjs_t bpjs_pendaftaran ON ((pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id)))
             LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON ((pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id)))
             LEFT JOIN asuransipasien_m asuransi_admisi ON ((pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id)))
             LEFT JOIN pemberianpiutang_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE ((pt.status_bayar = 349) AND (pt.is_deleted = false))
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON ((penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM tindakanpelayanan_t
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) belum_bayar ON ((pendaftaran_t.pendaftaran_id = belum_bayar.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL) AND (daftartindakan_m.kelompoktindakan_id <> 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_penunjang ON ((pendaftaran_t.pendaftaran_id = sisa_penunjang.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                           FROM (tindakanpelayanan_t
                             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (daftartindakan_m.kelompoktindakan_id = 17))
                          GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_karcis ON ((pendaftaran_t.pendaftaran_id = sisa_karcis.pendaftaran_id)))
             LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                    sum(tagihan.tagihan) AS total_tagihan
                   FROM ( SELECT obatalkespasien_t.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan
                           FROM obatalkespasien_t
                          WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                          GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                  GROUP BY tagihan.pendaftaran_id) sisa_obat ON ((pendaftaran_t.pendaftaran_id = sisa_obat.pendaftaran_id)))
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan, sisa_penunjang.total_tagihan, sisa_karcis.total_tagihan, COALESCE(sisa_obat.total_tagihan, (0)::double precision), pendaftaran_t.status_periksa
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) AS obat,
            ((COALESCE(penjualanresep_t.totalhargajual, (0)::double precision) + COALESCE(penjualanresep_t.totaltarifservice, (0)::double precision)) + COALESCE(penjualanresep_t.biayaadministrasi, (0)::double precision)) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            pemberianpiutang_t.total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran,
            (tagihan_resep.tagihan_obat)::integer AS tagihan_belumbayar,
            0 AS sisa_penunjang,
            0 AS sisa_karcis,
            (tagihan_resep.tagihan_obat)::integer AS sisa_obat,
            NULL::character varying AS status_periksa
           FROM ((((((((penjualanresep_t
             LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN ruangan_m ON ((penjualanresep_t.ruangan_id = ruangan_m.ruangan_id)))
             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN carabayar_m ON ((penjualanresep_t.carabayar_id = carabayar_m.carabayar_id)))
             LEFT JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
             LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
             LEFT JOIN pemberianpiutang_t ON ((penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id)))
             LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                   FROM obatalkespasien_t
                  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.obatsudahbayar_id IS NULL))
                  GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON ((penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id)))
          WHERE (((penjualanresep_t.jenispenjualan)::text = ANY (ARRAY['343'::text, '345'::text])) AND (penjualanresep_t.is_deleted = false))) data_info;
");

        $this->execute('ALTER TABLE "public"."infodatapendaftaran_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    (cppt_t.a_diag_utama ->> 'text'::text) AS a_diag_utama
   FROM ((((((((((((((((pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN cppt_t ON (((pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id) AND (pasienadmisi_t.pegawai_id = cppt_t.pegawai_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true) AND (cppt_t.is_instruksi_pulang = false))))
  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 12) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama
   FROM (((((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m dok_perujuk ON ((pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
     JOIN instalasi_m instalasi_asal ON ((pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN rencanaoperasi_t ON ((pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id)))
  WHERE ((pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pendaftaran_t.instalasi_id = 12));
");

        $this->execute('ALTER TABLE "public"."infopasienoperasi_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."rinciankelompoktindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rinciankelompoktindakan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    kelompoktindakan_m.kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM (((pendaftaran_t
     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
  WHERE (tindakanpelayanan_t.is_deleted = false)
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, kelompoktindakan_m.kelompoktindakan_nama
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    tipepaket_m.tipepaket_nama AS kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total
   FROM ((pendaftaran_t
     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
  WHERE (tindakanpelayanan_t.is_deleted = false)
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, tipepaket_m.tipepaket_nama
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    'obat'::text AS kelompoktindakan_nama,
    sum(obatalkespasien_t.hargajual_oa) AS total
   FROM ((pendaftaran_t
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
  WHERE (obatalkespasien_t.is_deleted = false)
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, 'obat'::text;
");

        $this->execute('ALTER TABLE "public"."rinciankelompoktindakan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."rincianpasiendetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"rincianpasiendetail_v\" AS  SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.daftartindakan_kode AS kode
   FROM (((((((((((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
     LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
     LEFT JOIN instalasi_m instalasi_pelayanan ON ((tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id)))
     LEFT JOIN ruangan_m ruangan_pelayanan ON ((tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id)))
     LEFT JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
     LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
     LEFT JOIN pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
     LEFT JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi,
    0 AS kelompoktindakan_id,
    'Obat'::character varying AS kelompoktindakan_nama,
    obatalkes_m.obatalkes_kode AS kode
   FROM (((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m r_pendaftaran ON ((pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id)))
     LEFT JOIN ruangan_m r_admisi ON ((pasienadmisi_t.ruangan_id = r_admisi.ruangan_id)))
     LEFT JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)));
");
        $this->execute('ALTER TABLE "public"."rincianpasiendetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infokartustokobatnew_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokartustokobatnew_v\" AS  SELECT kartu_stok.stokobatalkes_id,
    kartu_stok.obatalkes_id,
    kartu_stok.tanggal_transaksi,
    kartu_stok.no_transaksi,
    obatalkes_m.obatalkes_nama,
    kartu_stok.qtystok_in,
    kartu_stok.qtystok_out,
    kartu_stok.stok_tersedia AS stok,
    satuanunit_m.satuanunit_nama,
    kartu_stok.tglkadaluarsa,
    kartu_stok.keterangan,
    kartu_stok.ruangan_asal_id,
    kartu_stok.ruangan_tujuan_id,
    ruangan_asal.ruangan_nama AS ruangan_asal_nama,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan_nama,
        CASE
            WHEN (kartu_stok.keterangan = 'Penerimaan Mutasi'::text) THEN ruangan_asal.ruangan_nama
            WHEN (kartu_stok.keterangan = 'Mutasi Obat'::text) THEN ruangan_tujuan.ruangan_nama
            ELSE kartu_stok.reference
        END AS reference,
    kartu_stok.stok_tersedia,
        CASE
            WHEN (kartu_stok.keterangan = 'Penerimaan Mutasi'::text) THEN kartu_stok.ruangan_asal_id
            WHEN (kartu_stok.keterangan = 'Mutasi Obat'::text) THEN kartu_stok.ruangan_tujuan_id
            ELSE kartu_stok.ruangan_asal_id
        END AS ruangan_id
   FROM ((((( SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
                CASE
                    WHEN (penjualan_resep.penjualanresep_id IS NULL) THEN 'BMHP'::text
                    ELSE 'Penjualan Resep'::text
                END AS keterangan,
            penjualan_resep.no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            penjualan_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT obatalkespasien_t.obatalkespasien_id,
                    obatalkespasien_t.penjualanresep_id,
                        CASE
                            WHEN (penjualanresep_t.noresep IS NOT NULL) THEN penjualanresep_t.noresep
                            ELSE pendaftaran_t.no_pendaftaran
                        END AS no_transaksi,
                        CASE
                            WHEN (obatalkespasien_t.penjualanresep_id IS NULL) THEN pasien_pendaftaran.nama_pasien
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM ((((obatalkespasien_t
                     LEFT JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
                     LEFT JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                     LEFT JOIN pasien_m pasien_pendaftaran ON ((pendaftaran_t.pasien_id = pasien_pendaftaran.pasien_id)))) penjualan_resep ON ((stokobatalkes_t.obatalkespasien_id = penjualan_resep.obatalkespasien_id)))
          WHERE ((stokobatalkes_t.is_deleted = false) AND (stokobatalkes_t.returresepdetail_id IS NULL) AND (stokobatalkes_t.pembatalanresep_id IS NULL))
          GROUP BY
                CASE
                    WHEN (penjualan_resep.penjualanresep_id IS NULL) THEN 'BMHP'::text
                    ELSE 'Penjualan Resep'::text
                END, penjualan_resep.no_transaksi, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.qtystok_in, penjualan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Pembatalan Resep'::text AS keterangan,
            pembatalan_resep.no_pembatalan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            pembatalan_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pembatalanresep_t.pembatalanresep_id,
                    pembatalanresep_t.tgl_pembatalan,
                    pembatalanresep_t.no_pembatalan,
                        CASE
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM ((pembatalanresep_t
                     JOIN penjualanresep_t ON ((pembatalanresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))) pembatalan_resep ON ((pembatalan_resep.pembatalanresep_id = stokobatalkes_t.pembatalanresep_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY pembatalan_resep.no_pembatalan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, pembatalan_resep.reference, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Retur Resep'::text AS keterangan,
            retur_resep.no_returresep AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            retur_resep.reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT returresepdetail_t.returresepdetail_id,
                    returresep_t.no_returresep,
                    returresep_t.tgl_retur,
                        CASE
                            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.nama_pasien
                            ELSE penjualanresep_t.nama_pembeli
                        END AS reference
                   FROM (((returresepdetail_t
                     JOIN returresep_t ON ((returresepdetail_t.returresep_id = returresep_t.returresep_id)))
                     JOIN penjualanresep_t ON ((returresep_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
                     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))) retur_resep ON ((stokobatalkes_t.returresepdetail_id = retur_resep.returresepdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Adjusmen Masuk'::text AS keterangan,
            adjusmen_masuk.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            max(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT adjusmenobatmasuk_t.adjusmenobatmasuk_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM (adjusmenobatmasuk_t
                     JOIN adjusmenobat_t ON ((adjusmenobatmasuk_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjusmen_masuk ON ((stokobatalkes_t.adjusmenobatmasuk_id = adjusmen_masuk.adjusmenobatmasuk_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY adjusmen_masuk.no_adjusmen, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Adjusmen Keluar'::text AS keterangan,
            adjusmen_keluar.no_adjusmen AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT adjusmenobatkeluar_t.adjusmenobatkeluar_id,
                    adjusmenobat_t.no_adjusmen,
                    adjusmenobat_t.tgl_adjusmen
                   FROM (adjusmenobatkeluar_t
                     JOIN adjusmenobat_t ON ((adjusmenobatkeluar_t.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))) adjusmen_keluar ON ((stokobatalkes_t.adjusmenobatkeluar_id = adjusmen_keluar.adjusmenobatkeluar_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY adjusmen_keluar.no_adjusmen, stokobatalkes_t.tglstok_out, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT max(stokobatalkes_t.stokobatalkes_id) AS stokobatalkes_id,
            'Penerimaan Alternatif'::text AS keterangan,
            penerimaan_alternatif.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            sum(stokobatalkes_t.qtystok_in) AS qtystok_in,
            sum(stokobatalkes_t.qtystok_out) AS qtystok_out,
            min(stokobatalkes_t.stok_tersedia) AS stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT penerimaansuppdetail_t.penerimaansuppdetail_id,
                    penerimaansupp_t.no_penerimaan,
                    penerimaansupp_t.tgl_penerimaan
                   FROM (penerimaansupp_t
                     JOIN penerimaansuppdetail_t ON ((penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id)))) penerimaan_alternatif ON ((stokobatalkes_t.penerimaansuppdetail_id = penerimaan_alternatif.penerimaansuppdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY 'Penerimaan Alternatif'::text, penerimaan_alternatif.no_penerimaan, stokobatalkes_t.tglstok_in, stokobatalkes_t.obatalkes_id, stokobatalkes_t.satuankecil_id, stokobatalkes_t.tglkadaluarsa, '-'::character varying, stokobatalkes_t.ruangan_id
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Pemakaian Ruangan'::text AS keterangan,
            pemakaian_ruangan.nopemakaian_obat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
                    pemakaianobat_t.nopemakaian_obat,
                    pemakaianobat_t.tglpemakaianobat
                   FROM (pemakaianobat_t
                     JOIN pemakaianobatdetail_t ON ((pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id)))) pemakaian_ruangan ON ((stokobatalkes_t.pemakaianobatdetail_id = pemakaian_ruangan.pemakaianobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Pemusnahan Obat'::text AS keterangan,
            pemusnahan_obat.nopemusnahan AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT pemusnahanobatdetail_t.pemusnahanobatdetail_id,
                    pemusnahanobat_t.nopemusnahan,
                    pemusnahanobat_t.tglpemusnahan
                   FROM (pemusnahanobat_t
                     JOIN pemusnahanobatdetail_t ON ((pemusnahanobat_t.pemusnahanobat_id = pemusnahanobatdetail_t.pemusnahanobat_id)))) pemusnahan_obat ON ((stokobatalkes_t.pemusnahanobatdetail_id = pemusnahan_obat.pemusnahanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Stok Opname'::text AS keterangan,
            stok_opname.nostokopname AS no_transaksi,
            stok_opname.tglstokopname AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                    stokopname_t.nostokopname,
                    stokopname_t.tglstokopname
                   FROM (stokopname_t
                     JOIN stokopnamedetail_t ON ((stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id)))) stok_opname ON ((stokobatalkes_t.stokopnamedetail_id = stok_opname.stokopnamedetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Penerimaan Supplier'::text AS keterangan,
            penerimaan_supp.no_penerimaan AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
                    penerimaanobat_t.no_penerimaan,
                    penerimaanobat_t.tgl_penerimaan
                   FROM (penerimaanobat_t
                     JOIN penerimaanobatdetail_t ON ((penerimaanobat_t.penerimaanobat_id = penerimaanobatdetail_t.penerimaanobat_id)))) penerimaan_supp ON ((stokobatalkes_t.penerimaanobatdetail_id = penerimaan_supp.penerimaanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Retur Penerimaan Supplier'::text AS keterangan,
            retur_penerimaan.no_returpenerimaanobat AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            stokobatalkes_t.ruangan_id AS ruangan_asal_id,
            stokobatalkes_t.ruangan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobatdetail_id,
                    returpenerimaanobat_t.no_returpenerimaanobat,
                    returpenerimaanobat_t.tgl_retur
                   FROM (returpenerimaanobat_t
                     JOIN returpenerimaanobatdetail_t ON ((returpenerimaanobatdetail_t.returpenerimaanobat_id = returpenerimaanobat_t.returpenerimaanobat_id)))) retur_penerimaan ON ((stokobatalkes_t.returpenerimaanobatdetail_id = retur_penerimaan.returpenerimaanobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Penerimaan Mutasi'::text AS keterangan,
            terima_mutasi.noterimamutasi AS no_transaksi,
            stokobatalkes_t.tglstok_in AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            terima_mutasi.ruanganasal_id AS ruangan_asal_id,
            terima_mutasi.ruanganpenerima_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT terimamutasiobatdetail_t.terimamutasiobatdetail_id,
                    terimamutasiobat_t.noterimamutasi,
                    terimamutasiobat_t.tglterima,
                    terimamutasiobat_t.ruanganpenerima_id,
                    terimamutasiobat_t.ruanganasal_id
                   FROM (terimamutasiobat_t
                     JOIN terimamutasiobatdetail_t ON ((terimamutasiobat_t.terimamutasiobat_id = terimamutasiobatdetail_t.terimamutasiobat_id)))) terima_mutasi ON ((stokobatalkes_t.terimamutasidetail_id = terima_mutasi.terimamutasiobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)
        UNION ALL
         SELECT stokobatalkes_t.stokobatalkes_id,
            'Mutasi Obat'::text AS keterangan,
            mutasi_obat.nomutasioa AS no_transaksi,
            stokobatalkes_t.tglstok_out AS tanggal_transaksi,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.satuankecil_id AS satuanunit_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.stok_tersedia,
            '-'::character varying AS reference,
            mutasi_obat.ruanganasal_id AS ruangan_asal_id,
            mutasi_obat.ruangantujuan_id AS ruangan_tujuan_id
           FROM (stokobatalkes_t
             JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                    mutasiobatruangan_t.nomutasioa,
                    mutasiobatruangan_t.tglmutasioa,
                    mutasiobatruangan_t.ruanganasal_id,
                    mutasiobatruangan_t.ruangantujuan_id
                   FROM (mutasiobatruangan_t
                     JOIN mutasiobatdetail_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))) mutasi_obat ON ((stokobatalkes_t.mutasiobatdetail_id = mutasi_obat.mutasiobatdetail_id)))
          WHERE (stokobatalkes_t.is_deleted = false)) kartu_stok
     JOIN obatalkes_m ON ((kartu_stok.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m ON ((kartu_stok.satuanunit_id = satuanunit_m.satuanunit_id)))
     LEFT JOIN ruangan_m ruangan_asal ON ((kartu_stok.ruangan_asal_id = ruangan_asal.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((kartu_stok.ruangan_tujuan_id = ruangan_tujuan.ruangan_id)));");

        $this->execute('ALTER TABLE "public"."infokartustokobatnew_v" OWNER TO "postgres";');

        $this->execute("
            CREATE VIEW \"public\".\"infotarifrskamar_v\" AS  SELECT 'kamar'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    NULL::text AS is_default,
    daftartindakan_m.is_akomodasi,
    penjamin_m.carabayar_id,
    daftartindakan_m.is_konsultasi,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    tariftindakan_m.kamarruangan_id
   FROM ((((((((((tariftindakan_m
     JOIN kamarruangan_m ON ((tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON (((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id) AND (komponentarif_m.is_deleted IS FALSE))))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
  WHERE ((perdatarif_m.is_active = true) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL));");
       
        $this->execute('ALTER TABLE "public"."infotarifrskamar_v" OWNER TO "postgres";');

        $this->execute("
            CREATE VIEW \"public\".\"dashboardkamarkosong_v\" AS  SELECT kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas,
    count(kamartempattidur_m.kamarruangan_id) AS jumlah_kosong
   FROM (((kamarruangan_m
     JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kelaspelayanan_m ON ((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
  WHERE ((kamarruangan_m.is_deleted = false) AND (kamartempattidur_m.is_deleted = false) AND (kamartempattidur_m.status_isi = false))
  GROUP BY kamarruangan_m.ruangan_id, ruangan_m.ruangan_nama, kamarruangan_m.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, kamarruangan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama;");
        
        $this->execute('ALTER TABLE "public"."dashboardkamarkosong_v" OWNER TO "postgres";');
        
        $this->execute("
            CREATE VIEW \"public\".\"infotarifakomodasi_v\" AS  SELECT kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    tariftindakan_m.kamarruangan_id,
    tariftindakan_m.penjamin_id,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS kamarruangan_jenis,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur,
    kamartempattidur_m.kettempattidur_id,
    kamartempattidur_m.status_isi,
    kettempattidur_m.kode_warna,
    daftartindakan_m.is_akomodasi,
    tariftindakan_m.harga_tariftindakan,
    NULL::text AS isi_jk
   FROM (((((((tariftindakan_m
     JOIN kamarruangan_m ON ((tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
     JOIN kettempattidur_m ON ((kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id)))
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)));");
        
        $this->execute('ALTER TABLE "public"."infotarifakomodasi_v" OWNER TO "postgres";');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_030404_migrate_20200420 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200421_030404_migrate_20200420 cannot be reverted.\n";

        return false;
    }
    */
}
