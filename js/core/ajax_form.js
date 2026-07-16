var AJAX_Form = new Class({
	// define the list of classes we are implementing
	Implements: [Options],
	
	// define default options that can later be overridden during
	// initialization of the class
	options: {
		"redirect_url": "/",
		"inputs": {
			"input": ["text", "hidden"]
		},
		"modal": {
			"width": 400,
			"height": 200,
			"delay": 400},
		"error_classes": {
			"error_container": "sb_error",
			"error_list": "sb_error_list"
		},
		"request": {
			"no_cache": true
		}
	},
	
	initialize: function(options)
	{
		// store the options
		this.setOptions(options);
	},
	
	getFormData: function(formElement)
	{
		// initialize the data object
		data = {}
		
		// loop over the elements in the form to check
		for (elementName in this.options.inputs)
		{
			// get the elements in the form that match the current
			// element name
			elements = formElement.getChildren(elementName);
			
			// loop over the elements
			for (enumElements = 0; enumElements < elements.length; enumElements++)
			{
				// get the current element and element type
				element = elements[enumElements];
				type = element.getProperty("type").toLowerCase();
				
				// loop over the element types that we are interested in
				for (enumTypes = 0; enumTypes < this.options.inputs[elementName].length; enumTypes++)
				{
					// get the current type
					currentType = this.options.inputs[elementName][enumTypes];
					
					// if the current type matches the element type, then
					// the element value should be added to the data
					if (currentType == type)
					{
						data[element.getProperty("name")] = element.getProperty("value");
						break;
					}
				}
			}
		}

		// return the form data
		return data;
	},

	submit: function(formElement)
	{
		// grab the data from the form
		data = this.getFormData(formElement);
		
		// construct and send the AJAX request
		request = new Request.JSON({
			"url": formElement.getProperty("action"),
			"method": formElement.getProperty("method"),
			"data": data,
			"noCache": this.options.request.no_cache,
			"onSuccess": function(obj, text){
				// pass the successful request off the process handler
				this.process(obj);
			}.bind(this),
			"onError": function(text, error){
				// an error has occurred; pass it off to the error
				// handler
				this.error(this.options.error_msg);
			}.bind(this),
			"onFailure": function(xhr){
				// an error has occurred; pass it off to the error
				// handler
				this.error(this.options.error_msg);
			}.bind(this),
		});
		request.send();
	},

	process: function(obj)
	{
		// if the 'success' attribute is '1' then the object was
		// successfuly added
		if (obj.success == 1)
		{
			// redirect to the supplied page
			document.location = this.options.redirect_url;
		}
		
		// otherwise, request was not successful
		else
		{
			// create the error list element
			ulElement = new Element("ul", {
				"class": this.options.error_classes.error_list});
		
			// loop over each of the errors
			for (errorKey in obj.errors)
			{
				// create a list element for each of the errors and
				// insert the error element into the list
				liElement = new Element("li", {
					"html": obj.errors[errorKey]});
				ulElement.grab(liElement);
			}
			
			// insert the error list into the DOM and show the modal
			// window
			$(document.body).grab(ulElement);
			this.show(ulElement);
		}
	},

	addEvents: function(inputElement, counterElement, maxChars)
	{
		// add the 'keyup' event to count the number of characters in
		// the input element
		inputElement.addEvent("keyup", function(event){
			TextLimiter.limitText(inputElement, counterElement, maxChars);
		});

		// add the 'keydown' event to the count the number of characters
		// in the input element
		inputElement.addEvent("keydown", function(event){
			TextLimiter.limitText(inputElement, counterElement, maxChars);
		});
	},
	
	error: function(text)
	{
		// create the error element, insert the element into the DOM and
		// then show the modal window
		divElement = new Element("div", {
			"class": this.options.error_classes.error_container,
			"html": text});
		$(document.body).grab(divElement);
		this.show(divElement);
	},
	
	show: function(element)
	{
		// initialize squeezebox and and show the window
		SqueezeBox.initialize({"size": {"x": this.options.modal.width, "y": this.options.modal.height}});
		SqueezeBox.open(element, {"handler": "adopt"});
		(function() { element.setStyle("display", "block"); }).delay(this.options.modal.delay);
	}
});