<?php

namespace app\modules\jobs;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

class TestQueue extends BaseObject implements RetryableJobInterface
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function execute($queue)
    {
        error_log("My name is:" . $this->name);
        // TODO: Implement execute() method.
    }

    public function getTtr()
    {
        return 60;
        // TODO: Implement getTtr() method.
    }

    public function canRetry($attempt, $error)
    {
        return $attempt < 3;
        // TODO: Implement canRetry() method.
    }
}