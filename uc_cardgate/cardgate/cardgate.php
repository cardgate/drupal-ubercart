<?php

/**
 * CARDGATE Library for PHP
 *
 * This file is a base class for all payment methods of CARDGATE.
 * However, creating an instance of this class will redirect you to a payment screen of CARDGATE
 * where you will be able to choose from different payment methods.
 *
 * @author CARDGATE <support@cardgate.com>
 * @copyright Copyright (c) 2015, CARDGATE
 * @version 1.2
 */
// For Ubercart usage

class CARDGATE {

    var $version = '7.0.15';
    protected $siteID = 0;
    protected $hashKey = "";
    protected $orderID = "";
    protected $country = "";
    protected $language = "";
    protected $currency = "";
    protected $amount = 0;
    protected $description = "";
    protected $paymentMethod = "";
    protected $reference = "";
    protected $logging = false;
    protected $loggingDirectory = ".";
    protected $pageData = NULL;
    protected $notifyUrl = "";

    /**
     * Constructor
     * @since Version 1.01
     * @param int $siteID This is the site ID that you can create in your CARDGATE backoffice account.
     * @param int $secretCode This is the key that belongs to your site ID.
     * @return CARDGATE
     */
    public function __construct( $siteID = NULL, $secretCode = NULL ) {
        $this->siteID = $siteID;
        $this->hashKey = $secretCode;
        $url = "https://secure.curopayments.net/gateway/cardgate/";

        $test = variable_get( 'cardgate_mode', '' ) == 'test';
        if ( $test ) {
            $url = "https://secure-staging.curopayments.net/gateway/cardgate/";
        }
        $this->notifyUrl = $url;
    }



    /**
     * Returns the API ID
     * @access public
     * @since Version 1.01
     * @return string
     */

    /**
     * Find for a string in an array of strings
     * @access protected
     * @param $collection
     * @param $find
     * @return true|false TRUE if the string is found, otherwise FALSE
     */
    protected function inCollection( $collection, $find ) {
        foreach ( $collection as $item )
            if ( $find == $item )
                return true;
        return false;
    }

    /**
     * Set the order ID if you do not wish to use automatic order ID generation.
     * @access public
     * @return void
     */
    public function SetOrderID( $orderID ) {
        $orderID = trim( $orderID );

        if ( strlen( $orderID ) > 10 )
            throw new Exception( sprintf( "Your order ID '%s' may not be longer than 10 characters", $orderID ) );

        $this->orderID = $orderID;

        return;
    }

    /**
     * Set the reference
     * @access public
     * @return void
     */
    public function SetReference( $reference ) {
        $this->reference = $reference;

        return;
    }

    /**
     * Enable/disable logging
     * @access public
     * @return void
     */
    public function SetLogging( $logging ) {
        $this->logging = $logging;
        return;
    }

    /**
     * Set logging directory
     * @access public
     * @return void
     */
    public function SetLoggingDirectory( $loggingDirectory ) {
        $this->loggingDirectory = $loggingDirectory;
        return;
    }

    /**
     * Sets the API URL
     * @access public
     * @return void
     */
    public function SetApiURL( $url ) {
        $this->apiURL = $url;
        return;
    }

    /**
     * Set stream method
     * @access public
     * @return void
     */
    public function SetStreamMethod( $streamMethod ) {
        $this->streamMethod = strtolower( $streamMethod );
        return;
    }

    /**
     * Appends text to a log file
     * @access protected
     * @return bool Returns TRUE if logging is enabled, otherwise FALSE
     */
    protected function doLogging( $line ) {
        if ( !$this->logging )
            return false;
        date_default_timezone_set( "Europe/Paris" );
        $filename = sprintf( "%s/#%s.log", $this->loggingDirectory, date( "Ymd", time() ) );
        $fp = @fopen( $filename, "a" );
        $line = sprintf( "%s - %s\r\n", date( "H:i", time() ), $line );
        @fwrite( $fp, $line );
        @fclose( $fp );

        return true;
    }

