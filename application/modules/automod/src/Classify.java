package charlie;

// import the necessary Java libraries
import java.io.*;
import java.util.*;

public class Classify
 {
  public static void main(String args[])
   {
    try
     {
      // get the classifier file, configuration file and number of comment
      // samples from command line argument
      String classifierPath = args[0];
      String configPath = args[1];
      int samples = Integer.parseInt(args[2]);

      // read the rating classifier from file
      FileInputStream fileStream = new FileInputStream(classifierPath);
      ObjectInputStream objectStream = new ObjectInputStream(fileStream);
      RatingClassifier classifier = (RatingClassifier)objectStream.readObject();
      fileStream.close();      

      // build the configuration Map
      Map<String, String> config = ConfigBuilder.build(configPath);
      
      // create the database connection
      String dbURL = "jdbc:mysql://" + config.get("db_host");
      dbURL += ":" + config.get("db_port");
      dbURL += "/" + config.get("db_schema");
      dbURL += "?user=" + config.get("db_username");
      dbURL += "&password=" + config.get("db_password");
      RatingDatabase db = new RatingDatabase(dbURL);
      db.connect();

      // get the last modified RID and get some ratings to moderate
      int lastRID = db.getLastModRID();
      LinkedList<String[]> comments = db.getRatingComments(samples);
      Iterator<String[]> iter = comments.iterator();

      // initialize the comment text and RID
      String comment = null;
      int rid = lastRID;

      // loop over the ratings
      while (iter.hasNext())
       {
        // get the next rating, parse the RID and comment, and then
        // sanitize the comment
        String info[] = iter.next();
        rid = Integer.parseInt(info[0]);
        comment = info[1];
        comment = comment.toLowerCase().replaceAll("[\\s]+", " ");

        // break the comment into n-grams and classify the rating
        Set<String> nGrams = NGram.create(comment, 4, '$');
        Rating rating = new Rating(nGrams, null);
        String label[] = classifier.classify(rating);
        int status = -2;

        // update the classified status if the rating is valid
        if (label[0].equals("VALID"))
         {
          status = 2;
         }

        // update the database with the moderated status and score and
        // pause execution so that we don't increase server load
        db.updateRating(rid, status, Double.parseDouble(label[1]));
        Thread.sleep(40);
       }

      // update the last modified RID
      db.updateLastModRID(rid);      
     }
     
    catch (Exception exception)
     {
      // print the exception stack trace
      exception.printStackTrace();
     }
   }
 }