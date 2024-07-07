var brandlist = new Array("Porsche","Volkswagen","Audi","BMW");
function newClient(){
	var preference = Math.floor((Math.random()*4));
	var time = Math.floor((Math.random()*10000)+1);
	var client = Math.floor((Math.random()*10)+1);
	$("#clients_queue").append('<div class="client client_'+client+'"><span class="preference">Client for '+brandlist[preference]+'</span></div>');
	setTimeout(function(){newClient();},time);
}
$("document").ready(function(e) {
	newClient();
});
