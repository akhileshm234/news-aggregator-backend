window.onload = function () {
    // Get the first response from any endpoint to obtain the CSRF token
    fetch("/api/any-endpoint").then((response) => {
        const csrfToken = response.headers.get("X-CSRF-TOKEN");
        if (csrfToken) {
            // Add the token to all future requests
            const originalSwaggerExecute = window.ui.executeRequest;
            window.ui.executeRequest = function (req) {
                req.headers["X-CSRF-TOKEN"] = csrfToken;
                return originalSwaggerExecute.call(window.ui, req);
            };
        }
    });
};
