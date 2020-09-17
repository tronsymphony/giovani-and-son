<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Range {
	/**
	 * @var float
	 */
	protected $from;

	/**
	 * @var float
	 */
	protected $to;

	/**
	 * @var mixed
	 */
	protected $data;

	/**
	 * @param float $from
	 * @param float $to
	 * @param mixed $range_data
	 */
	public function __construct( $from, $to, $range_data ) {
		$this->from = is_numeric( $from ) && $from >= 0 ? (float) $from : 0.0;
		$this->to   = is_numeric( $to ) ? (float) $to : INF;

		$this->data = $range_data;
	}

	public function isValid() {
		return $this->lteEnd( $this->from );
	}

	/**
	 * Less than finish value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function ltEnd( $value ) {
		return $value < $this->to;
	}

	/**
	 * Less than or equal finish value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function lteEnd( $value ) {
		return $value <= $this->to;
	}

	/**
	 * Equal finish value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function isEqualEnd( $value ) {
		return $value === $this->to;
	}

	/**
	 * Greater than finish value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function gtEnd( $value ) {
		return $this->to < $value;
	}

	/**
	 * Greater than or equal finish value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function gteEnd( $value ) {
		return $this->to <= $value;
	}

	/**
	 * Less than start value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function ltStart( $value ) {
		return $value < $this->from;
	}

	/**
	 * Less than or equal start value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function lteStart( $value ) {
		return $value <= $this->from;
	}

	/**
	 * Equal start value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function isEqualStart( $value ) {
		return $value === $this->from;
	}

	/**
	 * Greater than start value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function gtStart( $value ) {
		return $this->from < $value;
	}

	/**
	 * Greater than or equal start value of the interval
	 *
	 * @param integer|float $value
	 *
	 * @return bool
	 */
	private function gteStart( $value ) {
		return $this->from <= $value;
	}

	/**
	 * Is value in interval?
	 *
	 * @param float $value
	 *
	 * @return bool
	 */
	public function isIn( $value ) {
		return $this->from <= $value && $this->lteEnd( $value );
	}

	/**
	 * Is value greater than finish value of the interval?
	 *
	 * @param float $value
	 *
	 * @return bool
	 */
	public function isGreater( $value ) {
		return $this->gtEnd( $value );
	}

	/**
	 * Is value greater than finish value of the interval inclusively?
	 *
	 * @param float $value
	 *
	 * @return bool
	 */
	public function isGreaterInc( $value ) {
		return $this->gteEnd( $value );
	}

	/**
	 * Is value less than start value of the interval?
	 *
	 * @param float $value
	 *
	 * @return bool
	 */
	public function isLess( $value ) {
		return $this->ltStart( $value );
	}

	/**
	 * Is value less than start value of the interval inclusively?
	 *
	 * @param float $value
	 *
	 * @return bool
	 */
	public function isLessInc( $value ) {
		return $this->lteStart( $value );
	}

	public function getData() {
		return $this->data;
	}

	public function getFrom() {
		return $this->from;
	}

	public function getTo() {
		return $this->to;
	}

	public function getQty() {
		return $this->to - $this->from;
	}

	public function getQtyInc() {
		return $this->to - $this->from + 1;
	}
}
