<?php

use yii\db\Migration;

/**
 * Class m221204_135046_init
 */
class m221204_135046_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency}}', [
            'id'         => $this->primaryKey()->unsigned(),
            'code'       => $this->char(3)->notNull(),
            'rate'       => $this->money()->unsigned(),
            'date'       => $this->date()->notNull(),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->createIndex('currency-date-idx', '{{%currency}}', ['code', 'date'], true);
        (new \api\modules\v1\models\CurrencyRate(['code' => 'RUB', 'date' => date('Y-m-d'), 'rate' => 1]))->save();

        $this->createTable('{{%wallet}}', [
            'id'       => $this->primaryKey()->unsigned(),
            'balance'  => $this->money(17, 2)->unsigned()->notNull(),
            'currency' => $this->char(3)->notNull(),
        ]);

        $this->createTable('{{%transaction}}', [
            'id'         => $this->primaryKey()->unsigned(),
            'wallet_id'  => $this->integer()->unsigned()->notNull(),
            'type'       => $this->tinyInteger()->unsigned(),
            'amount'     => $this->money(17, 2)->unsigned()->notNull(),
            'currency'   => $this->char(3)->notNull(),
            'amount_rub' => $this->money(17, 2)->unsigned()->notNull(),
            'reason'     => $this->tinyInteger()->unsigned(),
            'created_at' => $this->integer()->unsigned()->notNull(),
        ]);
        $this->addForeignKey('wallet-transaction-fk1', '{{%transaction}}', 'wallet_id', '{{%wallet}}', 'id', 'CASCADE',
            'CASCADE');
        $this->createIndex('transaction-reason-idx', '{{%transaction}}', ['created_at', 'reason']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('wallet-transaction-fk1', '{{%transaction}}');
        $this->dropTable('{{%transaction}}');
        $this->dropTable('{{%wallet}}');
        $this->dropIndex('currency-date-idx', '{{%currency}}');
        $this->dropTable('{{%currency}}');
    }
}
