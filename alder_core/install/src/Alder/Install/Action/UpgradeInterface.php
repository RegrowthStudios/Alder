<?php
    
    namespace Alder\Install\Action;
    
    /**
     * Interface for all classes providing upgrade procedures for Alder mdoules.
     * 
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0 
     */
    interface UpgradeInterface
    {
        public static function run();
    }
