<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class CompanyTrade extends ActiveRecord
{
    public static function tableName()
    {
        return 'common_company_trade';
    }

    public function rules()
    {
        return [
            [['id', 'group_id'], 'integer'],
            [['time'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'group_id' => 'Group ID',
            'author' => 'Author',
            'time' => 'Time',
        ];
    }

    public function getTrade_name() {
        if (isset($this->type) && $this->type != "") {

            $trades = TradeSubType::find()->all();
            $trade_array = [];
            $trade_array[0] = "通用资料";
            foreach ($trades as $trade) {
                $trade_array[$trade->id] = $trade->name;
            }

            $_trade = [];
            $tradeId = explode("+", $this->type);
            foreach ($tradeId as $id) {
                $_trade[] = $trade_array[$id];
            }
            return implode("/", $_trade);
        }
        else return null;
    }
}
