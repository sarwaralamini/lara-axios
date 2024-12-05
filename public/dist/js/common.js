/**
 * Configure Axios default settings for secure cross-origin requests.
 *
 * This configuration ensures that Axios includes credentials (such as cookies and HTTP authentication)
 * and the CSRF token in requests by default. This is important for handling sessions and preventing
 * cross-site request forgery (CSRF) attacks, especially when interacting with backends that require
 * authentication or have CSRF protection enabled.
 *
 * @param {boolean} withCredentials - Set to `true` to include credentials (cookies, HTTP auth) in requests.
 * @param {boolean} withXSRFToken - Set to `true` to automatically send the CSRF token in headers for security.
 */
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

/**
 * Display a Bootstrap alert message dynamically within a container.
 *
 * This function creates and injects a Bootstrap-styled alert into the `alertContainer` element,
 * allowing for customizable alert types and messages. The alert is dismissible, featuring a close button.
 *
 * @param {string} type - The type of the alert (e.g., 'success', 'danger', 'warning', 'info').
 *                        Determines the styling of the alert.
 * @param {string} message - The message to be displayed within the alert.
 *
 */

const alertContainer = document.getElementById('alert-container');

function showAlert(type, message) {
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show text-center" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

/**
 * Handles the login form submission process.
 *
 * This function is triggered when the user submits the login form. It performs the following steps:
 * 1. Prevents the default form submission behavior.
 * 2. Disables the submit button, shows a loading spinner, and updates the button text to indicate the form is being submitted.
 * 3. Collects the form data and disables the input fields to prevent multiple submissions.
 * 4. Configures Axios to include credentials and the CSRF token for secure cross-origin requests.
 * 5. Sends a GET request to retrieve the CSRF token from the server before submitting the login form.
 * 6. Sends a POST request to the server to log in using the collected form data.
 * 7. If the login is successful (status 200), it shows a success alert and redirects the user to the specified dashboard URL.
 * 8. If an error occurs, it handles various response statuses (e.g., 404, 401, 500) and displays appropriate error messages.
 *    If there are validation errors (422), it displays them in the form.
 * 9. After the request completes (whether successful or not), it restores the form inputs and button, hides the spinner,
 *    and updates the button text back to its default state.
 *
 * @param {Event} event - The submit event triggered by the user.
 */

const loginForm = document.getElementById('login-form');

if(loginForm)
{
    loginForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        // Get the necessary DOM elements.
        const usernameField = document.getElementById('username');
        const passwordField = document.getElementById('password');
        const button = event.target.querySelector('button');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');
        const buttonTextLogging = document.getElementById('button-text-logging');

        // Disable the submit button, show the spinner and "Logging In..." text, and hide the default button text to indicate the form is being submitted.
        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonTextLogging.style.display = 'inline-block';
        buttonText.style.display = 'none';

        const form = event.target; // Get the form element
        const formData = new FormData(form); // Collect all form data

        // Disable input fields to prevent changes during submission
        usernameField.disabled = true;
        passwordField.disabled = true;

        // Retrieve CSRF cookie for secure authentication
        await axios.get('http://lara-axios.sar/sanctum/csrf-cookie').then(async (csrfResponse) => {
            try {
                // Send the login request with form data
                const loginResponse = await axios.post('http://lara-axios.sar/api/v1/web/login',
                    formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json'
                        },
                    }
                );

                // If login is successful, show success message and redirect
                if (loginResponse.status === 200) {
                    showAlert('success', 'Login successful! Redirecting to dashboard...');
                    setTimeout(() => {
                        window.location.href = current_url;
                    }, 2000);
                }
            } catch (error) {
                setTimeout(() => {
                    // Handle different types of errors
                    if (error.response) {
                        // Handle specific HTTP error codes (404, 401, 500, etc.)
                        if(
                            (error.response.status === 404)
                            || (error.response.status === 401)
                            || (error.response.status === 410)
                            || (error.response.status === 410)
                            || (error.response.status === 500)
                        )
                        {
                            showAlert('danger', error.response.data.message);
                        }

                        // Handle validation errors (422)
                        if((error.response.status === 422))
                        {
                            const errors = error.response.data.errors;

                            displayValidationErrors(errors, "login-form");
                        }
                    }

                    // Restore the form state (enable button and inputs, hide spinner)
                    button.disabled = false;
                    usernameField.disabled = false;
                    passwordField.disabled = false;
                    spinner.style.display = 'none';
                    buttonTextLogging.style.display = 'none';
                    buttonText.style.display = 'inline-block';
                }, 100);
            }
        });
    });
}

