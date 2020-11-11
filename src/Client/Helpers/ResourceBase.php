<?php


namespace Tarre\Swish\Client\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Stringable;
use Tarre\Swish\Client\Contracts\Transformable;
use Tarre\Swish\Exceptions\InvalidRequestParamException;
use Tarre\Swish\Exceptions\ValidationFailedException;

abstract class ResourceBase implements Arrayable, Jsonable, Stringable, Transformable
{
    public function __construct(array $options = [])
    {
        /*
         * Get all transforms
         */
        $transforms = $this->transforms();


        foreach ($options as $key => $value) {

            /*
             * Transform values
             */
            if (isset($transforms[$key])) {
                $value = $transforms[$key]($value);
            }

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

    /**
     * Validate the resource
     * @throws ValidationFailedException
     */
    public function validate()
    {
        $requiredFields = $this->requiredFields();

        foreach ($requiredFields as $key) {
            $value = $this->{$key};
            if (empty($value)) {
                throw new ValidationFailedException(sprintf('The key "%s" is required for this resource', $key));
            }

        }
    }

    public function requiredFields(): array
    {
        return [];
    }

    public function transforms(): array
    {
        return [];
    }
}