class Panel extends HTMLElement {

	constructor() {
		super();

		if ("category" in this.dataset) {
			window.localStorage.setItem("panel-category", this.dataset.category);
			this.dataset.category = window.localStorage.getItem("panel-category") || 0;
			this.dataset.expand = true;
		} else {
			this.dataset.category = window.localStorage.getItem("panel-category") || 0;
		}

		if ("expand" in this.dataset) {
			window.localStorage.setItem("panel-expand", this.dataset.expand);
		} else {
			this.dataset.expand = window.localStorage.getItem("panel-expand") || false;
		}

	}

	close() {
		if (this.dataset.expand == "true") {
			this.dataset.expand = false;
		}
		window.localStorage.setItem("panel-expand", false);
	}

	expand() {
		if (this.dataset.expand == "false") {
			this.dataset.expand = true;
		}
		window.localStorage.setItem("panel-expand", true);
	}

	category(name) {
		let category = this.dataset.category;
		if (category == name) {
			window.localStorage.setItem("panel-category", 0);
			this.dataset.category = 0;
			this.close();
			return;
		} else {
			const categories = this.querySelectorAll(".panel-category");
			// console.log(categories);
			for (let cat of categories) {
				if (cat.dataset.element_category == name) {
					this.expand();
					this.dataset.category = name;
					window.localStorage.setItem("panel-category", name);
					break;
				}
			};
		}
	}
}

class PanelButton extends HTMLElement {

	constructor() {
		super();

		this.addEventListener('click', this.click, true);
	}

	click() {
		if (this.getAttribute("href")) {
			window.location = this.getAttribute("href");
		} else {
			this.category();
        }
    }

	category() {
		let panel = document.querySelector("#panel");
		panel.category(this.dataset.button_category);
	}

}


// Define the new element
customElements.define('manage-panel', Panel);
customElements.define('panel-button', PanelButton);

