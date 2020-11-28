function xml(page, element) {
	const xml = new XMLHttpRequest();

	xml.onreadystatechange = function () {
		if (xml.readyState === 4 && xml.status === 200) {
			if (element !== null) {
				element.innerHTML = xml.responseText;
				return true;
			} else {
				return true;
			}
		}
	};

	xml.open('GET', `${page}`, true);
	xml.send();
}

function navHandler() {
	const buttons = document.querySelectorAll('nav div');

	buttons.forEach((button) => {
		button.addEventListener('click', () => {
			xml(`${button.id}.php`, document.querySelector('main'));
		});
	});
}

function duplicateNode(button) {
	const lastDiv = document.querySelector('.create-list-field');
	const parentDiv = document.querySelector('.add-list');
	const originalDiv = button.parentElement;
	const clonedDiv = originalDiv.cloneNode(true);
	clonedDiv.children[1].value = '';
	clonedDiv.children[1].placeholder = 'Additional list owner (email)';
	clonedDiv.children[1].name = 'additional-list-owner';
	clonedDiv.style.backgroundColor = '#ffd166';
	document.querySelector('.create-list-field').classList.add('div-disabled');

	if (clonedDivsCount < 2) {
		parentDiv.insertBefore(clonedDiv, lastDiv);
		clonedDivsCount++;
	} else {
		showModalNotification('warning');
	}
}

function showModalNotification(type) {
	document.querySelector(`.modal.${type}`).classList.add('modal-open');

	setTimeout(() => {
		document.querySelector(`.modal.${type}`).classList.remove('modal-open');
	}, 3000);
}

function showModal(type) {
	document.querySelector(`.modal.${type}`).classList.add('modal-open');
}

function hideModal(type) {
	document.querySelector(`.modal.${type}`).classList.remove('modal-open');
}

function validateCreateAccountForm(form) {
	if (validateEmail(form[0])) {
		form[1].removeAttribute('disabled');
		if (validatePassword(form[1])) {
			form[2].removeAttribute('disabled');
			if (validatePassword(form[2]) && comparePasswords(form[1], form[2])) {
				form[3].parentElement.classList.remove('invalid');
				form[3].removeAttribute('disabled');
				form[3].parentElement.classList.add('valid');
			} else {
				form[3].parentElement.classList.remove('valid');
				form[3].setAttribute('disabled', true);
				form[3].parentElement.classList.add('invalid');
			}
		}
	}
}

function validateEmail(email) {
	const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	const field = email.parentElement;

	if (regex.test(email.value)) {
		field.style.backgroundColor = '#e1f9eb';
		return true;
	} else {
		field.style.backgroundColor = '#ffd166';
		return false;
	}
}

function validatePassword(password) {
	const regex = /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,}$/;
	const field = password.parentElement;

	if (regex.test(password.value)) {
		field.style.backgroundColor = '#e1f9eb';
		return true;
	} else {
		field.style.backgroundColor = '#ffd166';
		return false;
	}
}

function comparePasswords(password, repeated) {
	const field = password.parentElement;

	if (password.value === repeated.value) {
		field.style.backgroundColor = '#e1f9eb';
		return true;
	} else {
		field.style.backgroundColor = '#ffd166';
		return false;
	}
}

function showPassword(passwordField) {
	const passwordInput = passwordField.previousElementSibling;

	if (passwordInput.type == 'password') {
		passwordField.classList.remove('fa-eye');
		passwordField.classList.add('fa-eye-slash');
		passwordInput.type = 'text';
	} else {
		passwordField.classList.add('fa-eye');
		passwordField.classList.remove('fa-eye-slash');
		passwordInput.type = 'password';
	}
}

function validateLogin(form) {
	if (validateEmail(form[0])) {
		form[1].removeAttribute('disabled');
		if (validatePassword(form[1])) {
			form[2].parentElement.classList.remove('invalid');
			form[2].removeAttribute('disabled');
			form[2].parentElement.classList.add('valid');
		} else {
			form[2].parentElement.classList.remove('valid');
			form[2].setAttribute('disabled', true);
			form[2].parentElement.classList.add('invalid');
		}
	}
}

function logout() {
	xml('inc/logout.inc.php', document.querySelector('main'));
	setTimeout(() => {
		window.location.reload();
	}, 1500);
}

function addArticle(button) {
	const article = button.previousElementSibling.children[1];
	const category = button.previousElementSibling.children[2];
	const unit = button.previousElementSibling.children[4];

	if (validateArticle(article.value, category.value)) {
		xml(
			`inc/manage-articles.inc.php?article=${article.value}&category=${category.value}&unit=${unit.value}&add-article`,
			document.querySelector('main')
		);

		showModalNotification('success');

		setTimeout(() => {
			xml('manage-articles.php', document.querySelector('main'));
		}, 100);
	} else {
		showModalNotification('failure');
	}
}

function validateArticle(article, category) {
	if (
		article.length > 2 &&
		(category !== null || category !== undefined || category !== '')
	) {
		return true;
	} else {
		return false;
	}
}

function deleteArticle(button) {
	const article = button.previousElementSibling.children[1];

	xml(
		`inc/manage-articles.inc.php?article=${article.value}&delete-article`,
		document.querySelector('main')
	);

	showModalNotification('success');

	setTimeout(() => {
		xml('manage-articles.php', document.querySelector('main'));
	}, 100);
}

function addList(form) {
	if (validateListName(form[0])) {
		form[1].removeAttribute('disabled');
		if (validateEmail(form[1])) {
			form[1].nextElementSibling.classList.remove('button-disabled');
			form.lastElementChild.classList.remove('div-disabled');

			var ownersCount = form.childElementCount - 2;

			if (ownersCount > 1) {
				form.lastElementChild.classList.add('div-disabled');
				if (validateEmail(form[ownersCount])) {
					form.lastElementChild.classList.remove('div-disabled');
				}
			}
		}
	}
}

