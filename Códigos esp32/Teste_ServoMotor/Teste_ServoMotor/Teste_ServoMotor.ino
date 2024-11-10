#include <ESP32Servo.h>

// Cria um objeto servo para controlar o SG90
Servo myServo;

// Pino onde o sinal do servo está conectado
const int servoPin = 13;

void setup() {
  // Inicia a comunicação serial para depuração
  Serial.begin(9600);
  
  // Anexa o servo ao pino
  myServo.attach(servoPin);
}

void loop() {
  myServo.write(30);
}