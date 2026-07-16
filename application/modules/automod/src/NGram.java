package charlie;

// import the necessary Java libraries
import java.util.*;

public class NGram
 {
  public static Set<String> create(String text, int size, char padder)
   {
    // initialize the Set of n-grams and pad the text
    Set<String> nGrams = new HashSet<String>();
    text = NGram.pad(text.toLowerCase(), size - 1, padder);
    int enumText = 0;
    
    // loop over the text and create the n-grams
    for (enumText = 0; enumText < text.length() - size + 1; enumText++)
     {
      // extract the n-gram and update the Set of n-grams
      String nGram = text.substring(enumText, enumText + size);
      nGrams.add(nGram);
     }
    
    // return the Set of n-grams
    return nGrams;
   }
   
  public static String pad(String text, int padSize, char padder)
   {
    int enumPad = 0;
    
    // loop over the padding size and add the padder before and after the text
    for (enumPad = 0; enumPad < padSize; enumPad++)
     {
      text = padder + text + padder;
     }
     
    // return the padded text
    return text;
   }
 }