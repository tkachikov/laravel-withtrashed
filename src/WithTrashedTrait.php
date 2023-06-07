<?php
declare(strict_types=1);

namespace Tkachikov\LaravelWithtrashed;

use Illuminate\Database\Eloquent\SoftDeletes;

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
            ? $this->callMagic($method, $args, $magic)
            : parent::$magic($method, $args);
    }

    /**
     * @param string $method
     * @param array  $args
     * @param string $magic
     *
     * @return mixed
     */
    public function callMagic(string $method, array $args, string $magic): mixed
    {
        $builder = $this->callWithTrashed($method, $args);
        $originalMethod = $this->getMethodWithoutWithTrashed($method);
        $getMethod = str($originalMethod)->singular()->is($originalMethod)
            ? 'first'
            : 'get';

        return $magic === '__get'
            ? $builder->$getMethod()
            : $builder;
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

        return isset(class_uses($this->$relationMethod()->getRelated())[SoftDeletes::class])
            ? $this->$relationMethod(...$args)->withTrashed()
            : $this->$relationMethod(...$args);
    }
}
