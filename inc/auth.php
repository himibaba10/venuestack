<?php
/**
 * Branded login / register pages.
 *
 * @package Venuestack
 */

defined('ABSPATH') || exit;

/**
 * Permalink for a seeded auth page slug.
 *
 * @param string $slug login|register|lost-password|reset-password
 */
function venuestack_get_auth_page_url(string $slug): string
{
	$page = get_page_by_path($slug);
	if ($page instanceof WP_Post) {
		$url = get_permalink($page);
		if (is_string($url) && '' !== $url) {
			return $url;
		}
	}

	return home_url('/' . $slug . '/');
}

/**
 * Ensure publish pages exist for login + register.
 */
function venuestack_ensure_auth_pages(): void
{
	// Booking flow requires accounts; keep open registration available.
	if (!get_option('users_can_register')) {
		update_option('users_can_register', 1);
	}

	$map = array(
		'login'          => __( 'Log in', 'venuestack' ),
		'register'       => __( 'Create account', 'venuestack' ),
		'lost-password'  => __( 'Forgot password', 'venuestack' ),
		'reset-password' => __( 'Reset password', 'venuestack' ),
	);

	foreach ($map as $slug => $title) {
		$existing = get_page_by_path($slug);
		if ($existing instanceof WP_Post) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_title' => $title,
				'post_name' => $slug,
				'post_content' => '',
			),
			true
		);
	}
}
add_action('init', 'venuestack_ensure_auth_pages', 30);

/**
 * Point core login URLs at the branded page.
 *
 * @param string $login_url    Default login URL.
 * @param string $redirect     Redirect after login.
 * @param bool   $force_reauth Force reauth flag.
 */
function venuestack_filter_login_url(string $login_url, string $redirect, bool $force_reauth): string
{
	// Keep stock wp-login.php for forced re-auth (admin interim flows).
	if ($force_reauth) {
		return $login_url;
	}

	$url = venuestack_get_auth_page_url('login');
	if ('' !== $redirect) {
		$url = add_query_arg('redirect_to', $redirect, $url);
	}

	return $url;
}
add_filter('login_url', 'venuestack_filter_login_url', 10, 3);

/**
 * @param string $register_url Default registration URL.
 */
function venuestack_filter_register_url(string $register_url): string
{
	unset($register_url);
	return venuestack_get_auth_page_url('register');
}
add_filter( 'register_url', 'venuestack_filter_register_url' );

/**
 * Point core lost-password URLs at the branded page.
 *
 * @param string $lostpassword_url Default URL.
 * @param string $redirect         Optional redirect after reset login.
 */
function venuestack_filter_lostpassword_url( string $lostpassword_url, string $redirect ): string {
	unset( $lostpassword_url );
	$url = venuestack_get_auth_page_url( 'lost-password' );
	if ( '' !== $redirect ) {
		$url = add_query_arg( 'redirect_to', $redirect, $url );
	}
	return $url;
}
add_filter( 'lostpassword_url', 'venuestack_filter_lostpassword_url', 10, 2 );

/**
 * Rewrite password-reset links in emails to the branded reset page.
 *
 * @param string      $url    Full URL.
 * @param string      $path   Path passed to site_url() / network_site_url().
 * @param string|null $scheme Scheme.
 */
function venuestack_filter_site_url_for_reset( string $url, string $path, $scheme ): string {
	unset( $scheme );
	if ( ! is_string( $path ) || ! str_contains( $path, 'wp-login.php' ) ) {
		return $url;
	}

	$query = array();
	$qpos  = strpos( $path, '?' );
	if ( false !== $qpos ) {
		parse_str( (string) substr( $path, $qpos + 1 ), $query );
	}

	$action = isset( $query['action'] ) ? sanitize_key( (string) $query['action'] ) : '';
	if ( ! in_array( $action, array( 'rp', 'resetpass' ), true ) ) {
		return $url;
	}

	unset( $query['action'] );
	return add_query_arg( $query, venuestack_get_auth_page_url( 'reset-password' ) );
}
add_filter( 'site_url', 'venuestack_filter_site_url_for_reset', 10, 3 );
add_filter( 'network_site_url', 'venuestack_filter_site_url_for_reset', 10, 3 );

/**
 * Catch stock wp-login.php password flows and send them to branded pages.
 */
