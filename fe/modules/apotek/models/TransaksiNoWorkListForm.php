<?php


namespace Doco\apotek\models;


class TransaksiNoWorkListForm extends \yii\base\Model
{
    public $no_transaksi;
    public $search_no_resep;


    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['no_transaksi'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'search_no_transaksi' => 'No. Resep / No. Reseptur / No. UDD',
        ];
    }
}


