<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\CartSet;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\Discount;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\PackageRangeAdjustments;
use ADP\BaseVersion\Includes\Rule\Structures\RangeDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\SetDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule\ProductsRangeAdjustments;

class TierUpItems {
	/**
	 * @var Rule
	 */
	protected $rule;

	/**
	 * @var ProductsRangeAdjustments|PackageRangeAdjustments
	 */
	protected $handler;

	/**
	 * @var Cart
	 */
	protected $cart;

	const MARK_CALCULATED = 'tier_calculated';

	/**
	 * @param SingleItemRule|PackageRule $rule
	 * @param Cart $cart
	 */
	public function __construct( $rule, $cart ) {
		$this->rule    = $rule;
		$this->cart    = $cart;
		$this->handler = $rule->getProductRangeAdjustmentHandler();
	}

	/**
	 * @param CartItem[] $items
	 *
	 * @return CartItem[]
	 */
	public function executeItems( $items ) {
		foreach ( $this->handler->getRanges() as $range ) {
			$items = $this->processRange( $items, $range );
		}

		foreach ( $items as $item ) {
			$item->removeMark( self::MARK_CALCULATED );
		}

		return $items;
	}

	/**
	 * @param CartItem[] $items
	 * @param float      $customQty
	 *
	 * @return CartItem[]
	 */
	public function executeItemsWithCustomQty( $items, $customQty ) {
		if ( $customQty === floatval( 0 ) ) {
			return $items;
		}

		foreach ( $this->handler->getRanges() as $range ) {
			if ( ! is_null( $customQty ) && $range->isIn( $customQty ) ) {
				$range = new RangeDiscount( $range->getFrom(), $customQty, $range->getData() );
				$items = $this->processRange( $items, $range );
				break;
			}

			$items = $this->processRange( $items, $range );
		}

		foreach ( $items as $item ) {
			$item->removeMark( self::MARK_CALCULATED );
		}

		return $items;
	}

	/**
	 * @param CartSet[] $items
	 *
	 * @return CartSet[]
	 */
	public function executeSets( $items ) {
		foreach ( $this->handler->getRanges() as $range ) {
			$items = $this->processRange( $items, $range );
		}

		foreach ( $items as $item ) {
			$item->removeMark( self::MARK_CALCULATED );
		}

		return $items;
	}

	/**
	 * @param CartItem[]|CartSet[] $elements
	 * @param RangeDiscount        $range
	 *
	 * @return CartItem[]|CartSet[]
	 */
	protected function processRange( $elements, $range ) {
		$processedQty          = 1;
		$newElements           = array();
		$indexOfItemsToProcess = array();
		foreach ( $elements as $element ) {
			if ( $element->hasMark( self::MARK_CALCULATED ) ) {
				$newElements[] = $element;
				$processedQty  += $element->getQty();
				continue;
			}

			if ( $range->isLess( $processedQty ) ) {
				if ( $range->isIn( $processedQty + $element->getQty() ) ) {
					$requireQty = $processedQty + $element->getQty() - $range->getFrom();

					if ( $requireQty > 0 ) {
						$newItem = clone $element;
						$newItem->setQty( $requireQty );
						$newElements[]           = $newItem;
						$indexOfItemsToProcess[] = count( $newElements ) - 1;
						$processedQty            += $requireQty;
					}

					if ( ( $element->getQty() - $requireQty ) > 0 ) {
						$newItem = clone $element;
						$newItem->setQty( $element->getQty() - $requireQty );
						$newElements[] = $newItem;
						$processedQty  += $element->getQty() - $requireQty;
					}
				} elseif ( $range->isGreater( $processedQty + $element->getQty() ) ) {
					$requireQty = $range->getQtyInc();

					if ( $requireQty > 0 ) {
						$newItem = clone $element;
						$newItem->setQty( $requireQty );
						$newElements[]           = $newItem;
						$indexOfItemsToProcess[] = count( $newElements ) - 1;
						$processedQty            += $requireQty;
					}

					if ( ( $element->getQty() - $requireQty ) > 0 ) {
						$newItem = clone $element;
						$newItem->setQty( $element->getQty() - $requireQty );
						$newElements[] = $newItem;
						$processedQty  += $element->getQty() - $requireQty;
					}

				} else {
					$newElements[] = $element;
					$processedQty  += $element->getQty();
				}
			} elseif ( $range->isIn( $processedQty ) ) {
				$requireQty = $range->getTo() + 1 - $processedQty;
				$requireQty = $requireQty < $element->getQty() ? $requireQty : $element->getQty();

				if ( $requireQty > 0 ) {
					$newItem = clone $element;
					$newItem->setQty( $requireQty );
					$newElements[]           = $newItem;
					$indexOfItemsToProcess[] = count( $newElements ) - 1;
					$processedQty            += $requireQty;
				}

				if ( ( $element->getQty() - $requireQty ) > 0 ) {
					$newItem = clone $element;
					$newItem->setQty( $element->getQty() - $requireQty );
					$newElements[] = $newItem;
					$processedQty  += $element->getQty() - $requireQty;
				}

			} elseif ( $range->isGreater( $processedQty ) ) {
				$newElements[] = $element;
				$processedQty  += $element->getQty();
			}
		}

		$discount        = $range->getData();
		$priceCalculator = new PriceCalculator( $this->rule, $discount );
		foreach ( $indexOfItemsToProcess as $index ) {
			$elementToProcess = $newElements[ $index ];

			if ( $elementToProcess instanceof CartSet ) {
				if ( $discount instanceof SetDiscount ) {
					$priceCalculator->calculatePriceForSet( $elementToProcess, $this->cart, $this->handler );
				} elseif ( $discount instanceof Discount ) {
					foreach ( $elementToProcess->get_items() as $element ) {
						$priceCalculator->applyItemDiscount( $element, $this->cart, $this->handler );
					}
				}
				$elementToProcess->addMark( self::MARK_CALCULATED );
			} elseif ( $elementToProcess instanceof CartItem ) {
				$priceCalculator->applyItemDiscount( $elementToProcess, $this->cart, $this->handler );
				$elementToProcess->addMark( self::MARK_CALCULATED );
			}

		}

		return $newElements;
	}
}