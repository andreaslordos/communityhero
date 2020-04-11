$('#loginModal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget) // Button that triggered the modal
	var modal = $(this)
});

// Click login
// TODO: actually implement authentication
$('#modalLoginButton').click(function(event){
	console.log('Login clicked...');
	if(localStorage.getItem('isLoggedIn')==true){ // If logged in, then log out
		console.log('Logging out...');
		localStorage.setItem('isLoggedIn', false);
		localStorage.setItem('user', null);
		location.reload();
	}
	else{
		console.log('here');
		localStorage.setItem('isLoggedIn', true);
		localStorage.setItem('user', $('#username').val());
		$('#loginModal').modal('hide');
		loadPage();
	}
});

$('#navbarLoginButton').click(function(event){
	console.log('Login clicked...');
	if(localStorage.getItem('isLoggedIn')=='true'){ // If logged in, then log out
		console.log('Logging out...');
		localStorage.setItem('isLoggedIn', false);
		localStorage.setItem('user', null);
		location.reload();
	}
	else{
		console.log('here');
		localStorage.setItem('isLoggedIn', true);
		localStorage.setItem('user', $('#username').val());
		$('#loginModal').modal('hide');
		loadPage();
	}

});