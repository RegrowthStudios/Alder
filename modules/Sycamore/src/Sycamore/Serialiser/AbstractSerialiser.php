<?php
    namespace Sycamore\Serialiser;
    
    use Zend\Serializer\Adapter\AdapterOptions;
    use Zend\Serializer\Serializer;
    
    // TODO(Matthew): Rework this wrapper.
    /**
     * Facilitates serialisation of data via various serialisation methods.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractSerialiser
    {
        /**
         * Constructs the instance's serialiser if not already constructed and returns it.
         * 
         * @return \Zend\Serializer\Adapter\AbstractAdapter The serialiser adapter.
         */
        protected static function getSerialiser()
        {
            if (!isset(static::$serialiser)) {
                static::$serialiser = Serializer::factory(static::$serialiserType);
                if (!empty(static::$options)) {
                    static::$serialiser->setOptions(new AdapterOptions(static::$options));
                }
            }
            return static::$serialiser;
        }
        
        /**
         * Encodes the given data appropriately, returning a string of the encoded form.
         * 
         * @param mixed $data The data to be serialised.
         * 
         * @return string The serialisation result.
         */
        public static function encode($data)
        {
            return static::getSerialiser()->serialize($data);
        }
        
        /**
         * Decodes the given data appropriately, returning the decode results. Objects are decoded as associative arrays.
         * 
         * @param string $data The serialised data to be unserialised.
         * 
         * @return mixed The unserialisation result.
         */
        public static function decode($data)
        {
            return static::getSerialiser()->unserialize($data);
        }
    }