function createList(form) {
	var owners = [];

	var ownersCount = form.childElementCount - 2;

	for (let owner = 1; owner <= ownersCount; owner++) {
		owners.push(form[owner].value);
	}

	var incPath = `inc/manage-lists.inc.php?listName=${form[0].value}`;

	for (let owner = 0; owner < owners.length; owner++) {
		incPath += `&owner${owner}=${owners[owner]}`;
	}

	incPath += '&create-list';

	if (xml(incPath, document.querySelector('main')) !== false) {
		showModalNotification('success');

		// setTimeout(() => {
		// 	xml('my-lists.php', document.querySelector('main'));
		// }, 100);
	} else {
		showModalNotification('failure');
	}
}

function validateListName(listName) {
	const regex = /^(\w|\s){3,50}$/;
	const field = listName.parentElement;

	if (regex.test(listName.value)) {
		field.style.backgroundColor = '#e1f9eb';
		document.querySelector('.create-list-field p').style.pointerEvents = 'all';
		return true;
	} else {
		field.style.backgroundColor = '#ffd166';
		return false;
	}
}

function validatePrice(price) {
	const regex = /^\d{0,4}.?\d{1,2}$/;
	const field = price.parentElement.parentElement;
	const button = price.nextElementSibling;

	if (regex.test(parseFloat(price.value))) {
		button.classList.remove('fa-shopping-basket');
		button.classList.add('fa-edit');
		field.style.backgroundColor = '#bed7c8b3';
		field.classList.add('article-bought');
		return true;
	} else {
		field.style.backgroundColor = '#ffd166';
		return false;
	}
}

function showList(list) {
	xml(
		`inc/manage-lists.inc.php?listId=${list.id}&listName=${list.textContent}&show-list`,
		document.querySelector('.my-lists .list-detail')
	);
}

function changePrice(articleId, listId, button) {
	var price = button.parentElement.children[0];

	if (validatePrice(price)) {
		xml(
			`inc/manage-lists.inc.php?articleId=${articleId}&listId=${listId}&articlePrice=${parseFloat(
				price.value
			)}&change-price`,
			null
		);
	}
}

function updateTotalValue(listId) {
	xml(
		`inc/manage-lists.inc.php?listId=${listId}&update-total-value`,
		document.querySelector('.list-total-value')
	);
}

function deleteArticleFromList(articleId, listId, button) {
	const articleDiv = button.parentElement.parentElement;

	articleDiv.remove();

	xml(
		`inc/manage-lists.inc.php?articleId=${articleId}&listId=${listId}&delete-article-from-list`,
		null
	);
}

function showArticles(option) {
	// podzielic to dla kazdego selecta
	const field = option.parentElement.parentElement.nextElementSibling;

	if (option.value !== 0) {
		xml(`inc/manage-lists.inc.php?categoryId=${option.value}`, field); // wyświetlenie prodoktów w danej kategorii
		field.style.visibility = 'visible'; // wyswietlenie drugiego selecta
	}
}

function showUnits(option) {
	const field = option.parentElement.parentElement.nextElementSibling;

	if (option.value !== 0) {
		xml(`inc/manage-lists.inc.php?articleId=${option.value}`, field); // wyświetlenie jednostki dla danego produktu
		field.style.visibility = 'visible'; // wyswietlenie trzeciego selecta

		setTimeout(() => {
			field.nextElementSibling.style.visibility = 'visible';
		}, 1000);
	}
}

function addArticleToList(button) {
	const inputs = button.previousElementSibling;
	var articleId = inputs.children[2].children[0].value;
	var listId = button.parentElement.parentElement.parentElement.previousElementSibling.lastElementChild.getAttribute(
		'name'
	);
	var quanity = parseInt(inputs.children[4].children[0].value);

	if (articleId == undefined || listId == undefined || quanity > 10) {
		showModalNotification('failure');
	} else {
		for (let index = 0; index < quanity; index++) {
			xml(
				`inc/manage-lists.inc.php?articleId=${articleId}&listId=${listId}&add-article-to-list`,
				null
			);
		}

		showModalNotification('success');
	}
}

function deleteList(listId, listName) {
	if (confirm(`Delete ${listName}?`)) {
		xml(`inc/manage-lists.inc.php?listId=${listId}&delete-list`, null);

		setTimeout(() => {
			showModalNotification('success');
		}, 100);

		setTimeout(() => {
			xml('my-lists.php', document.querySelector('main'));
		}, 1000);
	}
}

function modalAddListOwner(listId) {
	document
		.querySelector(`.modal.additionalListOwner`)
		.classList.add('modal-open');

	setTimeout(() => {
		document.querySelector(
			'.modal.additionalListOwner.modal-open div.modal-content form div.submit-field'
		).id = listId; // dodanie atrybutu id o wartości numeru id listy do przycisku
	}, 100);
}

function addListOwner(button) {
	var listId = button.id;
	var email = button.previousElementSibling.children[1];

	if (validateEmail(email)) {
		xml(
			`inc/manage-lists.inc.php?listId=${listId}&emailAddress=${email.value}&add-subowner`,
			document.querySelector(
				'.modal.additionalListOwner.modal-open div.modal-content form p'
			)
		);

		// setTimeout(() => {
		// 	xml(`my-lists.php`, document.querySelector('main'));
		// }, 3000);
	}
}

navHandler();
var clonedDivsCount = 0;
