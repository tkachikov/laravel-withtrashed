<?php
declare(strict_types=1);

namespace Tkachikov\LaravelWithtrashed;

trait WithTrashedTrait
{
    public function __get($key)
    {
        return $this->prepareCallWithTrashed('__get', $key);
    }

    public function __call($method, $args)
    {
        return $this->prepareCallWithTrashed('__call', $method, $args);
    }

    /**
     * @param string $magic
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function prepareCallWithTrashed(string $magic, string $method, array $args = []): mixed
    {
        return $this->isWithTrashed($method)
            ? (function ($builder) use ($magic) {
                return $magic === '__get'
                    ? $builder->get()
                    : $builder;
            })($this->callWithTrashed($method, $args))
            : parent::$magic($method, $args);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isWithTrashed(string $method): bool
    {
        $relation = $this->getMethodWithoutWithTrashed($method);

        return method_exists($this, $relation)
            && str($method)->after($relation)->is('WithTrashed')
            && !method_exists($this, $method);
    }

    /**
     * @param string $method
     *
     * @return string
     */
    public function getMethodWithoutWithTrashed(string $method): string
    {
        return str($method)
            ->before('WithTrashed')
            ->toString();
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function callWithTrashed(string $method, array $args = []): mixed
    {
        $relationMethod = $this->getMethodWithoutWithTrashed($method);

        return $this->$relationMethod(...$args)->withTrashed();
    }
}
