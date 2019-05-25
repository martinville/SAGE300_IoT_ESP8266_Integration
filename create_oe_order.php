<?php
//**************************************************************************************************************************
// Create an order in Sage 300 via the Sage 300 Web-API
// This is for sample purposes only
// Security Risk: Please escape input and add username and password control before placing into production.
//**************************************************************************************************************************

//Order Header
if(!empty($_POST["ACCOUNT_CODE"])){$ACCOUNT_CODE=$_POST["ACCOUNT_CODE"];}else{$ACCOUNT_CODE='';}
if(!empty($_POST["CUST_PO_NUMBER"])){$CUST_PO_NUMBER=$_POST["CUST_PO_NUMBER"];}else{$CUST_PO_NUMBER='';}
if(!empty($_POST["ORDER_DESC"])){$ORDER_DESC=$_POST["ORDER_DESC"];}else{$ORDER_DESC='';}
if(!empty($_POST["ORDER_REFERENCE"])){$ORDER_REFERENCE=$_POST["ORDER_REFERENCE"];}else{$ORDER_REFERENCE='';}
if(!empty($_POST["CUSTOMER_SHIPTO_LOCATION"])){$CUSTOMER_SHIPTO_LOCATION=$_POST["CUSTOMER_SHIPTO_LOCATION"];}else{$CUSTOMER_SHIPTO_LOCATION='';}
if(!empty($_POST["SHIP_VIA_CODE"])){$SHIP_VIA_CODE=$_POST["SHIP_VIA_CODE"];}else{$SHIP_VIA_CODE='';}
if(!empty($_POST["ORDER_COMMENT"])){$ORDER_COMMENT=$_POST["ORDER_COMMENT"];}else{$ORDER_COMMENT='';}

//Order Detail
if(!empty($_POST["fItem"])){$fItem=$_POST["fItem"];}else{$fItem='';}
if(!empty($_POST["QTY_ORDERED"])){$QTY_ORDERED=$_POST["QTY_ORDERED"];}else{$QTY_ORDERED='0';}
if(!empty($_POST["QTY_BACKORDERED"])){$QTY_BACKORDERED=$_POST["QTY_BACKORDERED"];}else{$QTY_BACKORDERED='0';}
if(!empty($_POST["QTY_COMITTED"])){$QTY_COMITTED=$_POST["QTY_COMITTED"];}else{$QTY_COMITTED='0';}
if(!empty($_POST["QTY_SHIPPED"])){$QTY_SHIPPED=$_POST["QTY_SHIPPED"];}else{$QTY_SHIPPED='0';}



//Declare  Arrays
$ItemData='';  
$allItemData=array(); 
$result='';


//Add item data to $ItemData
$ItemData = array(
				'Item' => $fItem ,
				'QuantityOrdered' => (int)$QTY_ORDERED,
				'QuantityBackordered' => (int)$QTY_BACKORDERED,
				'QuantityCommitted' => (int)$QTY_COMITTED,
				'QuantityShipped' => (int)$QTY_SHIPPED
			);	
//Add $ItemData row set to nested $allItemData		
$allItemData[] = $ItemData;	



//Setup Order Header
$ACCOUNT_CODE='1100';
$CUST_PO_NUMBER='PO00001';
$ORDER_DESC='Order description';
$ORDER_REFERENCE='Order Reference';
$CUSTOMER_SHIPTO_LOCATION='001';
$SHIP_VIA_CODE='CCT';
$ORDER_COMMENT='This is a comment';

//Add order variables to array that will which will later be formatted into JSON
$data = array(
'CustomerNumber' => $ACCOUNT_CODE,
'PurchaseOrderNumber' => $CUST_PO_NUMBER,
'OrderDescription' => $ORDER_DESC,
'OrderReference' => $ORDER_REFERENCE,			
'ShipToLocationCode' => $CUSTOMER_SHIPTO_LOCATION,
'ShipViaCode' => $SHIP_VIA_CODE,
'OrderComment' => $ORDER_COMMENT,		
//Add Nested Item Data to the Order
'OrderDetails' => $allItemData
);

//Format Arrays into JSON 
$payload = json_encode($data);

//POST the JSON DATA USING CURL
	
	//Endpoint, Username & Password setup
	$ENDPOINT_URL = 'http://localhost/Sage300WebApi/v1.0/-/SAMINC/OE/OEOrders';
	$ENDPOINT_USER='WEBAPI';
	$ENDPOINT_PASS='WEBAPI';	

			$CurlHeader = curl_init($ENDPOINT_URL);		
			//set user and pass
			curl_setopt($CurlHeader, CURLOPT_USERPWD, "$ENDPOINT_USER:$ENDPOINT_PASS"); //Your credentials goes here
			//attach encoded JSON string to the POST fields
			curl_setopt($CurlHeader, CURLOPT_POSTFIELDS, $payload);
			//set the content type to application/json
			curl_setopt($CurlHeader, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			//return response instead of outputting
			curl_setopt($CurlHeader, CURLOPT_RETURNTRANSFER, true);	
			//execute the POST and put response into $result
			$result = curl_exec($CurlHeader);				
			//close cURL resource
			curl_close($CurlHeader);

		//Check response if errors exist else print successfull response
		$JSON_OBJ='{}';
		$JSON_OBJ = json_decode($result,true); //Decode data from URL into JSON Format
		$ResponseSuccessFailed=0;
		//Failed
		if(isset($JSON_OBJ["error"])){
			$ResponseSuccessFailed=1;
			$Error_Check = $JSON_OBJ["error"]; //Extractet field value with  ObjectName:error
			echo $Error_Check['message']['value'];
		}	
		//Success
		if(isset($JSON_OBJ["OrderNumber"])){
			$ResponseSuccessFailed=1;
			$SystemOrderNumber = $JSON_OBJ["OrderNumber"]; //Extractet field value 		
			$OrderUniquifier = $JSON_OBJ["OrderUniquifier"]; //Extractet field value 
			echo 'SUCCESS=ORDER CREATED&ORDERNUMBER=' . $SystemOrderNumber;
		}	
		//Produce unknown error if no response were found.
		if($ResponseSuccessFailed==0;)
		{
			echo 'No respponse from web-api';
		}

?>
