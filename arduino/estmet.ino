/***************************************************
* Techeonics adaptado para ESP32 com DHT22
* Umidade e Temperatura via Serial + envio HTTP
****************************************************/
#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

// Configurações do DHT
#define DHTPIN 2         // GPIO 2 do ESP32
#define DHTTYPE DHT22
DHT_Unified dht(DHTPIN, DHTTYPE);
uint32_t delayMS;

// Configurações Wi-Fi e servidor
const char* ssid = "seussid";
const char* password = "suasenha";
const int id = 123456;
const String serverUrl = "http://seuipoudominio/estacao/logica.php";

void setup() {
  Serial.begin(115200);
  dht.begin();

  // Detalhes do sensor
  sensor_t sensor;
  dht.temperature().getSensor(&sensor);
  delayMS = sensor.min_delay / 1000;

  Serial.print("Conectando-se ao Wi-Fi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConectado ao Wi-Fi");
}

void loop() {
  delay(delayMS);

  sensors_event_t event;
  float temperatura = NAN;
  float umidade = NAN;

  // Leitura da Temperatura
  dht.temperature().getEvent(&event);
  if (!isnan(event.temperature)) {
    temperatura = event.temperature;
    Serial.print("Temperatura: ");
    Serial.print(temperatura);
    Serial.println(" °C");
  } else {
    Serial.println("Erro na leitura da Temperatura!");
  }

  // Leitura da Umidade
  dht.humidity().getEvent(&event);
  if (!isnan(event.relative_humidity)) {
    umidade = event.relative_humidity;
    Serial.print("Umidade: ");
    Serial.print(umidade);
    Serial.println(" %");
  } else {
    Serial.println("Erro na leitura da Umidade!");
  }

  // Envio para servidor se Wi-Fi conectado
  if (WiFi.status() == WL_CONNECTED && !isnan(temperatura) && !isnan(umidade)) {
    HTTPClient http;
    String url = serverUrl + "?id=" + id + "&temp=" + temperatura + "&umid=" + umidade;

    Serial.println("Enviando dados para: " + url);
    http.begin(url);
    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      Serial.println("Resposta do servidor: " + http.getString());
    } else {
      Serial.println("Erro na solicitação HTTP");
    }

    http.end();
  }

  delay(30000);
}
