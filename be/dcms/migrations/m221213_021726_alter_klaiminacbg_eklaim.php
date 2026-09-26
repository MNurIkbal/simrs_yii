<?php

use yii\db\Migration;

/**
 * Class m230304_021726_alter_klaiminacbg_eklaim
 */
class m221213_021726_alter_klaiminacbg_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS nama_pasien varchar(255) NULL;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS obat_kronis float8 NULL;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS obat_kemoterapi varchar(52) NULL;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_turunkelas bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS klaim_penjamin int2 NULL;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS status_covid varchar(100) NULL;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_komplikasi bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_pemulasaranjenazah bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_kantongjenazah bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_petijenazah bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_plastikerat bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_desinfektanjenazah bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_transport bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS is_desinfektanmobil bool NOT NULL DEFAULT false;
        ');
        $this->execute('
            ALTER TABLE klaiminacbg_t ADD IF NOT EXISTS total_episodedijamin int2 NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_021726_alter_klaiminacbg_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_021726_alter_klaiminacbg_eklaim cannot be reverted.\n";

        return false;
    }
    */
}
