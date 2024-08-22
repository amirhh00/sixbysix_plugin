import React from "react";
import ReactDOM from "react-dom";
import { ComponentWrapper } from "./Component";

window.addEventListener("DOMContentLoaded", () => {
	const tcomponents = document.querySelectorAll(".mt-block-test2-wrapper");
	if (tcomponents) {
		// tcomponent.forEach((element) => {
		Array.from(tcomponents).forEach((tcomponent: HTMLElement) => {
			const attributes = JSON.parse(tcomponent.dataset.mtAttributes);
			ReactDOM.hydrate(
				<ComponentWrapper attributes={attributes} />,
				tcomponent,
			);
		});
	}
});
