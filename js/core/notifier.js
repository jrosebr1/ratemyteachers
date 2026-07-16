var Notifier =
{
	// initialize the effect that the notifier uses for opening and
	// closing
	effect: null,
	// initialize the delay the notifier uses when staying open and
	// then closing again
	delay: null,
	// define the options used by the notifier
	options: {},
	// define the new set of options used the notifier if another
	// notifier is tried to open overtop of the old one
	newOptions: {},
	// define the indicator variable to show if the notifier is created
	// or not
	created: false,
	// define the indicator variable used to show if the notifier is
	// currently open or not
	isOpen: false,
 
	create: function(options)
	{
		// store the notifier options
		Notifier.newOptions = options;
   
		// check to see if the notifier is created or not
		if (!Notifier.created)
		{
			// define the element to hold the notifier as well as the
			// element to hold the notifier message
			notifierElement = new Element("div", {"id": "notifier"});
			messageElement= new Element("div", {"id": "message"});

			// check to see if the notifier is supposed to be closeable
			// or not
			if (Notifier.newOptions["closeable"])
			{
				// define the image used for the close icon and then
				// add it to the DOM
				closeElement = new Element("img", {
					"id": "close",
					"src": "/images/notifier_close.png",
					"events": {"click": function(){
						Notifier.hide(null);
						}
				}});
				closeElement.inject(notifierElement);
			}
			
			// hide the notifier until it is ready to be opened
			notifierElement.setStyle("display", "none");
    
			// add the notifier to the DOM
			messageElement.inject(notifierElement);
			notifierElement.inject($(document.body), "top");

			// update the created indicator to show that the notifier
			// has been created
    	  Notifier.created = true;
	     }
	},
 
	open: function(message)
	{
		// check if the notifier is already opening when trying to
		// show another notification
		if (Notifier.isOpen)
		{
			// if the notifier is already open, close it immediately
			$clear(Notifier.delay);
			Notifier.effect.cancel();
			Notifier.hide(message);
			return;
		}

		// update the indicator that the notifier is currently open,
		// update the options, and open the notifier window
		$("notifier").setStyle("display", "block");
		Notifier.isOpen = true;
		Notifier.options = Notifier.newOptions;
		Notifier.effect = new Fx.Tween($("notifier")).start("height", 0, 70).chain(function(){
			// if the notifier is closeable, show the close icon
			if (Notifier.options["closeable"])
			{
				$("close").setStyle("display", "block");
			}

			// show the notifier message
			Notifier.displayText(message);

			// if the notifier is not persistent and there is a delay,
			// option set the delay for the notifier to close
			if (!Notifier.options["persistent"] && Notifier.options["delay"])
			{
				Notifier.delay = Notifier.hide.delay(Notifier.options["delay"]);
			}
		});
	},
   
	hide: function(message)
	{
		// if the notifier is closeable, then hide the close icon
		if (Notifier.options["closeable"])
		{
			$("close").setStyle("display", "none");
		}

		// reset the message content and start the close animation
		$("message").innerHTML = "";
		Notifier.effect = new Fx.Tween($("notifier")).start("height", $("notifier").getStyle("height"), 0).chain(function()
			{
				// update the indicator to show the notifier is not
				// open and reset the notifier
				Notifier.isOpen = false;
				Notifier.destroy();
                
                // if the message is not empty, then there is another
                // notifier waiting to be opened, so go ahead and open
                // it
				if (message != null)
				{
					Notifier.open(message);
				}
		});
	},
   
	destroy: function()
	{
		// destroy the notifier element, update the indicator to show
		// that the notifier is not currently created, and update the
		// notifier options
		$("notifier").destroy();
		Notifier.created = false;
		Notifier.create(Notifier.newOptions);
	},
   
	displayText: function(message)
	{
		// upde the notifier message and then show the message
		$("message").innerHTML = message;
		$("message").setStyle("display", "block");
	}
};