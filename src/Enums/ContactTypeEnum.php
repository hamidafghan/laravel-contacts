<?php

namespace Jenishev\Laravel\Contacts\Enums;

use Illuminate\Support\Str;
use Jenishev\Laravel\Contacts\Casts\Strategies\EmailStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\NullStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\PhoneStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\Strategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\TelegramStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\UrlStrategy;
use Jenishev\Laravel\Contacts\Casts\Strategies\WhatsAppStrategy;

/**
 * Defines available contact method types for the system.
 */
enum ContactTypeEnum: string
{
    case Email = 'email';
    case Other = 'other';
    case Phone = 'phone';
    case Telegram = 'telegram';
    case Url = 'url';
    case Whatsapp = 'whatsapp';

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::Url => 'URL',
            self::Whatsapp => 'WhatsApp',
            default => __(Str::headline(Str::lower($this->value))),
        };
    }

    /**
     * Get all enum values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum values mapped to their labels.
     *
     * @return array<string, string>
     */
    public static function choices(): array
    {
        $choices = [];

        foreach (self::cases() as $case) {
            $choices[$case->value] = $case->label();
        }

        return $choices;
    }

    /**
     * Get the strategy class for processing this contact type.
     *
     * @return class-string<Strategy>
     */
    public function getStrategyClass(): string
    {
        return match ($this) {
            self::Email => EmailStrategy::class,
            self::Other => NullStrategy::class,
            self::Phone => PhoneStrategy::class,
            self::Telegram => TelegramStrategy::class,
            self::Url => UrlStrategy::class,
            self::Whatsapp => WhatsAppStrategy::class,
            // default will throw an exception
        };
    }
}