    /**
     * This method is meant for 'listening' to and handling all postbacks sent by CARDGATE.
     * If logging is enabled, then the received postbacks and possible errors will be logged.
     * The logs are handy for debugging purposes and/or in case you contact technical support
     *
     * @return bool Returns TRUE if a valid CARDGATE postback is detected, otherwise FALSE
     */
    public function VerifyPostback() {

        if ( $_SERVER['REQUEST_METHOD'] != 'POST' && !isset( $_POST['ref'] ) && !isset( $_POST['status'] ) )
            return false;

        $this->status = $_POST['status'];
        $this->doLogging( sprintf( "Postback: %s", serialize( $_POST ) ) );

        // If we don't have an 'extra' arg (CURO) we retrieve it from the 'ref'
        if ( empty( $_POST['extra'] ) ) {
            $orderID = intval( substr( $_POST['ref'], 10 ) );
        } else {
            $orderID = intval( $_POST['extra'] );
        }

        $this->SetOrderID( $orderID );
        $this->SetReference( intval( $_POST['ref'] ) );

        $order = uc_order_load( $orderID );

        $currency = $order->currency;
        // NB: we need to use this function, or else rounding the order->total will go WRONG (34.16*100 -> 3416.4?!?!?)
        $amount = uc_currency_format( $order->order_total, FALSE, FALSE, '.' ) * 100;

        $ref = $this->reference;
        $hashKey = variable_get( 'cardgate_hash_key', '' );
        $test = $_POST['is_test'];

        $hash = md5( ($test == 1 ? 'TEST' : '') . $_POST['transactionid'] . $currency . $amount . $ref . $_POST['status'] . $hashKey );

        if ( $hash == $_POST['hash'] ) {
            return true;
        } else {
            return false;
        }
    }

    public function getNotifyUrl() {
        return $this->notifyUrl;
    }

