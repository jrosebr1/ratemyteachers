package charlie;

// import the necessary Java libraries
import java.util.*;

public class Rating
 {
  // Set of n-grams from the rating text
  private Set<String> nGrams;
  // label of the rating, either VALID or INVALID
  private String label;
 
  public Rating(Set<String> nGrams, String label)
   {
    // store the Set of n-grams and the label of the rating
    this.nGrams = nGrams;
    this.label = label;
   }
   
  public Set<String> getNGrams()
   {
    // return the Set of n-grams
    return this.nGrams;
   }
   
  public String getLabel()
   {
    // return the label of the rating
    return this.label;
   }
 }