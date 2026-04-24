<?php
/**
 * Item class.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

use DateTimeImmutable;
use WP_Post;
use WP_User;
use WPCF7_ContactForm;

/**
 * Class Item.
 */
final class Item {
	/**
	 * Submission Item ID.
	 *
	 * @var int|null
	 */
	public readonly ?int $id;

	/**
	 * Submission Form ID.
	 *
	 * @var int|null
	 */
	public readonly ?int $form_id;

	/**
	 * Submission Author ID.
	 *
	 * @var int|null
	 */
	public readonly ?int $author_id;

	/**
	 * Submission Author Name.
	 *
	 * @var string|null
	 */
	public readonly ?string $author_name;

	/**
	 * Submission Author Email.
	 *
	 * @var string|null
	 */
	public readonly ?string $author_email;

	/**
	 * Submission Author Phone.
	 *
	 * @var string|null
	 */
	public readonly ?string $author_phone;

	/**
	 * Submission read status.
	 *
	 * @var 0|1
	 */
	public readonly int $read_status;

	/**
	 * Submission title.
	 *
	 * @var string|null
	 */
	public readonly ?string $title;

	/**
	 * Submission message.
	 *
	 * @var string|null
	 */
	public readonly ?string $message;

	/**
	 * Submission datetime.
	 *
	 * @var DateTimeImmutable|null
	 */
	public readonly ?DateTimeImmutable $datetime;

	/**
	 * Pair of form submission field & value.
	 *
	 * @var array<string, string>
	 */
	public readonly array $submission;

	/**
	 * Set the read status for a submission item.
	 *
	 * @param int|null $id   The item ID.
	 * @param bool     $read The read status.
	 * @return int|false
	 */
	public static function set_read_status( ?int $id, bool $read ): int|false {
		return \update_post_meta( $id, '_cf7em_read_status', $read ? 1 : 0 );
	}

	/**
	 * Store a submission for the given form and submission option.
	 *
	 * @param WPCF7_ContactForm $form   The contact form.
	 * @param Option            $option The submission option.
	 * @return int|WP_Error
	 */
	public static function store( WPCF7_ContactForm $form, Option $option ) {
		$form_data = $option->form_data();

		$returned_id = \wp_insert_post(
			array(
				'post_type'    => 'form-submissions',
				'post_status'  => 'publish',
				'post_title'   => $option->subject ?: sprintf(
					/* translators: %s: Contact form title */
					\esc_html__( 'Submission for "%s"', 'cf7-entry-manager' ),
					$form->title()
				),
				'post_parent'  => $form->id(),
				'post_author'  => self::store_author( $option ),
				'post_excerpt' => $option->message,
			// 'post_content' => null,
			),
			true
		);

		if ( ! \is_wp_error( $returned_id ) && $returned_id > 0 ) {
			foreach ( $form_data as $field => $value ) {
				\add_post_meta( $returned_id, $field, $value );
			}

			\add_post_meta( $returned_id, '_cf7em_read_status', 0 );
		}

		return $returned_id;
	}

	/**
	 * Store submission author.
	 *
	 * @param Option $option The submission option.
	 * @return int
	 */
	private static function store_author( Option $option ): int {
		$could_store = ( $option->email && \is_email( $option->email ) );

		if ( ! $option->store_author || ! $could_store ) {
			return 0;
		}

		if ( \email_exists( $option->email ) ) {
			$user = WP_User::get_data_by( 'email', $option->email );

			\update_user_meta( $user->ID, 'user_phone', $option->phone ?? '' );

			return (int) $user->ID;
		}

		list( $login ) = explode( '@', $option->email );

		$login = \sanitize_user( $login );

		if ( \username_exists( $login ) ) {
			$user = WP_User::get_data_by( 'login', $login );

			\update_user_meta( $user->ID, 'user_phone', $option->phone ?? '' );

			return (int) $user->ID;
		}

		$user_data = array(
			'user_login'   => $login,
			'user_email'   => $option->email,
			'display_name' => $option->name,
			'user_pass'    => \wp_generate_password( 12, true ),
			'role'         => 'subscriber',
		);

		$name_parts = explode( ' ', $option->name );

		if ( count( $name_parts ) > 1 ) {
			$user_data['first_name'] = $name_parts[0];
			$user_data['last_name']  = implode( ' ', array_slice( $name_parts, 1 ) );
		}

		$user_id = \wp_insert_user( $user_data );

		if ( ! $user_id || \is_wp_error( $user_id ) ) {
			return 0;
		}

		\update_user_meta( $user_id, 'user_phone', $option->phone ?? '' );

		return (int) $user_id;
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Post|int $item The item ID or post object.
	 */
	public function __construct( WP_Post|int $item ) {
		if ( is_int( $item ) ) {
			$item = \get_post( $item );
		}

		$this->id          = $item?->ID;
		$this->title       = $item?->post_title;
		$this->form_id     = $item?->post_parent;
		$this->author_id   = $item?->post_author;
		$this->message     = $item?->post_excerpt;
		$this->datetime    = \get_post_datetime( $item?->ID ?? null ) ?: null;
		$this->read_status = (int) \get_post_meta( $this->id, '_cf7em_read_status', true );

		$author = $this->author();

		$this->author_name  = empty( $author?->display_name ) ? null : $author?->display_name;
		$this->author_email = empty( $author?->user_email ) ? null : $author?->user_email;
		$this->author_phone = empty( $author?->user_phone ) ? null : $author?->user_phone;

		$submission = array();

		foreach ( \get_post_meta( $item?->ID ) as $field => $value ) {
			if ( str_starts_with( $field, '_' ) ) {
				continue;
			}

			$submission[ $field ] = is_array( $value ) ? reset( $value ) : $value;
		}

		$this->submission = $submission;
	}

	/**
	 * Get the form post for this submission item.
	 */
	public function form(): ?WPCF7_ContactForm {
		return $this->form_id ? WPCF7_ContactForm::get_instance( $this->form_id ) : null;
	}

	/**
	 * Get the author for this submission item.
	 */
	public function author(): ?WP_User {
		if ( ! $this->author_id ) {
			return null;
		}

		return \get_userdata( $this->author_id ) ?: null;
	}

	/**
	 * Mark this submission item as read.
	 */
	public function mark_read() {
		return self::set_read_status( $this->id, true );
	}

	/**
	 * Mark this submission item as unread.
	 */
	public function mark_unread() {
		return self::set_read_status( $this->id, false );
	}

	/**
	 * Check if this submission item is read.
	 */
	public function is_read(): bool {
		return 1 === $this->read_status;
	}

	/**
	 * Check if this submission item is unread.
	 */
	public function is_unread(): bool {
		return 0 === $this->read_status;
	}

	/**
	 * Get the item URL.
	 *
	 * @param 'view'|'read' $action    The action.
	 * @param string|null   $nonce_key The nonce key.
	 * @return string
	 */
	public function url( string $action = 'view', ?string $nonce_key = null ) {
		$link = admin_menu_url(
			array(
				'post'   => $this->id,
				'action' => $action,
			)
		);

		if ( $nonce_key ) {
			return \wp_nonce_url( $link, $nonce_key . $this->id );
		}

		return \esc_url( $link );
	}

	/**
	 * Helper method that return current user capability for this submission item.
	 *
	 * @see \current_user_can()
	 *
	 * @param string $capability The capability.
	 * @return bool
	 */
	public function current_user_can( string $capability ): bool {
		return \current_user_can( $capability, $this->id );
	}
}
