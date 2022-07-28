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
define( 'DB_NAME', 'web' );

/** Database username */
define( 'DB_USER', 'web' );

/** Database password */
define( 'DB_PASSWORD', '123456' );

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
define( 'AUTH_KEY',         'AJaObwiRfeZR-r8n`U3^U%ZmHmt=([&I4h %$CAwxH#:lds|QEuBDz3(w1Mp,hRB' );
define( 'SECURE_AUTH_KEY',  '8ix3[j ddZ*UaxS+&@=>w)Rb?w$e3I8l8~WZ.n8]]I2+-!4SeTa?W|W:zlRs+E)m' );
define( 'LOGGED_IN_KEY',    '!]Jo5]pJ|;X:i&[+VRMJS/F_ZXJ^s-I<y9zWo[z`]iDJ:17[/I 6}{5+bmC_pb-I' );
define( 'NONCE_KEY',        'v]hcO?r8A{gpeNWki4Au}k&k}0N36b`{=`^=Bu`{c8|rV2uHc?-}Fb_gEE(#AU2O' );
define( 'AUTH_SALT',        ':{MB6V:JmKZ`CB=Zqy)f]+W}FcTmK&d3+WkZ*:(]$b$Ep7zf~eRkO-:guc+!*>XF' );
define( 'SECURE_AUTH_SALT', '2y[B]IX=rbOZ)X}HL24@cW~}nbp6SsuO< sjj9yE0:#]8L5b$/[FNktaeBd{(tFw' );
define( 'LOGGED_IN_SALT',   'C0i}0c< [^XET#Sp~T>eOt.*D8O2 &!,D/i<XUgG O/A9u%mx&8RWAbIB?Q,Kwup' );
define( 'NONCE_SALT',       'N4hh$6`@(osh%w6&D)^um/HuFVE0d)*qNRG8zdtu-HNtCuT!pWRKIS7F7S$BNkUS' );

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

define( 'WP_ALLOW_REPAIR', true );
