//Fancy new thread tagging system
//By Nina

var ThreadTagContainer = function(inside) {
	var self = this;

	this.containerElement = document.createElement("div");
	this.containerElement.style.display = "inline-block";

	var ThreadTag = function() {
		var self = this;

		this.tagHolder = document.createElement("span");
		this.tagElement = document.createElement("input");
		this.tagElement.style.color = "white";
		this.tagElement.maxLength = 16;
		this.tagElement.size = 16;
		this.tagElement.className = "threadTag";

		this.tagHolder.appendChild(this.tagElement);

		this.text = new String();

		this.calculateBackgroundColor = function() {
			var hash = -105;

			for (var i = 0, length = this.text.length; i < length; i++)
				hash += this.text.charCodeAt(i);

			var color = "hsl(" + ((hash * 57) % 360) + ", 70%, 40%)";
			return color;
		}

		this.tagElement.style.background = this.calculateBackgroundColor();

		this.tagElement.addEventListener("keyup", function(e) {
			self.text = this.value; //Just to make it look cleaner.
			this.style.background = self.calculateBackgroundColor();
		}, false);

		this.destroy = function() {
			self.tagHolder.parentNode.removeChild(this.tagHolder);
			delete this;
		}

		this.setText = function(text) {
			self.text = text;
			self.tagElement.value = text;
			self.tagElement.style.background = self.calculateBackgroundColor();
		}

		this.tagDestroyButton = document.createElement("button");
		this.tagDestroyButton.type = "button";
		this.tagDestroyButton.innerHTML = "x";

		this.tagDestroyButton.addEventListener("click", function() {
			self.destroy();
			return false;
		}, false);

		this.tagHolder.appendChild(this.tagDestroyButton);
	}

	this.newTag = function(text) {
		var newTag = new ThreadTag();
		this.containerElement.appendChild(newTag.tagHolder);
		if (text)
			newTag.setText(text);
		newTag.tagElement.focus();
	}

	this.constructTitle = function(title) {
		var title = title;

		for (var i in this.containerElement.childNodes) {
			if (this.containerElement.childNodes[i].tagName == "SPAN" &&
				this.containerElement.childNodes[i].childNodes[0].value.trim() != "")
				title += " [" + this.containerElement.childNodes[i].childNodes[0].value + "]";
		}

		return title;
	}

	inside.appendChild(this.containerElement);

	this.addButton = document.createElement("button");
	this.addButton.innerHTML = "Add tag";
	this.addButton.type = "button";

	this.addButton.addEventListener("click", function(e) {
		self.newTag();
		return false;
	}, false);

	inside.appendChild(this.addButton);
}

window.addEventListener("load", function(e) {
	//First of all get a shortcut to the table cell we want
	var threadTitleContainer = document.getElementById('threadTitleContainer');

	//Iterate over the cell's object looking for an input box
	//When we find it, make a nice reference to it
	//... What we are doing here is not really that useful in the context of newthread, but it is in editthread.
	for (i in threadTitleContainer.childNodes) {
		if (threadTitleContainer.childNodes[i].type == "text") {
			var threadTitleEntry = threadTitleContainer.childNodes[i];
		}
	}
	threadTitleEntry.style.display = "none";

	//Now duplicate threadTitleEntry and insert a clone of it so we can work with th eoriginal
	var newThreadTitleEntry = threadTitleEntry.cloneNode(true);

	newThreadTitleEntry.name = "";
	newThreadTitleEntry.id = "";
	newThreadTitleEntry.style.display = "inline";

	threadTitleContainer.appendChild(newThreadTitleEntry);

	//Get the thread tag things going
	var threadTagContainer = new ThreadTagContainer(threadTitleContainer);

	//Add already existing tags to the list of thread tags
	newThreadTitleEntry.value = threadTitleEntry.value.replace(/\[(.*?)\]/g, function (full, tag) {
		threadTagContainer.newTag(tag);
		return "";
	}).trim();

	//Locate newTagTitleEntry's parent form

	var parentNode = threadTitleContainer.parentNode;

	while (parentNode.tagName != "FORM") {
		parentNode = parentNode.parentNode;
	}

	parentNode.addEventListener("submit", function(e) {
		threadTitleEntry.value = threadTagContainer.constructTitle(newThreadTitleEntry.value);
	});
});
