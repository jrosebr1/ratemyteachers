package sitemap_generator;

// import the necessary packages
import java.io.*;

public class URLSetWriter extends SitemapWriter
{
	public URLSetWriter(String outputPath) throws IOException
	{
		// call the parent constructor
		super(outputPath);
	}
	
	protected void writeHeader() throws IOException
	{
		// construct the header to be written to file
		String header = "<?xml version='1.0' encoding='UTF-8'?>\n";
		header += "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" ";
		header += "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
		header += "xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">";
		
		// write the header to file
		this.writer.write(header + "\n");
	}
	
	protected void writeFooter() throws IOException
	{
		// write the footer to file
		this.writer.write("</urlset>\n");
	}
	
	public void writeBlock() throws IOException
	{
		// write a blank URL, date, frequency, and priority to file
		this.writeBlock("", "", "", "");
	}
	
	public void writeBlock(String url, String date, String freq, String priority) throws IOException
	{
		// construct the block to be written to file
		String block = "\t<url>\n";
		block += "\t\t<loc>" + url + "</loc>\n";
		block += "\t\t<lastmod>" + date + "</lastmod>\n";
		block += "\t\t<changefreq>" + freq + "</changefreq>\n";
		block += "\t\t<priority>" + priority + "</priority>\n";
		block += "\t</url>\n";
		
		// write the block to file
		this.writer.write(block);
	}
}