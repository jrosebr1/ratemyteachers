package charlie;

// import the necessary libraries
import java.util.*;

public class Config
 {
  public static Map<String, String> getTables()
   {
    // initialize the tables map
    Map<String, String> tablesMap = new HashMap<String, String>();
    
    // add the tables to the tables map
    tablesMap.put("Automod", "AutomodInfo");
    tablesMap.put("AutomodTracker", "AutomodTracker");
    tablesMap.put("Ratings", "Ratings");
    
    // return the tables map
    return tablesMap;
   }
   
   public static Map<String, Map<String, String>> getTableSchemas()
    {
     // initialze the table schemas map
     Map<String, Map<String, String>> schemasMap = new HashMap<String, Map<String, String>>();

     // create the schema for the 'Automod' table
     Map<String, String> schema = new HashMap<String, String>();
     schema.put("ID", "AID");
     schema.put("LastModeratedID", "LastModRID");
     schemasMap.put("Automod", schema);
     
     // create the schema for the 'AutomodTracker' table
     schema = new HashMap<String, String>();
     schema.put("RatingID", "RID");
     schema.put("Score", "RScore");
     schema.put("OriginalStatus", "RStatusOrig");
     schema.put("UpdatedStatus", "RStatusUpdated");
     schema.put("ModeratedDate", "RModDate");
     schemasMap.put("AutomodTracker", schema);
     
     // create the schema for the 'Ratings' table
     schema = new HashMap<String, String>();
     schema.put("ID", "RID");
     schema.put("PersonID", "TID");
     schema.put("Comment", "RComments");
     schema.put("Status", "RStatus");
     schemasMap.put("Ratings", schema);
     
     // return the schemas map
     return schemasMap;
    }
   
   public static Map<String, String> getConstants()
    {
     // initialze the constants map
     Map<String, String> constantsMap = new HashMap<String, String>();
     
     // add the constants to the constants map
     constantsMap.put("min_rating_status", "0");
     constantsMap.put("max_rating_status", "2");
     
     // return the constants map
     return constantsMap;
    }
 }