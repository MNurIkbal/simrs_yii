<?php

use yii\db\Migration;

/**
 * Class m200122_073517_tagihanpasienpulang_v
 */
class m200122_073517_tagihanpasienpulang_v extends Migration
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
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             LEFT JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
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
             LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
             LEFT JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_073517_tagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_073517_tagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }
    */
}
