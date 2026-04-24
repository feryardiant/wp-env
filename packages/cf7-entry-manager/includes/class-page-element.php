<?php
/**
 * Page element class.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

use Closure;
use WPCF7_HTMLFormatter;

/**
 * Page_Element class.
 *
 * This class provides a fluent interface for generating HTML elements using WPCF7_HTMLFormatter.
 *
 * // Grouping & Text
 *
 * @method self div(array $atts = [], Closure(self)|string $child = null)
 * @method self p(array $atts = [], Closure(self)|string $child = null)
 * @method self span(array $atts = [], Closure(self)|string $child = null)
 * @method self br(array $atts = [])
 * @method self wbr(array $atts = [])
 * @method self hr(array $atts = [])
 *
 * // Sectioning
 * @method self article(array $atts = [], Closure(self)|string $child = null)
 * @method self section(array $atts = [], Closure(self)|string $child = null)
 * @method self nav(array $atts = [], Closure(self)|string $child = null)
 * @method self aside(array $atts = [], Closure(self)|string $child = null)
 * @method self header(array $atts = [], Closure(self)|string $child = null)
 * @method self footer(array $atts = [], Closure(self)|string $child = null)
 * @method self main(array $atts = [], Closure(self)|string $child = null)
 * @method self address(array $atts = [], Closure(self)|string $child = null)
 * @method self h1(array $atts = [], Closure(self)|string $child = null)
 * @method self h2(array $atts = [], Closure(self)|string $child = null)
 * @method self h3(array $atts = [], Closure(self)|string $child = null)
 * @method self h4(array $atts = [], Closure(self)|string $child = null)
 * @method self h5(array $atts = [], Closure(self)|string $child = null)
 * @method self h6(array $atts = [], Closure(self)|string $child = null)
 * @method self hgroup(array $atts = [], Closure(self)|string $child = null)
 *
 * // Lists
 * @method self ul(array $atts = [], Closure(self)|string $child = null)
 * @method self ol(array $atts = [], Closure(self)|string $child = null)
 * @method self menu(array $atts = [], Closure(self)|string $child = null)
 * @method self li(array $atts = [], Closure(self)|string $child = null)
 * @method self dl(array $atts = [], Closure(self)|string $child = null)
 * @method self dt(array $atts = [], Closure(self)|string $child = null)
 * @method self dd(array $atts = [], Closure(self)|string $child = null)
 *
 * // Tables
 * @method self table(array $atts = [], Closure(self)|string $child = null)
 * @method self caption(array $atts = [], Closure(self)|string $child = null)
 * @method self colgroup(array $atts = [], Closure(self)|string $child = null)
 * @method self col(array $atts = [])
 * @method self thead(array $atts = [], Closure(self)|string $child = null)
 * @method self tbody(array $atts = [], Closure(self)|string $child = null)
 * @method self tfoot(array $atts = [], Closure(self)|string $child = null)
 * @method self tr(array $atts = [], Closure(self)|string $child = null)
 * @method self th(array $atts = [], Closure(self)|string $child = null)
 * @method self td(array $atts = [], Closure(self)|string $child = null)
 *
 * // Forms
 * @method self form(array $atts = [], Closure(self)|string $child = null)
 * @method self fieldset(array $atts = [], Closure(self)|string $child = null)
 * @method self legend(array $atts = [], Closure(self)|string $child = null)
 * @method self label(array $atts = [], Closure(self)|string $child = null)
 * @method self input(array $atts = [])
 * @method self button(array $atts = [], Closure(self)|string $child = null)
 * @method self select(array $atts = [], Closure(self)|string $child = null)
 * @method self optgroup(array $atts = [], Closure(self)|string $child = null)
 * @method self option(array $atts = [], Closure(self)|string $child = null)
 * @method self textarea(array $atts = [], Closure(self)|string $child = null)
 * @method self datalist(array $atts = [], Closure(self)|string $child = null)
 * @method self output(array $atts = [], Closure(self)|string $child = null)
 * @method self progress(array $atts = [], Closure(self)|string $child = null)
 * @method self meter(array $atts = [], Closure(self)|string $child = null)
 *
 * // Inline Formatting
 * @method self a(array $atts = [], Closure(self)|string $child = null)
 * @method self strong(array $atts = [], Closure(self)|string $child = null)
 * @method self b(array $atts = [], Closure(self)|string $child = null)
 * @method self em(array $atts = [], Closure(self)|string $child = null)
 * @method self i(array $atts = [], Closure(self)|string $child = null)
 * @method self u(array $atts = [], Closure(self)|string $child = null)
 * @method self s(array $atts = [], Closure(self)|string $child = null)
 * @method self small(array $atts = [], Closure(self)|string $child = null)
 * @method self mark(array $atts = [], Closure(self)|string $child = null)
 * @method self sub(array $atts = [], Closure(self)|string $child = null)
 * @method self sup(array $atts = [], Closure(self)|string $child = null)
 * @method self abbr(array $atts = [], Closure(self)|string $child = null)
 * @method self dfn(array $atts = [], Closure(self)|string $child = null)
 * @method self cite(array $atts = [], Closure(self)|string $child = null)
 * @method self q(array $atts = [], Closure(self)|string $child = null)
 * @method self ruby(array $atts = [], Closure(self)|string $child = null)
 * @method self rt(array $atts = [], Closure(self)|string $child = null)
 * @method self rp(array $atts = [], Closure(self)|string $child = null)
 *
 * // Inline Tech & Data
 * @method self data(array $atts = [], Closure(self)|string $child = null)
 * @method self time(array $atts = [], Closure(self)|string $child = null)
 * @method self code(array $atts = [], Closure(self)|string $child = null)
 * @method self kbd(array $atts = [], Closure(self)|string $child = null)
 * @method self samp(array $atts = [], Closure(self)|string $child = null)
 * @method self var(array $atts = [], Closure(self)|string $child = null)
 * @method self bdi(array $atts = [], Closure(self)|string $child = null)
 * @method self bdo(array $atts = [], Closure(self)|string $child = null)
 * @method self ins(array $atts = [], Closure(self)|string $child = null)
 * @method self del(array $atts = [], Closure(self)|string $child = null)
 *
 * // Figures & Interactive
 * @method self figure(array $atts = [], Closure(self)|string $child = null)
 * @method self figcaption(array $atts = [], Closure(self)|string $child = null)
 * @method self details(array $atts = [], Closure(self)|string $child = null)
 * @method self summary(array $atts = [], Closure(self)|string $child = null)
 * @method self dialog(array $atts = [], Closure(self)|string $child = null)
 *
 * // Media & Embedded
 * @method self img(array $atts = [])
 * @method self picture(array $atts = [], Closure(self)|string $child = null)
 * @method self video(array $atts = [], Closure(self)|string $child = null)
 * @method self audio(array $atts = [], Closure(self)|string $child = null)
 * @method self source(array $atts = [])
 * @method self track(array $atts = [])
 * @method self iframe(array $atts = [], Closure(self)|string $child = null)
 * @method self canvas(array $atts = [], Closure(self)|string $child = null)
 * @method self map(array $atts = [], Closure(self)|string $child = null)
 * @method self area(array $atts = [])
 * @method self object(array $atts = [], Closure(self)|string $child = null)
 * @method self param(array $atts = [])
 * @method self embed(array $atts = [])
 *
 * // Miscellaneous
 * @method self pre(array $atts = [], Closure(self)|string $child = null)
 * @method self blockquote(array $atts = [], Closure(self)|string $child = null)
 * @method self noscript(array $atts = [], Closure(self)|string $child = null)
 * @method self template(array $atts = [], Closure(self)|string $child = null)
 * @method self slot(array $atts = [], Closure(self)|string $child = null)
 * @method self base(array $atts = [])
 */
