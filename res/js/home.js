import { SystemFn, $ } from "./lib/utilities.js";
import Request from "./lib/request.js";
import Contextmenu from "./lib/html/contextmenu.js";
import ConfirmDialog from "./lib/html/confirmdialog.js";
import Toast from "./lib/html/toast.js";

SystemFn(function() {
	const show_menu = function(id) {

		const
			toastPosition = "top-right",
			toastTimeout = 5,
			m = new Contextmenu({
				title: "Menu",
				voices: {
					1: {
						label: "Add note",
						click: function() {
							window.location.href = `/serjeant/add-note/${id}`;
						}
					}
				}
			});

		$().appendChild(m).then(() => {
			Contextmenu.setMenuPos(m);
		});
	};

	$(".issue").each(i => {
		const 
			ths = $(i),
			id = ths.attr("data-id");

		ths.on("contextmenu", function(t) {
			const e = window.event;
			e.preventDefault();

			show_menu(id);
		});
	});
});