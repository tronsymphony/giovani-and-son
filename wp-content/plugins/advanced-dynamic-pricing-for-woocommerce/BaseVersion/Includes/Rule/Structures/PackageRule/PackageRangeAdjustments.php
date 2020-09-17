<?php

namespace ADP\BaseVersion\Includes\Rule\Structures\PackageRule;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Structures\RangeDiscount;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PackageRangeAdjustments {
	const TYPE_BULK = 'bulk';
	const TYPE_TIER = 'tier';

	const AVAILABLE_TYPES = array(
		self::TYPE_BULK,
		self::TYPE_TIER,
	);

	/**
	 * @var string
	 */
	protected $type;

	const GROUP_BY_DEFAULT = 'not';
	const GROUP_BY_PRODUCT = 'product';
	const GROUP_BY_VARIATION = 'variation';
	const GROUP_BY_CART_POSITIONS = 'cart_pos';
	const GROUP_BY_SETS = 'sets';

	const BULK_AVAILABLE_GROUP_BY = array(
		self::GROUP_BY_PRODUCT,
		self::GROUP_BY_VARIATION,
		self::GROUP_BY_CART_POSITIONS,
		self::GROUP_BY_SETS,
	);

	// degenerated aggregations
	const GROUP_BY_ALL_ITEMS_IN_CART = 'total_qty_in_cart';
	const GROUP_BY_PRODUCT_CATEGORIES = 'product_categories';
	const GROUP_BY_PRODUCT_SELECTED_CATEGORIES = 'product_selected_categories';
	const GROUP_BY_PRODUCT_SELECTED_PRODUCTS = 'selected_products';

	const TIER_AVAILABLE_GROUP_BY = array(
		self::GROUP_BY_SETS,
	);

	/**
	 * @var string
	 */
	protected $groupBy;

	/**
	 * Coupon or Fee
	 *
	 * @var bool
	 */
	protected $replaceAsCartAdjustment;

	/**
	 * @var RangeDiscount[]
	 */
	protected $ranges;

	/**
	 * @var string
	 */
	protected $replaceCartAdjustmentCode;

	/**
	 * @var string
	 */
	protected $promotionalMessage;

	/**
	 * @var int[]
	 */
	protected $selectedProductIds;

	/**
	 * @var int[]
	 */
	protected $selectedCategoryIds;

	/**
	 * @param Context $context
	 * @param string  $type
	 * @param string  $groupBy
	 */
	public function __construct( $context, $type, $groupBy ) {
		if ( ! in_array( $type, self::AVAILABLE_TYPES ) ) {
			$context->handle_error( new Exception( sprintf( "Item range adjustment type '%s' not supported",
				$type ) ) );
		}

        if ( ( $type === self::TYPE_BULK AND ! in_array( $groupBy, self::BULK_AVAILABLE_GROUP_BY ) ) OR
        ( $type === self::TYPE_TIER AND ! in_array( $groupBy, self::TIER_AVAILABLE_GROUP_BY ) ) ) {
			$context->handle_error( new Exception( sprintf( "Item range adjustment qty based '%s' not supported",
				$groupBy ) ) );
        }

		$this->type                      = $type;
		$this->groupBy                   = $groupBy;
		$this->replaceAsCartAdjustment   = false;
		$this->replaceCartAdjustmentCode = null;
		$this->selectedProductIds        = array();
		$this->selectedCategoryIds       = array();
	}

	/**
	 * @param RangeDiscount $range
	 */
	public function addRange( $range ) {
		if ( $range instanceof RangeDiscount ) {
			$this->ranges[] = $range;
		}
	}

	/**
	 * @param RangeDiscount[] $ranges
	 */
	public function setRanges( $ranges ) {
		$this->ranges = array();

		foreach ( $ranges as $range ) {
			$this->addRange( $range );
		}
	}

	/**
	 * @return RangeDiscount[]
	 */
	public function getRanges() {
		return $this->ranges;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getGroupBy() {
		return $this->groupBy;
	}

	/**
	 * @param bool $replace
	 */
	public function setReplaceAsCartAdjustment( $replace ) {
		$this->replaceAsCartAdjustment = boolval( $replace );
	}

	/**
	 * @return bool
	 */
	public function isReplaceWithCartAdjustment() {
		return $this->replaceCartAdjustmentCode && $this->replaceAsCartAdjustment;
	}

	/**
	 * @param string $code
	 */
	public function setReplaceCartAdjustmentCode( $code ) {
		$this->replaceCartAdjustmentCode = (string) $code;
	}

	/**
	 * @return string
	 */
	public function getReplaceCartAdjustmentCode() {
		return $this->replaceCartAdjustmentCode;
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return count( $this->ranges ) > 0;
	}

	/**
	 * @param string $promotionalMessage
	 */
	public function setPromotionalMessage( $promotionalMessage ) {
		$this->promotionalMessage = $promotionalMessage;
	}

	/**
	 * @return string
	 */
	public function getPromotionalMessage() {
		return $this->promotionalMessage;
	}

	/**
	 * @param int[] $selectedProductIds
	 */
	public function setSelectedProductIds( $selectedProductIds ) {
		$this->selectedProductIds = $selectedProductIds;
	}

	/**
	 * @return int[]
	 */
	public function getSelectedProductIds() {
		return $this->selectedProductIds;
	}

	/**
	 * @param int[] $selectedCategoryIds
	 */
	public function setSelectedCategoryIds( $selectedCategoryIds ) {
		$this->selectedCategoryIds = $selectedCategoryIds;
	}

	/**
	 * @return int[]
	 */
	public function getSelectedCategoryIds() {
		return $this->selectedCategoryIds;
	}
}