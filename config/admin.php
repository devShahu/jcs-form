<?php

/**
 * Admin Configuration
 * 
 * IMPORTANT: Change these credentials before deploying to production!
 * To generate a new password hash, run:
 * php -r "echo password_hash('your_password', PASSWORD_DEFAULT);"
 */

return [
    'username' => 'admin',
    
    // Default password: admin123
    // CHANGE THIS IN PRODUCTION!
    'password_hash' => '$2y$10$.xBgXry.oxjpCoA7xepwh.KdfykwgDY6R2Q1OkJDcUu8u0C/kP4E2',
    
    // Session timeout in seconds (default: 24 hours)
    'session_timeout' => 24 * 60 * 60,
    
    // Rate limiting for login attempts
    'max_login_attempts' => 5,
    'login_lockout_duration' => 15 * 60, // 15 minutes
];
