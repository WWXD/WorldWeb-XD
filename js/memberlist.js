//Memberlist JavaScript

function hookUpMemberlistControls() {
	$("#orderBy,#order,#sex,#power").change(function(e) {
		refreshMemberlist();
	});

	$("#submitQuery").click(function() {
		refreshMemberlist();
	});

	refreshMemberlist();
}

function refreshMemberlist(page) {
	var orderBy = $("#orderBy").val();
	var order   = $(  "#order").val();
	var sex     = $(    "#sex").val();
	var power   = $(  "#power").val();
	var query   = $(  "#query").val();
	if (typeof page == "undefined")
		page = 0;

	$.get("./?page=memberlist", {
		listing: true,
		dir: order,
		sort: orderBy,
		sex: sex,
		pow: power,
		from: page,
		query: query}, function(data) {
			$("#memberlist").html(data);
		});
}


$(document).ready(function() {
	hookUpMemberlistControls();
});
