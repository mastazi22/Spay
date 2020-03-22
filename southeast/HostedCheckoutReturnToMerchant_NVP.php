<?php


/* Main processing page

This is the best NVP example and most useful source code to view if wanting to build an integration. If wanting to utilise REST,
see the Hosted Checkout Return to Merchant REST version.

1. Create one MerchantConfiguration object for each merchant ID.
2. Create one Parser object.
3. Generate a unique Order ID with a method of your choice to use to identify the order. 
4. Set a receipt page to be re-directed to on order completion by using the data-complete tag 
   (alternatively, a function can be specified here).
5. Call Parser object FormRequest method to create a payment page session by making 
   a CREATE_CHECKOUT_SESSION apiOperation request that will be sent to the payment server.
   A successful request will return a session.id and and successIndicator.
6. Store the sussessIndicator and order ID for later use in the receipt page
7. Pass the session.id to the Checkout.configure function which will be called next.
8. When the customer selects the "Payment" button, either call Checkout.showLightbox()
   or  Checkout.showPaymentPage() (note both examples are shown below, but you would only
   use one of these methods).
9. The receipt page compares the sussessIndicator and the resultIndicator to make sure the transaction was successfull, and if
   required, full order details can be retrieved by calling the RETRIEVE_ORDER apiOperation request passing the Order ID. 

*/

//session_unset();
session_start();

include "api_lib.php";
include "configuration.php";
include "connection.php";
include("../includes/configure.php");
include("../includes/session_handler.php");
//Ensure this is the first invokation of this page

if(isset($_SESSION['ORDER_DETAILS']['eblID']) and $_SESSION['ORDER_DETAILS']['eblID'] !="") {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (array_key_exists("submit", $_POST))
            unset($_POST["submit"]);
        $order_amount = $_POST["order_amount"];
        $order_currency = $_POST["order_currency"];
        $customer_receipt_email = "'" . $_POST["customer_receipt_email"] . "'";

        //Creates the Merchant Object from config. If you are using multiple merchant ID's,
        // you can pass in another configArray each time, instead of using the one from configuration.php
        $merchantObj = new Merchant($configArray);

        // The Parser object is used to process the response from the gateway and handle the connections
        // and uses connection.php
        $parserObj = new Parser($merchantObj);

        //The Gateway URL can be set by using the following function, or the
        //value can be set in configuration.php
        //$merchantObj->SetGatewayUrl("https://secure.uat.tnspayments.com/api/nvp");
        $requestUrl = $parserObj->FormRequestUrl($merchantObj);

        //This is a library if useful functions
        $new_api_lib = new api_lib;

        //Use a method to create a unique Order ID. Store this for later use in the receipt page or receipt function.
        $order_id = $new_api_lib->getRandomString(10);

        //Form the array to obtain the checkout session ID.
        $request_assoc_array = array("apiOperation" => "CREATE_CHECKOUT_SESSION",
            "order.id" => $order_id,
            "order.amount" => $order_amount,
            "order.currency" => $order_currency
        );

        //This builds the request adding in the merchant name, api user and password.
        $request = $parserObj->ParseRequest($merchantObj, $request_assoc_array);

        //Submit the transaction request to the payment server
        $response = $parserObj->SendTransaction($merchantObj, $request);

        //Parse the response
        $parsed_array = $new_api_lib->parse_from_nvp($response);

        //Store the successIndicator for later use in the receipt page or receipt function.
        $successIndicator = $parsed_array['successIndicator'];

        //The session ID is passed to the Checkout.configure() function below.
        $session_id = $parsed_array['session.id'];

        //Store the variables in the session, or a database could be used for example
        $_SESSION['successIndicator'] = $successIndicator;
        $_SESSION['orderID'] = $order_id;

        $merchantID = "'" . $_SESSION['ORDER_DETAILS']['eblID'] . "'";//$merchantObj->GetMerchantId()
		 $sp_order_id = $_SESSION['ORDER_DETAILS']['order_id'];
		$sql_query = mysqli_query($GLOBALS["___mysqli_sm"],"UPDATE sp_epay SET bank_tx_id='" . $order_id . "' WHERE order_id='" . $sp_order_id . "'");
    };
}else{
    exit("Sorry Service Not Available Please Contact With shurjoPay Support");
}

?>

<html>

<head>

    <link rel="stylesheet" type="text/css" href="../css/paymentstyle.css"/>
    <script src="https://southeastbank.gateway.mastercard.com/checkout/version/48/checkout.js"
            data-error="errorCallback"
            data-cancel="https://shurjopay.com/southeash/Cancelled.php"
            data-complete="https://shurjopay.com/southeash/Receipt_NVP.php"
    >	        
    </script>

    <script type="text/javascript">
        function errorCallback(error) {
            alert(JSON.stringify(error));
        }

        function completeCallback(resultIndicator, sessionVersion) {
            alert("Result Indicator");
            alert(JSON.stringify(resultIndicator));
            alert("Session Version:");
            alert(JSON.stringify(sessionVersion));
            alert("Successful Payment");
        }

        function cancelCallback() {
            alert('Payment cancelled');

        }

        Checkout.configure({
            merchant: <?php echo $merchantID; ?>,
            order: {
                amount: <?php echo json_encode($order_amount); ?>,
                currency: <?php echo json_encode($order_currency); ?>,
                description: 'shurjoPay',
                id: <?php echo json_encode($order_id); ?>,
                item: {
                    brand: 'Mastercard',
                    description: 'shurjoPay',
                    name: 'Testcheckout',
                    quantity: '1',
                    unitPrice: '1.00',
                    unitTaxAmount: '1.00'
                }
            },
            billing: {
                address: {
                    street: '100 Dilkusha C/A',
                    stateProvince: 'Dhaka',
                    city: 'Dhaka',
                    company: 'Eastern Bank Limited',
                    postcodeZip: '1000',
                    country: 'BGD'
                }
            },
            customer: {
                email: <?php echo $customer_receipt_email; ?>
            },
            interaction: {
                merchant: {
                    name: 'shurjoPay',
                    address: {
                        line1: 'Mohakali(DOSH)',
                        line2: 'Mohakali'
                    },
                    logo: 'https://shurjopay.com/images/shurjopay-logo.png'
                }
            },
            session: {
                id: <?php echo json_encode($session_id); ?>
            }
        });

    </script>
</head>

<body>
<script>
    Checkout.showPaymentPage();
</script>

</body>
</html>
