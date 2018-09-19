module.exports = {
  getToken: function () {
    var accessToken = window.localStorage.getItem('access_token');

    return accessToken;
  }
};
