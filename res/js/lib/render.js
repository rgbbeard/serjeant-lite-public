import {SystemFn, $, isDeclared} from "./utilities.js";
import {element} from "./html/e.js";

const elementsRender = function() {
    // Ripple animate button
    $(".btn-ripple").each(btn => {
        $(btn).on("click", function() {
            btn.rippleAnimation();
        });
    });

    // Custom selectors
    $(".select-input").each(container => {
    	if(isDeclared(container)) {
	    	const rendered = container.getAttribute("rendered");

	    	if(!isDeclared(rendered)) {
	    		container.setAttribute("rendered", "true");
		    	const 
		    		$container = $(container),
		    		label = $container.getIfExists("label")?.first(),
					select = $container.getIfExists("select"),
					options = $(select).getIfExists("option"),
					select_id = label.getAttribute("for"),
		    		s = element({
		    			type: "ul"
		    		}),
		    		d = element({
		    			type: "p"
		    		});

		    	options.each(option => {
		    		const o = element({
		    			type: "li",
		    			text: option.textContent,
		    			attributes: {
		    				"data-value": option.value
		    			}
		    		});

		    		s.appendChild(o);
		    	});

		    	$container.appendChild(d);
		    	$container.appendChild(s);

		    	d.textContent = s.children[0]?.textContent;
				select.value(s.children[0]?.dataset.value.trim());
				s.style.left = `${d.offsetLeft}px`;

				d.on("click", function(p) {
					if(isDeclared(d.getAttribute("open"))) {
						s.removeAttribute("visible");
						d.removeAttribute("open");
					} else {
						s.setAttribute("visible", true);
						d.setAttribute("open", true);
					}
				});

				select.children({ objectify: true }).each(function(c) {
					if(c.hasAttribute("selected")) {
						d.textContent = c.textContent;		
						select.value(c.value.trim());
					}
				});

				$(s).children({ objectify: true }).each(c => {
					c.on("click", function(e) {
						if(e.target === c) {
							d.textContent = c.textContent;
							d.removeAttribute("open");
							s.removeAttribute("visible");
							select.value(c.dataset.value.trim());
						}
					});
				});
	    	}
    	}
    });

	$(".password-input").each(container => {
		if(isDeclared(container)) {
			const c = $(container);

			const shown = container.hasAttribute("shown");

			let
				password = c?.getIfExists("input")?.first(),
				btn = c?.getIfExists("span")?.first();

			password = $(password);

			password.attr("type", "password");

			btn?.on("click", () => {
				shown ? container.removeAttribute("shown") : container.setAttribute("shown", true);
			});

			if(shown) {
				password.attr("type", "password");
				btn.removeAttributes("password-shown");
			} else {
				password.attr("type", "text");
				btn.setAttribute("password-shown", "");
			}
		}
	});
};

SystemFn(elementsRender);
setInterval(elementsRender, 1000);