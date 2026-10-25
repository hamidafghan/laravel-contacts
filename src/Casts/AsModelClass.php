<?php

namespace Jenishev\Laravel\Contacts\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Cast an attribute as a model class that implements a required contract.
 */
class AsModelClass implements Castable
{
    /**
     * {@inheritDoc}
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments) implements CastsAttributes
        {
            /**
             * @param  array<int, string>  $arguments
             */
            public function __construct(protected array $arguments) {}

            /**
             * {@inheritDoc}
             */
            public function get(Model $model, string $key, mixed $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                $this->validate($value);

                return $value;
            }

            /**
             * {@inheritDoc}
             */
            public function set(Model $model, string $key, mixed $value, array $attributes): ?string
            {
                if ($value === null) {
                    return null;
                }

                if (! is_string($value)) {
                    throw new InvalidArgumentException(sprintf('The value must be a string, %s given.', get_debug_type($value)));
                }

                $this->validate($value);

                return $value;
            }

            protected function validate(mixed $value): void
            {
                $class = $this->arguments[0] ?? null;

                if (! is_string($class) || (! class_exists($class) && ! interface_exists($class))) {
                    throw new InvalidArgumentException(sprintf('Class or interface [%s] does not exist.', $class));
                }

                if (! is_string($value) || ! is_a($value, $class, true)) {
                    throw new InvalidArgumentException(sprintf('Class [%s] must implement [%s] or extend it.', $value, $class));
                }
            }
        };
    }

    /**
     * Create the cast declaration for the required class or interface.
     */
    public static function of(string $class): string
    {
        return static::class . ':' . $class;
    }
}
