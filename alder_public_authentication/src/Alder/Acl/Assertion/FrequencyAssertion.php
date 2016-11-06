<?php
    
    namespace Alder\Acl\Assertion;
    
    use Alder\Acl\Assertion\FrequencyContainer;
    
    use Zend\Permissions\Acl\Acl;
    use Zend\Permissions\Acl\Assertion\AssertionInterface;
    use Zend\Permissions\Acl\Resource\ResourceInterface;
    use Zend\Permissions\Acl\Role\RoleInterface;
    
    /**
     * Provides a stack for passing multiple assertions into ACL rules.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class FrequencyAssertion implements AssertionInterface
    {
        public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null,
                               $privilege = null) {
            // Define a unique string for this combination of role, resource and privilege.
            $ruleUID = (string) $role . (string) $resource . ($privilege ?: "");
            
            // Fetch frequency settings.
            $frequencySettings = FrequencyContainer::create()->get()[$ruleUID];
            
            // TODO(Matthew): Get recent usage data of requester.
            $visitorData = [];
            
            // Reset the usage count after the time period set in the frequency settings.
            if ($visitorData["last_usage_times"][$ruleUID] + $frequencySettings["usage_period"] <= time()) {
                // TODO(Matthew): Reset usage count data for visitor for this rule.
                $visitorData["usage_counts"][$ruleUID] = 0;
            }
            
            // Determine if the current usage count is greater than the max count limit.
            if (($usageCount = $visitorData["usage_counts"][$ruleUID]) < $frequencySettings["max_count"]) {
                // Determine if the visitor has any cooldown periods assigned to them.
                if (($cooldownCount = $visitorData["cooldown_counts"][$ruleUID]) > 0) {
                    // Calculate when the current cooldown period expires.
                    $cooldownExpire = $visitorData["last_cooldown_start_times"][$ruleUID]
                                      + $frequencySettings["cooldown_period"]
                                        * pow($frequencySettings["cooldown_coefficient"], $cooldownCount);
                    if ($cooldownExpire > time()) {
                        // TODO(Matthew): Update visitor data for new usage count and last usage time.
                        return false;
                    }
                    if ($visitorData["last_cooldown_start_times"][$ruleUID]
                        + $frequencySettings["cooldown_reset_period"] <= time()
                    ) {
                        // TODO(Matthew): Reset visitor cooldown count for this rule.
                    }
                }
                
                // TODO(Matthew): Update visitor data for new usage count and last usage time.
                return true;
            } else {
                if ($usageCount > $frequencySettings["cut_off_count"]) {
                    // TODO(Matthew): Trigger cut off mechanism (i.e. banning IP). (Consider best approach to this. E.g. could be good to combine with a global middleware for abuse across any and all endpoints.)
                }
                
                // TODO(Matthew): Update visitor data for reset usage count, last usage time, cooldown count and last cooldown time.
                return false;
            }
        }
    }
