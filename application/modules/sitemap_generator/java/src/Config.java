package sitemap_generator;

// import the necessary packages
import java.io.*;
import java.util.*;

public class Config
{
	// Map of configuration options
	private Map<String, String> options = new HashMap<String, String>();
	
	public Config(String inputPath) throws IOException
	{
		// build the configuration
		this.build(inputPath);
	}
	
	public String getOption(String key)
	{
		// return the option value with the supplied key
		return this.options.get(key);
	}
	
	private void build(String inputPath) throws IOException
	{
		// open the configuration file for reading
		BufferedReader reader = new BufferedReader(new FileReader(inputPath));
		String line = null;
		
		// loop over the contents of the file
		while ((line = reader.readLine()) != null)
		{
			// if the line is empty or starts with a '#' then the line
			// can be ignored
			if (line.equals("") || line.charAt(0) == '#')
			{
				continue;
			}
			
			// extract the key and the value from the line
			int pos = line.indexOf(":");
			String key = line.substring(0, pos);
			String value = line.substring(pos + 1);
			
			// add the option to the options Map
			this.options.put(key, value);
		}
		
		// close the reader
		reader.close();
	}	
}