<?php
    namespace Alder\Db\Row;
    
    use Alder\Db\Row\AbstractRowInterface;
    use Alder\Stdlib\ArrayUtils;
    
    /**
     * Abstract row representation class, implementing functions for creating rows from, and transforming rows into, arrays.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractRow implements AbstractRowInterface
    {
        /**
         * {@inheritdoc}
         */
        public function exchangeArray($data)
        {
            $validatedData = ArrayUtils::validateArrayLike($data, get_class($this), true);
            $oldData = get_object_vars($this);
            foreach($oldData as $key => $_) {
                if (isset($validatedData[$key])) {
                    $this->$key = $validatedData[$key];
                }
            }
            return $oldData;
        }
        
        /**
         * {@inheritdoc}
         */
        public function toArray()
        {
            return get_object_vars($this);
        }
    }
