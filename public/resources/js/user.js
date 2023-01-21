class UserDetails extends HTMLElement {

	constructor() {
		super();

		let container = this;
		if (this.getAttribute("edit")) {
			let form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("enctype", "multipart/form-data");
			form.setAttribute("action", "/myart/users/" + this.getAttribute("u_id") + "/edit");
			container.appendChild(form);
			container = form;
		}

		let title = document.createElement("div");
		title.classList.add("user-name");

		let buttons = document.createElement("div");

		if (this.getAttribute("typeof")) {
			switch (this.getAttribute("typeof")) {
				case "add_new_user":
					let form = document.createElement("form");
					form.setAttribute("enctype", "multipart/form-data");
					form.setAttribute("method", "POST");
					form.setAttribute("action", "/myart/users/add");
					let img = document.createElement("input");
					img.classList.add("avatar");
					img.addEventListener("change", function () {
						const formPicture = img.files[0];
						const url = URL.createObjectURL(formPicture);
						const imgButton = img.querySelector("::file-selector-button");
						imgButton.style.backgroundImage = `url(${url})`;
					});
					img.setAttribute("type", "file");
					img.setAttribute("name", "avatar");
					img.setAttribute("value", "/public/avatars/default_pic.png");
					let new_title = document.createElement("div");
					new_title.classList.add("user-name");
					new_title.appendChild(img);
					let username = document.createElement("input");
					username.setAttribute("name", "username");
					username.setAttribute("placeholder", "Username");
					username.setAttribute("type", "text");
					new_title.appendChild(username);
					let email = document.createElement("input");
					email.setAttribute("name", "email");
					email.setAttribute("type", "text");
					email.setAttribute("placeholder", "Email");
					email.classList.add("email");
					let password = document.createElement("input");
					password.setAttribute("name", "password");
					password.setAttribute("placeholder", "Password");
					password.setAttribute("type", "text");

					form.appendChild(new_title);
					container = form;
					form.appendChild(email);
					form.appendChild(password);

					this.appendChild(form);

					let addUser = document.createElement("button");
					addUser.setAttribute("type", "submit");
					addUser.setAttribute("name", "action");
					addUser.setAttribute("value", "add_user");
					addUser.innerText = "Add user";
					buttons.appendChild(addUser);
					break;
			}
		}

		if (this.getAttribute("avatar")) {
			let img = document.createElement("img");
			img.classList.add("avatar");
			img.setAttribute("src", this.getAttribute("avatar"));
			title.appendChild(img);
		}

		if (this.getAttribute("username")) {
			let username = document.createElement("input");
			username.setAttribute("readonly", true);
			username.setAttribute("name", "username");
			username.setAttribute("type", "text");
			username.classList.add("editable");
			username.value = this.getAttribute("username");
			title.appendChild(username);
		}

		container.appendChild(title);
		if (this.getAttribute("email")) {
			let email = document.createElement("input");
			email.setAttribute("readonly", true);
			email.setAttribute("name", "email");
			email.setAttribute("type", "text");
			email.classList.add("email");
			email.classList.add("editable");
			email.value = this.getAttribute("email");
			container.appendChild(email);
		}

		if (this.getAttribute("edit")) {
			if (this.getAttribute("email")) {
				let initialemail = document.createElement("input");
				initialemail.setAttribute("readonly", true);
				initialemail.setAttribute("name", "initialemail");
				initialemail.setAttribute("type", "hidden");
				initialemail.value = this.getAttribute("email");
				container.appendChild(initialemail);
			}
			buttons.classList.add("controls");
			let edit = document.createElement("button");
			edit.setAttribute("type", "button");
			edit.addEventListener('click', this.edit, true);
			edit.innerText = "Edit";
			buttons.appendChild(edit);
		}
		if (this.getAttribute("transfer_owner")) {
			let transfer = document.createElement("button");
			transfer.setAttribute("type", "submit");
			transfer.setAttribute("name", "action");
			transfer.setAttribute("formaction", "/myart/users/" + this.getAttribute("u_id") + "/change-owner");
			transfer.setAttribute("value", "change_owner");
			transfer.innerText = "Transfer ownership";
			buttons.appendChild(transfer);
		}

		if (buttons) {
			container.appendChild(buttons);
		}
	}

	edit() {
		let form = this.closest('user-details').querySelector("form");
		let addFile = document.createElement("input");
		let editables = this.closest('user-details').querySelectorAll(".editable");
		for (let i = 0; i < editables.length; i++) {
			if (editables[i].getAttribute("readonly")) {
				editables[i].removeAttribute("readonly");
			} else {
				editables[i].setAttribute("readonly", true);
			}

		}

		let buttons = this.closest('user-details').querySelector(".controls");
		let editbtns = form.querySelectorAll('.edit');
		console.log(editbtns);
		if (editbtns.length > 0 ) {
			for (let i = 0; i < editbtns.length; i++) {
				editbtns[i].remove();
            }
		} else {

			addFile.setAttribute("type", "file");
			addFile.setAttribute("name", "avatar");
			addFile.classList.add("edit");
			form.appendChild(addFile);

			let submit = document.createElement("button");
			submit.setAttribute("type", "submit");
			submit.setAttribute("name", "action");
			submit.setAttribute("value", "edit_user");
			submit.classList.add("edit-buttons");
			submit.classList.add("edit");
			submit.innerText = "Save changes";
			buttons.appendChild(submit);

			let deleteUs = document.createElement("button");
			deleteUs.setAttribute("type", "submit");
			deleteUs.setAttribute("name", "action");
			deleteUs.classList.add("edit-buttons");
			deleteUs.setAttribute("formaction", "/myart/users/" + form.parentElement.getAttribute("u_id") + "/delete");
			deleteUs.classList.add("edit");
			deleteUs.setAttribute("value", "delete_user");
			deleteUs.innerText = "Delete user";
			buttons.appendChild(deleteUs);
		}
	}
}

customElements.define('user-details', UserDetails);