<?php
    namespace Sycamore\Serialiser;

    use Sycamore\Serialiser\AbstractSerialiser;
    
    /**
     * Simple wrapper for serialising and unserialising object data.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Object extends AbstractSerialiser
    {
        /**
         * Serialiser adapter for serialising data.
         * 
         * @var \Zend\Serializer\Adapter\AbstractAdapter
         */
        protected static $serialiser;
        
        /**
         * Options for a given instance's serialiser adapter.
         *
         * @var array
         */
        protected static $options = [];
        
        /**
         * {@inheritdoc}
         */
        protected static $serialiserType = "PhpSerialize";
    }
    