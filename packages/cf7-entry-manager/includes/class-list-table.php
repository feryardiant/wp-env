<?php
/**
 * List table class.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

use WP_List_Table;
use WP_Query;
use WPCF7_ContactForm;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class List_Table.
 */
class List_Table extends WP_List_Table {
	/**
	 * Define the columns for the submissions list table.
	 *
	 * @param array $columns The columns array.
	 * @return array
	 */
	public static function define_column( array $columns ) {
		return \wp_parse_args(
			$columns,
			array(
				'cb'     => '<input type="checkbox" />',
				'title'  => __( 'Subject', 'cf7-entry-manager' ),
				'form'   => __( 'Form', 'cf7-entry-manager' ),
				'author' => __( 'Author', 'cf7-entry-manager' ),
				'date'   => __( 'Date', 'cf7-entry-manager' ),
			)
		);
	}

	/**
	 * Constructor.
	 *
	 * @param WPCF7_ContactForm|null $contact_form The contact form.
	 */
	public function __construct(
		private ?WPCF7_ContactForm $contact_form = null,
	) {
		parent::__construct(
			array(
				'singular' => 'post',
				'plural'   => 'posts',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare the items for the submissions list table.
	 */
	public function prepare_items() {
		$per_page = max( 1, (int) $this->get_items_per_page( 'cf7em_submissions_per_page' ) );

		$args = array(
			'post_type'      => 'form-submissions',
			'post_parent'    => $this->contact_form?->id(),
			'posts_per_page' => $per_page,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'offset'         => ( $this->get_pagenum() - 1 ) * $per_page,
		);

		if ( $search_keyword = \wpcf7_superglobal_request( 's' ) ) {
			$args['s'] = $search_keyword;
		}

		$sortable = array_keys( $this->get_sortable_columns() );
		$order_by = \wpcf7_superglobal_request( 'orderby' );

		if ( $order_by && in_array( $order_by, $sortable, true ) ) {
			$args['orderby'] = $order_by;
		}

		$order = strtoupper( \wpcf7_superglobal_request( 'order' ) );

		if ( $order && in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$args['order'] = $order;
		}

		$q = new WP_Query();

		foreach ( $q->query( $args ) as &$item ) {
			$this->items[] = new Item( $item );
		}

		$total_items = $q->found_posts;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => (int) ceil( $total_items / $per_page ),
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_sortable_columns() {
		$columns = array(
			'title'  => array( 'title', true ),
			'author' => array( 'author', false ),
			'date'   => array( 'date', false ),
		);

		return $columns;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		return \get_column_headers( \get_current_screen() );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Item   $item        The item object.
	 * @param string $column_name The column name.
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		return '';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Item   $item        The item object.
	 * @param string $column_name The column name.
	 * @param string $primary     The primary column name.
	 * @return string
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $column_name !== $primary ) {
			return '';
		}

		$actions = array(
			'view' => sprintf(
				'<a href="%1$s" aria-label="%2$s">%3$s</a>',
				$item->url(),
				sprintf(
					/* translators: %s: title of contact form */
					\esc_attr__( 'View "%s"', 'cf7-entry-manager' ),
					$item->title
				),
				\__( 'View', 'cf7-entry-manager' ),
			),
		);

		if ( $item->is_unread() ) {
			$actions['read'] = sprintf(
				'<a href="%1$s" aria-label="%2$s">%3$s</a>',
				$item->url( 'read', 'cf7em-entry_' ),
				sprintf(
					/* translators: %s: title of contact form */
					\esc_attr__( 'Mark "%s" as read', 'cf7-entry-manager' ),
					$item->title,
				),
				\__( 'Mark as read', 'cf7-entry-manager' ),
			);
		}

		return $this->row_actions( $actions );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Item $item The item object.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id
		);
	}

	/**
	 * Configure the title column.
	 *
	 * @param Item $item The item object.
	 * @return string
	 */
	public function column_title( Item $item ): string {
		$output = sprintf(
			'<a class="%4$s" href="%1$s" aria-label="%2$s">%3$s</a>',
			$item->url(),
			sprintf(
				/* translators: %s: title of submission */
				\esc_attr__( 'View &#8220;%s&#8221;', 'cf7-entry-manager' ),
				$item->title
			),
			\esc_html( $item->title ),
			$item->is_unread() ? 'row-title' : ''
		);

		return $output;
	}

	/**
	 * Configure the author column.
	 *
	 * @param Item $item The item object.
	 * @return string
	 */
	public function column_author( Item $item ): string {
		if ( $author = $item->author() ) {
			return \esc_html( $author->display_name );
		}

		return sprintf(
			'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
			\__( 'no author', 'cf7-entry-manager' )
		);
	}

	/**
	 * Configure the form column.
	 *
	 * @param Item $item The item object.
	 * @return string
	 */
	public function column_form( Item $item ): string {
		if ( $form = $item->form() ) {
			return \esc_html( $form->title() );
		}

		return sprintf(
			'<span aria-hidden="true">—</span><span class="screen-reader-text">(%s)</span>',
			\__( 'no form', 'cf7-entry-manager' )
		);
	}

	/**
	 * Configure the date column.
	 *
	 * @param Item $item The item object.
	 * @return string
	 */
	public function column_date( Item $item ): string {
		if ( ! $item->datetime ) {
			return '';
		}

		return sprintf(
			/* translators: 1: date, 2: time */
			\__( '%1$s at %2$s', 'cf7-entry-manager' ),
			/* translators: date format, see https://www.php.net/date */
			$item->datetime->format( \__( 'Y/m/d', 'cf7-entry-manager' ) ),
			/* translators: time format, see https://www.php.net/date */
			$item->datetime->format( \__( 'g:i a', 'cf7-entry-manager' ) )
		);
	}
}
