<?php

namespace mrcrmn\Http2Push;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class Http2Push
{
    /**
    * The collection of resources to apply to the link header.
    *
    * @var \Illuminate\Support\Collection
    */
    protected $resources;

    /**
     * Constructs the object and adds the first resources.
     *
     * @param array $preload
     */
    public function __construct($preload = [])
    {
        $this->resources = new Collection();

        foreach ($preload as $resource) {
            $this->add($resource);
        }
    }

    /**
        * Adds a resource to the collection.
        *
        * @param string $resource The path of the resource
        * @param boolean $silent Whether or not to return the resource
        * @return void|string
        */
    public function add($resource, $silent = false)
    {
        // Pushes the resource to the collection and removes the base url from it.
        $this->resources->push(
            str_replace(config('app.url'), '', $resource)
        );

        // Return the resource if we didn't chose the silent mode.
        if (! $silent) {
            return $resource;
        }
    }

    /**
     * Checks if the collection holds any resources.
     * 
     * @return bool
     */
    public function any()
    {
        return $this->resources->isNotEmpty();
    }

    /**
     * Guesses the type of the resource.
     *
     * @param string $resource
     * @return string
     */
    protected function guessType($resource)
    {
        if (Str::contains($resource, '.css')) {
            return 'style';
        }

        if (Str::contains($resource, '.js')) {
            return 'script';
        }

        return 'image';
    }

    /**
     * Generates the value for the link header which is applied to the response.
     * 
     * @return string
     */
    public function generateHeader()
    {
        $segments = $this->resources->unique()->map(function($resource) {
            return sprintf('<%s>; rel=preload; as=%s', $resource, $this->guessType($resource));
        });

        return implode(', ', $segments->all());
    }
}
