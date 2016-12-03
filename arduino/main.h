#include <dht11.h>
#include <Arduino.h>
#include <SoftwareSerial.h>
#include <ESP8266_Simple.h>

//--SSID--//
#define ESP8266_SSID  "HUAWEI P8 lite"
//--PASSWORD SSID--//
#define ESP8266_PASS  "12345678"

//--RX,TX--//
ESP8266_Simple wifi(8,9);
dht11 tempLogger;

int temp = 0;
int humidity = 0;
int relayOnline = 0;

void setup()
{
  //--IF Serial open, write self state--//
  Serial.begin(9600);
  Serial.println("Home Control Online is starting...");
  //--Start comm with WIFI module--//
  wifi.begin(9600);
  
  wifi.setupAsWifiStation(ESP8266_SSID, ESP8266_PASS, &Serial);
   
  static ESP8266_HttpServerHandler myServerHandlers[] = {  
    { PSTR("GET /relayToggle"), relayToggle },    
    { PSTR("GET "),    index   } 
  };
  //Start HTTP Server <MAIN>
  wifi.startHttpServer(80, myServerHandlers, sizeof(myServerHandlers), 250, &Serial);
  Serial.println();

  pinMode(12, OUTPUT);
  pinMode(11, OUTPUT);
}

int i = 0;
void loop()
{        
  //--Delay without stop--//
  if(i%2000 == 0)
  tempLogger.read(10);
  //--HTTP REQUEST--//
  wifi.serveHttpRequest(); 
  i++;
  return;  
}

//--INDEX--//
unsigned long index(char *buffer, int bufferLength)
{  
  //--Set Temp and Humidity--//
  temp = tempLogger.temperature;
  humidity = tempLogger.humidity;
  //--END--//
  memset(buffer, 0, bufferLength); 
  //--Create String And Save To Ram--//
  strncpy_P(buffer, PSTR(""), bufferLength-strlen(buffer));
  ultoa(temp,buffer+strlen(buffer),10);
  strncpy_P(buffer+strlen(buffer), PSTR(";"), bufferLength-strlen(buffer));
  ultoa(humidity,buffer+strlen(buffer),10);
  strncpy_P(buffer+strlen(buffer), PSTR(";"), bufferLength-strlen(buffer));
  ultoa(relayOnline,buffer+strlen(buffer),10);
  //--END--//
  return ESP8266_HTML | 404;
}

//--RELAY--//
//--TOGGLE--//
unsigned long relayToggle(char *buffer, int bufferLength)
{  

    if(relayOnline == 0)
    {
      digitalWrite(11, HIGH);
      digitalWrite(12, HIGH);
      strncpy_P(buffer, PSTR("ON"), bufferLength);
      relayOnline = 1; 
    }
    else
    {
      digitalWrite(11, LOW);
      digitalWrite(12, LOW);
      strncpy_P(buffer, PSTR("OFF"), bufferLength);
      relayOnline = 0;
    }
    return ESP8266_HTML | 404;
}