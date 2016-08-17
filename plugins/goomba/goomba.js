function Goomba() {
	this.x = 0;
	this.y = 0;
	this.xVel = 0;
	this.yVel = 0;

	this.goomba = document.createElement('div');

	$(this.goomba).addClass('goomba');
	$('body').append(this.goomba);

	if(Math.random() > 0.5) {
		this.position = $(document).width();
		this.direction = -1;
	} else {
		this.position = -15;
		this.direction = 1;
	}

	this.startWalking = function() {
		this.goomba.addEventListener("click", this.stomp.bind(this), false);

		setInterval(function () {
			if (this.stomped) {
				return;
			}

			this.position += this.direction;
			if (this.position > $(document).width() || this.position < -16) {
				if (Math.random() > 0.5) {
					this.position = $(document).width();
					this.direction = -1;
				} else {
					this.position = -15;
					this.direction = 1;
				}
			}
			this.goomba.style.left = this.position + "px";
			this.step = (this.step + 1) % 2;
			this.goomba.style.backgroundPosition = "-" + (this.step * 16) + "px 0px";
		}.bind(this), 100);
	};

	this.spawnOnPage = function(x, y) {
		this.x = x;
		this.y = y;
		this.xVel = Math.random() * 10 - 5;
		this.yVel = Math.random() * 400 + 200;
		this.initTime = new Date;

		this.movementInterval = setInterval(function() {
			// Pixels per second squared (around 9.8 cm s^-2)
			var GRAVITY = 370;

			this.x += this.xVel;

			//If we are about to go off the screen, we should stop that.
			if (this.x > document.documentElement.clientWidth - 16 || this.x < 0) {
				this.xVel = -this.xVel;
			}

			//Decrease x velocity
			this.xVel -= this.xVel / 250;

			var time = (new Date - this.initTime) / 1000
			this.y = y + GRAVITY * Math.pow(time, 2) / 2 - this.yVel * time;

			this.goomba.style.top = this.y + "px";
			this.goomba.style.left = this.x + "px";

			if (this.y > document.documentElement.clientHeight - 16) {
				clearInterval(this.movementInterval);
				
				this.position = Math.round(this.x);
				this.goomba.style.top = "";
				this.goomba.style.bottom = "0px";
				this.startWalking();
			}
		}.bind(this), 10);
	};

	this.stomp = function () {
		if (this.stomped) {
			return;
		}

		this.stomped = true;
		this.goomba.style.backgroundPosition = "-32px 0px";
		this.audio.play();
		clearInterval(this.interval);

		setTimeout(function () {
			this.goomba.style.display = "none";
		}.bind(this), 500);
	};

	this.step = 0;

	this.audio = new Audio(resourceLink("plugins/goomba/goomba.ogg"));
}

var goombaKeyBuffer = "";

window.addEventListener("keypress", function(ev) {
	var cc = String.fromCharCode(ev.charCode);
	goombaKeyBuffer += cc;
	
	if ("goomba".slice(0, goombaKeyBuffer.length) == goombaKeyBuffer.toLowerCase()) {
		if (goombaKeyBuffer.length == 6) {
			var goomba = new Goomba();
			goomba.spawnOnPage(document.documentElement.clientWidth / 2, document.documentElement.clientHeight / 2);
			goombaKeyBuffer = "";
		}
	} else {
		goombaKeyBuffer = "";
	}
}, false);
