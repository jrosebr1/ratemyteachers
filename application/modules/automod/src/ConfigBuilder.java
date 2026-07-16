package charlie;

// import the necessary libraries
import java.io.*;
import java.util.*;

public class ConfigBuilder
 {
  public static Map<String, String> build(String filePath) throws IOException
   {
    // initialize the Map of configuration options
    Map<String, String> config = new HashMap<String, String>();
    
    // open the config file for reading
    BufferedReader reader = new BufferedReader(new FileReader(filePath));
    String line = "";
    
    // keep looping while there are lines in the file
    while ((line = reader.readLine()) != null)
     {
      // trim any trailing whitespace off the line
      line = line.trim();
      
      // if the line is empty or the first character is a comment
      // then we can ignore the line
      if (line.equals("") || line.charAt(0) == '#')
       {
        continue;
       }
       
      // extract the option and value from the line and add it to the
      // configuration Map
      String option[] = ConfigBuilder.extractOption(line);
      config.put(option[0], option[1]);
     }

    // close the reader
    reader.close();
    
    // return the configuration Map
    return config;
   }
   
  public static String[] extractOption(String line)
   {
    // initialize the array that will contain the option and
    // the value
    String option[] = new String[2];
    
    // find the first occurrence of an equal sign which will
    // be used to extract the option and value
    int pos = line.indexOf("=");
    option[0] = line.substring(0, pos);
    option[1] = line.substring(pos + 1);
    
    // return the option
    return option;
   }
 }