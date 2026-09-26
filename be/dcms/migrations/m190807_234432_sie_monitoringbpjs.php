<?php

use yii\db\Migration;

/**
 * Class m190807_234432_sie_monitoringbpjs
 */
class m190807_234432_sie_monitoringbpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     DROP VIEW if exists public.sie_monitoringbpjs;
        ');

        $this->execute("
     CREATE OR REPLACE VIEW public.sie_monitoringbpjs AS 
 SELECT x.jenis,
    x.pendaftaran_id,
    x.pasienadmisi_id,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.monitorbpjs_id,
    x.kelompok,
    x.sub_total,
    monitorbpjspersen.persen,
    monitorbpjspersen.persen_kelompok,
    monitorbpjspersen.total_persen,
    monitorbpjspersen.total_persenkelompok
   FROM ( SELECT 'TINDAKAN'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            monitorbpjs_m.monitorbpjs_id,
            monitorbpjs_m.kelompoktindakan_nama AS kelompok,
            sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
                    monitorbpjs_m_1.kelompoktindakan_nama,
                    monitorbpjsdetail_m.groupinacbg_id
                   FROM monitorbpjs_m monitorbpjs_m_1
                     JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
                  WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON daftartindakan_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, monitorbpjs_m.monitorbpjs_id, monitorbpjs_m.kelompoktindakan_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT 'PAKET'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            monitorbpjs_m.monitorbpjs_id,
            monitorbpjs_m.kelompoktindakan_nama AS kelompok,
            sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
                    monitorbpjs_m_1.kelompoktindakan_nama,
                    monitorbpjsdetail_m.groupinacbg_id
                   FROM monitorbpjs_m monitorbpjs_m_1
                     JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
                  WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON daftartindakan_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, monitorbpjs_m.monitorbpjs_id, monitorbpjs_m.kelompoktindakan_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT 'OBAT'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            monitorbpjs_m.monitorbpjs_id,
            monitorbpjs_m.kelompoktindakan_nama AS kelompok,
            sum(obatalkespasien_t.hargajual_oa) AS sub_total
           FROM pendaftaran_t
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
                    monitorbpjs_m_1.kelompoktindakan_nama,
                    monitorbpjsdetail_m.groupinacbg_id
                   FROM monitorbpjs_m monitorbpjs_m_1
                     JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
                  WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON obatalkes_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, monitorbpjs_m.monitorbpjs_id, monitorbpjs_m.kelompoktindakan_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date)) x
     LEFT JOIN ( SELECT monitorbpjspersen_r.monitorbpjspersen_id,
            monitorbpjspersen_r.pendaftaran_id,
            monitorbpjspersen_r.pasienadmisi_id,
            monitorbpjspersen_r.monitorbpjs_id,
            monitorbpjspersen_r.persen,
            monitorbpjspersen_r.persen_kelompok,
            monitorbpjspersen_r.total_persen,
            monitorbpjspersen_r.total_persenkelompok
           FROM monitorbpjspersen_r
             JOIN ( SELECT max(monitorbpjspersen_r_1.monitorbpjspersen_id) AS monitorbpjspersen_id,
                    monitorbpjspersen_r_1.monitorbpjs_id
                   FROM monitorbpjspersen_r monitorbpjspersen_r_1
                  GROUP BY monitorbpjspersen_r_1.monitorbpjs_id) monitoribpjspersen_max ON monitoribpjspersen_max.monitorbpjspersen_id = monitorbpjspersen_r.monitorbpjspersen_id) monitorbpjspersen ON x.pendaftaran_id = monitorbpjspersen.pendaftaran_id AND x.pasienadmisi_id = monitorbpjspersen.pasienadmisi_id AND x.monitorbpjs_id = monitorbpjspersen.monitorbpjs_id;
        ");

        $this->execute('
     ALTER TABLE public.sie_monitoringbpjs
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_234432_sie_monitoringbpjs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190807_234432_sie_monitoringbpjs cannot be reverted.\n";

        return false;
    }
    */
}
