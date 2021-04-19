<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'project1');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '=0X{n3&i[d(m7P5=tm!C#-cEF(J]tYQqAmVFap6cS;lSV[?*(i9qJ[Hs8LE0G*<D');
define('SECURE_AUTH_KEY',  'jjd*O,t uBG/_Y?a&c;o{:9+Cy8[I!+?)fSm6j7G:6&b~=th.@3`]Cr*[lU{~DwV');
define('LOGGED_IN_KEY',    '@Y@=K87`DRbA,>[(=z#5du(TvMw%(dFZ^^#5+#.LaYdi)c}fGgmLKm)qn-V/ Gn`');
define('NONCE_KEY',        'VQX<tD4MW+g(fFAG|QDASLz}9w2A>SZO`G9d/D|mW26%z3_G|.7I;k`lWV_(`aXv');
define('AUTH_SALT',        ':qDi{E+dk#a).n^>RLu$opk$iUGcg$QFS%DqA3qF|aM1UbDVA^PIrzF?^kPV/qHp');
define('SECURE_AUTH_SALT', ':-V#rvwf=*;|A&cf@4a%UWaXw@V/ TXpH+y.X2zr0$N:gS0[|}px~7J+Qu]3gRhM');
define('LOGGED_IN_SALT',   'WWmV-Y9k1Y93~mbV{_j8-3-^J{R4*W_<ks$<j5[,8g}xI4C_X[Cq_tHh#EU elZ)');
define('NONCE_SALT',       ' #CKxM@x=1fwsI2lO>4t=cG*qb=4jFu(E)hqveTOY$J?e#fGrvCVG/?bV=krT;Jd');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
