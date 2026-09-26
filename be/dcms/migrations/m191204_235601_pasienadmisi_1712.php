<?php

use yii\db\Migration;

/**
 * Class m191204_235601_pasienadmisi_1712
 */
class m191204_235601_pasienadmisi_1712 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE if exists "public"."pasienadmisi_t" 
                      ADD COLUMN "is_aps" bool,
                      ADD COLUMN "asuransipasien_id" int4;');

        $this->execute('DROP VIEW if exists public.infopasienri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienri_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pasien_m.jeniskelamin AS jeniskelamin_id,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenmedis_t.r_peskk,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
    carabayar_m.groupcarabayar_id AS group_carabayar,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor,
    bpjs_t.nosep,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    pendaftaran_t.keterangan_pendaftaran,
    pasienadmisi_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infopasienri_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infokunjunganri_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokunjunganri_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi,
    pasienadmisi_t.tgl_pulang,
    pasienadmisi_t.kunjungan,
    pasienadmisi_t.status_keluar,
    pasienadmisi_t.rawat_gabung,
    kamarruangan_m.kamarruangan_id,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pasienadmisi_t.pegawai_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pegawai_m.kelompokpegawai_id,
    pasien_m.is_deleted,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    golonganumur_m.golonganumur_nama,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienadmisi_t.created_by,
    kamartempattidur_m.kamartempattidur_id,
    pasienadmisi_t.bpjs_id,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    bpjs_t.nosep,
    kelaspelayanan_m.urutankelas,
    kelaspelayanan_m.bpjs_kelas,
    bpjs_t.klsrawat,
    pasienadmisi_t.is_aps,
    pasienadmisi_t.is_pasientitipan
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN caramasuk_m ON pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infokunjunganri_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infokunjunganrd_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infokunjunganrd_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.agama,
    pasien_m.golongandarah,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.status_pasien,
    pendaftaran_t.kunjungan,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.gelardepan,
    pegawai_m.nama_pegawai,
    pegawai_m.gelarbelakang,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.rhesus,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    bpjs_t.nosep,
    pendaftaran_t.status_periksa AS status_periksa_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
  WHERE instalasi_m.instalasi_id = 2;
");

        $this->execute('ALTER TABLE public.infokunjunganrd_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporankunjunganrd_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporankunjunganrd_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.jeniskelamin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.statusperkawinan,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.nama_pegawai,
    pendaftaran_t.rujukan_id,
    pendaftaran_t.pasienpulang_id,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.pembayaranpelayanan_id,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    pasien_m.warga_negara,
    pasien_m.nama_ibu,
    pasien_m.nama_ayah,
    suku_m.suku_id,
    suku_m.suku_nama,
    pendidikan_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama AS carakeluar,
    kondisikeluar_m.kondisikeluar_id,
    kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    pendaftaran_t.status_periksa AS status_periksa_id,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    bpjs_t.nosep,
    bpjs_t.bpjs_id,
    pendaftaran_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m ON pasien_m.suku_id = suku_m.suku_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
  WHERE instalasi_m.instalasi_id = 2 AND pendaftaran_t.status_periksa::text <> '402'::text;");

        $this->execute('ALTER TABLE public.laporankunjunganrd_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporankunjunganrj_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporankunjunganrj_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.nama_pegawai,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    NULL::integer AS konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    NULL::integer AS ruanganasal_id,
    NULL::character varying AS ruanganasal_nama,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    kondisikeluar_m.kondisikeluar_nama,
    carakeluar_m.carakeluar_nama,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasienbatalperiksa_t.alasan_batal,
    pendaftaran_t.bpjs_id,
    bpjs_t.nosep,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.status_bayar AS status_bayar_id,
    pendaftaran_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.status_periksa::text <> '402'::text
UNION
 SELECT pasien_m.pasien_id,
    pasien_m.no_identitas_pasien,
    pasien_m.nama_pasien,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.tanggal_lahir,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.photopasien,
    pasien_m.alamatemail,
    pasien_m.statusrekammedis,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pendaftaran_t.pendaftaran_id,
    pekerjaan_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.tgl_konsulpoli AS tgl_pendaftaran,
    pendaftaran_t.no_urutantri,
    pendaftaran_t.transportasi,
    pendaftaran_t.keadaan_masuk,
    pendaftaran_t.alih_status,
    pendaftaran_t.by_phone,
    pendaftaran_t.kunjungan_rumah,
    pendaftaran_t.status_masuk,
    pendaftaran_t.umur,
    asuransipasien_m.nokartuasuransi AS no_asuransi,
    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    caramasuk_m.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    pendaftaran_t.shift_id,
    golonganumur_m.golonganumur_id,
    golonganumur_m.golonganumur_nama,
    rujukan_t.no_rujukan,
    rujukan_t.nama_perujuk,
    rujukan_t.tanggal_rujukan,
    rujukan_t.kodediagnosa_rujukan,
    asalrujukan_m.asalrujukan_id,
    asalrujukan_m.asalrujukan_nama,
    penanggungjawab_m.penanggungjawab_id,
    penanggungjawab_m.pengantar,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.ruangan_singkatan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pegawai_m.nama_pegawai,
    asuransipasien_m.status_konfirmasi,
    asuransipasien_m.tgl_konfirmasi,
    pendaftaran_t.pegawai_id,
    pendaftaran_t.tgl_renkontrol,
    pendaftaran_t.pembayaranpelayanan_id,
    pendaftaran_t.panggil_antrian,
    antrian_t.antrian_id,
    antrian_t.tgl_antrian,
    antrian_t.no_antrian,
    antrian_t.panggil_flag,
    loket_m.loket_id,
    loket_m.loket_nama,
    loket_m.loket_fungsi,
    loket_m.loket_singkatan,
    loket_m.loket_nourut,
    loket_m.loket_formatnomor,
    loket_m.loket_maxantrian,
    asuransipasien_m.nopeserta,
    asuransipasien_m.tglcetakkartuasuransi,
    asuransipasien_m.kodefeskestk1,
    asuransipasien_m.nama_feskestk1,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.nokartukeluarga,
    asuransipasien_m.nopassport,
    asuransipasien_m.is_active,
    pendaftaran_t.keterangan_pendaftaran,
    pendaftaran_t.statusdok_rekammedik,
    pegawai_m.kelompokpegawai_id,
    konsulpoli_t.konsulpoli_id,
    pasien_m.is_deleted,
    pasienpulang_t.tglpasienpulang,
    fgetnamalookup(pasien_m.agama::integer) AS jenis_kelamin,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    konsulpoli_t.asalpoliklinikkonsul_id AS ruanganasal_id,
    ruanganasal_m.ruangan_nama AS ruanganasal_nama,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pendaftaran_t.status_pasien::integer) AS status_pasien,
    fgetnamalookup(pendaftaran_t.kunjungan::integer) AS kunjungan,
    fgetnamalookup(pegawai_m.gelardepan::integer) AS gelardepan,
    gelarbelakang.gelarbelakang_nama,
    fgetnamalookup(pasien_m.rhesus::integer) AS rhesus,
    kondisikeluar_m.kondisikeluar_nama,
    carakeluar_m.carakeluar_nama,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasienbatalperiksa_t.alasan_batal,
    pendaftaran_t.bpjs_id,
    bpjs_t.nosep,
    pendaftaran_t.status_periksa AS status_periksa_id,
    pendaftaran_t.status_bayar AS status_bayar_id,
    pendaftaran_t.asuransipasien_id
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
     LEFT JOIN pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ruangan_m ruanganasal_m ON konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN antrian_t ON antrian_t.antrian_id = pendaftaran_t.antrian_id
     LEFT JOIN loket_m ON antrian_t.loket_id = loket_m.loket_id
     LEFT JOIN asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN gelarbelakang_m gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN pasienbatalperiksa_t ON pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
  WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.status_periksa::text <> '402'::text;");

        $this->execute('ALTER TABLE public.laporankunjunganrj_v
  OWNER TO postgres;');

        $this->execute('DROP TRIGGER if exists pendaftaran_inap ON public.pendaftaran_t;');

        $this->execute('DROP FUNCTION if exists public.pendaftaran_inap();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.pendaftaran_inap()
  RETURNS trigger AS
\$BODY\$

DECLARE

    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    paramJson VARCHAR;
    dataAsuransi VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
BEGIN
    paramJson := NEW.additional_data;
    vuser_id := NEW.last_modified_by;
    vpendaftaran_id := NEW.pendaftaran_id;
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    vadmisi := paramJson::json->>'pasien_admisi';
    vmasukkamar := paramJson::json->>'masuk_kamar';
    dataAsuransi := paramJson::json->>'asuransi';
    vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
    vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
    
    IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi,
                    idPasienAsuransi
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                
                IF (idAsuransi IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                        END),
                        dataAsuransi::json->>'namapemilikasuransi',
                        dataAsuransi::json->>'namaperusahaan',
                        dataAsuransi::json->>'nokartuasuransi',
                        dataAsuransi::json->>'nomorpokokperusahaan',
                        dataAsuransi::json->>'status_konfirmasi',
                        (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi;
                    
                    NEW.asuransipasien_id = idAsuransi;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                            END), 
                            namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                            namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                            nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                            status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                            tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi;           
                        
                END IF;
         END IF;
     -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO pasienadmisi_t (
                            pendaftaran_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pasien_id,
                            kamarruangan_id,
                            kamartempattidur_id,
                            kelaspelayanan_id,
                            pegawai_id,
                            tgl_admisi,
                            tgl_pendaftaran,
                            kunjungan,
                            bpjs_id,
                            status_ranap,
                            status_verifikasi,
                            is_skd,
                            is_pasientitipan,
                            is_aps,
                            created_by,
                            asuransipasien_id
         ) VALUES (
                            vpendaftaran_id,
                            (vadmisi::json->>'carabayar_id')::INTEGER,
              (vadmisi::json->>'penjamin_id')::INTEGER,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            (vadmisi::json->>'pasien_id')::INTEGER,
                            (vadmisi::json->>'kamarruangan_id')::INTEGER,
                            (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                            (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                            (vadmisi::json->>'pegawai_id')::INTEGER,
                            (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                            (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                            (vadmisi::json->>'kunjungan')::INTEGER,
                            (vadmisi::json->>'bpjs_id')::INTEGER,
                            (vadmisi::json->>'status_ranap')::INTEGER,
                            (vadmisi::json->>'status_verifikasi')::INTEGER,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            (vadmisi::json->>'is_aps')::BOOL,
                            vuser_id,
                            idAsuransi
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
    
            UPDATE pendaftaran_t
            SET pasienadmisi_id = vpasienadmisi_id
            WHERE pendaftaran_id = vpendaftaran_id;
            
            UPDATE pendaftaran_t
            SET is_ranap = TRUE 
            WHERE pendaftaran_id = vpendaftaranasal_id;
            
     END IF;
     
     -- Insert Masuk Kamar
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO masukkamar_t (
                            pasienadmisi_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pegawai_id,
                            kelaspelayanan_id,
                            kamartempattidur_id,
                            kamarruangan_id,
                            tgl_masukkamar,
                            jam_masukkamar,
                            created_by
         ) VALUES (
                            vpasienadmisi_id,
                            vcarabayar_id,
                            vpenjamin_id,
                            (vmasukkamar::json->>'ruangan_id')::INTEGER,
                            (vmasukkamar::json->>'pegawai_id')::INTEGER,
                            (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                            (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                            (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                            (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                            (vmasukkamar::json->>'jam_masukkamar')::TIME,
                            vuser_id
         ) ;
     
     SELECT 
            CASE pasien_m.jeniskelamin::int4
                WHEN 15 THEN 4
                ELSE 3
            END INTO vkettempattidur_id
        FROM pasien_m
        WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;

        UPDATE kamartempattidur_m
        SET status_isi = TRUE,
                    kettempattidur_id = vkettempattidur_id
        WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;
        
        IF(COALESCE(vkelahiran_id,0) <> 0)
        THEN
            UPDATE kelahiranbayi_t
            SET pendaftaranbaru_id = vpendaftaran_id,
                    last_modified_by = vuser_id,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE kelahiranbayi_id = vkelahiran_id;
        END IF;
     END IF;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.pendaftaran_inap()
  OWNER TO postgres;');

        $this->execute('
            CREATE TRIGGER pendaftaran_inap
              AFTER UPDATE OF additional_data
              ON public.pendaftaran_t
              FOR EACH ROW
              EXECUTE PROCEDURE public.pendaftaran_inap();');

        $this->execute('DROP TRIGGER if exists pendaftaran_t ON public.pendaftaran_t;');

        $this->execute('DROP FUNCTION if exists public.pendaftaran_t();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.pendaftaran_t()
  RETURNS trigger AS
\$BODY\$

DECLARE
    vPrefix VARCHAR;
  vNumber VARCHAR;
    -- Penambahan Triger Dari Yaya
    -- Tanggal 08-08-2019
    dataAntrian VARCHAR;
    dataKarcis VARCHAR;
    dataRujukan VARCHAR;
    dataPenanggung VARCHAR;
    dataAsuransi VARCHAR;
    dataPasien VARCHAR;
    
    paramJson VARCHAR;
    
    -- Get New Pasien
    pasienId INTEGER;
    
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
    -- Generate Id
    rujukanId INTEGER;
    antrianId INTEGER;
    penanggungId INTEGER;
    
    -- Antrian Active
    isActive BOOLEAN;
    
    -- Count Tagihan
    countTagihan INTEGER;
    countPenunjang INTEGER;
    --antrian
    v_konfigantrian INTEGER;
    
    -- Kebutuhan untuk penunjang
    vPenunjang VARCHAR;
    noAntrian VARCHAR;
    idPenunjang INTEGER;
    
    -- Pendaftaran Online
    
    -- Update untuk pasienadmisi ranap
    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
BEGIN
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    
        SELECT 
            CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(substring(no_pendaftaran FROM '[0-9]+')), 6) AS INT), 0) + 1 AS                VARCHAR(6)), 6, '0')) last_no
        INTO 
             vNumber
        FROM pendaftaran_t where instalasi_id = NEW.instalasi_id;
    
        SELECT 
                instalasi_singkatan 
    INTO 
       vPrefix 
    FROM instalasi_m WHERE instalasi_id = NEW.instalasi_id;

        NEW.no_pendaftaran := TRIM(vPrefix) || vNumber;
        
        -- Generate Form Pendaftaran
         paramJson := NEW.additional_data;  
         dataKarcis := paramJson::json->>'tarif';
         dataRujukan := paramJson::json->>'rujukan';
         dataAntrian := paramJson::json->>'antrian';
         dataPenanggung := paramJson::json->>'penanggung_jawab';
         dataAsuransi := paramJson::json->>'asuransi';
         dataPasien := paramJson::json->>'pasien';
         vPenunjang := paramJson::json->>'tarif_penunjang';
         vadmisi := paramJson::json->>'pasien_admisi';
       vmasukkamar := paramJson::json->>'masuk_kamar';
         countTagihan := json_array_length(dataKarcis::json);
         countPenunjang := json_array_length(vPenunjang::json);
         vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
         vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
         
         IF (dataPasien::json->>'nama_pasien' IS NOT NULL AND NEW.pasien_id IS NULL) THEN
                        INSERT INTO pasien_m (
                            tgl_rekam_medik,
                            jenisidentitas,
                            no_identitas_pasien,
                            namadepan,
                            nama_pasien,
                            nama_bin,
                            jeniskelamin,
                            tempat_lahir,
                            tanggal_lahir,
                            golonganumur_id,
                            alamat_pasien,
                            rt,
                            rw,
                            propinsi_id,
                            kabupaten_id,
                            kecamatan_id,
                            kelurahan_id,
                            pendidikan_id,
                            pekerjaan_id,
                            suku_id,
                            statusperkawinan,
                            agama,
                            golongandarah,
                            rhesus,
                            anakke,
                            jumlah_bersaudara,
                            no_telepon_pasien,
                            no_mobile_pasien,
                            warga_negara,
                            photopasien,
                            alamatemail,
                            nama_ibu,
                            nama_ayah,
                            statusrekammedis,
                            alamat_sekarang,
                            is_aps,
                            created_by
                        ) VALUES (
                            (dataPasien::json->>'tgl_rekam_medik')::DATE,
                            
                            (CASE
                                dataPasien::json->>'jenisidentitas'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
                            
                            dataPasien::json->>'no_identitas_pasien',
                            
                            (CASE
                                dataPasien::json->>'namadepan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'namadepan')::INTEGER END),
                            
                            dataPasien::json->>'nama_pasien',
                            dataPasien::json->>'nama_bin',
                            
                            (CASE
                                dataPasien::json->>'jeniskelamin'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
                            
                            dataPasien::json->>'tempat_lahir',
                            (dataPasien::json->>'tanggal_lahir')::DATE,
                            (dataPasien::json->>'golonganumur_id')::INTEGER,
                            (dataPasien::json->>'alamat_pasien'),
                            (CASE
                                dataPasien::json->>'rt'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rt')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rw'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rw')::INTEGER END),
                            (CASE
                                dataPasien::json->>'propinsi_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kabupaten_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kecamatan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kelurahan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pendidikan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pekerjaan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'suku_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'suku_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'statusperkawinan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
                            (CASE
                                dataPasien::json->>'agama'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'agama')::INTEGER END),
                            (CASE
                                dataPasien::json->>'golongandarah'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rhesus'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rhesus')::INTEGER END),
                            (CASE
                                dataPasien::json->>'anakke'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'anakke')::INTEGER END),
                            (CASE
                                dataPasien::json->>'jumlah_bersaudara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
                            dataPasien::json->>'no_telepon_pasien',
                            dataPasien::json->>'no_mobile_pasien',
                            (CASE
                                dataPasien::json->>'warga_negara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
                            
                            dataPasien::json->>'photopasien',
                            dataPasien::json->>'alamatemail',
                            dataPasien::json->>'nama_ibu',
                            dataPasien::json->>'nama_ayah',
                            336,
                            dataPasien::json->>'alamat_sekarang',
                            (dataPasien::json->>'is_aps')::BOOLEAN,
                            NEW.created_by
                        ) RETURNING pasien_id INTO pasienId;
                        NEW.pasien_id := pasienId;
         END IF;
         
         IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi,
                    idPasienAsuransi
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                
--              IF (idPasienAsuransi != NEW.pasien_id) THEN
-- -- Sementara case asuranasi
-- --                   RAISE EXCEPTION 'Duplicate No Asuransi: %', dataAsuransi::json->>'nokartuasuransi' 
-- --                           USING HINT = 'No Asuransi Sudah digunakan';
--              ELSE
                IF (idAsuransi IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                        END),
                        dataAsuransi::json->>'namapemilikasuransi',
                        dataAsuransi::json->>'namaperusahaan',
                        dataAsuransi::json->>'nokartuasuransi',
                        dataAsuransi::json->>'nomorpokokperusahaan',
                        dataAsuransi::json->>'status_konfirmasi',
                        (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi;
                    
                    NEW.asuransipasien_id = idAsuransi;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                            END), 
                            namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                            namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                            nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                            status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                            tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi;           
                        
                        NEW.asuransipasien_id = idAsuransi;
                END IF;
--              END IF;
         END IF;

         IF (dataPenanggung::json->>'pj_pengantar' IS NOT NULL) THEN
                INSERT INTO penanggungjawab_m (
                    pengantar,
                    jenisidentitas,
                    no_identitas,
                    hubungankeluarga,
                    penanggungjawab_nama,
                    penanggungjawab_tempatlahir,
                    penanggungjawab_tgllahir,
                    penanggungjawab_jeniskelamin,
                    penanggungjawab_alamat,
                    penanggungjawab_notelp,
                    penanggungjawab_nohp,
                    pasien_id,
                    created_by
                ) VALUES (
                    dataPenanggung::json->>'pj_pengantar',
                    dataPenanggung::json->>'pj_jenis_identitas',
                    dataPenanggung::json->>'pj_no_identitas',
                    dataPenanggung::json->>'pj_hubungan',
                    dataPenanggung::json->>'pj_nama',
                    dataPenanggung::json->>'pj_tempat_lahir',
                    (dataPenanggung::json->>'pj_tanggal_lahir')::DATE,
                    dataPenanggung::json->>'pj_jk',
                    dataPenanggung::json->>'pj_alamat',
                    dataPenanggung::json->>'pj_no_telepon',
                    dataPenanggung::json->>'pj_no_telepon',
                    NEW.pasien_id,
                    NEW.created_by
                ) RETURNING penanggungjawab_id INTO penanggungId;
         END IF;
         
         
         -- Set Antrian 
         IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS NULL) THEN
                     --get konfigantrian
                    SELECT konfigantrian_id INTO v_konfigantrian
                    from konfigantrian_m
                    WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                    and konfigantrian_m.is_deleted=FALSE 
                    and konfigantrian_m.is_active=true
                    limit 1;
 
                INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                NEW.pendaftaran_id,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian
             ) RETURNING antrian_id INTO antrianId;
                NEW.antrian_id = antrianId;
                
                --- Ini Kondisi Penunjang GET nomor antrian untuk pasien masuk penunjang
                IF (NEW.carabayar_id != 5 AND countPenunjang > 0) THEN
                        SELECT 
                            no_antrian
                        INTO 
                            noAntrian
                        FROM antrian_t WHERE antrian_id = antrianId;
                END IF;
         ELSE
                -- Update Antrian Pendaftaran
                UPDATE antrian_t SET 
                    pendaftaran_id = NEW.pendaftaran_id, 
                    pasien_id = NEW.pasien_id 
                WHERE antrian_id = NEW.antrian_id;
                
                IF (countPenunjang > 0) THEN
                        IF (NEW.carabayar_id != 5) THEN
                            SELECT 
                                no_antrian
                            INTO 
                                noAntrian
                            FROM antrian_t WHERE antrian_id = NEW.antrian_id;
                        END IF;
                ELSE
                    -- Validasi Untuk Non Penunjang 
                    isActive := false;
                    IF (NEW.carabayar_id != 5 OR countTagihan <= 0) THEN
                        isActive := true;
                    END IF;
                    -- Update Pendaftan Poli
                    UPDATE antrian_t SET 
                        pendaftaran_id = NEW.pendaftaran_id, 
                        pasien_id = NEW.pasien_id, 
                        ruangan_id = NEW.ruangan_id,
                        carabayar_id = NEW.carabayar_id,
                        is_active = isActive
                    WHERE antrianasal_id = NEW.antrian_id;  
                END IF;
         END IF;
         
         -- Set Rujukan Jika Ada
         IF (dataRujukan::json->>'rujukandari_id' IS NOT NULL) THEN
                INSERT INTO rujukan_t (
                                asalrujukan_id,
                                rujukandari_id,
                                diagnosa_id,
                                no_rujukan,
                                nama_perujuk,
                                tanggal_rujukan,
                                created_by
             ) VALUES (
                                (dataRujukan::json->>'asalrujukan_id')::INTEGER,
                                (dataRujukan::json->>'rujukandari_id')::INTEGER,
                                (CASE
                                    dataRujukan::json->>'diagnosa_id'
                                WHEN NULL 
                                THEN NULL 
                                ELSE (dataPasien::json->>'diagnosa_id')::INTEGER END),
                                dataRujukan::json->>'no_rujukan',
                                dataRujukan::json->>'nama_perujuk',
                                (dataRujukan::json->>'tanggal_rujukan')::TIMESTAMP,
                                NEW.created_by
             ) RETURNING rujukan_id INTO rujukanId;
                NEW.rujukan_id = rujukanId;
         END IF;
         
         -- Tagihan Karcis
         IF (countTagihan > 0) THEN
                INSERT INTO tindakanpelayanan_t (
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                carabayar_id,
                                pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                created_by
             ) SELECT 
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                carabayar_id,
                                NEW.pendaftaran_id as pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
         END IF;
    
    --- Kondisi Pendaftaran Penunjang
        IF (countPenunjang > 0) THEN
                INSERT INTO pasienmasukpenunjang_t (
                        kelaspelayanan_id,
                        jeniskasuspenyakit_id,
                        pegawai_id,
                        ruangan_id,
                        pasien_id,
                        pendaftaran_id,
                        tglmasukpenunjang,
                        no_antrian,
                        status_periksa,
                        ruanganasal_id,
                        instalasiasal_id,
                        created_by
                ) VALUES (
                        NEW.kelaspelayanan_id,
                        NEW.jeniskasuspenyakit_id,
                        NEW.pegawai_id,
                        NEW.ruangan_id,
                        NEW.pasien_id,
                        NEW.pendaftaran_id,
                        NEW.tgl_pendaftaran,
                        noAntrian,
                        477,
                        NEW.ruangan_id,
                        NEW.instalasi_id,
                        NEW.created_by
                ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                INSERT INTO tindakanpelayanan_t (
                                pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
                                carabayar_id,
                                pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                created_by
             ) SELECT 
                                idPenunjang as pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
                                carabayar_id,
                                NEW.pendaftaran_id as pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,vPenunjang::json);
        END IF;
    
        -- Pendafatran Online
        IF (paramJson::json->>'pendaftaranol_id' IS NOT NULL) THEN
            UPDATE pendaftaranol_t
                SET pendaftaran_id = NEW.pendaftaran_id,
                status_daftar_ol = 565
            WHERE pendaftaranol_id = (paramJson::json->>'pendaftaranol_id')::INTEGER;           
        END IF;
    
    -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO pasienadmisi_t (
                            pendaftaran_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pasien_id,
                            kamarruangan_id,
                            kamartempattidur_id,
                            kelaspelayanan_id,
                            pegawai_id,
                            tgl_admisi,
                            tgl_pendaftaran,
                            kunjungan,
                            bpjs_id,
                            status_ranap,
                            status_verifikasi,
                            is_skd,
                            is_pasientitipan,
                            created_by,
                            is_aps,
                            asuransipasien_id
                    
         ) VALUES (
                            NEW.pendaftaran_id,
                            vcarabayar_id,
                            vpenjamin_id,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            NEW.pasien_id,
                            (vadmisi::json->>'kamarruangan_id')::INTEGER,
                            (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                            (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                            (vadmisi::json->>'pegawai_id')::INTEGER,
                            (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                            (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                            (vadmisi::json->>'kunjungan')::INTEGER,
                            (vadmisi::json->>'bpjs_id')::INTEGER,
                            (vadmisi::json->>'status_ranap')::INTEGER,
                            (vadmisi::json->>'status_verifikasi')::INTEGER,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            NEW.created_by,
                            (vadmisi::json->>'is_aps')::BOOL,
                            idAsuransi
                            
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
            
            NEW.pasienadmisi_id = vpasienadmisi_id;
        

            UPDATE pendaftaran_t
            SET is_ranap = TRUE
            WHERE pendaftaran_id = vpendaftaranasal_id;
             -- Insert Masuk Kamar
            IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
                INSERT INTO masukkamar_t (
                                pasienadmisi_id,
                                carabayar_id,
                                penjamin_id,
                                ruangan_id,
                                pegawai_id,
                                kelaspelayanan_id,
                                kamartempattidur_id,
                                kamarruangan_id,
                                tgl_masukkamar,
                                jam_masukkamar,
                                created_by
             ) VALUES (
                                vpasienadmisi_id,
                                vcarabayar_id,
                                vpenjamin_id,
                                (vmasukkamar::json->>'ruangan_id')::INTEGER,
                                (vmasukkamar::json->>'pegawai_id')::INTEGER,
                                (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                                (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                                (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                                (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                                (vmasukkamar::json->>'jam_masukkamar')::TIME,
                                NEW.created_by
             ) ;
            END IF;
             
            SELECT 
                CASE pasien_m.jeniskelamin::int4
                    WHEN 15 THEN 4
                    ELSE 3
                END INTO vkettempattidur_id
            FROM pasien_m
            WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;

            UPDATE kamartempattidur_m
            SET status_isi = TRUE,
                        kettempattidur_id = vkettempattidur_id
            WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;

            IF(COALESCE(vkelahiran_id,0) <> 0)
            THEN
                UPDATE kelahiranbayi_t
                SET pendaftaranbaru_id = NEW.pendaftaran_id,
                        last_modified_by = NEW.created_by,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE kelahiranbayi_id = vkelahiran_id;
            END IF;
            
     END IF;
        
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.pendaftaran_t()
  OWNER TO postgres;');

        $this->execute('
            CREATE TRIGGER pendaftaran_t
          BEFORE INSERT
          ON public.pendaftaran_t
          FOR EACH ROW
          EXECUTE PROCEDURE public.pendaftaran_t();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191204_235601_pasienadmisi_1712 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191204_235601_pasienadmisi_1712 cannot be reverted.\n";

        return false;
    }
    */
}
