#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ESP32Servo.h>

#define RST_PIN 2 // Pino RST
#define SS_PIN 5  // Pino SDA
MFRC522 mfrc522(SS_PIN, RST_PIN); // Cria uma instância do leitor RC522

#define pinoSensor1 32 
#define pinoSensor2 34
#define pinoSensor3 35

bool estadoAnteriorSensor1 = HIGH;
bool estadoAnteriorSensor2 = HIGH;
bool estadoAnteriorSensor3 = HIGH;

const int pinoVermelho = 25;
const int pinoVerde = 33;

Servo myServo;
const int servoPin = 13;

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

  pinMode(pinoSensor1, INPUT);
  pinMode(pinoSensor2, INPUT);
  pinMode(pinoSensor3, INPUT);

  pinMode(pinoVermelho, OUTPUT);
  pinMode(pinoVerde, OUTPUT);

  myServo.attach(servoPin);

  SPI.begin();          // Inicializa a comunicação SPI
  mfrc522.PCD_Init();   // Inicializa o RC522
  Serial.println();
  Serial.println("Aproxime o cartão do leitor...");

}

void loop() {
  digitalWrite(pinoVermelho, HIGH);

  myServo.write(105);

  bool estadoAtualSensor1 = digitalRead(pinoSensor1);
  bool estadoAtualSensor2 = digitalRead(pinoSensor2);
  bool estadoAtualSensor3 = digitalRead(pinoSensor3);

 if (mfrc522.PICC_IsNewCardPresent()) {

    // Verifica se conseguimos ler o cartão
    if ( !mfrc522.PICC_ReadCardSerial()) {
      return;
    }

    // Salva o UID do cartão em uma string
    String uidString = "";

    for (byte i = 0; i < mfrc522.uid.size; i++) {
      if (mfrc522.uid.uidByte[i] < 0x10) {
        uidString += "0"; // Adiciona um zero à esquerda se o byte for menor que 0x10
      }
      
      uidString += String(mfrc522.uid.uidByte[i], HEX);

      if (i < mfrc522.uid.size - 1) {
        uidString += " "; // Adiciona um espaço entre os bytes
      } 
    }
  
    uidString.toUpperCase(); // Converte para maiúsculas
    Serial.println("UID do cartão: " + uidString); // Exibe o UID do cartão
    postDataCartao(uidString);

    // Pare o uso do cartão
    mfrc522.PICC_HaltA();
  }

  if(estadoAtualSensor1 != estadoAnteriorSensor1){

    delay(50);

    if(estadoAtualSensor1 == LOW){
      postDataSensor(1,LOW);
      Serial.println("Sensor 1 ativado");
    }
    else {
      postDataSensor(1,HIGH);
      Serial.println("Sensor 1 desativado");
    }

    estadoAnteriorSensor1 = estadoAtualSensor1;
  }

  if(estadoAtualSensor2 != estadoAnteriorSensor2){

    delay(50);

    if(estadoAtualSensor2 == LOW){
      postDataSensor(2,LOW);
      Serial.println("Sensor 2 ativado");
    }
    else {
      postDataSensor(2,HIGH);
      Serial.println("Sensor 2 desativado");
    }

    estadoAnteriorSensor2 = estadoAtualSensor2;
  }

  if(estadoAtualSensor3 != estadoAnteriorSensor3){

    delay(50);

    if(estadoAtualSensor3 == LOW){
      postDataSensor(3,LOW);
      Serial.println("Sensor 3 ativado");
    }
    else {
      postDataSensor(3,HIGH);
      Serial.println("Sensor 3 desativado");
    }

    estadoAnteriorSensor3 = estadoAtualSensor3;
  }


}

void postDataCartao(String NS_Cartao){

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin("http://192.168.15.11/api-estacionamento/api/cartoes/index.php");
    http.addHeader("Content-Type", "application/json");  // Define o tipo de conteúdo

    // Cria o JSON no formato esperado pela API
    String httpRequestData = "{\"NS_Cartao\":\"" + NS_Cartao + "\"}";

    int httpResponseCode = http.POST(httpRequestData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(httpResponseCode);
      Serial.println(response);

      if (response == "1") {
        Serial.println("Movimentação criada com sucesso.");

        digitalWrite(pinoVermelho, LOW);
        digitalWrite(pinoVerde, HIGH);

        myServo.write(30);
        delay(3000);
        myServo.write(105);

        digitalWrite(pinoVermelho, HIGH);
        digitalWrite(pinoVerde, LOW);

      } else if (response == "0") {
          Serial.println("Novo cartão adicionado.");
      } else if (response == "2") {
          Serial.println("Nenhum nome de cartão pendente encontrado. Envie o nome primeiro.");
          digitalWrite(pinoVermelho, LOW);
          delay(500);
          digitalWrite(pinoVermelho, HIGH);
          delay(500);
          digitalWrite(pinoVermelho, LOW);
          delay(500);
          digitalWrite(pinoVermelho, HIGH);
      } else {
        Serial.println("Erro ao processar a resposta da API.");
      }

    } else {
      Serial.print("Erro na requisição: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
}

void postDataSensor(int sensorNumero, bool ativado) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin("http://192.168.15.11/api-estacionamento/api/movimentacao/index.php");
    http.addHeader("Content-Type", "application/json");

    String status = ativado ? "ativado" : "desativado";
    String httpRequestData = "{\"sensorNumero\":" + String(sensorNumero) + ", \"status\":\"" + status + "\"}";

    int httpResponseCode = http.POST(httpRequestData);
    if (httpResponseCode > 0) {
      Serial.printf("Resposta da API para sensor %d: %s\n", sensorNumero, http.getString().c_str());
    } else {
      Serial.print("Erro na requisição do sensor ");
      Serial.print(sensorNumero);
      Serial.print(": ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
}