/**
 * servo controll program
 * 
 * @author dongyado<dongyado@gmail.com>
 */

#include<Servo.h>
#include <IRremote.h>

Servo unlockServo;
Servo phoneServo;

int RECV_PIN=11;
IRrecv irrecv(RECV_PIN);
decode_results results;

int pos = 0;

int maxPhonePos  = 90;
int maxUnlockPos = 30;

String comdata = "";

void setup() {
  
    Serial.begin(9600);
    irrecv.enableIRIn();
    
    // connect 
    unlockServo.attach(9);
    phoneServo.attach(10);
  
    //reset
    unlockServo.write(0);
    phoneServo.write(maxPhonePos);
    
    delay(200);
    
    // disconnect
    unlockServo.detach();
    phoneServo.detach();
}

/**
 * return key according the irremote code
 */
char returnKey(unsigned long value) {
      switch (value) {
        case 0xFF6897:
          return '0';
        case 0xFF30CF:
          return '1';
        case 0xFF18E7:
          return  '2';
        case 0xFF7A85:
        return  '3';
        case 0xFF10EF:
        return  '4';
        case 0xFF38C7:
        return  '5';
        case 0xFF5AA5:
        return  '6';
        case 0xFF42BD:
        return  '7';
        case 0xFF4AB5:
        return  '8';
        case 0xFF52AD:
        return  '9';
      }
}

// phone status
int phoneStatus = 0; // 0 - down, 1 - up

/**
 * phone operation
 */
void getPhone() {
        // connect 
        phoneServo.attach(10);

        // get the phone up
        if (phoneStatus == 0) {
            for (pos = maxPhonePos; pos >= 1; pos -=1 ){
              phoneServo.write(pos);
              delay(10);  
            }

            phoneStatus = 1;

            delay(20000);
            getPhone();
        } else if (phoneStatus == 1) {
          
            for (pos = 1; pos < maxPhonePos; pos+=1) {
                phoneServo.write(pos);
                delay(10);
            }
            
            phoneStatus = 0;
        }

        // disconnect
        phoneServo.detach();
}

/**
 * open the door
 * get phone ->  press the unlock key -> hang up the phone
 */
void openDoor() {
          // connect 
        unlockServo.attach(9);
        phoneServo.attach(10);

            
        // get the phone up
        for (pos = maxPhonePos; pos >= 1; pos -=1 ){
          phoneServo.write(pos);
          delay(10);  
        }
        

        delay(500);
        
        // press the button of open door
        for (pos = 1; pos < maxUnlockPos; pos+=1) {
            unlockServo.write(pos);
            delay(10);
        }
             
        delay(200);

        //release the unlock button
        for (pos = maxUnlockPos; pos >= 1; pos -=1 ){
          unlockServo.write(pos);
          delay(10);  
        }



        // get the phone down            
        for (pos = 1; pos < maxPhonePos; pos+=1) {
            phoneServo.write(pos);
            delay(10);
        }

        //Serial.println(comdata);

        // disconnect
        unlockServo.detach();
        phoneServo.detach();
}

// global key
char key = '0';
void loop() {

    // irremote control
    if(irrecv.decode(&results)) {
        if (results.value != -1 ) {
            key = returnKey(results.value);
            
            if ( key == '1') {
                comdata = "open";
            } else if (key == '2') {
                comdata = "getphone";  
            }
      }
      irrecv.resume();
   }

    // serial control
    while (Serial.available() > 0)  
    {
        comdata += char(Serial.read());
        delay(10);
    }
    
    if (comdata.length() > 0)
    {
          if (comdata == "open") {
            openDoor();
          } else if (comdata == "getphone") {
            getPhone();
          }
    } 

 
    delay(10);
    comdata = "";
}
