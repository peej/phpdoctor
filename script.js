$(document).ready(function(){
	$("#content div").hide();
	$("h2 a").click(toggleSection);
	if (window.location.hash) {
		$("h2" + window.location.hash + " a").trigger("click");
	} else {
		$("h2:first a").trigger("click");
	}
});

function toggleSection() {
	if ($(this).attr("class") != "active") {
		$("#content div:visible").slideUp("slow");
		$(this).parent().next().slideDown("slow");
		$("h2 a").removeClass("active");
		$(this).addClass("active");
	}
}
