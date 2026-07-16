<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
 		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Homepage | <?php echo $this->config->item("core.sitename"); ?></title>
		<?php $this->load->view("common/head_libs"); ?>
		<link rel="stylesheet" type="text/css" href="/css/qscroller/qscroller.css">
		<script type="text/javascript" src="/js/qscroller/qscroller.js"></script>
	</head>
	<body>
		<?php $this->load->view("common/header"); ?>
		
		<h1>Homepage</h1>
		
		<p>
			<form action="/search" method="post">
				Rate your person by searching for your organization:<br>
				<input type="text" name="search_org" value="">
				<input type="submit" value="Search">
			</form>
			
			<p>
				Search Directory by:
				<a href="<?php echo $homepage_search_drilldown["org_drilldown_url"]; ?>">Organization</a>
				|
				<a href="<?php echo $homepage_search_drilldown["person_drilldown_url"]; ?>">Person</a>
			</p>
		</p>
		
		<p>
			<strong>
				Latest:
			</strong>
			
			<p id="qscroller">
			</p>
			
			<div class="qhide">
				<?php foreach ($recent_activity as $activity): ?>
				<div class="qslide">
					<a href="<?php echo $activity["url"]; ?>"><?php echo $activity["name"]; ?></a> was <?php echo $activity["text"]; ?>.
				</div>
				<?php endforeach; ?>
			</div>
		</p>
		
		<script type="text/javascript">
			window.addEvent("domready", function(event){
				// create the scroller and start scrolling
				// recent activity
				scroller = new QScroller("qscroller", {
					duration: 1000,
					delay: 3000,
					auto: true,
					onMouseEnter: function() { this.stop(); },
					onMouseLeave: function() { this.play(); }});
				scroller.load();
			});
		</script>
		
		<?php $this->load->view("common/footer"); ?>
	</body>
</html>
 