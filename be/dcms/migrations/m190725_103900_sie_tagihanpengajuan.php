<?php

use yii\db\Migration;

/**
 * Class m190725_103900_sie_tagihanpengajuan
 */
class m190725_103900_sie_tagihanpengajuan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP VIEW if exists public.sie_tagihanpengajuan;
        ');


        $this->execute("
         CREATE OR REPLACE VIEW public.sie_tagihanpengajuan AS 
 SELECT 'pengajuan'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    to_char(pengajuanklaim_t.tgl_pengajuanklaim, 'YYYY-MM-DD'::text)::date AS tgl_pengajuan,
    NULL::date AS tgl_alokasi,
    NULL::date AS tgl_terima,
    pengajuanklaimdetail_t.jumlah_piutang AS tagihan,
    pengajuanklaimdetail_t.jumlah_sisapiutang AS sisa_tagihan
   FROM pendaftaran_t
     JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
     JOIN pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id AND pengajuanklaim_t.is_deleted = false
UNION ALL
 SELECT 'penerimaan'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    NULL::date AS tgl_pengajuan,
    to_char(terimabayarklaim_t.tgl_terimabayarklaim, 'YYYY-MM-DD'::text)::date AS tgl_alokasi,
    NULL::date AS tgl_terima,
    terimabayarklaim_t.total_terimabayar AS tagihan,
    terimabayarklaimdetail_t.total_sisapiutang AS sisa_tagihan
   FROM pendaftaran_t
     JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
     JOIN pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id
     JOIN terimabayarklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = terimabayarklaimdetail_t.pengajuanklaim_id AND terimabayarklaimdetail_t.is_deleted = false
     JOIN terimabayarklaim_t ON terimabayarklaimdetail_t.terimabayarklaim_id = terimabayarklaim_t.terimabayarklaim_id AND terimabayarklaim_t.is_deleted = false
UNION ALL
 SELECT 'alokasi'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    NULL::date AS tgl_pengajuan,
    NULL::date AS tgl_alokasi,
    to_char(pembayaranalokasi_t.tgl_pembayaranalokasi, 'YYYY-MM-DD'::text)::date AS tgl_terima,
    pembayaranalokasi_t.total_terbayar AS tagihan,
    pembayaranalokasi_t.sisa_piutang AS sisa_tagihan
   FROM pendaftaran_t
     JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
     JOIN pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id
     JOIN pembayaranalokasi_t ON pengajuanklaim_t.pengajuanklaim_id = pembayaranalokasi_t.pengajuanklaim_id;
        ");


        $this->execute('
         ALTER TABLE public.sie_tagihanpengajuan
  OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190725_103900_sie_tagihanpengajuan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190725_103900_sie_tagihanpengajuan cannot be reverted.\n";

        return false;
    }
    */
}
