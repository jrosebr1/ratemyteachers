var PersonFilter =
{
	// initialize the list of filter options
	options: null,
	
	init: function(options)
	{
		// store the filter options
		PersonFilter.options = options;
	},
	
	addEvents: function()
	{
		// get the alphabet filter elements so we can add an
		// event to each of them
		$$(".alpha_filter").addEvent("click", function(event){
			// stop the event from propagating any further
			event.stop();
			event.stopPropagation();

			// figure out which letter was clicked
			letter = this.get("html").toUpperCase();
													
			// if the letter is 'ALL', then make the letter blank
			if (letter == "ALL")
			{
				letter = "";
			}
													
			// set the filter letter and apply the filter
			PersonFilter.setLetter(letter);
			PersonFilter.filter();
		});

		// get the department filter element so we can add an
		// event to each of the options
		$("dept_filter").addEvent("change", function(event){
			// get the value of the currently selected department
			dept = this.getSelected().get("value")[0].toLowerCase();

			// if the department is 'all departments' then make
			// it blank
			if (dept == "all departments")
			{
				dept = "";
			}
													
			// set the department filter and apply the filter
			PersonFilter.setDept(dept);
			PersonFilter.filter();
		});

		// get the sort filter elements so we can change
		// the column and direction we are sorting on
		$$(".sort_filter").addEvent("click", function(event){
			// stop the event from propagating any further
			event.stop();
			event.stopPropagation();
													
			// get the mapping from the selected columns
			mapping = PersonFilter.options["order_mappings"][this.get("html")];

			// get the current sorting direction
			dir = PersonFilter.options["order_dir"];
													
			// switch the direction of the order
			switch (dir)
			{
				case "ASC":
					dir = "DESC";
					break;

				case "DESC":
					dir = "ASC";
					break;
			}
													
			// update the ordering information and apply the filter
			PersonFilter.setOrder(mapping, dir);
			PersonFilter.filter();
		});
	},
	
	setOption: function(option, value)
	{
		// update the options dictionary with a new value
		PersonFilter.options[option] = value;
	},
	
	setLetter: function(letter)
	{
		// update the letter
		PersonFilter.setOption("letter", letter);
	},
	
	setDept: function(dept)
	{
		// update the department
		PersonFilter.setOption("dept", dept);
	},
	
	setOrder: function(name, dir)
	{
		// update the order by and order direction
		PersonFilter.setOption("order_by", name);
		PersonFilter.setOption("order_dir", dir);
	},
	
	filter: function()
	{
		// create a form element and an input element to store the
		// encoded filter
		formElement = new Element("form", {
			"id": "person_filter",
			"action": "/person_filter",
			"method": "post"});
		inputElement = new Element("input", {
			"name": "person_filter",
			"type": "hidden",
			"value": JSON.encode(PersonFilter.options)});

		// add the input element to the form and then add the form
		// element to the document
		formElement.grab(inputElement);
		$(document.body).grab(formElement);
		
		// submit the form
		formElement.submit();
	}
};