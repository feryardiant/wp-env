<?php
/**
 * Admin functions.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

defined( 'ABSPATH' ) || exit;

use WPCF7_ContactForm;

/**
 * Register the submissions admin menu.
 */
\add_action(
	'admin_menu',
	static function (): void {
		$post_type_object = \get_post_type_object( 'form-submissions' );

		$submissions = \add_submenu_page(
			'wpcf7',
			$post_type_object->labels->items_list,
			$post_type_object->labels->menu_name,
			'wpcf7_read_contact_forms',
			'cf7-entry-manager',
			__NAMESPACE__ . '\admin_management_page',
			2,
		);

		\add_action(
			'load-' . $submissions,
			__NAMESPACE__ . '\admin_load_page',
			10,
			0
		);
	},
	9,
	0
);

/**
 * Capture the contact form submission and store it to database before sending it.
 */
\add_action(
	'wpcf7_before_send_mail',
	static function ( WPCF7_ContactForm $contact_form ): void {
		$option = Option::get( $contact_form );

		if ( ! $option ) {
			return;
		}

		$form_data = $option->form_data();

		\do_action( 'cf7em_before_save', $form_data );

		$returned_id = Item::store( $contact_form, $option );

		\do_action( 'cf7em_after_save', $form_data, $returned_id );
	},
	10,
	1
);

/**
 * Prepare to store option properties values.
 */
\add_action(
	'wpcf7_save_contact_form',
	static function ( WPCF7_ContactForm $contact_form, array $data ): void {
		$submissions = \wp_parse_args( $data['cf7-entry-manager'], array() );

		$contact_form->set_properties( array( 'submissions' => $submissions ) );
	},
	10,
	2
);

/**
 * Register new contact form option properties.
 */
\add_filter(
	'wpcf7_pre_construct_contact_form_properties',
	static fn ( array $properties ) => array_merge(
		$properties,
		array( 'submissions' => array() )
	),
	10,
	1
);

/**
 * Add a submissions panel to the contact form editor.
 */
\add_filter(
	'wpcf7_editor_panels',
	static function ( array $panels ): array {
		$post_type_object = \get_post_type_object( 'form-submissions' );

		$panels['submissions'] = array(
			'title'    => $post_type_object->label,
			'callback' => __NAMESPACE__ . '\admin_editor_panel',
		);

		return $panels;
	},
	10,
	1
);

/**
 * Load the submissions admin page.
 *
 * @internal
 */
function admin_load_page(): void {
	$action = \wpcf7_superglobal_request( 'action', null );

	\do_action(
		'cf7em_admin_page_load',
		\wpcf7_superglobal_get( 'page' ),
		$action
	);

	if ( 'read' === $action ) {
		$id = (int) \wpcf7_superglobal_get( 'post' );

		\check_admin_referer( 'cf7em-entry_' . $id );

		$query = array();

		if ( Item::set_read_status( $id, true ) ) {
			$query['post']    = $id;
			$query['message'] = 'marked-read';
		}

		\wp_safe_redirect( admin_menu_url( $query ) );

		exit();
	}

	$screen = \get_current_screen();

	\add_filter(
		'manage_' . $screen->id . '_columns',
		array( List_Table::class, 'define_column' ),
		10,
		1
	);
}

/**
 * Render the submissions panel for the contact form editor.
 *
 * @param WPCF7_ContactForm $contact_form The contact form.
 * @internal
 */
