<?php

use yii\db\Migration;

/**
 * Class m190805_040723_sie_tagihanpengajuan
 */
class m190805_040723_sie_tagihanpengajuan extends Migration
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
    to_char(pengajuanklaim_t.tgl_pengajuanklaim, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    to_char(pengajuanklaim_t.tgl_pengajuanklaim, 'YYYY-MM-DD'::text)::date AS tgl_pengajuan,
    NULL::date AS tgl_alokasi,
    NULL::date AS tgl_terima,
    pengajuanklaim_t.total_piutang AS tagihan,
    pengajuanklaim_t.total_sisapiutang AS sisa_tagihan
   FROM pengajuanklaim_t
  WHERE pengajuanklaim_t.is_deleted = false
UNION ALL
 SELECT 'penerimaan'::text AS jenis,
    to_char(terimabayarklaim_t.tgl_terimabayarklaim, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    NULL::date AS tgl_pengajuan,
    to_char(terimabayarklaim_t.tgl_terimabayarklaim, 'YYYY-MM-DD'::text)::date AS tgl_alokasi,
    NULL::date AS tgl_terima,
    terimabayarklaimdetail_t.pembayaran AS tagihan,
    terimabayarklaimdetail_t.total_sisapiutang AS sisa_tagihan
   FROM terimabayarklaim_t
     JOIN terimabayarklaimdetail_t ON terimabayarklaim_t.terimabayarklaim_id = terimabayarklaimdetail_t.terimabayarklaim_id AND terimabayarklaimdetail_t.is_deleted = false
  WHERE terimabayarklaim_t.is_deleted = false
UNION ALL
 SELECT 'alokasi'::text AS jenis,
    to_char(pembayaranalokasi_t.tgl_pembayaranalokasi, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    NULL::date AS tgl_pengajuan,
    NULL::date AS tgl_alokasi,
    to_char(pembayaranalokasi_t.tgl_pembayaranalokasi, 'YYYY-MM-DD'::text)::date AS tgl_terima,
    pembayaranalokasidetail_t.jumlah_bayar AS tagihan,
    pembayaranalokasidetail_t.jumlah_sisapiutang AS sisa_tagihan
   FROM pembayaranalokasi_t
     JOIN pembayaranalokasidetail_t ON pembayaranalokasi_t.pembayaranalokasi_id = pembayaranalokasidetail_t.pembayaranalokasi_id AND pembayaranalokasidetail_t.is_deleted = false
  WHERE pembayaranalokasi_t.is_deleted = false;
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
        echo "m190805_040723_sie_tagihanpengajuan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_040723_sie_tagihanpengajuan cannot be reverted.\n";

        return false;
    }
    */
}
