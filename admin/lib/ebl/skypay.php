<?php
    @session_start();
    require "nvp/merchant.php";
    require "nvp/connection.php";

    class Skypay
    {
        protected $skypay;
        protected $merchant;
        protected $parser;
        protected $order;
        protected $completeUrl;

        public function __construct($configArray)
        {
            // The below value should not be changed
            if (!array_key_exists("proxyCurlOption", $configArray)) {
                $configArray["proxyCurlOption"] = CURLOPT_PROXYAUTH;
            }
            
            // The CURL Proxy type. Currently supported values: CURLAUTH_NTLM and CURLAUTH_BASIC
            if (!array_key_exists("proxyCurlValue", $configArray)) {
                $configArray["proxyCurlValue"] = CURLAUTH_NTLM;
            }

            // Base URL of the Payment Gateway. Do not include the version.
            if (!array_key_exists("gatewayUrl", $configArray)) {
                if ($configArray["gatewayMode"] === true) {
                    $configArray["gatewayUrl"] = "https://ap-gateway.mastercard.com/api/nvp";
                } else {
                    $configArray["gatewayUrl"] = "https://test-gateway.mastercard.com/api/nvp";
                }
            }
            // API username in the format below where Merchant ID is the same as above
            $configArray["apiUsername"] = "merchant." . $configArray["merchantId"];            
            $this->merchant = new Merchant($configArray);
            $this->parser = new Parser($this->merchant);
        }

        public function Request($requestArray, $requestType="POST")
        {
            $requestUrl = $this->parser->FormRequestUrl($this->merchant);

            //This builds the request adding in the merchant name, api user and password.
            $request = $this->parser->ParseRequest($this->merchant, $requestArray);
            //Submit the transaction request to the payment server
            $response = $this->parser->SendTransaction($this->merchant, $request, $requestType);

            //Parse the response
            $result = $this->ParseData($response);

            return $result;
        }

        public function Checkout($orderArray)
        {
            
			$this->RectifyOrder($orderArray);
            $this->order = $this->Array2Dot($orderArray);

            $requestArray=array_merge(array("apiOperation" => "CREATE_CHECKOUT_SESSION", "order.description" => "TEST ORDER"), $this->order);
			//echo "<pre>";
            //var_dump($requestArray);exit();                                        
            $checkout = $this->Request($requestArray);
            if ($checkout['result'] == 'SUCCESS') {
                $_SESSION['eblskypay'] = $checkout;
                $url = parse_url($this->merchant->GetGatewayUrl());
                $url['host'] = str_replace('-', '.', 'easternbank.' . $url['host']);
                $url['path'] = "/checkout/entry/" . $checkout["session.id"];
                $this->redirect($url['scheme'] . '://' . $url['host'] . $url['path']);
            }
            return $checkout;
        }

        public function RetrieveOrder($orderID)
        {
            $requestArray = array(
                "apiOperation" => "RETRIEVE_ORDER",
                "order.id" => $orderID
            );

            return $this->Request($requestArray);
        }

        public function VoidTransaction($orderID, $transactionID)
        {
            $requestArray = array (
                "apiOperation" => "VOID",
                "order.id" => $orderID,
                "transaction.targetTransactionId" => $transactionID,
                "transaction.id" => 'VOID-' . $transactionID
            );

            return $this->Request($requestArray);
        }

        // function for removing unnecessary data
        // basically it removes single dimension data from array
        public function RectifyOrder(&$orderArray)
        {
            foreach ($orderArray as $key=>$value) {
                if (!is_array($value)) {
                    unset($orderArray[$key]);
                }
            }
        }

        // array to dot notation
        public function Array2Dot($dataArray)
        {
            $recursiveDataArray = new RecursiveIteratorIterator(new RecursiveArrayIterator($dataArray));
            $result = array();
            foreach ($recursiveDataArray as $leafValue) {
                $keys = array();
                foreach (range(0, $recursiveDataArray->getDepth()) as $depth) {
                    $keys[] = $recursiveDataArray->getSubIterator($depth)->key();
                }
                $result[ join('.', $keys) ] = $leafValue;
            }
            return $result;
        }

        //convert nvp data to array
        public function ParseData($string)
        {
            $array=array();
            $pairArray = array();
            $param = array();
            if (strlen($string) != 0) {
                $pairArray = explode("&", $string);
                foreach ($pairArray as $pair) {
                    $param = explode("=", $pair);
                    $array[urldecode($param[0])] = urldecode($param[1]);
                }
            }
            return $array;
        }

        public function redirect($newURL)
        {
            header('Location: ' . $newURL);
            die();
        }

        public function pr($data)
        {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
    }
