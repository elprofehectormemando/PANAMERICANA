<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'panamericana' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'iaSHch][z_k>C?PabCXzHB7$yrBl5jbxjz2VYQO |Lhq~W7I.TF=voef{;3xPs>J' );
define( 'SECURE_AUTH_KEY',  'IMD~~7F3fjl(&nm7(OBCB>:Xw^$L^Ya$g39K,m&f,D-$S!9%=e8CcQSE:1ln{pSk' );
define( 'LOGGED_IN_KEY',    'f.k ?7GhgQG:}p_s6qBePYi)n4RUjra~;o9qU.>p[@FCeR2^0NxbD`pj0 w0y{Jd' );
define( 'NONCE_KEY',        'i7X].~J[l~U!;HPVA6AGx=i=t)(3k5PC(/G[pukjqW40=HxJMo49@:{T}:LT Lg6' );
define( 'AUTH_SALT',        'U`NH_da<2FjdvRuO7]}dekryP2KS}:Rvx2Byh-hTAgdWefkv%A_Kpe%XIwoZBt*L' );
define( 'SECURE_AUTH_SALT', '.oqydMY5c,FunZ:gPqt]3CQr6NU/`AX2)Muu.s,x$xv2@3s%)bDm^r*gsUNdD/An' );
define( 'LOGGED_IN_SALT',   '51+s9k=[)R[)3a]X`v42ebEPDaA$33 HNSyMcisn0UjI3HU6;ET/GhyZ9<k<4!@}' );
define( 'NONCE_SALT',       'gOElP3AZ:!(_lM. uRs1_*{.(ieY!4#y#j>H^q<ex7i6EA,r|C=:FOvpT:WIXvPk' );

/**#@-*/

/**
 * WordPress database table prefix.
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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
