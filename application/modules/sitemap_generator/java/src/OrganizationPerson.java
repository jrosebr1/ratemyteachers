package sitemap_generator;

public class OrganizationPerson
{
	// the ID of the organization
	private int orgID = 0;
	// the country ID where the organization resides in
	private int countryID = 0;
	// the URL of the organization
	private String orgURL = null;
	// the ID of the person
	private int personID = 0;
	// the URL of the person
	private String personURL = null;
	
	public OrganizationPerson(int orgID, int countryID, String orgURL, int personID, String personURL)
	{
		// store the organization ID, country ID, organization URL,
		// person ID, and person URL
		this.orgID = orgID;
		this.countryID = countryID;
		this.orgURL = orgURL;
		this.personID = personID;
		this.personURL = personURL;
	}
	
	public int getOrgID()
	{
		// return the ID of the organization
		return this.orgID;
	}
	
	public int getCountryID()
	{
		// return the country ID that the organization resides in
		return this.countryID;
	}
	
	public String getOrgURL()
	{
		// return the organization URL
		return this.orgURL;
	}
	
	public int getPersonID()
	{
		// return the ID of the person
		return this.personID;
	}
	
	public String getPersonURL()
	{
		// return the URL of the person
		return this.personURL;
	}
}