package sitemap_generator;

public class Rating
{
	// the ID of the person the rating belongs to
	private int personID = 0;
	// the ID of the rating
	private int ratingID = 0;
	// the URL of the rating
	private String ratingURL = null;
	
	public Rating(int personID, int ratingID, String ratingURL)
	{
		// store the person ID, rating ID, and rating URL
		this.personID = personID;
		this.ratingID = ratingID;
		this.ratingURL = ratingURL;
	}
	
	public int getPersonID()
	{
		// return the ID of the person
		return this.personID;
	}
	
	public int getRatingID()
	{
		// return the ID of the rating
		return this.ratingID;
	}
	
	public String getRatingURL()
	{
		// return the URL of the rating
		return this.ratingURL;
	}
}