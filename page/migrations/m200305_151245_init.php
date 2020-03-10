<?php

use yii\db\Migration;

/**
 * Class m200305_151245_init
 */
class m200305_151245_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'image',
            [
                'id'          => $this->primaryKey(),
                'author'      => $this->string(50),
                'category_id' => $this->integer(),
                'watermark'   => $this->integer(),
                'title'       => $this->string(50),
                'date'        => $this->date(),
                'status'      => $this->string(50),
                'extension'   => $this->string(50),
                'image'       => $this->string(50),
                'link'        => $this->string(50),
            ]
        );

        $this->createTable(
            'category',
            [
                'id'     => $this->primaryKey(),
                'title'  => $this->string(50),
                'slug'   => $this->string(50)->unique(),
                'status' => $this->string(50),
                'count'  => $this->integer(),
                'link'   => $this->string(50),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('image');
        $this->dropTable('category');
    }
}