function venuestack_redirect_wp_login_lostpassword(): void {
	$url = venuestack_get_auth_page_url( 'lost-password' );
	if ( ! empty( $_REQUEST['redirect_to'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$url = add_query_arg(
			'redirect_to',
			venuestack_sanitize_auth_redirect( (string) wp_unslash( $_REQUEST['redirect_to'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$url
		);
	}
	wp_safe_redirect( $url );
	exit;
}
add_action( 'login_form_lostpassword', 'venuestack_redirect_wp_login_lostpassword' );
add_action( 'login_form_retrievepassword', 'venuestack_redirect_wp_login_lostpassword' );

/**
 * Catch stock reset-password links (email → wp-login.php?action=rp).
 */
function venuestack_redirect_wp_login_resetpassword(): void {
	$args = array();
	if ( ! empty( $_REQUEST['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$args['key'] = sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( ! empty( $_REQUEST['login'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$args['login'] = sanitize_text_field( wp_unslash( $_REQUEST['login'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	// Cookie-based RP flow from core (key stored in cookie, login in query).
	$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
	if ( empty( $args['key'] ) && ! empty( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( (string) $_COOKIE[ $rp_cookie ], ':' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		list( $rp_login, $rp_key ) = explode( ':', wp_unslash( (string) $_COOKIE[ $rp_cookie ] ), 2 ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( empty( $args['login'] ) ) {
			$args['login'] = sanitize_text_field( $rp_login );
		}
		$args['key'] = sanitize_text_field( $rp_key );
	}

	wp_safe_redirect( add_query_arg( $args, venuestack_get_auth_page_url( 'reset-password' ) ) );
	exit;
}
add_action( 'login_form_rp', 'venuestack_redirect_wp_login_resetpassword' );
add_action( 'login_form_resetpass', 'venuestack_redirect_wp_login_resetpassword' );

/**
 * Sanitize redirect target for post-login.
 *
 * @param string $redirect Candidate URL.
 */
function venuestack_sanitize_auth_redirect( string $redirect ): string {
	$redirect = esc_url_raw(wp_unslash($redirect));
	if ('' === $redirect) {
		return home_url('/');
	}

	$safe = wp_validate_redirect($redirect, home_url('/'));
	return is_string($safe) && '' !== $safe ? $safe : home_url('/');
}

/**
 * Default destination after a successful login.
 */
function venuestack_default_login_redirect(): string
{
	if (function_exists('venuestack_core_get_my_bookings_url')) {
		return venuestack_core_get_my_bookings_url();
	}

	return home_url('/');
}

/**
 * Logged-in visitors shouldn't sit on auth pages.
 */
function venuestack_auth_pages_logged_in_redirect(): void
{
	if (!is_user_logged_in()) {
		return;
	}

	if ( ! is_page( array( 'login', 'register', 'lost-password' ) ) ) {
		return;
	}

	$redirect = isset($_GET['redirect_to']) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? venuestack_sanitize_auth_redirect((string) wp_unslash($_GET['redirect_to'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: venuestack_default_login_redirect();

	wp_safe_redirect($redirect);
	exit;
}
add_action('template_redirect', 'venuestack_auth_pages_logged_in_redirect', 5);

/**
 * Handle login / register POSTs before output.
 */
function venuestack_handle_auth_forms(): void
{
	if ('POST' !== strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''))) {
		return;
	}

	$action = isset($_POST['venuestack_auth_action']) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		? sanitize_key((string) wp_unslash($_POST['venuestack_auth_action'])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		: '';

	if ( 'login' === $action ) {
		venuestack_process_login_form();
		return;
	}

	if ( 'register' === $action ) {
		venuestack_process_register_form();
		return;
	}

	if ( 'lostpassword' === $action ) {
		venuestack_process_lostpassword_form();
		return;
	}

	if ( 'resetpassword' === $action ) {
		venuestack_process_resetpassword_form();
	}
}
add_action('template_redirect', 'venuestack_handle_auth_forms', 4);

/**
 * Process branded login form.
 */
function venuestack_process_login_form(): void
{
	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'venuestack_login')) {
		wp_safe_redirect(add_query_arg('auth_error', 'nonce', venuestack_get_auth_page_url('login')));
		exit;
	}

	$redirect = isset($_POST['redirect_to'])
		? venuestack_sanitize_auth_redirect((string) wp_unslash($_POST['redirect_to']))
		: venuestack_default_login_redirect();

	$log = isset($_POST['log']) ? sanitize_text_field(wp_unslash($_POST['log'])) : '';
	$pwd = isset($_POST['pwd']) ? (string) wp_unslash($_POST['pwd']) : '';

	if ('' === $log || '' === $pwd) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'auth_error' => 'empty',
					'redirect_to' => $redirect,
				),
				venuestack_get_auth_page_url('login')
			)
		);
		exit;
	}

	$user = wp_signon(
		array(
			'user_login' => $log,
			'user_password' => $pwd,
			'remember' => !empty($_POST['rememberme']),
		),
		is_ssl()
	);

	if (is_wp_error($user)) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'auth_error' => 'failed',
					'redirect_to' => $redirect,
				),
				venuestack_get_auth_page_url('login')
			)
		);
		exit;
	}

	wp_safe_redirect($redirect);
	exit;
}

/**
 * Process branded register form.
 */
function venuestack_process_register_form(): void
{
	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'venuestack_register')) {
		wp_safe_redirect(add_query_arg('auth_error', 'nonce', venuestack_get_auth_page_url('register')));
		exit;
	}

	if (!get_option('users_can_register')) {
		wp_safe_redirect(add_query_arg('auth_error', 'closed', venuestack_get_auth_page_url('register')));
		exit;
	}

	$email = isset($_POST['user_email']) ? sanitize_email(wp_unslash($_POST['user_email'])) : '';
	$login = isset($_POST['user_login']) ? sanitize_user(wp_unslash($_POST['user_login']), true) : '';
	$pwd = isset($_POST['user_pass']) ? (string) wp_unslash($_POST['user_pass']) : '';
	$pwd2 = isset($_POST['user_pass_confirm']) ? (string) wp_unslash($_POST['user_pass_confirm']) : '';

	$redirect = isset($_POST['redirect_to'])
		? venuestack_sanitize_auth_redirect((string) wp_unslash($_POST['redirect_to']))
		: venuestack_default_login_redirect();

	$error = '';
	if ('' === $email || !is_email($email)) {
		$error = 'email';
	} elseif ('' === $login) {
		$error = 'login';
	} elseif (strlen($pwd) < 8) {
		$error = 'password';
	} elseif ($pwd !== $pwd2) {
		$error = 'mismatch';
	} elseif (username_exists($login) || email_exists($email)) {
		$error = 'exists';
	}

	if ('' !== $error) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'auth_error' => $error,
					'redirect_to' => $redirect,
				),
				venuestack_get_auth_page_url('register')
			)
		);
		exit;
	}

	$user_id = wp_create_user($login, $pwd, $email);
	if (is_wp_error($user_id)) {
		wp_safe_redirect(add_query_arg('auth_error', 'failed', venuestack_get_auth_page_url('register')));
		exit;
	}

	$role = class_exists('WooCommerce') ? 'customer' : 'subscriber';
	$user = new WP_User((int) $user_id);
	$user->set_role($role);

	$first = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
	$last = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
	if ('' !== $first) {
		update_user_meta($user_id, 'first_name', $first);
	}
	if ('' !== $last) {
		update_user_meta($user_id, 'last_name', $last);
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );

	wp_safe_redirect( $redirect );
	exit;
}

