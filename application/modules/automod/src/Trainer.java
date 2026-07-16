package charlie;

// import the necessary java libraries
import java.io.*;

public class Trainer
 {
  public static void main(String args[])
   {
    try
     {
      // get the ARFF file, selected feature file, and output classifier from
      // command line argument
      String arffPath = args[0];
      String selectedPath = args[1];
      String classifierPath = args[2];
      
      // build and train the classifier
      RatingClassifier classifier = new RatingClassifier(arffPath, selectedPath);
      classifier.train();
      
      // write the classifier to file
      FileOutputStream fileStream = new FileOutputStream(classifierPath);
      ObjectOutputStream objectStream = new ObjectOutputStream(fileStream);
      objectStream.writeObject(classifier);
      objectStream.flush();
      fileStream.close();
     }
     
    catch (Exception exception)
     {
      // print the exception stack trace
      exception.printStackTrace();
     }
   }
 }