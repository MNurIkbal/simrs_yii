<?php

use yii\db\Migration;

/**
 * Class m220620_113531_migrate_MHG2156_data_bodymassindexanak_m
 */
class m220620_113531_migrate_MHG2156_data_bodymassindexanak_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE bodymassindexanak_m RESTART IDENTITY;
        ');

        $this->execute("
            INSERT INTO bodymassindexanak_m (jenis_kelamin, umur_awal, umur_akhir, bmi_minimum, bmi_maksimum, bmi_defenisi, bmi_pesan) VALUES 
                (15, '0', '23', '0', '13.4', 'gizi buruk', 'gizi buruk'),
                (15, '0', '23', '13.5', '14.6', 'gizi kurang', 'gizi kurang'),
                (15, '0', '23', '14.7', '18.7', 'normal', 'normal'),
                (15, '0', '23', '18.8', '22.3', 'overweight', 'overweight'),
                (15, '0', '23', '22.4', '100', 'obesitas', 'obesitas'),
                (16, '0', '23', '0', '12.2', 'gizi buruk', 'gizi buruk'),
                (16, '0', '23', '12.3', '13.2', 'gizi kurang', 'gizi kurang'),
                (16, '0', '23', '13.3', '17', 'normal', 'normal'),
                (16, '0', '23', '17.1', '20.5', 'overweight', 'overweight'),
                (16, '0', '23', '20.6', '100', 'obesitas', 'obesitas'),
                (15, '24', '60', '0', '12.6', 'gizi buruk', 'gizi buruk'),
                (15, '24', '60', '12.7', '13.5', 'gizi kurang', 'gizi kurang'),
                (15, '24', '60', '13.6', '17.1', 'normal', 'normal'),
                (15, '24', '60', '17.2', '20.3', 'overweight', 'overweight'),
                (15, '24', '60', '20.4', '100', 'obesitas', 'obesitas'),
                (16, '24', '60', '0', '11.7', 'gizi buruk', 'gizi buruk'),
                (16, '24', '60', '11.8', '12.6', 'gizi kurang', 'gizi kurang'),
                (16, '24', '60', '12.7', '16.9', 'normal', 'normal'),
                (16, '24', '60', '17', '18.9', 'overweight', 'overweight'),
                (16, '24', '60', '19', '100', 'obesitas', 'obesitas'),
                (15, '61', '216', '0', '12', 'gizi buruk', 'gizi buruk'),
                (15, '61', '216', '12.1', '12.9', 'gizi kurang', 'gizi kurang'),
                (15, '61', '216', '13', '14', 'normal', 'normal'),
                (15, '61', '216', '14.1', '16.6', 'overweight', 'overweight'),
                (15, '61', '216', '16.7', '100', 'obesitas', 'obesitas'),
                (16, '61', '216', '0', '11.7', 'gizi buruk', 'gizi buruk'),
                (16, '61', '216', '11.8', '12.6', 'gizi kurang', 'gizi kurang'),
                (16, '61', '216', '12.7', '16.9', 'normal', 'normal'),
                (16, '61', '216', '17', '18.9', 'overweight', 'overweight'),
                (16, '61', '216', '19', '100', 'obesitas', 'obesitas');

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220620_113531_migrate_MHG2156_data_bodymassindexanak_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_113531_migrate_MHG2156_data_bodymassindexanak_m cannot be reverted.\n";

        return false;
    }
    */
}
