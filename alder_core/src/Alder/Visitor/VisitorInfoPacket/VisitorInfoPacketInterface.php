<?php
    
    namespace Alder\Visitor\VisitorInfoPacket;
    
    /**
     * Provides an interface for visitor information wrappers.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    interface VisitorInfoPacketInterface extends \ArrayAccess
    {
        /**
         * Initialises the info packet, populating it with the data provided.
         *
         * @param array $metadata The metadata of the source of this info packet.
         * @param array $data The data to initialise the cookie with.
         *
         * @return \Alder\Visitor\VisitorInfoPacket\VisitorInfoPacketInterface|null Returns self if initialised, null if
         *                                                                  already initialised.
         */
        public function initialise(array $metadata = [], array $data = []) : ?VisitorInfoPacketInterface;
        
        /**
         * Determines in the info packet has been modified in any way.
         *
         * @return bool True if the info packet's data has been changed, once otherwise.
         */
        public function hasChanged() : bool;
        
        public function save() : bool;
    }
