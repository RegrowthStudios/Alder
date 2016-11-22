<?php
    namespace Alder\Db\Row;
    
    use Alder\DiContainer;
    
    use Zend\Db\Adapter\Adapter;
    use Zend\Db\Metadata\MetadataInterface;
    use Zend\Db\RowGateway\AbstractRowGateway;
    use Zend\Db\Sql\Sql;
    
    /**
     * Abstract row representation class, implementing functions for creating rows from, and transforming rows into,
     * arrays.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     * @abstract
     */
    abstract class AbstractRow extends AbstractRowGateway implements AbstractRowInterface
    {
        /**
         * Gets the columns of the unique keys of the table.
         * Acquires them from metadata if not already available.
         *
         * @return array|NULL
         */
        protected static function getUniqueKeyColumns() : ?array {
            if (!static::$uniqueKeyColumns) {
                self::fetchMetadata();
            }
            
            return static::$uniqueKeyColumns;
        }
        
        /**
         * Gets the columns of the primary key of the table.
         * Acquires them from metadata if not already available.
         *
         * @return array|NULL
         */
        protected static function getPrimaryKeyColumns() : ?array {
            if (!static::$primaryKeyColumns) {
                self::fetchMetadata();
            }
            
            return static::$primaryKeyColumns;
        }
        
        /**
         * Fetches the metadata for unique and primary keys.
         */
        protected static function fetchMetadata() : void {
            // Acquire the metadata for the table.
            /**
             * @var \Zend\Db\Metadata\Object\TableObject $metadata
             */
            $metadata = DiContainer::get()->get(MetadataInterface::class)->getTable(static::$table);
            
            $uniqueKeyColumns = [];
            
            // Iterate over constraints and set unique/primary key columns.
            /**
             * @var \Zend\Db\Metadata\Object\ConstraintObject $constraint
             */
            foreach ($metadata->getConstraints() as $constraint) {
                if ($constraint->isPrimaryKey()) {
                    static::$primaryKeyColumn = $constraint->getColumns();
                } else {
                    if ($constraint->isUnique()) {
                        $uniqueKeyColumns[] = $constraint->getColumns();
                    }
                }
            }
            
            // Set unique key columns.
            static::$uniqueKeyColumns = $uniqueKeyColumns ?: null;
        }
        
        /**
         * The columns of the unique keys of the table.
         *
         * @var array
         */
        protected $uniqueKeyColumns = null;
        
        /**
         * Prepares the row with the needed SQL object and data to operate on the associated database.
         *
         * @param string $table The table in which the row resides.
         */
        protected function __construct(string $table) {
            $container = DiContainer::get();
            
            // Prefix table.
            $this->table = $container->get("config")["alder"]["db"]["table_prefix"] . $table;
            
            // Acquire unique and primary key column info.
            $this->primaryKeyColumn = self::getPrimaryKeyColumns();
            $this->uniqueKeyColumns = self::getUniqueKeyColumns();
            
            // Acquire adapter from service container and create SQL object with it.
            $this->sql = new Sql($container->get(Adapter::class), $this->table);
            
            // Initialise the row gateway.
            $this->initialize();
        }
        
        /**
         * Determines if the row instance exists in the database.
         *
         * @return bool True if the row exists, false otherwise.
         *
         * @throws \Exception Thrown if not all necessary primary key data was provided.
         */
        public function exists() : bool {
            if ($this->rowExistsInDatabase()) {
                return true;
            }
            try {
                return $this->existsInDatabase();
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * Determines if a row instance exists in the database.
         * In contrast to rowExistsInDatabase() this method checks the actual
         * database instead of if primary key data has been defined.
         *
         * @return bool True if the row exists, false otherwise.
         *
         * @throws \Exception Thrown if not all necessary primary key data was provided.
         */
        protected function existsInDatabase() : bool {
            $where = [];
            
            foreach ($this->primaryKeyColumn as $pkColumn) {
                if (!isset($this->data[$pkColumn])) {
                    $where = [];
                    break;
                }
                $where[$pkColumn] = $this->data[$pkColumn];
            }
            
            if (empty($where)) {
                foreach ($this->uniqueKeyColumns as $uniqueKeyColumn) {
                    $success = true;
                    foreach ($uniqueKeyColumn as $ukColumn) {
                        if (!isset($this->data[$ukColumn])) {
                            $where = [];
                            $success = false;
                            break;
                        }
                        $where[$ukColumn] = $this->data[$ukColumn];
                    }
                    if ($success) {
                        break;
                    }
                }
            }
            
            if (empty($where)) {
                throw new \Exception("Neither the primary key data or any unique key data has been provided, at least one of these must be to determine existence of the row in the database.");
            }
            
            $select = $this->sql->select();
            $select->where($where)->columns([end($this->primaryKeyColumn)]);
            
            $statement = $this->sql->prepareStatementForSqlObject($select);
            
            return (bool) $statement->execute()->count();
        }
    }
