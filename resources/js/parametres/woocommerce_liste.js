document.querySelector("#woocommerce-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const form = document.getElementById("woocommerce-form");
    const button = form.querySelector('button[type="submit"]');
    const html = button.innerHTML;
    button.innerHTML = __spinner_element + html;
    button.setAttribute('disabled', '');

    try {
        const response = await fetch(form.getAttribute('action'), {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        if (response.ok) {
            const data = await response.text();
            toastr.success(data);
        } else if (response.status === 422) {
            const data = await response.json();
            handleValidationErrors(data.errors, e.target);
        } else if (response.status === 500) {
            const errorData = await response.text();
            toastr.error(errorData);
        }
    } catch (error) {
        toastr.error('An error occurred while processing your request.');
    } finally {
        button.removeAttribute('disabled');
        button.innerHTML = html;
    }
});

document.querySelector("#test-btn").addEventListener("click", async (e) => {
    e.preventDefault();
    const form = document.getElementById("woocommerce-form");
    const formData = new FormData(form);
    const button = form.querySelector('#test-btn');
    const html = button.innerHTML;
    button.innerHTML = __spinner_element + html;
    button.setAttribute('disabled', '');

    try {
        const response = await fetch(button.dataset.url, {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        });

        if (response.ok) {
            const responseData = await response.text();
            toastr.success(responseData);
        } else if (response.status === 422) {
            const data = await response.json();
            handleValidationErrors(data.errors, form);
        } else if (response.status === 500) {
            const responseData = await response.text();
            toastr.error(responseData);
        }
    } catch (error) {
        toastr.error('An error occurred while processing your request.');
    } finally {
        button.removeAttribute('disabled');
        button.innerHTML = html;
    }
});

const handleValidationErrors = (errors, form) => {
    form.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(error => error.remove());

    Object.keys(errors).forEach((key) => {
        const fixedKey = dotNotationToBracketNotation(key);
        const input = form.querySelector(`[name="${fixedKey}"]`);
        if (input) {
            input.classList.add('is-invalid');

            const error = document.createElement('div');
            error.classList.add('invalid-feedback');
            error.innerHTML = errors[key][0];
            input.insertAdjacentElement('afterend', error);
        }
    });
};

function dotNotationToBracketNotation(str) {
    return str.replace(/\.(\d+)|\.(\w+)/g, (_, number, word) =>
        number !== undefined ? `[${number}]` : `[${word}]`
    );
}
