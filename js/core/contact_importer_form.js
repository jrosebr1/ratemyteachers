var Contact_Importer_Form = new Class({
	// define the class that the rating form extends
	Extends: AJAX_Form,
	
	initialize: function(options)
	{
		// call the parent constructor
		this.parent(options);
	},
	
	process: function(obj)
	{
		// if the 'success' attribute is '1' then the object was
		// successfuly added
		if (obj.success == 1)
		{	
			// loop over each of the imported contacts
			for (enumContacts = 0; enumContacts < obj.contacts.length; enumContacts++)
			{
				// add each contact to the contact list
				this.addContact(obj.contacts[enumContacts]);
			}

			// hide the contact form and show the contact list
			$(this.options["contact_form_id"]).setStyle("display", "none");
			$(this.options["contact_list_id"]).setStyle("display", "block");
		}
		
		// otherwise, request was not successful
		else
		{
			// show the error message
			this.error(obj.error);
		}
	},
	
	error: function(text)
	{
		// show the error message via Notifier
		Notifier.create({"persistent": true, "closeable": true});
		Notifier.open(text);
	},
	
	show: function()
	{
		// override the method so the parent isn't accidentally
		// called
	},
	
	getService: function(email)
	{
		// find the position of the '@' symbol
		pos = email.lastIndexOf("@");

		// if the pos is negative one, then a valid email address was
		// not entered
		if (pos == -1)
		{
			return "";
		}
		
		// extract the web address the service provider resides at,
		// then find hte first occurrence of the '.'
		webAddr = email.substring(pos + 1);
		pos = webAddr.indexOf(".");
		
		// extract and return the email service provider
		return webAddr.substring(0, pos);
	},
	
	addContact: function(contact)
	{
		// create a list element for the contact
		liElement = new Element("li");
		
		// create the input checkbox
		inputElement = new Element("input", {
			"type": "checkbox",
			"name": "contact",
			"value": contact,
			"checked": true
		});
		
		// create the span element to hold the contact email
		spanElement = new Element("span", {
			"html": contact
		});
		
		// add the checkbox and span to the list element
		liElement.grab(inputElement);
		liElement.grab(spanElement);

		// grab the unordered list element
		ulElement = $(this.options["contact_list_id"]).getElements("ul")[0];
		ulElement.grab(liElement);
	}
});