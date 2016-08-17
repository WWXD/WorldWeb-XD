"use strict";

var Overlay = function(options) {
	var defaults = {
		title: "Popup",
		content: "Content",
		hasButtons: false,
		buttons: []
	};

	//TODO: Merge options and defaults

	var shade = document.createElement("div");
	shade.className = "overlayShade";

	var body = document.getElementsByTagName("body")[0];
	body.appendChild(shade);

	var overlayContainer = document.createElement("div");
	overlayContainer.className = "message";
	shade.appendChild(overlayContainer);

	var overlayTitle = document.createElement("h3");
	overlayTitle.innerText = options.title;
	overlayContainer.appendChild(overlayTitle);

	var overlayContent = document.createElement("div");
	overlayContent.className = "overlayContent";
	overlayContent.innerHTML = options.content;
	overlayContainer.appendChild(overlayContent);

	if (options.hasButtons !== undefined && options.hasButtons !== false) {
		var buttonContainer = document.createElement("div");
		buttonContainer.className = "buttonContainer";
		overlayContainer.appendChild(buttonContainer);

		var i;
		for (i in options.buttons) {
			if (options.buttons[i]) {
				buttonContainer.appendChild(options.buttons[i].construct(this));
			}
		}
	}

	this.destroy = function() {
		body.removeChild(shade);
	}
};

var Button = function(label, callback, preferred) {
	var button = document.createElement("button");
	button.innerHTML = label;

	button.addEventListener("click", callback, false);

	this.construct = function(overlay) {
		return button;
	};
};
