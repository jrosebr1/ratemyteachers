var Rating_Form = new Class({
	// define the class that the rating form extends
	Extends: AJAX_Form,
	
	initialize: function(options)
	{
		// call the parent constructor
		this.parent(options);
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

		// split the string of field names into a list		
		fieldNames = this.options["numerical_field_names"].split(",");
		
		// loop over the field names
		for (enumFields = 0; enumFields < fieldNames.length; enumFields++)
		{
			// grab the selected value for the current field and update
			// the data dictionary for the request
			value = formElement.getElements("input[name=" + fieldNames[enumFields] + "]:checked").get("value")[0];
			data[fieldNames[enumFields]] = (value == null) ? "" : value;
		}

		// return the form data
		return data;
	}	
});