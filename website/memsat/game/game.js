function onscreen() {
    $('#controls').toggle(500);   
}

var audio = true;

pressKey = null;
releaseKey = null;
onmusic = null;

$(document).ready(function() {
	//init Crafty with FPS of 50 and create the canvas element
    var RESISTOR = "resistor";
    var CAPACITOR = "capacitor";
    var INDUCTOR = "inductor";
    var MOSFET = "mosfet";
    var MEMRISTOR = "memristor";
    var MEMRISTOR_HIGH_R = false;
    var MAX_ENEMY_HIT_POINTS = 5;
    var PLAYER_HP = 3;
    var IMMUNITY_PERIOD = 100;
    
    var height = 500;
    var width = 960;
    if (window.innerWidth < 960) {
        // Optimize screen for mobile
        height = window.innerHeight * 0.8;
        width = window.innerWidth - 48;
    }
	Crafty.init(width, height, document.getElementById('game'));
//	Crafty.canvas();
	
	//preload the needed assets
	Crafty.load({ "images" : ["memsat/game/sprite.png", "memsat/game/bg.png", "memsat/game/bullets.png"]}, function() {
		//splice the spritemap
		Crafty.sprite(64, "memsat/game/sprite.png", {
			ship: [0,0],
			lo_f_hi_v: [1,0],
			hi_f_lo_v: [2,0],
			lo_f_lo_v: [3,0],
			hi_f_hi_v: [4,0]
		});
        
        Crafty.sprite(7, "memsat/game/bullets.png", {
            resistor:  [0,0],
            capacitor: [1,0],
            inductor:  [2,0],
            memristor_l: [3,0],
            memristor_h: [4,0],
            mosfet:      [5,0] 
        });
		
		//start the main scene when loaded
		Crafty.scene("main");
	});
    Crafty.audio.add('rocket', 'memsat/game/ship_moving.ogg');
    Crafty.audio.add('weapon', 'memsat/game/weapon.ogg');
    Crafty.audio.add('bgm', 'memsat/game/bgm.ogg');
    
    var colors = {}
    colors[RESISTOR] = "rgb(153, 51, 0)";
    colors[CAPACITOR] = "rgb(255, 255, 0)";
    colors[MOSFET] = "rgb(0, 255, 0)";
    colors[INDUCTOR] = "rgb(255, 255, 255)";
    colors["m1"] = "rgb(255, 51, 153)";
    colors["m2"] = "rgb(0, 0, 255)";
    
    function createProjectile(type, ship) {
        var color = "rgb(255, 0, 0)"
        if (type == MEMRISTOR) {
            if (MEMRISTOR_HIGH_R) {
                color = colors["m1"];
                type += ", memristor_h";
            } else {
                color = colors["m2"];
                type += ", memristor_l";
            }
        } else {
            color = colors[type];
        }
        Crafty.e("2D, DOM, bullet, " + type)
            .attr({
                x: ship._x + 32, 
                y: ship._y + 32,
                rotation: ship._rotation, 
                xspeed: 20 * Math.sin(ship._rotation / 57.3), 
                yspeed: 20 * Math.cos(ship._rotation / 57.3),
                high_res: MEMRISTOR_HIGH_R
            })
            .origin("center")
//            .color(color)
            .bind("EnterFrame", function() {	
                this.x += this.xspeed;
                this.y -= this.yspeed;

                //destroy if it goes out of bounds
                if(this._x > Crafty.viewport.width || this._x < 0 || this._y > Crafty.viewport.height || this._y < 0) {
                    this.destroy();
                }
            });
        Crafty.audio.play('weapon');
    }
    
    function isA(object, type) {
        return object.obj._element.className.indexOf(type) > -1;
    } 
    
    function getHighScores() {
        // Local high
        if (localStorage['local_high'] != undefined) {
            try {
                high_score.text("Personal Best: " + localStorage['local_high']);
            } catch (E) {
                console.warn(E);
                setTimeout(getHighScores, 100);
                return;
            }
        }
        firebase.database().ref("/release/memsat/").once('value').then(function(snapshot) {
            var score = snapshot.val();
            var best = 0;
            var best_player = undefined;
            for (i in score) {
                if (best < score[i].score) {
                    best = score[i].score;
                    best_player = (score[i].username != undefined) ? score[i].username : 'Anonymous';
                }
            }
            global_high_score.text("Global: " + best + ' - ' + best_player);
        });
    }
    
    function publishScore(theScore) {
        setTimeout(getHighScores, 100); // This currently doesn't work.
        if (theScore == 0) {
            return; // We don't need this data.
        }
        localStorage['local_high'] = Math.max(theScore, localStorage['local_high']);
        userid = firebase.auth().currentUser;
        // Anonymous submissions
        updates = {
            score: theScore
        };
        if (firebase.auth().currentUser != undefined) {
            updates['user'] = firebase.auth().currentUser.uid;
            updates['username'] = firebase.auth().currentUser.displayName;
        }
        firebase.database().ref("/release/memsat/").push(updates);
    }
    
    if (localStorage['local_high'] == undefined || localStorage['local_high'] == NaN || localStorage['local_high'] == 'NaN') {
        localStorage['local_high'] = 0;
    }
    
    setTimeout(getHighScores, 100);
    
    high_score = undefined;
    global_high_score = undefined;
	
	Crafty.scene("main", function() {
        function incrementScore(inc) {
            player.score += inc;
            score.text("Score: "+player.score);
        }
        
        // Global touchcontrols
        var tX = 0;
        var tY = 0;
        document.getElementById('game').addEventListener('touchstart', function(e) {
            tX = e.touches[0].screenX;
            tY = e.touches[0].screenY;
        });
        document.getElementById('game').addEventListener('touchmove', function(e) {
/*            if (Math.abs(e.touches[0].screenX - tX) < 10) {
                e.touches[0].screenX = tX;
            }
            if (Math.abs(e.touches[0].screenY - tY) < 10) {
                e.touches[0].screenY = tY;
            }*/
            player.xspeed = (e.touches[0].screenX - tX) / 5;
            player.yspeed = (e.touches[0].screenY - tY) / 5;
//            var speed = Math.sqrt(Math.pow(player.xspeed, 2) + Math.pow(player.yspeed, 2));

            if (player.xspeed > 0 && player.yspeed < 0) {
                player.rotation = 45;
            } else if (player.xspeed > 0 && player.yspeed > 0) {
                player.rotation = 135;
            } else if (player.xspeed < 0 && player.yspeed < 0) {
                player.rotation = -45;
            } else {
                player.rotation = 225;
            }

//            player.rotation = Math.atan((e.touches[0].screenY - tY) / (e.touches[0].screenX - tX)) * 180 / Math.PI;
            tX = tX + (e.touches[0].screenX - tX) / 45;
            tY = tY + (e.touches[0].screenY - tY) / 45;
            console.log(player.xspeed, player.yspeed, player.rotation);
            e.preventDefault()
        });
        document.getElementById('game').addEventListener('touchend', function(e) {
            player.xspeed = 0;
            player.yspeed = 0;
        });

		Crafty.background("url('memsat/game/bg.png')")


        
        pressKey = function(keycode) {
            var e = $.Event("keydown");
            e.which = keycode;
            e.keyCode = keycode;
            player.trigger("KeyDown", e);
        }

        releaseKey = function(keycode) {
            var e = $.Event("keyup");
            e.which = keycode;
            e.keyCode = keycode;
            player.trigger("KeyUp", e);
        }
        
        onmusic = function() {
            audio = !audio;
            if (!audio) {
                $('#music').html('Enable Audio');
                Crafty.audio.mute();
            } else {
                $('#music').html('Disable Audio');
                Crafty.audio.unmute();
            }
        }
        Crafty.audio.stop('bgm');
        Crafty.audio.play('bgm', -1, 0.4);
		
		//score display
		var score = Crafty.e("2D, DOM, Text")
			.text("Score: 0")
			.attr({x: Crafty.viewport.width - 200, 
                   y: Crafty.viewport.height - 70,
                   w: 200,
                   h:50
                  })
			.css({color: "#fff"});
		var hp = Crafty.e("2D, DOM, Text")
			.text("HP: " + PLAYER_HP)
			.attr({x: 20, 
                   y: Crafty.viewport.height - 50,
                   w: 200,
                   h:50
                  })
			.css({color: "#fff"});
		high_score = Crafty.e("2D, DOM, Text")
			.text("LOADING PERSONAL BEST")
			.attr({x: Crafty.viewport.width - 200, 
                   y: Crafty.viewport.height - 50,
                   w: 200,
                   h: 20
                  })
			.css({color: "#fff"});
        global_high_score = Crafty.e("2D, DOM, Text")
			.text("LOADING GLOBAL BEST")
			.attr({x: Crafty.viewport.width - 200, 
                   y: Crafty.viewport.height - 30,
                   w: 200,
                   h: 20
                  })
			.css({color: "#fff"});
			
		//player entity
		var player = Crafty.e("2D, Canvas, ship, Controls, Collision")
			.attr({
                    move: {left: false, right: false, up: false, down: false}, 
                    xspeed: 0,
                    yspeed: 0,
                    decay: 0.9, 
                    level: 1,
				    x: Crafty.viewport.width / 2,
                    y: Crafty.viewport.height / 2,
                    hit_points: PLAYER_HP,
                    score: 0})
			.origin("center")
			.bind("KeyDown", function(e) {
				//on keydown, set the move booleans
				if(e.keyCode === Crafty.keys.RIGHT_ARROW) {
					this.move.right = true;
				} else if(e.keyCode === Crafty.keys.LEFT_ARROW) {
					this.move.left = true;
				} else if(e.keyCode === Crafty.keys.UP_ARROW) {
					this.move.up = true;
                    Crafty.audio.play('rocket', -1);
				} else if(e.keyCode === Crafty.keys.SPACE) {
					//create a bullet entity
					createProjectile(RESISTOR, this);
				} else if (e.keyCode == Crafty.keys.C) {
                    createProjectile(CAPACITOR, this);
                } else if (e.keyCode == Crafty.keys.M || e.keyCode == Crafty.keys.ENTER) {
                    createProjectile(MEMRISTOR, this);
                } else if (e.keyCode == Crafty.keys.I || e.keyCode == Crafty.keys.L) {
                    createProjectile(INDUCTOR, this);
                } else if (e.keyCode == Crafty.keys.N) {
                    createProjectile(MOSFET, this);
                }
			}).bind("KeyUp", function(e) {
				//on key up, set the move booleans to false
				if(e.keyCode === Crafty.keys.RIGHT_ARROW) {
					this.move.right = false;
				} else if(e.keyCode === Crafty.keys.LEFT_ARROW) {
					this.move.left = false;
				} else if(e.keyCode === Crafty.keys.UP_ARROW) {
					this.move.up = false;
                    Crafty.audio.stop('rocket');
				}
			}).bind("EnterFrame", function() {
				if(this.move.right) this.rotation += 5;
				if(this.move.left) this.rotation -= 5;
				
				//acceleration and movement vector
				var vx = Math.sin(this._rotation * Math.PI / 180) * 0.3,
					vy = Math.cos(this._rotation * Math.PI / 180) * 0.3;
				
				//if the move up is true, increment the y/xspeeds
				if(this.move.up) {
					this.yspeed -= vy;
					this.xspeed += vx;
				} else {
					//if released, slow down the ship
					this.xspeed *= this.decay;
					this.yspeed *= this.decay;
				}
                
                // Have some upper limit
                this.xspeed = Math.min(this.xspeed, 20);
                this.yspeed = Math.min(this.yspeed, 20);
                this.xspeed = Math.max(this.xspeed, -20);
                this.yspeed = Math.max(this.yspeed, -20);
				
				//move the ship by the x and y speeds or movement vector
				this.x += this.xspeed;
				this.y += this.yspeed;
				
				//if ship goes out of bounds, put him back
				if(this._x > Crafty.viewport.width) {
					this.x = -64;
				}
				if(this._x < -64) {
					this.x =  Crafty.viewport.width;
				}
				if(this._y > Crafty.viewport.height) {
					this.y = -64;
				}
				if(this._y < -64) {
					this.y = Crafty.viewport.height;
				}
				
				//if all asteroids are gone, start again with more
				if(asteroidCount <= 0) {
					initRocks(lastCount, lastCount * 1.3);
                    if (Math.random() > 0.5) {
                        this.hit_points++; // Get one more hit point, maybe
                    }
                    this.level++;
                    incrementScore(5);
                    this.immune = new Date().getTime() + 500; // An extra immunity period
				}
                
                if (this.immune + IMMUNITY_PERIOD > new Date().getTime()) {
                    this.addComponent('immune');
                    this.alpha = 0.5;
                } else {
                    this.removeComponent('immune');
                    this.alpha = 1;
                }
			}).collision()
			.onHit("enemy", function(e) {
				//if player gets hit enough times, restart the game
                if (this.immune + IMMUNITY_PERIOD > new Date().getTime()) {
                    console.log("I am immune");
                    return;
                }
                e[0].obj.destroy(); // Prevent double-kills
                asteroidCount--;
                this.immune = new Date().getTime();
                player.hit_points--;
                hp.text("HP: " + player.hit_points);
                
                if (player.hit_points <= 0) {
                    publishScore(player.score);
                    Crafty.scene("main");
                }
			})

		//keep a count of asteroids
		var asteroidCount,
			lastCount;
		
        function getBigSpeed() {
            var min = Math.max(3, player.level*0.4);
            var max = 20;
            var weight = player.level*0.8;
            return Math.max(Math.min(weight, max), min);
        }
        
        function getSmallSpeed() {
            var min = Math.max(1, player.level*0.4);
            var max = 4;
            var weight = player.level*0.3;
            return Math.max(Math.min(weight, max), min);
        }
        
        function getPosition() {
            if (Math.random() < 0.5) {
                return Crafty.math.randomInt(0, 64);   
            } else {
                return Crafty.viewport.width - Crafty.math.randomInt(0, 64);   
            }
        }
        
        function findSpeed(seed) {
            var s = Crafty.math.randomInt(-seed, seed);
            if (s == 0) {
                s = Math.random();
            }
            return s;
        }
        
		//Asteroid component
		Crafty.c("enemy", {
			init: function() {
				this.origin("center");
				this.attr({
					x: getPosition(), //give it random positions, rotation and speed
					y: Crafty.math.randomInt(0, Crafty.viewport.height),
					rspeed: Crafty.math.randomInt(-1, 1),
                    hi_freq: Math.random() > 0.5,
                    hi_volt: Math.random() > 0.5,
                    immune: 0,
                    hit_points: Crafty.math.randomInt(2, MAX_ENEMY_HIT_POINTS) // TODO Make random
				})
                .attr({
                    xspeed: findSpeed(getBigSpeed()),
                    yspeed: findSpeed(getSmallSpeed())
                })
                .addComponent((this.hi_freq) ? (this.hi_volt ? "hi_f_hi_v" : "hi_f_lo_v") : (this.hi_volt ? "lo_f_hi_v" : "lo_f_lo_v"))
                .bind("EnterFrame", function() {
					this.x += this.xspeed;
					this.y += this.yspeed;
					this.rotation += this.rspeed / 40;
					
					if(this._x > Crafty.viewport.width) {
						this.x = -64;
					}
					if(this._x < -64) {
						this.x =  Crafty.viewport.width;
					}
					if(this._y > Crafty.viewport.height) {
						this.y = -64;
					}
					if(this._y < -64) {
						this.y = Crafty.viewport.height;
					}
                    if (this.immune + IMMUNITY_PERIOD > new Date().getTime()) {
                        this.addComponent('immune');
                        this.alpha = 0.5;
                    } else {
                        this.removeComponent('immune');
                        this.alpha = 1;
                    }
				}).collision()
				.onHit("bullet", function(e) {
                    if (this.immune + IMMUNITY_PERIOD > new Date().getTime()) {
                        return;   
                    }
                    
					//if hit by a bullet increment the score
                    if (isA(e[0], RESISTOR)) {
                        this.immune = new Date().getTime();
                        e[0].obj.destroy(); //destroy the bullet
                        this.hit_points--;
                    } else if (isA(e[0], CAPACITOR) && this.hi_freq) {
                        this.immune = new Date().getTime();
                        e[0].obj.destroy(); //destroy the bullet
                        this.hit_points -= 3; // Is this too powerful?
                        // We're smart!
                        incrementScore(5);
                    } else if (isA(e[0], INDUCTOR) && !this.hi_freq) {
                        this.immune = new Date().getTime();
                        e[0].obj.destroy(); //destroy the bullet
                        this.hit_points -= 3;
                        // We're smart!
                        incrementScore(5);
                    } else if (isA(e[0], MEMRISTOR)) {
                        this.immune = new Date().getTime();
                        e[0].obj.destroy(); //destroy the bullet
                        if (this.hi_volt) {
                            MEMRISTOR_HIGH_R = !MEMRISTOR_HIGH_R; // Toggles a write op
                        }
                        if (e[0].obj.high_res) {
                            this.hit_points -= MAX_ENEMY_HIT_POINTS; // It's memristing!
                            incrementScore(3);
                        } else {
                            this.hit_points--; // Standard resistor
                        }
                    } else if (isA(e[0], MOSFET)) {
                        this.hit_points -= MAX_ENEMY_HIT_POINTS; // MOSFETs beat all
                        incrementScore(-5); // We're cheating! :'(
                    }
                    if (this.hit_points <= 0) {
                        incrementScore(10);
                        this.destroy();
                        asteroidCount--;
                    }
				});
				
			}
		});
		
		//function to fill the screen with asteroids by a random amount
		function initRocks(lower, upper) {
			var rocks = Crafty.math.randomInt(lower, upper);
			asteroidCount = rocks;
			lastCount = rocks;
			
			for(var i = 0; i < rocks; i++) {
				Crafty.e("2D, DOM, big, Collision, enemy");
			}
		}
		//first level has between 1 and 10 asteroids
		initRocks(2, 3);
	});

    // Disable scrolling keys
    window.addEventListener("keydown", function(e) {
        // space and arrow keys
        if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
            e.preventDefault();
        }
    }, false);
});
