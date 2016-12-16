<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\DiContainer;
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\Client as ClientTable;
    use Alder\PublicAuthentication\Db\Table\ClientScopeMap as ClientScopeMapTable;
    use Alder\PublicAuthentication\Db\Table\Scope as ScopeTable;
    
    use Zend\Db\Adapter\Adapter;
    use Zend\Db\ResultSet\ResultSet;
    
    /**
     * Representation of a row in the table of clients.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Client extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  VARCHAR(16)    |  Yes  |       |       |  The ID of the client.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the client.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the client.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the client.
         * name                       |  VARCHAR(50)    |       |       |       |  The name of the client.
         */
        
        /**
         * @var string Identifies a request for all allowed scopes.
         */
        public const FULL_SCOPE = "all";
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = ClientTable::NAME;
        
        /**
         * The columns of the unique keys of the table.
         *
         * @var array
         */
        protected static $uniqueKeyColumns = null;
        
        /**
         * The columns of the primary key of the table.
         *
         * @var array
         */
        protected static $primaryKeyColumns = null;
        
        /**
         * Prepare the row.
         */
        public function __construct() {
            parent::__construct(static::$table);
        }
    
        /**
         * Gets all redirects registered by the client. Returns null if
         * the row object has yet to be initialised or if it's ID is
         * uninitialised. Returns redirects in no paticular order, except,
         * if any redirect has been marked as the default by the client, then
         * it will be found at the index "default".
         *
         * @return array|null
         */
        public function getRegisteredRedirects() : ?array {
            if (!$this->isInitialized || !$this->rowExistsInDatabase()) {
                return null;
            }
            
            /**
             * @var \Alder\PublicAuthentication\Db\Table\ClientRedirect $redirectsTable;
             */
            $redirectsTable = DiContainer::get()->get("alder_pa_table_cache")->fetchTable("ClientRedirect");
    
            return array_reduce($redirectsTable->getByColumnWithValue("client_id", $this["id"], ["uri", "default"])->toArray(), function ($carry, $item) {
                if ($item["default"]) {
                    $carry["default"] = $item["uri"];
                } else {
                    $carry[] = $item["uri"];
                }
            }, []);
        }
        
        // TODO(Matthew): Have a set of defaults for all, public and confidential clients? Could make requests faster.
        /**
         * Determines which of the provided scopes are allowed for this client.
         * By default returns all allowed scopes for the client by default.
         *
         * @param string $scopes   The scopes to find allowed scopes within.
         *
         * @return array|null The array of allowed scopes.
         */
        public function getAllowedScopes(string $scopes = self::FULL_SCOPE) : ?array {
            if (!$this->isInitialized || !$this->rowExistsInDatabase()) {
                return null;
            }
    
            /**
             * @var \Zend\Db\Adapter\Adapter $adapter
             */
            $adapter = DiContainer::get()->get(Adapter::class);
            
            // Prepare statement to get labels of all allowed scopes of the specified client.
            $results = $adapter->query('
                SELECT ' . $adapter->platform->quoteIdentifier("label")
                 . ' FROM ' . $adapter->platform->quoteIdentifier(ScopeTable::NAME)
                 . ' WHERE ' . $adapter->platform->quoteIdentifier("id")
                 . ' = (SELECT ' . $adapter->platform->quoteIdentifier("scope_id")
                 . ' FROM ' . $adapter->platform->quoteIdentifier(ClientScopeMapTable::NAME)
                 . ' WHERE client_id = ?)
            ');
            // Get results.
            $allowedScopes = array_map(function ($item) {
                return $item["label"];
            }, (new ResultSet())->initialize($results->execute([$this["id"]]))->toArray());
            
            // Return all scopes requested that are allowed.
            if ($scopes === self::FULL_SCOPE) {
                return $allowedScopes;
            } else {
                $scopes = explode(" ", $scopes);
                
                // Reduce scope of request to intersect with allowed scope of the client making the request.
                return array_intersect($scopes, $allowedScopes);
            }
        }
    }
