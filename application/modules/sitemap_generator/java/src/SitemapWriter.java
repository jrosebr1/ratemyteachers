package sitemap_generator;

// import the necessary packages
import java.io.*;

public abstract class SitemapWriter
{
	// writer object used to write sitemap contents to file
	protected BufferedWriter writer = null;
	
	public SitemapWriter(String outputPath) throws IOException
	{
		// create the writer and write the header
		this.writer = new BufferedWriter(new FileWriter(outputPath));
		this.writeHeader();
	}

	public void close() throws IOException
	{
		// write the footer and then close the writer
		this.writeFooter();
		this.writer.close();
	}
	
	protected abstract void writeHeader() throws IOException;
	
	protected abstract void writeFooter() throws IOException;
	
	public abstract void writeBlock() throws IOException;
}