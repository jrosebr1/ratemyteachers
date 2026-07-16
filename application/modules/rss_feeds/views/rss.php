<rss version="2.0">
	<channel>
		<generator><?php echo $generator; ?></generator>
		<title><?php echo $title; ?></title>
		<link><?php echo $link; ?></link>
		<description><?php echo $description; ?></description>
		<language><?php echo $language; ?></language>
		<pubDate><?php echo $publish_date; ?></pubDate>
		<lastBuildDate><?php echo $last_build_date; ?></lastBuildDate>
		<?php foreach ($items as $item): ?>
		<item>
			<title><?php echo $item["title"]; ?></title>
			<link><?php echo $item["link"]; ?></link>
			<description><?php echo $item["desc"]; ?></description>
			<guid><?php echo $item["guid"]; ?></guid>
		</item>
		<?php endforeach; ?>
	</channel>
</rss>