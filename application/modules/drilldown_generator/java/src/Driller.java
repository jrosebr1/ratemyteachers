package drilldown;

import java.io.*;
import java.util.*;

public class Driller
 {
  // Map of ranges to that were created
  private Map<Integer, ArrayList<DrillNode>> ranges = new TreeMap<Integer, ArrayList<DrillNode>>();
  // initial starting offset
  private int startOffset;
  // total number of requested objects from the starting offset
  private int total;
  // the current depth of recursion
  private int depth;
  // maximum number of links to appear on a page
  private int maxLinks;
  // path to where the output files from drilling will be stored
  private String outputDir;
  // array of DrillNodes that were passed via the constructor
  private DrillNode nodes[];

  public Driller(int maxLinks, String outputDir, DrillNode nodes[])
   {
    // create the Driller object with the default values for
    // starting offset, total requested, and a depth of 0
    this(maxLinks, 0, nodes.length, 0, outputDir, nodes);
   }
   
  public Driller(int maxLinks, int startOffset, int total, int depth, String outputDir, DrillNode nodes[])
   {
    // save the parameters
    this.maxLinks = maxLinks;
    this.startOffset = startOffset;
    this.total = total;
    this.depth = depth;
    this.outputDir = outputDir;
    this.nodes = nodes;
   }
   
  private void buildRanges(int limit)
   {
    // number of objects placed in a range
    int found = 0;
    // used to loop over the nodes
    int enumNodes = 0;
    
    // loop over all nodes in the range of the starting offset to the starting
    // offset added to the total requested
    for (enumNodes = this.startOffset; enumNodes < this.startOffset + this.total; enumNodes++)
     {
      // get the index for that particular node
      int index = this.nodes[enumNodes].getIndex() - this.startOffset;
      
      // check to see if the index should be part of a range
      if (index % limit == 0 || index % limit == 1 || index == this.total)
       {
        // calculate the range index
        int mapIndex = (int)Math.floor(found / 2.0);
        // get the List that at the range index
        ArrayList<DrillNode> rangeList = ranges.get(mapIndex);
        
        // if the List has not been initialized...
        if (rangeList == null)
         {
          // ...then initialize it
          rangeList = new ArrayList<DrillNode>(2);
         }

        // add the current node to the List of ranges for that particular index
        rangeList.add(this.nodes[enumNodes]);
        
        // perform a special case check, if both the modulos is 1 and the index is
        // equal to the total, then the ranges will become unbalanced, leaving the
        // last List with only one entry, so let's add the last entry twice and ensure
        // the balance is kept. this will not affect the accuracy of the drill down,
        // it just means that there will be a leaf level node with only one link in it
        if (index % limit == 1 && index == this.total)
         {
          rangeList.add(this.nodes[enumNodes]);
         }
        
        // save the List
        ranges.put(mapIndex, rangeList);
        // increment the number of objects placed in a range
        found++;        
       }
     }
   }

  public void drill()
   {
    // number of objects allowed in a next range
    int limit = (int)Math.ceil((double)this.total / this.maxLinks);
    // number of objects in the the current range
    int count = this.startOffset + this.total;
    
    // check to see if we can stop drilling down
    if (this.total <= this.maxLinks)
     {
      // since we have reached the lowest level of the tree, try writing
      // the names to file
      try
       {
        // holds the ID and name of each node to be written to file
        String contents = "";
        int enumNodes = 0;

        // loop over all nodes in the range
        for (enumNodes = this.startOffset; enumNodes < count; enumNodes++)
         {
          // save the name and ID to the String instead of individually
          // writing each name to file to achieve better performance
          contents += (this.nodes[enumNodes].getID() + "\n");
          contents += (this.nodes[enumNodes].getName() + "\n");
         }
        
        // construct the path and filename for the output file
        String filename = this.createOutputFilename();
        // open the file for writing
        BufferedWriter writer = new BufferedWriter(new FileWriter(filename));
        // write the header to file
        writer.write("leaf\n");
        // write the contents to file
        writer.write(contents.trim());
        // close the file
        writer.close();
       }
       
      // catch any errors
      catch (IOException error)
       {
        System.out.println(error);
       }
     }
   
    // keep drilling
    else
     {
      // when the drill finds a result that is useful, we will store it in
      // the 'well' and write it out to file later
      String well = "edge\n";
     
      // create the Map of ranges
      this.buildRanges(limit);

      // get the Set of indicies for each range
      Set<Integer> rangeSet = this.ranges.keySet();
      Iterator<Integer> iter = rangeSet.iterator();
    
      // loop over each range
      while (iter.hasNext())
       {
        // get the next range index
        Integer index = iter.next();
        // get the List of DrillNode objects at that index
        List<DrillNode> drillList = this.ranges.get(index);

        // calculate the new start offset
        int newStart = drillList.get(0).getIndex() - 1;
        // calculate the new total
        int newTotal = drillList.get(1).getIndex() - drillList.get(0).getIndex() + 1;
      
        // save the node name and our newly found calculation in the well
        well += (drillList.get(0).getName() + "\n");
        well += (drillList.get(1).getName() + "\n");
        well += (newStart + " - " + newTotal + "\n");
        
        // recursively drill deeper with the new start and new total
        // doing recursion instead of maintaining a List of new ranges to drill
        // improves the locality of reference, thus improving performance as well
        Driller driller = new Driller(this.maxLinks, newStart, newTotal, this.depth + 1, this.outputDir, this.nodes);
        driller.drill();
       }
      
      // try writing the well to file
      // here the use of the well becomes apparent since we have ensured that
      // there will be a long CPU burst followed by a short burst of I/O cycles,
      // ensuring optimality for most process scheduling algorithms
      try
       {
        // construct the path and filename for the output file
        String filename = this.createOutputFilename();
        // open the file for writing
        BufferedWriter writer = new BufferedWriter(new FileWriter(filename));
        // write the well to file
        writer.write(well.trim());
        // close the writer
        writer.close();
       }
       
      // catch any errors
      catch (IOException error)
       {
        System.out.println(error);
       }
     }
   }
   
  private String createOutputFilename()
   {
    String filename = this.outputDir;
        
    // if we are at a depth of zero we are at the root of the tree so let's
    // name the file differenty so we can easily find the root file
    if (this.depth == 0)
     {
      filename += "/root.txt";
     }

    // construct the standard path and filename for the output file
    else
     {
      filename += "/" + this.startOffset + "-" + this.total + ".txt";
     }
    
    // return the created filename    
    return filename;
   }
 }