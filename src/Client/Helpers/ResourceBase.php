<?php


namespace Tarre\Swish\Client\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Stringable;
use Tarre\Swish\Exceptions\InvalidRequestParamException;

abstract class ResourceBase implements Arrayable, Jsonable, Stringable
{
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

    }

    public function __set($key, $value)
    {
        $this->throwIfKeyDoesNotExist($key);
        $this->{$key} = $value;
    }

    public function __get($key)
    {
        return $this->{$key};
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }

    protected function throwIfKeyDoesNotExist($key)
    {
        if (!property_exists($this, $key)) {
            throw new InvalidRequestParamException(sprintf("%s does not have the property %s", get_class($this), $key));
        }
    }
}