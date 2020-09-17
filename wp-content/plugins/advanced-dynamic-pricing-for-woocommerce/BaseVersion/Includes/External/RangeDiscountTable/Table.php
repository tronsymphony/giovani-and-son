<?php

namespace ADP\BaseVersion\Includes\External\RangeDiscountTable;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Frontend;

class Table {
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var string
	 */
	protected $tableHeader;

	/**
	 * @var array
	 */
	protected $columns;

	/**
	 * @var array[]
	 */
	protected $rows;

	/**
	 * @var string
	 */
	protected $tableFooter;

	/**
	 * @param Context $context
	 */
	public function __construct( $context ) {
		$this->context = $context;

		$this->tableHeader = '';
		$this->columns     = array();
		$this->rows        = array();
		$this->tableFooter = '';
	}

	public function getHtml() {
		// fill not existing and remove redundant cells
		foreach ( $this->rows as $index => $row ) {
			$filteredRow = array();
			foreach ( array_keys( $this->columns ) as $key ) {
				$filteredRow[ $key ] = isset( $row[ $key ] ) ? $row[ $key ] : '';
			}

			if ( count( array_filter( $filteredRow ) ) > 0 ) {
				$this->rows[ $index ] = $filteredRow;
			} else {
				unset( $this->rows[ $index ] );
			}
		}
		$this->rows = array_values( $this->rows );

		$args = array(
			'header_html'  => $this->tableHeader,
			'table_header' => $this->columns,
			'rows'         => $this->rows,
			'footer_html'  => $this->tableFooter,
		);

		ob_start();
		echo Frontend::wdp_get_template( "bulk-table.php", $args );
		return ob_get_clean();
	}

	public function setTableHeader( $text ) {
		if ( is_string( $text ) ) {
			$this->tableHeader = $text;
		}

		return $this;
	}

	public function addColumn( $key, $title ) {
		$this->columns[ $key ] = $title;

		return $this;
	}

	/**
	 * @param array $row
	 */
	public function addRow( $row ) {
		$this->rows[] = $row;
	}

	public function setTableFooter( $text ) {
		if ( is_string( $text ) ) {
			$this->tableFooter = $text;
		}


		return $this;
	}
}