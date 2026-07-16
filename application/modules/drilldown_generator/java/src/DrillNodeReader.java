package drilldown;

import java.io.*;
import java.util.*;

public class DrillNodeReader
 {
  // filename of the file containing the DrillDownNode objects
  private String filename;
  
  public DrillNodeReader(String filename)
   {
    this.filename = filename;
   }
   
  public DrillNode[] readNodes()
   {
    try
     {
      // open the file for reading
      BufferedReader reader = new BufferedReader(new FileReader(this.filename));
      // get the size of the table from the file
      int size = Integer.parseInt(reader.readLine());
      // create the array of DrillNode objects
      DrillNode nodes[] = new DrillNode[size];
      String rawIndex = null;
      
      // loop until the end of file has been reached
      while ((rawIndex = reader.readLine()) != null)
       {
        // finish reading and parsing the index, id, and name
        int index = Integer.parseInt(rawIndex.trim());
        int id = Integer.parseInt(reader.readLine().trim());
        String name = reader.readLine().trim();
        
        // save the new DrillNode object
        nodes[index - 1] = new DrillNode(index, id, name);
       }
      
      // close the file
      reader.close();

      // return the array of DrillNode objects
      return nodes;
     }
     
    catch (Exception error)
     {
      System.out.println(error);
     }
     
    // return null since an error occured
    return null;
   }
 }