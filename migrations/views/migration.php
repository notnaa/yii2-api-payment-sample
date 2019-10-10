<?php

/** @var string $className */

echo "<?php\n";
?>

use console\components\db\Migration;

/**
* Class <?= $className . "\n"; ?>
*/
class <?= $className; ?> extends Migration
{
    /**
    * @inheritdoc
    */
    public function safeUp()
    {

    }

    /**
    * @inheritdoc
    */
    public function safeDown()
    {

    }
}
