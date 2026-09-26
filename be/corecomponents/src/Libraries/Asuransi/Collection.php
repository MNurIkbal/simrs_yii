<?php

namespace Doco\Libraries\Asuransi;

class Collection
{

    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable $callback
     *
     * @return static
     */
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);

        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    public function transform(callable $callback)
    {
        $this->items = $this->map($callback)->all();

        return $this;
    }


    public function all()
    {
        return $this->items;
    }


    public function __toString()
    {
        return $this->toJson();
    }

    protected function getArrayableItems($items)
    {
        return (array) $items;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return isset($this->items[$property]) ? $this->items[$property] : null;
        }
    }
}