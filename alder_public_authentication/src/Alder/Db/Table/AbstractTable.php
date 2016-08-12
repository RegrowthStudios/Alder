<?php
    
    namespace Alder\Db\Table;
    
    use Alder\Container;
    use Alder\Db\Row\AbstractRowInterface;
    
    use Zend\Db\Adapter\Adapter;
    use Zend\Db\TableGateway\AbstractTableGateway;
    use Zend\Db\ResultSet\ResultSet;
    
    /**
     * Alder-specific implementation of Zend's abstract table gateway.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractTable extends AbstractTableGateway
    {
        /**
         * The local application settings.
         *
         * @var array
         */
        protected $config;
        
        /**
         * Prepares the table with the DB adapter and local settings.
         * 
         * @param string $table The name of the table for this instance.
         * @param \Zend\Db\ResultSet\ResultSetInterface|NULL $row The row object to construct with the results of queries.
         */
        public function __construct($table, AbstractRowInterface& $row = NULL)
        {
            $this->config = Container::get()->get("config")["alder"];
            $dbConfig = $this->config["db"];
            
            // Prefix table.
            $this->table = $dbConfig["table_prefix"] . $table;
            
            // Create adapter.
            $this->adapter = new Adapter($dbConfig["adapter"]);
            
            // Prepare result set prototype.
            $this->resultSetPrototype = new ResultSet();
            if ($row) {
                $this->resultSetPrototype->setArrayObjectPrototype($row);
            }
            
            // Initialise the table gateway. Sets up SQL object.
            $this->initialize();
        }
    }
