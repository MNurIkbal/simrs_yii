<?php

use yii\db\Migration;

/**
 * Class m190719_101520_cektagihan_v
 */
class m190719_101520_cektagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP VIEW if exists public.cektagihan_v;
        ');

        $this->execute('
         CREATE OR REPLACE VIEW public.cektagihan_v AS 
 SELECT hitung.pendaftaran_id,
    hitung.pasien_id,
    hitung.no_rekam_medik,
    hitung.total_tagihan,
    rincianpasiendetail.total_sdh_bayar
   FROM ( SELECT tagihan.pendaftaran_id,
            tagihan.pasien_id,
            tagihan.no_rekam_medik,
            sum(tagihan.total_tagihan) AS total_tagihan
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan
                   FROM pendaftaran_t
                     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik
                UNION ALL
                 SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    sum(obatalkespasien_t.hargajual_oa) AS total_tagihan
                   FROM pendaftaran_t
                     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik) tagihan
          GROUP BY tagihan.pendaftaran_id, tagihan.pasien_id, tagihan.no_rekam_medik) hitung
     JOIN ( SELECT tagihan.pendaftaran_id,
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
                    sum(pembayaranpelayanan_t.total_biayapelayanan) AS tagihan_tindakan_obat,
                    sum(pembayaranpelayanan_t.total_terbayar) AS tagihan_sudah_bayar,
                    0 AS tagihan_uang_muka,
                    sum(pembayaranpelayanan_t.biaya_administrasi) AS tagihan_biaya_admin,
                    sum(pembayaranpelayanan_t.pembulatan) AS tagihan_pembulatan,
                    sum(pembayaranpelayanan_t.total_subsidiasuransi) AS tagihan_asuransi,
                    sum(pembayaranpelayanan_t.total_bayartindakan) AS pembayaran_pasien,
                    pendaftaran_t.pasien_id
                   FROM pendaftaran_t
                     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
                  GROUP BY pendaftaran_t.pendaftaran_id, pembayaranpelayanan_t.biaya_administrasi, pembayaranpelayanan_t.pembulatan, pendaftaran_t.pasienadmisi_id, pembayaranpelayanan_t.total_subsidiasuransi) tagihan
             LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
          GROUP BY tagihan.pendaftaran_id, tagihan.pasienadmisi_id, pasien_m.no_rekam_medik) rincianpasiendetail ON hitung.pendaftaran_id = rincianpasiendetail.pendaftaran_id;
        ');

        $this->execute('
         ALTER TABLE public.cektagihan_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_101520_cektagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_101520_cektagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