final class Page_Element {
	/**
	 * Formatter instance.
	 *
	 * @var WPCF7_HTMLFormatter
	 */
	private WPCF7_HTMLFormatter $formatter;

	/**
	 * Known elements.
	 *
	 * @var array
	 */
	private array $known_elements = array();

	/**
	 * Ignored elements.
	 *
	 * @var array
	 */
	private array $ignored_elements = array(
		WPCF7_HTMLFormatter::placeholder_block,
		WPCF7_HTMLFormatter::placeholder_inline,
		'html',
		'head',
		'title',
		'link',
		'meta',
		'body',
		'script',
		'style',
		'keygen',
	);

	/**
	 * Within element flag.
	 *
	 * @var bool
	 */
	private bool $within_element = false;

	/**
	 * Constructor.
	 *
	 * @param array $option The options.
	 */
	public function __construct( array $option ) {
		if ( array_key_exists( 'allowed_html', $option ) ) {
			$option['allowed_html'] = array_merge( \wpcf7_kses_allowed_html(), $option['allowed_html'] );
		}

		$this->formatter = new WPCF7_HTMLFormatter( $option );

		$known_elements = array_filter(
			array_merge(
				WPCF7_HTMLFormatter::void_elements,
				WPCF7_HTMLFormatter::p_parent_elements,
				WPCF7_HTMLFormatter::p_nonparent_elements,
				WPCF7_HTMLFormatter::p_child_elements,
				WPCF7_HTMLFormatter::br_parent_elements,
			),
			fn( string $elm ) => ! in_array( $elm, $this->ignored_elements, true )
		);

		$this->known_elements = array_unique( $known_elements );
	}

