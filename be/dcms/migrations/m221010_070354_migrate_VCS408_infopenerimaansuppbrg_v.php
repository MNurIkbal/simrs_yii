<?php

use yii\db\Migration;

/**
 * Class m221010_070354_migrate_VCS408_infopenerimaansuppbrg_v
 */
class m221010_070354_migrate_VCS408_infopenerimaansuppbrg_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopenerimaansuppbrg_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopenerimaansuppbrg_v
        AS SELECT penerimaansupp_t.penerimaansupp_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.tgl_penerimaan,
            penerimaansupp_t.no_faktur,
            penerimaansupp_t.supplier_id,
            supplier_m.supplier_nama,
            penerimaansupp_t.peg_menyetujui,
            peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
            penerimaansupp_t.peg_mengetahui,
            peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
            ruangan_m.ruangan_nama,
            concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
            payterm_m.payterm_nama,
            penerimaansupp_t.no_suratjalan,
            penerimaansupp_t.is_verifikasi,
                CASE
                    WHEN penerimaansupp_t.is_verifikasi = true THEN 'Sudah Verifikasi'::text
                    ELSE 'Belum Verifikasi'::text
                END AS status_verifikasi,
            penerimaansupp_t.tgl_verifikasi,
            supplier_m.supplier_alamat,
            supplier_m.no_tlp,
            supplier_m.no_fax,
            peg_created.nama_pegawai AS peg_created,
            penerimaansupp_t.is_consigment,
            penerimaansupp_t.is_donasi
           FROM penerimaansupp_t
             JOIN ( SELECT a.supplier_id,
                    a.supplier_nama,
                    a.supplier_alamat,
                    a.no_tlp,
                    a.no_fax
                   FROM supplier_m a) supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON penerimaansupp_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) peg_created ON loginpemakai_k.pegawai_id = peg_created.pegawai_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pajak_id,
                    a.pajak_name,
                    a.pajak_persen
                   FROM pajak_m a) pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
             LEFT JOIN ( SELECT a.payterm_id,
                    a.payterm_nama
                   FROM payterm_m a) payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id
          WHERE penerimaansupp_t.is_tipe = 1 AND penerimaansupp_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221010_070354_migrate_VCS408_infopenerimaansuppbrg_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221010_070354_migrate_VCS408_infopenerimaansuppbrg_v cannot be reverted.\n";

        return false;
    }
    */
}