/**
 * Handles the logout process when the logout button is clicked.
 *
 * This function is triggered when the user clicks the 'logout-button'. It performs the following actions:
 * 1. Disables the logout button and its associated elements to prevent multiple clicks.
 * 2. Displays a loading spinner and changes the button text to indicate the logout process is in progress.
 * 3. Sends a GET request to the logout API endpoint using Axios, including necessary headers.
 * 4. If the response is successful (HTTP status 200), redirects the user to the homepage after a brief delay.
 * 5. If an error occurs during the request (e.g., server error 500), re-enables the button, hides the loading spinner,
 *    and displays an error message using a notification.
 *
 * @param {Event} event - The click event triggered by the user.
 */

const logoutForm = document.getElementById('logout-button');

if(logoutForm)
{
    logoutForm.addEventListener('click', async function (event) {

        // Reference the necessary DOM elements: the current button (this), the spinner element, the button text elements (default and "Logging Out" text), and the loading spinner inside the body for indicating the loading state during logout.
        const button = this;
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');
        const buttonTextLoggingOut = document.getElementById('button-text-logging-out');
        const loadingSpinner_body = document.getElementById('loadingSpinner_body');

        // Disable the submit button, show the spinner and "Logging out..." text, and hide the default button text to indicate the logout request is being processed.
        button.disabled = true;
        spinner.style.display = 'inline-block';
        buttonTextLoggingOut.style.display = 'inline-block';
        buttonText.style.display = 'none';
        loadingSpinner_body.style.display = 'flex';

        try {
            // Send the logout request to the server using Axios
            const response = await axios.get('http://lara-axios.sar/api/v1/web/logout',
                {
                    headers: {
                        'Content-Type': 'application/json', // Content-Type header
                        'Accept': 'application/json',
                    },
                }
            );

            // If the logout is successful (status 200), redirect to the homepage after a short delay
            if((response.status === 200))
            {
                setTimeout(() => {
                    window.location.href = '/';
                }, 1000);
            }
        } catch (error) {
            // If an error occurs during the request
            if (error.response) {
                // If the error is a server error (status 500), re-enable the button and show an error notification
                if((error.response.status === 500))
                {
                    button.disabled = false;
                    spinner.style.display = 'none';
                    buttonTextLoggingOut.style.display = 'none';
                    buttonText.style.display = 'inline-block';
                    loadingSpinner_body.style.display = 'none';

                    // Display error notification using jQuery Notify
                    $.notify({
                        title: "Error : ",
                        message: error.response.data.message
                    },{
                        allow_dismiss: false,
                        type: "danger",
                        placement: {
                            from: "top",
                            align: "center"
                        }
                    });
                }
            }
        }
    });
}


/**
 * Display validation error messages next to each input field
 * @param {Object} errors - The validation errors returned by Laravel API (in JSON format).
 * @param {string} formId - The ID of the form containing the input fields.
 */

function displayValidationErrors(errors, formId) {
    // Clear previous error messages
    const form = document.getElementById(formId);
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(msg => msg.remove());

    // Loop through each error in the errors object
    Object.keys(errors).forEach(field => {
        if (errors.hasOwnProperty(field)) {
            // Find the input field by its name attribute
            const inputElement = form.querySelector(`[name="${field}"]`);

            if (inputElement) {
                // Create a div element to display the error message
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');  //Add the 'invalid-feedback' class to style the error message according to Bootstrap's validation styles.
                errorDiv.classList.add('d-block'); // Add the 'd-block' class to the errorDiv element to make it display as a block element.

                // Set the error message text
                errorDiv.textContent = errors[field][0];

                // Insert the error message after the input field
                inputElement.insertAdjacentElement('afterend', errorDiv);
            }
        }
    });
}

