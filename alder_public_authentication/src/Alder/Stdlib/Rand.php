<?php
    namespace Alder\Stdlib;
    
    use Zend\Math\Rand as ZendRand;
    
    /**
     * Simple wrapper for Zend\Math\Rand adding some default charsets for getString().
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Rand extends ZendRand
    {
        const ALPHANUMERIC = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSDTUVWXYZ0123456789";
        const ALPHANUMERIC_LOWER = "abcdefghijklmnopqrstuvqxyz0123456789";
        const ALPHANUMERIC_UPPER = "ABCDEFGHIJKLMNOPQRSDTUVWXYZ0123456789";
        const ALPHABETIC = "abcdefghijklmnopqrstuvqxyzABCDEFGHIJKLMNOPQRSDTUVWXYZ";
        const ALPHABETIC_LOWER = "abcdefghijklmnopqrstuvqxyz";
        const ALPHABETIC_UPPER = "ABCDEFGHIJKLMNOPQRSDTUVWXYZ";
    }
