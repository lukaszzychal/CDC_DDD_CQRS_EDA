<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Messenger\Serializer;

use App\Backend\Application\Event\ProductCdcUpdatedEvent;
use App\Backend\Domain\Enum\CdcOperation;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class DebeziumSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = json_decode($encodedEnvelope['body'] ?? '{}', true);

        $op = isset($body['op']) ? CdcOperation::tryFrom((string) $body['op']) : null;

        $event = new ProductCdcUpdatedEvent(
            op: $op ?? CdcOperation::READ,
            before: $body['before'] ?? null,
            after: $body['after'] ?? null,
            timestamp: (int) ($body['ts_ms'] ?? time() * 1000),
        );

        return new Envelope($event);
    }

    public function encode(Envelope $envelope): array
    {
        return [
            'body' => json_encode([]),
            'headers' => ['Content-Type' => 'application/json'],
        ];
    }
}
