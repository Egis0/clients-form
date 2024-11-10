let form = {
    firstname: document.getElementById('firstname'),
    lastname: document.getElementById('lastname'),
    birthday: document.getElementById('birthday'),
    submit: document.getElementById('btn-submit'),
    errorMessages: document.getElementById('error-messages'),
    message: document.getElementById('message'),
};
updateClientsList();

form.submit.addEventListener('click', () => {
    const errors = validateForm(form);
    if (errors.length) {
        updateErrorMessages(errors);

        return;
    }

    const requestData = JSON.stringify({
        'firstname': form.firstname.value,
        'lastname': form.lastname.value,
        'birthday': form.birthday.value,
    });
    const handleResponse = function (responseObject) {
        updateErrorMessages(responseObject.errors);
        if (responseObject.message) {
            form.firstname.value = '';
            form.lastname.value = '';
            form.birthday.value = '';
            addMessage(responseObject.message);
            updateClientsList();
        }
    }
    makeRequest('post', handleResponse, requestData);
});

document.getElementById('clear-clients').addEventListener('click', () => {
    const handleResponse = function (responseObject) {
        if (responseObject.message) {
            addMessage(responseObject.message);
            updateClientsList();
        }
    }
    makeRequest('delete', handleResponse);
});

function makeRequest(method, handleResponse, requestData = '') {
    let request = new XMLHttpRequest();
    request.onload = () => {
        let responseObject = null;

        try {
            responseObject = JSON.parse(request.responseText);
        } catch (e) {
            console.error('Could not parse JSON!');
            updateErrorMessages(['Nenumatyta klaida!']);

            return;
        }

        if (responseObject) {
            handleResponse(responseObject);
        }
    };

    request.open(method, 'api/clients');
    if (requestData) {
        request.setRequestHeader('Content-type', 'application/json');
        request.send(requestData);
    } else {
        request.send();
    }

}

function updateClientsList() {
    const handleResponse = function (responseObject) {
        let clientList = document.getElementById('clients');
        while (clientList.firstChild) {
            clientList.removeChild(clientList.firstChild);
        }
        if (responseObject.data) {
            responseObject.data.forEach((client) => {
                let li = document.createElement('li');
                li.textContent = `${client.initials}, ${client.year}`;
                clientList.appendChild(li);
            });
        }
    }
    makeRequest('get', handleResponse);
}

function addMessage(message) {
    form.message.textContent = message;
    form.message.style.display = 'block';
}

function validateForm(form) {
    const required = ['firstname', 'lastname', 'birthday'];
    const translations = {
        firstname: 'Vardas',
        lastname: 'Pavardė',
        birthday: 'Gimimo data',
    };
    let errors = [];
    required.forEach((fieldName) => {
        if (!form[fieldName].value) {
            errors.push(`Laukas '${translations[fieldName]}' privalo būti užpildytas`);
        }
    });

    if (form.birthday.value) {
        let date = new Date(form.birthday.value);
        if (isNaN(date)) {
            errors.push(`Lauko '${translations.birthday}' formatas yra neteisingas`);
        }
    }

    return errors;
}

function clearMessages() {
    while (form.errorMessages.firstChild) {
        form.errorMessages.removeChild(form.errorMessages.firstChild);
    }
    form.errorMessages.style.display = 'none';
    form.message.style.display = 'none';
}

function updateErrorMessages(errors) {
    clearMessages();
    if (errors) {
        errors.forEach((error) => {
            let li = document.createElement('li');
            li.textContent = error;
            form.errorMessages.appendChild(li);
        });

        form.errorMessages.style.display = 'block';
    }
}