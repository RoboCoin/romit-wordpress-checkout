<?php
/*
 * Plugin Name: Enter Checkout Page
 * Description: Receive payments using Enter's checkout page
 * Author: enter.financial
 * Version: 1.2.2
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
{
    function initEnterCheckoutPage()
    {
        class EnterCheckoutPageGateway extends WC_Payment_Gateway
        {
            private static $logger = null;

            private function _stripNumber($phoneNumber)
            {
                return preg_replace('/[^0-9]/', '', $phoneNumber);
            }

            private function _startsWith($phoneNumber, $possibleCode)
            {
                $possibleCode = $this->_stripNumber($possibleCode);
                return (substr($phoneNumber, 0, strlen($possibleCode)) == $possibleCode);
            }

            private function _formatE164($countryCode, $phoneNumber)
            {
                $codes = array(
                    'AF' => ['93'],
                    'AL' => ['355'],
                    'DZ' => ['213'],
                    'AS' => ['1-684'],
                    'AD' => ['376'],
                    'AO' => ['244'],
                    'AI' => ['1-264'],
                    'AQ' => ['672'],
                    'AG' => ['1-268'],
                    'AR' => ['54'],
                    'AM' => ['374'],
                    'AW' => ['297'],
                    'AU' => ['61'],
                    'AT' => ['43'],
                    'AZ' => ['994'],
                    'BS' => ['1-242'],
                    'BH' => ['973'],
                    'BD' => ['880'],
                    'BB' => ['1-246'],
                    'BY' => ['375'],
                    'BE' => ['32'],
                    'BZ' => ['501'],
                    'BJ' => ['229'],
                    'BM' => ['1-441'],
                    'BT' => ['975'],
                    'BO' => ['591'],
                    'BA' => ['387'],
                    'BW' => ['267'],
                    'BR' => ['55'],
                    'IO' => ['246'],
                    'VG' => ['1-284'],
                    'BN' => ['673'],
                    'BG' => ['359'],
                    'BF' => ['226'],
                    'BI' => ['257'],
                    'KH' => ['855'],
                    'CM' => ['237'],
                    'CA' => ['1'],
                    'CV' => ['238'],
                    'KY' => ['1-345'],
                    'CF' => ['236'],
                    'TD' => ['235'],
                    'CL' => ['56'],
                    'CN' => ['86'],
                    'CX' => ['61'],
                    'CC' => ['61'],
                    'CO' => ['57'],
                    'KM' => ['269'],
                    'CK' => ['682'],
                    'CR' => ['506'],
                    'HR' => ['385'],
                    'CU' => ['53'],
                    'CW' => ['599'],
                    'CY' => ['357'],
                    'CZ' => ['420'],
                    'CD' => ['243'],
                    'DK' => ['45'],
                    'DJ' => ['253'],
                    'DM' => ['1-767'],
                    'DO' => ['1-809', '1-829', '1-849'],
                    'TL' => ['670'],
                    'EC' => ['593'],
                    'EG' => ['20'],
                    'SV' => ['503'],
                    'GQ' => ['240'],
                    'ER' => ['291'],
                    'EE' => ['372'],
                    'ET' => ['251'],
                    'FK' => ['500'],
                    'FO' => ['298'],
                    'FJ' => ['679'],
                    'FI' => ['358'],
                    'FR' => ['33'],
                    'PF' => ['689'],
                    'GA' => ['241'],
                    'GM' => ['220'],
                    'GE' => ['995'],
                    'DE' => ['49'],
                    'GH' => ['233'],
                    'GI' => ['350'],
                    'GR' => ['30'],
                    'GL' => ['299'],
                    'GD' => ['1-473'],
                    'GU' => ['1-671'],
                    'GT' => ['502'],
                    'GG' => ['44-1481'],
                    'GN' => ['224'],
                    'GW' => ['245'],
                    'GY' => ['592'],
                    'HT' => ['509'],
                    'HN' => ['504'],
                    'HK' => ['852'],
                    'HU' => ['36'],
                    'IS' => ['354'],
                    'IN' => ['91'],
                    'ID' => ['62'],
                    'IR' => ['98'],
                    'IQ' => ['964'],
                    'IE' => ['353'],
                    'IM' => ['44-1624'],
                    'IL' => ['972'],
                    'IT' => ['39'],
                    'CI' => ['225'],
                    'JM' => ['1-876'],
                    'JP' => ['81'],
                    'JE' => ['44-1534'],
                    'JO' => ['962'],
                    'KZ' => ['7'],
                    'KE' => ['254'],
                    'KI' => ['686'],
                    'XK' => ['383'],
                    'KW' => ['965'],
                    'KG' => ['996'],
                    'LA' => ['856'],
                    'LV' => ['371'],
                    'LB' => ['961'],
                    'LS' => ['266'],
                    'LR' => ['231'],
                    'LY' => ['218'],
                    'LI' => ['423'],
                    'LT' => ['370'],
                    'LU' => ['352'],
                    'MO' => ['853'],
                    'MK' => ['389'],
                    'MG' => ['261'],
                    'MW' => ['265'],
                    'MY' => ['60'],
                    'MV' => ['960'],
                    'ML' => ['223'],
                    'MT' => ['356'],
                    'MH' => ['692'],
                    'MR' => ['222'],
                    'MU' => ['230'],
                    'YT' => ['262'],
                    'MX' => ['52'],
                    'FM' => ['691'],
                    'MD' => ['373'],
                    'MC' => ['377'],
                    'MN' => ['976'],
                    'ME' => ['382'],
                    'MS' => ['1-664'],
                    'MA' => ['212'],
                    'MZ' => ['258'],
                    'MM' => ['95'],
                    'NA' => ['264'],
                    'NR' => ['674'],
                    'NP' => ['977'],
                    'NL' => ['31'],
                    'AN' => ['599'],
                    'NC' => ['687'],
                    'NZ' => ['64'],
                    'NI' => ['505'],
                    'NE' => ['227'],
                    'NG' => ['234'],
                    'NU' => ['683'],
                    'KP' => ['850'],
                    'MP' => ['1-670'],
                    'NO' => ['47'],
                    'OM' => ['968'],
                    'PK' => ['92'],
                    'PW' => ['680'],
                    'PS' => ['970'],
                    'PA' => ['507'],
                    'PG' => ['675'],
                    'PY' => ['595'],
                    'PE' => ['51'],
                    'PH' => ['63'],
                    'PN' => ['64'],
                    'PL' => ['48'],
                    'PT' => ['351'],
                    'PR' => ['1-787', '1-939'],
                    'QA' => ['974'],
                    'CG' => ['242'],
                    'RE' => ['262'],
                    'RO' => ['40'],
                    'RU' => ['7'],
                    'RW' => ['250'],
                    'BL' => ['590'],
                    'SH' => ['290'],
                    'KN' => ['1-869'],
                    'LC' => ['1-758'],
                    'MF' => ['590'],
                    'PM' => ['508'],
                    'VC' => ['1-784'],
                    'WS' => ['685'],
                    'SM' => ['378'],
                    'ST' => ['239'],
                    'SA' => ['966'],
                    'SN' => ['221'],
                    'RS' => ['381'],
                    'SC' => ['248'],
                    'SL' => ['232'],
                    'SG' => ['65'],
                    'SX' => ['1-721'],
                    'SK' => ['421'],
                    'SI' => ['386'],
                    'SB' => ['677'],
                    'SO' => ['252'],
                    'ZA' => ['27'],
                    'KR' => ['82'],
                    'SS' => ['211'],
                    'ES' => ['34'],
                    'LK' => ['94'],
                    'SD' => ['249'],
                    'SR' => ['597'],
                    'SJ' => ['47'],
                    'SZ' => ['268'],
                    'SE' => ['46'],
                    'CH' => ['41'],
                    'SY' => ['963'],
                    'TW' => ['886'],
                    'TJ' => ['992'],
                    'TZ' => ['255'],
                    'TH' => ['66'],
                    'TG' => ['228'],
                    'TK' => ['690'],
                    'TO' => ['676'],
                    'TT' => ['1-868'],
                    'TN' => ['216'],
                    'TR' => ['90'],
                    'TM' => ['993'],
                    'TC' => ['1-649'],
                    'TV' => ['688'],
                    'VI' => ['1-340'],
                    'UG' => ['256'],
                    'UA' => ['380'],
                    'AE' => ['971'],
                    'GB' => ['44'],
                    'US' => ['1'],
                    'UY' => ['598'],
                    'UZ' => ['998'],
                    'VU' => ['678'],
                    'VA' => ['379'],
                    'VE' => ['58'],
                    'VN' => ['84'],
                    'WF' => ['681'],
                    'EH' => ['212'],
                    'YE' => ['967'],
                    'ZM' => ['260'],
                    'ZW' => ['263']
                );

                $strippedNumber = $this->_stripNumber($phoneNumber);

                if (!array_key_exists($countryCode, $codes))
                {
                    error_log('unknown country code: ' . $countryCode);
                    return '+' . $strippedNumber;
                }

                $possibleCodes = $codes[$countryCode];
                for ($i = 0; $i < count($possibleCodes); $i++)
                {
                    if ($this->_startsWith($strippedNumber, $possibleCodes[$i]))
                    {
                        return '+' . $strippedNumber;
                    }
                }

                return '+' . $possibleCodes[0] . $strippedNumber;
            }

            private function _get_form_url($order)
            {
                $isProduction = ($this->settings['production'] == 'yes');

                $baseUrl = (($isProduction)
                    ? 'https://checkout.enter.financial/payment-page'
                    : 'https://checkout.sandbox.enter.financial/payment-page');

                $vendorId = $this->settings['vendor_id'];

                $returnUrl = $this->get_return_url($order);
                $urlParts = parse_url($returnUrl);
                $successUri = $urlParts['path'];
                if ($urlParts['query'])
                {
                    $successUri .= '?' . $urlParts['query'];
                }

                $url = $baseUrl . '?' . http_build_query(array(
                        'vendor_id' => $vendorId,
                        'price' => $order->get_total(),
                        'memo' => $order->get_order_number(),
                        'currency_type' => get_woocommerce_currency(),
                        'success_uri' => esc_url_raw($successUri),
                        'first_name' => $order->billing_first_name,
                        'last_name' => $order->billing_last_name,
                        'email' => $order->billing_email,
                        'phone_number' => $this->_formatE164($order->billing_country, $order->billing_phone),
                        // TODO Can we get this from WooCommerce?
                        //'gender' => '',
                        //'dob' => '',
                        'address_line_1' => $order->billing_address_1,
                        'address_line_2' => $order->billing_address_2,
                        'city' => $order->billing_city,
                        'state' => $order->billing_state,
                        'postal' => $order->billing_postcode,
                        'country' => $order->billing_country
                    ));

                return $url;
            }

            protected function log($message)
            {
                if (!self::$logger)
                {
                    self::$logger = new WC_Logger();
                }

                self::$logger->add('enter', $message);
            }

            public function __construct()
            {
                $this->id = 'enter_checkout_page';
                $this->icon = 'https://checkout.sandbox.enter.financial/app/img/romit_button_new.png';
                $this->has_fields = false;
                $this->method_title = __('Enter Checkout Page', 'enter_checkout_page');
                $this->method_description = __('Use a payment form hosted on wallet.enter.financial', 'enter_checkout_page');
                $this->order_button_text = __('Proceed to Enter', 'enter_checkout_page');

                $this->init_form_fields();
                $this->init_settings();

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, 'process_admin_options');
                add_action('woocommerce_api_wc_' . $this->id, array($this, 'on_callback'));
                add_action('woocommerce_api_callback', array($this, 'on_callback'));
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            }

            public function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable/Disable', 'enter_checkout_page'),
                        'type' => 'checkbox',
                        'label' => __('Enable Enter Payment Page', 'enter_checkout_page'),
                        'default' => 'yes'
                    ),
                    'production' => array(
                        'title' => __('Production?', 'enter_checkout_page'),
                        'type' => 'checkbox',
                        'label' => __('Enable Production Enter Payment Page, otherwise Sandbox', 'enter_checkout_page'),
                        'default' => 'no'
                    ),
                    'title' => array(
                        'title' => __('Title', 'enter_checkout_page'),
                        'type' => 'text',
                        'description' => __('This controls the title which the user sees during checkout.', 'enter_checkout_page'),
                        'default' => __('Enter Checkout Page', 'enter_checkout_page'),
                        'desc_tip' => true
                    ),
                    'description' => array(
                        'title' => __('Customer Message', 'enter_checkout_page'),
                        'type' => 'textarea',
                        'default' => ''
                    ),
                    'vendor_id' => array(
                        'title' => __('Checkout ID', 'enter_checkout_page'),
                        'type' => 'text',
                        'desc_tip' => true,
                        'description' => __('This is your Checkout ID from Tools -> Edit Checkout configuration', 'enter_checkout_page')
                    ),
                    'callback_secret' => array(
                        'title' => __('Callback Secret', 'enter_checkout_page'),
                        'type' => 'text',
                        'desc_tip' => true,
                        'description' => __('This is is the secret we\'ll use to verify that a callback came from Enter', 'enter_checkout_page')
                    )
                );
            }

            public function admin_options()
            {
                ?><h2><?php _e('Enter Checkout Page', 'enter_checkout_page'); ?></h2>
                <table class="form-table">
                <?php $this->generate_settings_html(); ?>
                </table><?php
            }

            public function process_payment($order_id)
            {
                $order = new WC_Order($order_id);

                return array(
                    'result' => 'success',
                    'redirect' => $this->_get_form_url($order)
                );
            }

            public function on_callback()
            {
                $rawBody = file_get_contents('php://input');
                $signature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'];
                $secret = $this->settings['callback_secret'];
                $hashedBody = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));

                if ($signature != $hashedBody)
                {
                    $this->log('expected signature: ' . $hashedBody . ' header signature: ' . $signature);
                    return;
                }

                $postBody = json_decode($rawBody, true);

                if (isset($postBody['statusType']))
                {
                    $order = new WC_Order($postBody['sourceMessage']);
                    $order->add_order_note('Enter transfer ID: ' . $postBody['paymentId']);
                    switch ($postBody['statusType'])
                    {
                        case 'EXECUTED':

                            if ($order->get_total() != $postBody['sourceAmount'])
                            {
                                $order->add_order_note('Amount paid (' . $postBody['sourceAmount']
                                    . ') differs from amount of order (' . $order->get_total() . ')');
                                $order->update_status('failed');
                                return;
                            }

                            global $woocommerce;
                            $woocommerce->cart->empty_cart();

                            $order->payment_complete();
                            break;

                        case 'CANCELLED':
                            $order->update_status('cancelled');
                            break;

                        case 'ERROR':
                            $order->update_status('failed');
                            break;

                        case 'REFUNDED':
                            $order->update_status('refunded');
                            break;

                        default:
                            $this->log('unknown transaction callback status: ' . $postBody['statusType']);
                    }
                }
                else
                {
                    $this->log('transaction callback with no status');
                }
            }
        }
    }

    function addEnterCheckoutPageGatewayClass($methods)
    {
        $methods[] = 'EnterCheckoutPageGateway';
        return $methods;
    }

    add_action('plugins_loaded', 'initEnterCheckoutPage');
    add_filter('woocommerce_payment_gateways', 'addEnterCheckoutPageGatewayClass');
}
