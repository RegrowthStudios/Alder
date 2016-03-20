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
         * {@inheritdoc}
         */
        protected static $serialiserType = "PhpSerialize";
    }
    