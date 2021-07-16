<?php

return [

    /*
     * Custom header name. `null` will use the default name defined in the middleware.
     */
    'header_name' => null,

    /*
     * Enabling this option will cause the middleware to dispatch an event every time a deprecated route is visited.
     */
    'fire_event' => true,

    /*
     * Choice of level to trigger a log every time a deprecated route is visited. This functionality uses the
     * `Log::` facade under the hood, so it will use your log channel defined in your project's config/logging.php.
     *
     * Available options: false, 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'
     */
    'log_level' => 'warning',

];
