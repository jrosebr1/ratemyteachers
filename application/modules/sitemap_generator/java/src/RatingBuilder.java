package sitemap_generator;

// import the necessary packages
import java.io.*;
import java.util.*;

public class RatingBuilder
{
	// path to where the input file resides
	private String inputPath = null;
	// ArrayList of Rating objects built from file
	private ArrayList<Rating> list = null;
	
	public RatingBuilder(String inputPath)
	{
		// store the input path
		this.inputPath = inputPath;
	}
	
	public ArrayList<Rating> build() throws IOException
	{
		// count the number of lines in the input file and then
		// construct the ArrayList
		int count = LineCounter.count(this.inputPath);
		this.list = new ArrayList<Rating>(count);
		
		// open the input file for reading
		BufferedReader reader = new BufferedReader(new FileReader(this.inputPath));
		String ratingID = null;
		
		// loop over the contents of the file
		while ((ratingID = reader.readLine()) != null)
		{
			// read the person ID and rating URL from file as well
			int personID = Integer.parseInt(reader.readLine());
			String ratingURL = reader.readLine();
			
			// construct a Rating object from the block and add it
			// to the list
			Rating node = new Rating(personID, Integer.parseInt(ratingID), ratingURL);
			this.list.add(node);
		}
		
		// close the reader
		reader.close();
		
		// return the list of Rating objects
		return this.list;
	}
}