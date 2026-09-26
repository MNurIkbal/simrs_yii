<?php

use yii\db\Migration;

/**
 * Class m190805_035601_infomonitoringbpjsdetail_v
 */
class m190805_035601_infomonitoringbpjsdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
        DROP VIEW if exists public.infomonitoringbpjsdetail_v;
        ');

          $this->execute("
        CREATE OR REPLACE VIEW public.infomonitoringbpjsdetail_v AS 
 SELECT 'tindakan'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    monitorbpjs_m.monitorbpjs_id,
    monitorbpjs_m.kelompoktindakan_nama AS kelompok,
    monitorbpjs_m.groupinacbg_id,
    monitorbpjs_m.groupinacbg_nama,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jml_tarif
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
            monitorbpjs_m_1.kelompoktindakan_nama,
            monitorbpjsdetail_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama
           FROM monitorbpjs_m monitorbpjs_m_1
             JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
             LEFT JOIN groupinacbg_m ON monitorbpjsdetail_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON daftartindakan_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id
UNION ALL
 SELECT 'paket'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    monitorbpjs_m.monitorbpjs_id,
    monitorbpjs_m.kelompoktindakan_nama AS kelompok,
    monitorbpjs_m.groupinacbg_id,
    monitorbpjs_m.groupinacbg_nama,
    tindakanpelayanan_t.tgl_tindakan,
    (tipepaket_m.tipepaket_nama::text || '-'::text) || daftartindakan_m.daftartindakan_nama::text AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan AS jml_tarif
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
            monitorbpjs_m_1.kelompoktindakan_nama,
            monitorbpjsdetail_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama
           FROM monitorbpjs_m monitorbpjs_m_1
             JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
             LEFT JOIN groupinacbg_m ON monitorbpjsdetail_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON daftartindakan_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    monitorbpjs_m.monitorbpjs_id,
    monitorbpjs_m.kelompoktindakan_nama AS kelompok,
    monitorbpjs_m.groupinacbg_id,
    monitorbpjs_m.groupinacbg_nama,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_nama AS daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS jml_tarif
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT monitorbpjs_m_1.monitorbpjs_id,
            monitorbpjs_m_1.kelompoktindakan_nama,
            monitorbpjsdetail_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama
           FROM monitorbpjs_m monitorbpjs_m_1
             JOIN monitorbpjsdetail_m ON monitorbpjs_m_1.monitorbpjs_id = monitorbpjsdetail_m.monitorbpjs_id AND monitorbpjsdetail_m.is_active = true AND monitorbpjsdetail_m.is_deleted = false
             LEFT JOIN groupinacbg_m ON monitorbpjsdetail_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE monitorbpjs_m_1.is_active = true AND monitorbpjs_m_1.is_deleted = false) monitorbpjs_m ON obatalkes_m.groupinacbg_id = monitorbpjs_m.groupinacbg_id;

        ");

           $this->execute('
        ALTER TABLE public.infomonitoringbpjsdetail_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190805_035601_infomonitoringbpjsdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_035601_infomonitoringbpjsdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
