/**
 * servo controll program
 * 
 * @author dongyado<dongyado@gmail.com>
 */

#include<Servo.h>

Servo unlockServo;
Servo phoneServo;

int pos = 0;

int maxPhonePos  = 90;
int maxUnlockPos = 30;

String comdata = "";

void setup() {
  
    Serial.begin(9600);
    
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

void loop() {

    while (Serial.available() > 0)  
    {
        comdata += char(Serial.read());
        delay(10);
    }
    
    if (comdata.length() > 0)
    {
        // connect 
        unlockServo.attach(9);
        phoneServo.attach(10);

        if (comdata == "open" || comdata == "1" ) {
            
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
        }

        //Serial.println(comdata);

        // disconnect
        unlockServo.detach();
        phoneServo.detach();
    } 

 
    delay(10);
    comdata = "";
}
