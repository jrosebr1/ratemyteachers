var TextLimiter =
{
	limitText: function(inputElement, counterElement, maxChars)
	{
		// if the number of characters in the element is greater than the
		// maximum number of characters, then truncate it
		if (inputElement.value.length > maxChars)
		{
			inputElement.value = inputElement.value.substring(0, maxChars);
		}
		
		// otherwise, update the counter element value
		else
		{
			counterElement.innerHTML = maxChars - inputElement.value.length;
		}
	}
};