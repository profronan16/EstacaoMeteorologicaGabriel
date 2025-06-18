// Pin definitions
#define Hall 12        //  Pino digital 2

// Constants using #define
#define VOLTAGE 3.3
#define RESOLUTION 4095

// Constants definitions
int pin = A0;
float valor =0;
int Winddir =0;
String direcao;

//others
float wd;
float wds;
float ar;

const float pi = 3.14159265;           // Numero pi
int period = 5000;               // Tempo de medida(miliseconds)
int delaytime = 2000;             // Time between samples (miliseconds)
int radius = 88;      // Raio do anemometro(mm)

// Variable definitions
unsigned int Sample = 0;   // Sample number
unsigned int counter = 0; // magnet counter for sensor
unsigned int RPM = 0;          // Revolutions per minute
float speedwind = 0;         // Wind speed (m/s)
float windspeed = 0;           // Wind speed (km/h)

void setup()
{
  // Set the pins
  pinMode(Hall, INPUT);
  digitalWrite(Hall, HIGH);     //internall pull-up active
    
  //Start serial 
  Serial.begin(9600);       // sets the serial port to 9600 baud
  }

void loop()
{
  // Anemômetro
  Sample++;
  Serial.print(Sample);
  Serial.print(": Iniciar medição...");
  windvelocity();
  Serial.println("   finalizada.");
  Serial.print("Counter: ");
  Serial.print(counter);
  Serial.print(";  RPM: ");
  RPMcalc();
  Serial.print(RPM);
  Serial.print(";  Wind speed: ");
  
//*****************************************************************
//print m/s  
  WindSpeed();
  Serial.print(windspeed);
  Serial.print(" [m/s] ");              
  
//*****************************************************************
//print km/h  
  SpeedWind();
  Serial.print(speedwind);
  Serial.print(" [km/h] ");  
  Serial.println();


  delay(delaytime);                        //delay between prints

// Indicador de Direção do Vento
valor = analogRead(pin) * (VOLTAGE / RESOLUTION);
 
Serial.print("leitura do sensor :");
Serial.print(valor);
Serial.println(" volt");

Serial.print("leitura bruta: ");
Serial.println(analogRead(pin));

if (valor <= 0.27) {
Winddir = 315;
direcao = "Noroeste";
}
else if (valor <= 0.32) { 
Winddir = 270;
direcao = "Oeste";
}
else if (valor <= 0.38) { 
Winddir = 225;
direcao = "Sudoeste";
}
else if (valor <= 0.45) { 
Winddir = 180;
direcao = "Sul";
}
else if (valor <= 0.57) { 
Winddir = 135;
direcao = "Sudeste";
}
else if (valor <= 0.75) { 
Winddir = 90;
direcao = "Leste";
}
else if (valor <= 1.25) {  
Winddir = 45;
direcao = "Nordeste";
}
else {  
Winddir = 000;
direcao = "Norte";
}
 Serial.print("Direção a :");
 Serial.print(Winddir);
 Serial.print(" graus");
 Serial.print(" / ");
 Serial.print(direcao);
 Serial.println("\n");
 delay (500);
}


// Measure wind speed
void windvelocity(){
  speedwind = 0;
  windspeed = 0;
  
  counter = 0;  
  attachInterrupt(Hall, addcount, RISING);
  unsigned long millis();       
  long startTime = millis();
  while(millis() < startTime + period) {
  }
}

 void winddir_2(){
 for (int i = 0; i < 20; i++) {
 wd = analogRead(pin); //34 esp ou A0 arduino
 Serial.println(wd);
 wds=wds+wd;
 delay(50);
 }
 ar = wds / 20;
 int wdir;
 if(ar >= 0 && ar <=64 )  {
 wdir = 315;
 }
 if (ar >= 65  && ar <= 100) {
 wdir = 270;
 }
 if (ar >= 101  && ar <=200) {
 wdir = 225;
 }
 if (ar >= 201 && ar <= 300) {
 wdir = 180;
 }
 if (ar >= 301 && ar <= 400){
 wdir = 135;
 }
 if (ar >= 401 && ar <= 480) {
 wdir = 90;
 }
 if (ar >= 481 && ar <= 580) {
 wdir = 45;
 }
 if (ar >= 581 && ar <= 699 ) {
 wdir= 0;
 }
 Serial.print("Leitura Analog Media (2a versão) : ");
 Serial.print(ar);
Serial.print(" - Direção : ");
 Serial.println(wdir);
 delay(1000);
 ar=0;
 wd=0;
 wds=0;
 }

void RPMcalc(){
  RPM=((counter)*60)/(period/1000);  // Calculate revolutions per minute (RPM)
}

void WindSpeed(){
  windspeed = ((2 * pi * radius * RPM)/60) / 1000;  // Calculate wind speed on m/s
}

void SpeedWind(){
  speedwind = (((2 * pi * radius * RPM)/60) / 1000)*3.6;  // Calculate wind speed on km/h
}

void addcount(){
  counter++;
}
