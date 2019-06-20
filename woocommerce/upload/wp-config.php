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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'woocommerce' );

/** MySQL database username */
define( 'DB_USER', 'woocommerce' );

/** MySQL database password */
define( 'DB_PASSWORD', 'woocommerce' );

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
define( 'AUTH_KEY',         'Kh+ME5cMrzQ3Eh91nmgeU [7|om:o+&)3Wm%=%=iTPq*Xe#}r|R;456g|R-}$79*' );
define( 'SECURE_AUTH_KEY',  'q^z2xuEuM&>5~eP^%b+fbfs-`Zj,L#BEA_+/Yfy4$`CXWPZ4+y,}oP~;TA3q?>kJ' );
define( 'LOGGED_IN_KEY',    'vE;M<kBxT?kg[S(JA_N(8GS #=p?I%*gy/}tGkqJ-mRY1#&8%TByuSC%XgT+~Ql0' );
define( 'NONCE_KEY',        '9&T?+{XgU3.cviMIin-n{wiVm]xYLk8W1<`E-APhu1l_b3aUA8a V#uT00>Y$5Q)' );
define( 'AUTH_SALT',        '/2E?`Gik0m X/]/DMr<Cbfd[s3%bkG98QZIURA7*{&ok-t|c[|<,[{Qa:6vcJ#E^' );
define( 'SECURE_AUTH_SALT', '2fu$[)rF9.0n9]*Qb~Pl#@x) 4lxH,=YcH p`oHF}hn7caVx%1P!{H/=?Yzx][(m' );
define( 'LOGGED_IN_SALT',   '&4~6yVKGp6m2t{`RVN=cEj&~n_g`GF6=RNq%fWt[8.S^+Y%e`?r[I/-5i6iFjOI8' );
define( 'NONCE_SALT',       '>5B{;mM4p`fZk <]8?MGrB=7}29$*BMp&vQ08C:Q<K.tDN{c!SYsf=).l>A6GuKC' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wc_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
