package sitemap_generator;

// import the necessary packages
import java.io.*;
import java.util.*;

public class OrganizationPersonBuilder
{
	// path to where the input file resides
	private String inputPath = null;
	// ArrayList of OrganizationPerson objects built from file
	private ArrayList<OrganizationPerson> list = null;
	
	public OrganizationPersonBuilder(String inputPath)
	{
		// store the input path
		this.inputPath = inputPath;		
	}
	
	public ArrayList<OrganizationPerson> build() throws IOException
	{
		// count the number of lines in the input file and then
		// construct the ArrayList
		int count = LineCounter.count(this.inputPath);
		this.list = new ArrayList<OrganizationPerson>(count);

		// open the input file for reading
		BufferedReader reader = new BufferedReader(new FileReader(this.inputPath));
		String orgID = null;
		
		// loop over the contents of the file
		while ((orgID = reader.readLine()) != null)
		{
			// read the country ID, organization URL, person ID and
			// person URL from the file as well
			int countryID = Integer.parseInt(reader.readLine());
			String orgURL = reader.readLine();
			int personID = Integer.parseInt(reader.readLine());
			String personURL = reader.readLine();
			
			// construct an OrganizationPerson node from the block
			// and add it to the list
			OrganizationPerson node = new OrganizationPerson(Integer.parseInt(orgID), countryID, orgURL, personID, personURL);
			this.list.add(node);
		}
		
		// close the reader
		reader.close();
		
		// return the list of OrganizationPerson objects
		return this.list;
	}
	
	public OrganizationPerson find(int personID)
	{
		// initialize the binary search variables
		int low = 0;
		int mid = 0;
		int high = this.list.size() - 1;
		
		// keep trying to find the OrganizationPerson with the
		// supplied person ID
		while (low <= high)
		{
			// calculate the midpoint between the low and high indexes
			// and grab the OrganizationPerson at the midpoint
			mid = (low + high) / 2;
			OrganizationPerson orgPerson = this.list.get(mid);
			
			// check to see if the low index can be bumped up
			if (personID > orgPerson.getPersonID())
			{
				low = mid + 1;
			}
			
			// check to see if the high index can be moved down
			else if (personID < orgPerson.getPersonID())
			{
				high = mid - 1;
			}
			
			// otherwise, we have found the OrganizationPerson with
			// the supplied ID
			else
			{
				return orgPerson;
			}
		}
		
		// return null since the OrganizationPerson with the supplied
		// ID could not be found
		return null;
	}
}