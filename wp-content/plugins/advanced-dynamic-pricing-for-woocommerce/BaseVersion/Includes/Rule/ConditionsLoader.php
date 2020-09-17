<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleCondition;
use ADP\Factory;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ConditionsLoader {
    const KEY = 'conditions';

	const LIST_TYPE_KEY = 'type';
	const LIST_LABEL_KEY = 'label';
	const LIST_TEMPLATE_PATH_KEY = 'path';

	const GROUP_CART_ITEMS = 'cart_items';
	const GROUP_CART = 'cart';
    const GROUP_CUSTOMER = 'customer';
    const GROUP_DATE_TIME = 'date_time';
    const GROUP_SHIPPING = 'shipping';

    /**
	 * @var array
	 */
	protected $groups = array();

	/**
	 * @var string[]
	 */
	protected $items = array();

	public function __construct() {
		$this->initGroups();

		foreach ( Factory::getClassNames( 'Rule_Conditions' ) as $className ) {
			/**
			 * @var $className RuleCondition
			 */
			$this->items[ $className::getType() ] = $className;
		}
	}

	protected function initGroups() {
		$this->groups[ self::GROUP_CART_ITEMS ] = __( 'Cart items', 'advanced-dynamic-pricing-for-woocommerce' );
		$this->groups[ self::GROUP_CART ]      = __( 'Cart', 'advanced-dynamic-pricing-for-woocommerce' );
        $this->groups[ self::GROUP_CUSTOMER ] = __( 'Customer', 'advanced-dynamic-pricing-for-woocommerce' );
		$this->groups[ self::GROUP_DATE_TIME ] = __( 'Date & time', 'advanced-dynamic-pricing-for-woocommerce' );
		$this->groups[ self::GROUP_SHIPPING ] = __( 'Shipping', 'advanced-dynamic-pricing-for-woocommerce' );
	}

	/**
	 * @param $data
	 *
	 * @return RuleCondition
	 * @throws Exception
	 */
	public function build( $data ) {
		if ( empty( $data['type'] ) ) {
			throw new Exception( 'Missing condition type' );
		}

		$condition = $this->create( $data['type'] );

		if ( $condition instanceof Conditions\ValueComparisonCondition ) {
			$condition->setComparisonValue( $data['options'][ $condition::COMPARISON_VALUE_KEY ] );
			$condition->setValueComparisonMethod( $data['options'][ $condition::COMPARISON_VALUE_METHOD_KEY ] );
		}
		if ( $condition instanceof Conditions\ListComparisonCondition ) {
			$condition->setComparisonList( $data['options'][ $condition::COMPARISON_LIST_KEY ] );
			$condition->setListComparisonMethod( $data['options'][ $condition::COMPARISON_LIST_METHOD_KEY ] );
		}
		if ( $condition instanceof Conditions\RangeValueCondition ) {
			$condition->setStartRange( $data['options'][ $condition::START_RANGE_KEY ] );
			$condition->setEndRange( $data['options'][ $condition::END_RANGE_KEY ] );
		}
		if ( $condition instanceof Conditions\TimeRangeCondition ) {
			$condition->setTimeRange( $data['options'][ $condition::TIME_RANGE_KEY ] );
		}
		if ( $condition instanceof Conditions\DateTimeComparisonCondition ) {
			$condition->setComparisonDateTime( $data['options'][ $condition::COMPARISON_DATETIME_KEY ] );
			$condition->setDateTimeComparisonMethod( $data['options'][ $condition::COMPARISON_DATETIME_METHOD_KEY ] );
		}
		if ( $condition instanceof Conditions\BinaryCondition ) {
			$condition->setComparisonBinValue( $data['options'][ $condition::COMPARISON_BIN_VALUE_KEY ] );
		}
		if ( $condition instanceof Conditions\CombinationCondition ) {
			$condition->setCombineType( $data['options'][ $condition::COMBINE_TYPE_KEY ] );
			$condition->setCombineList( $data['options'][ $condition::COMBINE_LIST_KEY ] );
		}

		if ( $condition->isValid() ) {
			return $condition;
		} else {
			throw new Exception( 'Wrong condition' );
		}
	}

	/**
	 * @param $type string
	 *
	 * @return RuleCondition
	 * @throws Exception
	 */
	public function create( $type ) {
		if ( isset( $this->items[ $type ] ) ) {
			$className = $this->items[ $type ];

			return new $className();
		} else {
			throw new Exception( 'Wrong condition' );
		}
	}

	/**
	 * @return array
	 */
	public function getAsList() {
		$list = array();

		foreach ( $this->items as $type => $className ) {
			/**
			 * @var $className RuleCondition
			 */

			if( $className == Factory::getClassName("Rule_Conditions_ProductTaxonomy") OR
				$className == Factory::getClassName("Rule_Conditions_ProductTaxonomiesAmount") ) {
				foreach( Helpers::get_custom_product_taxonomies(true) as $taxonomy ) {
					//$taxonomyCondition = new ProductTaxonomy();
					$taxonomyCondition = new $className();
					$taxonomyCondition->setTaxonomy( $taxonomy );
					$list[ $taxonomyCondition->getGroup() ][] = array(
						self::LIST_TYPE_KEY          => $taxonomyCondition->getType(),
						self::LIST_LABEL_KEY         => $taxonomyCondition->getTaxonomyLabel(),
						self::LIST_TEMPLATE_PATH_KEY => $taxonomyCondition->getTemplatePath(),
						'taxonomy'					 => $taxonomy,
					);
				}
			}
			else {
				$list[ $className::getGroup() ][] = array(
					self::LIST_TYPE_KEY          => $className::getType(),
					self::LIST_LABEL_KEY         => $className::getLabel(),
					self::LIST_TEMPLATE_PATH_KEY => $className::getTemplatePath(),
				);
			}
		}

		return $list;
	}

	/**
	 * @return array
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param $key string
	 *
	 * @return string|null
	 */
	public function getGroupLabel( $key ) {
		return isset( $this->groups[ $key ] ) ? $this->groups[ $key ] : null;
	}

	public function getItems() {
		return $this->items;
	}
}
