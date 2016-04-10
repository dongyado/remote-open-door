/**
 * servo controll program
 * 
 * @author dongyado<dongyado@gmail.com>
 */

#include<Servo.h>

Servo myservo;

int pos = 0;

String comdata = "";

void setup() {
  Serial.begin(9600);
}

int lastPos = 0;
void loop() {

    while (Serial.available() > 0)  
    {
        comdata += char(Serial.read());
        delay(10);
    }
    
    if (comdata.length() > 0)
    {
        // connect 
        myservo.attach(9);
      
        //reset
        myservo.write(0);

        
        if (comdata == "open" || comdata == "1" ) {

            // press the button of open door
            for (pos = 1; pos < 70; pos+=1) {
                myservo.write(pos);
                delay(10);
                lastPos = pos;
            }
            
            Serial.println(comdata);
            
            delay(200);

            //reset
            for (pos = lastPos; pos >= 1; pos -=1 ){
              myservo.write(pos);
              delay(10);  
              lastPos = pos;
            }
            
//        }else if (comdata == "close" || comdata == "2") {   
//            //myservo.write(lastPos);                                                                                                                                                                                                                                        
//            for (pos = lastPos; pos >= 1; pos -=1 ){
//              myservo.write(pos);
//              delay(10);  
//              lastPos = pos;
//            }
//            Serial.println(comdata);
//            delay(200);
//        }

        // disconnect
        myservo.detach();
    }
    
 
    delay(10);
    comdata = "";
}
