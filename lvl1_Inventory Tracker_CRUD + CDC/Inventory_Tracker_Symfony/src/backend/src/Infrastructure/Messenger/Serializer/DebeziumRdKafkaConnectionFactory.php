<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Messenger\Serializer;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaContext;
use Interop\Queue\Context;

final class DebeziumRdKafkaConnectionFactory extends RdKafkaConnectionFactory
{
    public function createContext(): Context
    {
        $context = parent::createContext();
        if ($context instanceof RdKafkaContext) {
            $context->setSerializer(new RawDebeziumKafkaSerializer());
        }

        return $context;
    }
}
