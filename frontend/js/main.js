var BASE_URL = 'https://rhubarb-cake-22341.herokuapp.com/api/v1/';
//var BASE_URL = 'http://localhost:8000/api/v1/';
var orders;
var loading = '<p class=\'card-text\'>Loading...</p>';
var markers = [];
var selectedMarker = 1;

function loadListDetails(position){
	console.log(markers);
	markers[position-1].setIcon(new L.AwesomeNumberMarkers({number: position, markerColor: 'darkred'}));
	$.get(BASE_URL + "orders/", {'orderId': orders[position-1]['OrderID']}, function(data, status){
		console.log(data);
		totalCost=0;
		for(var i in data){
			$('#order-details-list').append('<div class="list-group-item d-flex w-100 justify-content-between"><h5 class="mb-1">' + data[i]['PriceID']['ProductID']['ProductBrandID']['BrandName'] + ' ' + data[i]['PriceID']['ProductID']['ProductName'] + '</h5><h6>' + data[i]['Quantity'] + 'x' + data[i]['PriceID']['Price'] + '&euro;</h6></div>');
			totalCost+= parseFloat(data[i]['Quantity']) * parseFloat(data[i]['PriceID']['Price']);
		}
		$('#order-details-list').append("<h5 class='mb-1 list-group-item right-justify'>Total: " + totalCost.toFixed(2) + "&euro;</h5>")

	});
}

function selectMarker(mark){
	markers[selectedMarker-1].setIcon(new L.AwesomeNumberMarkers({number: selectedMarker, markerColor: 'red'}));
	$('#order-details-list').html('');
	selectedMarker = mark;
	console.log('selectedMarker: ', selectedMarker);
	$('#order-input').val(selectedMarker);
	loadListDetails(selectedMarker);

}

function markerClick(e){
	selectMarker(e['sourceTarget']['options']['icon']['options']['number']);
}

function loadLists(position){
	for(var marker in markers){
		mymap.removeLayer(markers[marker]);
	}
	markers = [];
	$.get(BASE_URL + "users/geo/", {'lat': position.coords.latitude, 'lng': position.coords.longitude, 'rad':RADIUS}, function(data, status){
		orders = data;
		for(var order in data){
			d = data[order];
			markers.push(L.marker(
				[d["UserID"]['Userlatitude'], d["UserID"]['Userlongitude']], 
				{icon: new L.AwesomeNumberMarkers({number: parseInt(order)+1, markerColor: 'red'})
			}).on('click', markerClick).addTo(mymap));
		}
		selectMarker(1);
	});

}

// Assuming logged in!
function loadPage(){
	$('#navbarLoginButton').html('Log out');
	//$('#order-details-card').html(loading);
	console.log('Requesting location access');
	var watchID = navigator.geolocation.getCurrentPosition(function(position) {
		console.log('Got location');
		marker.setLatLng(new L.LatLng(position.coords.latitude, position.coords.longitude)); 
		circle.setLatLng(new L.LatLng(position.coords.latitude, position.coords.longitude)); 
		loadLists(position);
	});
}
const RADIUS = 3; // In km
var mapOptions = {};
var mymap = L.map('mapid').setView([35, 33], 10);
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
	attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	maxZoom: 18,
	id: 'mapbox/streets-v11',
	tileSize: 512,
	zoomOffset: -1,
	accessToken: 'pk.eyJ1IjoiY2ZhbGFzIiwiYSI6ImNrOGhmZDNhNTAwN3czZm1jcjdqYnVwcTYifQ.DFTZF3tuUwz8zEJ_WczQYw'
}).addTo(mymap);
var marker = L.marker([0,0]).addTo(mymap);
var circle = L.circle([0,0], {
	color: '#a',
	fillColor: '#f03',
	fillOpacity: 0.1,
	radius: RADIUS*1000
}).addTo(mymap);
var colors = ['red', 'darkred', 'orange', 'green', 'darkgreen', 'blue', 'purple', 'darkpuple', 'cadetblue'];

if(localStorage.getItem('isLoggedIn')=='true'){
	loadPage();
}
else{
	$('#loginModal').modal();
}


function previous_marker(){
	if(selectedMarker>1)
		selectMarker(selectedMarker-1);
	setTimeout(function(){
		$('#order-prev').one('click', previous_marker);
	}, 500);
}

function next_marker(){
	if(selectedMarker<markers.length)
		selectMarker(selectedMarker+1);
	setTimeout(function(){
		$('#order-next').one('click', next_marker);
	}, 500);
}


// Change order buttons prev/next
$('#order-prev').one('click', previous_marker);
$('#order-next').one('click', next_marker);


// Deliver Button
$('#deliver-button').click(function(){
	$.post(BASE_URL + 'orders/deliver/' + orders[selectedMarker-1]['OrderID'], function(data, status){
		window.location.href = '/guidelines.html?order=' + orders[selectedMarker-1]['OrderID'];
	})
});


// Prevent map moving while expanding menu
$("#listlist .card-header").mousedown(function () {
    $(this).mousemove(function (e) {
		if($(window).width()<600){
			$("#listlist").css('height', "calc(100% - " + (e.clientY-56-30) + "px");
		}
    });
}).mouseup(function () {
	$(this).unbind('mousemove');
}).mouseleave(function () {
    $(this).unbind('mousemove');
});

$("#listlist").mouseover(function () {
	mymap.scrollWheelZoom.disable();
	mymap.dragging.disable();
}).mousedown(function () {
	mymap.scrollWheelZoom.disable();
	mymap.dragging.disable();
}).mouseup(function () {
	mymap.scrollWheelZoom.enable();
	mymap.dragging.enable();
}).mouseleave(function () {
	mymap.scrollWheelZoom.enable();
	mymap.dragging.enable();
});