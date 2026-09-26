<?php

use yii\db\Migration;

/**
 * Class m210616_111851_migrate_3691_infoformulirinfostokopname_v
 */
class m210616_111851_migrate_3691_infoformulirinfostokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoformulirinfostokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoformulirinfostokopname_v\" AS  SELECT formulirstokopname_t.formulirstokopname_id,
    stokopname_t.stokopname_id,
    formulirstokopname_t.created_date AS tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.created_date AS tglstokopname,
    stokopname_t.nostokopname,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    pegawaiverifikasi.pegawai_id AS pegawaiverifikasi_id,
    pegawaiverifikasi.nama_pegawai AS pegawaiverifikasi_nama,
    stokopname_t.is_verifikasi
   FROM formulirstokopname_t
     LEFT JOIN stokopname_t ON formulirstokopname_t.formulirstokopname_id = stokopname_t.formulirstokopname_id
     LEFT JOIN pegawai_m pegawaiverifikasi ON stokopname_t.pegawaiverifikasi_id = pegawaiverifikasi.pegawai_id
     JOIN ruangan_m ON formulirstokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE formulirstokopname_t.is_active = true AND formulirstokopname_t.is_deleted = false;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_111851_migrate_3691_infoformulirinfostokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_111851_migrate_3691_infoformulirinfostokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
