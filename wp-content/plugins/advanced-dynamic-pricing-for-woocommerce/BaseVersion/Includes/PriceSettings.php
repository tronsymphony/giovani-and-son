<?php

namespace ADP\BaseVersion\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PriceSettings {
	const CURRENCY_POS_LEFT = 'left';
	const CURRENCY_POS_LEFT_SPACE = 'left_space';
	const CURRENCY_POS_RIGHT = 'right';
	const CURRENCY_POS_RIGHT_SPACE = 'right_space';

	/**
	 * @var string[]
	 */
	protected $currencySymbols = array();

	/**
	 * @var bool
	 */
	protected $taxEnabled = false;

	/**
	 * @var bool
	 */
	protected $includeTax = false;

	/**
	 * @var int
	 */
	protected $decimals = 2;

	/**
	 * @var string
	 */
	protected $decimalSeparator = '.';

	/**
	 * @var string
	 */
	protected $thousandSeparator = '';

	protected $currencyPos = self::CURRENCY_POS_LEFT;

	/**
	 * @var string
	 */
	protected $priceFormat = '';

	protected $defaultCurrencyCode = 'USD';

	public function __construct() {
		$this->currencySymbols = $this->getDefaultAvailableCurrencySymbols();
	}

	/**
	 * @param bool $taxEnabled
	 *
	 * @return self
	 */
	public function setTaxEnabled( $taxEnabled ) {
		$this->taxEnabled = boolval( $taxEnabled );

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTaxEnabled() {
		return $this->taxEnabled;
	}

	/**
	 * @param bool $includeTax
	 *
	 * @return self
	 */
	public function setIncludeTax( $includeTax ) {
		$this->includeTax = boolval( $includeTax );

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isIncludeTax() {
		return $this->taxEnabled && $this->includeTax;
	}

	/**
	 * @param int $decimals
	 *
	 * @return self
	 */
	public function setDecimals( $decimals ) {
		if ( $decimals = intval( $decimals ) ) {
			$this->decimals = $decimals;
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDecimals() {
		return $this->decimals;
	}

	/**
	 * @param string $decimalSeparator
	 */
	public function setDecimalSeparator( $decimalSeparator ) {
		if ( ! empty( $decimalSeparator ) && is_string( $decimalSeparator ) ) {
			$this->decimalSeparator = $decimalSeparator;
		}
	}

	/**
	 * @return string
	 */
	public function getDecimalSeparator() {
		return $this->decimalSeparator;
	}

	/**
	 * @param string $thousandSeparator
	 */
	public function setThousandSeparator( $thousandSeparator ) {
		if ( is_string( $thousandSeparator ) ) {
			$this->thousandSeparator = $thousandSeparator;
		}
	}

	/**
	 * @return string
	 */
	public function getThousandSeparator() {
		return $this->thousandSeparator;
	}

	/**
	 * @param string $currencyPos
	 */
	public function setCurrencyPos( $currencyPos ) {
		if ( in_array( $currencyPos, array(
			self::CURRENCY_POS_LEFT,
			self::CURRENCY_POS_LEFT_SPACE,
			self::CURRENCY_POS_RIGHT,
			self::CURRENCY_POS_RIGHT_SPACE,
		) ) ) {
			$this->currencyPos = $currencyPos;
		}
	}

	/**
	 * @return string
	 */
	public function getCurrencyPos() {
		return $this->currencyPos;
	}

	/**
	 * @param string $priceFormat
	 */
	public function setPriceFormat( $priceFormat ) {
		if ( ! empty( $decimalSeparator ) && is_string( $priceFormat ) ) {
			$this->priceFormat = $priceFormat;
		}
	}

	/**
	 * @return string
	 */
	public function getPriceFormat() {
		if ( $this->priceFormat ) {
			return $this->priceFormat;
		}

		$format = '%1$s%2$s';

		switch ( $this->currencyPos ) {
			case 'left':
				$format = '%1$s%2$s';
				break;
			case 'right':
				$format = '%2$s%1$s';
				break;
			case 'left_space':
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space':
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		return $format;
	}

	/**
	 * @param string $defaultCurrencyCode
	 */
	public function setDefaultCurrencyCode( $defaultCurrencyCode ) {
		if ( isset( $this->currencySymbols[ $defaultCurrencyCode ] ) ) {
			$this->defaultCurrencyCode = $defaultCurrencyCode;
		}
	}

	/**
	 * @return string
	 */
	public function getDefaultCurrencyCode() {
		return $this->defaultCurrencyCode;
	}

	/**
	 * @param array $currencySymbols
	 */
	public function setCurrencySymbols( $currencySymbols ) {
		if ( ! empty( $currencySymbols ) && is_array( $currencySymbols ) ) {
			$this->currencySymbols = $currencySymbols;
		}
	}

	/**
	 * @return string[]
	 */
	public function getCurrencySymbols() {
		return $this->currencySymbols;
	}

	/**
	 * @return string[]
	 */
	protected function getDefaultAvailableCurrencySymbols() {
		return array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
	}
}