/**
 * Process branded lost-password form.
 */
function venuestack_process_lostpassword_form(): void {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'venuestack_lostpassword' ) ) {
		wp_safe_redirect( add_query_arg( 'auth_error', 'nonce', venuestack_get_auth_page_url( 'lost-password' ) ) );
		exit;
	}

	$user_login = isset( $_POST['user_login'] ) ? trim( (string) wp_unslash( $_POST['user_login'] ) ) : '';
	if ( '' === $user_login ) {
		wp_safe_redirect( add_query_arg( 'auth_error', 'empty', venuestack_get_auth_page_url( 'lost-password' ) ) );
		exit;
	}

	$result = retrieve_password( $user_login );
	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'auth_error', 'invalid', venuestack_get_auth_page_url( 'lost-password' ) ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'auth_notice', 'sent', venuestack_get_auth_page_url( 'lost-password' ) ) );
	exit;
}

/**
 * Process branded reset-password form.
 */
function venuestack_process_resetpassword_form(): void {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'venuestack_resetpassword' ) ) {
		wp_safe_redirect( add_query_arg( 'auth_error', 'nonce', venuestack_get_auth_page_url( 'reset-password' ) ) );
		exit;
	}

	$key   = isset( $_POST['rp_key'] ) ? sanitize_text_field( wp_unslash( $_POST['rp_key'] ) ) : '';
	$login = isset( $_POST['rp_login'] ) ? sanitize_text_field( wp_unslash( $_POST['rp_login'] ) ) : '';
	$pwd   = isset( $_POST['pass1'] ) ? (string) wp_unslash( $_POST['pass1'] ) : '';
	$pwd2  = isset( $_POST['pass2'] ) ? (string) wp_unslash( $_POST['pass2'] ) : '';

	$base_args = array(
		'key'   => $key,
		'login' => $login,
	);

	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( 'auth_error', 'expired', venuestack_get_auth_page_url( 'reset-password' ) ) );
		exit;
	}

	if ( strlen( $pwd ) < 8 ) {
		wp_safe_redirect( add_query_arg( array_merge( $base_args, array( 'auth_error' => 'password' ) ), venuestack_get_auth_page_url( 'reset-password' ) ) );
		exit;
	}

	if ( $pwd !== $pwd2 ) {
		wp_safe_redirect( add_query_arg( array_merge( $base_args, array( 'auth_error' => 'mismatch' ) ), venuestack_get_auth_page_url( 'reset-password' ) ) );
		exit;
	}

	reset_password( $user, $pwd );

	$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
	if ( isset( $_COOKIE[ $rp_cookie ] ) ) {
		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
	}

	wp_safe_redirect( add_query_arg( 'auth_notice', 'reset', venuestack_get_auth_page_url( 'login' ) ) );
	exit;
}

