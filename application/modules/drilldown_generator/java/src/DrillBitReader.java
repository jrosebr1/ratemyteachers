package drilldown;

import java.io.*;
import java.util.*;

public class DrillBitReader
 {
  // List of drill bits read from file
  private LinkedList<String> drillBitList = new LinkedList<String>();
  // filename of where the drill bits reside
  private String filename;
  
  public DrillBitReader(String filename)
   {
    this.filename = filename;
   }
   
  public LinkedList<String> getDrillBits()
   {
    // return the list of drill bits
    return this.drillBitList;
   }
   
  public void readDrillBits()
   {
    // try to read the contents of the drill bit file
    try
     {
      // open the drill bit file for reading
      BufferedReader reader = new BufferedReader(new FileReader(this.filename));
      
      // loop until all drill bits are read from file
      while (reader.ready())
       {
        // read the next drill bit fom the file
        String drillBit = reader.readLine().trim();
        // add the drill bit to the list
        this.drillBitList.add(drillBit);
       }
      
      // close the file
      reader.close();
     }
     
    // catch any errors
    catch (IOException error)
     {
      System.out.println(error);
     }
   }
 }