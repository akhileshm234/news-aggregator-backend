window.onload = function() {
    // First, get the CSRF token from the meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const ui = SwaggerUIBundle({
        dom_id: '#swagger-ui',
        url: "{!! $urlToDocs !!}",
        requestInterceptor: (request) => {
            // Add both header variations for CSRF token
            request.headers['X-CSRF-TOKEN'] = csrfToken;
            request.headers['X-XSRF-TOKEN'] = csrfToken;
            request.credentials = 'include';
            return request;
        },
        persistAuthorization: true
    });
}; 