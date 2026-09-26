<?php

use yii\db\Migration;

/**
 * Class m220107_154324_migrate_infopemberianpiutang_v
 */
class m220107_154324_migrate_infopemberianpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopemberianpiutang_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infopemberianpiutang_v\" AS  SELECT 'tagihan_rs'::text AS jenis,
    pemberianpiutang_t.pemberianpiutang_id,
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
    pendaftaran_t.tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip,
    pemberianpiutang_t.created_date
   FROM pemberianpiutang_t
     JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE p.is_deleted = false AND p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN ( SELECT gabung.pendaftaran_id,
            COALESCE(sum(gabung.total_tindakan), 0::double precision) + COALESCE(sum(gabung.total_obat), 0::double precision) AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                    NULL::double precision AS total_obat,
                    tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
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
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
             LEFT JOIN pemberianpiutang_t pemberianpiutang_t_1 ON gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id AND pemberianpiutang_t_1.is_deleted = false
          WHERE gabung.sudah_bayar IS NULL
          GROUP BY gabung.pendaftaran_id) tagihan ON pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id
  WHERE pemberianpiutang_t.is_deleted = false
UNION ALL
 SELECT 'resep_bebas'::text AS jenis,
    pemberianpiutang_t.pemberianpiutang_id,
    pemberianpiutang_t.no_pemberianpiutang,
    pemberianpiutang_t.tgl_pemberianpiutang,
    pemberianpiutang_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    NULL::integer AS pasien_id,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tagihan_resep.tagihan_obat AS tagihan,
    pemberianpiutang_t.total_piutang,
    pemberianpiutang_t.total_sisapiutang,
    pemberianpiutang_t.total_bayarpiutang,
    pemberianpiutang_t.status_piutang,
    fgetnamalookup(pemberianpiutang_t.status_piutang::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    NULL::date AS tanggal_lahir,
    penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
    pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
    peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
    peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip,
    pemberianpiutang_t.created_date
   FROM pemberianpiutang_t
     JOIN penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false
          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
     LEFT JOIN ( SELECT p.pegawai_id,
            p.nomorindukpegawai,
            p.nama_pegawai
           FROM pegawai_m p
          WHERE p.is_deleted = false AND p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
  WHERE pemberianpiutang_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220107_154324_migrate_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220107_154324_migrate_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
