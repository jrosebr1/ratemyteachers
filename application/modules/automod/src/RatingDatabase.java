package charlie;

// import the necessary libraries
import java.util.*;
import java.sql.*;

public class RatingDatabase extends Database
 {
  // mapping of core tables
  Map<String, String> coreTables = null;
  // mapping of core table schemas
  Map<String, Map<String, String>> coreSchemas = null;
  // mapping of core constants
  Map<String, String> coreConstants = null;
  
  public RatingDatabase(String dbURL)
   {
    // call the parent constructor to connect to the database
    super(dbURL);
    
    // grab the configuration of core tables, schemas, and constants
    this.coreTables = Config.getTables();
    this.coreSchemas = Config.getTableSchemas();
    this.coreConstants = Config.getConstants();
   }

  public int getLastModRID()
   {
    // initialize the RID
    int rid = -1;
    
    // grab the schema for the 'Automod' table
    Map<String, String> schema = this.coreSchemas.get("Automod");

    // try to fetch the lastest modified RID
    try
     {
      // construct the query to fetch the latest modified RID
      String sql = "SELECT " + schema.get("LastModeratedID") + " ";
      sql += "FROM " + this.coreTables.get("Automod") + " ";
      sql += "WHERE " + schema.get("ID") + "=1";

      // execute the query
      this.resultSet = this.stmt.executeQuery(sql);

      // if there is a result, then grab the RID
      if (this.resultSet.next())
       {
        rid = this.resultSet.getInt(1);
       }
     }

    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }

    // return the RID
    return rid;
   }

  public void updateRating(int rid, int status, double score)
   {
    // grab the schema for the 'Ratings' table
    Map<String, String> schema = this.coreSchemas.get("Ratings");

    // try to update the rating
    try
     {
      // construct the query to get the old status of the rating
      String sql = "SELECT " + schema.get("Status") + " ";
      sql += "FROM " + this.coreTables.get("Ratings") + " ";
      sql += "WHERE " + schema.get("ID") + "=?";

      // create the PreparedStatement object for the query and then bind
      // the variables
      PreparedStatement prepStmt = this.conn.prepareStatement(sql);
      prepStmt.setInt(1, rid);
      
      // execute the query and grab the original rating status
      this.resultSet = prepStmt.executeQuery();
      this.resultSet.next();
      int origStatus = this.resultSet.getInt(1);

      // construct the query to update the Ratings table
      sql = "UPDATE " + this.coreTables.get("Ratings") + " ";
      sql += "SET " + schema.get("Status") + "=? ";
      sql += "WHERE " + schema.get("ID") + "=?";

      // create the PreparedStatement object for the query and then bind
      // the variables
      prepStmt = this.conn.prepareStatement(sql);
      prepStmt.setInt(1, status);
      prepStmt.setInt(2, rid);

      // execute the query
      prepStmt.executeUpdate();
      
      // grab the schema for the 'AutomodTracker' table
      schema = this.coreSchemas.get("AutomodTracker");

      // construct the query to log the update
      sql = "INSERT INTO " + this.coreTables.get("AutomodTracker") + " (";
      sql += schema.get("RatingID") + ", ";
      sql += schema.get("Score") + ", ";
      sql += schema.get("OriginalStatus") + ", ";
      sql += schema.get("UpdatedStatus") + ") ";
      sql += "VALUES(?, ?, ?, ?)";

      // create the PreparedStatement object for the query and then bind
      // the variables
      prepStmt = this.conn.prepareStatement(sql);
      prepStmt.setInt(1, rid);
      prepStmt.setDouble(2, score);
      prepStmt.setInt(3, origStatus);
      prepStmt.setInt(4, status);

      // execute the query
      prepStmt.executeUpdate();
     }

    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }

  public void updateLastModRID(int rid)
   {
    // grab the schema for the 'Automod' table
    Map<String, String> schema = this.coreSchemas.get("Automod");
    
    // try to update the last modified RID
    try
     {
      // construct the query to update the last modified RID
      String sql = "UPDATE " + this.coreTables.get("Automod") + " ";
      sql += "SET " + schema.get("LastModeratedID") + "=? ";
      sql += "WHERE " + schema.get("ID") + "=1";

      // create the PreparedStatement object for the query and then bind
      // the variables
      PreparedStatement prepStmt = this.conn.prepareStatement(sql);
      prepStmt.setInt(1, rid);

      // execute the query
      prepStmt.executeUpdate();
     }

    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }

  public LinkedList<String[]> getRatingComments(int limit)
   {
    // grab the schema for the 'Ratings' table
    Map<String, String> schema = this.coreSchemas.get("Ratings");
    
    // initialize the list of rating comments and get the last modified
    // RID
    LinkedList<String[]> comments = new LinkedList<String[]>();
    int lastRID = this.getLastModRID();
   
    // try to fetch the rating comments
    try
     {
      // construct the query to fetch the rating comments
      String sql = "SELECT " + schema.get("ID") + ", ";
      sql += schema.get("Comment") + " ";
      sql += "FROM " + this.coreTables.get("Ratings") + " ";
      sql += "WHERE " + schema.get("ID") + " > ? ";
      sql += "AND " + schema.get("Comment") + " != '' ";
      sql += "AND (" + schema.get("Status") + " >= " + this.coreConstants.get("min_rating_status") + " ";
      sql += "AND " + schema.get("Status") + " <= " + this.coreConstants.get("max_rating_status") + ") ";
      sql += "ORDER BY " + schema.get("ID") + " ASC ";
      sql += "LIMIT ?;";
      
      // create the PreparedStatement object for the query and then bind
      // the variables
      PreparedStatement prepStmt = this.conn.prepareStatement(sql);
      prepStmt.setInt(1, lastRID);
      prepStmt.setInt(2, limit);
      
      // execute the query
      this.resultSet = prepStmt.executeQuery();

      // loop over the comments
      while (this.resultSet.next())
       {
        // get the RID and comment
        String rid = this.resultSet.getString(1);
        String comment = this.resultSet.getString(2);

        // create an array to store the RID and comment and add the info
        // to the comments list
        String info[] = new String[2];
        info[0] = rid;
        info[1] = comment;
        comments.add(info);
       }
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }

    // return the list of rating comments    
    return comments;
   }   
 }