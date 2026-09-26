<?php

use yii\db\Migration;

/**
 * Class m230815_122518_migrate_skema_resepkronis_add_colum_table
 */
class m230815_122518_migrate_skema_resepkronis_add_colum_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS enable_split_kronis bool DEFAULT true;');
		$this->execute('ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS hari_resep_kronis int4;');
		
		$this->execute('ALTER TABLE penjualanresep_t ADD IF NOT EXISTS resep_kronis_asal_id int4;');
		$this->execute('ALTER TABLE penjualanresep_t ADD IF NOT EXISTS hasil_resep_kronis_id int4;');
		$this->execute('ALTER TABLE penjualanresep_t ADD IF NOT EXISTS reseptur_kronis_asal_id int4;');
		$this->execute('ALTER TABLE penjualanresep_t ADD IF NOT EXISTS nosep varchar(100);');
		
		$this->execute('ALTER TABLE reseptur_t ADD IF NOT EXISTS hasil_resep_kronis_id int4;');
		
		$this->execute('ALTER TABLE obatalkespasien_t ADD IF NOT EXISTS is_kronis bool DEFAULT false;');
		
		$this->execute('ALTER TABLE resepturdetail_t ADD IF NOT EXISTS is_kronis bool DEFAULT false;');
		
		$this->execute('ALTER TABLE reseptempdetail_m ADD IF NOT EXISTS is_kronis bool DEFAULT false;;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_122518_migrate_skema_resepkronis_add_colum_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_122518_migrate_skema_resepkronis_add_colum_table cannot be reverted.\n";

        return false;
    }
    */
}
