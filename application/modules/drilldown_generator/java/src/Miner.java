package drilldown;

import java.io.*;
import java.util.*;

public class Miner
 {
  public static void main(String args[])
   {
    // ensure that enough arguments have been supplied
    if (args.length < 4)
     {
      System.err.println("usage: miner <drill bit file> <input directory> <output directory> <links per page>");
      System.exit(-1);
     }
    
    // save the command line arguments into variables for better code readability
    String drillBitFile = args[0];
    String inputDir = args[1];
    String outputDir = args[2];
    int linksPerPage = Integer.parseInt(args[3]);
    
    // read the drill bits from file
    DrillBitReader drillBitReader = new DrillBitReader(drillBitFile);
    drillBitReader.readDrillBits();
    
    // get a list of the drill bits
    LinkedList<String> drillBits = drillBitReader.getDrillBits();
    Iterator<String> drillBitIter = drillBits.iterator();
    
    // loop over all drill bits
    while (drillBitIter.hasNext())
     {
      // get the next drill bit and convert it to uppercase
      String drillBit = drillBitIter.next().toUpperCase();

      // construct the output path for the given drill bit
      String outputPath = outputDir + "/" + drillBit;
      // create the drill bit directory in the output path if it does not already exist
      (new File(outputPath)).mkdir();
       
      // construct the filename for where the DrillNode objects reside
      String nodeFilename = inputDir + "/" + drillBit + ".txt";
      // read the nodes from disk
      DrillNodeReader nodeReader = new DrillNodeReader(nodeFilename);
      DrillNode nodes[] = nodeReader.readNodes();
      
      // start drilling
      Driller driller = new Driller(linksPerPage, outputPath, nodes);
      driller.drill();
     }
   }
 }