"use strict";

/* This plugin adds titles to the board header sort of like those in
 * Minecraft.
 *
 * ~Nina
 */

var makeMcTitle = function(title) {
	var boardHeader = document.getElementById("theme_banner");
	var mcTitle = document.createElement("span");
	mcTitle.className = "mcTitle";
	mcTitle.appendChild(document.createTextNode(title));

	boardHeader.parentNode.parentNode.insertBefore(mcTitle, boardHeader.parentNode.nextSibling);
};
