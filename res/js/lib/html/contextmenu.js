import {$, isDeclared, dw, dh} from "../utilities.js";
import {element} from "./e.js";

export default class Contextmenu {
	title = "Menu";
	menuParams = {};

	constructor(data = {
		title: null,
		voices: {},
		closeOnClickOut: true,
		closeOnClickOver: true
	}) {
		this.closeMenus();
		if(isDeclared(data.title) && String(data.title)) {
			this.title = data.title;
		}
		this.menuParams = {
			type: "div",
			id: ["contextmenu"],
			class: ["contextmenu"],
			children: [
				element({
					type: "h4",
					id: ["menu-title"],
					text: this.title
				})
			]
		};
		this.setParams(data);
		let menu = element(this.menuParams);
		$(window).on("scroll", (d) => {
			this.closeMenus();
		});
		return menu;
	}

	closeMenus() {
		$(".contextmenu").each(m => {
			m?.remove();
		});
	}

	setParams(data) {
		let voices = data.voices;
		if (isDeclared(voices)) {
			if (typeof voices == "object" && !voices.isFunction()) {
				//Add menu voices
				for (let voice in voices) {
					let value = voices[voice];
					if (!value.isFunction() && typeof value == "object" && isDeclared(value.label)) {
						let params = {
							type: "a",
							class: ["contextmenu-item"],
							text: value.label
						};
						//Add action
						if (isDeclared(value.click) && value.click.isFunction()) params.click = () => {
							value.click.call();
							this.closeMenus();
						};
						this.menuParams.children.push(element(params));
					}
				}
			} else {
				console.warn("Expected object.");
			}
		} else {
			console.warn("Expected menu voices.");
		}

		//Add close menu btn
		this.menuParams.children.push(element({
			type: "a",
			class: ["contextmenu-item"],
			text: "Cancel",
			click: () => {
				this.closeMenus();
			}
		}));
	}

	static setMenuPos(menu) {
	    let 
	    	mousePos = document.body.mousepos(),
	    	x = mousePos.x,
	    	y = mousePos.y,
	    	top = y,
	    	left = x,
	    	menuWidth = menu.offsetWidth,
	    	menuHeight = menu.offsetHeight,
	    	parentWidth = menu.parentNode.offsetWidth,
	    	parentHeight = menu.parentNode.offsetHeight;

	    if((x + menuWidth) > parentWidth) {
	    	left = x - menuWidth;
	    } else if(x < menuWidth) {
	    	left = menuWidth / 4;
	    } else if(x > (parentWidth - menuWidth)) {
	    	left = parentWidth - menuWidth;
	    } else {
	    	// handle this case
	    }

	    if((y + menuHeight) > parentHeight) {
	    	top = y - menuHeight;
	    } else if(y < menuHeight) {
	    	top = menuHeight / 2;
	    } else if(y > (parentHeight - menuHeight)) {
	    	top = parentHeight - menuHeight;
	    } else {
	    	// handle this case
	    }

	    menu.addStyles({
	        "top": top + "px",
	        "left": left + "px"
	    });
	}
}