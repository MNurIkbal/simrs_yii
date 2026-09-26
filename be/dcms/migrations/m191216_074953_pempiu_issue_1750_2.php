<?php

use yii\db\Migration;

/**
 * Class m191216_074953_pempiu_issue_1750_2
 */
class m191216_074953_pempiu_issue_1750_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists public.tagihanpasienpulang_v;');

         $this->execute("
          CREATE OR REPLACE VIEW public.tagihanpasienpulang_v AS 
 SELECT gabung.pendaftaran_id,
    gabung.no_pendaftaran,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) AS total_tindakan,
    COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision) AS total_obat,
    COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) AS uang_muka,
    (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer::double precision - COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) AS total_tagihan,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.tgl_pendaftaran,
    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
    COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
    COALESCE(pemberianpiutang_t.total_sisapiutang, 0::double precision) AS total_sisapiutang,
    konfigsystem_k.adm_persen,
    adm.tarif_max
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
            NULL::double precision AS total_obat,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.penjamin_id
           FROM pendaftaran_t
             LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
          GROUP BY pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.pasienpulang_id, pasienadmisi_t.pasienpulang_id, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.penjamin_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            NULL::double precision AS total_tindakan,
            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.obatsudahbayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.penjamin_id
           FROM pendaftaran_t
             LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
          WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
          GROUP BY pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.pasienpulang_id, pasienadmisi_t.pasienpulang_id, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup(pasien_m.jeniskelamin::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.penjamin_id) gabung
     LEFT JOIN pemberianpiutang_t ON gabung.pendaftaran_id = pemberianpiutang_t.pendaftaran_id AND pemberianpiutang_t.is_deleted = false
     LEFT JOIN bayaruangmuka_t ON gabung.pendaftaran_id = bayaruangmuka_t.pendaftaran_id AND bayaruangmuka_t.is_deleted = false
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
     LEFT JOIN ( SELECT tariftindakan_m.tariftindakan_id,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            tariftindakan_m.penjamin_id,
            tariftindakan_m.harga_tariftindakan AS tarif_max,
            konfigsystem_k_1.adm_persen
           FROM tariftindakan_m
             JOIN konfigsystem_k konfigsystem_k_1 ON tariftindakan_m.daftartindakan_id = konfigsystem_k_1.adm_tindakan_id
          WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.komponentarif_id = 6) adm ON adm.kelaspelayanan_id = gabung.kelaspelayanan_id AND adm.penjamin_id = gabung.penjamin_id
  WHERE gabung.sudah_bayar IS NULL
  GROUP BY gabung.pendaftaran_id, gabung.no_pendaftaran, gabung.no_rekam_medik, gabung.nama_pasien, gabung.tanggal_lahir, gabung.umur, gabung.tgl_pendaftaran, (COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision)), (COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision)), pemberianpiutang_t.total_piutang, pemberianpiutang_t.total_sisapiutang, konfigsystem_k.adm_persen, adm.tarif_max;
");

         $this->execute('ALTER TABLE public.tagihanpasienpulang_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infopemberianpiutang_v;');

         $this->execute("
          CREATE OR REPLACE VIEW public.infopemberianpiutang_v AS 
 SELECT pemberianpiutang_t.pemberianpiutang_id,
    pemberianpiutang_t.no_pemberianpiutang,
    pemberianpiutang_t.tgl_pemberianpiutang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.total_tagihan AS tagihan,
    pemberianpiutang_t.total_piutang,
    pemberianpiutang_t.total_sisapiutang,
    pemberianpiutang_t.total_bayarpiutang,
    pemberianpiutang_t.status_piutang,
    fgetnamalookup(pemberianpiutang_t.status_piutang::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    pasien_m.tanggal_lahir,
    pendaftaran_t.tgl_pendaftaran
   FROM pemberianpiutang_t
     JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT gabung.pendaftaran_id,
            (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                    NULL::double precision AS total_obat,
                    tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, tindakanpelayanan_t.tindakansudahbayar_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    NULL::double precision AS total_tindakan,
                    sum(obatalkespasien_t.hargajual_oa) AS total_obat,
                    obatalkespasien_t.obatsudahbayar_id
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
             LEFT JOIN pemberianpiutang_t pemberianpiutang_t_1 ON gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id AND pemberianpiutang_t_1.is_deleted = false
          WHERE gabung.sudah_bayar IS NULL
          GROUP BY gabung.pendaftaran_id) tagihan ON pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id
  WHERE pemberianpiutang_t.is_deleted = false;");

         $this->execute('ALTER TABLE public.infopemberianpiutang_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infopembayaranpiutang_v;');

         $this->execute("
          CREATE OR REPLACE VIEW public.infopembayaranpiutang_v AS 
 SELECT pembayaranpiutang_t.pembayaranpiutang_id,
    pembayaranpiutang_t.no_pembayaranpiutang,
    pembayaranpiutang_t.tgl_pembayaranpiutang,
    pembayaranpiutang_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pembayaranpiutang_t.total_bayarpiutang
   FROM pembayaranpiutang_t
     JOIN pendaftaran_t ON pembayaranpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pembayaranpiutang_t.is_deleted = false;");

         $this->execute('ALTER TABLE public.infopembayaranpiutang_v
  OWNER TO postgres;
');
               
       $this->execute('DROP VIEW if exists public.pasien_v;');

        $this->execute("
          CREATE OR REPLACE VIEW public.pasien_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jenisidentitas,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS identitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.statusperkawinan,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS status_perkawinan,
    pasien_m.nama_ibu,
    pasien_m.alamat_sekarang,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.no_mobile_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.warga_negara,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warganegara,
    pasien_m.agama,
    fgetnamalookup(pasien_m.agama::integer) AS agama_pasien,
    pasien_m.alamatemail,
    pasien_m.suku_id,
    suku.suku_nama,
    pasien_m.nama_ayah,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.golongandarah,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongan_darah,
    pasien_m.photopasien,
    pasien_m.is_aps,
    dokrekammedis_m.dokrekammedis_id,
    pasien_m.nopeserta_bpjs,
    pasien_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    COALESCE(piutang.total_sisapiutang, 0::double precision) AS total_sisapiutang
   FROM pasien_m
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN suku_m suku ON pasien_m.suku_id = suku.suku_id
     LEFT JOIN dokrekammedis_m ON pasien_m.pasien_id = dokrekammedis_m.pasien_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN ( SELECT pendaftaran_t.pasien_id,
            sum(pemberianpiutang_t.total_sisapiutang) AS total_sisapiutang
           FROM pemberianpiutang_t
             JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
          GROUP BY pendaftaran_t.pasien_id) piutang ON pasien_m.pasien_id = piutang.pasien_id
  WHERE pasien_m.is_active = true AND pasien_m.is_deleted = false;");

         $this->execute('ALTER TABLE public.pasien_v
  OWNER TO postgres;');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191216_074953_pempiu_issue_1750_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_074953_pempiu_issue_1750_2 cannot be reverted.\n";

        return false;
    }
    */
}
