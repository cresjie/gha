//Step 2


$payRequest = new PayRequest();

$receiver = array();
$receiver[0] = new Receiver();
$receiver[0]->amount = "1.00";
$receiver[0]->email = "platfo_1255170694_biz@gmail.com";
 				
$receiver[1] = new Receiver();
$receiver[1]->amount = "2.00";
$receiver[1]->email = "platfo_1255612361_per@gmail.com";
$receiver[1]->primary = "true";
$receiverList = new ReceiverList($receiver);
$payRequest->receiverList = $receiverList;

$requestEnvelope = new RequestEnvelope("en_US");
$payRequest->requestEnvelope = $requestEnvelope; 
$payRequest->actionType = "PAY";
$payRequest->cancelUrl = "{CANCEL_URL}";
$payRequest->returnUrl = "{RETURN_URL}";
$payRequest->currencyCode = "USD";
$payRequest->ipnNotificationUrl = "http://replaceIpnUrl.com";

$sdkConfig = array(
	"mode" => "sandbox",
	"acct1.UserName" => "{API_USER}",
	"acct1.Password" => "{API_PWD}",
	"acct1.Signature" => "{API_SIGNATURE}",
	"acct1.AppId" => "APP-80W284485P519543T"
);

$adaptivePaymentsService = new AdaptivePaymentsService($sdkConfig);
$payResponse = $adaptivePaymentsService->Pay($payRequest); 
 