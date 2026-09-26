<?php

use yii\db\Migration;

/**
 * Class m220509_074014_migrate_historirencanaoperasi_v
 */
class m220509_074014_migrate_historirencanaoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."historirencanaoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"historirencanaoperasi_v\" AS  SELECT historirencanaoperasi_r.rencanaoperasi_id,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    historirencanaoperasi_r.tgl_permintaan,
    historirencanaoperasi_r.jam_rencana_mulai,
    historirencanaoperasi_r.jam_rencana_selesai,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    status.lookup_name AS status_operasi,
    historirencanaoperasi_r.keterangan,
    historirencanaoperasi_r.tgl_perubahan,
    historirencanaoperasi_r.created_by,
    loginpemakai.nama_pegawai
   FROM historirencanaoperasi_r
     JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON historirencanaoperasi_r.status_operasi = status.lookup_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON historirencanaoperasi_r.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON historirencanaoperasi_r.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) loginpemakai ON historirencanaoperasi_r.created_by = loginpemakai.loginpemakai_id
     LEFT JOIN ( SELECT DISTINCT a.rencanaoperasi_id,
            a.pasienkirimkeunitlain_id
           FROM rencanaoperasi_t a) rencanaoperasi_t ON historirencanaoperasi_r.rencanaoperasi_id = rencanaoperasi_t.rencanaoperasi_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220509_074014_migrate_historirencanaoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220509_074014_migrate_historirencanaoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
