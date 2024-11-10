const int pinoVermelho = 25;
const int pinoVerde = 33;

void setup() {
  pinMode(pinoVermelho, OUTPUT);
  pinMode(pinoVerde, OUTPUT);

}

void loop() {
  digitalWrite(pinoVermelho, HIGH);
  digitalWrite(pinoVerde, LOW);

  delay(5000);

  digitalWrite(pinoVermelho, LOW);
  digitalWrite(pinoVerde, HIGH);

  delay(5000);

}
