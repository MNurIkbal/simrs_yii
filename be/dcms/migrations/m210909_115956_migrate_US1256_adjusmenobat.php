<?php

use yii\db\Migration;

/**
 * Class m210909_115956_migrate_US1256_adjusmenobat
 */
class m210909_115956_migrate_US1256_adjusmenobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."infoadjusmenobat_v";');

    $this->execute("
        CREATE VIEW \"public\".\"infoadjusmenobat_v\" AS  SELECT adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobat_t.tgl_adjusmen,
    adjusmenobat_t.jenis_adjusmen,
        CASE
            WHEN adjusmenobat_t.jenis_adjusmen = 0 THEN 'adjusmen_masuk'::text
            ELSE 'adjusmen_keluar'::text
        END AS jenis_adjusmen_nama,
    adjusmenobat_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS pegawai_mengetahui,
    adjusmenobat_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS pegawai_menyetujui,
    adjusmenobat_t.ruangan_adjusmen_id,
    ruangan_m.ruangan_nama,
    peg_adjusmen.nama_pegawai AS pegawai_adjusmen
   FROM adjusmenobat_t
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_mengetahui ON adjusmenobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_menyetujui ON adjusmenobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON adjusmenobat_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_adjusmen ON loginpemakai_k.pegawai_id = peg_adjusmen.pegawai_id
  WHERE adjusmenobat_t.is_deleted = false;");
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210909_115956_migrate_US1256_adjusmenobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210909_115956_migrate_US1256_adjusmenobat cannot be reverted.\n";

        return false;
    }
    */
}
