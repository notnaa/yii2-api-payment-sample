<?php

/** @var string $className */

echo "<?php\n";
?>

use app\components\db\Migration;

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
