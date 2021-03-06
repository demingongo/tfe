<?php
namespace Novice\Annotation;

/**
 * Annotation class for @Service().
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 */
class Service
{
	/** @var string */
    private $value;
	
	public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

	public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
         return $this->value;
    }
}
