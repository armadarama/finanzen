var anzahl;
var fieldset;

$(function(){
	countAnzahl();
	$("#showkategorien select option").click(function() {
		switchMode(this);
	});
	$("#addDatensatz").click(function(){
		addData();
	});
});

function addData(){
	anzahl++;
	setNewAnzahl();
	$.ajax({
		type: "POST",
		url: "contentClass.php",
		data: "jscontent=getFieldset&anzahl="+anzahl,
		success: function(fieldset){
			$("#fieldsets").append(fieldset);
		}
	});
}

function countAnzahl(){
	anzahl = $('#fieldsets > *').length; 
}

function setNewAnzahl(){
	$("#anzahlhiddenfield").val(anzahl);
}

function switchMode(e){
	var val = $(e).val();
	var zq = $(e).closest(".datensatz").children(".zweckquelle");
	if(val == "Abheben,ausgaben"){
		zq.hide();
	} else if(val == "Überweisen,ausgaben"){
		zq.hide();;
	} else {
		zq.show();
	}
}