	/**
	 * Magic method __call.
	 *
	 * @param string $method The method name.
	 * @param array  $args   The arguments.
	 * @return self
	 * @throws \BadMethodCallException If method is undefined.
	 * @throws \TypeError              If arguments are invalid.
	 */
	public function __call( string $method, array $args = array() ): self {
		if ( ! in_array( $method, $this->known_elements, true ) ) {
			throw new \BadMethodCallException(
				sprintf(
					'Call to undefined method: %s::%s()',
					__CLASS__,
					\esc_attr( $method )
				)
			);
		}

		$atts = $args[0] ?? $args['atts'] ?? array();

		if ( ! is_array( $atts ) ) {
			throw new \TypeError(
				sprintf(
					'%s::%s(): Argument #1 ($atts) must be of type array, %s given',
					__CLASS__,
					\esc_attr( $method ),
					\esc_attr( gettype( $atts ) )
				)
			);
		}

		$this->formatter->append_start_tag( $method, $atts );

		if ( in_array( $method, WPCF7_HTMLFormatter::void_elements, true ) ) {
			return $this;
		}

		$child = $args[1] ?? $args['child'] ?? null;

		if ( null !== $child ) {
			if ( is_string( $child ) ) {
				$this->formatter->append_preformatted( $child );
			} elseif ( $child instanceof Closure ) {
				$child_callback = new \ReflectionFunction( $child );

				$this->within_element = true;

				$return = $child_callback->invoke( $this );

				if ( is_string( $return ) ) {
					$this->formatter->append_preformatted( $child );
				}

				$this->within_element = false;
			} else {
				throw new \TypeError(
					sprintf(
						'%s::%s(): Argument #2 ($child) must be of type Closure|string, %s given',
						__CLASS__,
						\esc_attr( $method ),
						\esc_attr( gettype( $child ) )
					)
				);
			}
		}

		$this->formatter->end_tag( $method );

		$comment = implode(
			'',
			array_filter(
				array(
					( ! empty( $atts['id'] ?? null ) ? '#' . $atts['id'] : null ),
					( ! empty( $atts['class'] ?? null ) ? '.' . explode( ' ', $atts['class'] )[0] : null ),
				)
			)
		);

		if ( ! empty( $comment ) ) {
			$this->formatter->append_comment( "<!-- /{$comment} -->" );
		}

		return $this;
	}

	/**
	 * Append whitespace.
	 *
	 * @return self
	 */
	public function whitespace(): self {
		$this->formatter->append_preformatted( ' ' );

		return $this;
	}

	/**
	 * Clear float.
	 *
	 * @param 'br'|'div'|'span' $mode The element mode.
	 * @throws \InvalidArgumentException If mode is invalid.
	 * @return self
	 */
	public function clear( string $mode = 'br' ): self {
		if ( ! in_array( $mode, array( 'br', 'div', 'span' ), true ) ) {
			throw new \InvalidArgumentException(
				sprintf(
					'%s::clear(): Argument #1 ($mode) must be one of "br", "div", or "span", %s given',
					__CLASS__,
					\esc_html( $mode )
				)
			);
		}

		$this->formatter->append_start_tag( $mode, array( 'class' => 'clear' ) );

		if ( 'br' !== $mode ) {
			$this->formatter->append_end_tag( $mode );
		}

		return $this;
	}

	/**
	 * Call a user function.
	 *
	 * @param Closure $callback The callback.
	 * @param mixed   ...$params The parameters.
	 * @return self
	 */
	public function call( Closure $callback, mixed ...$params ): self {
		$this->formatter->call_user_func( $callback, ...$params );

		return $this;
	}

	/**
	 * Call a user function when condition is met.
	 *
	 * @param bool|Closure $condition The condition.
	 * @param Closure      $met       The met callback.
	 * @param mixed        ...$params The parameters.
	 * @return self
	 */
	public function call_when( bool|Closure $condition, Closure $met, mixed ...$params ): self {
		if ( $condition instanceof Closure ) {
			$condition = (bool) call_user_func( $condition );
		}

		return $condition
			? $this->call( $met, ...$params )
			: $this;
	}

	/**
	 * Execute callback when condition is met.
	 *
	 * @param bool|Closure       $condition The condition.
	 * @param Closure(self)      $met       The met callback.
	 * @param Closure(self)|null $unmet     The unmet callback.
	 * @return self|void
	 */
	public function when( bool|Closure $condition, Closure $met, ?Closure $unmet = null ): self {
		if ( $condition instanceof Closure ) {
			$condition = (bool) call_user_func( $condition );
		}

		if ( $condition ) {
			$met( $this );
		} else {
			$unmet && $unmet( $this );
		}

		return $this;
	}

	/**
	 * Dump parameters.
	 *
	 * @param mixed ...$params The parameters.
	 * @return self
	 * @internal
	 */
	public function dump( mixed ...$params ): self {
		if ( ! CF7EM_DEBUG ) {
			return $this; // No-op in production.
		}

		$atts = array( 'class' => 'cf7em-debug' );

		return $this->div(
			$atts,
			static fn ( $elm ) => $elm
			->pre(
				child: static fn ( $elm ) => $elm
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
				->call( static fn () => var_dump( ...$params ) )
			)
		);
	}

	/**
	 * Render the output.
	 *
	 * @return void
	 * @throws \LogicException If called within an element.
	 */
	public function render(): void {
		if ( $this->within_element ) {
			throw new \LogicException( 'Cannot render within an element' );
		}

		$this->formatter->print();
	}
}
