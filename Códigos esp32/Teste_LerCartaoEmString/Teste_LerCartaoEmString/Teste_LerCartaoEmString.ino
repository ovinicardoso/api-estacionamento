#include <SPI.h>
#include <MFRC522.h>

// Defina os pinos que você escolheu
#define RST_PIN 2 // Pino RST
#define SS_PIN 5  // Pino SDA

MFRC522 mfrc522(SS_PIN, RST_PIN); // Cria uma instância do leitor RC522

void setup() {
  Serial.begin(9600); // Inicializa a comunicação serial
  SPI.begin();          // Inicializa a comunicação SPI
  mfrc522.PCD_Init();   // Inicializa o RC522
  Serial.println();
  Serial.println("Aproxime o cartão do leitor...");
}

void loop() {
  // Verifica se um novo cartão está disponível
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

    // Pare o uso do cartão
    mfrc522.PICC_HaltA();
  }

}
