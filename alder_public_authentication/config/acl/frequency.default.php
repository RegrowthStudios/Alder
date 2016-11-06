<?php

    class Frequency
    {
        public $max_count;
        public $time_period;
        public $cooldown;
        public $cooldown_coefficient;

        public function __construct(integer $max_count, double $time_period, double $cooldown, double $cooldown_coefficient) {
            $this->max_count = $max_count;
            $this->time_period = $time_period;
            $this->cooldown = $cooldown;
            $this->cooldown_coefficient = $cooldown_coefficient;
        }
    }

    $frequencies = [];

    // Example.
    //$frequencies[GUEST . AUTHENTICATION . GET] = new Frequency(2, 3.0, 4.0, 1.2);

    // TODO(Matthew): Set the frequencies for each of the various combinations.

    return $frequencies;
