package charlie;

// import the necessary weka libraries
import weka.core.*;
import weka.classifiers.*;
import weka.classifiers.bayes.DMNBtext;

// import the necessary Java libraries
import java.io.*;
import java.util.*;

public class RatingClassifier implements Serializable
 {
  // number of iterations to perform when training the classifier
  public static final int TRAINING_ITERATIONS = 10;
  // size of the n-grams that will be created
  public static final int NGRAM_SIZE = 4;
  // padding character to be added onto the string to be turned into n-grams
  public static final char NGRAM_PADDER = '$';
  // Map of features that were previously selected and will be used to classify
  // the ratings
  private Map<String, Integer> selectedFeatures = new HashMap<String, Integer>();
  // training data instances
  private Instances instances;
  // machine learning classifier
  private DMNBtext classifier = new DMNBtext();
 
  public RatingClassifier(String arffPath, String selectedPath) throws Exception
   {
    // build the instances from the supplied ARFF file
    BufferedReader reader = new BufferedReader(new FileReader(arffPath));
    this.instances = new Instances(reader);
    this.instances.setClassIndex(this.instances.numAttributes() - 1);
    reader.close();
   
    // build the Map of selected features and set the number of iterations
    // to be performed when training the classifiesr
    this.buildSelectedFeatures(selectedPath);    
    this.classifier.setNumIterations(RatingClassifier.TRAINING_ITERATIONS);
   }
   
  private void buildSelectedFeatures(String selectedPath) throws Exception
   {
    // open the selected feature file
    BufferedReader reader = new BufferedReader(new FileReader(selectedPath));
    String line = "";
    int counter = 0;
    
    // loop over the contents of the file
    while ((line = reader.readLine()) != null)
     {
      // break the line into feature score and token
      int pos = line.indexOf(":");
      double score = Double.parseDouble(line.substring(0, pos));
      String feature = line.substring(pos + 1);
      
      // store the feature in the selected feature Map
      this.selectedFeatures.put(feature, counter);
      counter++;
     }
    
    // close the reader
    reader.close();
   }
   
  private Instance buildInstance(Rating rating)
   {
    // initialize the Instance object
    Instance instance = new Instance(this.selectedFeatures.size() + 1);
    int enumAttr = 0;
    
    // initialize all attribute values to zero
    for (enumAttr = 0; enumAttr < this.selectedFeatures.size(); enumAttr++)
     {
      instance.setValue(enumAttr, 0.0);
     }

    Iterator<String> iter = rating.getNGrams().iterator();
    
    // loop over the n-grams in the rating
    while (iter.hasNext())
     {
      // get the next n-gram and get index of the n-gram from the selected
      // features Map
      String nGram = iter.next();
      Integer index = this.selectedFeatures.get(nGram);
      
      // if the index is not null, then we need to update the instance
      if (index != null)
       {
        instance.setValue(index, 1.0);
       }
     }
     
    // give the instance access to the attribute information from the dataset
    instance.setDataset(this.instances);
    
    // return the Instance
    return instance;
   }
   
  public void train() throws Exception
   {
    // build the classifier from the training data
    this.classifier.buildClassifier(this.instances);
   }
   
  public String[] classify(Rating rating) throws Exception
   {
    // build an Instance from the Rating and perform the classification
    Instance instance = this.buildInstance(rating);
    int labelIndex = (int)this.classifier.classifyInstance(instance);
    String label = this.instances.classAttribute().value(labelIndex);

    // get the distribution of label scores
    double scores[] = this.classifier.distributionForInstance(instance);

    // construct an array of info about the classification
    String info[] = new String[2];
    info[0] = label;
    info[1] = Double.toString(scores[labelIndex]);

    // return the classification info
    return info;
   }   
 }