package sitemap_generator;

// import the necessary packages
import java.io.*;

public class LineCounter
{
	public static int count(String inputPath) throws IOException
	{
		// open the input file for reading and initialize the number
		// of lines in the file
		BufferedReader reader = new BufferedReader(new FileReader(inputPath));
		String line = null;
		int count = 0;
		
		// loop over the contents of the file
		while ((line = reader.readLine()) != null)
		{
			// increment the total number of lines
			count++;
		}
		
		// close the input file
		reader.close();
		
		// return the number of lines in the file
		return count;
	}
}