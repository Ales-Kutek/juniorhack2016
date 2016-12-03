#include <dht11.h>
#include <Arduino.h>
#include <SoftwareSerial.h>
#include <ESP8266_Simple.h>

#define ESP8266_SSID  "Xperia Z5_82b3"
#define ESP8266_PASS  "llllllll"

ESP8266_Simple wifi(8,9);
dht11 tempLogger;

int temp = 0;
int humidity = 0;

void setup()
{
  Serial.begin(9600);
  Serial.println("Home Control Online is starting...");
  wifi.begin(9600);
  
  wifi.setupAsWifiStation(ESP8266_SSID, ESP8266_PASS, &Serial);
   
  static ESP8266_HttpServerHandler myServerHandlers[] = {  
    { PSTR("GET "),        index    } 
  };
  //Start HTTP Server <MAIN>
  wifi.startHttpServer(80, myServerHandlers, sizeof(myServerHandlers), 250, &Serial);
  Serial.println();
}


void loop()
{        
  //Requestne http data
  //tempLogger.read(10);
  wifi.serveHttpRequest(); 
  return;  
}

//--INDEX--//
unsigned long index(char *buffer, int bufferLength)
{  
  temp = tempLogger.temperature;
  memset(buffer, 0, bufferLength); 
  strncpy_P(buffer, PSTR(""), bufferLength-strlen(buffer));
  ultoa(temp,buffer+strlen(buffer),10);
   strncpy_P(buffer+strlen(buffer), PSTR("; "), bufferLength-strlen(buffer));  
  return ESP8266_HTML | 404;
}