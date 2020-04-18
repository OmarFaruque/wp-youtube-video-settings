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
define( 'DB_NAME', 'larasof1_dev1' );

/** MySQL database username */
define( 'DB_USER', 'larasof1_larasof1' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Mahmud123698' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ' kv&yzL[m2;6B0%54fGN%P~i([LK96Ov|J??5gi8[#Cc<riW9F7R?1}5&* 5U^e ' );
define( 'SECURE_AUTH_KEY',  '.Z0*s?!Tl)2n^gG@)HN7OeV:|xx+4t/(c3(5E&`sVaqV~EUtE+U)}P[8|(;@2=@^' );
define( 'LOGGED_IN_KEY',    'fX YQJ!vU~I8n%&zLFBq*WJ}O9yk+>hZ~$5V+WPn>O M<%xq2tGad)I!F/i=3Ij2' );
define( 'NONCE_KEY',        'Vw0)L>:%]oM?`He`!4J(dI*nDx6V86>nV&<%&,8hpAa~(>8vD[`YmTk2.&&C.04Z' );
define( 'AUTH_SALT',        'C=V^;9[aGLs~JF6mW~[: b@ghho|wuQET 2221a;fIpd$`=f(/w>~4gRpMbJPe4g' );
define( 'SECURE_AUTH_SALT', 'g+jW{*jr7/|pDv/u cc&Xw||XAceN%}=QO w}do4ONrU5T0*3VaBGx9CO4QA.dr?' );
define( 'LOGGED_IN_SALT',   '|%/po6]k#w}/2vKS4,[tw4~d1[A#*]mezwGt0x[biCvmre[Id9=qepL(.nP+) CX' );
define( 'NONCE_SALT',       'hyK;d;Ncp=ga[+}Im24nW ?Zd8*2t:D/%3cNGw2CV~@twC(U4aeR#U(05CtWyv|F' );

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
