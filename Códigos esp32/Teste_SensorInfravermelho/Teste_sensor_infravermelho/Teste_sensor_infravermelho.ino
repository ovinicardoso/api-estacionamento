#define pinoSensor1 32 
#define pinoSensor2 34
#define pinoSensor3 35

bool estadoAnteriorSensor1 = HIGH;
bool estadoAnteriorSensor2 = HIGH;
bool estadoAnteriorSensor3 = HIGH;

unsigned long debouceDelay = 50;

void setup() {
  pinMode(pinoSensor1, INPUT);
  pinMode(pinoSensor2, INPUT);
  pinMode(pinoSensor3, INPUT);
  Serial.begin(9600);
}

void loop() {
  bool estadoAtualSensor1 = digitalRead(pinoSensor1);
  bool estadoAtualSensor2 = digitalRead(pinoSensor2);
  bool estadoAtualSensor3 = digitalRead(pinoSensor3);

  if(estadoAtualSensor1 != estadoAnteriorSensor1){

    delay(debouceDelay);

    if(estadoAtualSensor1 == LOW){
      Serial.println("Sensor 1 ativado");
    }
    else {
      Serial.println("Sensor 1 desativado");
    }

    estadoAnteriorSensor1 = estadoAtualSensor1;
  }
  

  if(estadoAtualSensor2 != estadoAnteriorSensor2){

    delay(debouceDelay);

    if(estadoAtualSensor2 == LOW){
      Serial.println("Sensor 2 ativado");
    }
    else {
      Serial.println("Sensor 2 desativado");
    }

    estadoAnteriorSensor2 = estadoAtualSensor2;
  }

  if(estadoAtualSensor3 != estadoAnteriorSensor3){

    delay(debouceDelay);

    if(estadoAtualSensor3 == LOW){
      Serial.println("Sensor 3 ativado");
    }
    else {
      Serial.println("Sensor 3 desativado");
    }

    estadoAnteriorSensor3 = estadoAtualSensor3;
  }
}
