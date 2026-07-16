package charlie;

// import the necessary libraries
import java.sql.*;

public class Database
 {
  // String representing the url that the database will connect with
  private String dbURL;
  // database connection used to interface with the database
  protected Connection conn;
  // Statement object encapsulating SQL commands
  protected Statement stmt;
  // ResultSet that will be populated after a query executes
  protected ResultSet resultSet;
 
  public Database(String dbURL)
   {
    // store the parameters
    this.dbURL = dbURL;
   }
   
  public void connect()
   {
    // try to load the mySQL driver
    try
     {
      // connect to the database and create the Statement object so
      // we can run SQL commands
      Class.forName("com.mysql.jdbc.Driver");
      this.conn = DriverManager.getConnection(this.dbURL);
      this.stmt = this.conn.createStatement();
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }
   
  public void execQuery(String sql)
   {
    // try to execute the SQL statement
    try
     {
      this.resultSet = this.stmt.executeQuery(sql);
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }
   
  public void closeSQL()
   {
    // if the Statement object has not been initialized, return from
    // the method
    if (this.stmt == null)
     {
      return;
     }
     
    // try to close the Statement object
    try
     {
      this.stmt.close();
      this.stmt = null;
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }
   
  public void closeResults()
   {
    // if the ResultSet object has not been initialized, return from
    // the method
    if (this.resultSet == null)
     {
      return;
     }
     
    // try to close the ResultSet object
    try
     {
      this.resultSet.close();
      this.resultSet = null;
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }
   
  public void disconnect()
   {
    // try to disconnect from the database
    try
     { 
      // close the ResultSet and Statement objects
      this.closeResults();
      this.closeSQL();
      
      // check to see if the connection is null; if it is, then
      // return from the method
      if (this.conn == null)
       {
        return;
       }
       
      // close the connection and reset the connection variable
      this.conn.close();
      this.conn.close();
     }
     
    // if an exception occurs, print the stack trace
    catch (Exception exception)
     {
      exception.printStackTrace();
     }
   }   
 }