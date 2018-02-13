<?php
/**
 * Created by A.
 * User: ahmetgunes
 * Date: 13.02.2018
 * Time: 16:17
 */

namespace RabbitMQEvent;


use ScheduledEvent\Model\Message\AbstractMessage;
use ScheduledEvent\Model\Message\MessageSerializerInterface;
use ScheduledEvent\Traits\ConvertibleTrait;

class RabbitMQMessageSerializer implements MessageSerializerInterface
{
    use ConvertibleTrait;

    public static function convert($message)
    {
        if ($message instanceof AbstractMessage) {
            $body = $message->toJsonObject();
            $amqpMessage = new AMQPMessage($body);
            if (!is_null($message->getPriority())) {
                $amqpMessage->set('priority', $message->getPriority());
            }

            return $amqpMessage;
        } else {
            throw new ScheduledEventException('Message must be an instance of AMQPMessage');
        }
    }

    public static function deConvert($message)
    {
        if ($message instanceof AMQPMessage) {
            return self::toObject($message->getBody());
        } else {
            throw new ScheduledEventException('Message must be an instance of AMQPMessage');
        }
    }

    protected static function toMessage(string $body)
    {
        $object = json_decode($body, true);
        $message = new RabbitMQMessage();
        foreach ($object as $key => $value) {
            $message->{$key} = $value;
        }
    }
}