    public function getOrderData( $order ) {
        $aCartItems = array();

        // See if an applicable tax exists
        foreach ( $order->line_items as $sKey => $aLineItem ) {
            if ( $aLineItem['type'] == 'tax' ) {
                $data = $aLineItem['data']['tax'];
                $tax = array();
                $tax['rate'] = $data->rate;
                $tax['shippable'] = $data->shippable;
                $tax['taxed_product_types'] = $data->taxed_product_types; // array
                $tax['taxed_line_items'] = $data->taxed_line_items;
            }
        }

        $cart_total = 0;

        foreach ( $order->products as $sKey => $oItem ) {

            $price = $oItem->price * 100;
            $vat = 0;
            $vat_amount = 0;

            if ( !empty( $tax ) ) {
                if ( in_array( $oItem->type, $tax['taxed_product_types'] ) ) {
                    $price = $price * (1 + $tax['rate']);
                    $vat = $tax['rate'] * 100;
                    $vat_amount = $oItem->price * $vat;
                }
            }

            $aCartItems[] = array(
                'quantity' => $oItem->qty,
                'sku' => $oItem->model,
                'name' => $oItem->title,
                'price' => round( $price, 0 ),
                'vat' => round( $vat, 0 ),
                'vat_amount' => round( $vat_amount, 0 ),
                'vat_inc' => true,
                'type' => 1 );

            $cart_total += $oItem->qty * (round( $price, 0 ));
        }

        foreach ( $order->line_items as $sKey => $aLineItem ) {
            switch ( $aLineItem['type'] ) {
                case 'shipping':
                    $price = $aLineItem['amount'] * 100;
                    $vat = 0;
                    $vat_amount = 0;

                    if ( !empty( $tax ) ) {
                        if ( in_array( 'shipping', $tax['taxed_line_items'] ) ) {
                            $price = $price * (1 + $tax['rate']);
                            $vat = $tax['rate'] * 100;
                            $vat_amount = $aLineItem['amount'] * $vat;
                        }
                    }
                    $aCartItems[] = array(
                        'quantity' => 1,
                        'sku' => 'SHIPPING',
                        'name' => $aLineItem['title'],
                        'price' => round( $price, 0 ),
                        'vat' => round( $vat, 0 ),
                        'vat_amount' => round( $vat_amount, 0 ),
                        'vat_inc' => true,
                        'type' => 2 );

                    $cart_total += round( $price, 0 );
                    break;

                case 'coupon':
                    // $price = ($aLineItem['amount'] < 0 ) ? ( $aLineItem['amount'] * -1 ) * 100 : $aLineItem['amount'] * 100;
                    $price = $aLineItem['amount'] * 100;
                    $vat = 0;
                    $vat_amount = 0;

                    if ( !empty( $tax ) ) {
                        if ( in_array( 'coupon', $tax['taxed_line_items'] ) ) {
                            $price = $price * (1 + $tax['rate']);
                            $vat = $tax['rate'] * 100;
                            $vat_amount = $aLineItem['amount'] * $vat;
                        }
                    }
                    $aCartItems[] = array(
                        'quantity' => 1,
                        'sku' => 'DISCOUNT',
                        'name' => $aLineItem['title'],
                        'price' => round( $price, 0 ),
                        'vat' => round( $vat, 0 ),
                        'vat_amount' => round( $vat_amount, 0 ),
                        'vat_inc' => true,
                        'type' => 4 );
                    $cart_total += round( $price, 0 );
                    break;
            }
        }

        $amount = uc_currency_format( $order->order_total, FALSE, FALSE, '.' ) * 100;
        $correction = $amount - $cart_total;
        if ( $correction != 0 ) {
            $aCartItems[] = array(
                'quantity' => 1,
                'sku' => 'Correction',
                'name' => 'Total offset',
                'price' => round( $correction, 0 ),
                'vat' => 0,
                'vat_amount' => 0,
                'vat_inc' => true,
                'type' => 4 );
        }

        $data = array();
        $shop_data = system_get_info('module', 'uc_cart');
        $test = variable_get( 'cardgate_mode', '' );
        $my_country = array( 'country_id' => $order->billing_country );
        $b_country = uc_get_country_data( $my_country );
        $ref = time() . $order->order_id;
        $extra = $order->order_id;
        $hashKey = variable_get( 'cardgate_hash_key', '' );
        $hash = md5( ($test == 'live' ? '' : 'TEST') . $this->siteID . $amount . $ref . $hashKey );
        $data['test'] = ($test == 'test' ? 1 : 0);
        $data['option'] = substr( $order->payment_method, 4 );
        $data['siteid'] = $this->siteID;
        $data['currency'] = $order->currency;
        $data['amount'] = $amount;
        $data['ref'] = $ref;
        $data['description'] = 'order_' . $order->order_id;
        $data['return_url'] = url( '', array( 'absolute' => TRUE ) ) . '?q=cart/cgp_success&extra=' . $extra;
        $data['return_url_failed'] = url( '', array( 'absolute' => TRUE ) ) . '?q=cart/cgp_failure&extra=' . $extra;
        $data['email'] = (isset( $order->primary_email ) ? $order->primary_email : '');
        $data['first_name'] = (isset( $order->billing_first_name ) ? $order->billing_first_name : '');
        $data['last_name'] = (isset( $order->billing_last_name ) ? $order->billing_last_name : '');
        $data['address'] = (isset( $order->billing_street1 ) ? $order->billing_street1 : '');
        $data['postal_code'] = (isset( $order->billing_postal_code ) ? $order->billing_postal_code : '');
        $data['city'] = (isset( $order->billing_city ) ? $order->billing_city : '');
        $data['country_code'] = $b_country[0]['country_iso_code_2'];
        $data['hash'] = $hash;
        $data['shop_name'] = 'DrupalUbercart';
        $data['shop_version'] = $shop_data['version'];
        $data['plugin_name'] = 'drupal_ubercart';
        $data['plugin_version'] = $this->version;
        $data['extra'] = $extra;

        if ( count( $aCartItems ) > 0 ) {
            $data['cartitems'] = json_encode( $aCartItems );
        }
        return $data;
    }
}
?>