/**
 * Map auth_error query keys to user-facing copy.
 *
 * @param string $code    Error code.
 * @param string $context login|register|lostpassword|resetpassword
 */
function venuestack_auth_error_message(string $code, string $context): string
{
	$messages = array(
		'nonce'    => __( 'Something went wrong. Please try again.', 'venuestack' ),
		'empty'    => 'lostpassword' === $context
			? __( 'Enter your username or email.', 'venuestack' )
			: __( 'Enter your username and password.', 'venuestack' ),
		'failed'   => 'login' === $context
			? __( 'Those credentials didn’t match. Try again.', 'venuestack' )
			: __( 'Could not create your account. Try again.', 'venuestack' ),
		'email'    => __( 'Enter a valid email address.', 'venuestack' ),
		'login'    => __( 'Choose a username.', 'venuestack' ),
		'password' => __( 'Use a password with at least 8 characters.', 'venuestack' ),
		'mismatch' => __( 'Passwords don’t match.', 'venuestack' ),
		'exists'   => __( 'That username or email is already registered.', 'venuestack' ),
		'closed'   => __( 'Registration is currently closed.', 'venuestack' ),
		'invalid'  => __( 'No account matched that username or email.', 'venuestack' ),
		'expired'  => __( 'That reset link is invalid or has expired. Request a new one.', 'venuestack' ),
		'missing'  => __( 'Open the reset link from your email to choose a new password.', 'venuestack' ),
	);

	return $messages[ $code ] ?? __( 'Something went wrong. Please try again.', 'venuestack' );
}

/**
 * Success notice copy for auth pages.
 *
 * @param string $code Notice code.
 */
function venuestack_auth_notice_message( string $code ): string {
	$messages = array(
		'sent'  => __( 'Check your email for a link to reset your password.', 'venuestack' ),
		'reset' => __( 'Password updated. You can log in now.', 'venuestack' ),
	);

	return $messages[ $code ] ?? '';
}

/**
 * Render the branded login form (for page templates).
 */
