import { SystemFn, $ } from "./lib/utilities.js";

SystemFn(function() {
	const filter_issues = function(term) {
		$(".issue").each(i => {
			const ths = i;

			i = $(i);

			const
				title = i.getIfExists(".title").value().toLowerCase().trim(),
				subtitle = i.getIfExists(".subtitle").value().toLowerCase().trim();
	 
	 		if(title.includes(term) || subtitle.includes(term)) {
	 			ths.show();
	 		} else {
	 			ths.hide();
	 		}
		});
	};

	$("#issue-finder").on("keyup", function(ths) {
		filter_issues(ths.value.toLowerCase().trim());
	});
});