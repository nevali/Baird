MainMenu = function()
{
	var _this = this;
	this.KEY_UP = 38;
	this.KEY_DOWN = 40;
	this.KEY_LEFT = 37;
	this.KEY_RIGHT = 39;
	this.KEY_ENTER = 13;
	this.KEY_ESC = 27;
	this.KEY_SPACE = 32;
	this.switchSection($('#carousel > section').first());
	$(document).keyup(function(ev) { _this.onKeypress(ev); });
}
MainMenu.prototype.switchSection = function(el)
{
	if(this.section)
	{
		this.section.removeClass('active');
		this.menu.removeClass('active');
	}
	this.section = el;
	this.current = this.section.attr('id');
	console.log('switched to ' + this.current);
	this.section.addClass('active');
	this.menu = $('#nav-' + this.current);
	this.nav = $('nav');
	if(this.menu[0].selectedMenuItem)
	{
		console.log('switching menu item');
		this.selectMenuItem($(this.menu[0].selectedMenuItem));
	}
	else
	{
		console.log('no previously-selected menu item');
		this.selectMenuItem(this.menu.find('li').first());
	}
}
MainMenu.prototype.prevSection = function()
{
	return this.section.prev('section');
}
MainMenu.prototype.nextSection = function()
{
	return this.section.next('section');
}
MainMenu.prototype.selectMenuItem = function(el)
{
	if(this.menuItem)
	{
		this.menuItem.removeClass('selected');
	}
	this.menuItem = el;
	el.addClass('selected');
	this.section.removeClass('selected');
	this.nav.removeClass('inactive');
	this.menu.addClass('active');
	this.menu[0].selectedMenuItem = el[0];
}
MainMenu.prototype.selectCarousel = function()
{
	if(this.menuItem)
	{
		this.menuItem.removeClass('selected');
	}
	this.menuItem = null;
	this.menu[0].selectedMenuItem = null;
	this.section.addClass('selected');
	this.nav.addClass('inactive');
}
MainMenu.prototype.prevMenuItem = function()
{
	if(this.menuItem)
	{
		return this.menuItem.prev('li');
	}
	return null;
}
MainMenu.prototype.nextMenuItem = function()
{
	if(this.menuItem)
	{
		return this.menuItem.next('li');
	}
	return this.menu.find('li').first();
}
MainMenu.prototype.onKeypress = function(ev)
{
	switch(ev.keyCode)
	{
		case this.KEY_UP:
			this.up();
			break;
		case this.KEY_DOWN:
			this.down();
			break;
		case this.KEY_LEFT:
			this.left();
			break;
		case this.KEY_RIGHT:
			this.right();
			break;
	}
}
MainMenu.prototype.up = function()
{
	console.log('up');
	if((i = this.prevMenuItem()) && i.length)
	{
		this.selectMenuItem(i);
	}
	else
	{
		this.selectCarousel();
	}
}
MainMenu.prototype.down = function()
{
	var i;
	
	console.log('down');
	if((i = this.nextMenuItem()) && i.length)
	{
		this.selectMenuItem(i);
	}
}
MainMenu.prototype.left = function()
{
	var s;
	
	console.log('left');
	if(this.menuItem)
	{
		if((s = this.prevSection()) && s.length)
		{
			this.switchSection(s);
		}
	}
}
MainMenu.prototype.right = function()
{
	var s;
	
	console.log('right');
	if(this.menuItem)
	{
		if((s = this.nextSection()) && s.length)
		{
			this.switchSection(s);
		}
	}
}

$(document).ready(function() {
	var mainMenu = new MainMenu();
})