function venuestack_render_login_form(): void
{
	$redirect = isset($_GET['redirect_to']) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? venuestack_sanitize_auth_redirect((string) wp_unslash($_GET['redirect_to'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: venuestack_default_login_redirect();

	$error_code = isset( $_GET['auth_error'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key( (string) wp_unslash( $_GET['auth_error'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$notice_code = isset( $_GET['auth_notice'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key( (string) wp_unslash( $_GET['auth_notice'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$can_register = (bool) get_option( 'users_can_register' );
	$lost_url     = wp_lostpassword_url( $redirect );
	?>
	<div class="venuestack-auth-form venuestack-auth-form--login">
		<?php if ( '' !== $notice_code ) : ?>
			<p class="venuestack-auth-form__notice is-style-body" role="status">
				<?php echo esc_html( venuestack_auth_notice_message( $notice_code ) ); ?>
			</p>
		<?php endif; ?>
		<?php if ( '' !== $error_code ) : ?>
			<p class="venuestack-auth-form__error is-style-body" role="alert">
				<?php echo esc_html( venuestack_auth_error_message( $error_code, 'login' ) ); ?>
			</p>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( venuestack_get_auth_page_url( 'login' ) ); ?>" class="venuestack-auth-form__fields">
			<?php wp_nonce_field( 'venuestack_login' ); ?>
			<input type="hidden" name="venuestack_auth_action" value="login" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>" />

			<label class="venuestack-auth-form__field">
				<span class="is-style-label"><?php echo esc_html__( 'Username or email', 'venuestack' ); ?></span>
				<input type="text" name="log" required autocomplete="username" class="venuestack-auth-form__input" />
			</label>

			<label class="venuestack-auth-form__field">
				<span class="is-style-label"><?php echo esc_html__( 'Password', 'venuestack' ); ?></span>
				<input type="password" name="pwd" required autocomplete="current-password" class="venuestack-auth-form__input" />
			</label>

			<label class="venuestack-auth-form__remember">
				<input type="checkbox" name="rememberme" value="forever" />
				<span><?php echo esc_html__( 'Remember me', 'venuestack' ); ?></span>
			</label>

			<button type="submit" class="venuestack-auth-form__submit">
				<?php echo esc_html__( 'Log in', 'venuestack' ); ?>
			</button>
		</form>

		<p class="venuestack-auth-form__links is-style-body">
			<a href="<?php echo esc_url( $lost_url ); ?>"><?php echo esc_html__( 'Forgot password?', 'venuestack' ); ?></a>
			<?php if ( $can_register ) : ?>
				<span aria-hidden="true"> · </span>
				<a href="<?php echo esc_url( add_query_arg( 'redirect_to', $redirect, venuestack_get_auth_page_url( 'register' ) ) ); ?>">
					<?php echo esc_html__( 'Create account', 'venuestack' ); ?>
				</a>
			<?php endif; ?>
		</p>
	</div>
	<?php
}

/**
 * Render the branded register form (for page templates).
 */
function venuestack_render_register_form(): void
{
	$redirect = isset($_GET['redirect_to']) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? venuestack_sanitize_auth_redirect((string) wp_unslash($_GET['redirect_to'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: venuestack_default_login_redirect();

	$error_code = isset($_GET['auth_error']) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key((string) wp_unslash($_GET['auth_error'])) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$can_register = (bool) get_option('users_can_register');
	$login_url = add_query_arg('redirect_to', $redirect, venuestack_get_auth_page_url('login'));
	?>
	<div class="venuestack-auth-form venuestack-auth-form--register">
		<?php if (!$can_register): ?>
			<p class="venuestack-auth-form__error is-style-body" role="status">
				<?php echo esc_html__('Registration is currently closed.', 'venuestack'); ?>
			</p>
			<p class="venuestack-auth-form__links is-style-body">
				<a href="<?php echo esc_url($login_url); ?>"><?php echo esc_html__('Log in instead', 'venuestack'); ?></a>
			</p>
		<?php else: ?>
			<?php if ('' !== $error_code): ?>
				<p class="venuestack-auth-form__error is-style-body" role="alert">
					<?php echo esc_html(venuestack_auth_error_message($error_code, 'register')); ?>
				</p>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url(venuestack_get_auth_page_url('register')); ?>"
				class="venuestack-auth-form__fields">
				<?php wp_nonce_field('venuestack_register'); ?>
				<input type="hidden" name="venuestack_auth_action" value="register" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>" />

				<div class="venuestack-auth-form__row">
					<label class="venuestack-auth-form__field">
						<span class="is-style-label"><?php echo esc_html__('First name', 'venuestack'); ?>
						</span>
						<input type="text" name="first_name" autocomplete="given-name" class="venuestack-auth-form__input" />
					</label>
					<label class="venuestack-auth-form__field">
						<span class="is-style-label">
							<?php echo esc_html__('Last name', 'venuestack'); ?>
						</span>
						<input type="text" name="last_name" autocomplete="family-name" class="venuestack-auth-form__input" />
					</label>
				</div>

				<label class="venuestack-auth-form__field">
					<span class="is-style-label">
						<?php echo esc_html__('Username', 'venuestack'); ?>
					</span>
					<input type="text" name="user_login" required autocomplete="username" class="venuestack-auth-form__input" />
				</label>

				<label class="venuestack-auth-form__field">
					<span class="is-style-label">
						<?php echo esc_html__('Email', 'venuestack'); ?>
					</span>
					<input type="email" name="user_email" required autocomplete="email" class="venuestack-auth-form__input" />
				</label>

				<label class="venuestack-auth-form__field">
					<span class="is-style-label">
						<?php echo esc_html__('Password', 'venuestack'); ?>
					</span>
					<input type="password" name="user_pass" required autocomplete="new-password" minlength="8"
						class="venuestack-auth-form__input" />
				</label>

				<label class="venuestack-auth-form__field">
					<span class="is-style-label">
						<?php echo esc_html__('Confirm password', 'venuestack'); ?>
					</span>
					<input type="password" name="user_pass_confirm" required autocomplete="new-password" minlength="8"
						class="venuestack-auth-form__input" />
				</label>

				<button type="submit" class="venuestack-auth-form__submit">
					<?php echo esc_html__('Create account', 'venuestack'); ?>
				</button>
			</form>

			<p class="venuestack-auth-form__links is-style-body">
				<a
					href="<?php echo esc_url($login_url); ?>"><?php echo esc_html__('Already have an account? Log in', 'venuestack'); ?></a>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the branded lost-password request form.
 */
function venuestack_render_lostpassword_form(): void {
	$error_code = isset( $_GET['auth_error'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key( (string) wp_unslash( $_GET['auth_error'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$notice_code = isset( $_GET['auth_notice'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key( (string) wp_unslash( $_GET['auth_notice'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$login_url = venuestack_get_auth_page_url( 'login' );
	?>
	<div class="venuestack-auth-form venuestack-auth-form--lostpassword">
		<?php if ( '' !== $notice_code ) : ?>
			<p class="venuestack-auth-form__notice is-style-body" role="status">
				<?php echo esc_html( venuestack_auth_notice_message( $notice_code ) ); ?>
			</p>
			<p class="venuestack-auth-form__links is-style-body">
				<a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html__( 'Back to log in', 'venuestack' ); ?></a>
			</p>
		<?php else : ?>
			<?php if ( '' !== $error_code ) : ?>
				<p class="venuestack-auth-form__error is-style-body" role="alert">
					<?php echo esc_html( venuestack_auth_error_message( $error_code, 'lostpassword' ) ); ?>
				</p>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( venuestack_get_auth_page_url( 'lost-password' ) ); ?>" class="venuestack-auth-form__fields">
				<?php wp_nonce_field( 'venuestack_lostpassword' ); ?>
				<input type="hidden" name="venuestack_auth_action" value="lostpassword" />

				<label class="venuestack-auth-form__field">
					<span class="is-style-label"><?php echo esc_html__( 'Username or email', 'venuestack' ); ?></span>
					<input type="text" name="user_login" required autocomplete="username" class="venuestack-auth-form__input" />
				</label>

				<button type="submit" class="venuestack-auth-form__submit">
					<?php echo esc_html__( 'Email reset link', 'venuestack' ); ?>
				</button>
			</form>

			<p class="venuestack-auth-form__links is-style-body">
				<a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html__( 'Back to log in', 'venuestack' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the branded set-new-password form.
 */
function venuestack_render_resetpassword_form(): void {
	$key   = isset( $_GET['key'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_text_field( wp_unslash( $_GET['key'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';
	$login = isset( $_GET['login'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_text_field( wp_unslash( $_GET['login'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$error_code = isset( $_GET['auth_error'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		? sanitize_key( (string) wp_unslash( $_GET['auth_error'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		: '';

	$lost_url  = venuestack_get_auth_page_url( 'lost-password' );
	$login_url = venuestack_get_auth_page_url( 'login' );

	$valid_user = null;
	if ( '' !== $key && '' !== $login ) {
		$checked = check_password_reset_key( $key, $login );
		if ( ! is_wp_error( $checked ) ) {
			$valid_user = $checked;
		} elseif ( '' === $error_code ) {
			$error_code = 'expired';
		}
	} elseif ( '' === $error_code ) {
		$error_code = 'missing';
	}
	?>
	<div class="venuestack-auth-form venuestack-auth-form--resetpassword">
		<?php if ( ! $valid_user instanceof WP_User ) : ?>
			<p class="venuestack-auth-form__error is-style-body" role="alert">
				<?php echo esc_html( venuestack_auth_error_message( $error_code, 'resetpassword' ) ); ?>
			</p>
			<p class="venuestack-auth-form__links is-style-body">
				<a href="<?php echo esc_url( $lost_url ); ?>"><?php echo esc_html__( 'Request a new reset link', 'venuestack' ); ?></a>
				<span aria-hidden="true"> · </span>
				<a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html__( 'Log in', 'venuestack' ); ?></a>
			</p>
		<?php else : ?>
			<?php if ( '' !== $error_code && ! in_array( $error_code, array( 'expired', 'missing' ), true ) ) : ?>
				<p class="venuestack-auth-form__error is-style-body" role="alert">
					<?php echo esc_html( venuestack_auth_error_message( $error_code, 'resetpassword' ) ); ?>
				</p>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( venuestack_get_auth_page_url( 'reset-password' ) ); ?>" class="venuestack-auth-form__fields">
				<?php wp_nonce_field( 'venuestack_resetpassword' ); ?>
				<input type="hidden" name="venuestack_auth_action" value="resetpassword" />
				<input type="hidden" name="rp_key" value="<?php echo esc_attr( $key ); ?>" />
				<input type="hidden" name="rp_login" value="<?php echo esc_attr( $login ); ?>" />

				<label class="venuestack-auth-form__field">
					<span class="is-style-label"><?php echo esc_html__( 'New password', 'venuestack' ); ?></span>
					<input type="password" name="pass1" required autocomplete="new-password" minlength="8" class="venuestack-auth-form__input" />
				</label>

				<label class="venuestack-auth-form__field">
					<span class="is-style-label"><?php echo esc_html__( 'Confirm new password', 'venuestack' ); ?></span>
					<input type="password" name="pass2" required autocomplete="new-password" minlength="8" class="venuestack-auth-form__input" />
				</label>

				<button type="submit" class="venuestack-auth-form__submit">
					<?php echo esc_html__( 'Save new password', 'venuestack' ); ?>
				</button>
			</form>

			<p class="venuestack-auth-form__links is-style-body">
				<a href="<?php echo esc_url( $login_url ); ?>"><?php echo esc_html__( 'Back to log in', 'venuestack' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Shortcodes so FSE templates can place the forms.
 *
 * @return string
 */
function venuestack_login_form_shortcode(): string
{
	ob_start();
	venuestack_render_login_form();
	return venuestack_minify_auth_markup((string) ob_get_clean());
}
add_shortcode('venuestack_login_form', 'venuestack_login_form_shortcode');

/**
 * @return string
 */
function venuestack_register_form_shortcode(): string
{
	ob_start();
	venuestack_render_register_form();
	return venuestack_minify_auth_markup((string) ob_get_clean());
}
add_shortcode('venuestack_register_form', 'venuestack_register_form_shortcode');

/**
 * @return string
 */
function venuestack_lostpassword_form_shortcode(): string {
	ob_start();
	venuestack_render_lostpassword_form();
	return venuestack_minify_auth_markup( (string) ob_get_clean() );
}
add_shortcode( 'venuestack_lostpassword_form', 'venuestack_lostpassword_form_shortcode' );

/**
 * @return string
 */
function venuestack_resetpassword_form_shortcode(): string {
	ob_start();
	venuestack_render_resetpassword_form();
	return venuestack_minify_auth_markup( (string) ob_get_clean() );
}
add_shortcode( 'venuestack_resetpassword_form', 'venuestack_resetpassword_form_shortcode' );

/**
 * Collapse whitespace so wpautop does not wrap form controls in <p>/<br>.
 *
 * @param string $html Form markup.
 */
function venuestack_minify_auth_markup(string $html): string
{
	$minified = preg_replace('/\R+/', '', $html);
	return is_string($minified) ? $minified : $html;
}
