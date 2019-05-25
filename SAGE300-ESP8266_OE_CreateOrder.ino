//**************************************************************************************************************************
// Code by Martin Viljoen 2019-05-24
// Post an o/e order to Sage 300 via the Sage 300 Web-API
// This sketch is making use of external ESP8266 libraries 
// <ESP8266WiFi.h> & <ESP8266HTTPClient.h>
//**************************************************************************************************************************


#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

const char* ssid     = "Your_AP_SSID";
const char* password = "Your_AP_Password";

void setup() {
  pinMode(0, INPUT_PULLUP); //flash button will create order
  pinMode(2, OUTPUT); //flash LED when creating order
  digitalWrite(2, HIGH);//Turn off  LED
  Serial.begin(115200);
  delay(10);

  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");  
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
 if(digitalRead(0)==LOW){ PostOrder(); }
}

void PostOrder()
{
Serial.println("Creating order"); 
digitalWrite(2, LOW);//Turn on  LED
//Setup Order header
String ACCOUNT_CODE ="1100";
String CUST_PO_NUMBER="PO000002";
String ORDER_DESC="Order Test Desc";
String ORDER_REFERENCE="Order Test Ref";
String CUSTOMER_SHIPTO_LOCATION="001";
String SHIP_VIA_CODE="CCT";
//Setup Order detail
String ORDER_COMMENT="This is a ESP9266 Test comment";
String fItem="A14010";
String QTY_ORDERED="1";
String QTY_BACKORDERED="1";
String QTY_COMITTED="0";
String QTY_SHIPPED="0";


 
 if(WiFi.status()== WL_CONNECTED){   //Check WiFi connection status
 
   HTTPClient http;    //Declare object of class HTTPClient
 
   http.begin("http://192.168.1.245:8080/sagemicro/esp/create_oe_order.php");      //Specify request destination
   http.addHeader("Content-Type", "application/x-www-form-urlencoded");

   String POSTDATA="";
   POSTDATA = POSTDATA + "ACCOUNT_CODE=" + ACCOUNT_CODE;
   POSTDATA = POSTDATA + "&CUST_PO_NUMBER=" + CUST_PO_NUMBER;
   POSTDATA = POSTDATA + "&ORDER_DESC=" + ORDER_DESC;
   POSTDATA = POSTDATA + "&ORDER_REFERENCE=" + ORDER_REFERENCE;
   POSTDATA = POSTDATA + "&CUSTOMER_SHIPTO_LOCATION=" + CUSTOMER_SHIPTO_LOCATION;
   POSTDATA = POSTDATA + "&SHIP_VIA_CODE=" + SHIP_VIA_CODE;
   POSTDATA = POSTDATA + "&ORDER_COMMENT=" + ORDER_COMMENT;
   POSTDATA = POSTDATA + "&fItem=" + fItem;
   POSTDATA = POSTDATA + "&QTY_ORDERED=" + QTY_ORDERED;
   POSTDATA = POSTDATA + "&QTY_BACKORDERED=" + QTY_BACKORDERED;
   POSTDATA = POSTDATA + "&QTY_COMITTED=" + QTY_COMITTED;
   POSTDATA = POSTDATA + "&QTY_SHIPPED=" + QTY_SHIPPED;

   
   int httpCode = http.POST(POSTDATA);   //Send the request
   String payload = http.getString();                  //Get the response payload
 
   Serial.println(httpCode);   //Print HTTP return code
   Serial.println(payload);    //Print request response payload
 
   http.end();  //Close connection
 
 }else{
 
    Serial.println("Error in WiFi connection");   
 
 }
Serial.println("Done creating order, wait 2 seconds before creating another one."); 
 delay(2000);  //Send a request every 2 seconds 
 digitalWrite(2, HIGH);//Turn off  LED
}
