<?php
    
    namespace Alder\PublicAuthentication\Db\Table;
    
    use Alder\Db\Table\AbstractTable;
    use Alder\PublicAuthentication\Db\Row\UserLicenseMap as UserLicenseMapRow;
    
    /**
     * Gateway for the user license map table.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class UserLicenseMap extends AbstractTable
    {
        /**
         * @var string Name of the table.
         */
        const NAME = "user_license_maps";
        
        /**
         * Prepare the user table gateway, establishing the row prototype.
         */
        public function __construct() {
            parent::__construct(self::NAME, new UserLicenseMapRow());
        }
    }