function admin_editor_panel( WPCF7_ContactForm $contact_form ): void {
	$post_type_object = \get_post_type_object( 'form-submissions' );

	$elm = new Page_Element(
		array(
			'allowed_html' => array(
				'form' => array( 'method' => true ),
			),
		)
	);

	$elm->h2( array(), \esc_html( $post_type_object->label ) );

	$elm->fieldset(
		array( 'class' => 'cf7em-option' ),
		static fn ( $elm ) => $elm
		->legend(
			array(),
			\__(
				'You can edit the way you treat each submissions here.',
				'cf7-entry-manager'
			)
		)

		->table(
			array( 'class' => 'form-table' ),
			static fn ( $elm ) => $elm
			->tbody(
				child: static function ( $elm ) use ( $contact_form ) {
					$option   = new Option( $contact_form );
					$panel_id = 'cf7-entry-manager';

					foreach ( $option->fields() as $id => $field ) {
						$field = \wp_parse_args(
							$field,
							array(
								'label'       => '',
								'description' => '',
								'type'        => 'input',
								'atts'        => array(),
								'options'     => array(),
							)
						);

						if ( 'separator' === $field['type'] ) {
							$elm->tr(
								child: static fn ( $elm ) => $elm
								->td(
									array(
										'colspan' => '2',
										'style'   => 'padding: 0;',
									),
									static fn ( $elm ) => $elm->hr()
								)
							);

							continue;
						}

						$field_id = sprintf( '%s-%s', $panel_id, $id );

						$elm->tr(
							child: static fn ( $elm ) => $elm
							->th(
								array( 'scope' => 'row' ),
								static fn ( $elm ) => $elm
									->label( array( 'for' => $field_id ), esc_html( $field['label'] ) )
							)

							->td(
								child: static function ( $elm ) use ( $option, $id, $panel_id, $field, $field_id ) {
									$field_atts = \wp_parse_args(
										$field['atts'],
										array(
											'id'    => $field_id,
											'name'  => sprintf( '%s[%s]', $panel_id, $id ),
											'value' => $option[ $id ],
										)
									);

									$is_select   = 'select' === $field['type'];
									$is_checkbox = 'input' === $field['type'] && 'checkbox' === $field_atts['type'];

									$selected = null;

									if ( $is_select ) {
										$selected = $field_atts['value'];
										unset( $field_atts['value'] );
									}

									if ( $is_checkbox ) {
										$field_atts['value']   = 'on';
										$field_atts['checked'] = $option[ $id ];
									}

									match ( $field['type'] ) {
										'select' => $elm->select(
											$field_atts,
											static function ( $elm ) use ( $field, $selected ) {
												$elm->option(
													array(
														'selected' => empty( $selected ),
														'value'    => '',
													),
													\__( 'None selected', 'cf7-entry-manager' )
												);

												foreach ( $field['options'] as $value => $label ) {
													$value = is_int( $value ) ? $label : $value;

													$elm->option(
														array(
															'value' => \esc_attr( $value ),
															'selected' => $selected === $value,
														), \esc_html( $label )
													);
												}
											}
										),

										default => $elm->input( $field_atts ),
									};

									if ( empty( $field['description'] ) ) {
										return;
									}

									if ( $is_checkbox ) {
										$elm->span( array(), \esc_html( $field['description'] ) );
									} else {
										$elm->p( array( 'class' => 'description' ), \esc_html( $field['description'] ) );
									}
								}
							)
						);
					}
				}
			)
		)
	);

	$elm->render();
}

/**
 * Render the submissions admin management page.
 *
 * @internal
 */
function admin_management_page(): void {
	$action = \wpcf7_superglobal_request( 'action', null );
	$item   = \wpcf7_superglobal_request( 'post', null );

	if ( 'view' === $action && $item ) {
		$item = new Item( $item );
		$elm  = new Page_Element(
			array(
				'allowed_html' => array(
					'form' => array(
						'method'   => true,
						'action'   => true,
						'id'       => true,
						'class'    => true,
						'disabled' => true,
					),
				),
			)
		);

		$item->mark_read();

		require_once __DIR__ . '/view-entry.php';

		return;
	}

	$list_table       = new List_Table();
	$post_type_object = \get_post_type_object( 'form-submissions' );

	$list_table->prepare_items();

	$elm = new Page_Element(
		array(
			'allowed_html' => array(
				'form' => array( 'method' => true ),
			),
		)
	);

	$elm->div(
		array( 'class' => 'wrap' ),
		static fn ( $elm ) => $elm
		->h1(
			array( 'class' => 'wp-heading-inline' ),
			\esc_html( $post_type_object->labels->items_list )
		)

		->hr( array( 'class' => 'wp-header-end' ) )

		->form(
			array( 'method' => 'get' ),
			static fn ( $elm ) => $elm
			->input(
				array(
					'type'  => 'hidden',
					'name'  => 'page',
					'value' => 'cf7-entry-manager',
				)
			)

			->call(
				static function () use ( $list_table, $post_type_object ) {
					$list_table->search_box(
						$post_type_object->labels->search_items,
						'cf7-entry-manager'
					);

					$list_table->display();
				}
			)
		)
	);

	$elm->render();
}

/**
 * Generate the admin URL for the submissions page.
 *
 * @param array $query The query arguments to add to the URL.
 */
function admin_menu_url( array $query ): string {
	return \add_query_arg(
		$query,
		\menu_page_url( 'cf7-entry-manager', false )
	);
}
