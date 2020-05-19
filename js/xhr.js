// @see https://gomakethings.com/promise-based-xhr/

export const makeRequest = function (url, method = 'GET') {

    // Create the XHR request
    var request = new XMLHttpRequest();

    // Return it as a Promise
    return new Promise(function (resolve, reject) {

        // Setup our listener to process compeleted requests
        request.onreadystatechange = function () {

            // Only run if the request is complete
            if (request.readyState !== 4) return;

            // Process the response
            if (request.status >= 200 && request.status < 300) {
                // If successful
                resolve(request);
            } else {
                // If failed
                reject({
                    status: request.status,
                    statusText: request.statusText,
                    request: request
                });
            }

        };

        // Setup our HTTP request
        request.open(method || 'GET', url, true);

        // Send the request
        request.send();

    });
};

const jsonDecodeIfPossible = function(str) {
    try {
        return JSON.parse(str);
    } catch(e) {
        return str;
    }
}

export const makeJSONRequest = async function(url, method = 'GET') {
    try {
        const request = await makeRequest(url, method);
        return jsonDecodeIfPossible(request.responseText);

    } catch(e) {
        return jsonDecodeIfPossible(e.request.responseText);
    }
}
