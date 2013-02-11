//ouvre une popup d'information avec le contenu du fichier passé en paramètre
function openInfoFromHTMLFile(url){
	var message = '' ;
	$.get(url, function(data) {
		message = data;
	})
	.success(function() { 
		
	})
	.error(function() { 
		message = "Erreur d'affchage";
	})
	.complete(function() { 
		$( "#infopopup" ).dialog( "option", "modal", false );
		openInfo(message);
	})
}

//ouvre une popup d'information avec le message passé en paramètre
function openInfo(message){
	$( "#infopopup" ).dialog( "open" ) ;
	document.getElementById('infopopup').innerHTML = message ;
}