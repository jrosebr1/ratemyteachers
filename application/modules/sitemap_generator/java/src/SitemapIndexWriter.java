package sitemap_generator;

// import the necessary packages
import java.io.*;

public class SitemapIndexWriter extends SitemapWriter
{
	public SitemapIndexWriter(String outputPath) throws IOException
	{
		// call the parent constructor
		super(outputPath);
	}
	
	protected void writeHeader() throws IOException
	{
		// construct the header to be written to file
		String header = "<?xml version='1.0' encoding='UTF-8'?>\n";
		header += "<sitemapindex xmlns=\"http://www.google.com/schemas/sitemap/0.84\" ";
		header += "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
		header += "xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/siteindex.xsd\">";
		
		// write the header to file
		this.writer.write(header + "\n");
	}
	
	protected void writeFooter() throws IOException
	{
		// write the footer to file
		this.writer.write("</sitemapindex>");
	}
	
	public void writeBlock() throws IOException
	{
		// write a blank URL and date block to file
		this.writeBlock("", "");
	}
	
	public void writeBlock(String url, String date) throws IOException
	{
		// construct the block to be written to file
		String block = "\t<sitemap>\n";
		block += "\t\t<loc>" + url + "</loc>\n";
		block += "\t\t<lastmod>" + date + "</lastmod>\n";
		block += "\t</sitemap>\n";
		
		// write the block to file
		this.writer.write(block);
	}
}