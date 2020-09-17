<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class CartCustomer {
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var array
	 */
	protected $billingAddress;

	/**
	 * @var array
	 */
	protected $shippingAddress;

	/**
	 * @var string
	 */
	protected $selectedPaymentMethod;

	/**
	 * @var string[]
	 */
	protected $selectedShippingMethods;

	/**
	 * @var bool
	 */
	protected $isVatExempt;

	/**
	 * @var array
	 */
	protected $roles;

	/**
	 * @param int $id
	 */
	public function __construct( $id = null ) {
		$this->id = $id;

		$this->billingAddress          = array();
		$this->shippingAddress         = array();
		$this->selectedPaymentMethod   = null;
		$this->selectedShippingMethods = array();
		$this->isVatExempt             = false;
	}

	/**
	 * @param int $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	public function isGuest() {
		return $this->id === null;
	}

	/**
	 * @param array $billingAddress
	 */
	public function setBillingAddress( $billingAddress ) {
		$this->billingAddress = (array) $billingAddress;
	}

	/**
	 * @return array
	 */
	public function getBillingAddress() {
		return $this->billingAddress;
	}

	/**
	 * @param array $shippingAddress
	 */
	public function setShippingAddress( $shippingAddress ) {
		$this->shippingAddress = (array) $shippingAddress;
	}

	/**
	 * @return array
	 */
	public function getShippingAddress() {
		return $this->shippingAddress;
	}

	/**
	 * @param string[] $selectedShippingMethods
	 */
	public function setSelectedShippingMethods( $selectedShippingMethods ) {
		$this->selectedShippingMethods = $selectedShippingMethods;
	}

	/**
	 * @return string[]
	 */
	public function getSelectedShippingMethods() {
		return $this->selectedShippingMethods;
	}

	/**
	 * @param string $selectedPaymentMethod
	 */
	public function setSelectedPaymentMethod( $selectedPaymentMethod ) {
		$this->selectedPaymentMethod = $selectedPaymentMethod;
	}

	/**
	 * @return string
	 */
	public function getSelectedPaymentMethod() {
		return $this->selectedPaymentMethod;
	}

	/**
	 * @param array $roles
	 */
	public function setRoles( $roles ) {
		$this->roles = (array) $roles;
	}

	/**
	 * All non registered users have a dummy 'wdp_guest' role
	 *
	 * @return array
	 */
	public function getRoles() {
		return ! empty( $this->roles ) ? $this->roles : array( 'wdp_guest' );
	}

	/**
	 * @param bool $isVatExempt
	 */
	public function setIsVatExempt( $isVatExempt ) {
		$this->isVatExempt = boolval( $isVatExempt );
	}

	/**
	 * @return bool
	 */
	public function isVatExempt() {
		return $this->isVatExempt;
	}

	public function getShippingCountry() {
		return isset( $this->shippingAddress['country'] ) ? $this->shippingAddress['country'] : "";
	}

	public function getShippingState() {
		return isset( $this->shippingAddress['state'] ) ? $this->shippingAddress['state'] : "";
	}

	public function getShippingPostCode() {
		return isset( $this->shippingAddress['postcode'] ) ? $this->shippingAddress['postcode'] : "";
	}

	public function getShippingCity() {
		return isset( $this->shippingAddress['city'] ) ? $this->shippingAddress['city'] : "";
	}

	public function getBillingCountry() {
		return isset( $this->billingAddress['country'] ) ? $this->billingAddress['country'] : "";
	}

	public function getBillingState() {
		return isset( $this->billingAddress['state'] ) ? $this->billingAddress['state'] : "";
	}

	public function getBillingPostCode() {
		return isset( $this->billingAddress['postcode'] ) ? $this->billingAddress['postcode'] : "";
	}

	public function getBillingCity() {
		return isset( $this->billingAddress['city'] ) ? $this->billingAddress['city'] : "";
	}
}
