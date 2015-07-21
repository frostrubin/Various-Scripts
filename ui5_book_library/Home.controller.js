sap.ui.controller("bookLibraryHome.Home", {

	onInit: function () {
    var oLoginButton = this.getView().byId('IdLoginButton');
    oLoginButton.addStyleClass('loginButton');
    
    var oBookListButton = this.getView().byId('IdBookListButton');
    oBookListButton.addStyleClass('visibleAfterLogin');
	},

	onAfterRendering: function() {
    $(".visibleAfterLogin").hide();
	},

	onBookList: function (evt) {
    app.to('list');
  },

  onLogin: function(evt) {
  	var params = {
     'callback': signinCallback,
     'clientid': '1036739147710-urn353g882plkjlakkasfasgjr707.apps.googleusercontent.com',
     'cookiepolicy': 'single_host_origin',
     'scope': 'profile',
    };
    gapi.auth.signIn(params);
  }
});
