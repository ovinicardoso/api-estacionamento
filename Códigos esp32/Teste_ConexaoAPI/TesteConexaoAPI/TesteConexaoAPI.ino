#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "VIVOFIBRA-1361";
const char* password = "ar3vXs5s9u";

void setup() {
    Serial.begin(9600);
    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.println("Conectando ao WiFi...");
    }
    Serial.println("Conectado ao WiFi!");
}

void loop() {
  postData("A3 B2 C1 D0");
  delay(10000);
}

void postData(String codCartao){

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin("http://192.168.15.9/api-estacionamento/api/cartoes/index.php");
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Define o tipo de conteúdo

    String httpRequestData = "codCartao=" + codCartao;  // Dados que você quer enviar
    int httpResponseCode = http.POST(httpRequestData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(httpResponseCode);
      Serial.println(response);
    } else {
      Serial.print("Erro na requisição: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
}