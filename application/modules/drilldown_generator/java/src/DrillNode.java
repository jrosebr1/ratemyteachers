package drilldown;

public class DrillNode
 {
  // index of the node
  private int index;
  // ID of the node
  private int id;
  // name of the node
  private String name;
  
  public DrillNode(int index, int id, String name)
   {
    this.index = index;
    this.id = id;
    this.name = name;
   }
   
  public int getIndex()
   {
    // return the index of this node
    return this.index;
   }
   
  public int getID()
   {
    // return the ID of this node
    return this.id;
   }
   
  public String getName()
   {
    // return the name of the node
    return this.name;
   }
 }