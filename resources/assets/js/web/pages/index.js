$(document).ready(function() {
    console.log('Hello from index console');

    window.axios.get('/api/v1/auth/me').then(function (response) {
        console.log(response);
    });
});
