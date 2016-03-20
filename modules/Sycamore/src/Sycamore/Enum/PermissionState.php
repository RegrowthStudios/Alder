<?php
    namespace Sycamore\Enums;
    
    /**
     * Enumerator of permission states.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class PermissionState
    {
        const ALLOWED = 1;
        const NEITHER = 0;
        const DENIED = -1;
    }
