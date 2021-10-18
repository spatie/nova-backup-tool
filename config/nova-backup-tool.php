<?php

return [
    /*
     * Enable or disable backup tool polling.
     */
    'polling' => true,

    /*
     * Interval seconds between polling requests.
     */
    'polling_interval' => 1,

    /*
     * Queue to use for the jobs to run through.
     */
    'queue' => null,

    /*
     * The time at which the URL should expire.
     * This can be a PHP DateTime object, set to `null` to disable creating a temporary URL
     */
    'temporary_url_expiration' => null,
];
